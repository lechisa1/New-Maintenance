<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MaintenanceRequest;

class UpdateMaintenanceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'nullable|string|max:255',
            'description' => 'sometimes|string|min:10|max:5000',
            // REMOVE THIS LINE - issue_type_id does not belong to maintenance_requests table
            // 'issue_type_id' => 'required|exists:issue_types,id',
            'priority' => 'sometimes|string|in:' . implode(',', array_keys(MaintenanceRequest::getPriorityOptions())),
            'status' => 'sometimes|string|in:' . implode(',', array_keys(MaintenanceRequest::getStatusOptions())),
            'assigned_to' => 'nullable|exists:users,id',
            'technician_notes' => 'nullable|string|max:2000',
            'resolution_notes' => 'nullable|string|max:2000',
            'files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xls,xlsx',

            // Add items validation for updating items
            'items' => 'sometimes|array',
            'items.*.id' => 'sometimes|exists:maintenance_request_items,id',
            'items.*.item_id' => 'required_with:items|exists:items,id',
            'items.*.issue_type_id' => 'required_with:items|exists:issue_types,id',
            'items.*.description' => 'nullable|string|max:2000',
        ];

        // Only admin/technician can change status
        if ($this->user()->hasRole(['admin', 'technician'])) {
            $rules['status'] = 'sometimes|string|in:' . implode(',', array_keys(MaintenanceRequest::getStatusOptions()));
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'description.min' => 'Description must be at least 10 characters.',
            'priority.in' => 'Please select a valid priority level.',
            'status.in' => 'Please select a valid status.',
            'assigned_to.exists' => 'Selected technician does not exist.',
            'files.*.max' => 'File size must not exceed 10MB.',
            'files.*.mimes' => 'Allowed file types: JPG, PNG, PDF, DOC, DOCX, TXT, XLS, XLSX.',
            'items.*.item_id.required_with' => 'Please select equipment for each item.',
            'items.*.item_id.exists' => 'Selected equipment does not exist.',
            'items.*.issue_type_id.required_with' => 'Please select issue type for each item.',
            'items.*.issue_type_id.exists' => 'Selected issue type does not exist.',
        ];
    }
}

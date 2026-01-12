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
             'issue_type_id' => 'required|exists:issue_types,id',
            'priority' => 'sometimes|string|in:' . implode(',', array_keys(MaintenanceRequest::getPriorityOptions())),
            'status' => 'sometimes|string|in:' . implode(',', array_keys(MaintenanceRequest::getStatusOptions())),
            'assigned_to' => 'nullable|exists:users,id',
            'technician_notes' => 'nullable|string|max:2000',
            'resolution_notes' => 'nullable|string|max:2000',
            'files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xls,xlsx',
        ];

        // Only admin/technician can change status
        if ($this->user()->hasRole(['admin', 'technician'])) {
            $rules['status'] = 'sometimes|string|in:' . implode(',', array_keys(MaintenanceRequest::getStatusOptions()));
        }

        return $rules;
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MaintenanceRequest;

class StoreMaintenanceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.issue_type_id' => 'required|exists:issue_types,id',
            'items.*.description' => 'nullable|string|max:2000',
            'description' => 'required|string|min:10|max:5000', // overall description
            'priority' => 'required|string|in:' . implode(',', array_keys(MaintenanceRequest::getPriorityOptions())),
            'files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xls,xlsx',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one equipment.',
            'items.min' => 'Please add at least one equipment.',
            'items.*.item_id.required' => 'Please select equipment for all items.',
            'items.*.item_id.exists' => 'Selected equipment does not exist.',
            'items.*.issue_type_id.required' => 'Please select issue type for all items.',
            'items.*.issue_type_id.exists' => 'Selected issue type does not exist.',
            'description.required' => 'Please describe the overall problem.',
            'description.min' => 'Description must be at least 10 characters.',
            'priority.required' => 'Please select priority level.',
            'files.*.max' => 'File size must not exceed 10MB.',
            'files.*.mimes' => 'Allowed file types: JPG, PNG, PDF, DOC, DOCX, TXT, XLS, XLSX.',
        ];
    }
}

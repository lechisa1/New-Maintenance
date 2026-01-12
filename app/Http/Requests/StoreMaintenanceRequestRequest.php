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
            'item_id' => 'required|exists:items,id',
            'title' => 'nullable|string|max:255',
            'description' => 'required|string|min:10|max:5000',
          'issue_type_id' => 'required|exists:issue_types,id',

            'priority' => 'required|string|in:' . implode(',', array_keys(MaintenanceRequest::getPriorityOptions())),
            'files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,txt,xls,xlsx',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Please select equipment.',
            'item_id.exists' => 'Selected equipment does not exist.',
            'description.required' => 'Please describe the problem.',
            'description.min' => 'Description must be at least 10 characters.',
            'issue_type.required' => 'Please select issue type.',
            'priority.required' => 'Please select priority level.',
            'files.*.max' => 'File size must not exceed 10MB.',
            'files.*.mimes' => 'Allowed file types: JPG, PNG, PDF, DOC, DOCX, TXT, XLS, XLSX.',
        ];
    }
}
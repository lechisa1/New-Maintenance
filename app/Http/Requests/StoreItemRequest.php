<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Item;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Item::getTypeOptions())),
            'unit' => 'required|string|in:' . implode(',', array_keys(Item::getUnitOptions())),
            'status' => 'required|string|in:' . implode(',', array_keys(Item::getStatusOptions())),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The equipment name is required.',
            'type.required' => 'Please select an equipment type.',
            'unit.required' => 'Please select a unit of measure.',
            'status.required' => 'Please select a status.',
        ];
    }
}
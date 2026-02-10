<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Keep simple & safe
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'full_name' => 'required|string|max:255',

            'email' => 'required|email|unique:users,email,' . $user->id,

            'phone' => [
            'nullable',
            'regex:/^(09\d{8}|2519\d{8})$/',
            'unique:users,phone,' . $user->id,
           ],

            'assign_type' => 'required|in:cluster,division',

            'cluster_id' => 'required_if:assign_type,cluster|nullable|exists:clusters,id',

            'division_id' => 'required_if:assign_type,division|nullable|exists:divisions,id',

            // ✅ SINGLE ROLE (string, not array)
            'roles' => 'required|exists:roles,name',

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'A role must be assigned.',
            'email.unique'   => 'This email is already registered.',
            'phone.unique'   => 'This phone number is already registered or invalid',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    protected function prepareForValidation()
    {
        // Normalize phone
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }
    }
}
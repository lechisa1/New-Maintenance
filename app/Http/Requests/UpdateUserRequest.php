<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin') || 
               (auth()->user()->id === $this->route('user')->id && 
                auth()->user()->hasPermissionTo('edit profile'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'division_id' => 'nullable|exists:divisions,id',
            'cluster_id' => 'nullable|exists:clusters,id',
            'roles' => 'sometimes|array|min:1',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'nullable|boolean',
            'current_password' => [
                'required_with:password',
                function ($attribute, $value, $fail) {
                    if (!\Hash::check($value, auth()->user()->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'current_password.required_with' => 'Current password is required to change password.',
            'roles.required' => 'At least one role must be assigned.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean phone number format
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }

        // If password is empty, remove it from validation
        if (empty($this->password)) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
            $this->request->remove('current_password');
        }
    }
}
<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Division;
use App\Models\Cluster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    private $importedCount = 0;
    private $errors = [];

    public function model(array $row)
    {
        // Find or create role
        $role = Role::firstOrCreate(['name' => $row['role']]);

        // Find division and cluster
        $division = Division::where('name', $row['division'])->first();
        $cluster = Cluster::where('name', $row['cluster'])->first();

        // Check if user exists
        $user = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'id' => (string) Str::uuid(),
                'full_name' => $row['full_name'],
                'phone' => $row['phone'] ?? null,
                'division_id' => $division->id ?? null,
                'cluster_id' => $cluster->id ?? null,
                'password' => Hash::make($row['password'] ?? 'password123'),
                'email_verified_at' => $row['status'] === 'Active' ? now() : null,
            ]
        );

        // Assign role
        $user->syncRoles([$role->name]);

        $this->importedCount++;

        return $user;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|exists:roles,name',
            'division' => 'nullable|string',
            'cluster' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'full_name.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email already exists in the system',
            'role.required' => 'Role is required',
            'role.exists' => 'The specified role does not exist',
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

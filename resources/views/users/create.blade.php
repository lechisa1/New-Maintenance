@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Add New User" />
    
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 gap-6">
            <!-- Form Card -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] ">
                <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90 text-center">
                    <i class="bi bi-person-plus-fill me-2"></i>Add New User
                </h3>
                <hr class="mb-6 border-gray-200 dark:border-gray-700">

                <div class="space-y-6">
                    <!-- Personal Information -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-person-vcard me-2"></i>Personal Information
                        </h4>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Full Name -->
                            <div>
                                <label for="full_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="full_name" id="full_name"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('full_name') border-red-500 dark:border-red-500 @enderror"
                                    placeholder="e.g., John Doe"
                                    value="{{ old('full_name') }}"
                                    required>
                                @error('full_name')
                                    <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('email') border-red-500 dark:border-red-500 @enderror"
                                    placeholder="e.g., john@example.com"
                                    value="{{ old('email') }}"
                                    required>
                                @error('email')
                                    <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Phone
                                </label>
                                <input type="text" name="phone" id="phone"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('phone') border-red-500 dark:border-red-500 @enderror"
                                    placeholder="e.g., 0912345678"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
<!-- Organization Information -->
<div>
    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-diagram-3 me-2"></i>Organization Information
    </h4>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

        <!-- Organization -->
        <div>
            <label for="organization_id"
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Organization <span class="text-red-500">*</span>
            </label>

            <select id="organization_id" name="organization_id"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs
                       focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10
                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                       @error('organization_id') border-red-500 dark:border-red-500 @enderror">
                <option value="">Select Organization</option>
                @foreach($organizations as $organization)
                    <option value="{{ $organization->id }}"
                        {{ old('organization_id') == $organization->id ? 'selected' : '' }}>
                        {{ $organization->name }}
                    </option>
                @endforeach
            </select>

            @error('organization_id')
                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        <!-- Cluster -->
        <div>
            <label for="cluster_id"
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Cluster <span class="text-red-500">*</span>
            </label>

            <select id="cluster_id" name="cluster_id" disabled
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs
                       focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10
                       disabled:cursor-not-allowed disabled:opacity-60
                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                       @error('cluster_id') border-red-500 dark:border-red-500 @enderror">
                <option value="">Select Cluster</option>
            </select>

            @error('cluster_id')
                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

        <!-- Division -->
        <div>
            <label for="division_id"
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Division
            </label>

            <select id="division_id" name="division_id" disabled
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs
                       focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10
                       disabled:cursor-not-allowed disabled:opacity-60
                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                       @error('division_id') border-red-500 dark:border-red-500 @enderror">
                <option value="">Select Division</option>
            </select>

            @error('division_id')
                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
            @enderror
        </div>

    </div>
</div>


                    <!-- Security & Permissions -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-shield-check me-2"></i>Security & Permissions
                        </h4>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Roles -->
                            <div>
                                <label for="roles" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Roles <span class="text-red-500">*</span>
                                </label>
                                <select name="roles[]" id="roles" multiple
                                    class="h-auto min-h-[44px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('roles') border-red-500 dark:border-red-500 @enderror">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ in_array($role->name, old('roles', [])) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('roles')
                                    <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" x-data="{ showPassword: false }">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-12 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('password') border-red-500 dark:border-red-500 @enderror"
                                        placeholder="Create a strong password"
                                        required>
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <i x-show="!showPassword" class="bi bi-eye"></i>
                                        <i x-show="showPassword" class="bi bi-eye-slash"></i>
                                    </button>
                                    @error('password')
                                        <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" x-data="{ showPassword: false }">
                                    <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-12 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"
                                        placeholder="Confirm password"
                                        required>
                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                        <i x-show="!showPassword" class="bi bi-eye"></i>
                                        <i x-show="showPassword" class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div>
                        <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
                            <i class="bi bi-gear me-2"></i>Additional Options
                        </h4>
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <!-- Email Verification -->
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="email_verified" id="email_verified" 
                                    class="h-4 w-4 rounded border-gray-300 bg-transparent text-blue-500 focus:ring-blue-500/30 dark:border-gray-600 dark:bg-gray-900 dark:checked:border-blue-500 dark:checked:bg-blue-500 dark:focus:ring-blue-500/30"
                                    {{ old('email_verified') ? 'checked' : '' }}>
                                <label for="email_verified" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Mark email as verified
                                </label>
                            </div>

                            <!-- Send Welcome Email -->
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="send_welcome_email" id="send_welcome_email" 
                                    class="h-4 w-4 rounded border-gray-300 bg-transparent text-blue-500 focus:ring-blue-500/30 dark:border-gray-600 dark:bg-gray-900 dark:checked:border-blue-500 dark:checked:bg-blue-500 dark:focus:ring-blue-500/30"
                                    {{ old('send_welcome_email') ? 'checked' : '' }}>
                                <label for="send_welcome_email" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Send welcome email with credentials
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('users.index') }}" 
                                class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-blue-500 px-6 py-3 text-sm font-semibold text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                <i class="bi bi-save me-2"></i>Save User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validation Summary -->
        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <div class="flex items-start">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Please fix the following errors:
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>

    <script>
        // Initialize select2 for roles (if using select2)
        document.addEventListener('DOMContentLoaded', function() {
            const rolesSelect = document.getElementById('roles');
            if (rolesSelect) {
                // You can initialize Select2 here if you have it
                // $('#roles').select2();
                
                // Or just make it a nice multi-select
                rolesSelect.size = Math.min(rolesSelect.options.length, 4);
            }
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function () {

    const organizationSelect = document.getElementById('organization_id');
    const clusterSelect = document.getElementById('cluster_id');
    const divisionSelect = document.getElementById('division_id');

    // Reset helper
    function resetSelect(select, placeholder, disabled = true) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = disabled;
    }

    // When organization changes → load clusters
    organizationSelect.addEventListener('change', async function () {
        const organizationId = this.value;

        resetSelect(clusterSelect, 'Select Cluster');
        resetSelect(divisionSelect, 'Select Division');

        if (!organizationId) return;

        try {
            const response = await fetch(`/organizations/${organizationId}/clusters`);
            const clusters = await response.json();

            if (clusters.length > 0) {
                clusterSelect.disabled = false;
                clusters.forEach(cluster => {
                    clusterSelect.innerHTML += `
                        <option value="${cluster.id}">${cluster.name}</option>
                    `;
                });
            }
        } catch (error) {
            console.error('Failed to load clusters', error);
        }
    });

    // When cluster changes → load divisions
    clusterSelect.addEventListener('change', async function () {
        const clusterId = this.value;

        resetSelect(divisionSelect, 'Select Division');

        if (!clusterId) return;

        try {
            const response = await fetch(`/clusters/${clusterId}/divisions`);
            const divisions = await response.json();

            if (divisions.length > 0) {
                divisionSelect.disabled = false;
                divisions.forEach(division => {
                    divisionSelect.innerHTML += `
                        <option value="${division.id}">${division.name}</option>
                    `;
                });
            }
        } catch (error) {
            console.error('Failed to load divisions', error);
        }
    });

});
</script>

@endsection
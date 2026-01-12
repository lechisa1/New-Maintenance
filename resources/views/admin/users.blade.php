@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Add New User" />
    
    <div class="grid grid-cols-1 gap-6">
        <!-- Form Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] ">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90 text-center">
                <i class="bi bi-person-plus-fill me-2"></i>Add New User
            </h3>
            <hr class="mb-6 border-gray-200 dark:border-gray-700">

            <div class="space-y-6">
                <!-- Default Inputs Section -->
                <div>
                  
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('name') border-red-500 dark:border-red-500 @enderror"
                                placeholder="e.g., John Doe"
                                value="{{ old('name') }}">
                            @error('name')
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
                                value="{{ old('email') }}">
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

                <!-- Select Inputs Section -->
                <div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Sector -->
                        <div>
                            <label for="sector_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Sector <span class="text-red-500">*</span>
                            </label>
                            <select name="sector_id" id="sector_id"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                <option value="" disabled selected>Select Sector</option>
                             <option value="A">A</option>
                              <option value="B">B</option>
                               <option value="C">C</option>
                            </select>
                        </div>

                        <!-- Division -->
                        <div>
                            <label for="division_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Division
                            </label>
                            <select name="division_id" id="division_id"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                <option value="" disabled selected>Select Division</option>
                                   <option value="A">A</option>
                              <option value="B">B</option>
                               <option value="C">C</option>
                            </select>
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Department
                            </label>
                            <select name="department_id" id="department_id"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                                <option value="" disabled selected>Select Department</option>
                                   <option value="A">A</option>
                              <option value="B">B</option>
                               <option value="C">C</option>
                            </select>
                        </div>

                        <!-- Job Position -->
                        <div>
                            <label for="job_position_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Job Position <span class="text-red-500">*</span>
                            </label>
                            <select name="job_position_id" id="job_position_id"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('job_position_id') border-red-500 dark:border-red-500 @enderror">
                                <option value="" disabled selected>Select Job Position</option>
                            <option value="A">A</option>
                              <option value="B">B</option>
                               <option value="C">C</option>
                            </select>
                            @error('job_position_id')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="roles" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="roles" id="roles"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800 @error('roles') border-red-500 dark:border-red-500 @enderror">
                                <option value="" disabled selected>Select Role</option>
                               <option value="A">A</option>
                              <option value="B">B</option>
                               <option value="C">C</option>
                            </select>
                            @error('roles')
                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-12 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 @error('password') border-red-500 dark:border-red-500 @enderror"
                                    placeholder="Create a strong password">
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
                    </div>
                </div>

            

                <!-- Submit Button -->
                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                    <div class="flex justify-end">
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const sectorSelect = document.getElementById('sector_id');
        const divisionSelect = document.getElementById('division_id');
        const departmentSelect = document.getElementById('department_id');



        sectorSelect.addEventListener('change', function () {
            const sectorId = this.value;
            const url = getDivisionUrl.replace(':id', sectorId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    divisionSelect.innerHTML = `<option value="" disabled selected>Select Division</option>`;
                    departmentSelect.innerHTML = `<option value="" disabled selected>Select Department</option>`;
                    data.forEach(division => {
                        divisionSelect.innerHTML += `<option value="${division.id}">${division.name}</option>`;
                    });
                });
        });

        divisionSelect.addEventListener('change', function () {
            const divisionId = this.value;
            const url = getDepartmentUrl.replace(':id', divisionId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    departmentSelect.innerHTML = `<option value="" disabled selected>Select Department</option>`;
                    data.forEach(dept => {
                        departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                    });
                });
        });
    });
    </script>
@endsection
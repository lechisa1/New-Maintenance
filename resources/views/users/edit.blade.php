@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit User" />
    @include('maintenance-requests.partials.alerts')
    <div class="max-w-5xl mx-auto pb-10">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50">

                    {{-- Header --}}
                    <div class="border-b border-gray-200 p-6 dark:border-gray-800">
                        <h3 class="flex items-center text-lg font-bold text-gray-800 dark:text-white/90">
                            <span
                                class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                                <i class="bi bi-pencil-square"></i>
                            </span>
                            Edit User Account
                        </h3>
                    </div>

                    <div class="p-6 space-y-8">

                        {{-- Section 1: Personal Info --}}
                        <section>
                            <h4 class="mb-5 flex items-center text-sm font-bold uppercase tracking-wider text-gray-500">
                                <i class="bi bi-person-vcard mr-2"></i> Personal Information
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Full
                                        Name *</label>
                                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    @error('full_name')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Email
                                        *</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                </div>
                            </div>
                        </section>

                        <hr class="border-gray-100 dark:border-gray-800">

                        {{-- Section 2 & 3: Assignment & Role (Grouped in 2 columns) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                            {{-- Left Column: Assignment --}}
                            <section>
                                <h4 class="mb-5 text-sm font-bold uppercase tracking-wider text-gray-500">
                                    <i class="bi bi-diagram-3 mr-2"></i> Assignment
                                </h4>

                                @php
                                    $assignType = old('assign_type', $user->division_id ? 'division' : 'cluster');
                                @endphp

                                <div class="mb-4 flex gap-4">
                                    <label class="flex items-center text-sm font-medium cursor-pointer">
                                        <input type="radio" name="assign_type" value="cluster"
                                            class="mr-2 text-blue-600 focus:ring-blue-500"
                                            {{ $assignType === 'cluster' ? 'checked' : '' }}>
                                        Cluster
                                    </label>
                                    <label class="flex items-center text-sm font-medium cursor-pointer">
                                        <input type="radio" name="assign_type" value="division"
                                            class="mr-2 text-blue-600 focus:ring-blue-500"
                                            {{ $assignType === 'division' ? 'checked' : '' }}>
                                        Division
                                    </label>
                                </div>

                                <div id="cluster-wrapper">
                                    <select name="cluster_id"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                        <option value="">Select Cluster</option>
                                        @foreach ($clusters as $cluster)
                                            <option value="{{ $cluster->id }}"
                                                {{ old('cluster_id', $user->cluster_id) == $cluster->id ? 'selected' : '' }}>
                                                {{ $cluster->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="division-wrapper" class="hidden">
                                    <select name="division_id"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                        <option value="">Select Division</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}"
                                                {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                                                {{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>

                            {{-- Right Column: Role --}}
                            <section>
                                <h4 class="mb-5 text-sm font-bold uppercase tracking-wider text-gray-500">
                                    <i class="bi bi-shield-lock mr-2"></i> System Role
                                </h4>

                                <div class="mt-9"> {{-- Aligned with the select input on the left --}}
                                    <select name="roles"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ old('roles', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </section>
                        </div>

                        <hr class="border-gray-100 dark:border-gray-800">

                        {{-- Section 4: Password --}}
                        <section>
                            <h4 class="mb-5 text-sm font-bold uppercase tracking-wider text-gray-500">
                                <i class="bi bi-key mr-2"></i> Security Update
                            </h4>
                            <p class="mb-4 text-xs text-gray-400 italic font-medium">Leave password fields empty if you do
                                not want to change the current password.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">New
                                        Password</label>
                                    <input type="password" name="password"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Confirm
                                        New Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                </div>
                            </div>
                        </section>

                    </div>

                    {{-- Footer --}}
                    <div
                        class="flex items-center justify-end gap-4 border-t border-gray-200 bg-gray-50/50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
                        <a href="{{ route('users.index') }}"
                            class="text-sm font-bold text-gray-500 hover:text-gray-700">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-8 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-blue-700">
                            <i class="bi bi-arrow-repeat mr-2"></i> Update User
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- Toggle JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cluster = document.getElementById('cluster-wrapper');
            const division = document.getElementById('division-wrapper');

            function toggle() {
                const checkedRadio = document.querySelector('input[name="assign_type"]:checked');
                if (checkedRadio) {
                    const type = checkedRadio.value;
                    cluster.classList.toggle('hidden', type !== 'cluster');
                    division.classList.toggle('hidden', type !== 'division');
                }
            }

            document.querySelectorAll('input[name="assign_type"]').forEach(r => {
                r.addEventListener('change', toggle);
            });

            toggle();
        });
    </script>
@endsection

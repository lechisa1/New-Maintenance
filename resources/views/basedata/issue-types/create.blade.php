@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Create Issue Type" :links="[['label' => 'Issue Types', 'url' => route('issue-types.index')], ['label' => 'Create']]" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Form Inputs -->
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-6 border-b border-gray-100 pb-4 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                        <i class="bi bi-plus-circle me-2 text-blue-500"></i>Create New Issue Type
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">Define a new category for reporting and tracking issues.</p>
                </div>

                <form method="POST" action="{{ route('issue-types.store') }}" id="issueTypeForm">
                    @csrf

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Issue Type Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                placeholder="e.g., Technical Bug, Facility Maintenance">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea name="description" rows="4"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                placeholder="Briefly describe when this issue type should be used...">{{ old('description') }}</textarea>
                            <div class="mt-2 flex justify-between">
                                <p class="text-xs text-gray-400">Provide context for users selecting this type.</p>
                                <p class="text-xs text-gray-400">Max 500 chars</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column - Configuration & Actions -->
        <div class="lg:col-span-1">
            <!-- Configuration Card -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03] mb-6">
                <h4 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="bi bi-gear me-2 text-blue-500"></i>Configuration
                </h4>

                <div class="space-y-4">
                    <!-- Active Status -->
                    <div class="flex items-start gap-3">
                        <div class="flex h-5 items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }} form="issueTypeForm"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700">
                        </div>
                        <div>
                            <label for="is_active"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                Active Status
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Make this issue type available for selection</p>
                        </div>
                    </div>

                    <!-- Approval Requirement -->
                    <div class="flex items-start gap-3">
                        <div class="flex h-5 items-center">
                            <input type="checkbox" id="is_need_approval" name="is_need_approval" value="1"
                                {{ old('is_need_approval') ? 'checked' : '' }} form="issueTypeForm"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700">
                        </div>
                        <div>
                            <label for="is_need_approval"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                                Requires Approval
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Admin must approve issues of this type</p>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Actions Card -->
            <div
                class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h4 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="bi bi-lightning-charge me-2 text-blue-500"></i>Actions
                </h4>

                <div class="space-y-3">
                    <button type="submit" form="issueTypeForm"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30">
                        <i class="bi bi-check-lg"></i>
                        <span>Save Issue Type</span>
                    </button>

                    <a href="{{ route('issue-types.index') }}"
                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        <i class="bi bi-x-lg"></i>
                        <span>Cancel</span>
                    </a>


                </div>
            </div>

        </div>
    </div>
@endsection

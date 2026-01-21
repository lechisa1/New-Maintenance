@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Issue Type Details" :links="[['label' => 'Issue Types', 'url' => route('issue-types.index')], ['label' => $issueType->name]]" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        <i class="bi bi-tag me-2"></i>{{ $issueType->name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">Issue type details and configuration</p>
                </div>
                <a href="{{ route('issue-types.edit', $issueType) }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    <i class="bi bi-pencil me-2"></i> Edit
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Basic Information</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $issueType->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $issueType->slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $issueType->description ?: 'No description provided' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Settings & Stats -->
                <div class="space-y-4">
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <h4 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Settings</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $issueType->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ $issueType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Approval Requirement</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $issueType->is_need_approval ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                        {{ $issueType->is_need_approval ? 'Requires Approval' : 'Auto Approve' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $issueType->created_at->format('M d, Y \a\t h:i A') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $issueType->updated_at->format('M d, Y \a\t h:i A') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                <form method="POST" action="{{ route('issue-types.toggle-status', $issueType) }}">
                    @csrf
                    @method('POST')
                    <button type="submit"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                        {{ $issueType->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('issue-types.toggle-approval', $issueType) }}">
                    @csrf
                    @method('POST')
                    <button type="submit"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                        {{ $issueType->is_need_approval ? 'Set to Auto Approve' : 'Set to Need Approval' }}
                    </button>
                </form>

                <a href="{{ route('issue-types.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    Back to List
                </a>
            </div>
        </div>
    </div>
@endsection

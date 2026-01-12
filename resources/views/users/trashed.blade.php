@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Deleted Users" />
    
    <div class="grid grid-cols-1 gap-6">
        <!-- Trashed Users Table -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-trash me-2"></i>Deleted Users
                </h3>
                <div class="flex space-x-3">
                    <a href="{{ route('users.index') }}" 
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        <i class="bi bi-arrow-left me-2"></i> Back to Users
                    </a>
                </div>
            </div>

            @if($users->count() > 0)
                <!-- Trashed Users Table Component -->
                @php
                    $trashedTransactions = $users->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->full_name,
                            'email' => $user->email,
                            'image' => 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&background=random',
                            'date' => $user->deleted_at->format('M d, Y h:i A'),
                            'price' => '-',
                            'category' => $user->division?->name ?? 'N/A',
                            'status' => 'Deleted',
                        ];
                    })->toArray();
                @endphp

                <x-tables.basic-tables.basic-tables-three :transactions="$trashedTransactions" />
                
                <!-- Custom Actions for Trashed Users -->
                <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <p class="mb-3"><i class="bi bi-info-circle me-2"></i>Deleted users can be restored within 30 days.</p>
                        
                        <!-- Bulk Actions -->
                        <div class="flex space-x-3">
                            <button type="button" onclick="restoreAll()"
                                class="inline-flex items-center rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-600 shadow-theme-xs hover:bg-green-100 hover:text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-900/30">
                                <i class="bi bi-arrow-clockwise me-2"></i> Restore All
                            </button>
                            <button type="button" onclick="deleteAll()"
                                class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-100 hover:text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                <i class="bi bi-trash3 me-2"></i> Delete All Permanently
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination Links -->
                @if($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            @else
                <div class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <i class="bi bi-check2-circle text-2xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">No Deleted Users</h3>
                    <p class="text-gray-500 dark:text-gray-400">There are no deleted users to display.</p>
                    <a href="{{ route('users.index') }}" 
                        class="mt-4 inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <i class="bi bi-arrow-left me-2"></i> Go Back to Users
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Confirmation Modals -->
    <x-ui.modal x-data="{ open: false, action: '' }" x-cloak>
        <x-slot name="button">
            <div x-show="false"></div>
        </x-slot>
        
        <x-slot name="content">
            <div class="p-6">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full" 
                     :class="action === 'restore' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                    <i :class="action === 'restore' ? 'bi bi-arrow-clockwise text-green-600 dark:text-green-400 text-xl' : 'bi bi-exclamation-triangle text-red-600 dark:text-red-400 text-xl'"></i>
                </div>
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" x-text="action === 'restore' ? 'Restore All Users' : 'Permanently Delete All Users'">
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-text="action === 'restore' ? 'Are you sure you want to restore all deleted users?' : 'Are you sure you want to permanently delete all users? This action cannot be undone.'">
                    </p>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="open = false" type="button"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <form :action="action === 'restore' ? '{{ route('users.restore.all') }}' : '{{ route('users.forceDelete.all') }}'" method="POST">
                        @csrf
                        <button type="submit"
                            :class="action === 'restore' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'"
                            class="rounded-lg px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <span x-text="action === 'restore' ? 'Restore All' : 'Delete All Permanently'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </x-slot>
    </x-ui.modal>

    <script>
        function restoreAll() {
            const modal = document.querySelector('[x-data]');
            if (modal) {
                modal.__x.$data.action = 'restore';
                modal.__x.$data.open = true;
            }
        }

        function deleteAll() {
            const modal = document.querySelector('[x-data]');
            if (modal) {
                modal.__x.$data.action = 'delete';
                modal.__x.$data.open = true;
            }
        }

        function restoreUser(userId) {
            if (confirm('Are you sure you want to restore this user?')) {
                fetch(`/users/${userId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function forceDeleteUser(userId) {
            if (confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')) {
                fetch(`/users/${userId}/force-delete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
@endsection
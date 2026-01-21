<!-- resources/views/approvals/index.blade.php -->
@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Approval Requests" />

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-shield-check me-2"></i>Pending Approvals
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Review and approve maintenance requests that require your authorization
                </p>
            </div>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $approvals->total() }} pending approvals
            </div>
        </div>

        <!-- Approval Requests -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($approvals as $request)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <!-- Request Info -->
                        <div class="flex-1">
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-800 dark:text-white">
                                        {{ $request->item->name }}
                                    </h3>
                                    <span
                                        class="text-sm font-medium {{ $request->getPriorityBadgeClass() }} px-3 py-1 rounded-full">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i class="bi bi-ticket"></i>
                                        {{ $request->ticket_number }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="bi bi-clock"></i>
                                        {{ $request->requested_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <!-- Issue Type -->
                                <div>
                                    <span class="text-xs font-medium text-gray-500">Issue Type</span>
                                    <p class="text-sm text-gray-800 dark:text-white">
                                        {{ $request->issueType->name }}
                                        @if ($request->issueType->is_need_approval)
                                            <span class="text-yellow-600 text-xs ml-2">
                                                <i class="bi bi-shield-exclamation"></i> Requires Approval
                                            </span>
                                        @endif
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <span class="text-xs font-medium text-gray-500">Description</span>
                                    <p class="text-sm text-gray-800 dark:text-white">
                                        {{ Str::limit($request->description, 150) }}
                                    </p>
                                </div>

                                <!-- Requester Info -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs font-medium text-gray-500">Requester</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <img src="{{ $request->user->avatar_url }}" class="w-6 h-6 rounded-full">
                                            <span class="text-sm text-gray-800 dark:text-white">
                                                {{ $request->user->full_name }}
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-xs font-medium text-gray-500">Department</span>
                                        <p class="text-sm text-gray-800 dark:text-white">
                                            @if ($request->user->division)
                                                {{ $request->user->division->name }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Actions -->
                        <div class="lg:w-64">
                            <div class="space-y-3">
                                <form action="{{ route('approvals.approve', $request) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to approve this request?')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 flex items-center justify-center gap-2">
                                        <i class="bi bi-check-lg"></i>
                                        Approve Request
                                    </button>
                                </form>

                                <button type="button" onclick="showRejectionModal('{{ $request->id }}')"
                                    class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 flex items-center justify-center gap-2">
                                    <i class="bi bi-x-lg"></i>
                                    Reject Request
                                </button>

                                <a href="{{ route('maintenance-requests.show', $request) }}"
                                    class="block w-full border border-gray-300 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 text-center">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-12 dark:border-gray-800 dark:bg-white/[0.03] text-center">
                    <i class="bi bi-check2-circle text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-2">
                        No Pending Approvals
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        You don't have any maintenance requests waiting for your approval.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($approvals->hasPages())
            <div class="mt-6">
                {{ $approvals->links('vendor.pagination.dashboard') }}
            </div>
        @endif
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900" @click.away="hideRejectionModal()">
            <h4 class="font-semibold text-gray-800 dark:text-white mb-4">
                Reject Maintenance Request
            </h4>

            <form id="rejectionForm" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reason for Rejection *
                    </label>
                    <textarea name="rejection_reason" rows="4" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        placeholder="Please provide a reason for rejecting this request..."></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideRejectionModal()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectionModal(requestId) {
            const form = document.getElementById('rejectionForm');
            form.action = `/approvals/${requestId}/reject`;
            document.getElementById('rejectionModal').classList.remove('hidden');
            document.getElementById('rejectionModal').classList.add('flex');
        }

        function hideRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
            document.getElementById('rejectionModal').classList.remove('flex');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideRejectionModal();
            }
        });
    </script>
@endsection

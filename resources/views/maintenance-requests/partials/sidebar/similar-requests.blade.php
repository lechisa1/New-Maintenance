@if ($similarRequests->count() > 0 && auth()->user()->can('maintenance_requests.assign' || 'maintenance_requests.resolve'))
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
            <i class="bi bi-diagram-3 me-2"></i>Similar Requests
        </h3>

        <div class="space-y-3">
            @foreach ($similarRequests as $similarRequest)
                <a href="{{ route('maintenance-requests.show', $similarRequest) }}"
                    class="block rounded-lg border border-gray-200 p-3 hover:border-blue-300 hover:bg-blue-50 dark:border-gray-700 dark:hover:border-blue-800 dark:hover:bg-blue-900/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white/90">
                                {{ $similarRequest->ticket_number }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $similarRequest->getRequestedDate() }}
                            </div>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $similarRequest->getStatusBadgeClass() }}">
                            {{ $similarRequest->getStatusText() }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
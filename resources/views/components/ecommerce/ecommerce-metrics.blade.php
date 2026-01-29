@props([
    'totalRequests' => 0,
    'pendingRequests' => 0,
    'inProgressRequests' => 0,
    'completedRequests' => 0,
    'assignedToMe' => 0,
])

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
    @php
        // Shared logic for growth/rates
        $completionRate = $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100, 1) : 0;
        $pendingRate = $totalRequests > 0 ? round(($pendingRequests / $totalRequests) * 100, 1) : 0;

        // Configuration array to keep the pattern identical
        $stats = [
            [
                'label' => 'Total Requests',
                'value' => number_format($totalRequests),
                'icon' =>
                    'M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621L18.7807 6.97856L12.3358 10.2009C12.1247 10.3065 11.8762 10.3065 11.665 10.2009L5.22014 6.97856L11.665 3.75621ZM4.29297 8.19203V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0366V11.6513C11.1631 11.6205 11.0777 11.5843 10.9942 11.5426L4.29297 8.19203ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19202L13.0066 11.5426C12.9229 11.5844 12.8372 11.6208 12.75 11.6516V20.037Z',
                'badge' => null,
                'color' => 'gray',
            ],
            [
                'label' => 'Pending',
                'value' => number_format($pendingRequests),
                'icon' =>
                    'M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM12 4C16.4183 4 20 7.58172 20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4ZM12 11V7H10V13H14.5V11H12Z',
                'badge' => $pendingRate . '%',
                'badgeType' => $pendingRate > 50 ? 'error' : 'warning',
            ],
            [
                'label' => 'In Progress',
                'value' => number_format($inProgressRequests),
                'icon' =>
                    'M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2ZM12 4C16.4183 4 20 7.58172 20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4ZM12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6Z',
                'badge' => null,
                'color' => 'blue',
            ],
            [
                'label' => 'Completed',
                'value' => number_format($completedRequests),
                'icon' => 'M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z',
                'badge' => $completionRate . '%',
                'badgeType' => 'success',
            ],
            [
                'label' => 'Assigned to Me',
                'value' => number_format($assignedToMe),
                'icon' =>
                    'M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 16.66 14.67 14 12 14Z',
                'badge' => null,
                'color' => 'purple',
            ],
        ];
    @endphp

    @foreach ($stats as $stat)
        <div
            class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-2xl dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800">
                    <svg class="w-5 h-5 fill-gray-800 dark:fill-white/90" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="{{ $stat['icon'] }}" />
                    </svg>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white/90 leading-tight">
                        {{ $stat['value'] }}
                    </h4>
                </div>
            </div>

            @if ($stat['badge'])
                <span
                    class="px-2 py-0.5 text-xs font-bold rounded-full 
                    {{ $stat['badgeType'] === 'success' ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500' : '' }}
                    {{ $stat['badgeType'] === 'warning' ? 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-500' : '' }}
                    {{ $stat['badgeType'] === 'error' ? 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500' : '' }}">
                    {{ $stat['badge'] }}
                </span>
            @endif
        </div>
    @endforeach
</div>

@php
    $user = auth()->user();
    $unreadCount = $user->unreadNotifications->count();
    $notifications = $user->notifications()->latest()->take(8)->get();
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() { this.dropdownOpen = !this.dropdownOpen },
    closeDropdown() { this.dropdownOpen = false }
}" @click.away="closeDropdown()">
    <!-- 🔔 Notification Button -->
    <button
        class="relative flex items-center justify-center h-11 w-11 rounded-full
               border border-gray-200 bg-white text-gray-500
               hover:bg-gray-100 hover:text-gray-700
               dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400
               dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()" type="button">
        <!-- Unread Badge -->
        <!-- Unread Badge -->
        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 rounded-full bg-red-500 text-white text-xs font-bold">
                {{ $unreadCount }}
            </span>
        @endif


        <!-- Bell Icon -->
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M10 1.5c-3.3 0-6 2.7-6 6v5H3a1 1 0 0 0 0 2h14a1 1 0 0 0 0-2h-1v-5c0-3.3-2.7-6-6-6zm-2 15a2 2 0 0 0 4 0H8z" />
        </svg>
    </button>

    <!-- 📥 Dropdown -->
    <div x-show="dropdownOpen" x-transition
        class="absolute right-0 mt-4 w-[360px] max-h-[480px]
               rounded-2xl border border-gray-200 bg-white shadow-lg
               dark:border-gray-800 dark:bg-gray-900"
        style="display: none;">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b dark:border-gray-800">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white">
                Notifications
            </h5>

            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button class="text-sm text-primary-600 hover:underline">
                    Mark all as read
                </button>
            </form>
        </div>

        <!-- Notification List -->
        <ul class="overflow-y-auto max-h-[360px]">
            @forelse ($notifications as $notification)
                <li>
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex gap-3 px-4 py-3 border-b
                                   hover:bg-gray-100 dark:hover:bg-gray-800
                                   {{ is_null($notification->read_at) ? 'bg-gray-50 dark:bg-gray-800/50' : '' }}">
                            <!-- Icon -->
                            <span class="mt-1 text-primary-600">
                                <i class="{{ $notification->data['icon'] ?? 'bi-bell' }}"></i>
                            </span>

                            <!-- Content -->
                            <span class="flex-1">
                                <span class="block text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $notification->data['message'] }}
                                </span>

                                <span class="block text-xs text-gray-500">
                                    Ticket: {{ $notification->data['ticket_number'] ?? '-' }}
                                </span>

                                <span class="block text-xs text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </span>
                        </button>
                    </form>
                </li>
            @empty
                <li class="px-4 py-6 text-center text-gray-500">
                    No notifications
                </li>
            @endforelse
        </ul>

        <!-- View All -->
        <a href="{{ route('notifications.index') }}"
            class="block text-center px-4 py-3 text-sm font-medium
                   hover:bg-gray-100 dark:hover:bg-gray-800">
            View All Notifications
        </a>
    </div>
</div>

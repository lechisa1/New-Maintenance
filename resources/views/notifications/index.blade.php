@extends('layouts.app') 

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">All Notifications</h1>
            
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($notifications as $notification)
                    <div class="p-4 sm:p-6 transition hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $notification->read_at ? '' : 'bg-primary-50/30 dark:bg-primary-900/10 border-l-4 border-primary-500' }}">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400">
                                    <i class="{{ $notification->data['icon'] ?? 'bi-bell' }} text-lg"></i>
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <time class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </time>
                                </div>
                                
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Ticket Number: <span class="font-mono font-medium">{{ $notification->data['ticket_number'] ?? 'N/A' }}</span>
                                </p>

                                <div class="flex gap-3">
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs font-medium text-primary-600 hover:underline">
                                            View Details
                                        </button>
                                    </form>
                                    
                                    @if(!$notification->read_at)
                                        <span class="text-gray-300 dark:text-gray-700">•</span>
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-gray-500 hover:underline">
                                                Mark as read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-20 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                            <i class="bi bi-bell-slash text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400">You have no notifications yet.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
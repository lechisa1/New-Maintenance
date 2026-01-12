<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 dark:border-gray-800 rounded-lg">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold">User</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Division</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Cluster</th>
                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <!-- Avatar + Name -->
                    <td class="px-4 py-3 flex items-center gap-3">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}"
                            class="w-9 h-9 rounded-full"
                            alt="avatar"
                        >
                        <div>
                            <div class="font-medium text-gray-800 dark:text-white">
                                {{ $user->full_name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Joined {{ $user->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </td>

                    <!-- Email -->
                    <td class="px-4 py-3 text-sm">
                        {{ $user->email }}
                    </td>

                    <!-- Division -->
                    <td class="px-4 py-3 text-sm">
                        {{ $user->division?->name ?? 'N/A' }}
                    </td>

                    <!-- Cluster -->
                    <td class="px-4 py-3 text-sm">
                        {{ $user->cluster?->name ?? 'N/A' }}
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-3">
                        @if($user->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                Inactive
                            </span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('users.show', $user) }}"
                               class="text-blue-600 hover:underline text-sm">
                                View
                            </a>

                            <a href="{{ route('users.edit', $user) }}"
                               class="text-yellow-600 hover:underline text-sm">
                                Edit
                            </a>

                            <form action="{{ route('users.destroy', $user) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        No users found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

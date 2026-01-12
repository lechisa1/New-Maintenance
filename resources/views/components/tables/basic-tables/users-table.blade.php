@props(['users' => []])

@php
$jsonUsers = json_encode($users, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
@endphp

<div x-data="{
    users: {!! $jsonUsers !!},
    itemsPerPage: 5,
    currentPage: 1,
    
    get totalPages() {
        return Math.max(1, Math.ceil(this.users.length / this.itemsPerPage));
    },
    
    get paginatedUsers() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        return this.users.slice(start, start + this.itemsPerPage);
    }
}">
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="px-5 mb-4 sm:px-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Users List</h3>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <span x-text="'Total: ' + users.length + ' users'"></span>
                <span x-show="users.length > 0" x-text="' | Showing page ' + currentPage + ' of ' + totalPages"></span>
            </div>
        </div>

        <!-- Simple Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Avatar</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Date Joined</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-if="users.length === 0">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No users available
                            </td>
                        </tr>
                    </template>
                    <template x-for="user in paginatedUsers" :key="user.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <img class="w-8 h-8 rounded-full" :src="user.image" :alt="user.name">
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white" x-text="user.name"></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="user.email"></td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400" x-text="user.date_joined"></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                      :class="user.status === 'Verified' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'"
                                      x-text="user.status"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Simple Pagination -->
        <div x-show="totalPages > 1" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <button @click="currentPage--" :disabled="currentPage === 1" 
                        class="px-3 py-1 text-sm border rounded disabled:opacity-50">
                    Previous
                </button>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                </span>
                <button @click="currentPage++" :disabled="currentPage === totalPages" 
                        class="px-3 py-1 text-sm border rounded disabled:opacity-50">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>
@props(['users'])

<div x-show="showModal" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100">

    <div @click.away="closeModal()" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-800"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        <div class="mb-6 flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                <span x-text="editingId ? 'Edit' : 'Add New'"></span>
                <span x-text="getSingularLabel()"></span>
            </h3>
            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form @submit.prevent="saveEntry()">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text" x-model="formData.name" required
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:text-white">
                </div>

                <template x-if="view === 'clusters' || view === 'divisions'">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Chairman</label>
                        <select x-model="formData.chairman_id"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 dark:border-gray-700 dark:text-white">
                            <option value="">Select a Chairman</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" @click="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

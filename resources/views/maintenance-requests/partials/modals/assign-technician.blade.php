<div x-show="showAssignModal" x-cloak style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Assign Request to Technician
            </h3>
            <button @click="showAssignModal = false"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('maintenance-requests.assign', $maintenanceRequest) }}" method="POST" class="mt-4"
            id="assign-technician-form">
            @csrf
            @method('PUT')
            <!-- Form fields -->
        </form>
    </div>
</div>

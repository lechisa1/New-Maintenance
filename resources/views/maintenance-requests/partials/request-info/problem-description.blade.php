<div class="mt-6" x-data="{
    expanded: false,
    text: @js($maintenanceRequest->description),
    limit: 200
}">

    <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">
        <i class="bi bi-chat-dots me-2"></i>Problem Description
    </h4>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
        <p class="break-words whitespace-pre-wrap text-gray-800 dark:text-white/90"
            x-text="expanded || text.length <= limit 
                    ? text 
                    : text.substring(0, limit) + '...'">
        </p>

        <!-- See more / See less -->
        <template x-if="text.length > limit">
            <div class="mt-2 flex gap-4">
                <button @click="expanded = !expanded"
                    class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">
                    <span x-text="expanded ? 'See less' : 'See more'"></span>
                </button>

                <a href="{{ route('maintenance.description.pdf', $maintenanceRequest) }}" target="_blank"
                    class="text-sm font-medium text-gray-600 hover:underline dark:text-gray-400">
                    <i class="bi bi-file-earmark-pdf me-1"></i> View as PDF
                </a>
            </div>
        </template>



    </div>
</div>

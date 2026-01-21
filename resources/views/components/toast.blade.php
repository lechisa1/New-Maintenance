@if (session('success') || session('error') || session('warning') || session('info'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2" class="fixed top-4 right-4 z-50 max-w-sm">
        <div
            class="rounded-lg border p-4 shadow-lg {{ session('success') ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : (session('error') ? 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800' : (session('warning') ? 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-800' : 'bg-blue-50 border-blue-200 dark:bg-blue-900/20 dark:border-blue-800')) }}">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i
                        class="bi bi-{{ session('success') ? 'check-circle' : (session('error') ? 'x-circle' : (session('warning') ? 'exclamation-triangle' : 'info-circle')) }} 
                        {{ session('success') ? 'text-green-600 dark:text-green-400' : (session('error') ? 'text-red-600 dark:text-red-400' : (session('warning') ? 'text-yellow-600 dark:text-yellow-400' : 'text-blue-600 dark:text-blue-400')) }}"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p
                        class="text-sm font-medium {{ session('success') ? 'text-green-800 dark:text-green-300' : (session('error') ? 'text-red-800 dark:text-red-300' : (session('warning') ? 'text-yellow-800 dark:text-yellow-300' : 'text-blue-800 dark:text-blue-300')) }}">
                        {{ session('success') ?? (session('error') ?? (session('warning') ?? session('info'))) }}
                    </p>
                </div>
                <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>
@endif

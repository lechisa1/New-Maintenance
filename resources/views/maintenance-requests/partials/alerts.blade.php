@if (session('success'))
    <div id="alert-success"
        class="mb-6 flex items-center rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 shadow-sm dark:border-green-900/30 dark:bg-green-900/20 dark:text-green-400">
        <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
        <div class="text-sm font-bold">
            {{ session('success') }}
        </div>
        <button type="button" onclick="document.getElementById('alert-success').remove()"
            class="ml-auto text-green-600 hover:text-green-800">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
@endif

@if (session('error') || $errors->any())
    <div id="alert-error"
        class="mb-6 flex items-center rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-400">
        <i class="bi bi-exclamation-triangle-fill mr-3 text-xl"></i>
        <div class="text-sm font-bold">
            {{ session('error') ?? 'Please correct the highlighted errors below.' }}
        </div>
        <button type="button" onclick="document.getElementById('alert-error').remove()"
            class="ml-auto text-red-600 hover:text-red-800">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
@endif

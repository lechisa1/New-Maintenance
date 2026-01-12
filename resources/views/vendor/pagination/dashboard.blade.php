@if ($paginator->hasPages())
<div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
    <div class="flex items-center justify-between">

        <!-- Previous -->
        <a
            href="{{ $paginator->previousPageUrl() }}"
            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium
                   {{ $paginator->onFirstPage() ? 'opacity-50 cursor-not-allowed pointer-events-none' : 'hover:bg-gray-50' }}
                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
            <span>Previous</span>
        </a>

        <!-- Page Numbers -->
        <ul class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                {{-- Dots --}}
                @if (is_string($element))
                    <li class="flex h-10 w-10 items-center justify-center text-gray-500">
                        {{ $element }}
                    </li>
                @endif

                {{-- Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            <a href="{{ $url }}"
                               class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium
                               {{ $page == $paginator->currentPage()
                                    ? 'bg-blue-500 text-white'
                                    : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500'
                               }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        <!-- Next -->
        <a
            href="{{ $paginator->nextPageUrl() }}"
            class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium
                   {{ $paginator->hasMorePages() ? 'hover:bg-gray-50' : 'opacity-50 cursor-not-allowed pointer-events-none' }}
                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
            <span>Next</span>
        </a>

    </div>
</div>
@endif

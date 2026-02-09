{{-- @props(['pageTitle' => 'Page'])

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        {{ $pageTitle }}
    </h2>
    <nav>
        <ol class="flex items-center gap-1.5">
            <li>
                <a
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                    href="{{ url('/') }}"
                >
                    Home
                    <svg
                        class="stroke-current"
                        width="17"
                        height="16"
                        viewBox="0 0 17 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                            stroke=""
                            stroke-width="1.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                </a>
            </li>
            <li class="text-sm text-gray-800 dark:text-white/90">
                {{ $pageTitle }}
            </li>
        </ol>
    </nav>
</div> --}}
{{-- resources/views/components/common/page-breadcrumb.blade.php --}}
@props(['breadcrumbs' => []])

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <!-- Page Title -->
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        {{ end($breadcrumbs)['label'] ?? 'Page' }}
    </h2>

    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb">
        <ol class="flex items-center gap-1.5 text-sm">
            @foreach ($breadcrumbs as $index => $breadcrumb)
                @php
                    $isLast = $index === count($breadcrumbs) - 1;
                @endphp

                <li class="flex items-center gap-1.5">
                    @if (!$isLast && !empty($breadcrumb['url']))
                        <!-- Normal breadcrumb -->
                        <a href="{{ $breadcrumb['url'] }}"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 inline-flex items-center gap-1.5">
                            {{ $breadcrumb['label'] }}
                            <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke-width="1.2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @else
                        <!-- Active breadcrumb (current page) -->
                        <span aria-current="page"
                            class="font-semibold text-blue-600 dark:text-blue-400 border-b-2 border-blue-500 pb-0.5">
                            {{ $breadcrumb['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>

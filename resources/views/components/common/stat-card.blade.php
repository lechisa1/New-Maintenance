@props([
    'title' => '',
    'value' => 0,
    'icon' => '',
    'variant' => 'primary'
])

@php
    $variants = [
        'primary' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'success' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        'warning' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
        'danger'  => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $variantClass }}">
        <i class="{{ $icon }} text-xl"></i>
    </div>

    <div>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $title }}
        </p>
        <h4 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
            {{ $value }}
        </h4>
    </div>
</div>

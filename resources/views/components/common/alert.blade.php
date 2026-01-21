@props(['variant' => 'info', 'title' => null, 'message' => null])

@php
    $classes =
        [
            'success' =>
                'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400',
            'error' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400',
            'info' =>
                'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400',
        ][$variant] ?? 'bg-gray-50 border-gray-200 text-gray-800';
@endphp

{{-- Use $attributes->merge to ensure Alpine's :variant works on the outer div --}}
<div {{ $attributes->merge(['class' => "p-4 border rounded-xl flex items-start gap-3 $classes"]) }}
    @if ($attributes->has('::variant')) :class="
        {
            'bg-green-50 border-green-200 text-green-800': alert.variant === 'success',
            'bg-red-50 border-red-200 text-red-800': alert.variant === 'error',
            'bg-blue-50 border-blue-200 text-blue-800': alert.variant === 'info',
        }" @endif>

    <div class="flex-1">
        {{-- Display PHP Title OR Alpine Title --}}
        <h4 class="text-sm font-bold uppercase tracking-wide">
            @if ($title)
                {{ $title }}
            @else
                <span x-text="alert.title"></span>
            @endif
        </h4>

        {{-- Display PHP Message OR Alpine Message --}}
        <div class="mt-1 text-sm opacity-90">
            @if ($message)
                {{ $message }}
            @else
                <span x-text="alert.message"></span>
            @endif
        </div>
    </div>
</div>

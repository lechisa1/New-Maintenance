@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Permission Matrix</h2>
            <p class="text-sm text-gray-500">Global system control for feature access</p>
        </div>

        {{-- Filter Bar --}}
        <form action="{{ route('permissions.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search resource..." 
                    class="rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <i class="bi bi-search absolute left-3 top-2.5 text-gray-400"></i>
            </div>

            <select name="status" onchange="this.form.submit()" 
                class="rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
            </select>

            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('permissions.index') }}" class="text-sm text-red-600 hover:underline dark:text-red-400">Clear Filters</a>
            @endif
        </form>
    </div>

    @if($grouped->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 py-12 text-center dark:border-gray-700">
            <i class="bi bi-shield-slash text-4xl text-gray-300"></i>
            <p class="mt-2 text-gray-500">No permissions found matching your filters.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Resource</th>
                            @foreach ($actions as $action)
                                <th class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-500">
                                    {{ $action }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-transparent">
                        @foreach ($grouped as $resource => $permissions)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ ucwords(str_replace('_', ' ', $resource)) }}
                                </td>

                                @foreach ($actions as $action)
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $perm = $permissions->first(
                                                fn($p) => str_ends_with($p->name, '.' . $action) || $p->name == $action,
                                            );
                                        @endphp

                                        @if ($perm)
                                            <form action="{{ route('permissions.toggle', $perm->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none {{ $perm->is_active ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-700' }}">
                                                    <span class="sr-only">Toggle {{ $perm->name }}</span>
                                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition duration-200 ease-in-out {{ $perm->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-700">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
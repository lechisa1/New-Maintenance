@extends('layouts.app')

@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Organizations', 'url' => route('organizations.index')],
            [
                'label' => $division->cluster->organization->name,
                'url' => route('organizations.show', $division->cluster->organization),
            ],
            [
                'label' => $division->cluster->name,
                'url' => route('clusters.divisions', $division->cluster),
            ],
            ['label' => $division->name],
        ];
    @endphp

    @include('maintenance-requests.partials.alerts')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-5">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-500/10">
                        <i class="bi bi-building-gear text-3xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                Division Level
                            </span>
                        </div>
                        <h2 class="mt-1 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                            {{ $division->name }}</h2>
                        <nav class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                            <span>{{ $division->cluster->organization->name }}</span>
                            <i class="bi bi-chevron-right text-[10px]"></i>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $division->cluster->name }}</span>
                        </nav>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ url()->previous() }}"
                        class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        <i class="bi bi-arrow-left me-2"></i> Back
                    </a>
                    <button onclick="confirmDelete('{{ $division->id }}')"
                        class="inline-flex items-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100 dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-400">
                        <i class="bi bi-trash me-2"></i> Delete
                    </button>
                    <button onclick="editDivision()"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700 hover:shadow-blue-300 dark:shadow-none">
                        <i class="bi bi-pencil-square me-2"></i> Edit Details
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

            <div
                class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all hover:shadow-md dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white dark:bg-blue-900/20">
                        <i class="bi bi-building text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Organization</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            {{ $division->cluster->organization->name }}</p>
                    </div>
                </div>
            </div>

            <div
                class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all hover:shadow-md dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-900/20">
                            <i class="bi bi-diagram-3 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Parent Cluster</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $division->cluster->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3 dark:border-gray-800">
                    <span class="text-xs text-gray-500">Chairman:</span>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ $division->cluster->chairman->full_name ?? 'N/A' }}
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border-2 border-blue-500 bg-blue-600 p-5 shadow-xl shadow-blue-100 dark:shadow-none">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-md">
                        <i class="bi bi-person-badge text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-blue-100">Division Chairman</p>
                        <p class="text-xl font-black text-white">
                            {{ $division->chairman->full_name ?? 'Vacant Position' }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-medium text-blue-100/80">
                    <span>Direct Supervisor of {{ $division->users_count }} members</span>
                    <i class="bi bi-shield-check text-base"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-100 p-6 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Personnel Directory</h3>
                    <span
                        class="ml-2 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        {{ $division->users_count }}
                    </span>
                </div>
                <button class="text-sm font-bold text-blue-600 hover:underline">View All Personnel</button>
            </div>

            <div class="py-20 text-center">
                <div
                    class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 text-gray-300 dark:bg-gray-800/50">
                    <i class="bi bi-people text-4xl"></i>
                </div>
                <h4 class="mt-5 text-lg font-bold text-gray-800 dark:text-white">No Team Members Listed Yet</h4>
                <p class="mx-auto mt-2 max-w-xs text-sm text-gray-500">
                    Personnel data for this division is currently being processed and will appear here shortly.
                </p>
                <button
                    class="mt-6 rounded-xl bg-gray-900 px-6 py-2 text-sm font-bold text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900">
                    Assign Member
                </button>
            </div>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-sm overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-900">
            <div class="p-8 text-center">
                <div
                    class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-500">
                    <i class="bi bi-exclamation-octagon text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Delete Division?</h3>
                <p class="mt-3 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                    This action will permanently remove <span
                        class="font-bold text-gray-700 dark:text-gray-200">{{ $division->name }}</span>. This cannot be
                    undone.
                </p>
            </div>
            <div class="flex gap-0 border-t border-gray-100 dark:border-gray-800">
                <button onclick="closeDeleteModal()"
                    class="flex-1 px-6 py-4 text-sm font-bold text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                    Cancel
                </button>
                <button id="confirmDeleteBtn"
                    class="flex-1 border-l border-gray-100 px-6 py-4 text-sm font-bold text-red-600 hover:bg-red-50 dark:border-gray-800 dark:hover:bg-red-900/20">
                    Confirm Delete
                </button>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;

        function confirmDelete(id) {
            deleteId = id;
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            deleteId = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!deleteId) return;

            const btn = document.getElementById('confirmDeleteBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';

            try {
                const response = await fetch(`/api/divisions/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    // Redirect back to the cluster view since the division no longer exists
                    window.location.href = "{{ route('clusters.divisions', $division->cluster) }}";
                } else {
                    alert('Action failed. Please try again.');
                    btn.disabled = false;
                    btn.innerText = 'Confirm Delete';
                }
            } catch (error) {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerText = 'Confirm Delete';
            }
        });

        function editDivision() {
            // Logic to trigger the same edit modal pattern used in the list view
            window.location.href = "{{ route('clusters.divisions', $division->cluster) }}?edit={{ $division->id }}";
        }
        // Auto-hide success alert after 5 seconds
        const successAlert = document.getElementById('alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 500);
            }, 5000);
        }
    </script>
@endsection

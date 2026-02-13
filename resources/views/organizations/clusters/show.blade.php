@extends('layouts.app')
@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Organizations', 'url' => route('organizations.index')],
        [
            'label' => $cluster->organization->name,
            'url' => route('organizations.show', $cluster->organization),
        ],
        ['label' => $cluster->name],
    ];
@endphp


@section('content')
    <x-common.page-breadcrumb :breadcrumbs="$breadcrumbs" />

    @include('maintenance-requests.partials.alerts')
    <div class="space-y-6">
        {{-- Cluster Info Header --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10">
                        <i class="bi bi-diagram-3 text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $cluster->name }}</h2>
                        <p class="text-sm text-gray-500">
                            Total Divisions: <span class="font-semibold text-blue-600">{{ $cluster->divisions_count }}</span>
                        </p>
                    </div>
                </div>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>

        {{-- Divisions Table Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                        <i class="bi bi-building me-2 text-blue-500"></i> Divisions
                    </h3>

                    <div class="flex flex-1 items-center justify-end gap-3">
                        <form action="{{ route('clusters.divisions', $cluster) }}" method="GET"
                            class="relative w-full max-w-sm">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search divisions..."
                                class="h-10 w-full rounded-lg border border-gray-300 pl-10 pr-4 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </form>

                        <button onclick="openDivisionModal()"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
                            <i class="bi bi-plus-lg me-2"></i> Add Division
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-gray-500 dark:text-white">#</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-gray-500 dark:text-white">
                                Division Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-gray-500 dark:text-white">
                                Chairman</th>
                            <th class="px-6 py-4 text-center text-xs font-bold uppercase text-gray-500 dark:text-white">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                        @forelse ($divisions as $index => $division)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-white">
                                    {{ $divisions->firstItem() + $index }}</td>
                                <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ $division->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-white">
                                    {{ $division->chairman->full_name ?? 'Not Assigned' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-3">
                                        <a href="{{ route('divisions.show', $division) }}"
                                            class="text-blue-500 hover:text-blue-700" title="View"><i
                                                class="bi bi-eye"></i></a>
                                        <button
                                            onclick="editDivision('{{ $division->id }}', '{{ addslashes($division->name) }}', '{{ $division->division_chairman }}')"
                                            class="text-amber-500 hover:text-amber-700"><i
                                                class="bi bi-pencil"></i></button>
                                        <button onclick="confirmDelete('{{ $division->id }}')"
                                            class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No divisions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div id="divisionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <h3 id="modalTitle" class="mb-4 text-lg font-bold text-gray-800 dark:text-white">Add Division</h3>
            <form id="divisionForm">
                @csrf
                <input type="hidden" name="id" id="divisionId">
                <input type="hidden" name="cluster_id" value="{{ $cluster->id }}">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Division Name
                            *</label>
                        <input type="text" name="name" id="divisionName" required
                            class="w-full rounded-lg border border-gray-300 p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Division
                            Chairman</label>
                        <select name="division_chairman" id="divisionChairman"
                            class="w-full rounded-lg border border-gray-300 p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">-- Select Chairman --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeDivisionModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-600">Cancel</button>
                    <button type="submit" id="submitBtn"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-xl dark:bg-gray-900">
            <div
                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-500/10">
                <i class="bi bi-exclamation-triangle text-3xl"></i>
            </div>
            <h3 class="mb-2 text-xl font-bold text-gray-800 dark:text-white">Delete Division?</h3>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                Are you sure you want to delete this division? This action cannot be undone.
            </p>
            <div class="flex justify-center gap-3">
                <button onclick="closeDeleteModal()"
                    class="w-full rounded-lg border border-gray-300 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300">
                    Cancel
                </button>
                <button id="confirmDeleteBtn"
                    class="w-full rounded-lg bg-red-600 py-2 text-sm font-medium text-white hover:bg-red-700">
                    Delete Now
                </button>
            </div>
        </div>
    </div>

    <script>
        let isEditMode = false;
        let deleteId = null;

        function openDivisionModal(id = null, name = '', chairmanId = '') {
            isEditMode = !!id;
            document.getElementById('divisionId').value = id || '';
            document.getElementById('divisionName').value = name || '';
            document.getElementById('divisionChairman').value = chairmanId || '';
            document.getElementById('modalTitle').innerText = isEditMode ? 'Edit Division' : 'Add Division';
            document.getElementById('divisionModal').classList.replace('hidden', 'flex');
        }

        function closeDivisionModal() {
            document.getElementById('divisionModal').classList.replace('flex', 'hidden');
            document.getElementById('divisionForm').reset();
        }

        function editDivision(id, name, chairmanId) {
            openDivisionModal(id, name, chairmanId);
        }

        // Delete Confirmation Logic
        function confirmDelete(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.replace('hidden', 'flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.replace('flex', 'hidden');
            deleteId = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!deleteId) return;
            const btn = document.getElementById('confirmDeleteBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin mr-2"></i>Deleting...';

            try {
                const res = await fetch(`/api/divisions/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) window.location.reload();
                else alert('Delete failed');
            } catch (error) {
                console.error(error);
            } finally {
                btn.disabled = false;
                btn.innerText = 'Delete Now';
            }
        });

        document.getElementById('divisionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;

            const id = document.getElementById('divisionId').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const url = isEditMode ? `/api/divisions/${id}` : '/api/divisions';
            const method = isEditMode ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (res.ok) window.location.reload();
                else alert('Error saving division');
            } catch (error) {
                console.error(error);
            } finally {
                submitBtn.disabled = false;
            }
        });
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

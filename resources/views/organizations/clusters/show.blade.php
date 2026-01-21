@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Cluster Detail" :links="[
        ['label' => 'Organizations', 'url' => route('organizations.index')],
        ['label' => 'Clusters'],
        ['label' => $cluster->name],
    ]" />

    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        <i class="bi bi-diagram-3 me-2 text-blue-500"></i>
                        {{ $cluster->name }}
                    </h2>
                    <p class="text-sm text-gray-500">Total Divisions: {{ $cluster->divisions_count }}</p>
                </div>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                    <i class="bi bi-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    <i class="bi bi-building me-2"></i> Divisions
                </h3>
                <button onclick="openDivisionModal()"
                    class="inline-flex items-center rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-600">
                    <i class="bi bi-plus-lg me-2"></i> Add Division
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg dark:border-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Division Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold">Chairman</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($divisions as $index => $division)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3">{{ $divisions->firstItem() + $index }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $division->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $division->chairman->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-5">
                                        <a href="{{ route('divisions.show', $division) }}"
                                            class="text-blue-600 hover:text-blue-800">View Details</a>
                                        <button
                                            onclick="editDivision('{{ $division->id }}', '{{ addslashes($division->name) }}', '{{ $division->division_chairman }}')"
                                            class="text-yellow-600 hover:text-yellow-800">Edit</button>

                                        <button onclick="deleteDivision('{{ $division->id }}')"
                                            class="text-red-600 hover:text-red-800">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No divisions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="divisionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Add Division</h3>
                <button onclick="closeDivisionModal()" class="text-gray-400 hover:text-gray-600"><i
                        class="bi bi-x-lg"></i></button>
            </div>
            <form id="divisionForm">
                @csrf
                <input type="hidden" name="id" id="divisionId">
                <input type="hidden" name="cluster_id" value="{{ $cluster->id }}">

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Division Name *</label>
                    <input type="text" name="name" id="divisionName" required
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div class="mb-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Division Chairman</label>
                    <select name="division_chairman" id="divisionChairman"
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm focus:border-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">-- Select Chairman --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeDivisionModal()"
                        class="rounded-lg border px-4 py-2.5 text-sm font-medium text-gray-700">Cancel</button>
                    <button type="submit" id="submitBtn"
                        class="rounded-lg bg-blue-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-600">Save
                        Division</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let isEditMode = false;

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

        async function deleteDivision(id) {
            if (!confirm('Are you sure?')) return;
            const res = await fetch(`/api/divisions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            if (res.ok) window.location.reload();
        }

        document.getElementById('divisionForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('divisionId').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const url = isEditMode ? `/api/divisions/${id}` : '/api/divisions';
            const method = isEditMode ? 'PUT' : 'POST';

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
        });
    </script>
@endsection

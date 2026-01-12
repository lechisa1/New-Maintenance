@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Equipment List" />

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                <i class="bi bi-pc-display me-2"></i>Equipment List
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Manage all registered equipment
            </p>
        </div>
        
        <a href=""
            class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
            <i class="bi bi-plus-lg me-2"></i>Add Equipment
        </a>
    </div>

    <!-- Equipment Table with DataTable Component -->
    <x-data-table 
        :data="$equipment"
        :columns="[
            ['key' => 'id', 'label' => 'ID', 'sortable' => true],
            ['key' => 'name', 'label' => 'Equipment Name', 'sortable' => true],
            ['key' => 'type', 'label' => 'Type', 'sortable' => true],
            ['key' => 'unit', 'label' => 'Unit', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'type' => 'status'],
            ['key' => 'created_at', 'label' => 'Registered On', 'sortable' => true, 'format' => 'date'],
        ]"
        :actions="[
            [
                'name' => 'view',
                'label' => 'View',
                'icon' => 'bi bi-eye',
                'url' => '/equipment/:id',
                'showLabel' => false,
                'tooltip' => 'View Details',
            ],
            [
                'name' => 'edit',
                'label' => 'Edit',
                'icon' => 'bi bi-pencil',
                'url' => '/equipment/:id/edit',
                'showLabel' => false,
                'tooltip' => 'Edit Equipment',
            ],
            [
                'name' => 'delete',
                'label' => 'Delete',
                'icon' => 'bi bi-trash',
                'class' => 'text-red-600 hover:text-red-800 dark:text-red-400',
                'showLabel' => false,
                'tooltip' => 'Delete Equipment',
            ],
        ]"
        :bulk-actions="[]"
        searchable="true"
        pagination="true"
        exportable="true"
        empty-message="No equipment registered yet."
        search-placeholder="Search equipment..."
        table-id="equipmentTable"
    />
</div>
@endsection
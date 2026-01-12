@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Transactions" />

@php
$columns = [
    'image' => 'Image',
    'name' => 'Name',
    'date' => 'Date',
    'price' => 'Price',
    'category' => 'Category',
    'status' => 'Status',
];

$formats = [
    'status' => [
        'Success' => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500 px-2 py-1 rounded-full',
        'Pending' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400 px-2 py-1 rounded-full',
        'Failed'  => 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-500 px-2 py-1 rounded-full',
    ],
];

// Prepare JSON for Alpine.js
$jsonTransactions = json_encode($transactions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
@endphp

<!-- Simple Alpine.js test - FIXED -->
<div x-data="{
    test: 'Alpine is working!', 
    items: {!! $jsonTransactions !!}
}" class="mb-4 p-4 bg-blue-50 rounded">
    <h3 class="font-bold mb-2">Alpine.js Test:</h3>
    <p x-text="test"></p>
    <p x-text="'Items count: ' + items.length"></p>
    <div x-show="items.length > 0">
        <p>First item name: <span x-text="items[0].name"></span></p>
    </div>
    <button @click="test = 'Button clicked!'" class="mt-2 px-3 py-1 bg-blue-500 text-white rounded">
        Test Click
    </button>
</div>

{{-- Debug section --}}
<div class="mb-4 p-4 bg-gray-100 rounded-lg">
    <h3 class="font-bold mb-2">Debug Information:</h3>
    <p>Transactions count: {{ count($transactions ?? []) }}</p>
    <p>Is array: {{ is_array($transactions) ? 'yes' : 'no' }}</p>
    <p>Data type: {{ gettype($transactions) }}</p>
    <p>First item: {{ json_encode($transactions[0] ?? 'No data') }}</p>
</div>

<x-common.component-card title="Latest Transactions">
    @if(empty($transactions))
        <div class="p-8 text-center text-gray-500">
            <p class="mb-2">No transactions data available</p>
            <p class="text-sm">Debug: $transactions is empty or not set</p>
        </div>
    @else
        <x-tables.generic-table 
            :data="$transactions" 
            :columns="$columns" 
            :formats="$formats" 
            :items-per-page="5" />
    @endif
</x-common.component-card>
@endsection
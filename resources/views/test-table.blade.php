
@extends('layouts.app')

@section('content')
    <h1>Testing Generic Table Component</h1>
    
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
            'Success' => 'bg-green-100 text-green-800 px-2 py-1 rounded',
            'Pending' => 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded',
            'Failed'  => 'bg-red-100 text-red-800 px-2 py-1 rounded',
        ],
    ];

    $transactions = [
        [
            'id' => 1,
            'name' => 'Bought PYPL',
            'image' => 'https://via.placeholder.com/32',
            'date' => 'Nov 23, 01:00 PM',
            'price' => '$2,567.88',
            'category' => 'Finance',
            'status' => 'Success',
        ],
        [
            'id' => 2,
            'name' => 'Bought AAPL',
            'image' => 'https://via.placeholder.com/32',
            'date' => 'Nov 23, 01:00 PM',
            'price' => '$2,567.88',
            'category' => 'Finance',
            'status' => 'Pending',
        ],
    ];
    @endphp

    <h2>Direct HTML Test (Without Component)</h2>
    <div id="direct-test">
        <table border="1">
            <thead>
                <tr>
                    @foreach($columns as $field => $label)
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $row)
                <tr>
                    @foreach($columns as $field => $label)
                        <td>
                            @if($field === 'image')
                                <img src="{{ $row[$field] }}" width="32" height="32">
                            @else
                                {{ $row[$field] }}
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>
    
    <h2>Component Test</h2>
    @include('components.tables.generic-table', [
        'data' => $transactions,
        'columns' => $columns,
        'formats' => $formats,
        'itemsPerPage' => 5
    ])

    <script>
        // Test Alpine.js is working
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js is initialized!');
        });
    </script>
@endsection
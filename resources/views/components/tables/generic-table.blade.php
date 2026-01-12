@props([
    'data' => [],
    'columns' => [],
    'formats' => [],
    'itemsPerPage' => 5,
])

@php
// Debug: Check what data we're receiving
// dd($data);

// Simple PHP pagination
$currentPage = request()->get('page', 1);
$search = request()->get('search', '');
$perPage = $itemsPerPage;

// Ensure $data is an array
$data = is_array($data) ? $data : [];

// Filter data if search is provided
if ($search && !empty($data)) {
    $filteredData = array_filter($data, function($row) use ($search) {
        foreach ($row as $value) {
            if (stripos((string)$value, $search) !== false) {
                return true;
            }
        }
        return false;
    });
    $filteredData = array_values($filteredData); // Re-index
} else {
    $filteredData = $data;
}

// Paginate
$totalItems = count($filteredData);
$totalPages = max(1, ceil($totalItems / $perPage));
$currentPage = min(max(1, $currentPage), $totalPages);
$start = ($currentPage - 1) * $perPage;
$paginatedData = array_slice($filteredData, $start, $perPage);
@endphp

<!-- Debug info -->
<div class="mb-4 p-4 bg-blue-50 rounded">
    <h4 class="font-bold mb-2">Table Debug Info:</h4>
    <p>Total users: {{ count($data) }}</p>
    <p>Filtered users: {{ count($filteredData) }}</p>
    <p>Showing page {{ $currentPage }} of {{ $totalPages }}</p>
</div>

<div>
    <!-- Search -->
    <div class="flex justify-end mb-4">
        <form method="GET" action="" class="relative">
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Search users..." 
                   class="h-[42px] w-72 rounded-lg border px-4 text-sm placeholder-gray-400 focus:ring focus:ring-blue-500" />
            @if($search)
                <a href="{{ request()->url() }}" 
                   class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                    ✕ Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-b">
            <thead>
                <tr class="bg-gray-50">
                    @foreach($columns as $field => $label)
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">{{ $label }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($paginatedData))
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="px-4 py-8 text-center text-gray-500">
                            <div class="text-lg font-medium">No data found</div>
                            @if($search)
                                <div class="text-sm mt-2">Try a different search term</div>
                            @elseif(empty($data))
                                <div class="text-sm mt-2">No users data available</div>
                            @endif
                        </td>
                    </tr>
                @else
                    @foreach($paginatedData as $row)
                    <tr class="border-b hover:bg-gray-50">
                        @foreach($columns as $field => $label)
                            <td class="px-4 py-3">
                                @if($field === 'image')
                                    <img src="{{ $row[$field] ?? '/images/user/user-01.png' }}" 
                                         alt="{{ $row['name'] ?? 'User' }}" 
                                         class="w-10 h-10 rounded-full">
                                @else
                                    @php
                                        $cellClass = isset($formats[$field][$row[$field] ?? '']) 
                                                   ? $formats[$field][$row[$field]] 
                                                   : '';
                                    @endphp
                                    <span class="{{ $cellClass }}">
                                        {{ $row[$field] ?? '' }}
                                    </span>
                                @endif
                            </td>
                        @endforeach
                        <td class="px-4 py-3">
                            <div class="flex space-x-2">
                                <a href="/users/{{ $row['id'] ?? '#' }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm px-2 py-1 border border-blue-300 rounded">View</a>
                                <a href="/users/{{ $row['id'] ?? '#' }}/edit" 
                                   class="text-green-600 hover:text-green-800 text-sm px-2 py-1 border border-green-300 rounded">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
    <div class="flex justify-between mt-6 items-center">
        <div class="text-sm text-gray-600">
            Showing {{ $start + 1 }} to {{ min($start + $perPage, $totalItems) }} of {{ $totalItems }} users
        </div>
        
        <div class="flex space-x-2">
            @if($currentPage > 1)
                <a href="?page={{ $currentPage - 1 }}&search={{ $search }}" 
                   class="px-3 py-1 border rounded hover:bg-gray-100 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </a>
            @endif
            
            @for($i = 1; $i <= $totalPages; $i++)
                @if($i == $currentPage)
                    <span class="px-3 py-1 bg-blue-500 text-white rounded">{{ $i }}</span>
                @elseif($i == 1 || $i == $totalPages || ($i >= $currentPage - 1 && $i <= $currentPage + 1))
                    <a href="?page={{ $i }}&search={{ $search }}" 
                       class="px-3 py-1 border rounded hover:bg-gray-100">{{ $i }}</a>
                @elseif($i == $currentPage - 2 || $i == $currentPage + 2)
                    <span class="px-3 py-1 text-gray-500">...</span>
                @endif
            @endfor
            
            @if($currentPage < $totalPages)
                <a href="?page={{ $currentPage + 1 }}&search={{ $search }}" 
                   class="px-3 py-1 border rounded hover:bg-gray-100 flex items-center">
                    Next
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @endif
        </div>
    </div>
    @endif
</div>
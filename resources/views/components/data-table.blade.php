{{-- resources/views/components/data-table.blade.php --}}
@props([
    'data' => [],
    'columns' => [],
    'actions' => [],
    'searchable' => true,
    'pagination' => true,
    'perPageOptions' => [5, 10, 25, 50],
    'defaultPerPage' => 5,
    'sortable' => true,
    'exportable' => false,
    'bulkActions' => [],
    'emptyMessage' => 'No records found.',
    'searchPlaceholder' => 'Search...',
    'tableId' => 'dataTable',
])

@php
    // Generate unique table ID if not provided
    $tableId = $tableId ?: 'table_' . Str::random(8);
    
    // Convert arrays to JSON for Alpine.js - FIXED VERSION
    $dataJson = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $columnsJson = json_encode($columns, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $actionsJson = json_encode($actions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $bulkActionsJson = json_encode($bulkActions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
@endphp

<div x-data="dataTable({
    data: {!! $dataJson !!},
    columns: {!! $columnsJson !!},
    actions: {!! $actionsJson !!},
    perPageOptions: @json($perPageOptions),
    defaultPerPage: {{ $defaultPerPage }},
    searchable: {{ $searchable ? 'true' : 'false' }},
    sortable: {{ $sortable ? 'true' : 'false' }},
    exportable: {{ $exportable ? 'true' : 'false' }},
    bulkActions: {!! $bulkActionsJson !!},
    tableId: '{{ $tableId }}'
})" x-init="init()" class="space-y-4">
    
    <!-- Table Controls -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <!-- Per Page Selector -->
        @if($pagination)
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
            <select x-model="perPage" @change="updatePagination()"
                class="h-8 rounded-lg border border-gray-300 bg-white px-2 py-1 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-800 dark:text-white/90">
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
        </div>
        @endif

        <!-- Search Box -->
        @if($searchable)
        <div class="relative w-full sm:w-64">
            <input type="text" x-model="searchQuery" @input.debounce.300ms="search()"
                placeholder="{{ $searchPlaceholder }}"
                class="h-9 w-full rounded-lg border border-gray-300 bg-white pl-9 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        @endif

        <!-- Export Button -->
        @if($exportable)
        <button @click="exportData()"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
            <i class="bi bi-download"></i>
            Export
        </button>
        @endif
    </div>

    <!-- Bulk Actions -->
    <template x-if="bulkActions.length > 0">
        <div x-show="selectedRows.length > 0" x-transition
            class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex items-center justify-between">
                <div class="text-sm text-blue-700 dark:text-blue-300">
                    <span x-text="selectedRows.length"></span> item(s) selected
                </div>
                <div class="flex gap-2">
                    <template x-for="action in bulkActions" :key="action.name">
                        <button @click="executeBulkAction(action)"
                            class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors"
                            :class="action.class || 'bg-blue-500 text-white hover:bg-blue-600'">
                            <i :class="action.icon" class="mr-1"></i>
                            <span x-text="action.label"></span>
                        </button>
                    </template>
                    <button @click="clearSelection()"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        Clear
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <!-- Checkbox Column for Bulk Actions -->
                        <template x-if="bulkActions.length > 0">
                            <th scope="col" class="w-12 px-6 py-3 text-left">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800">
                            </th>
                        </template>

                        <!-- Dynamic Columns -->
                        <template x-for="column in columns" :key="column.key">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <span x-text="column.label"></span>
                                    <template x-if="sortable && (column.sortable !== false)">
                                        <button @click="sortBy(column.key)"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <i class="bi bi-arrow-down-up text-xs"></i>
                                        </button>
                                    </template>
                                </div>
                            </th>
                        </template>

                        <!-- Actions Column -->
                        <template x-if="actions.length > 0">
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Actions
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Empty State -->
                    <template x-if="filteredData.length === 0">
                        <tr>
                            <td :colspan="columns.length + (bulkActions.length > 0 ? 1 : 0) + (actions.length > 0 ? 1 : 0)"
                                class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="bi bi-inbox text-3xl text-gray-300 dark:text-gray-600"></i>
                                    <p class="mt-2" x-text="searchQuery.length > 0 ? 'No matching records found.' : '{{ $emptyMessage }}'"></p>
                                    <template x-if="searchQuery.length > 0">
                                        <button @click="clearSearch()"
                                            class="mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            Clear search
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Data Rows -->
                    <template x-for="(row, index) in paginatedData" :key="row.id || index">
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <!-- Bulk Selection Checkbox -->
                            <template x-if="bulkActions.length > 0">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <input type="checkbox" x-model="selectedRows" :value="row.id"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800">
                                </td>
                            </template>

                            <!-- Dynamic Columns Data -->
                            <template x-for="column in columns" :key="column.key">
                                <td class="whitespace-nowrap px-6 py-4 text-sm"
                                    :class="column.class || 'text-gray-800 dark:text-white/90'">
                                    <!-- Date Format -->
                                    <template x-if="column.format === 'date'">
                                        <span x-text="formatDate(row[column.key])"></span>
                                    </template>
                                    
                                    <!-- Status Badge -->
                                    <template x-else-if="column.type === 'status'">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="getStatusClass(row[column.key])"
                                            x-text="row[column.key]"></span>
                                    </template>
                                    
                                    <!-- Custom Badge -->
                                    <template x-else-if="column.type === 'badge'">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="column.badgeClass"
                                            x-text="row[column.key]"></span>
                                    </template>
                                    
                                    <!-- Default Display -->
                                    <template x-else>
                                        <span x-text="row[column.key]"></span>
                                    </template>
                                </td>
                            </template>

                            <!-- Actions -->
                            <template x-if="actions.length > 0">
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <template x-for="action in actions" :key="action.name">
                                            <button @click="executeAction(action, row)"
                                                class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors"
                                                :class="action.class || 'text-blue-600 hover:text-blue-800 dark:text-blue-400'"
                                                :title="action.tooltip">
                                                <i :class="action.icon"></i>
                                                <template x-if="action.showLabel">
                                                    <span class="ml-1" x-text="action.label"></span>
                                                </template>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($pagination)
    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="startIndex + 1"></span> to 
            <span x-text="Math.min(startIndex + perPage, filteredData.length)"></span> of 
            <span x-text="filteredData.length"></span> entries
        </div>
        
        <div class="flex items-center gap-1">
            <button @click="previousPage()" :disabled="currentPage === 1"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                <i class="bi bi-chevron-left"></i>
            </button>
            
            <template x-for="page in visiblePages" :key="page">
                <button @click="goToPage(page)"
                    :class="page === currentPage 
                        ? 'bg-blue-500 text-white' 
                        : 'border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400'"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg">
                    <span x-text="page"></span>
                </button>
            </template>
            
            <button @click="nextPage()" :disabled="currentPage === totalPages"
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function dataTable(config) {
    return {
        // State
        data: config.data || [],
        columns: config.columns || [],
        actions: config.actions || [],
        bulkActions: config.bulkActions || [],
        perPage: config.defaultPerPage || 10,
        currentPage: 1,
        searchQuery: '',
        sortColumn: null,
        sortDirection: 'asc',
        selectedRows: [],
        selectAll: false,
        tableId: config.tableId || 'table',

        // Computed
        get filteredData() {
            let filtered = this.data;
            
            // Apply search
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(row => {
                    return this.columns.some(column => {
                        const value = row[column.key];
                        if (!value) return false;
                        return value.toString().toLowerCase().includes(query);
                    });
                });
            }
            
            // Apply sorting
            if (this.sortColumn) {
                filtered.sort((a, b) => {
                    let aVal = a[this.sortColumn];
                    let bVal = b[this.sortColumn];
                    
                    // Handle undefined/null values
                    if (aVal === undefined || aVal === null) aVal = '';
                    if (bVal === undefined || bVal === null) bVal = '';
                    
                    // Convert to string for comparison
                    aVal = String(aVal).toLowerCase();
                    bVal = String(bVal).toLowerCase();
                    
                    if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
                    if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
                    return 0;
                });
            }
            
            return filtered;
        },
        
        get totalPages() {
            return Math.ceil(this.filteredData.length / this.perPage);
        },
        
        get paginatedData() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredData.slice(start, end);
        },
        
        get startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },
        
        get visiblePages() {
            const pages = [];
            const maxVisible = 5;
            let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(this.totalPages, start + maxVisible - 1);
            
            if (end - start + 1 < maxVisible) {
                start = Math.max(1, end - maxVisible + 1);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        },

        // Methods
        init() {
            console.log('DataTable initialized with', this.data.length, 'items');
            // Initialize from localStorage if needed
            const savedPerPage = localStorage.getItem(`${this.tableId}_perPage`);
            if (savedPerPage) {
                this.perPage = parseInt(savedPerPage);
            }
        },
        
        search() {
            this.currentPage = 1;
            this.clearSelection();
        },
        
        clearSearch() {
            this.searchQuery = '';
            this.currentPage = 1;
        },
        
        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            this.currentPage = 1;
        },
        
        updatePagination() {
            localStorage.setItem(`${this.tableId}_perPage`, this.perPage);
            this.currentPage = 1;
            this.clearSelection();
        },
        
        goToPage(page) {
            this.currentPage = page;
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedRows = this.filteredData.map(row => row.id).filter(id => id !== undefined);
            } else {
                this.selectedRows = [];
            }
        },
        
        clearSelection() {
            this.selectedRows = [];
            this.selectAll = false;
        },
        
        executeAction(action, row) {
            if (action.url) {
                window.location.href = action.url.replace(':id', row.id);
            } else if (action.event) {
                window.dispatchEvent(new CustomEvent(action.event, { detail: row }));
            } else if (action.name === 'delete') {
                if (confirm('Are you sure you want to delete this equipment?')) {
                    console.log('Delete:', row);
                    // Add your delete logic here
                }
            }
        },
        
        executeBulkAction(action) {
            if (action.confirm) {
                if (confirm(action.confirm)) {
                    console.log('Executing bulk action:', action.name, this.selectedRows);
                    // Add your bulk action logic here
                }
            }
        },
        
        exportData() {
            const headers = this.columns.map(col => col.label);
            const rows = this.filteredData.map(row => 
                this.columns.map(col => row[col.key] || '')
            );
            
            const csv = [
                headers.join(','),
                ...rows.map(row => row.join(','))
            ].join('\n');
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `export-${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
        },
        
        // Utility Methods
        formatDate(dateString) {
            if (!dateString) return '';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        },
        
        getStatusClass(status) {
            const classes = {
                'active': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                'inactive': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                'maintenance': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                'completed': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            };
            return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        }
    };
}
</script>
@endpush
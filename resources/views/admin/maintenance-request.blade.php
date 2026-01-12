<!-- In your maintenance requests list page -->
<x-data-table 
    :data="$requests"
    :columns="[
        ['key' => 'id', 'label' => 'Ticket #', 'sortable' => true],
        ['key' => 'equipment_name', 'label' => 'Equipment', 'sortable' => true],
        [
            'key' => 'priority',
            'label' => 'Priority',
            'sortable' => true,
            'type' => 'badge',
            'badgeClass' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        ],
        [
            'key' => 'status',
            'label' => 'Status',
            'sortable' => true,
            'type' => 'status',
        ],
        ['key' => 'requested_by', 'label' => 'Requested By', 'sortable' => true],
        ['key' => 'created_at', 'label' => 'Request Date', 'sortable' => true, 'format' => 'date'],
    ]"
    :actions="[
        ['name' => 'view', 'icon' => 'bi bi-eye', 'url' => '/maintenance/:id', 'tooltip' => 'View'],
        ['name' => 'assign', 'icon' => 'bi bi-person-plus', 'url' => '/maintenance/:id/assign', 'tooltip' => 'Assign'],
    ]"
    searchable="true"
    pagination="true"
    exportable="true"
/>
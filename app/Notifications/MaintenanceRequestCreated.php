<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MaintenanceRequestCreated extends Notification
{
    use Queueable;

    public MaintenanceRequest $maintenanceRequest;

    public function __construct(MaintenanceRequest $maintenanceRequest)
    {
        $this->maintenanceRequest = $maintenanceRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        // Get item names from the request items
        $itemNames = $this->maintenanceRequest->items
            ->map(fn($item) => $item->item?->name ?? 'Unknown Item')
            ->filter()
            ->values()
            ->toArray();
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number'          => $this->maintenanceRequest->ticket_number,
            'item_names' => $itemNames,
            'items_count' => count($itemNames),
            'message'                => 'A new maintenance request has been created.',
            'url'                    => route('maintenance-requests.show', $this->maintenanceRequest->id),
            'type'                   => 'maintenance_request_created',
            'icon'                   => 'bi-plus-circle',
            'created_at'             => now()->toDateTimeString(),
        ];
    }
}

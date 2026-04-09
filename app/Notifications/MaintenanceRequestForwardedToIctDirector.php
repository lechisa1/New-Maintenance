<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestForwardedToIctDirector extends Notification
{
    use Queueable;

    public $maintenanceRequest;

    public function __construct(MaintenanceRequest $maintenanceRequest)
    {
        $this->maintenanceRequest = $maintenanceRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Get item names from the request items
        $itemNames = $this->maintenanceRequest->items
            ->map(function ($requestItem) {
                return $requestItem->item?->name ?? 'Unknown Item';
            })
            ->filter()
            ->values()
            ->toArray();

        // Get issue types
        $issueTypes = $this->maintenanceRequest->items
            ->map(function ($requestItem) {
                return $requestItem->issueType?->name ?? 'Unknown Issue';
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'item_names' => $itemNames, // Changed to array of item names
            'items_count' => count($itemNames), // Added count
            'issue_types' => $issueTypes, // Added issue types
            'requester_name' => $this->maintenanceRequest->user?->full_name ?? 'Unknown User',
            'requester_id' => $this->maintenanceRequest->user_id,
            'priority' => $this->maintenanceRequest->priority,
            'status' => $this->maintenanceRequest->status,
            'message' => 'A maintenance request has been forwarded to you.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'maintenance_request_forwarded',
            'icon' => 'bi-arrow-right-circle',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}

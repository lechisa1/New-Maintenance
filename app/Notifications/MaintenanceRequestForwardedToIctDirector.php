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
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'item_name' => $this->maintenanceRequest->item->name,
            'requester_name' => $this->maintenanceRequest->user->full_name,
            'message' => 'A maintenance request has been forwarded to you.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'maintenance_request_forwarded',
            'icon' => 'bi-arrow-right-circle',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
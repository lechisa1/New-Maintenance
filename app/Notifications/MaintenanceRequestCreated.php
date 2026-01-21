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
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number'          => $this->maintenanceRequest->ticket_number,
            'message'                => 'A new maintenance request has been created.',
            'url'                    => route('maintenance-requests.show', $this->maintenanceRequest->id),
            'type'                   => 'maintenance_request_created',
            'icon'                   => 'bi-plus-circle',
            'created_at'             => now()->toDateTimeString(),
        ];
    }
}
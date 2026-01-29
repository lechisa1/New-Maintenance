<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestConfirmedToIct extends Notification
{
    use Queueable;

    public $maintenanceRequest;

    public function __construct(MaintenanceRequest $maintenanceRequest)
    {
        $this->maintenanceRequest = $maintenanceRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Maintenance Request Closed - ' . $this->maintenanceRequest->ticket_number)
            ->line('A maintenance request has been successfully closed.')
            ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
            ->line('Status: Confirmed and Closed')
            ->line('Requester: ' . $this->maintenanceRequest->user?->name ?? 'N/A')
            ->line('Technician: ' . $this->maintenanceRequest->assignedTechnician?->name ?? 'N/A')
            ->line('Priority: ' . $this->maintenanceRequest->priority)
            ->line('The requester has confirmed that the issue is resolved.')
            ->action('View Request', url('/maintenance-requests/' . $this->maintenanceRequest->id))
            ->line('This request is now considered completed.');
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'message' => 'A maintenance request has been confirmed and closed.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'maintenance_request_confirmed_ict',
            'icon' => 'bi-archive',
            'priority' => $this->maintenanceRequest->priority,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
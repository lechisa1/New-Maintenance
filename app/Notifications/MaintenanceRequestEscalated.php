<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestEscalated extends Notification
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
            ->subject('Maintenance Request Requires Review - ' . $this->maintenanceRequest->ticket_number)
            ->line('A maintenance request requires your attention.')
            ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
            ->line('Priority: ' . $this->maintenanceRequest->priority)
            ->line('Requester: ' . $this->maintenanceRequest->user?->name ?? 'N/A')
            ->line('Status: Technician unable to fix')
            ->line('Description: ' . $this->maintenanceRequest->description)
            ->action('Review Request', url('/maintenance-requests/' . $this->maintenanceRequest->id))
            ->line('Please review this request and take appropriate action.');
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'message' => 'Maintenance request requires review. Technician was unable to fix.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'maintenance_request_escalated',
            'icon' => 'bi-arrow-up',
            'priority' => $this->maintenanceRequest->priority,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
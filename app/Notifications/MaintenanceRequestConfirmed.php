<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestConfirmed extends Notification
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
            ->subject('Maintenance Request Confirmed - ' . $this->maintenanceRequest->ticket_number)
            ->line('Your work has been confirmed by the requester.')
            ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
            ->line('Requester: ' . $this->maintenanceRequest->user?->name ?? 'N/A')
            ->line('Status: Confirmed ✅')
            ->line('The requester has reviewed and accepted your work.')
            ->line('Thank you for your excellent service!')
            ->action('View Request', url('/maintenance-requests/' . $this->maintenanceRequest->id))
            ->line('Your efforts are greatly appreciated.');
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'message' => 'Your work has been confirmed by the requester.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'maintenance_request_confirmed_technician',
            'icon' => 'bi-check-circle-fill',
            'priority' => $this->maintenanceRequest->priority,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
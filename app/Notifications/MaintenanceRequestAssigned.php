<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestAssigned extends Notification
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
            ->subject('Maintenance Request Assigned to You')
            ->line('A maintenance request has been assigned to you.')
            ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
            ->line('Priority: ' . $this->maintenanceRequest->priority)
            ->line('Description: ' . $this->maintenanceRequest->description)
            ->action('View Request', url('/maintenance-requests/' . $this->maintenanceRequest->id))
            ->line('Thank you for your attention to this matter.');
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'message' => 'A maintenance request has been assigned to you.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'maintenance_request_assigned',
            'icon' => 'bi-person-check',
            'priority' => $this->maintenanceRequest->priority,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceRequestCompleted extends Notification
{
    use Queueable;

    public $maintenanceRequest;
    public $status;

    public function __construct(MaintenanceRequest $maintenanceRequest, string $status)
    {
        $this->maintenanceRequest = $maintenanceRequest;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->status === 'completed') {
            return (new MailMessage)
                ->subject('Maintenance Request Completed - ' . $this->maintenanceRequest->ticket_number)
                ->line('Your maintenance request has been completed by the technician.')
                ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
                ->line('Status: Completed')
                ->line('Technician: ' . ($this->maintenanceRequest->assignedTechnician?->name ?? 'N/A'))
                ->line('Please review the work done and confirm if the issue is resolved.')
                ->action('Review & Confirm', url('/maintenance-requests/' . $this->maintenanceRequest->id))
                ->line('Thank you for using our maintenance system.');
        } else {
            return (new MailMessage)
                ->subject('Update: Maintenance Request Could Not Be Fixed - ' . $this->maintenanceRequest->ticket_number)
                ->line('We regret to inform you that the technician was unable to fix the reported issue.')
                ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
                ->line('Status: Not Fixed')
                ->line('The issue has been escalated to ICT directors for further review.')
                ->line('You will be contacted shortly regarding next steps.')
                ->action('View Details', url('/maintenance-requests/' . $this->maintenanceRequest->id))
                ->line('We apologize for any inconvenience.');
        }
    }

    public function toArray($notifiable)
    {
        if ($this->status === 'completed') {
            return [
                'maintenance_request_id' => $this->maintenanceRequest->id,
                'ticket_number' => $this->maintenanceRequest->ticket_number,
                'message' => 'Your maintenance request has been completed. Please review and confirm.',
                'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
                'type' => 'maintenance_request_completed',
                'icon' => 'bi-check-circle',
                'priority' => $this->maintenanceRequest->priority,
                'created_at' => now()->toDateTimeString(),
            ];
        } else {
            return [
                'maintenance_request_id' => $this->maintenanceRequest->id,
                'ticket_number' => $this->maintenanceRequest->ticket_number,
                'message' => 'Technician was unable to fix the issue. Escalated to ICT directors.',
                'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
                'type' => 'maintenance_request_not_fixed',
                'icon' => 'bi-exclamation-triangle',
                'priority' => $this->maintenanceRequest->priority,
                'created_at' => now()->toDateTimeString(),
            ];
        }
    }
}
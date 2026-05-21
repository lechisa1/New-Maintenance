<?php
// app/Notifications/MaintenanceRequestStatusChanged.php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MaintenanceRequestStatusChanged extends Notification
{
    use Queueable;

    protected $maintenanceRequest;
    protected $newStatus;
    protected $technician;

    public function __construct(MaintenanceRequest $maintenanceRequest, string $newStatus, User $technician)
    {
        $this->maintenanceRequest = $maintenanceRequest;
        $this->newStatus = $newStatus;
        $this->technician = $technician;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $statusText = ucfirst(str_replace('_', ' ', $this->newStatus));

        return (new MailMessage)
            ->subject("Maintenance Request Status Update - {$this->maintenanceRequest->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your maintenance request #{$this->maintenanceRequest->ticket_number} status has been updated to: **{$statusText}**")
            ->line("Updated by: {$this->technician->full_name}")
            ->when($this->maintenanceRequest->resolution_notes, function ($mail) {
                return $mail->line("Resolution Notes: {$this->maintenanceRequest->resolution_notes}");
            })
            ->action('View Request', url("/maintenance-requests/{$this->maintenanceRequest->id}"))
            ->line("Equipment: {$this->maintenanceRequest->items->pluck('item.name')->implode(', ')}")
            ->line("Priority: " . ucfirst($this->maintenanceRequest->priority))
            ->line('Thank you for using our maintenance system.');
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'status' => $this->newStatus,
            'status_text' => ucfirst(str_replace('_', ' ', $this->newStatus)),
            'updated_by' => $this->technician->id,
            'updated_by_name' => $this->technician->full_name,
            'resolution_notes' => $this->maintenanceRequest->resolution_notes,
            'message' => "Status changed to " . ucfirst(str_replace('_', ' ', $this->newStatus)) . " by {$this->technician->full_name}"
        ];
    }
}

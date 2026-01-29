<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use App\Models\WorkLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkLogRejected extends Notification
{
    use Queueable;

    public $maintenanceRequest;
    public $workLog;
    public $rejectionReason;

    public function __construct(MaintenanceRequest $maintenanceRequest, WorkLog $workLog, string $rejectionReason)
    {
        $this->maintenanceRequest = $maintenanceRequest;
        $this->workLog = $workLog;
        $this->rejectionReason = $rejectionReason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Work Log Rejected - ' . $this->maintenanceRequest->ticket_number)
            ->line('Your work log has been rejected by the requester.')
            ->line('Ticket: ' . $this->maintenanceRequest->ticket_number)
            ->line('Rejection Reason: ' . $this->rejectionReason)
            ->line('Requester: ' . $this->maintenanceRequest->user?->name ?? 'N/A')
            ->line('Date: ' . $this->workLog->log_date->format('M d, Y'))
            ->line('Time Spent: ' . $this->workLog->getTimeSpentFormatted())
            ->line('')
            ->line('Please review the feedback and resubmit your work log with necessary corrections.')
            ->action('View Request', url('/maintenance-requests/' . $this->maintenanceRequest->id))
            ->line('Thank you for your attention to this matter.');
    }

    public function toArray($notifiable)
    {
        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'work_log_id' => $this->workLog->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'message' => 'Your work log has been rejected. Reason: ' . substr($this->rejectionReason, 0, 100) . '...',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'work_log_rejected',
            'icon' => 'bi-x-circle',
            'priority' => $this->maintenanceRequest->priority,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
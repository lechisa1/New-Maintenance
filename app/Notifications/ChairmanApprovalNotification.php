<?php

namespace App\Notifications;

use App\Models\ApprovalRequest;
use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChairmanApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $maintenanceRequest;
    protected $approvalRequest;
    protected $action; // 'approved' or 'rejected'

    /**
     * Create a new notification instance.
     */
    public function __construct(MaintenanceRequest $maintenanceRequest, ApprovalRequest $approvalRequest, string $action = 'approved')
    {
        $this->maintenanceRequest = $maintenanceRequest;
        $this->approvalRequest = $approvalRequest;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->action === 'approved'
            ? "The chairman has approved the issue type change for the maintenance request."
            : "The chairman has rejected the issue type change for the maintenance request.";

        $itemName = $this->approvalRequest->item->name ?? 'N/A';
        $issueTypeName = $this->approvalRequest->issueType->name ?? 'N/A';

        return (new MailMessage)
            ->subject("Maintenance Request Issue Type {$this->action}")
            ->line($message)
            ->line("Ticket: {$this->maintenanceRequest->ticket_number}")
            ->line("Item: {$itemName}")
            ->line("Approved Issue Type: {$issueTypeName}")
            ->action('View Request', url("/maintenance-requests/{$this->maintenanceRequest->id}"))
            ->line('Please proceed with the maintenance work.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $issueTypeName = $this->approvalRequest->issueType->name ?? 'N/A';
        $itemName = $this->approvalRequest->item->name ?? 'N/A';

        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number' => $this->maintenanceRequest->ticket_number,
            'approval_request_id' => $this->approvalRequest->id,
            'issue_type_id' => $this->approvalRequest->issue_type_id,
            'issue_type_name' => $issueTypeName,
            'item_id' => $this->approvalRequest->item_id,
            'item_name' => $itemName,
            'action' => $this->action,
            'message' => $this->action === 'approved'
                ? "Chairman approved issue type change: {$issueTypeName}"
                : "Chairman rejected the issue type change request",
            'chairman_id' => $notifiable->id ?? null,
        ];
    }
}

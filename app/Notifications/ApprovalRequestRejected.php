<?php
// app/Notifications/ApprovalRequestRejected.php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use App\Models\ApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalRequestRejected extends Notification
{
    use Queueable;

    protected $maintenanceRequest;
    protected $approvalRequest;

    public function __construct(MaintenanceRequest $maintenanceRequest, ApprovalRequest $approvalRequest)
    {
        $this->maintenanceRequest = $maintenanceRequest;
        $this->approvalRequest = $approvalRequest;
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
            'item_name' => $this->approvalRequest->item->name,
            'issue_type' => $this->approvalRequest->issueType->name,
            'message' => 'Your approval request has been rejected.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'approval_request_rejected',
            'icon' => 'bi-x-circle',
        ];
    }
}

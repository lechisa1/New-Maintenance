<?php
// app/Notifications/ApprovalRequestSubmitted.php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use App\Models\ApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalRequestSubmitted extends Notification
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
            'technician_name' => $this->approvalRequest->technician->full_name,
            'item_name' => $this->approvalRequest->item->name,
            'issue_type' => $this->approvalRequest->issueType->name,
            'message' => 'A technician has submitted an approval request for issue type.',
            'url' => '/maintenance-requests/' . $this->maintenanceRequest->id,
            'type' => 'approval_request_submitted',
            'icon' => 'bi-question-circle',
        ];
    }
}

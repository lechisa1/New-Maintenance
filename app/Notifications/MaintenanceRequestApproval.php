<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MaintenanceRequestApproval extends Notification
{
    use Queueable;

    public MaintenanceRequest $maintenanceRequest;
    public string $action;

    public function __construct(MaintenanceRequest $maintenanceRequest, string $action = 'approval_needed')
    {
        $this->maintenanceRequest = $maintenanceRequest;
        $this->action = $action;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $message = '';
        $icon = '';
        $type = '';

        switch ($this->action) {
            case 'approval_needed':
                $message = 'Maintenance request needs your approval';
                $icon = 'bi-shield-check';
                $type = 'approval_needed';
                break;

            case 'approved':
                $message = 'Your maintenance request has been approved';
                $icon = 'bi-check-circle';
                $type = 'request_approved';
                break;

            case 'rejected':
                $message = 'Your maintenance request has been rejected';
                $icon = 'bi-x-circle';
                $type = 'request_rejected';
                break;

            default:
                $message = 'Maintenance request updated';
                $icon = 'bi-info-circle';
                $type = 'request_updated';
                break;
        }

        return [
            'maintenance_request_id' => $this->maintenanceRequest->id,
            'ticket_number'          => $this->maintenanceRequest->ticket_number,
            'message'                => $message,
            'url'                    => route('maintenance-requests.show', $this->maintenanceRequest->id),
            'type'                   => $type,
            'icon'                   => $icon,
            'created_at'             => now()->toDateTimeString(),
        ];
    }
}
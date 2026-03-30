<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestFile;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ApprovalRequestSubmitted;
use App\Notifications\ApprovalRequestForwarded;
use App\Notifications\ApprovalRequestRejected;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovalRequest;
use Illuminate\Support\Facades\Auth;


class ApprovalController extends Controller
{
    /**
     * Show pending approvals
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get requests waiting for approval where user is an approver
        $approvals = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_WAITING_APPROVAL)
            ->where(function ($query) use ($user) {
                // Check if user is division chairman of requester
                $query->whereHas('user.division', function ($q) use ($user) {
                    $q->where('chairman_id', $user->id);
                })
                    // Check if user is cluster chairman of requester
                    ->orWhereHas('user.cluster', function ($q) use ($user) {
                        $q->where('chairman_id', $user->id);
                    })
                    // Check if user is admin
                    ->orWhereHas('user', function ($q) use ($user) {
                        if ($user->hasAnyRole(['super-admin', 'admin'])) {
                            $q->whereRaw('1=1');
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })
            ->with(['user', 'item', 'issueType', 'user.division', 'user.cluster'])
            ->latest()
            ->paginate(10);

        return view('approvals.index', compact('approvals'));
    }

    /**
     * Approve a maintenance request
     */
    public function approve(Request $request, MaintenanceRequest $maintenanceRequest)
    {

        // 🔒 Status guard (NOT authorization)
        // if ($maintenanceRequest->status !== MaintenanceRequest::STATUS_WAITING_APPROVAL || $maintenanceRequest->is_approved || $maintenanceRequest->status !== MaintenanceRequest::STATUS_PENDING) {
        //     return back()->with('error', 'This request is not awaiting approval.');
        // }


        // ✅ Attachment REQUIRED
        $validated = $request->validate([
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx|max:5120',

            'approval_notes' => 'nullable|string|max:1000',
        ]);

        \DB::transaction(function () use ($maintenanceRequest, $validated) {

            // ✅ Update request status
            $maintenanceRequest->update([
                'status' => MaintenanceRequest::STATUS_APPROVED,
                'approved_at' => now(),
                'is_approved' => true,
                'approved_by' => auth()->id(),
                'forwarded_to_ict_director_at' => now(),
                'approval_notes' => $validated['approval_notes'] ?? null,
            ]);

            // 📎 Store attachments
            foreach ($validated['attachments'] as $file) {

                $filename = \Str::uuid() . '.' . $file->getClientOriginalExtension();

                $path = $file->storeAs(
                    "maintenance-requests/{$maintenanceRequest->id}/approval",
                    $filename,
                    'public'
                );

                MaintenanceRequestFile::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'filename'        => $filename, // ✅ CORRECT
                    'original_name'   => $file->getClientOriginalName(),
                    'mime_type'       => $file->getMimeType(),
                    'size'            => $file->getSize(),
                    'path'            => $path,
                    'type'            => 'approval',
                    'uploaded_by'     => auth()->id(),
                ]);
            }
        });

        return back()->with('success', 'Maintenance request approved successfully.');
    }

    /**
     * Reject a maintenance request
     */
    public function reject(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $user = auth()->user();

        // if (!$this->isAuthorizedToApprove($user, $maintenanceRequest)) {
        //     if($request->expectsJson()){
        //         return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        //     }
        //     return back()->with('error', 'You are not authorized to reject this request.');
        // }

        DB::beginTransaction();
        try {
            $maintenanceRequest->reject($user, $request->rejection_reason);
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return back()->with('success', 'Request rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rejection failed: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Failed to reject request']);
            }
            return back()->with('error', 'Failed to reject request.');
        }
    }


    /**
     * Check if user is authorized to approve/reject
     */
    private function isAuthorizedToApprove($user, $maintenanceRequest)
    {
        $requester = $maintenanceRequest->user;

        // Optional: Admins can always approve/reject
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        // 1️⃣ Check if requester belongs to a division
        if ($requester->division) {
            $division = $requester->division;

            // 2️⃣ If requester is NOT a division chairman (normal user)
            if (!$requester->isDivisionChairman ?? false) {
                if ($division->chairman_id == $user->id) {
                    return true; // Division chairman can approve/reject
                }
            }

            // 3️⃣ If requester IS a division chairman
            else {
                // Find the cluster of this division
                if ($division->cluster) {
                    $cluster = $division->cluster;
                    if ($cluster->chairman_id == $user->id) {
                        return true; // Cluster chairman can approve/reject
                    }
                }
            }
        }

        // Otherwise, not authorized
        return false;
    }
    /**
     * Technician submits approval request for issue type
     */
    public function requestApproval(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();

        // Check if user is assigned technician
        $isAssigned = $maintenanceRequest->assignedTechnicians()
            ->where('user_id', $user->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->exists();
        // dd($isAssigned);

        if (!$isAssigned) {
            return redirect()->back()->with('error', 'You are not assigned to this request.');
        }

        // Validate request
        $validated = $request->validate([
            'issue_type_id' => 'required|exists:issue_types,id',
            'item_id' => 'required|exists:items,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if the selected item belongs to this request
        $itemBelongsToRequest = $maintenanceRequest->items()
            ->where('item_id', $validated['item_id'])
            ->exists();


        if (!$itemBelongsToRequest) {
            return redirect()->back()->with('error', 'Selected item does not belong to this request.');
        }

        try {
            DB::beginTransaction();

            // Create approval request
            $approvalRequest = ApprovalRequest::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'technician_id' => $user->id,
                'issue_type_id' => $validated['issue_type_id'],
                'item_id' => $validated['item_id'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);
            // dd($approvalRequest->status, $approvalRequest->id, $approvalRequest->maintenance_request_id, $approvalRequest->technician_id, $approvalRequest->issue_type_id, $approvalRequest->item_id, $approvalRequest->notes);
            // Update maintenance request status
            $maintenanceRequest->update([
                'status' => 'pending_approval_review',
            ]);

            DB::commit();

            // Notify ICT directors

            $ictDirectors = $maintenanceRequest->getGeneralIctDirectors();

            foreach ($ictDirectors as $director) {
                $director->notify(new ApprovalRequestSubmitted($maintenanceRequest, $approvalRequest));
            }

            return redirect()->back()->with('success', 'Approval request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to submit approval request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit approval request.');
        }
    }

    /**
     * ICT Director forwards approval request to chairman
     */
    public function forwardToChairman(MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();

        // Check if user has permission
        if (!$user->can('maintenance_requests.assign')) {
            return redirect()->back()->with('error', 'You do not have permission to perform this action.');
        }

        $approvalRequest = $maintenanceRequest->approvalRequest;

        if (!$approvalRequest) {
            return redirect()->back()->with('error', 'No pending approval request found.');
        }

        try {
            DB::beginTransaction();

            // Get the item and its current issue type
            $requestItem = $maintenanceRequest->items()
                ->where('item_id', $approvalRequest->item_id)
                ->first();

            if (!$requestItem) {
                throw new \Exception('Request item not found');
            }

            // Get the next approver (chairman)
            $requester = $maintenanceRequest->user;
            $approver = null;

            // CASE 1: Normal user with division
            if ($requester->division && !$requester->isDivisionChairman()) {

                $approver = $requester->division->chairman;
            }
            // CASE 2: Division chairman OR no division (direct cluster user)
            elseif ($requester->cluster) {

                $approver = $requester->cluster->chairman;
            }

            if (!$approver) {
                return redirect()->back()->with('error', 'No approver found for this request.');
            }

            // Update approval request
            $approvalRequest->update([
                'status' => 'forwarded',
                'forwarded_at' => now(),
                'reviewed_by' => $user->id,
            ]);

            // Update maintenance request status
            $maintenanceRequest->update([
                'status' => 'waiting_approval',
            ]);

            DB::commit();

            // Notify chairman
            $approver->notify(new ApprovalRequestForwarded($maintenanceRequest, $approvalRequest));

            return redirect()->back()->with('success', 'Request forwarded to chairman for approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to forward approval request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to forward approval request.');
        }
    }

    /**
     * ICT Director rejects technician's approval request
     */
    public function rejectApprovalRequest(MaintenanceRequest $maintenanceRequest)
    {
        $user = Auth::user();

        // Check if user has permission
        if (!$user->can('maintenance_requests.assign')) {
            return redirect()->back()->with('error', 'You do not have permission to perform this action.');
        }

        $approvalRequest = $maintenanceRequest->approvalRequest;

        if (!$approvalRequest) {
            return redirect()->back()->with('error', 'No pending approval request found.');
        }

        try {
            DB::beginTransaction();

            // Update approval request
            $approvalRequest->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'reviewed_by' => $user->id,
            ]);

            // Update maintenance request status back to assigned
            $maintenanceRequest->update([
                'status' => 'assigned',
            ]);

            DB::commit();

            // Notify technician
            $approvalRequest->technician->notify(new ApprovalRequestRejected($maintenanceRequest, $approvalRequest));

            return redirect()->back()->with('success', 'Approval request rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to reject approval request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject approval request.');
        }
    }
}

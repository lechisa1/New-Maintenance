<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    if ($maintenanceRequest->status !== MaintenanceRequest::STATUS_WAITING_APPROVAL) {
        return back()->with('error', 'This request is not awaiting approval.');
    }


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
        
        // Check authorization
        if (!$this->isAuthorizedToApprove($user, $maintenanceRequest)) {
            return back()->with('error', 'You are not authorized to reject this request.');
        }
        
        DB::beginTransaction();
        
        try {
            $maintenanceRequest->reject($user, $request->rejection_reason);
            
            DB::commit();
            
            return back()->with('success', 'Request rejected successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject request.');
        }
    }
    
    /**
     * Check if user is authorized to approve/reject
     */
    private function isAuthorizedToApprove($user, $maintenanceRequest)
    {
        // Check if user is division chairman of requester
        if ($maintenanceRequest->user->division && 
            $maintenanceRequest->user->division->chairman_id == $user->id) {
            return true;
        }
        
        // Check if user is cluster chairman of requester
        if ($maintenanceRequest->user->cluster && 
            $maintenanceRequest->user->cluster->chairman_id == $user->id) {
            return true;
        }
        
        // Check if user is admin
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }
        
        return false;
    }
}
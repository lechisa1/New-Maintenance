<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
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
    public function approve(MaintenanceRequest $maintenanceRequest)
    {
        $user = auth()->user();
        
        // Check authorization
        if (!$this->isAuthorizedToApprove($user, $maintenanceRequest)) {
            return back()->with('error', 'You are not authorized to approve this request.');
        }
        
        DB::beginTransaction();
        
        try {
            $maintenanceRequest->approve($user);
            
            DB::commit();
            
            return back()->with('success', 'Request approved successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve request.');
        }
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
<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceRequestApprovalController extends Controller
{
    public function approve(MaintenanceRequest $maintenanceRequest)
    {
        DB::beginTransaction();
        
        try {
            // Check if user is authorized to approve
            $user = auth()->user();
            $isAuthorized = false;
            
            // Check if user is division chairman of the requester
            if ($maintenanceRequest->user->division && 
                $maintenanceRequest->user->division->chairman_id == $user->id) {
                $isAuthorized = true;
            }
            
            // Check if user is cluster chairman of the requester
            if (!$isAuthorized && $maintenanceRequest->user->cluster && 
                $maintenanceRequest->user->cluster->chairman_id == $user->id) {
                $isAuthorized = true;
            }
            
            // Check if user has admin role
            if (!$isAuthorized && $user->hasAnyRole(['super-admin', 'admin'])) {
                $isAuthorized = true;
            }
            
            if (!$isAuthorized) {
                return back()->with('error', 'You are not authorized to approve this request.');
            }
            
            // Update request
            $maintenanceRequest->update([
                'status' => MaintenanceRequest::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => $user->id,
                'forwarded_to_ict_director_at' => now(),
            ]);
            
            // Notify ICT Directors
            $ictDirectors = $maintenanceRequest->getGeneralIctDirectors();
            
            foreach ($ictDirectors as $director) {
                $director->notify(new \App\Notifications\MaintenanceRequestForwardedToIctDirector(
                    $maintenanceRequest
                ));
            }
            
            // Notify requester
            $maintenanceRequest->user->notify(new \App\Notifications\MaintenanceRequestApproval(
                $maintenanceRequest,
                'approved'
            ));
            
            DB::commit();
            
            return back()->with('success', 'Request approved and forwarded to ICT Director.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }
    
    public function reject(MaintenanceRequest $maintenanceRequest, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Authorization check (same as approve)
            $user = auth()->user();
            $isAuthorized = false;
            
            if ($maintenanceRequest->user->division && 
                $maintenanceRequest->user->division->chairman_id == $user->id) {
                $isAuthorized = true;
            }
            
            if (!$isAuthorized && $maintenanceRequest->user->cluster && 
                $maintenanceRequest->user->cluster->chairman_id == $user->id) {
                $isAuthorized = true;
            }
            
            if (!$isAuthorized && $user->hasAnyRole(['super-admin', 'admin'])) {
                $isAuthorized = true;
            }
            
            if (!$isAuthorized) {
                return back()->with('error', 'You are not authorized to reject this request.');
            }
            
            // Update request
            $maintenanceRequest->update([
                'status' => MaintenanceRequest::STATUS_REJECTED,
                'approved_by' => $user->id,
                'rejection_reason' => $request->rejection_reason,
            ]);
            
            // Notify requester
            $maintenanceRequest->user->notify(new \App\Notifications\MaintenanceRequestApproval(
                $maintenanceRequest,
                'rejected'
            ));
            
            DB::commit();
            
            return back()->with('success', 'Request rejected successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject request: ' . $e->getMessage());
        }
    }
}
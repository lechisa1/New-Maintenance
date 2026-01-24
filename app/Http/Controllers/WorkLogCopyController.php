<?php

namespace App\Http\Controllers;

use App\Models\WorkLog;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkLogController extends Controller
{
    /**
     * Store a newly created work log (via modal)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $validated = $request->validate([
            'request_id' => 'required|exists:maintenance_requests,id',
            'new_status' => 'required|in:in_progress,completed,not_fixed',
            'work_done' => 'required|string|min:10|max:2000',
            'materials_used' => 'nullable|string|max:1000',
            'time_spent_minutes' => 'required|integer|min:1|max:480',
            'completion_notes' => 'nullable|string|max:1000',
            'log_date' => 'required|date',
        ]);
        
        // Get the maintenance request
        $maintenanceRequest = MaintenanceRequest::findOrFail($validated['request_id']);
        
        // Check if user is assigned to this request
        if ($maintenanceRequest->assigned_to !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this request.'
            ], 403);
        }
        
        // Check if current status allows adding work log
        if (!in_array($maintenanceRequest->status, ['assigned', 'in_progress', 'approved', 'not_fixed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add work log to a request with status: ' . $maintenanceRequest->status
            ], 400);
        }
        
        try {
            \DB::beginTransaction();
            
            // Create work log
            $workLog = WorkLog::create([
                'request_id' => $validated['request_id'],
                'technician_id' => $user->id,
                'work_done' => $validated['work_done'],
                'materials_used' => $validated['materials_used'],
                'time_spent_minutes' => $validated['time_spent_minutes'],
                'completion_notes' => $validated['completion_notes'],
                'log_date' => $validated['log_date'],
            ]);
            
            // Update request status based on selection
            $statusUpdateData = [
                'status' => $validated['new_status'],
            ];
            
            // Set started_at if this is the first work log
            if (!$maintenanceRequest->started_at && $validated['new_status'] !== 'not_fixed') {
                $statusUpdateData['started_at'] = now();
            }
            
            // Set completed_at if status is completed
            if ($validated['new_status'] === 'completed') {
                $statusUpdateData['completed_at'] = now();
            }
            
            // Set rejected_at if status is not_fixed
            if ($validated['new_status'] === 'not_fixed') {
                $statusUpdateData['completed_at'] = now(); // Still mark as completed (but not fixed)
            }
            
            $maintenanceRequest->update($statusUpdateData);
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Work log created and status updated successfully.',
                'workLog' => [
                    'id' => $workLog->id,
                    'work_done' => $workLog->work_done,
                    'materials_used' => $workLog->materials_used,
                    'time_spent_formatted' => $workLog->getTimeSpentFormatted(),
                    'log_date_formatted' => $workLog->getLogDateFormatted(),
                    'log_time_formatted' => $workLog->getLogTimeFormatted(),
                    'completion_notes' => $workLog->completion_notes,
                    'technician_name' => $workLog->technician?->full_name ?? 'Unknown Technician',
                ],
                'new_status' => $validated['new_status'],
                'new_status_text' => $maintenanceRequest->getStatusText(),
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating work log: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_id' => $validated['request_id'],
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create work log: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete work log via AJAX
     */
    public function destroy(WorkLog $workLog)
    {
        $user = Auth::user();
        
        // Check permission
        if ($workLog->technician_id !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }
        
        try {
            \DB::beginTransaction();
            
            $workLog->delete();
            
            // If this was the last work log, update request status back to assigned
            $maintenanceRequest = $workLog->maintenanceRequest;
            if ($maintenanceRequest->workLogs()->count() === 0 && 
                in_array($maintenanceRequest->status, ['in_progress', 'completed', 'not_fixed'])) {
                $maintenanceRequest->update([
                    'status' => 'assigned',
                    'started_at' => null,
                    'completed_at' => null,
                ]);
            }
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Work log deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting work log: ' . $e->getMessage(), [
                'work_log_id' => $workLog->id,
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete work log.'
            ], 500);
        }
    }
    
    /**
     * Get work logs for a specific request (for AJAX)
     */
    public function getForRequest(MaintenanceRequest $maintenanceRequest)
    {
        $workLogs = $maintenanceRequest->workLogs()
            ->with('technician')
            ->orderBy('log_date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'work_done' => $log->work_done,
                    'materials_used' => $log->materials_used,
                    'time_spent_formatted' => $log->getTimeSpentFormatted(),
                    'log_date_formatted' => $log->getLogDateFormatted(),
                    'log_time_formatted' => $log->getLogTimeFormatted(),
                    'completion_notes' => $log->completion_notes,
                    'technician_name' => $log->technician?->full_name ?? 'Unknown Technician',
                    'can_delete' => auth()->id() == $log->technician_id || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin(),
                ];
            });
            
        return response()->json([
            'success' => true,
            'workLogs' => $workLogs,
            'total_time' => $maintenanceRequest->getTotalWorkTimeFormatted(),
            'current_status' => $maintenanceRequest->status,
            'current_status_text' => $maintenanceRequest->getStatusText(),
        ]);
    }
}
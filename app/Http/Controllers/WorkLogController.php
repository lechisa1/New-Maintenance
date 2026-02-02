<?php

namespace App\Http\Controllers;

use App\Models\WorkLog;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkLogController extends Controller
{
    /**
     * Display work logs for a specific request
     */
    public function index(Request $request, $requestId = null)
    {
        $user = Auth::user();
        $query = WorkLog::query()->with(['maintenanceRequest', 'technician']);
        
        if ($requestId) {
            $query->where('request_id', $requestId);
        }
        
        // Filter by technician if not admin
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            $query->where('technician_id', $user->id);
        }
        
        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('log_date', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        $workLogs = $query->latest('log_date')->paginate(20);
        
        return view('work-logs.index', compact('workLogs'));
    }

    /**
     * Show the form for creating a new work log
     */
    public function create($requestId = null)
    {
        $user = Auth::user();
        $maintenanceRequest = null;
        
        if ($requestId) {
            $maintenanceRequest = MaintenanceRequest::findOrFail($requestId);
            
            // Check if user is assigned to this request
            if ($maintenanceRequest->assigned_to !== $user->id ) {
                abort(403, 'You are not assigned to this request.');
            }
        }
        
        // Get requests assigned to the technician
        $assignedRequests = MaintenanceRequest::where('assigned_to', $user->id)
            ->whereIn('status', [
                MaintenanceRequest::STATUS_ASSIGNED,
                MaintenanceRequest::STATUS_IN_PROGRESS,
                MaintenanceRequest::STATUS_APPROVED
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('work-logs.create', compact('maintenanceRequest', 'assignedRequests'));
    }

    /**
     * Store a newly created work log
     */
/**
 * Store a newly created work log (via modal)
 */
// public function store(Request $request)
// {
//     $user = Auth::user();
//     // dd($request->all());
    
//     // Validate the request - ADD 'new_status' field
//     $validated = $request->validate([
//         'request_id' => 'required|exists:maintenance_requests,id',
//         'new_status' => 'required|in:in_progress,completed,not_fixed', // ADD THIS
//         'work_done' => 'required|string|min:10|max:2000',
//         'materials_used' => 'nullable|string|max:1000',
//         'time_spent_minutes' => 'required|integer|min:1|max:480',
//         'completion_notes' => 'nullable|string|max:1000',
//         'log_date' => 'required|date',
//     ]);
    
//     // Get the maintenance request
//     $maintenanceRequest = MaintenanceRequest::findOrFail($validated['request_id']);
    
//     // Check if user is assigned to this request
//     // dd($maintenanceRequest->assigned_to === $user->id);
//     if ($maintenanceRequest->assigned_to !== $user->id) {
//         return response()->json([ // RETURN JSON RESPONSE
//             'success' => false,
//             'message' => 'You are not assigned to this request.'
//         ], 403);
//     }
    
//     try {
//         \DB::beginTransaction();
        
//         // Create work log
//         $workLog = WorkLog::create([
//             'request_id' => $validated['request_id'],
//             'technician_id' => $user->id,
//             'work_done' => $validated['work_done'],
//             'materials_used' => $validated['materials_used'],
//             'time_spent_minutes' => $validated['time_spent_minutes'],
//             'completion_notes' => $validated['completion_notes'],
//             'log_date' => $validated['log_date'],
//         ]);
//         // dd($workLog->toArray());
//         // Update request status based on selection
//         $statusUpdateData = [
//             'status' => $validated['new_status'],
//         ];
//         // dd($statusUpdateData);
//         // Set started_at if this is the first work log
//         if (!$maintenanceRequest->started_at && $validated['new_status'] !== 'not_fixed') {
//             $statusUpdateData['started_at'] = now();
//         }
        
//         // Set completed_at if status is completed
//         // dd($validated['new_status'] === 'completed');
//         if ($validated['new_status'] === 'completed') {
//             $statusUpdateData['completed_at'] = now();
//         }
        
//         // Set completed_at if status is not_fixed
//         if ($validated['new_status'] === 'not_fixed') {
//             $statusUpdateData['completed_at'] = now(); // Still mark as completed (but not fixed)
//         }
        
//         $maintenanceRequest->update($statusUpdateData);
        
//         \DB::commit();
        
//         return response()->json([
//             'success' => true,
//             'message' => 'Work log created and status updated successfully.',

//         ]);
        
//     } catch (\Exception $e) {
//         \DB::rollBack();
//         \Log::error('Error creating work log: ' . $e->getMessage(), [
//             'user_id' => $user->id,
//             'request_id' => $validated['request_id'],
//             'error' => $e->getTraceAsString()
//         ]);
        
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to create work log: ' . $e->getMessage()
//         ], 500);
//     }
// }
public function store(Request $request)
{
    $user = Auth::user();
    
    $validated = $request->validate([
        'request_id' => 'required|exists:maintenance_requests,id',
        'new_status' => 'required|in:in_progress,completed,not_fixed',
        'work_done' => 'required|string|min:10|max:2000',
        'materials_used' => 'nullable|string|max:1000',
        'time_spent_minutes' => 'required|integer|min:1|max:480',
        'completion_notes' => 'nullable|string|max:1000',
        'log_date' => 'required|date',
    ]);
    
    $maintenanceRequest = MaintenanceRequest::findOrFail($validated['request_id']);
    
    if ($maintenanceRequest->assigned_to !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'You are not assigned to this request.'
        ], 403);
    }
    
    try {
        \DB::beginTransaction();
        
        // Create work log with accepted status by default
        $workLog = WorkLog::create([
            'request_id' => $validated['request_id'],
            'technician_id' => $user->id,
            'work_done' => $validated['work_done'],
            'materials_used' => $validated['materials_used'],
            'time_spent_minutes' => $validated['time_spent_minutes'],
            'completion_notes' => $validated['completion_notes'],
            'log_date' => $validated['log_date'],
            'status' => WorkLog::STATUS_PENDING, 
        ]);
        
        // Store old status for comparison
        $oldStatus = $maintenanceRequest->status;
        
        // Update request status
        $statusUpdateData = [
            'status' => $validated['new_status'],
        ];
        
        // Set started_at if this is the first work log
        if (!$maintenanceRequest->started_at && $validated['new_status'] !== 'not_fixed') {
            $statusUpdateData['started_at'] = now();
        }
        
        // Set completed_at if status is completed or not_fixed
        if (in_array($validated['new_status'], ['completed', 'not_fixed'])) {
            $statusUpdateData['completed_at'] = now();
        }
        
        // Update the request
        $maintenanceRequest->update($statusUpdateData);
        
        // Send notifications based on status
        if ($validated['new_status'] === 'completed') {
            // Notify requester that work is completed (waiting confirmation)
            if ($maintenanceRequest->user) {
                $maintenanceRequest->user->notify(
                    new \App\Notifications\MaintenanceRequestCompleted(
                        $maintenanceRequest, 
                        'completed'
                    )
                );
            }
            
            // Change status to waiting_confirmation
            // $maintenanceRequest->update(['status' => MaintenanceRequest::STATUS_WAITING_CONFIRMATION]);
            
        } elseif ($validated['new_status'] === 'not_fixed') {
            // Notify requester that work could not be completed
            if ($maintenanceRequest->user) {
                $maintenanceRequest->user->notify(
                    new \App\Notifications\MaintenanceRequestCompleted(
                        $maintenanceRequest, 
                        'not_fixed'
                    )
                );
            }
            
            // Notify ICT directors for review
            $ictDirectors = $maintenanceRequest->getGeneralIctDirectors();
            foreach ($ictDirectors as $director) {
                $director->notify(
                    new \App\Notifications\MaintenanceRequestEscalated($maintenanceRequest)
                );
            }
        }
        
        \DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => $this->getSuccessMessage($validated['new_status']),
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

private function getSuccessMessage(string $status): string
{
    return match($status) {
        'completed' => 'Work log saved. Requester has been notified to confirm completion.',
        'not_fixed' => 'Work log saved. Issue marked as not fixed. ICT directors have been notified.',
        default => 'Work log saved successfully.',
    };
}

    /**
     * Display the specified work log
     */
    public function show(WorkLog $workLog)
    {
        $user = Auth::user();
        
        // Check permission
        if ($workLog->technician_id !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('work-logs.show', compact('workLog'));
    }

    /**
     * Show the form for editing the specified work log
     */
    public function edit(WorkLog $workLog)
    {
        $user = Auth::user();
        
        // Check permission
        if ($workLog->technician_id !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        $assignedRequests = MaintenanceRequest::where('assigned_to', $user->id)
            ->whereIn('status', [
                MaintenanceRequest::STATUS_ASSIGNED,
                MaintenanceRequest::STATUS_IN_PROGRESS,
                MaintenanceRequest::STATUS_APPROVED
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('work-logs.edit', compact('workLog', 'assignedRequests'));
    }

    /**
     * Update the specified work log
     */
    public function update(Request $request, WorkLog $workLog)
    {
        $user = Auth::user();
        
        // Check permission
        if ($workLog->technician_id !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        $validated = $request->validate([
            'work_done' => 'required|string|min:10|max:2000',
            'materials_used' => 'nullable|string|max:1000',
            'time_spent_minutes' => 'required|integer|min:1|max:480',
            'completion_notes' => 'nullable|string|max:1000',
            'log_date' => 'required|date',
        ]);
        
        $workLog->update($validated);
        
        return redirect()->route('work-logs.show', $workLog)
            ->with('success', 'Work log updated successfully.');
    }

    /**
     * Remove the specified work log
     */
    public function destroy(WorkLog $workLog)
    {
        $user = Auth::user();
        
        // Check permission
        if ($workLog->technician_id !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        $workLog->delete();
        
        return redirect()->route('work-logs.index')
            ->with('success', 'Work log deleted successfully.');
    }
    
    /**
     * Get work logs for a specific maintenance request
     */
/**
 * Get work logs for a specific maintenance request
 */
public function forRequest($requestId)
{
    $workLogs = WorkLog::where('request_id', $requestId)
        ->with(['technician', 'rejectedBy'])
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
                'is_rejected' => $log->isRejected(),
                'rejection_reason' => $log->rejection_reason,
                'rejection_notes' => $log->rejection_notes,
                'rejected_at_formatted' => $log->rejected_at ? $log->rejected_at->format('M d, Y h:i A') : null,
                'rejected_by_name' => $log->rejectedBy?->full_name,
                'can_reject' => auth()->user() && 
                               $log->maintenanceRequest->user_id == auth()->id() && 
                               !$log->isRejected(),
                'can_accept' => auth()->user() && 
                                $log->maintenanceRequest->user_id == auth()->id() && 
                                $log->isRejected(),
                'can_delete' => auth()->id() == $log->technician_id || 
                                auth()->user()->isSuperAdmin() || 
                                auth()->user()->isAdmin(),
            ];
        });
        
    return response()->json($workLogs);
}
    
    /**
     * Get technician's work log summary
     */
    public function technicianSummary(Request $request)
    {
        $user = Auth::user();
        
        $query = WorkLog::where('technician_id', $user->id);
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('log_date', [
                $request->start_date,
                $request->end_date
            ]);
        } else {
            $query->whereMonth('log_date', now()->month);
        }
        
        $summary = [
            'total_logs' => $query->count(),
            'total_time_minutes' => $query->sum('time_spent_minutes'),
            'avg_time_per_log' => $query->avg('time_spent_minutes'),
            'recent_logs' => $query->orderBy('log_date', 'desc')->take(5)->get(),
        ];
        
        return response()->json($summary);
    }
    // Add these methods to WorkLogController:

/**
 * Reject a specific work log
 */
public function rejectWorkLog(Request $request, WorkLog $workLog)
{
    $user = Auth::user();
    $maintenanceRequest = $workLog->maintenanceRequest;
    
    // Authorization - only requester can reject work logs
    if ($maintenanceRequest->user_id !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'You are not authorized to reject this work log.'
        ], 403);
    }
    
    // Check if work log is already rejected
    if ($workLog->isRejected()) {
        return response()->json([
            'success' => false,
            'message' => 'This work log is already rejected.'
        ], 400);
    }
    
    // Validate rejection reason
    $validated = $request->validate([
        'rejection_reason' => 'required|string|min:10|max:1000',
        'rejection_notes' => 'nullable|string|max:1000',
    ]);
    
    try {
        \DB::beginTransaction();
        
        // Reject the work log
        $workLog->update([
            'status' => WorkLog::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by' => $user->id,
            'rejection_reason' => $validated['rejection_reason'],
            'rejection_notes' => $validated['rejection_notes'] ?? null,
        ]);
        
        // If this was the latest work log and request is waiting confirmation,
        // reset request status back to in_progress
        if ($maintenanceRequest->status === MaintenanceRequest::STATUS_WAITING_CONFIRMATION) {
            $latestWorkLog = $maintenanceRequest->workLogs()
                ->whereNull('rejected_at')
                ->latest('log_date')
                ->first();
            
            // If no accepted work logs remain, set status to in_progress
            if (!$latestWorkLog) {
                $maintenanceRequest->update([
                    'status' => MaintenanceRequest::STATUS_IN_PROGRESS,
                ]);
            }
        }
        
        $maintenanceRequest->notifyWorkLogRejected($workLog, $validated['rejection_reason']);
        
        \DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Work log rejected successfully.',
            'work_log_id' => $workLog->id,
        ]);
        
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Error rejecting work log: ' . $e->getMessage(), [
            'work_log_id' => $workLog->id,
            'user_id' => $user->id,
            'error' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to reject work log: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Accept a previously rejected work log
 */

public function acceptWorkLog(WorkLog $workLog)
{
    $user = Auth::user();
    $maintenanceRequest = $workLog->maintenanceRequest;

    // Authorization - only requester can accept work logs
    if ($maintenanceRequest->user_id !== $user->id) {
        return response()->json([
            'success' => false,
            'message' => 'You are not authorized to accept this work log.'
        ], 403);
    }

    try {
        \DB::beginTransaction();

        // Accept the work log
        $workLog->update([
            'status' => WorkLog::STATUS_ACCEPTED,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
            'rejection_notes' => null,
        ]);

        // Update maintenance request status to 'confirmed'
        $maintenanceRequest->update([
            'status' => MaintenanceRequest::STATUS_CONFIRMED,
            'completed_at' => now(), // Ensure completed_at is set
        ]);

        // Send notifications
        $this->sendConfirmationNotifications($maintenanceRequest);

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Work log accepted successfully. Notifications have been sent.',
            'work_log_id' => $workLog->id,
        ]);

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Error accepting work log: ' . $e->getMessage(), [
            'work_log_id' => $workLog->id,
            'user_id' => $user->id,
            'error' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to accept work log: ' . $e->getMessage()
        ], 500);
    }
}

private function sendConfirmationNotifications(MaintenanceRequest $maintenanceRequest)
{
    try {
        // 1. Notify the technician
        if ($maintenanceRequest->assignedTechnician) {
            $maintenanceRequest->assignedTechnician->notify(
                new \App\Notifications\MaintenanceRequestConfirmed($maintenanceRequest)
            );
            \Log::info('Confirmation notification sent to technician', [
                'technician_id' => $maintenanceRequest->assignedTechnician->id,
                'request_id' => $maintenanceRequest->id
            ]);
        }

        // 2. Notify ICT directors
        $ictDirectors = $maintenanceRequest->getGeneralIctDirectors();
        foreach ($ictDirectors as $director) {
            // Skip if director is also the technician
            if ($maintenanceRequest->assignedTechnician && 
                $director->id === $maintenanceRequest->assignedTechnician->id) {
                continue;
            }
            
            $director->notify(
                new \App\Notifications\MaintenanceRequestConfirmedToIct($maintenanceRequest)
            );
        }
        
        \Log::info('Confirmation notifications sent to ICT directors', [
            'request_id' => $maintenanceRequest->id,
            'director_count' => count($ictDirectors)
        ]);

    } catch (\Exception $e) {
        \Log::error('Error sending confirmation notifications: ' . $e->getMessage(), [
            'request_id' => $maintenanceRequest->id,
            'error' => $e->getTraceAsString()
        ]);
        // Don't throw - we don't want to rollback the main transaction
    }
}

}
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
            if ($maintenanceRequest->assigned_to !== $user->id && !$user->isSuperAdmin() && !$user->isAdmin()) {
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
public function store(Request $request)
{
    $user = Auth::user();
    // dd($request->all());
    
    // Validate the request - ADD 'new_status' field
    $validated = $request->validate([
        'request_id' => 'required|exists:maintenance_requests,id',
        'new_status' => 'required|in:in_progress,completed,not_fixed', // ADD THIS
        'work_done' => 'required|string|min:10|max:2000',
        'materials_used' => 'nullable|string|max:1000',
        'time_spent_minutes' => 'required|integer|min:1|max:480',
        'completion_notes' => 'nullable|string|max:1000',
        'log_date' => 'required|date',
    ]);
    
    // Get the maintenance request
    $maintenanceRequest = MaintenanceRequest::findOrFail($validated['request_id']);
    
    // Check if user is assigned to this request
    // dd($maintenanceRequest->assigned_to === $user->id);
    if ($maintenanceRequest->assigned_to !== $user->id) {
        return response()->json([ // RETURN JSON RESPONSE
            'success' => false,
            'message' => 'You are not assigned to this request.'
        ], 403);
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
        // dd($workLog->toArray());
        // Update request status based on selection
        $statusUpdateData = [
            'status' => $validated['new_status'],
        ];
        // dd($statusUpdateData);
        // Set started_at if this is the first work log
        if (!$maintenanceRequest->started_at && $validated['new_status'] !== 'not_fixed') {
            $statusUpdateData['started_at'] = now();
        }
        
        // Set completed_at if status is completed
        // dd($validated['new_status'] === 'completed');
        if ($validated['new_status'] === 'completed') {
            $statusUpdateData['completed_at'] = now();
        }
        
        // Set completed_at if status is not_fixed
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
    // Debug: Check if request exists
    $maintenanceRequest = MaintenanceRequest::find($requestId);
    if (!$maintenanceRequest) {
        return response()->json(['error' => 'Request not found'], 404);
    }
    
    // Get work logs WITH technician relationship loaded
    $workLogs = WorkLog::where('request_id', $requestId)
        ->with(['technician' => function($query) {
            // Debug: Log what we're querying
            \Log::info('Loading technician for work logs', [
                'select_fields' => ['id', 'full_name']
            ]);
            $query->select('id', 'full_name');
        }])
        ->orderBy('log_date', 'desc')
        ->get();
    
    // Debug: Check what we got
    \Log::info('Work logs retrieved', [
        'count' => $workLogs->count(),
        'first_log_technician_id' => $workLogs->first()?->technician_id,
        'first_log_technician_loaded' => $workLogs->first()?->relationLoaded('technician'),
        'first_log_technician' => $workLogs->first()?->technician ? [
            'id' => $workLogs->first()->technician->id,
            'full_name' => $workLogs->first()->technician->full_name,
            'attributes' => $workLogs->first()->technician->getAttributes()
        ] : null
    ]);
    
    // Format the response
    $formattedLogs = $workLogs->map(function ($log) {
        // Debug each log
        \Log::info('Processing work log', [
            'log_id' => $log->id,
            'technician_id' => $log->technician_id,
            'technician_exists' => !!$log->technician,
            'technician_full_name' => $log->technician?->full_name,
            'technician_attributes' => $log->technician?->getAttributes() ?? 'No technician'
        ]);
        
        // Get technician name with fallback
        $technicianName = 'Unknown Technician';
        if ($log->technician) {
            $technicianName = $log->technician->full_name ?? 'Technician #' . $log->technician_id;
        }
        
        return [
            'id' => $log->id,
            'work_done' => $log->work_done,
            'materials_used' => $log->materials_used,
            'time_spent_formatted' => $log->getTimeSpentFormatted(),
            'log_date_formatted' => $log->getLogDateFormatted(),
            'log_time_formatted' => $log->getLogTimeFormatted(),
            'completion_notes' => $log->completion_notes,
            'technician_name' => $technicianName, // Simple string, not object
            'technician_id' => $log->technician_id,
            'can_delete' => auth()->id() == $log->technician_id || auth()->user()->isSuperAdmin() || auth()->user()->isAdmin(),
        ];
    });
    
    // Debug the final output
    \Log::info('Formatted logs output', [
        'formatted_count' => $formattedLogs->count(),
        'sample_output' => $formattedLogs->first()
    ]);
        
    return response()->json($formattedLogs);
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
}
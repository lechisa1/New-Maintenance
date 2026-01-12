<?php

namespace App\Http\Controllers\technician;

use Carbon\Carbon;
use App\Models\User;
use App\Models\WorkLog;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\MaintenanceRequest;
use App\Helpers\OrganizationHelper;
use App\Notifications\NotFixedNotification;
use App\Notifications\WorkCompletedNotification;
use App\Notifications\WorkInProgressNotification;
use App\Notifications\MaintenanceNotFixedNotification;

class TechnicianController extends Controller
{

    public function dashboard()
    {
        $technicianId = auth()->id();

$labels = OrganizationHelper::labels();
        $requests = MaintenanceRequest::whereHas('assignments', function ($query) use ($technicianId) {
            $query->where('technician_id', $technicianId);
        });

        $totalAssigned = $requests->count();


        $completedThisWeek = (clone $requests)->where('status', 'completed')
            ->whereBetween('updated_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Requests per day (for weekly chart)
        $weeklyData = (clone $requests)
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');


        $statusBreakdown = (clone $requests)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Average resolution time (in hours)
        $avgResolutionTime = (clone $requests)
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_time')
            ->value('avg_time');

        return view('technician.dashboard.dashboard', compact(
            'totalAssigned',
            'completedThisWeek',
            'weeklyData',
            'statusBreakdown',
            'avgResolutionTime','labels'
        ));
    }
    public function workProgressIndex()
    {
    $labels = OrganizationHelper::labels();
        return view('technician.tecknician_work_form',compact('labels'));
    }

public function show(MaintenanceRequest $request)
{
$labels = OrganizationHelper::labels();
    $isDirector = User::where('id', auth()->id())
        ->whereHas('roles', function ($q) {
            $q->where('name', 'technician');
        })
        ->exists();

    $request->load([
        'user',
        'categories',
        'latestAssignment.director',
        'latestAssignment.technician',
        'workLogs.technician',
        'item',
        'item.categories',
        'user.reportsTo',
        'user.jobPosition',
        'rejectedBy',
        'attachments',
        'updates.user'
    ]);
    
    // Block if technician is not assigned to this request
    if (
        auth()->user()->hasRole('technician') &&
        !$request->assignments->contains('technician_id', auth()->id())
    ) {
        abort(403, 'You are not authorized to view this request.');
    }
    
    $technicians = [];
    if (
        auth()->user()->can('assign-requests') ||
        (auth()->user()->hasRole('technician') && auth()->user()->id === $request->assignment?->technician_id)
    ) {
        $technicians = User::whereHas('roles', function ($q) use ($request) {
            $q->where('name', 'technician');

        })->get();
    }

    return view('technician.requests.show', [
        'request' => $request,
        'technicians' => $technicians,
        'isDirector' => $isDirector,
        'labels'=>$labels
    ]);
}
    public function assignedRequests()
    {
    $labels = OrganizationHelper::labels();
        $requests = MaintenanceRequest::with(['user', 'assignments', 'item'])
            ->whereHas('assignments', function ($query) {
                $query->where('technician_id', auth()->id());
            })
            ->where('status', 'assigned')

            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('technician.requests.index', compact('requests','labels'));
    }
    public function technicianWorkProgress(MaintenanceRequest $request)
    {
      $labels = OrganizationHelper::labels();
        return view('technician.tecknician_work_form', [
            'request' => $request->load(['assignments']),
            'labels'=>$labels
        ]);
    }

    public function updateWork(MaintenanceRequest $maintenanceRequest, Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'work_done' => 'required|string|min:10',
            'materials_used' => 'nullable|string|max:255',
            'time_spent_minutes' => 'nullable|numeric|min:0.1',

            'completion_notes' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['in_progress', 'not_fixed', 'completed'])]
        ]);

        // Check if a WorkLog already exists for this technician and maintenance request
        $workLog = $maintenanceRequest->workLogs()->where('technician_id', auth()->id())->first();

        if ($workLog) {
            // Update the existing WorkLog
            $workLog->update([
                'work_done' => $validated['work_done'],
                'materials_used' => $validated['materials_used'] ?? $workLog->materials_used, // Keep existing if not provided
                'time_spent_minutes' => $validated['time_spent_minutes'],
                'completion_notes'=> $validated['completion_notes']
            ]);
        } else {
            // Create a new WorkLog
            $maintenanceRequest->workLogs()->create([
                'technician_id' => auth()->id(),
                'work_done' => $validated['work_done'],
                'materials_used' => $validated['materials_used'] ?? null,
                'time_spent_minutes' => $validated['time_spent_minutes'],
                'completion_notes'=> $validated['completion_notes']
            ]);
        }

        // Update MaintenanceRequest status if changed
        if ($maintenanceRequest->status != $validated['status']) {
            $updateData = ['status' => $validated['status']];

            if ($validated['status'] === 'completed') {
                $updateData['completed_at'] = now();
            }

            $maintenanceRequest->update($updateData);

            // Add a status update log
            $maintenanceRequest->updates()->create([
                'user_id' => auth()->id(),
                'update_text' => $validated['status'] === 'completed'
                    ? 'Completed work: ' . ($validated['completion_notes'] ?? 'No notes provided')
                    : 'Started working on the request',
                'update_type' => 'status_change',

            ]);

            // Reset user feedback if previously rejected
            if ($maintenanceRequest->user_feedback === 'rejected') {
                $maintenanceRequest->update(['user_feedback' => 'pending']);
            }

            // Send notifications

            if ($validated['status'] === 'in_progress') {
                $assignment = $maintenanceRequest->assignments()->latest()->first();
                if ($assignment && $assignment->director_id) {
                    $assignment->director->notify(new WorkInProgressNotification($maintenanceRequest));
                }
            } elseif ($validated['status'] === 'completed') {
                $maintenanceRequest->user->notify(new WorkCompletedNotification($maintenanceRequest));
            } elseif ($validated['status'] === 'not_fixed') {
                $assignment = $maintenanceRequest->assignments()->latest()->first();
                if ($assignment && $assignment->director_id) {
                    $assignment->director->notify(new MaintenanceNotFixedNotification($maintenanceRequest));
                }
            }
        }

        return redirect()->route('technician.show', $maintenanceRequest)
            ->with('success', 'Work log updated successfully!');
    }
    public function completedTask()
    {
$labels = OrganizationHelper::labels();
        $completed = Assignment::with([
            'maintenanceRequest.categories',
            'maintenanceRequest.user',
            'director',
            'maintenanceRequest.item'
        ])->where('technician_id', auth()->id())
            ->whereHas('maintenanceRequest', fn($q) => $q->where('status', 'completed'))
            ->latest()->paginate(10);
        return view('technician.tasks.completed', [
            'requests' => $completed,
            'labels'=>$labels
        ]);
    }


    public function inProgressTask()
    {
    $labels = OrganizationHelper::labels();
        $inProgress = Assignment::with([
            'maintenanceRequest.categories',
            'maintenanceRequest.user',
            'director',
            'maintenanceRequest.item'
        ])->where('technician_id', auth()->id())
            ->whereHas('maintenanceRequest', fn($q) => $q->where('status', 'in_progress'))
            ->latest()->paginate(10);

        return view('technician.tasks.inProgress', [
            'requests' => $inProgress,
            'labels'=>$labels
        ]);
    }
}

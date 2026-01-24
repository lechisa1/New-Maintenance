<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestFile;
use App\Models\Item;
use App\Models\User;
use App\Models\IssueType;
use App\Http\Requests\StoreMaintenanceRequestRequest;
use App\Http\Requests\UpdateMaintenanceRequestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\MaintenanceRequestAssigned;
use App\Notifications\MaintenanceRequestCreated;
use App\Notifications\MaintenanceRequestApproval;

class MaintenanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaintenanceRequest::where('user_id', auth()->id())->with(['user', 'item', 'assignedTechnician']);
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('item', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }
        
        // Filter by user role
        // if ($request->user()->hasRole('user')) {
        //     $query->where('user_id', $request->user()->id);
        // } elseif ($request->user()->hasRole('technician')) {
        //     $query->where('assigned_to', $request->user()->id);
        // }
        
        // Get requests with filters
        $requests = $query->latest()->paginate(15);
        
        // Statistics
        $totalRequests = MaintenanceRequest::where('user_id', auth()->id())->count();
        $openRequests = MaintenanceRequest::where('user_id', auth()->id())->open()->count();
        $completedRequests = MaintenanceRequest::where('user_id', auth()->id())->where('status', MaintenanceRequest::STATUS_COMPLETED)->count();
        // $myRequests = $request->user()->hasRole('user') ? 
        //     MaintenanceRequest::forUser($request->user()->id)->count() : 
        //     MaintenanceRequest::assignedTo($request->user()->id)->count();
        
        $myRequests =10;
        // Recent requests for sidebar
        $recentRequests = MaintenanceRequest::where('user_id', auth()->id())->with('item')
            ->latest()
            ->take(5)
            ->get();
        
        return view('maintenance-requests.index', compact(
            'requests',
            'totalRequests',
            'openRequests',
            'completedRequests',
            'myRequests',
            'recentRequests'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active items for dropdown
        $items = Item::active()->orderBy('name')->get(['id', 'name', 'type']);
        $issueTypes = IssueType::orderBy('name')->get();
        // Get user's recent requests
        $userRecentRequests = MaintenanceRequest::forUser(auth()->id())
            ->latest()
            ->take(3)
            ->get();
        
        return view('maintenance-requests.create', compact('items', 'userRecentRequests','issueTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(StoreMaintenanceRequestRequest $request)
{
    DB::beginTransaction();
    
    try {
        // Create maintenance request
        $maintenanceRequest = MaintenanceRequest::create([
            'user_id' => auth()->id(),
            'item_id' => $request->item_id,
            'description' => $request->description,
            'issue_type_id' => $request->issue_type_id,
            'priority' => $request->priority,
            'status' =>"pending",
        ]);
        
        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('maintenance-requests/' . $maintenanceRequest->id, $filename, 'public');
                
                MaintenanceRequestFile::create([
                    'maintenance_request_id' => $maintenanceRequest->id,
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ]);
            }
        }
        

        DB::commit();
        
        return redirect()->route('maintenance-requests.index')
            ->with('success', 'Maintenance request created successfully. Ticket: ' . $maintenanceRequest->ticket_number);
            
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Maintenance request creation failed: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'error' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to create maintenance request: ' . $e->getMessage());
    }
}

    public function getStaffRequestsForApproval(Request $request)
{
    $chairman = auth()->user();

    // Get requests that need approval for this chairman
    $requests = MaintenanceRequest::with([
            'user:id,full_name,division_id,cluster_id',
            'issueType:id,name,is_need_approval'
        ])
        ->whereHas('issueType', function($query) {
            $query->where('is_need_approval', true);
        })
        ->whereHas('user', function($query) use ($chairman) {
            // Match requests where the user belongs to a division or cluster under this chairman
            $query->where(function($q) use ($chairman) {
                // If chairman is division_chairman
                $q->whereHas('division', function($d) use ($chairman) {
                    $d->where('division_chairman', $chairman->id);
                })
                // OR if chairman is cluster_chairman
                ->orWhereHas('cluster', function($c) use ($chairman) {
                    $c->where('cluster_chairman', $chairman->id);
                });
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('chairman.requests.index', compact('requests'));
}
public function approve(Request $request, MaintenanceRequest $maintenanceRequest)
{
    dd($request->all(), $maintenanceRequest);
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
           'is_approved' => true,
            'approved_at' => now(),
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
                'file_name' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'type' => 'approval',
                'uploaded_by' => auth()->id(),
            ]);
        }
    });

    return back()->with('success', 'Maintenance request approved successfully.');
}

    /**
     * Display the specified resource.
     */
public function show(MaintenanceRequest $maintenanceRequest)
{
    
    $maintenanceRequest->load(['user', 'item', 'assignedTechnician', 'files']);
    
    // Get technicians who have 'reports.assign' permission
    $technicians = User::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'reports.assign');
        })
        ->orWhereHas('permissions', function ($query) {
            $query->where('name', 'reports.assign');
        })
        ->select('id', 'full_name', 'email')
        ->get()
        ->mapWithKeys(function ($user) {
            return [$user->id => $user->full_name . ' (' . $user->email . ')'];
        })
        ->toArray();

    // Get similar requests
    $similarRequests = MaintenanceRequest::where('item_id', $maintenanceRequest->item_id)
        ->where('id', '!=', $maintenanceRequest->id)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    return view('maintenance-requests.show', compact(
        'maintenanceRequest', 
        'technicians', 
        'similarRequests'
    ));
}
/**
 * Assign technician to maintenance request.
 */
public function assign(Request $request, MaintenanceRequest $maintenanceRequest)
{
    // Check if user has permission to assign
    if (!auth()->user()->can('maintenance_requests.assign')) {
        return redirect()->back()
            ->with('error', 'You do not have permission to assign technicians.');
    }

    // Validate the request
    $validated = $request->validate([
        'assigned_to' => 'required|exists:users,id',
        'technician_notes' => 'nullable|string|max:1000',
    ]);

    // Check if the assigned user has 'reports.assign' permission
    $assignedUser = User::findOrFail($validated['assigned_to']);
    if (!$assignedUser->hasPermissionTo('reports.assign')) {
        return redirect()->back()
            ->with('error', 'Selected user does not have the required permission.');
    }

    // Update the maintenance request
    $maintenanceRequest->update([
        'assigned_to' => $validated['assigned_to'],
        'assigned_at' => now(),
        'status' => $maintenanceRequest->status === 'waiting_approval' ? 'assigned' : $maintenanceRequest->status,
        'technician_notes' => $validated['technician_notes'] ?? $maintenanceRequest->technician_notes,
    ]);

    // Optional: Send notification to assigned technician
    try {
        $assignedUser->notify(new \App\Notifications\MaintenanceRequestAssigned($maintenanceRequest));
    } catch (\Exception $e) {
        \Log::error('Failed to send assignment notification: ' . $e->getMessage());
    }

    return redirect()->back()
        ->with('success', 'Technician assigned successfully.');
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceRequest $maintenanceRequest)
    {
        // Authorization check
        // if (auth()->user()->hasRole('user') && $maintenanceRequest->user_id !== auth()->id()) {
        //     abort(403, 'Unauthorized access.');
        // }
        
        $maintenanceRequest->load(['item']);
        
        // Get active items for dropdown
        $items = Item::active()->orderBy('name')->get(['id', 'name', 'type']);
        $issueTypes = IssueType::orderBy('name')->get();
        // Get technicians for assignment
        // $technicians = User::role('technician')->orderBy('full_name')->get(['id', 'full_name', 'email']);
        $technicians=[
            '1' => 'Tech One',
            '2' => 'Tech Two',
            '3' => 'Tech Three',
        ];
        
        return view('maintenance-requests.edit', compact('maintenanceRequest', 'items', 'technicians', 'issueTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaintenanceRequestRequest $request, MaintenanceRequest $maintenanceRequest)
    {
        // Authorization check
        if (auth()->user()->hasRole('user') && $maintenanceRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        DB::beginTransaction();
        
        try {
            $updates = $request->validated();
            
            // Handle status changes
            if ($request->has('status')) {
                $updates = $this->handleStatusChange($maintenanceRequest, $request->status, $updates);
            }
            
            // Update maintenance request
            $maintenanceRequest->update($updates);
            
            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('maintenance-requests/' . $maintenanceRequest->id, $filename, 'public');
                    
                    MaintenanceRequestFile::create([
                        'maintenance_request_id' => $maintenanceRequest->id,
                        'filename' => $filename,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'path' => $path,
                        'size' => $file->getSize(),
                    ]);
                }
            }
            
            // Update item status based on request status
            $this->updateItemStatus($maintenanceRequest);
            
            DB::commit();
            
            return redirect()->route('maintenance-requests.show', $maintenanceRequest)
                ->with('success', 'Maintenance request updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Maintenance request update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update maintenance request. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        // Only allow deletion if request is pending or user is admin
        if ($maintenanceRequest->status !== MaintenanceRequest::STATUS_PENDING && 
            !auth()->user()->hasRole('admin')) {
            return redirect()->back()
                ->with('error', 'Only pending requests can be deleted.');
        }
        
        try {
            // Delete associated files
            foreach ($maintenanceRequest->files as $file) {
                Storage::disk('public')->delete($file->path);
                $file->delete();
            }
            
            // Delete maintenance request
            $maintenanceRequest->delete();
            
            return redirect()->route('maintenance-requests.index')
                ->with('success', 'Maintenance request deleted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Maintenance request deletion failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete maintenance request. Please try again.');
        }
    }

    /**
     * Handle file download
     */
    public function downloadFile(MaintenanceRequest $maintenanceRequest, $fileId)
    {
        $file = MaintenanceRequestFile::findOrFail($fileId);
        
        // Authorization check
        if ($file->maintenance_request_id !== $maintenanceRequest->id) {
            abort(404);
        }
        
        if (auth()->user()->hasRole('user') && $maintenanceRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        if (!Storage::disk('public')->exists($file->path)) {
            abort(404);
        }
        
        return Storage::disk('public')->download($file->path, $file->original_name);
    }

    /**
     * Delete file from maintenance request
     */
    public function deleteFile(MaintenanceRequest $maintenanceRequest, $fileId)
    {
        $file = MaintenanceRequestFile::findOrFail($fileId);
        
        // Authorization check
        if ($file->maintenance_request_id !== $maintenanceRequest->id) {
            abort(404);
        }
        
        if (auth()->user()->hasRole('user') && $maintenanceRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
        
        try {
            Storage::disk('public')->delete($file->path);
            $file->delete();
            
            return redirect()->back()
                ->with('success', 'File deleted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('File deletion failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete file.');
        }
    }

    /**
     * Handle status change with timestamps
     */
    private function handleStatusChange($maintenanceRequest, $newStatus, $updates): array
    {
        switch ($newStatus) {
            case MaintenanceRequest::STATUS_ASSIGNED:
                $updates['assigned_at'] = now();
                break;
                
            case MaintenanceRequest::STATUS_IN_PROGRESS:
                $updates['started_at'] = now();
                break;
                
            case MaintenanceRequest::STATUS_COMPLETED:
                $updates['completed_at'] = now();
                break;
                
            case MaintenanceRequest::STATUS_REJECTED:
                $updates['rejected_at'] = now();
                break;
        }
        
        return $updates;
    }

    /**
     * Update item status based on maintenance request status
     */
    private function updateItemStatus($maintenanceRequest): void
    {
        if (!$maintenanceRequest->item) {
            return;
        }
        
        $item = $maintenanceRequest->item;
        
        switch ($maintenanceRequest->status) {
            case MaintenanceRequest::STATUS_IN_PROGRESS:
                $item->update(['status' => Item::STATUS_MAINTENANCE]);
                break;
                
            case MaintenanceRequest::STATUS_COMPLETED:
                $item->update(['status' => Item::STATUS_ACTIVE]);
                break;
                
            case MaintenanceRequest::STATUS_NOT_FIXED:
                $item->update(['status' => Item::STATUS_INACTIVE]);
                break;
        }
    }

    /**
     * Export maintenance requests to CSV
     */
    public function export(Request $request)
    {
        $query = MaintenanceRequest::with(['user', 'item', 'assignedTechnician']);
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where('ticket_number', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        $requests = $query->latest()->get();
        
        $filename = 'maintenance_requests_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ];
        
        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'Ticket Number',
                'Equipment',
                'Description',
                'Issue Type',
                'Priority',
                'Status',
                'Requested By',
                'Assigned To',
                'Requested Date',
                'Completed Date',
                'Response Time (hours)',
                'Resolution Time (hours)'
            ]);
            
            // Data
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->ticket_number,
                    $request->item?->name ?? 'N/A',
                    substr($request->description, 0, 100) . (strlen($request->description) > 100 ? '...' : ''),
                    $request->getIssueTypeText(),
                    $request->getPriorityText(),
                    $request->getStatusText(),
                    $request->user?->full_name ?? 'N/A',
                    $request->assignedTechnician?->full_name ?? 'Not Assigned',
                    $request->requested_at->format('Y-m-d H:i:s'),
                    $request->completed_at?->format('Y-m-d H:i:s') ?? 'N/A',
                    $request->getResponseTime() ?? 'N/A',
                    $request->getResolutionTime() ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get maintenance request statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => MaintenanceRequest::count(),
            'pending' => MaintenanceRequest::where('status', MaintenanceRequest::STATUS_PENDING)->count(),
            'in_progress' => MaintenanceRequest::where('status', MaintenanceRequest::STATUS_IN_PROGRESS)->count(),
            'completed' => MaintenanceRequest::where('status', MaintenanceRequest::STATUS_COMPLETED)->count(),
            'by_priority' => DB::table('maintenance_requests')
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->priority => $item->count];
                }),
            'by_issue_type' => DB::table('maintenance_requests')
                ->select('issue_type', DB::raw('count(*) as count'))
                ->groupBy('issue_type')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->issue_type => $item->count];
                }),
            'avg_response_time' => MaintenanceRequest::whereNotNull('assigned_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, requested_at, assigned_at)) as avg_time'))
                ->first()->avg_time ?? 0,
            'avg_resolution_time' => MaintenanceRequest::whereNotNull('completed_at')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, requested_at, completed_at)) as avg_time'))
                ->first()->avg_time ?? 0,
        ];
        
        return response()->json($stats);
    }

    /**
     * Assign request to technician
     */
    // public function assign(Request $request, MaintenanceRequest $maintenanceRequest)
    // {
    //     if (!auth()->user()->hasAnyRole(['admin', 'technician_lead'])) {
    //         abort(403, 'Unauthorized.');
    //     }
        
    //     $request->validate([
    //         'assigned_to' => 'required|exists:users,id',
    //     ]);
        
    //     try {
    //         $maintenanceRequest->update([
    //             'assigned_to' => $request->assigned_to,
    //             'status' => MaintenanceRequest::STATUS_ASSIGNED,
    //             'assigned_at' => now(),
    //         ]);
            
    //         // Send notification to technician
    //         // Notification::send($technician, new MaintenanceRequestAssigned($maintenanceRequest));
            
    //         return redirect()->back()
    //             ->with('success', 'Request assigned to technician successfully.');
                
    //     } catch (\Exception $e) {
    //         \Log::error('Request assignment failed: ' . $e->getMessage());
            
    //         return redirect()->back()
    //             ->with('error', 'Failed to assign request.');
    //     }
    // }

    /**
     * Update status (for technicians)
     */
    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        if (!auth()->user()->hasRole('technician') || 
            $maintenanceRequest->assigned_to !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }
        
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                MaintenanceRequest::STATUS_IN_PROGRESS,
                MaintenanceRequest::STATUS_COMPLETED,
                MaintenanceRequest::STATUS_NOT_FIXED
            ]),
            'resolution_notes' => 'nullable|string|max:2000',
        ]);
        
        try {
            $updates = ['status' => $request->status];
            
            if ($request->status === MaintenanceRequest::STATUS_COMPLETED) {
                $updates['completed_at'] = now();
            }
            
            if ($request->filled('resolution_notes')) {
                $updates['resolution_notes'] = $request->resolution_notes;
            }
            
            $maintenanceRequest->update($updates);
            
            // Update item status
            $this->updateItemStatus($maintenanceRequest);
            
            return redirect()->back()
                ->with('success', 'Status updated successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Status update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update status.');
        }
    }
}
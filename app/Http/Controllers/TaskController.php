<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function index(Request $request)
{
    $user = Auth::user();
    $query = MaintenanceRequest::query();

    // --- FILTERS ---
    if ($search = $request->search) {
        $query->where(function($q) use ($search) {
            $q->where('ticket_number', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    if ($status = $request->status) {
        $query->where('status', $status);
    }

    if ($priority = $request->priority) {
        $query->where('priority', $priority);
    }

    // --- ACCESS CONTROL ---
    if ($user->isSuperAdmin() || $user->isAdmin()) {
        // Super admin & admin see everything
    } elseif ($user->isIctDirector()) {
        // ICT Director → approved requests that needed approval OR requests that do NOT need approval
        $query->visibleToIct();
    } else {
        // All other users: division chairman, cluster chairman, normal user
        $query->where(function ($mainQuery) use ($user) {

            // 1️⃣ Tasks assigned to me (not my own requests)
            $mainQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->where('user_id', '!=', $user->id);
            });

            // 2️⃣ Division Chairman → approval requests from division
            if ($user->isDivisionChairman()) {
                $divisionId = $user->division->id;

                $mainQuery->orWhere(function ($q) use ($divisionId) {
                    $q->whereHas('user', function ($sub) use ($divisionId) {
                        $sub->where('division_id', $divisionId);
                    })->whereHas('issueType', function ($q) {
                        $q->where('is_need_approval', true);
                    });
                });
            }

            // 3️⃣ Cluster Chairman → approval requests from cluster
            elseif ($user->isClusterChairman()) {
                $clusterId = $user->cluster->id;

                $mainQuery->orWhere(function ($q) use ($clusterId) {
                    $q->whereHas('user', function ($sub) use ($clusterId) {
                        $sub->whereHas('division', function ($d) use ($clusterId) {
                            $d->where('cluster_id', $clusterId);
                        })
                        ->whereHas('division', function ($div) {
                            $div->whereColumn('division_chairman', 'users.id');
                        });
                    });

                    $q->orWhereHas('user', function ($sub) use ($clusterId) {
                        $sub->where('cluster_id', $clusterId)
                            ->whereNull('division_id');
                    });

                })->whereHas('issueType', function ($q) {
                    $q->where('is_need_approval', true);
                });
            }
        });
    }

    // --- PAGINATION ---
              $requests = $query
        ->orderByRaw("FIELD(priority, 'emergency','high', 'medium', 'low')")
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // --- STATISTICS ---
    $totalRequests = (clone $query)->count();
    $openRequests = (clone $query)->whereIn('status', ['pending', 'assigned', 'in_progress'])->count();
    $completedRequests = (clone $query)->where('status', 'completed')->count();
    $myRequests = MaintenanceRequest::where('user_id', $user->id)->count();

    return view('task.index', [
        'requests' => $requests,
        'totalRequests' => $totalRequests,
        'openRequests' => $openRequests,
        'completedRequests' => $completedRequests,
        'myRequests' => $myRequests,
        'pageType' => 'tasks',
    ]);
}
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

    return view('task.show', compact(
        'maintenanceRequest', 
        'technicians', 
        'similarRequests'
    ));
}
}
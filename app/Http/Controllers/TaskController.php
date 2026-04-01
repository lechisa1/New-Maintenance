<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\MaintenanceRequestTechnician;
use App\Models\ApprovalRequest;
use App\Models\User;
use App\Notifications\ApprovalRequestSubmitted;

use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = MaintenanceRequest::with([
            'user',
            'items.issueType',
            'assignedTechnicians.technician',
            'approvalRequest.technician',
            'approvalRequest.issueType'
        ]);

        /*
    |--------------------------------------------------------------------------
    | SEARCH FILTER
    |--------------------------------------------------------------------------
    */
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
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

        /*
    |--------------------------------------------------------------------------
    | ACCESS CONTROL
    |--------------------------------------------------------------------------
    */
        if ($user->isSuperAdmin() || $user->isAdmin()) {

            // See everything — no filter needed

        } elseif ($user->isIctDirector()) {

            $query->where(function ($q) use ($user) {
                $q->visibleToIct()
                    ->orWhere('status', 'pending_approval_review')
                    ->orWhereHas('approvalRequest', function ($sub) {
                        $sub->where('status', 'pending');
                    });
            });
        } else {

            $query->where(function ($mainQuery) use ($user) {

                /*
            |--------------------------------------------------------------------------
            | 1️⃣ Tasks assigned to me (NOT my own request)
            |--------------------------------------------------------------------------
            */
                $mainQuery->where(function ($q) use ($user) {
                    $q->whereHas('assignedTechnicians', function ($sub) use ($user) {
                        $sub->where('user_id', $user->id)
                            ->whereIn('status', ['assigned', 'in_progress']);
                    })->where('user_id', '!=', $user->id);
                });

                /*
            |--------------------------------------------------------------------------
            | 2️⃣ Division Chairman
            |--------------------------------------------------------------------------
            */

                if ($user->isDivisionChairman()) {
                    $divisionId = $user->division_id;
                    $userId = $user->id;

                    $mainQuery->orWhere(function ($q) use ($divisionId, $userId) {
                        $q->whereHas('user', function ($sub) use ($divisionId, $userId) {
                            $sub->where('division_id', $divisionId)
                                ->where('id', '!=', $userId); // 🚀 Exclude own request
                        })
                            ->whereHas('items.issueType', function ($sub) {
                                $sub->where('is_need_approval', true);
                            })
                            ->whereIn('status', ['waiting_approval', 'pending_approval_review', 'pending']);
                    });
                }

                /*
            |--------------------------------------------------------------------------
            | 3️⃣ Cluster Chairman
            |--------------------------------------------------------------------------
            */
                /*
|--------------------------------------------------------------------------
| 3️⃣ Cluster Chairman
|--------------------------------------------------------------------------
*/ elseif ($user->isClusterChairman()) {

                    $clusterId = $user->cluster_id;

                    $mainQuery->orWhere(function ($q) use ($clusterId) {

                        $q->whereHas('items.issueType', function ($sub) {
                            $sub->where('is_need_approval', true);
                        })
                            ->whereIn('status', ['waiting_approval', 'pending_approval_review', 'pending'])
                            ->whereHas('user', function ($sub) use ($clusterId) {

                                $sub->where(function ($userQuery) use ($clusterId) {

                                    /*
                    |--------------------------------------------------------------------------
                    | 1️⃣ Division Chairmen under this cluster
                    |--------------------------------------------------------------------------
                    */
                                    $userQuery->whereHas('division', function ($div) use ($clusterId) {
                                        $div->where('cluster_id', $clusterId)
                                            ->whereColumn('divisions.division_chairman', 'users.id');
                                    });

                                    /*
                    |--------------------------------------------------------------------------
                    | 2️⃣ Users without division but directly under cluster
                    |--------------------------------------------------------------------------
                    */
                                    $userQuery->orWhere(function ($direct) use ($clusterId) {
                                        $direct->whereNull('division_id')
                                            ->where('cluster_id', $clusterId);
                                    });
                                });
                            });
                    });
                }
            });
        }

        /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */
        $requests = $query
            ->orderByRaw("FIELD(priority, 'emergency','high','medium','low')")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        /*
    |--------------------------------------------------------------------------
    | STATISTICS
    |--------------------------------------------------------------------------
    */
        $totalRequests = (clone $query)->count();

        $openRequests = (clone $query)
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->count();

        $completedRequests = (clone $query)
            ->where('status', 'completed')
            ->count();

        // For technicians - count their assigned items
        if ($user->can('maintenance_requests.resolve')) {
            $myRequests = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();
        } else {
            $myRequests = MaintenanceRequest::where('user_id', $user->id)->count();
        }

        // Approval requests pending for ICT directors
        $pendingApprovalReviews = 0;
        if ($user->isIctDirector()) {
            $pendingApprovalReviews = ApprovalRequest::where('status', 'pending')
                ->whereHas('maintenanceRequest', function ($q) {
                    $q->where('status', 'pending_approval_review');
                })
                ->count();
        }

        return view('task.index', [
            'requests' => $requests,
            'totalRequests' => $totalRequests,
            'openRequests' => $openRequests,
            'completedRequests' => $completedRequests,
            'myRequests' => $myRequests,
            'pendingApprovalReviews' => $pendingApprovalReviews ?? 0,
            'pageType' => 'tasks',
        ]);
    }
    public function show(MaintenanceRequest $maintenanceRequest)
    {

        $maintenanceRequest->load(['user', 'item', 'assignedTechnician', 'files']);

        // Get technicians who have 'maintenance_requests.resolve' permission
        $technicians = User::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'maintenance_requests.resolve');
        })
            ->orWhereHas('permissions', function ($query) {
                $query->where('name', 'maintenance_requests.resolve');
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

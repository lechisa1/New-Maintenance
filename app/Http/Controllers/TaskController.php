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

        // Filter by search
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Filter by priority
        if ($priority = $request->priority) {
            $query->where('priority', $priority);
        }
        //  dd($user->isDivisionChairman(), $user->isClusterChairman());
        // 1️⃣ Super Admin / Admin → all requests
            // 1 Super Admin / Admin → all requests
        if (!($user->isSuperAdmin() || $user->isAdmin())) {

            $query->where(function ($mainQuery) use ($user) {

                // TASKS: Assigned to me BUT not created by me
                $mainQuery->where(function ($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                    ->where('user_id', '!=', $user->id);
                });

                // 2 Division Chairman → approval requests
                if ($user->isDivisionChairman()) {
                    $divisionId = $user->division->id;

                    $mainQuery->orWhere(function ($q) use ($divisionId) {
                        $q->whereHas('user', function ($sub) use ($divisionId) {
                                $sub->where('division_id', $divisionId);
                            })
                            ->whereHas('issueType', function ($q) {
                                $q->where('is_need_approval', true);
                            });
                    });
                }

                // 3 Cluster Chairman → approval requests
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

                // 4 Normal user → ONLY tasks, not own requests
                else {
                    $mainQuery->orWhere(function ($q) use ($user) {
                        $q->where('assigned_to', $user->id)
                        ->where('user_id', '!=', $user->id);
                    });
                }
            });
        }


                    $requests = $query->latest()->paginate(10);
            // Clone base query for stats
            $baseQuery = clone $query;

            $totalRequests = (clone $baseQuery)->count();

            $openRequests = (clone $baseQuery)
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->count();

            $completedRequests = (clone $baseQuery)
                ->where('status', 'completed')
                ->count();


                    $myRequests = MaintenanceRequest::where('user_id', $user->id)->count();

                    return view('task.index', [
                        'requests'=>$requests,
                        'totalRequests'=>$totalRequests,
                        'openRequests'=>$openRequests,
                        'completedRequests'=>$completedRequests,
                        'myRequests'=>$myRequests,
                        'pageType' => 'tasks',
                    ]);
                }
}
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
        if (!($user->isSuperAdmin() || $user->isAdmin())) {

            // 2️⃣ Division Chairman → users in division & needing approval
            if ($user->isDivisionChairman()) {
                $divisionId = $user->division->id;

                $query->whereHas('user', function ($sub) use ($divisionId) {
                        $sub->where('division_id', $divisionId);
                    })
                    ->whereHas('issueType', function ($q) {
                        $q->where('is_need_approval', true);
                    });
            }

            // 3️⃣ Cluster Chairman → division chairmen + users without division under cluster, needing approval
          elseif ($user->isClusterChairman()) {
    $clusterId = $user->cluster->id;

    $query->where(function ($q) use ($clusterId) {

        // 1️⃣ Requests from Division Chairmen under this Cluster
        $q->whereHas('user', function ($sub) use ($clusterId) {
            $sub->whereHas('division', function ($d) use ($clusterId) {
                $d->where('cluster_id', $clusterId);
            })
            ->whereHas('division', function ($div) {
                $div->whereColumn('division_chairman', 'users.id');
            });
        });

        // 2️⃣ Requests from users directly under cluster (no division)
        $q->orWhereHas('user', function ($sub) use ($clusterId) {
            $sub->where('cluster_id', $clusterId)
                ->whereNull('division_id');
        });

    })->whereHas('issueType', function ($q) {
        $q->where('is_need_approval', true);
    });
}


            // 4️⃣ Normal user → only their requests
            else {
                $query->where('user_id', $user->id);
            }
        }

        $requests = $query->latest()->paginate(20);

        // Stats for cards
        $totalRequests = MaintenanceRequest::count();
        $openRequests = MaintenanceRequest::open()->count();
        $completedRequests = MaintenanceRequest::closed()->count();
        $myRequests = MaintenanceRequest::where('user_id', $user->id)->count();

        return view('task.index', compact(
            'requests',
            'totalRequests',
            'openRequests',
            'completedRequests',
            'myRequests'
        ));
    }
}
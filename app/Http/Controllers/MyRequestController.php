<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\Auth;

class MyRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = MaintenanceRequest::where('user_id', $user->id);

        // 🔍 Filters (same as TaskController)
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

        $requests = $query->latest()->paginate(10);
        $totalRequests = MaintenanceRequest::where('user_id', $user->id)->count();
        $openRequests = MaintenanceRequest::where('user_id', $user->id)->open()->count();
        $completedRequests = MaintenanceRequest::where('user_id', $user->id)->closed()->count();
        $myRequests = MaintenanceRequest::where('user_id', $user->id)->count();
        return view('task.index', [
            'requests' => $requests,
            'totalRequests'=>$totalRequests,
            'openRequests'=>$openRequests,
            'completedRequests'=>$completedRequests,
            'myRequests'=>$myRequests,
            'pageType' => 'my_requests',
        ]);
    }
}
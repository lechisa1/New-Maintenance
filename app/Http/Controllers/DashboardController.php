<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\IssueType;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Cluster;

use App\Models\MaintenanceRequestTechnician;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceRequestItem;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Check if user has user management permissions
        if ($user->can('users.create') || $user->can('users.index')) {
            // System Admin Dashboard
            return $this->adminDashboard();
        }

        // Otherwise, show maintenance dashboard
        return $this->maintenanceDashboard($user);
    }

    private function adminDashboard()
    {
        $user = auth()->user();

        // System Administration Statistics
        $totalUsers = User::count();

        $activeUsers = User::where('is_active', 1)->count();
        $inactiveUsers = User::where('is_active', 0)->count();

        $totalRoles = Role::count();
        $totalDivisions = Division::count();
        $totalClusters = Cluster::count();

        // User distribution by role
        $roleDistribution = Role::withCount('users')->get();

        // User distribution by division
        $divisionDistribution = Division::withCount('users')
            ->orderBy('users_count', 'desc')
            ->limit(10)
            ->get();

        // Recent users
        $recentUsers = User::with(['roles', 'division', 'cluster'])
            ->latest()
            ->limit(5)
            ->get();

        // User activity statistics
        $usersWithActivity = User::whereNotNull('last_login_at')->count();
        $usersLastWeek = User::where('last_login_at', '>=', now()->subWeek())->count();
        $usersLastMonth = User::where('last_login_at', '>=', now()->subMonth())->count();

        // Get users per month for chart
        $monthlyUserStats = $this->getMonthlyUserStatistics();

        return view('pages.dashboard.admin', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalRoles',
            'totalDivisions',
            'totalClusters',
            'roleDistribution',
            'divisionDistribution',
            'recentUsers',
            'usersWithActivity',
            'usersLastWeek',
            'usersLastMonth',
            'monthlyUserStats'
        ));
    }

    private function maintenanceDashboard($user)
    {
        // Initialize variables with default values
        $totalRequests = 0;
        $pendingRequests = 0;
        $inProgressRequests = 0;
        $completedRequests = 0;
        $assignedToMe = 0;
        $recentRequests = collect();
        $issueTypeStats = collect();
        $issueTypes = 0;
        $issueTypeAnalysis = collect();
        $itemAnalysis = collect();

        // REMOVE THESE LINES - constants cannot be defined inside a method!
        // const STATUS_PENDING = 'pending';
        // const STATUS_WAITING_APPROVAL = 'waiting_approval';
        // const STATUS_IN_PROGRESS = 'in_progress';
        // const STATUS_COMPLETED = 'completed';
        // const STATUS_CONFIRMED = 'confirmed';
        // const STATUS_ASSIGNED = 'assigned';
        // const STATUS_REJECTED = 'rejected';

        // Metrics based on user role
        if ($user->can('maintenance_requests.assign')) {
            // Admin/ICT Director view
            $totalRequests = MaintenanceRequest::count();

            // Pending includes both pending and waiting_approval
            $pendingRequests = MaintenanceRequest::whereIn('status', [
                'pending',
                'waiting_approval'
            ])->count();

            $inProgressRequests = MaintenanceRequest::where('status', 'in_progress')->count();

            // Completed includes both completed and confirmed
            $completedRequests = MaintenanceRequest::whereIn('status', [
                'completed',
                'confirmed'
            ])->count();

            $assignedToMe = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();

            $issueTypes = IssueType::count();

            $recentRequests = MaintenanceRequest::with([
                'user',
                'items.issueType',
                'assignedTechnicians.technician'
            ])->latest()->limit(5)->get();

            $issueTypeStats = IssueType::withTrashed()
                ->select('issue_types.*')
                ->selectSub(
                    MaintenanceRequestItem::whereColumn('issue_type_id', 'issue_types.id')
                        ->selectRaw('COUNT(*)'),
                    'maintenance_requests_count'
                )
                ->get();

            $itemAnalysis = MaintenanceRequestItem::select('item_id', DB::raw('COUNT(*) as total'))
                ->groupBy('item_id')
                ->with('item')
                ->orderByDesc('total')
                ->take(5)
                ->get()
                ->map(function ($requestItem) {
                    return [
                        'name' => $requestItem->item?->name ?? 'N/A',
                        'count' => $requestItem->total
                    ];
                });

            $issueTypeAnalysis = MaintenanceRequestItem::select('issue_type_id', DB::raw('COUNT(*) as total'))
                ->groupBy('issue_type_id')
                ->with('issueType')
                ->orderByDesc('total')
                ->take(5)
                ->get()
                ->map(function ($requestItem) {
                    return [
                        'name' => $requestItem->issueType?->name ?? 'N/A',
                        'count' => $requestItem->total
                    ];
                });
        } elseif ($user->isDivisionChairman() || $user->isClusterChairman()) {
            // Approver view
            $baseQuery = MaintenanceRequest::whereHas('user', function ($query) use ($user) {
                if ($user->isDivisionChairman()) {
                    $query->where('division_id', $user->division_id);
                } elseif ($user->isClusterChairman()) {
                    $query->where('cluster_id', $user->cluster_id);
                }
            });

            $totalRequests = (clone $baseQuery)->count();

            $pendingRequests = (clone $baseQuery)->whereIn('status', [
                'pending',
                'waiting_approval'
            ])->count();

            $inProgressRequests = (clone $baseQuery)->where('status', 'in_progress')->count();

            $completedRequests = (clone $baseQuery)->whereIn('status', [
                'completed',
                'confirmed'
            ])->count();

            $assignedToMe = 0; // Chairmen don't have assigned requests

            $recentRequests = (clone $baseQuery)
                ->with([
                    'user',
                    'items.issueType',
                    'assignedTechnicians.technician'
                ])
                ->latest()
                ->limit(3)
                ->get();
        } else if ($user->can('maintenance_requests.resolve')) {
            // Technician view
            $assignedRequestIds = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->pluck('maintenance_request_id');

            $totalRequests = $assignedRequestIds->count();
            $pendingRequests = MaintenanceRequestTechnician::where('user_id', $user->id)->where('status', 'assigned')->count();
            $inProgressRequests = MaintenanceRequestTechnician::where('user_id', $user->id)->where('status', 'in_progress')->count();
            $completedRequests = MaintenanceRequestTechnician::where('user_id', $user->id)->where('status', 'completed')->count();
            $assignedToMe = MaintenanceRequestTechnician::where('user_id', $user->id)->whereIn('status', ['assigned', 'in_progress'])->count();

            $recentRequests = MaintenanceRequest::whereIn('id', $assignedRequestIds)
                ->with(['user', 'items.issueType', 'assignedTechnicians' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)->with('technician');
                }])
                ->latest()
                ->limit(5)
                ->get();
        } else {
            // Regular user view
            $totalRequests = MaintenanceRequest::where('user_id', $user->id)->count();
            $pendingRequests = MaintenanceRequest::where('user_id', $user->id)->where('status', 'pending')->count();
            $inProgressRequests = MaintenanceRequest::where('user_id', $user->id)->whereIn('status', ['assigned', 'in_progress'])->count();
            $completedRequests = MaintenanceRequest::where('user_id', $user->id)->whereIn('status', ['completed', 'confirmed'])->count();
            $assignedToMe = MaintenanceRequestTechnician::whereHas('maintenanceRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            $recentRequests = MaintenanceRequest::with(['items.issueType', 'assignedTechnicians.technician'])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        // Monthly statistics data (for backward compatibility)
        $monthlyStats = $this->getMonthlyStatistics($user);

        // Priority distribution
        $priorityStats = $this->getPriorityStatistics($user);

        // Response time metrics
        $responseMetrics = $this->getResponseTimeMetrics($user);

        return view('pages.dashboard.ecommerce', compact(
            'totalRequests',
            'pendingRequests',
            'inProgressRequests',
            'completedRequests',
            'assignedToMe',
            'recentRequests',
            'monthlyStats',
            'priorityStats',
            'responseMetrics',
            'issueTypeStats',
            'issueTypes',
            'issueTypeAnalysis',
            'itemAnalysis'
        ));
    }

    private function getMonthlyStatistics($user)
    {
        $currentYear = Carbon::now()->year;

        // Build base query based on user role
        $query = MaintenanceRequest::query();

        if ($user->can('maintenance_requests.assign')) {
            // Admin/ICT Director: see all requests
            // No additional filter needed
        } elseif ($user->isDivisionChairman()) {
            // Division Chairman: see requests from their division
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('division_id', $user->division_id);
            });
        } elseif ($user->isClusterChairman()) {
            // Cluster Chairman: see requests from their cluster
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('cluster_id', $user->cluster_id);
            });
        } elseif ($user->can('maintenance_requests.resolve')) {
            // Technician: see only assigned requests
            $query->whereHas('assignedTechnicians', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            // Regular user: see only their own requests
            $query->where('user_id', $user->id);
        }

        $monthlyData = (clone $query)->selectRaw('
            MONTH(requested_at) as month,
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
            AVG(TIMESTAMPDIFF(HOUR, requested_at, assigned_at)) as avg_response_time,
            AVG(TIMESTAMPDIFF(HOUR, assigned_at, completed_at)) as avg_resolution_time
        ', [
            MaintenanceRequest::STATUS_COMPLETED,
            MaintenanceRequest::STATUS_PENDING
        ])
            ->whereYear('requested_at', $currentYear)
            ->whereNotNull('requested_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $monthlyData;
    }

    private function getPriorityStatistics($user)
    {
        // Build base query based on user role
        $query = MaintenanceRequest::query();

        if ($user->can('maintenance_requests.assign')) {
            // Admin/ICT Director: see all requests
            // No additional filter needed
        } elseif ($user->isDivisionChairman()) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('division_id', $user->division_id);
            });
        } elseif ($user->isClusterChairman()) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('cluster_id', $user->cluster_id);
            });
        } elseif ($user->can('maintenance_requests.resolve')) {
            $query->whereHas('assignedTechnicians', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            $query->where('user_id', $user->id);
        }

        // Get total count for percentage calculation
        $totalCount = (clone $query)->count();

        if ($totalCount === 0) {
            return collect();
        }

        return (clone $query)->selectRaw('
            priority,
            COUNT(*) as count,
            ROUND((COUNT(*) * 100.0 / ?), 2) as percentage
        ', [$totalCount])
            ->groupBy('priority')
            ->orderByRaw("FIELD(priority, 'emergency', 'high', 'medium', 'low')")
            ->get();
    }

    private function getResponseTimeMetrics($user)
    {
        // Build base query based on user role
        $query = MaintenanceRequest::query();

        if ($user->can('maintenance_requests.assign')) {
            // Admin/ICT Director: see all requests
        } elseif ($user->isDivisionChairman()) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('division_id', $user->division_id);
            });
        } elseif ($user->isClusterChairman()) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('cluster_id', $user->cluster_id);
            });
        } elseif ($user->can('maintenance_requests.resolve')) {
            $query->whereHas('assignedTechnicians', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            $query->where('user_id', $user->id);
        }

        return [
            'avg_response_time' => (clone $query)
                ->whereNotNull('assigned_at')
                ->whereNotNull('requested_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, requested_at, assigned_at)')),

            'avg_resolution_time' => (clone $query)
                ->where('status', MaintenanceRequest::STATUS_COMPLETED)
                ->whereNotNull('completed_at')
                ->whereNotNull('assigned_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, assigned_at, completed_at)')),

            'total_open' => (clone $query)
                ->whereIn('status', [
                    MaintenanceRequest::STATUS_PENDING,
                    MaintenanceRequest::STATUS_ASSIGNED,
                    MaintenanceRequest::STATUS_IN_PROGRESS
                ])->count(),
        ];
    }

    private function getMonthlyUserStatistics()
    {
        $currentYear = Carbon::now()->year;

        return User::selectRaw('
            MONTH(created_at) as month,
            COUNT(*) as total_users,
            SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as active_users,
            SUM(CASE WHEN email_verified_at IS NULL THEN 1 ELSE 0 END) as inactive_users
        ')
            ->whereYear('created_at', $currentYear)
            ->whereNotNull('created_at')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}

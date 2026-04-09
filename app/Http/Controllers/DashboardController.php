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

        // Metrics based on user role
        if ($user->can('maintenance_requests.assign')) {
            // Admin/ICT Director view
            $totalRequests = MaintenanceRequest::count();
            $pendingRequests = MaintenanceRequest::whereIn('status', [
                MaintenanceRequest::STATUS_PENDING,
                MaintenanceRequest::STATUS_WAITING_APPROVAL
            ])->count();
            $inProgressRequests = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_IN_PROGRESS)->count();
            $completedRequests = MaintenanceRequest::whereIn('status', [
                MaintenanceRequest::STATUS_COMPLETED,
                MaintenanceRequest::STATUS_CONFIRMED,
            ])->count();

            // Updated: Count requests where user is assigned as a technician through the pivot table
            $assignedToMe = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();

            $issueTypes = IssueType::count();

            // Recent requests for admin with technician assignments
            $recentRequests = MaintenanceRequest::with([
                'user',
                'items.issueType',
                'assignedTechnicians.technician'  // Updated to load all assigned technicians
            ])
                ->latest()
                ->limit(5)
                ->get();

            $issueTypeStats = IssueType::withTrashed()
                ->select('issue_types.*')
                ->selectSub(
                    MaintenanceRequestItem::whereColumn('issue_type_id', 'issue_types.id')
                        ->selectRaw('COUNT(*)'),
                    'maintenance_requests_count'
                )
                ->get();

            // Updated: Item analysis using the pivot table
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

            // Updated: Issue Type analysis using the pivot table
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
            // Approver view - queries remain the same as they don't depend on assigned_to
            $totalRequests = MaintenanceRequest::whereHas('user', function ($query) use ($user) {
                if ($user->isDivisionChairman()) {
                    $query->where('division_id', $user->division_id);
                } elseif ($user->isClusterChairman()) {
                    $query->where('cluster_id', $user->cluster_id);
                }
            })->count();

            $pendingRequests = MaintenanceRequest::whereIn('status', [
                MaintenanceRequest::STATUS_PENDING,
                MaintenanceRequest::STATUS_WAITING_APPROVAL
            ])
                ->whereHas('user', function ($query) use ($user) {
                    if ($user->isDivisionChairman()) {
                        $query->where('division_id', $user->division_id);
                    } elseif ($user->isClusterChairman()) {
                        $query->where('cluster_id', $user->cluster_id);
                    }
                })
                ->count();

            $completedRequests = MaintenanceRequest::whereIn('status', [
                MaintenanceRequest::STATUS_COMPLETED,
                MaintenanceRequest::STATUS_CONFIRMED,
            ])
                ->whereHas('user', function ($query) use ($user) {
                    if ($user->isDivisionChairman()) {
                        $query->where('division_id', $user->division_id);
                    } elseif ($user->isClusterChairman()) {
                        $query->where('cluster_id', $user->cluster_id);
                    }
                })
                ->count();

            $recentRequests = MaintenanceRequest::with([
                'user',
                'items.issueType',
                'assignedTechnicians.technician'  // Updated to load all assigned technicians
            ])
                ->whereHas('user', function ($query) use ($user) {
                    if ($user->isDivisionChairman()) {
                        $query->where('division_id', $user->division_id);
                    } elseif ($user->isClusterChairman()) {
                        $query->where('cluster_id', $user->cluster_id);
                    }
                })
                ->latest()
                ->limit(3)
                ->get();
        } else if ($user->can('maintenance_requests.resolve')) {
            // Technician view - updated to use the pivot table

            // Get all request IDs where this user is assigned as a technician
            $assignedRequestIds = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->pluck('maintenance_request_id');

            // Total requests assigned to this technician
            $totalRequests = $assignedRequestIds->count();

            // Pending requests (assigned but not started)
            $pendingRequests = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->where('status', 'assigned')
                ->count();

            // In progress requests
            $inProgressRequests = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->count();

            // Completed requests (where technician completed their items)
            $completedRequests = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count();

            // Total active assignments count
            $assignedToMe = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->count();

            // Recent requests for technician with their assignments
            $recentRequests = MaintenanceRequest::whereIn('id', $assignedRequestIds)
                ->with([
                    'user',
                    'items.issueType',
                    'assignedTechnicians' => function ($query) use ($user) {
                        $query->where('user_id', $user->id)->with('technician');
                    }
                ])
                ->latest()
                ->limit(5)
                ->get();
        } else {
            // Regular user view - queries remain mostly the same
            $totalRequests = MaintenanceRequest::where('user_id', $user->id)->count();

            $pendingRequests = MaintenanceRequest::where('user_id', $user->id)
                ->where('status', MaintenanceRequest::STATUS_PENDING)
                ->count();

            $inProgressRequests = MaintenanceRequest::where('user_id', $user->id)
                ->whereIn('status', [
                    MaintenanceRequest::STATUS_ASSIGNED,
                    MaintenanceRequest::STATUS_IN_PROGRESS
                ])
                ->count();

            $completedRequests = MaintenanceRequest::where('user_id', $user->id)
                ->whereIn('status', [
                    MaintenanceRequest::STATUS_COMPLETED,
                    MaintenanceRequest::STATUS_CONFIRMED,
                ])
                ->count();

            // Assigned to me count for users (requests assigned to technicians working on their items)
            $assignedToMe = MaintenanceRequestTechnician::whereHas('maintenanceRequest', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count();

            // Recent requests for user with technician assignments
            $recentRequests = MaintenanceRequest::with([
                'items.issueType',
                'assignedTechnicians.technician'  // Show which technicians are assigned
            ])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }

        // Monthly statistics data (updated to filter by user role)
        $monthlyStats = $this->getMonthlyStatistics($user);

        // Priority distribution (updated to filter by user role)
        $priorityStats = $this->getPriorityStatistics($user);

        // Response time metrics (updated to filter by user role)
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

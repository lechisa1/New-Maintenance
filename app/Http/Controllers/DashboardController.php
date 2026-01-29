<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\IssueType;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Cluster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $inactiveUsers = User::whereNull('email_verified_at')->count();
        
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
        
        // Metrics based on user role
        if ($user->can('maintenance_requests.assign')) {
            // Admin/ICT Director view
            $totalRequests = MaintenanceRequest::count();
            $pendingRequests = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_PENDING)->count();
            $inProgressRequests = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_IN_PROGRESS)->count();
            $completedRequests = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_COMPLETED)->count();
            $assignedToMe = MaintenanceRequest::where('assigned_to', $user->id)->count();
            $issueTypes = IssueType::count();
            
            // Recent requests for admin
            $recentRequests = MaintenanceRequest::with(['user', 'issueType', 'assignedTechnician'])
                ->latest()
                ->limit(5)
                ->get();
                
            $issueTypeStats = IssueType::withTrashed()
                ->withCount([
                    'maintenanceRequests' => function ($q) {
                        $q->withTrashed();
                    }
                ])
                ->get();
                
        } elseif ($user->isDivisionChairman() || $user->isClusterChairman()) {
            // Approver view
            $totalRequests = MaintenanceRequest::whereHas('user', function($query) use ($user) {
                if ($user->isDivisionChairman()) {
                    $query->where('division_id', $user->division_id);
                } elseif ($user->isClusterChairman()) {
                    $query->where('cluster_id', $user->cluster_id);
                }
            })->count();
            
            $pendingRequests = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_WAITING_APPROVAL)
                ->whereHas('user', function($query) use ($user) {
                    if ($user->isDivisionChairman()) {
                        $query->where('division_id', $user->division_id);
                    } elseif ($user->isClusterChairman()) {
                        $query->where('cluster_id', $user->cluster_id);
                    }
                })
                ->count();
                
            $completedRequests = MaintenanceRequest::where('status', MaintenanceRequest::STATUS_COMPLETED)
                ->whereHas('user', function($query) use ($user) {
                    if ($user->isDivisionChairman()) {
                        $query->where('division_id', $user->division_id);
                    } elseif ($user->isClusterChairman()) {
                        $query->where('cluster_id', $user->cluster_id);
                    }
                })
                ->count();
                
            $recentRequests = MaintenanceRequest::with(['user', 'issueType', 'assignedTechnician'])
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
                
        } else {
            // Regular user view
            $totalRequests = MaintenanceRequest::where('user_id', $user->id)->count();
            $pendingRequests = MaintenanceRequest::where('user_id', $user->id)
                ->where('status', MaintenanceRequest::STATUS_PENDING)
                ->count();
            $inProgressRequests = MaintenanceRequest::where('user_id', $user->id)
                ->whereIn('status', [MaintenanceRequest::STATUS_ASSIGNED, MaintenanceRequest::STATUS_IN_PROGRESS])
                ->count();
            $completedRequests = MaintenanceRequest::where('user_id', $user->id)
                ->where('status', MaintenanceRequest::STATUS_COMPLETED)
                ->count();
            
            // Recent requests for user
            $recentRequests = MaintenanceRequest::with(['issueType', 'assignedTechnician'])
                ->where('user_id', $user->id)
                ->latest()
                ->limit(5)
                ->get();
        }
        
        // Monthly statistics data
        $monthlyStats = $this->getMonthlyStatistics();
        
        // Priority distribution
        $priorityStats = $this->getPriorityStatistics();
        
        // Response time metrics
        $responseMetrics = $this->getResponseTimeMetrics();

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
            'issueTypes'
        ));
    }
    
    private function getMonthlyStatistics()
    {
        $currentYear = Carbon::now()->year;
        
        $monthlyData = MaintenanceRequest::selectRaw('
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
    
    private function getPriorityStatistics()
    {
        return MaintenanceRequest::selectRaw('
            priority,
            COUNT(*) as count,
            ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM maintenance_requests)), 2) as percentage
        ')
        ->groupBy('priority')
        ->orderByRaw("FIELD(priority, 'emergency', 'high', 'medium', 'low')")
        ->get();
    }
    
    private function getResponseTimeMetrics()
    {
        return [
            'avg_response_time' => MaintenanceRequest::whereNotNull('assigned_at')
                ->whereNotNull('requested_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, requested_at, assigned_at)')),
            
            'avg_resolution_time' => MaintenanceRequest::where('status', MaintenanceRequest::STATUS_COMPLETED)
                ->whereNotNull('completed_at')
                ->whereNotNull('assigned_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, assigned_at, completed_at)')),
            
            'total_open' => MaintenanceRequest::whereIn('status', [
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
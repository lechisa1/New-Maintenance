<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequestTechnician;
use App\Models\User;
use App\Models\WorkLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class TechnicianReportController extends Controller
{
    /**
     * Display reports page for assigners (those with assign permission)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check if user has permission to view reports
        if (!$user->can('maintenance_requests.assign')) {
            abort(403, 'You do not have permission to view technician reports.');
        }

        // Get technicians for dropdown
        $technicians = User::whereHas('permissions', function ($query) {
            $query->where('name', 'maintenance_requests.resolve');
        })
            ->orWhereHas('roles.permissions', function ($query) {
                $query->where('name', 'maintenance_requests.resolve');
            })
            ->select('id', 'full_name', 'email')
            ->orderBy('full_name')
            ->get();

        // Build query for assignments with filters
        $query = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'technician',
            'maintenanceRequest.items.item'
        ]);

        // Filter by technician
        if ($technicianId = $request->technician_id) {
            $query->where('user_id', $technicianId);
        }

        // Filter by date range
        if ($startDate = $request->start_date) {
            $query->whereDate('assigned_at', '>=', $startDate);
        }
        if ($endDate = $request->end_date) {
            $query->whereDate('assigned_at', '<=', $endDate);
        }

        // Filter by status
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // Order by assigned date
        $assignments = $query->orderBy('assigned_at', 'desc')->paginate(15);

        // Calculate statistics
        $statsQuery = MaintenanceRequestTechnician::query();
        if ($technicianId) {
            $statsQuery->where('user_id', $technicianId);
        }
        if ($startDate) {
            $statsQuery->whereDate('assigned_at', '>=', $startDate);
        }
        if ($endDate) {
            $statsQuery->whereDate('assigned_at', '<=', $endDate);
        }

        $totalAssigned = (clone $statsQuery)->count();
        $completed = (clone $statsQuery)->where('status', 'completed')->count();
        $inProgress = (clone $statsQuery)->where('status', 'in_progress')->count();
        $pending = (clone $statsQuery)->where('status', 'assigned')->count();

        // Group by technician if no specific technician selected
        $technicianSummary = [];
        if (!$technicianId) {
            $technicianSummary = MaintenanceRequestTechnician::select('user_id')
                ->selectRaw('COUNT(*) as total_assigned')
                ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
                ->selectRaw('SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress')
                ->selectRaw('SUM(CASE WHEN status = "assigned" THEN 1 ELSE 0 END) as pending')
                ->when($startDate, fn($q) => $q->whereDate('assigned_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('assigned_at', '<=', $endDate))
                ->groupBy('user_id')
                ->with('technician:id,full_name,email')
                ->get();
        }

        return view('reports.technician.index', compact(
            'assignments',
            'technicians',
            'statsQuery',
            'totalAssigned',
            'completed',
            'inProgress',
            'pending',
            'technicianSummary'
        ));
    }

    /**
     * Download report as PDF
     */
    public function downloadReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('maintenance_requests.assign')) {
            abort(403, 'You do not have permission to download reports.');
        }

        // Build query
        $query = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'technician',
            'maintenanceRequest.items.item'
        ]);

        if ($technicianId = $request->technician_id) {
            $query->where('user_id', $technicianId);
        }
        if ($startDate = $request->start_date) {
            $query->whereDate('assigned_at', '>=', $startDate);
        }
        if ($endDate = $request->end_date) {
            $query->whereDate('assigned_at', '<=', $endDate);
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $assignments = $query->orderBy('assigned_at', 'desc')->get();

        // Calculate stats
        $totalAssigned = $assignments->count();
        $completed = $assignments->where('status', 'completed')->count();
        $inProgress = $assignments->where('status', 'in_progress')->count();
        $pending = $assignments->where('status', 'assigned')->count();

        $technicianName = 'All Technicians';
        if ($technicianId) {
            $technician = User::find($technicianId);
            $technicianName = $technician ? $technician->full_name : 'Unknown';
        }

        $pdf = Pdf::loadView('reports.technician.pdf', compact(
            'assignments',
            'technicianName',
            'totalAssigned',
            'completed',
            'inProgress',
            'pending',
            'startDate',
            'endDate'
        ));

        $filename = 'technician_report_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export as Excel (CSV format for simplicity)
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('maintenance_requests.assign')) {
            abort(403, 'You do not have permission to export reports.');
        }

        $query = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'technician',
            'maintenanceRequest.items.item'
        ]);

        if ($technicianId = $request->technician_id) {
            $query->where('user_id', $technicianId);
        }
        if ($startDate = $request->start_date) {
            $query->whereDate('assigned_at', '>=', $startDate);
        }
        if ($endDate = $request->end_date) {
            $query->whereDate('assigned_at', '<=', $endDate);
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $assignments = $query->orderBy('assigned_at', 'desc')->get();

        $csvContent = $this->generateCSV($assignments);

        $filename = 'technician_report_' . Carbon::now()->format('Ymd_His') . '.csv';
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function generateCSV($assignments)
    {
        $output = fopen('php://temp', 'r+');

        // Header
        fputcsv($output, [
            'Ticket Number',
            'Technician',
            'Technician Email',
            'Request Description',
            'Requester',
            'Status',
            'Assigned Date',
            'Started Date',
            'Completed Date',
            'Items Count',
            'Notes'
        ]);

        foreach ($assignments as $assignment) {
            fputcsv($output, [
                $assignment->maintenanceRequest->ticket_number ?? 'N/A',
                $assignment->technician->full_name ?? 'N/A',
                $assignment->technician->email ?? 'N/A',
                substr($assignment->maintenanceRequest->description ?? '', 0, 100),
                $assignment->maintenanceRequest->user->full_name ?? 'N/A',
                ucfirst($assignment->status),
                $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i') : 'N/A',
                $assignment->started_at ? $assignment->started_at->format('Y-m-d H:i') : 'N/A',
                $assignment->completed_at ? $assignment->completed_at->format('Y-m-d H:i') : 'N/A',
                count($assignment->item_ids ?? []),
                $assignment->notes ?? ''
            ]);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }

    /**
     * Technicians view their own completed tasks
     */
    public function myReports(Request $request)
    {
        $user = Auth::user();

        // Check if user is a technician
        if (!$user->can('maintenance_requests.resolve')) {
            abort(403, 'You do not have permission to view your reports.');
        }

        $period = $request->period ?? 'week'; // week, month, or custom

        // Determine date range based on period
        if ($period === 'month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } else {
            // Custom range
            $startDate = $request->filled('start_date')
                ? Carbon::parse($request->start_date)->startOfDay()
                : Carbon::now()->subDays(30)->startOfDay();
            $endDate = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::now()->endOfDay();
        }

        // Get user's completed assignments - FIXED: Specify table names
        $query = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'maintenanceRequest.items.item'
        ])
            ->where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed');

        // Filter by date range
        $completedAssignments = $query
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->orderBy('maintenance_request_technicians.completed_at', 'desc')
            ->paginate(15);

        // Calculate statistics for the period - FIXED: Specify table names
        $totalCompleted = MaintenanceRequestTechnician::where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->count();

        // Calculate average completion time - FIXED: Specify table for all columns
        $avgCompletionTime = MaintenanceRequestTechnician::where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, maintenance_request_technicians.assigned_at, maintenance_request_technicians.completed_at)) as avg_time')
            ->value('avg_time');

        // Get weekly summary for chart - FIXED: Specify table names
        $weeklySummary = [];
        if ($period === 'week' || $period === 'custom') {
            $weeklySummary = MaintenanceRequestTechnician::where('maintenance_request_technicians.user_id', $user->id)
                ->where('maintenance_request_technicians.status', 'completed')
                ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
                ->selectRaw('DATE(maintenance_request_technicians.completed_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        }

        // Monthly summary - FIXED: Specify table names
        $monthlySummary = MaintenanceRequestTechnician::where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->selectRaw('YEAR(maintenance_request_technicians.completed_at) as year, MONTH(maintenance_request_technicians.completed_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('reports.my.index', compact(
            'completedAssignments',
            'totalCompleted',
            'avgCompletionTime',
            'weeklySummary',
            'monthlySummary',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Download technician's own completed tasks as PDF
     */
    /**
     * Download technician's own completed tasks as PDF with custom date range
     */
    /**
     * Download technician's own completed tasks as PDF with custom date range
     */
    /**
     * Download technician's own completed tasks as PDF with custom date range
     */
    public function downloadMyReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('maintenance_requests.resolve')) {
            abort(403, 'You do not have permission to download your report.');
        }

        // Get date range from request
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfWeek();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfWeek();

        $period = $request->period ?? 'week';

        // If custom period but no dates provided, use last 30 days
        if ($period === 'custom' && (!$request->filled('start_date') || !$request->filled('end_date'))) {
            $startDate = Carbon::now()->subDays(30)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        // Get completed assignments within date range
        $completedAssignments = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'maintenanceRequest.items.item',
            'maintenanceRequest.items.issueType'
        ])
            ->where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->orderBy('maintenance_request_technicians.completed_at', 'desc')
            ->get();

        $totalCompleted = $completedAssignments->count();

        // Calculate average completion time - FIXED: Specify table for all columns
        $avgCompletionTime = MaintenanceRequestTechnician::where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, maintenance_request_technicians.assigned_at, maintenance_request_technicians.completed_at)) as avg_time')
            ->value('avg_time');

        // Group by priority - FIXED: Specify table for all columns
        $priorityStats = MaintenanceRequestTechnician::where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->join('maintenance_requests', 'maintenance_request_technicians.maintenance_request_id', '=', 'maintenance_requests.id')
            ->selectRaw('maintenance_requests.priority, COUNT(*) as count')
            ->groupBy('maintenance_requests.priority')
            ->get();

        $pdf = Pdf::loadView('reports.my.pdf', compact(
            'completedAssignments',
            'totalCompleted',
            'avgCompletionTime',
            'priorityStats',
            'period',
            'startDate',
            'endDate',
            'user'
        ));

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        $filename = 'my_completed_tasks_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export technician's own completed tasks as CSV with custom date range
     */
    public function exportMyReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('maintenance_requests.resolve')) {
            abort(403, 'You do not have permission to export your report.');
        }

        // Get date range from request
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfWeek();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfWeek();

        $period = $request->period ?? 'week';

        // If custom period but no dates provided, use last 30 days
        if ($period === 'custom' && (!$request->filled('start_date') || !$request->filled('end_date'))) {
            $startDate = Carbon::now()->subDays(30)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        }

        $completedAssignments = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'maintenanceRequest.items.item',
            'maintenanceRequest.items.issueType'
        ])
            ->where('maintenance_request_technicians.user_id', $user->id)
            ->where('maintenance_request_technicians.status', 'completed')
            ->whereBetween('maintenance_request_technicians.completed_at', [$startDate, $endDate])
            ->orderBy('maintenance_request_technicians.completed_at', 'desc')
            ->get();

        $output = fopen('php://temp', 'r+');

        // Add UTF-8 BOM for Excel compatibility
        fwrite($output, "\xEF\xBB\xBF");

        // CSV Headers
        fputcsv($output, [
            'Ticket Number',
            'Request Description',
            'Priority',
            'Requester',
            'Requester Division',
            'Requester Cluster',
            'Assigned Date',
            'Completed Date',
            'Items Worked On',
            'Issue Types',
            'Time to Complete (Hours)',
            'Notes'
        ]);

        foreach ($completedAssignments as $assignment) {
            $items = $assignment->maintenanceRequest->items->pluck('item.name')->implode(', ');
            $issueTypes = $assignment->maintenanceRequest->items->pluck('issueType.name')->implode(', ');

            // Calculate completion time in hours
            $completionTime = null;
            if ($assignment->assigned_at && $assignment->completed_at) {
                $completionTime = round($assignment->assigned_at->diffInHours($assignment->completed_at), 1);
            }

            fputcsv($output, [
                $assignment->maintenanceRequest->ticket_number ?? 'N/A',
                substr($assignment->maintenanceRequest->description ?? '', 0, 200),
                $assignment->maintenanceRequest->priority ?? 'N/A',
                $assignment->maintenanceRequest->user->full_name ?? 'N/A',
                $assignment->maintenanceRequest->user->division->name ?? 'N/A',
                $assignment->maintenanceRequest->user->cluster->name ?? 'N/A',
                $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : 'N/A',
                $assignment->completed_at ? $assignment->completed_at->format('Y-m-d H:i:s') : 'N/A',
                $items,
                $issueTypes,
                $completionTime,
                $assignment->notes ?? ''
            ]);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        $filename = 'my_completed_tasks_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.csv';
        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}

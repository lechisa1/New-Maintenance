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

        $period = $request->period ?? 'week'; // week or month

        // Determine date range based on period
        if ($period === 'month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } else {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        // Get user's completed assignments
        $query = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'maintenanceRequest.items.item'
        ])
            ->where('user_id', $user->id)
            ->where('status', 'completed');

        // Filter by date range if provided
        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $period = 'custom';
        }

        $completedAssignments = $query
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->orderBy('completed_at', 'desc')
            ->paginate(15);

        // Calculate statistics for the period
        $totalCompleted = MaintenanceRequestTechnician::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->count();

        // Calculate average completion time
        $avgCompletionTime = MaintenanceRequestTechnician::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assigned_at, completed_at)) as avg_time')
            ->value('avg_time');

        // Get weekly summary for chart
        $weeklySummary = [];
        if ($period === 'week' || $period === 'custom') {
            $weeklySummary = MaintenanceRequestTechnician::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('count', 'date')
                ->toArray();
        }

        // Monthly summary
        $monthlySummary = MaintenanceRequestTechnician::where('user_id', $user->id)
            ->where('status', 'completed')
            ->selectRaw('YEAR(completed_at) as year, MONTH(completed_at) as month, COUNT(*) as count')
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
    public function downloadMyReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('maintenance_requests.resolve')) {
            abort(403, 'You do not have permission to download your report.');
        }

        $period = $request->period ?? 'week';

        if ($period === 'month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } else {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }

        $completedAssignments = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'maintenanceRequest.items.item'
        ])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->orderBy('completed_at', 'desc')
            ->get();

        $totalCompleted = $completedAssignments->count();

        $pdf = Pdf::loadView('reports.my.pdf', compact(
            'completedAssignments',
            'totalCompleted',
            'period',
            'startDate',
            'endDate',
            'user'
        ));

        $filename = 'my_completed_tasks_' . $period . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export technician's own completed tasks as CSV
     */
    public function exportMyReport(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('maintenance_requests.resolve')) {
            abort(403, 'You do not have permission to export your report.');
        }

        $period = $request->period ?? 'week';

        if ($period === 'month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } else {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }

        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        }

        $completedAssignments = MaintenanceRequestTechnician::with([
            'maintenanceRequest.user',
            'maintenanceRequest.items.item'
        ])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->orderBy('completed_at', 'desc')
            ->get();

        $output = fopen('php://temp', 'r+');

        fputcsv($output, [
            'Ticket Number',
            'Request Description',
            'Requester',
            'Completed Date',
            'Items Worked On',
            'Notes'
        ]);

        foreach ($completedAssignments as $assignment) {
            $items = $assignment->maintenanceRequest->items->pluck('item.name')->implode(', ');

            fputcsv($output, [
                $assignment->maintenanceRequest->ticket_number ?? 'N/A',
                substr($assignment->maintenanceRequest->description ?? '', 0, 100),
                $assignment->maintenanceRequest->user->full_name ?? 'N/A',
                $assignment->completed_at ? $assignment->completed_at->format('Y-m-d H:i') : 'N/A',
                $items,
                $assignment->notes ?? ''
            ]);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        $filename = 'my_completed_tasks_' . $period . '_' . Carbon::now()->format('Ymd_His') . '.csv';
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}

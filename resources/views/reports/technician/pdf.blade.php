<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Technician Activity Report</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            background: #ffffff;
            padding: 20px;
        }

        /* Typography */
        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        /* Header with Logo */
        .header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #10b981;
            position: relative;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            position: relative;
        }

        .logo-container {
            flex-shrink: 0;
        }

        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .title-container {
            text-align: center;
        }

        .header h1 {
            color: #059669;
            margin-bottom: 8px;
            font-size: 24px;
            font-weight: 600;
        }

        .header p {
            font-size: 11px;
            color: #6b7280;
            margin: 0;
        }

        /* Alternative: Logo on Left with Left Alignment */
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #10b981;
        }

        .logo-left {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .header-text {
            flex: 1;
        }

        .header-text h1 {
            color: #059669;
            margin-bottom: 5px;
            font-size: 24px;
            font-weight: 600;
        }

        .header-text p {
            font-size: 11px;
            color: #6b7280;
        }

        /* Alternative: Logo on Left with Title Centered */
        .header-centered {
            position: relative;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #10b981;
            text-align: center;
        }

        .logo-centered-left {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .header-centered h1 {
            color: #059669;
            margin-bottom: 8px;
            font-size: 24px;
            font-weight: 600;
        }

        .header-centered p {
            font-size: 11px;
            color: #6b7280;
        }

        /* Company Info */
        .company-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background: #f9fafb;
            border-radius: 6px;
        }

        .company-name {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .company-details {
            font-size: 9px;
            color: #6b7280;
        }

        /* Meta Information Card */
        .meta-card {
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 24px;
            padding: 16px;
            border: 1px solid #e5e7eb;
        }

        .meta-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .meta-item {
            flex: 1;
            min-width: 150px;
        }

        .meta-label {
            font-size: 9px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 11px;
            font-weight: 500;
            color: #111827;
        }

        /* Statistics Grid */
        .stats-container {
            margin-bottom: 28px;
        }

        .stats-grid {
            display: flex;
            gap: 16px;
            margin-top: 12px;
        }

        .stat-card {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            transition: all 0.2s;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 9px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-total .stat-value {
            color: #3b82f6;
        }

        .stat-completed .stat-value {
            color: #10b981;
        }

        .stat-progress .stat-value {
            color: #f59e0b;
        }

        .stat-pending .stat-value {
            color: #ef4444;
        }

        /* Table Styles */
        .table-container {
            margin-bottom: 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        thead {
            background: #f3f4f6;
            border-bottom: 2px solid #e5e7eb;
        }

        thead th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f9fafb;
        }

        td {
            padding: 10px 8px;
            vertical-align: top;
            color: #4b5563;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-assigned {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-in_progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-cancelled {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Priority Badges */
        .priority-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 600;
        }

        .priority-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .priority-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .priority-low {
            background: #d1fae5;
            color: #065f46;
        }

        /* Footer */
        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }

        .footer p {
            margin: 4px 0;
        }

        /* Print Optimizations */
        @media print {
            body {
                padding: 0;
            }

            .status-badge,
            .priority-badge {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            thead {
                display: table-header-group;
            }

            tbody tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        /* Utility Classes */
        .text-muted {
            color: #9ca3af;
        }

        .text-small {
            font-size: 8px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="{{ public_path('images/AII-logo.png') }}" alt="Company Logo" class="logo">
            </div>
            <div class="title-container">
                <h1>Technician Activity Report</h1>
                <p>Comprehensive performance and task completion summary</p>
            </div>
        </div>
    </div>
    <!-- Company Information -->
    <div class="company-info">
        <div class="company-name">Maintenance Management System</div>
        <div class="company-details">Enterprise Asset Management | Real-time Tracking | Performance Analytics</div>
    </div>

    <!-- Meta Information Card -->
    <div class="meta-card">
        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-label">Technician Information</div>
                <div class="meta-value">{{ $technicianName }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Report Period</div>
                <div class="meta-value">
                    @if ($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
                    @else
                        All Time
                    @endif
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Generated On</div>
                <div class="meta-value">{{ now()->format('F d, Y \a\t H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="stats-container">
        <h2>Performance Overview</h2>
        <div class="stats-grid">
            <div class="stat-card stat-total">
                <div class="stat-value">{{ $totalAssigned }}</div>
                <div class="stat-label">Total Assigned</div>
            </div>
            <div class="stat-card stat-completed">
                <div class="stat-value">{{ $completed }}</div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card stat-progress">
                <div class="stat-value">{{ $inProgress }}</div>
                <div class="stat-label">In Progress</div>
            </div>
            <div class="stat-card stat-pending">
                <div class="stat-value">{{ $pending }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>

    <!-- Detailed Assignments Table -->
    <div class="table-container">
        <h2>Task Details</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%">Ticket #</th>
                    <th style="width: 15%">Technician</th>
                    <th style="width: 25%">Request Description</th>
                    <th style="width: 15%">Requester</th>
                    <th style="width: 10%">Priority</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 13%">Assigned Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignments as $assignment)
                    <tr>
                        <td>
                            <strong>{{ $assignment->maintenanceRequest->ticket_number ?? 'N/A' }}</strong>
                        </td>
                        <td>{{ $assignment->technician->full_name ?? 'Unknown' }}</td>
                        <td>
                            {{ Str::limit($assignment->maintenanceRequest->description ?? 'N/A', 80) }}
                            @if ($assignment->maintenanceRequest && strlen($assignment->maintenanceRequest->description) > 80)
                                <span class="text-small text-muted">...</span>
                            @endif
                        </td>
                        <td>{{ $assignment->maintenanceRequest->user->full_name ?? 'N/A' }}</td>
                        <td>
                            @php
                                $priority = $assignment->maintenanceRequest->priority ?? 'low';
                                $priorityClass = match ($priority) {
                                    'high' => 'priority-high',
                                    'medium' => 'priority-medium',
                                    'low' => 'priority-low',
                                    default => 'priority-low',
                                };
                            @endphp
                            <span class="priority-badge {{ $priorityClass }}">
                                {{ ucfirst($priority) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $assignment->status }}">
                                {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                            </span>
                        </td>
                        <td>{{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>

                    @if ($assignment->completed_at)
                        <tr style="background-color: #f9fafb;">
                            <td colspan="7" style="padding: 4px 8px 8px 8px;">
                                <div class="text-small text-muted" style="display: flex; gap: 16px;">
                                    <span>✅ <strong>Completed:</strong>
                                        {{ $assignment->completed_at->format('M d, Y H:i') }}</span>
                                    @if ($assignment->notes)
                                        <span>📝 <strong>Notes:</strong>
                                            {{ Str::limit($assignment->notes, 100) }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <div class="text-muted">No assignments found for the selected criteria.</div>
                            <div class="text-small mt-2">Try adjusting the date range or check back later.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Performance Summary Notes -->
    @if ($assignments->count() > 0)
        <div class="meta-card" style="background: #fefce8; border-color: #fde047;">
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">Completion Rate</div>
                    <div class="meta-value">
                        @php
                            $completionRate = $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100) : 0;
                        @endphp
                        {{ $completionRate }}% ({{ $completed }}/{{ $totalAssigned }} tasks)
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Active Tasks</div>
                    <div class="meta-value">{{ $inProgress + $pending }} tasks currently in progress</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Report Reference</div>
                    <div class="meta-value">MMS-RPT-{{ now()->format('YmdHis') }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is a system-generated report. For inquiries, please contact the system administrator.</p>
        <p>Maintenance Management System &copy; {{ now()->format('Y') }} | All Rights Reserved</p>
        <p class="text-small text-muted mt-2">Report ID: {{ uniqid() }} | Generated:
            {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>

</html>

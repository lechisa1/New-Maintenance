<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>My Completed Tasks Report</title>
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
            background: #f0fdf4;
            border-radius: 6px;
            border: 1px solid #bbf7d0;
        }

        .company-name {
            font-size: 16px;
            font-weight: 600;
            color: #065f46;
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

        /* Statistics Section */
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
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 10px;
            font-weight: 600;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Performance Metrics */
        .performance-grid {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }

        .performance-card {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .performance-value {
            font-size: 20px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 4px;
        }

        .performance-label {
            font-size: 8px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
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
            background: #059669;
            border-bottom: 2px solid #047857;
        }

        thead th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            color: white;
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

        /* Item Tags */
        .item-tag {
            display: inline-block;
            background: #f3f4f6;
            color: #374151;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            margin: 2px 2px;
            white-space: nowrap;
        }

        .item-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        /* Completion Badge */
        .completion-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
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

            .stat-card,
            .completion-badge,
            .item-tag {
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

        .text-success {
            color: #059669;
        }

        .text-center {
            text-align: center;
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
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-container">
                <img src="{{ public_path('images/AII-logo.png') }}" alt="Company Logo" class="logo">
            </div>
            <div class="title-container">
                <h1>My Completed Tasks Report</h1>
                <p>Personal performance summary and task completion history</p>
            </div>
        </div>
    </div>

    <!-- Company Information -->
    <div class="company-info">
        <div class="company-name">Maintenance Management System</div>
        <div class="company-details">Individual Performance Report | Personal Activity Summary</div>
    </div>

    <!-- Meta Information Card -->
    <div class="meta-card">
        <div class="meta-grid">
            <div class="meta-item">
                <div class="meta-label">Technician Information</div>
                <div class="meta-value">{{ $user->full_name }}</div>
                <div class="meta-value text-muted" style="font-size: 9px; margin-top: 2px;">{{ $user->email }}</div>
                @if ($user->division)
                    <div class="meta-value text-muted" style="font-size: 9px;">{{ $user->division->name ?? '' }}</div>
                @endif
            </div>
            <div class="meta-item">
                <div class="meta-label">Report Period</div>
                <div class="meta-value">
                    {{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}
                </div>
                <div class="meta-value text-muted" style="font-size: 9px; margin-top: 2px;">
                    {{ ucfirst($period === 'custom' ? 'Custom Range' : $period) }}
                </div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Generated On</div>
                <div class="meta-value">{{ now()->format('F d, Y \a\t H:i:s') }}</div>
                <div class="meta-value text-muted" style="font-size: 9px; margin-top: 2px;">
                    Report ID: MMS-PR-{{ now()->format('YmdHis') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="stats-container">
        <h2>Performance Overview</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $totalCompleted }}</div>
                <div class="stat-label">Tasks Completed</div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    @if ($totalCompleted > 0)
        <div class="performance-grid">
            <div class="performance-card">
                <div class="performance-value">{{ $avgCompletionTime ? round($avgCompletionTime, 1) : 'N/A' }}</div>
                <div class="performance-label">Avg. Completion Time (Hours)</div>
            </div>
            <div class="performance-card">
                <div class="performance-value">
                    @php
                        $totalDays = $startDate->diffInDays($endDate) + 1;
                        $tasksPerDay = round($totalCompleted / max($totalDays, 1), 1);
                    @endphp
                    {{ $tasksPerDay }}
                </div>
                <div class="performance-label">Tasks Per Day</div>
            </div>
            <div class="performance-card">
                <div class="performance-value">
                    @php
                        $uniqueRequesters = $completedAssignments
                            ->pluck('maintenanceRequest.user_id')
                            ->unique()
                            ->count();
                    @endphp
                    {{ $uniqueRequesters }}
                </div>
                <div class="performance-label">Unique Requesters</div>
            </div>
        </div>
    @endif

    <!-- Completed Tasks Table -->
    <div class="table-container">
        <h2>Completed Tasks Details</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%">Ticket #</th>
                    <th style="width: 25%">Request Description</th>
                    <th style="width: 15%">Requester</th>
                    <th style="width: 20%">Items Worked On</th>
                    <th style="width: 13%">Completed Date</th>
                    <th style="width: 15%">Completion Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($completedAssignments as $assignment)
                    <tr>
                        <td>
                            <strong>{{ $assignment->maintenanceRequest->ticket_number ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            {{ Str::limit($assignment->maintenanceRequest->description ?? 'N/A', 70) }}
                            @if ($assignment->notes)
                                <div class="text-muted" style="font-size: 8px; margin-top: 4px;">
                                    📝 {{ Str::limit($assignment->notes, 60) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            {{ $assignment->maintenanceRequest->user->full_name ?? 'N/A' }}
                            @if ($assignment->maintenanceRequest->user->division)
                                <div class="text-muted" style="font-size: 8px;">
                                    {{ $assignment->maintenanceRequest->user->division->name ?? '' }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="item-list">
                                @foreach ($assignment->maintenanceRequest->items as $item)
                                    <span class="item-tag">
                                        {{ $item->item->name ?? 'N/A' }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <span class="completion-badge">
                                {{ $assignment->completed_at ? $assignment->completed_at->format('M d, Y') : 'N/A' }}
                            </span>
                            <div class="text-muted" style="font-size: 8px; margin-top: 2px;">
                                {{ $assignment->completed_at ? $assignment->completed_at->format('H:i') : '' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $completionHours = null;
                                if ($assignment->assigned_at && $assignment->completed_at) {
                                    $completionHours = round(
                                        $assignment->assigned_at->diffInHours($assignment->completed_at),
                                        1,
                                    );
                                }
                            @endphp
                            @if ($completionHours)
                                <span class="text-success">{{ $completionHours }} hrs</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <div class="text-muted">No completed tasks found for the selected period.</div>
                            <div class="text-muted" style="font-size: 8px; margin-top: 8px;">
                                Try selecting a different date range or complete some tasks first.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Performance Summary -->
    @if ($completedAssignments->count() > 0)
        <div class="meta-card" style="background: #f0fdf4; border-color: #bbf7d0;">
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-label">Most Active Day</div>
                    <div class="meta-value">
                        @php
                            $dailyCount = [];
                            foreach ($completedAssignments as $assignment) {
                                if ($assignment->completed_at) {
                                    $date = $assignment->completed_at->format('Y-m-d');
                                    $dailyCount[$date] = ($dailyCount[$date] ?? 0) + 1;
                                }
                            }
                            $maxDay = !empty($dailyCount) ? array_keys($dailyCount, max($dailyCount))[0] : null;
                            $maxCount = !empty($dailyCount) ? max($dailyCount) : 0;
                        @endphp
                        @if ($maxDay)
                            {{ \Carbon\Carbon::parse($maxDay)->format('F d, Y') }} ({{ $maxCount }} tasks)
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Total Work Items</div>
                    <div class="meta-value">
                        @php
                            $totalItems = 0;
                            foreach ($completedAssignments as $assignment) {
                                $totalItems += $assignment->maintenanceRequest->items->count();
                            }
                        @endphp
                        {{ $totalItems }} items serviced
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Achievement Rate</div>
                    <div class="meta-value">
                        @php
                            $achievementRate = 100;
                            if (isset($totalAssigned) && $totalAssigned > 0) {
                                $achievementRate = round(($totalCompleted / $totalAssigned) * 100);
                            }
                        @endphp
                        {{ $achievementRate }}% completion rate
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This is a system-generated personal performance report. For inquiries, please contact your supervisor.</p>
        <p>Maintenance Management System &copy; {{ now()->format('Y') }} | All Rights Reserved</p>
        <p class="text-muted" style="font-size: 7px; margin-top: 4px;">
            Report ID: MMS-PR-{{ now()->format('YmdHis') }} | Page 1 of 1
        </p>
    </div>
</body>

</html>

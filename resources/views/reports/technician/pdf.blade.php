<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Technician Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .meta-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 4px;
        }

        .meta-info table {
            width: 100%;
        }

        .meta-info td {
            padding: 3px 0;
        }

        .stats-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .stat-box {
            text-align: center;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            flex: 1;
            margin: 0 5px;
        }

        .stat-box:first-child {
            margin-left: 0;
        }

        .stat-box:last-child {
            margin-right: 0;
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
        }

        .stat-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        .stat-box.total .stat-value {
            color: #2563eb;
        }

        .stat-box.completed .stat-value {
            color: #16a34a;
        }

        .stat-box.in-progress .stat-value {
            color: #ca8a04;
        }

        .stat-box.pending .stat-value {
            color: #ea580c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #1e40af;
        }

        thead th {
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-assigned {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-in_progress {
            background: #fef9c3;
            color: #854d0e;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Technician Activity Report</h1>
        <p>Maintenance Management System</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td><strong>Technician:</strong></td>
                <td>{{ $technicianName }}</td>
                <td><strong>Report Generated:</strong></td>
                <td>{{ now()->format('F d, Y H:i:s') }}</td>
            </tr>
            @if ($startDate)
                <tr>
                    <td><strong>Start Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}</td>
                    <td><strong>End Date:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="stats-grid">
        <div class="stat-box total">
            <div class="stat-value">{{ $totalAssigned }}</div>
            <div class="stat-label">Total Assigned</div>
        </div>
        <div class="stat-box completed">
            <div class="stat-value">{{ $completed }}</div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-box in-progress">
            <div class="stat-value">{{ $inProgress }}</div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-box pending">
            <div class="stat-value">{{ $pending }}</div>
            <div class="stat-label">Pending</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Technician</th>
                <th>Request Description</th>
                <th>Requester</th>
                <th>Status</th>
                <th>Assigned</th>
                <th>Completed</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->maintenanceRequest->ticket_number ?? 'N/A' }}</td>
                    <td>{{ $assignment->technician->full_name ?? 'Unknown' }}</td>
                    <td>{{ Str::limit($assignment->maintenanceRequest->description ?? 'N/A', 60) }}</td>
                    <td>{{ $assignment->maintenanceRequest->user->full_name ?? 'N/A' }}</td>
                    <td>
                        <span class="status-badge status-{{ $assignment->status }}">
                            {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                        </span>
                    </td>
                    <td>{{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $assignment->completed_at ? $assignment->completed_at->format('M d, Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($assignments->count() === 0)
        <p style="text-align: center; padding: 30px; color: #64748b;">No assignments found for the selected criteria.
        </p>
    @endif

    <div class="footer">
        <p>This is an automatically generated report. For more details, please contact the system administrator.</p>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>My Completed Tasks Report</title>
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
            border-bottom: 2px solid #16a34a;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            color: #15803d;
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
            color: #16a34a;
        }

        .stat-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background: #15803d;
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

        td {
            padding: 6px 5px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>My Completed Tasks Report</h1>
        <p>Maintenance Management System - Personal Activity Report</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td><strong>Technician:</strong></td>
                <td>{{ $user->full_name }}</td>
                <td><strong>Email:</strong></td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td><strong>Period:</strong></td>
                <td class="capitalize">{{ $period === 'custom' ? 'Custom Range' : $period }}</td>
                <td><strong>Report Generated:</strong></td>
                <td>{{ now()->format('F d, Y H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>Start Date:</strong></td>
                <td>{{ $startDate->format('F d, Y') }}</td>
                <td><strong>End Date:</strong></td>
                <td>{{ $endDate->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value">{{ $totalCompleted }}</div>
            <div class="stat-label">Total Completed</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ticket #</th>
                <th>Request Description</th>
                <th>Requester</th>
                <th>Items Worked On</th>
                <th>Completed Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($completedAssignments as $assignment)
                <tr>
                    <td>{{ $assignment->maintenanceRequest->ticket_number ?? 'N/A' }}</td>
                    <td>{{ Str::limit($assignment->maintenanceRequest->description ?? 'N/A', 60) }}</td>
                    <td>{{ $assignment->maintenanceRequest->user->full_name ?? 'N/A' }}</td>
                    <td>
                        @foreach ($assignment->maintenanceRequest->items as $item)
                            {{ $item->item->name ?? 'N/A' }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </td>
                    <td>{{ $assignment->completed_at ? $assignment->completed_at->format('M d, Y H:i') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($completedAssignments->count() === 0)
        <p style="text-align: center; padding: 30px; color: #64748b;">No completed tasks found for the selected period.
        </p>
    @endif

    <div class="footer">
        <p>This is an automatically generated personal report. For more details, please contact the system
            administrator.</p>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>

</html>

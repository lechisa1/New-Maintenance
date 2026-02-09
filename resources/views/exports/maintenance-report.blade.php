<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Maintenance Completion Report - {{ $request->ticket_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header-container {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .logo-container {
            flex: 0 0 90px;
        }

        .company-logo {
            width: 80px;
            height: auto;
        }

        .title-section {
            flex: 1;
            text-align: center;
        }

        .report-title {
            font-size: 18pt;
            font-weight: 600;
            color: #1e3a8a;
            margin: 0 0 5px 0;
        }

        .ticket-reference {
            font-size: 11pt;
            color: #6b7280;
            margin: 0;
        }

        .document-info {
            text-align: right;
            font-size: 9pt;
            color: #6b7280;
            margin-top: 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0 30px 0;
            font-size: 10pt;
        }

        .data-table th {
            background-color: #f8fafc;
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            width: 30%;
            vertical-align: top;
        }

        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            color: #111827;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            background-color: #10b981;
            color: white;
            font-weight: 600;
            font-size: 9pt;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 25px 0;
        }

        .notes-section {
            margin: 20px 0;
        }

        .section-title {
            font-size: 12pt;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .notes-content {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 12px 15px;
            min-height: 60px;
            font-size: 10pt;
            line-height: 1.5;
            white-space: pre-wrap;
        }

        .materials-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .materials-list li {
            padding: 6px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .materials-list li:last-child {
            border-bottom: none;
        }

        .material-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .material-name {
            flex: 1;
        }

        .material-qty {
            font-weight: 600;
            color: #1e3a8a;
            margin-left: 15px;
        }

        .no-materials {
            font-style: italic;
            color: #6b7280;
            text-align: center;
            padding: 20px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9pt;
            color: #6b7280;
            text-align: center;
        }

        .footer-info {
            margin: 5px 0;
        }

        .timestamp {
            font-weight: 600;
            color: #374151;
        }

        @page {
            margin: 50px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- Header Section --}}
    <div class="header-container">
        <div class="logo-container">
            <img src="{{ public_path('images/AII-logo.png') }}" class="company-logo" alt="Company Logo">
        </div>

        <div class="title-section">
            <h1 class="report-title">MAINTENANCE COMPLETION REPORT</h1>
            <p class="ticket-reference">Work Order: {{ $request->ticket_number }}</p>
        </div>
    </div>

    {{-- Document Information --}}
    <div class="document-info">
        Document Type: Maintenance Completion Report<br>
        Document ID: MCR-{{ $request->ticket_number }}
    </div>

    {{-- Request Details Table --}}
    <table class="data-table">
        <tr>
            <th>Equipment / Asset</th>
            <td>{{ $request->item?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Issue Type</th>
            <td>{{ $request->getIssueTypeText() ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Priority Level</th>
            <td>{{ $request->getPriorityText() ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Request Description</th>
            <td>{{ $request->description ?? 'No description provided' }}</td>
        </tr>
    </table>

    {{-- Personnel Information --}}
    <div class="section-title">Personnel Information</div>
    <table class="data-table">
        <tr>
            <th>Requested By</th>
            <td>{{ $request->user?->full_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Assigned Technician</th>
            <td>{{ $request->assignedTechnician?->full_name ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Timeline Information --}}
    <div class="section-title">Timeline</div>
    <table class="data-table">
        <tr>
            <th>Assignment Date</th>
            <td>{{ $request->assigned_at?->format('F j, Y - H:i') ?? 'Not assigned' }}</td>
        </tr>
        <tr>
            <th>Completion Date</th>
            <td>{{ $request->completed_at?->format('F j, Y - H:i') ?? 'Not completed' }}</td>
        </tr>
    </table>

    {{-- Technician Notes Section --}}
    <div class="notes-section">
        <div class="section-title">Technician Notes & Resolution Details</div>
        <div class="notes-content">
            {{ $request->technician_notes ?? 'No additional notes provided.' }}
        </div>
    </div>

    {{-- Material Used --}}
    {{-- Materials & Parts Used --}}
    <div class="section-title">Materials & Parts Used</div>
    <table class="data-table">
        <tr>
            <th>Materials Used</th>
            <td>
                @php
                    // Get materials from work logs - try different approaches
                    $materials = null;

                    // Try to get materials from the request itself if it has a materials relationship
                    if (method_exists($request, 'materials') && $request->materials) {
                        $materials = $request->materials;
                    }
                    // Try to get from workLogs
                    elseif ($request->workLogs && $request->workLogs->isNotEmpty()) {
                        $workLog = $request->workLogs->first();

                        // Check if materials_used is a JSON string, array, or collection
                        if (isset($workLog->materials_used)) {
                            $materialsData = $workLog->materials_used;

                            if (is_string($materialsData)) {
                                // Try to decode JSON
                                $decoded = json_decode($materialsData, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $materials = $decoded;
                                } else {
                                    // If not JSON, check if it's a serialized array
                $unserialized = @unserialize($materialsData);
                if ($unserialized !== false && is_array($unserialized)) {
                    $materials = $unserialized;
                } else {
                    // If it's just a string, wrap it in an array
                                        $materials = [$materialsData];
                                    }
                                }
                            } elseif (
                                is_array($materialsData) ||
                                $materialsData instanceof \Illuminate\Support\Collection
                            ) {
                                $materials = $materialsData;
                            }
                        }
                    }
                @endphp

                @if (!empty($materials) && (is_array($materials) || $materials instanceof \Countable ? count($materials) > 0 : false))
                    <div style="margin: 0; padding: 0;">
                        @foreach ($materials as $index => $material)
                            @if (is_array($material) && isset($material['name']))
                                {{ $material['name'] }}
                            @elseif (is_string($material) && !empty(trim($material)))
                                {{ trim($material) }}
                            @elseif (is_object($material) && isset($material->name))
                                {{ $material->name }}
                            @endif

                            @if (!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="color: #6b7280; font-style: italic;">
                        No materials were used
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Status Confirmation  --}}
    <div class="section-title">Work Order Status</div>
    <table class="data-table">
        <tr>
            <th>Current Status</th>
            <td>
                <span class="status-badge">COMPLETED & CONFIRMED</span>
                <div style="margin-top: 8px; font-size: 9pt; color: #6b7280;">
                    This maintenance request has been successfully resolved and confirmed.
                </div>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-info">
            This document was automatically generated by the Maintenance Management System
        </div>
        <div class="footer-info">
            Generated on: <span class="timestamp">{{ now()->format('F j, Y \a\t H:i') }}</span>
        </div>
        <div class="footer-info">
            Document ID: MCR-{{ $request->ticket_number }} | Page 1 of 1
        </div>
    </div>

</body>

</html>

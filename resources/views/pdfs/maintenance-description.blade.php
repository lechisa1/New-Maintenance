<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Problem Description</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.6;
        }

        h2 {
            margin-bottom: 10px;
        }

        .box {
            border: 1px solid #ccc;
            padding: 12px;
        }
    </style>
</head>

<body>

    <h2>Maintenance Request Problem Description</h2>

    <p><strong>Ticket:</strong> {{ $maintenanceRequest->ticket_number ?? 'N/A' }}</p>

    <div class="box">
        {!! nl2br(e($maintenanceRequest->description)) !!}
    </div>

</body>

</html>

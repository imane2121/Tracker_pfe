<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Collection Report - {{ $collecte->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 150px auto;
            gap: 10px;
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Collection Report</h1>
        <p>Report generated on {{ now()->format('F d, Y H:i') }}</p>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <h2 class="section-title">Collection Information</h2>
        <div class="info-grid">
            <div class="label">Collection ID</div>
            <div class="value">{{ $collecte->id }}</div>

            <div class="label">Location</div>
            <div class="value">{{ $collecte->location }}</div>

            <div class="label">Region</div>
            <div class="value">{{ $collecte->region }}</div>

            <div class="label">Started</div>
            <div class="value">{{ $collecte->starting_date->format('M d, Y H:i') }}</div>

            <div class="label">Completed</div>
            <div class="value">{{ $collecte->completion_date->format('M d, Y H:i') }}</div>

            <div class="label">Organizer</div>
            <div class="value">{{ $collecte->creator->first_name }} {{ $collecte->creator->last_name }}</div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="section">
        <h2 class="section-title">Collection Statistics</h2>
        <div class="stats">
            <div class="stat-box">
                <div class="stat-value">{{ $collecte->actual_volume }} m³</div>
                <div class="stat-label">Total Volume Collected</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $collecte->current_contributors }}</div>
                <div class="stat-label">Total Contributors</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ round($collecte->attendancePercentage) }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>

    <!-- Waste Types -->
    <div class="section">
        <h2 class="section-title">Waste Types Collected</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Expected</th>
                    <th>Found</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wasteTypes as $type)
                    <tr>
                        <td>{{ $type->name }}</td>
                        <td>{{ in_array($type->id, $collecte->waste_types) ? '✓' : '-' }}</td>
                        <td>{{ in_array($type->id, $collecte->actual_waste_types) ? '✓' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Attendance -->
    <div class="section">
        <h2 class="section-title">Attendance Record</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Contributor</th>
                    <th>Present</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collecte->contributors as $contributor)
                    @php
                        $attendance = collect($collecte->attendance_data)
                            ->firstWhere('user_id', $contributor->id);
                    @endphp
                    <tr>
                        <td>{{ $contributor->first_name }} {{ $contributor->last_name }}</td>
                        <td>{{ $attendance['attended'] ? '✓' : '✗' }}</td>
                        <td>{{ $attendance['notes'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Notes -->
    @if($collecte->completion_notes)
    <div class="section">
        <h2 class="section-title">Additional Notes</h2>
        <p>{{ $collecte->completion_notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the Marine Waste Tracking System.</p>
        <p>Report ID: {{ Str::uuid() }}</p>
    </div>
</body>
</html> 
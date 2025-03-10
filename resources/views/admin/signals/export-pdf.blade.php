<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Signals Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Signals Export</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Location</th>
                <th>Waste Types</th>
                <th>Volume</th>
                <th>Reporter</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($signals as $signal)
                <tr>
                    <td>{{ $signal->id }}</td>
                    <td>{{ $signal->location }}</td>
                    <td>{{ $signal->wasteTypes->pluck('name')->join(', ') }}</td>
                    <td>{{ $signal->volume }} m³</td>
                    <td>{{ $signal->creator->first_name }} {{ $signal->creator->last_name }}</td>
                    <td>{{ $signal->status }}</td>
                    <td>{{ $signal->signal_date->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Collection Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #1e56b0;
            color: white;
            border-radius: 8px;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 28px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .section-title {
            font-size: 20px;
            color: #1e56b0;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e56b0;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-row {
            display: table-row;
        }
        .info-label, .info-value {
            display: table-cell;
            padding: 8px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 35%;
        }
        .waste-type {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 15px;
            margin: 3px;
            border-radius: 15px;
        }
        .participants-list {
            column-count: 2;
            column-gap: 40px;
        }
        .participant {
            break-inside: avoid;
            padding: 5px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            position: relative;
            padding-top: 30px;
        }
        .signature-line {
            margin-top: 100px;
            border-top: 1px solid #333;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            padding-top: 10px;
        }
        .website-signature {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 20px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .website-signature img {
            height: 30px;
            margin-bottom: 10px;
        }
        .website-signature .brand-name {
            font-size: 14px;
            font-weight: bold;
            color: #1e56b0;
            margin-bottom: 5px;
        }
        .website-signature .tagline {
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-bottom: 5px;
        }
        .website-signature .document-id {
            font-size: 10px;
            color: #999;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">AquaScan Collection Report</div>
        <div class="subtitle">Generated on {{ now()->format('F d, Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Collection Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value">{{ $rapport->location }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Region:</div>
                <div class="info-value">{{ $collecte->region }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Start Date:</div>
                <div class="info-value">{{ $rapport->starting_date->format('M d, Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">End Date:</div>
                <div class="info-value">{{ $rapport->end_date->format('M d, Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Volume:</div>
                <div class="info-value">{{ $rapport->volume }} m³</div>
            </div>
            <div class="info-row">
                <div class="info-label">Number of Contributors:</div>
                <div class="info-value">{{ $rapport->nbrContributors }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Supervisor:</div>
                <div class="info-value">{{ $rapport->supervisor->first_name }} {{ $rapport->supervisor->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Coordinates:</div>
                <div class="info-value">{{ $rapport->latitude }}, {{ $rapport->longitude }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <p>{{ $rapport->description }}</p>
    </div>

    <div class="section">
        <div class="section-title">Waste Types</div>
        <div class="waste-types">
            @foreach($rapport->waste_types as $wasteTypeId)
                @php
                    $wasteType = \App\Models\WasteTypes::find($wasteTypeId);
                @endphp
                @if($wasteType)
                    <span class="waste-type">{{ $wasteType->name }}</span>
                @endif
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-title">Attended Contributors</div>
        <div class="participants-list">
            @foreach($rapport->participants as $participantId)
                @php
                    $participant = \App\Models\User::find($participantId);
                @endphp
                @if($participant)
                    <div class="participant">
                        • {{ $participant->first_name }} {{ $participant->last_name }}
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <div class="footer">
        <div class="signature-line">
            Supervisor's Signature
        </div>
        <div class="website-signature">
            <img src="{{ asset('assets/img/apple-touch-icon.png') }}" alt="AquaScan Logo"><br>
            <div class="brand-name">AquaScan</div>
            <div class="tagline">Making our oceans cleaner</div>
            <div>{{ config('app.url') }}</div>
            <div class="document-id">Document ID: {{ md5($rapport->id . $rapport->created_at) }}</div>
        </div>
    </div>
</body>
</html>

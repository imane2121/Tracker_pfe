@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Batch Validate Signals</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-check-double me-1"></i>
            Pending Signals
        </div>
        <div class="card-body">
            <form action="{{ route('admin.signals.batch-validate.store') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Location</th>
                                <th>Waste Types</th>
                                <th>Reporter</th>
                                <th>Date</th>
                                <th>AI Analysis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingSignals as $signal)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="signals[]" value="{{ $signal->id }}" class="form-check-input">
                                    </td>
                                    <td>{{ $signal->location }}</td>
                                    <td>
                                        @foreach($signal->wasteTypes as $type)
                                            <span class="badge bg-info">{{ $type->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $signal->creator->first_name }} {{ $signal->creator->last_name }}</td>
                                    <td>{{ $signal->signal_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($signal->aiAnalysis)
                                            @if($signal->aiAnalysis->debris_detected)
                                                <span class="badge bg-success">Debris Detected</span>
                                                <br>
                                                <small>Confidence: {{ number_format($signal->aiAnalysis->confidence_score * 100, 1) }}%</small>
                                                <br>
                                                @if($signal->aiAnalysis->matches_reporter_selection)
                                                    <span class="badge bg-success">Matches Reporter</span>
                                                @else
                                                    <span class="badge bg-warning">Mismatch</span>
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#aiDetailsModal{{ $signal->id }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">No Debris Detected</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Not Analyzed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.signals.show', $signal) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- AI Details Modal -->
                                @if($signal->aiAnalysis && !$signal->aiAnalysis->matches_reporter_selection)
                                <div class="modal fade" id="aiDetailsModal{{ $signal->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">AI Analysis Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h6>Reporter Selected Types:</h6>
                                                <ul>
                                                    @foreach($signal->wasteTypes as $type)
                                                        <li>{{ $type->name }}</li>
                                                    @endforeach
                                                </ul>
                                                
                                                <h6>AI Detected Types:</h6>
                                                <ul>
                                                    @foreach($signal->aiAnalysis->detected_waste_types as $type => $confidence)
                                                        <li>{{ $type }} ({{ number_format($confidence * 100, 1) }}%)</li>
                                                    @endforeach
                                                </ul>
                                                
                                                <h6>Analysis Notes:</h6>
                                                <p>{{ $signal->aiAnalysis->analysis_notes }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No pending signals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Validate Selected</button>
                    <a href="{{ route('admin.signals.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
            <div class="mt-4">
                {{ $pendingSignals->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('input[name="signals[]"]')
        .forEach(checkbox => checkbox.checked = this.checked);
});
</script>
@endpush
@endsection 
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Anomaly Review</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-exclamation-triangle me-1"></i>
            Anomalous reports
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Waste Types</th>
                            <th>Volume</th>
                            <th>Reporter</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anomalies as $signal)
                            <tr>
                                <td>{{ $signal->location }}</td>
                                <td>
                                    @foreach($signal->wasteTypes as $type)
                                        <span class="badge bg-info">{{ $type->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $signal->volume }} m³</td>
                                <td>{{ $signal->creator->first_name }} {{ $signal->creator->last_name }}</td>
                                <td>{{ $signal->signal_date->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.signals.show', $signal) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.signals.edit', $signal) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No anomalous signals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $anomalies->links() }}
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.signals.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection 
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
            <form action="{{ route('admin.signals.batch-validate') }}" method="POST">
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No pending signals found.</td>
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
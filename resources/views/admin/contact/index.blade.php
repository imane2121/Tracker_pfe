@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Contact Messages</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>From</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr class="{{ $message->status === 'unread' ? 'table-primary' : '' }}">
                                        <td>
                                            <span class="badge badge-{{ $message->status === 'unread' ? 'danger' : ($message->status === 'read' ? 'warning' : 'success') }}">
                                                {{ ucfirst($message->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $message->name }}</div>
                                            <small class="text-muted">{{ $message->email }}</small>
                                        </td>
                                        <td>{{ $message->subject }}</td>
                                        <td>{{ $message->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.contact.show', $message) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($message->status === 'unread')
                                                    <form action="{{ route('admin.contact.mark-as-read', $message) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-check2"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.contact.delete', $message) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this message?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No messages found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #eee;
    padding: 20px 30px;
}

.card-title {
    color: #2c3e50;
    font-size: 1.5rem;
    margin: 0;
}

.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    padding: 8px 12px;
    font-weight: 500;
}

.btn-group .btn {
    padding: 6px 12px;
    margin: 0 2px;
}

.btn-group .btn i {
    font-size: 1rem;
}

.table-primary {
    background-color: rgba(14, 162, 189, 0.1);
}

@media (max-width: 768px) {
    .card-header {
        padding: 15px 20px;
    }
    
    .table-responsive {
        margin: 0 -15px;
    }
    
    .btn-group {
        display: flex;
        gap: 5px;
    }
    
    .btn-group .btn {
        padding: 4px 8px;
    }
}
</style>
@endsection 
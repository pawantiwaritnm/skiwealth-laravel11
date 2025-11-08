@extends('layouts.admin')

@section('title', 'KYC Applications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">KYC Applications</h1>
            <p class="text-muted">Review and manage KYC applications</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.kyc.list') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Name, Mobile, Email" value="{{ request('search') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.kyc.list') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- KYC List Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">KYC Applications ({{ $applications->total() }})</h6>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>PAN No</th>
                                    <th>Status</th>
                                    <th>Submitted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                <tr>
                                    <td>{{ $app->id }}</td>
                                    <td>{{ $app->registration->name ?? 'N/A' }}</td>
                                    <td>{{ $app->registration->mobile ?? 'N/A' }}</td>
                                    <td>{{ $app->registration->email ?? 'N/A' }}</td>
                                    <td>{{ $app->pan_no ?? 'N/A' }}</td>
                                    <td>
                                        @if($app->kyc_status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($app->kyc_status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($app->kyc_status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>{{ $app->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.kyc.view', $app->id) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($app->kyc_status == 'pending')
                                        <button type="button" class="btn btn-sm btn-success" onclick="approveKYC({{ $app->id }})" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="rejectKYC({{ $app->id }})" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $applications->links() }}
                    </div>
                    @else
                    <p class="text-center text-muted">No KYC applications found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: #5b6b3d;
    }
    .card {
        border-radius: 8px;
    }
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .table th {
        font-weight: 600;
        font-size: 13px;
    }
    .table td {
        font-size: 13px;
        vertical-align: middle;
    }
    .badge {
        padding: 5px 10px;
        font-size: 11px;
    }
    .btn-sm {
        padding: 3px 8px;
        font-size: 12px;
    }
</style>

@push('scripts')
<script>
    function approveKYC(id) {
        if (confirm('Are you sure you want to approve this KYC application?')) {
            $.ajax({
                url: '{{ route("admin.kyc.approve", "") }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('KYC application approved successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to approve'));
                    }
                },
                error: function(xhr) {
                    alert('Error approving KYC application');
                }
            });
        }
    }

    function rejectKYC(id) {
        var reason = prompt('Please enter reason for rejection:');
        if (reason) {
            $.ajax({
                url: '{{ route("admin.kyc.reject", "") }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        alert('KYC application rejected');
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to reject'));
                    }
                },
                error: function(xhr) {
                    alert('Error rejecting KYC application');
                }
            });
        }
    }
</script>
@endpush
@endsection

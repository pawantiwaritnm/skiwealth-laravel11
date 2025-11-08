@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Dashboard</h1>
            <p class="text-muted">Welcome to SKI Capital Admin Panel</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Registrations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_registrations'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pending KYC
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_kyc'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending IPV
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_ipv'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-video fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Account Closure Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['closure_requests'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Registrations</h6>
                </div>
                <div class="card-body">
                    @if(isset($recent_registrations) && count($recent_registrations) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Mobile</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_registrations as $reg)
                                    <tr>
                                        <td>{{ $reg->name }}</td>
                                        <td>{{ $reg->mobile }}</td>
                                        <td>{{ $reg->email }}</td>
                                        <td>{{ $reg->created_at->format('d-m-Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent registrations</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.kyc.list') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-list"></i> View KYC Applications
                        </a>
                        <a href="{{ route('admin.ipv.list') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-video"></i> View IPV Videos
                        </a>
                        <a href="{{ route('admin.closure.list') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-times-circle"></i> View Account Closure Requests
                        </a>
                        <a href="{{ route('admin.users.list') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.reports') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar"></i> View Reports
                        </a>
                    </div>
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
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .list-group-item i {
        margin-right: 10px;
        width: 20px;
    }
</style>
@endsection

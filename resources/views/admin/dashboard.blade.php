@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        border-radius: 10px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    .stat-card-primary .stat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .stat-card-success .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-card-info .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-card-warning .stat-icon {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .stat-card-danger .stat-icon {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .stat-card-secondary .stat-icon {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .quick-action-card {
        border-radius: 10px;
        border: 2px dashed #e2e8f0;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .quick-action-card:hover {
        border-color: #667eea;
        background: #f7fafc;
        transform: translateY(-3px);
        text-decoration: none;
        color: inherit;
    }
    .recent-leads-table {
        border-radius: 10px;
        overflow: hidden;
    }
    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($totalLeads) }}</div>
                        <div class="stat-label">Total Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($newLeads) }}</div>
                        <div class="stat-label">New Leads (30 Days)</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="user-plus"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($activeLeads) }}</div>
                        <div class="stat-label">Active Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="activity"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($convertedLeads) }}</div>
                        <div class="stat-label">Converted Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-secondary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($todayLeads) }}</div>
                        <div class="stat-label">Today's Leads</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-danger">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($thisWeekLeads) }}</div>
                        <div class="stat-label">This Week</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="trending-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($thisMonthLeads) }}</div>
                        <div class="stat-label">This Month</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="bar-chart-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card stat-card stat-card-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="stat-number">{{ number_format($totalUsers) }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-icon">
                        <i data-feather="user"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i data-feather="zap" class="me-2"></i> Quick Actions
                </h4>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.leads.create') }}" class="quick-action-card card p-3 text-center">
                            <div class="card-body">
                                <i data-feather="plus-circle" class="mb-2" style="width: 32px; height: 32px; color: #667eea;"></i>
                                <h6 class="mb-0">Add New Lead</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.leads.index') }}" class="quick-action-card card p-3 text-center">
                            <div class="card-body">
                                <i data-feather="list" class="mb-2" style="width: 32px; height: 32px; color: #f5576c;"></i>
                                <h6 class="mb-0">View All Leads</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.countries.index') }}" class="quick-action-card card p-3 text-center">
                            <div class="card-body">
                                <i data-feather="globe" class="mb-2" style="width: 32px; height: 32px; color: #4facfe;"></i>
                                <h6 class="mb-0">Manage Countries</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('admin.lead-statuses.index') }}" class="quick-action-card card p-3 text-center">
                            <div class="card-body">
                                <i data-feather="tag" class="mb-2" style="width: 32px; height: 32px; color: #43e97b;"></i>
                                <h6 class="mb-0">Lead Statuses</h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Leads & Status Distribution -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">
                        <i data-feather="clock" class="me-2"></i> Recent Leads
                    </h4>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive recent-leads-table">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Country</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeads as $lead)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <span class="text-primary fw-bold">{{ substr($lead->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <strong>{{ $lead->name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->country_code }} {{ $lead->phone }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->email ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="status-badge bg-primary text-white">
                                        {{ $lead->leadStatus->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->country->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $lead->created_at->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-sm btn-outline-info">
                                        <i data-feather="eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i data-feather="inbox" class="mb-2" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                                    <p class="text-muted mb-0">No leads found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i data-feather="pie-chart" class="me-2"></i> Leads by Status
                </h4>
                @if($leadsByStatus->count() > 0)
                <div class="mb-3">
                    @foreach($leadsByStatus as $status)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2" style="width: 12px; height: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%;"></div>
                            <span class="fw-medium">{{ $status['name'] }}</span>
                        </div>
                        <span class="badge bg-light text-dark">{{ $status['count'] }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i data-feather="bar-chart" class="mb-2" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                    <p class="text-muted mb-0">No status data available</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize feather icons with error handling
    if (typeof feather !== 'undefined') {
        try {
            feather.replace();
        } catch(e) {
            console.warn('Feather icons error:', e);
        }
    }
});
</script>
@endpush

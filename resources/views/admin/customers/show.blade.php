@extends('layouts.admin')

@section('title', 'View Customer')
@section('page-title', 'View Customer')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
<li class="breadcrumb-item active" aria-current="page">View</li>
@endsection

@push('styles')
<style>
    .customer-info-card {
        border-left: 4px solid #0ab39c;
        transition: all 0.3s ease;
    }
    .customer-info-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
        margin-bottom: 4px;
    }
    .info-value {
        color: #212529;
        font-size: 1rem;
    }
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .activity-item {
        position: relative;
        margin-bottom: 24px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #0ab39c;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -23px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #0ab39c;
        border: 2px solid #fff;
    }
    .activity-type-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Customer Information Card -->
    <div class="col-lg-8">
        <div class="card customer-info-card">
            <div class="card-header bg-success-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i data-feather="user-check" class="me-2"></i>Customer Information
                    </h5>
                    <div>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-secondary">
                            <i data-feather="arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Customer ID</div>
                            <div class="info-value">#{{ $customer->id }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value">
                                <strong>{{ $customer->name }}</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value">
                                <i data-feather="phone" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $customer->country_code }} {{ $customer->phone }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                @if($customer->email)
                                    <i data-feather="mail" class="me-1" style="width: 14px; height: 14px;"></i>
                                    <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Country</div>
                            <div class="info-value">
                                <i data-feather="map-pin" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $customer->country->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Purpose</div>
                            <div class="info-value">{{ $customer->purpose->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Lead ID</div>
                            <div class="info-value">
                                @if($customer->lead)
                                    <a href="{{ route('admin.leads.show', $customer->lead) }}" class="text-primary">
                                        #{{ $customer->lead_id }}
                                    </a>
                                @else
                                    #{{ $customer->lead_id }}
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Assigned Telecaller</div>
                            <div class="info-value">
                                @if($customer->telecaller)
                                    <i data-feather="user" class="me-1" style="width: 14px; height: 14px;"></i>
                                    {{ $customer->telecaller->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Converted Date</div>
                            <div class="info-value">
                                <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $customer->converted_date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Converted By</div>
                            <div class="info-value">
                                @if($customer->convertedBy)
                                    <i data-feather="user-check" class="me-1" style="width: 14px; height: 14px;"></i>
                                    {{ $customer->convertedBy->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Created At</div>
                            <div class="info-value">
                                <i data-feather="clock" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $customer->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Updated At</div>
                            <div class="info-value">
                                <i data-feather="clock" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $customer->updated_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info-subtle">
                <h5 class="card-title mb-0">
                    <i data-feather="zap" class="me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.quotations.index', $customer) }}" class="btn btn-primary">
                        <i data-feather="file-text" class="me-2"></i>View Quotations
                    </a>
                    <button class="btn btn-success" onclick="show_ajax_modal('{{ route('admin.quotations.create', $customer) }}', 'Generate Quotation')">
                        <i data-feather="plus" class="me-2"></i>Generate Quotation
                    </button>
                    @if($customer->lead)
                    <a href="{{ route('admin.leads.show', $customer->lead) }}" class="btn btn-info">
                        <i data-feather="eye" class="me-2"></i>View Original Lead
                    </a>
                    @endif
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left" class="me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Conversion Info Card -->
        <div class="card mt-3">
            <div class="card-header bg-success-subtle">
                <h5 class="card-title mb-0">
                    <i data-feather="check-circle" class="me-2"></i>Conversion Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i data-feather="refresh-cw" style="width: 48px; height: 48px; color: #0ab39c;"></i>
                    <h5 class="mt-2 mb-0">Lead Converted</h5>
                    <small class="text-muted">{{ $customer->converted_date->format('M d, Y') }}</small>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Converted By:</span>
                    <strong>{{ $customer->convertedBy ? $customer->convertedBy->name : 'N/A' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Days Since Conversion:</span>
                    <strong>{{ $customer->converted_date->diffInDays(now()) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity History -->
@if($customer->lead && $customer->lead->activities)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-success-subtle">
                <h5 class="card-title mb-0">
                    <i data-feather="activity" class="me-2"></i>Activity History
                </h5>
            </div>
            <div class="card-body">
                @if($customer->lead->activities->count() > 0)
                <div class="activity-timeline">
                    @foreach($customer->lead->activities->sortByDesc('created_at') as $activity)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="activity-type-badge bg-success text-white">
                                    {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                </span>
                                @if($activity->leadStatus)
                                    <span class="badge bg-info ms-2">{{ $activity->leadStatus->name }}</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                <i data-feather="calendar" style="width: 12px; height: 12px;"></i>
                                {{ $activity->date->format('M d, Y') }}
                            </small>
                        </div>
                        @if($activity->description)
                        <p class="mb-2"><strong>Description:</strong> {{ $activity->description }}</p>
                        @endif
                        @if($activity->remark)
                        <div class="alert alert-light mb-0" style="background: #f8f9fa; padding: 8px 12px;">
                            <strong>Remarks:</strong> {{ $activity->remark }}
                        </div>
                        @endif
                        @if($activity->followup_date)
                        <p class="mb-0 mt-2">
                            <i data-feather="calendar" style="width: 12px; height: 12px;"></i>
                            <strong>Followup:</strong> {{ $activity->followup_date->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i data-feather="inbox" style="width: 48px; height: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">No activity history available</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush

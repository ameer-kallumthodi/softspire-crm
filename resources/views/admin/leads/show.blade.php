@extends('layouts.admin')

@section('title', 'View Lead')
@section('page-title', 'View Lead')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active" aria-current="page">View</li>
@endsection

@push('styles')
<style>
    .lead-info-card {
        border-left: 4px solid #405189;
        transition: all 0.3s ease;
    }
    .lead-info-card:hover {
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
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.875rem;
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
        border-left: 3px solid #405189;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -23px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #405189;
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
    <!-- Lead Information Card -->
    <div class="col-lg-8">
        <div class="card lead-info-card">
            <div class="card-header bg-primary-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i data-feather="user" class="me-2"></i>Lead Information
                    </h5>
                    @if(!$lead->is_converted)
                    <div>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('{{ route('admin.leads.ajax-edit', $lead) }}', 'Edit Lead')">
                            <i data-feather="edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-success" onclick="openStatusModal({{ $lead->id }})">
                            <i data-feather="arrow-up"></i> Update Status
                        </button>
                    </div>
                    @else
                    <div>
                        <span class="badge bg-success">Converted to Customer</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Lead ID</div>
                            <div class="info-value">#{{ $lead->id }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value">
                                <strong>{{ $lead->name }}</strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value">
                                <i data-feather="phone" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $lead->country_code }} {{ $lead->phone }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">
                                @if($lead->email)
                                    <i data-feather="mail" class="me-1" style="width: 14px; height: 14px;"></i>
                                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Country</div>
                            <div class="info-value">
                                <i data-feather="map-pin" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $lead->country->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Purpose</div>
                            <div class="info-value">{{ $lead->purpose->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Lead Status</div>
                            <div class="info-value">
                                <span class="badge bg-primary status-badge">
                                    {{ $lead->leadStatus->name ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Lead Source</div>
                            <div class="info-value">{{ $lead->leadSource->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Assigned Telecaller</div>
                            <div class="info-value">
                                @if($lead->telecaller)
                                    <i data-feather="user" class="me-1" style="width: 14px; height: 14px;"></i>
                                    {{ $lead->telecaller->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Date</div>
                            <div class="info-value">
                                <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $lead->date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Followup Date</div>
                            <div class="info-value">
                                @if($lead->followup_date)
                                    <i data-feather="calendar" class="me-1" style="width: 14px; height: 14px;"></i>
                                    {{ $lead->followup_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">Not Set</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Created At</div>
                            <div class="info-value">
                                <i data-feather="clock" class="me-1" style="width: 14px; height: 14px;"></i>
                                {{ $lead->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($lead->remarks)
                <div class="mt-3 pt-3 border-top">
                    <div class="info-label">Remarks</div>
                    <div class="info-value mt-2">
                        <div class="alert alert-light" style="background: #f8f9fa;">
                            {{ $lead->remarks }}
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-3 pt-3 border-top">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Is Meta Lead</div>
                            <div class="info-value">
                                @if($lead->is_meta)
                                    <span class="badge bg-info">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-label">Is Converted</div>
                            <div class="info-value">
                                @if($lead->is_converted)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
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
                    @if(!$lead->is_converted)
                    <button class="btn btn-success" onclick="openStatusModal({{ $lead->id }})">
                        <i data-feather="arrow-up" class="me-2"></i>Update Status
                    </button>
                    <button class="btn btn-warning" onclick="show_ajax_modal('{{ route('admin.leads.ajax-edit', $lead) }}', 'Edit Lead')">
                        <i data-feather="edit" class="me-2"></i>Edit Lead
                    </button>
                    @else
                    @php
                        $customer = \App\Models\Customer::where('lead_id', $lead->id)->first();
                    @endphp
                    @if($customer)
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-primary">
                        <i data-feather="user-check" class="me-2"></i>View Customer
                    </a>
                    @endif
                    @endif
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left" class="me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card mt-3">
            <div class="card-header bg-primary-subtle">
                <h5 class="card-title mb-0">
                    <i data-feather="bar-chart-2" class="me-2"></i>Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="mb-0">{{ $lead->activities->count() }}</h3>
                    <small class="text-muted">Total Activities</small>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status Changes:</span>
                    <strong>{{ $lead->activities->where('activity_type', 'status_change')->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Updates:</span>
                    <strong>{{ $lead->activities->where('activity_type', 'update')->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Followups:</span>
                    <strong>{{ $lead->activities->where('activity_type', 'followup_update')->count() }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity History -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary-subtle">
                <h5 class="card-title mb-0">
                    <i data-feather="activity" class="me-2"></i>Activity History
                </h5>
            </div>
            <div class="card-body">
                @if($lead->activities->count() > 0)
                <div class="activity-timeline">
                    @foreach($lead->activities->sortByDesc('created_at') as $activity)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="activity-type-badge bg-primary text-white">
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

@if(!$lead->is_converted)
<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title">
                    <i data-feather="arrow-up" class="me-2"></i>Update Lead Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusUpdateForm">
                @csrf
                <input type="hidden" id="statusLeadId" name="lead_id" value="{{ $lead->id }}">
                <div class="modal-body">
                    <div class="card mb-3 border-primary">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i data-feather="info" class="me-2"></i>Lead Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Name:</strong> {{ $lead->name }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ $lead->country_code }} {{ $lead->phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Email:</strong> {{ $lead->email ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Current Status:</strong> 
                                        <span class="badge bg-primary">{{ $lead->leadStatus->name ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>New Lead Status <span class="text-danger">*</span></label>
                        <select name="lead_status_id" id="statusLeadStatusId" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach(\App\Models\LeadStatus::where('status', 'active')->get() as $status)
                            <option value="{{ $status->id }}" {{ $lead->lead_status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" id="statusRemarks" class="form-control" rows="3" required placeholder="Enter remarks for this status update..."></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="statusDate" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="needed_followup" id="neededFollowup" value="1">
                            <label class="form-check-label" for="neededFollowup">
                                Needed Followup
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-3" id="followupDateGroup" style="display: none;">
                        <label>Followup Date <span class="text-danger">*</span></label>
                        <input type="date" name="followup_date" id="followupDate" class="form-control" min="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="check" class="me-2"></i>Update Status
                    </button>
                </div>
            </form>
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

$('#statusUpdateForm').on('submit', function(e) {
    e.preventDefault();
    
    // Validate followup date if checkbox is checked
    if ($('#neededFollowup').is(':checked')) {
        const followupDate = $('#followupDate').val();
        if (!followupDate) {
            showToast('Followup date is required when followup is needed', 'error');
            return;
        }
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selectedDate = new Date(followupDate);
        if (selectedDate < today) {
            showToast('Followup date must be today or a future date', 'error');
            return;
        }
    }
    
    const leadId = $('#statusLeadId').val();
    const formData = new FormData(this);

    $.ajax({
        url: `/admin/leads/${leadId}/status-update`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Lead status updated successfully', 'success');
                $('#statusUpdateModal').modal('hide');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        },
        error: function(xhr) {
            let errorMessage = 'Error updating status';
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('<br>');
                }
            }
            showToast(errorMessage, 'error');
        }
    });
});

function openStatusModal(leadId) {
    $('#statusLeadStatusId').val('');
    $('#statusRemarks').val('');
    $('#statusDate').val('{{ now()->format('Y-m-d') }}');
    $('#neededFollowup').prop('checked', false);
    $('#followupDate').val('');
    $('#followupDateGroup').hide();
    $('#followupDate').removeAttr('required');
    $('#statusUpdateModal').modal('show');
    
    // Reinitialize feather icons after modal opens
    setTimeout(function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }, 300);
}

// Handle followup checkbox toggle
$(document).on('change', '#neededFollowup', function() {
    if ($(this).is(':checked')) {
        $('#followupDateGroup').show();
        $('#followupDate').attr('required', 'required');
    } else {
        $('#followupDateGroup').hide();
        $('#followupDate').removeAttr('required');
        $('#followupDate').val('');
    }
});
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Leads')
@section('page-title', 'Leads')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Leads</li>
@endsection

@push('styles')
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <h4 class="card-title">Leads</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.leads.ajax-add') }}', 'Add New Lead')">
                            <i data-feather="plus"></i> Add New Lead
                        </button>
                    </div>
                </div>

                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="">
                        </div>
                        <div class="col-md-3">
                            <select name="country_id" class="form-control" id="countryFilter">
                                <option value="">All Countries</option>
                                @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="lead_status_id" class="form-control" id="statusFilter">
                                <option value="">All Statuses</option>
                                @foreach($leadStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="lead_source_id" class="form-control" id="sourceFilter">
                                <option value="">All Sources</option>
                                @foreach($leadSources as $source)
                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="leadsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Lead Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusUpdateForm">
                @csrf
                <input type="hidden" id="statusLeadId" name="lead_id">
                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>Lead Information</h6>
                            <div id="leadInfo"></div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>New Lead Status <span class="text-danger">*</span></label>
                        <select name="lead_status_id" id="statusLeadStatusId" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" id="statusRemarks" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="statusDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    loadLeads();
    
    $('#leadsTable').on('click', '.filter-btn', function() {
        loadLeads();
    });
});

function loadLeads() {
    const search = $('input[name="search"]').val();
    const countryId = $('#countryFilter').val();
    const leadStatusId = $('#statusFilter').val();
    const leadSourceId = $('#sourceFilter').val();
    
    $.ajax({
        url: '{{ route('admin.leads.index') }}',
        method: 'GET',
        data: { search: search, country_id: countryId, lead_status_id: leadStatusId, lead_source_id: leadSourceId, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderLeadsTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading leads';
            showToast(errorMessage, 'error');
            $('#leadsTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderLeadsTable(leads) {
    let html = '';
    if (leads.length === 0) {
        html = '<tr><td colspan="8" class="text-center">No leads found</td></tr>';
    } else {
        leads.forEach(function(lead) {
            const date = new Date(lead.date);
            html += `
                <tr>
                    <td>${lead.id}</td>
                    <td>${lead.name}</td>
                    <td>${lead.country_code} ${lead.phone}</td>
                    <td>${lead.email || 'N/A'}</td>
                    <td>${lead.country ? lead.country.name : 'N/A'}</td>
                    <td>
                        <span class="badge bg-primary">${lead.lead_status ? lead.lead_status.name : 'N/A'}</span>
                    </td>
                    <td>${date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                                    <a href="/admin/leads/${lead.id}" class="btn btn-sm btn-info">
                                        <i data-feather="eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/leads/${lead.id}/ajax/edit', 'Update Lead')">
                                        <i data-feather="edit"></i>
                                    </button>
                        <button class="btn btn-sm btn-success" onclick="openStatusModal(${lead.id})">
                            <i data-feather="refresh-cw"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLead(${lead.id})">
                            <i data-feather="trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#leadsTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function resetFilters() {
    $('input[name="search"]').val('');
    $('#countryFilter').val('');
    $('#statusFilter').val('');
    $('#sourceFilter').val('');
    loadLeads();
}

function openStatusModal(leadId) {
    $.ajax({
        url: `/admin/leads/${leadId}`,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const lead = response.data;
                $('#statusLeadId').val(lead.id);
                $('#leadInfo').html(`
                    <p><strong>Name:</strong> ${lead.name}</p>
                    <p><strong>Phone:</strong> ${lead.country_code} ${lead.phone}</p>
                    <p><strong>Email:</strong> ${lead.email || 'N/A'}</p>
                    <p><strong>Current Status:</strong> ${lead.lead_status ? lead.lead_status.name : 'N/A'}</p>
                `);
                $('#statusLeadStatusId').val('');
                $('#statusRemarks').val('');
                $('#statusDate').val('{{ date('Y-m-d') }}');
                $('#statusUpdateModal').modal('show');
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Error loading lead';
            showToast(errorMessage, 'error');
        }
    });
}

$('#statusUpdateForm').on('submit', function(e) {
    e.preventDefault();
    const leadId = $('#statusLeadId').val();
    const formData = new FormData(this);

    $.ajax({
        url: `/admin/leads/${leadId}/status-update`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Lead status updated successfully', 'success');
                $('#statusUpdateModal').modal('hide');
                loadLeads();
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Error updating status';
            showToast(errorMessage, 'error');
        }
    });
});

function deleteLead(id) {
    showConfirmModal('Are you sure you want to delete this lead?', 'Delete Lead', function() {
        $.ajax({
            url: `/admin/leads/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'Lead deleted successfully', 'success');
                    loadLeads();
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Error deleting lead';
                showToast(errorMessage, 'error');
            }
        });
    });
}
</script>
@endpush

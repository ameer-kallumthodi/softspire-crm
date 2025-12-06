@extends('layouts.admin')

@section('title', 'Lead Statuses')
@section('page-title', 'Lead Statuses')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Lead Statuses</li>
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
                    <h4 class="card-title">Lead Statuses</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.lead-statuses.ajax-add') }}', 'Add New Lead Status')">
                            <i data-feather="plus"></i> Add New Lead Status
                        </button>
                    </div>
                </div>

                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="leadStatusesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">
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
@endsection

@push('scripts')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    loadLeadStatuses();
    
    $('#leadStatusesTable').on('click', '.filter-btn', function() {
        loadLeadStatuses();
    });
});

function loadLeadStatuses() {
    const search = $('input[name="search"]').val();
    const status = $('select[name="status"]').val();
    
    $.ajax({
        url: '{{ route('admin.lead-statuses.index') }}',
        method: 'GET',
        data: { search: search, status: status, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderLeadStatusesTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading lead statuses';
            showToast(errorMessage, 'error');
            $('#leadStatusesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderLeadStatusesTable(leadStatuses) {
    let html = '';
    if (leadStatuses.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No lead statuses found</td></tr>';
    } else {
        leadStatuses.forEach(function(leadStatus) {
            html += `
                <tr>
                    <td>${leadStatus.id}</td>
                    <td>${leadStatus.name}</td>
                    <td>
                        <span class="badge bg-${leadStatus.status == 'active' ? 'success' : 'danger'}">
                            ${leadStatus.status.charAt(0).toUpperCase() + leadStatus.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(leadStatus.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <a href="/admin/lead-statuses/${leadStatus.id}" class="btn btn-sm btn-info">
                            <i data-feather="eye"></i>
                        </a>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/lead-statuses/${leadStatus.id}/ajax/edit', 'Update Lead Status')">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-${leadStatus.status == 'active' ? 'secondary' : 'success'}" onclick="toggleStatus(${leadStatus.id})">
                            <i data-feather="${leadStatus.status == 'active' ? 'x' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLeadStatus(${leadStatus.id})">
                            <i data-feather="trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#leadStatusesTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function toggleStatus(id) {
    $.ajax({
        url: `/admin/lead-statuses/${id}/toggle-status`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Status updated successfully', 'success');
                loadLeadStatuses();
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Something went wrong';
            showToast(errorMessage, 'error');
        }
    });
}

function deleteLeadStatus(id) {
    showConfirmModal('Are you sure you want to delete this lead status?', 'Delete Lead Status', function() {
        $.ajax({
            url: `/admin/lead-statuses/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'Lead status deleted successfully', 'success');
                    loadLeadStatuses();
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Error deleting lead status';
                showToast(errorMessage, 'error');
            }
        });
    });
}

function resetFilters() {
    $('input[name="search"]').val('');
    $('select[name="status"]').val('');
    loadLeadStatuses();
}
</script>
@endpush

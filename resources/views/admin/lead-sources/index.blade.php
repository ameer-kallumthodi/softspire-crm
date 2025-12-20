@extends('layouts.admin')

@section('title', 'Lead Sources')
@section('page-title', 'Lead Sources')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Lead Sources</li>
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
                    <h4 class="card-title">Lead Sources</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.lead-sources.ajax-add') }}', 'Add New Lead Source')">
                            <i data-feather="plus"></i> Add New Lead Source
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
                    <table class="table table-striped table-bordered" id="leadSourcesTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
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
<script src="{{ asset('assets/extra-libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    loadLeadSources();
    
    $('#leadSourcesTable').on('click', '.filter-btn', function() {
        loadLeadSources();
    });
});

function loadLeadSources() {
    const search = $('input[name="search"]').val();
    const status = $('select[name="status"]').val();
    
    $.ajax({
        url: '{{ route('admin.lead-sources.index') }}',
        method: 'GET',
        data: { search: search, status: status, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderLeadSourcesTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading lead sources';
            showToast(errorMessage, 'error');
            $('#leadSourcesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderLeadSourcesTable(leadSources) {
    let html = '';
    if (leadSources.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No lead sources found</td></tr>';
    } else {
        leadSources.forEach(function(leadSource, index) {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${leadSource.name}</td>
                    <td>
                        <span class="badge bg-${leadSource.status == 'active' ? 'success' : 'danger'}">
                            ${leadSource.status.charAt(0).toUpperCase() + leadSource.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(leadSource.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <a href="/admin/lead-sources/${leadSource.id}" class="btn btn-sm btn-info">
                            <i data-feather="eye"></i>
                        </a>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/lead-sources/${leadSource.id}/ajax/edit', 'Update Lead Source')">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-${leadSource.status == 'active' ? 'secondary' : 'success'}" onclick="toggleStatus(${leadSource.id})">
                            <i data-feather="${leadSource.status == 'active' ? 'x' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="delete_modal('/admin/lead-sources/${leadSource.id}')">
                            <i data-feather="trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#leadSourcesTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function toggleStatus(id) {
    $.ajax({
        url: `/admin/lead-sources/${id}/toggle-status`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Status updated successfully', 'success');
                loadLeadSources();
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Something went wrong';
            showToast(errorMessage, 'error');
        }
    });
}


function resetFilters() {
    $('input[name="search"]').val('');
    $('select[name="status"]').val('');
    loadLeadSources();
}
</script>
@endpush

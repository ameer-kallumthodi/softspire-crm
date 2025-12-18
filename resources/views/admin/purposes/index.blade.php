@extends('layouts.admin')

@section('title', 'Purposes')
@section('page-title', 'Purposes')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Purposes</li>
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
                    <h4 class="card-title">Purposes</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.purposes.ajax-add') }}', 'Add New Purpose')">
                            <i data-feather="plus"></i> Add New Purpose
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
                    <table class="table table-striped table-bordered" id="purposesTable">
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
<script src="{{ asset('assets/extra-libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    loadPurposes();
    
    $('#purposesTable').on('click', '.filter-btn', function() {
        loadPurposes();
    });
});

function loadPurposes() {
    const search = $('input[name="search"]').val();
    const status = $('select[name="status"]').val();
    
    $.ajax({
        url: '{{ route('admin.purposes.index') }}',
        method: 'GET',
        data: { search: search, status: status, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderPurposesTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading purposes';
            showToast(errorMessage, 'error');
            $('#purposesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderPurposesTable(purposes) {
    let html = '';
    if (purposes.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No purposes found</td></tr>';
    } else {
        purposes.forEach(function(purpose) {
            html += `
                <tr>
                    <td>${purpose.id}</td>
                    <td>${purpose.name}</td>
                    <td>
                        <span class="badge bg-${purpose.status == 'active' ? 'success' : 'danger'}">
                            ${purpose.status.charAt(0).toUpperCase() + purpose.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(purpose.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <a href="/admin/purposes/${purpose.id}" class="btn btn-sm btn-info">
                            <i data-feather="eye"></i>
                        </a>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/purposes/${purpose.id}/ajax/edit', 'Update Purpose')">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-${purpose.status == 'active' ? 'secondary' : 'success'}" onclick="toggleStatus(${purpose.id})">
                            <i data-feather="${purpose.status == 'active' ? 'x' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="delete_modal('/admin/purposes/${purpose.id}')">
                            <i data-feather="trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#purposesTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function toggleStatus(id) {
    $.ajax({
        url: `/admin/purposes/${id}/toggle-status`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Status updated successfully', 'success');
                loadPurposes();
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
    loadPurposes();
}
</script>
@endpush

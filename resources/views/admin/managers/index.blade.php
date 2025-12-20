@extends('layouts.admin')

@section('title', 'Managers Management')
@section('page-title', 'Managers Management')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Managers</li>
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
                    <h4 class="card-title">Managers</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="openAddManagerModal()">
                            <i data-feather="plus"></i> Add New Manager
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="managersTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">
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
    loadManagers();
    
    $('#managersTable').on('click', '.filter-btn', function() {
        loadManagers();
    });
});

function loadManagers() {
    const search = $('input[name="search"]').val();
    
    $.ajax({
        url: '{{ route('admin.managers.index') }}',
        method: 'GET',
        data: { search: search, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderManagersTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading managers';
            showToast(errorMessage, 'error');
            $('#managersTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderManagersTable(managers) {
    let html = '';
    if (managers.length === 0) {
        html = '<tr><td colspan="7" class="text-center">No managers found</td></tr>';
    } else {
        managers.forEach(function(manager, index) {
            const currentUserId = {{ auth()->id() }};
            const phoneDisplay = (manager.country_code && manager.phone) ? `${manager.country_code} ${manager.phone}` : (manager.phone || 'N/A');
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${manager.name}</td>
                    <td>${manager.email}</td>
                    <td>${phoneDisplay}</td>
                    <td>
                        <span class="badge bg-${manager.status == 'active' ? 'success' : 'danger'}">
                            ${manager.status.charAt(0).toUpperCase() + manager.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(manager.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/managers/${manager.id}/ajax/edit', 'Update Manager')" title="Edit Manager">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-info" onclick="show_ajax_modal('/admin/managers/${manager.id}/reset-password', 'Reset Password')" title="Reset Password">
                            <i data-feather="key"></i>
                        </button>
                        ${manager.id != currentUserId ? `<button class="btn btn-sm btn-danger" onclick="delete_modal('/admin/managers/${manager.id}')" title="Delete Manager">
                            <i data-feather="trash"></i>
                        </button>` : ''}
                    </td>
                </tr>
            `;
        });
    }
    $('#managersTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        try {
            $('[data-feather]').each(function() {
                try {
                    var iconName = $(this).attr('data-feather');
                    if (iconName && feather.icons && feather.icons[iconName]) {
                        var svg = feather.icons[iconName].toSvg({
                            'stroke-width': 2,
                            width: 20,
                            height: 20
                        });
                        $(this).replaceWith(svg);
                    }
                } catch(err) {
                    console.warn('Error replacing icon:', $(this).attr('data-feather'), err);
                }
            });
        } catch(e) {
            console.warn('Feather icons error:', e);
        }
    }
}

function resetFilters() {
    $('input[name="search"]').val('');
    loadManagers();
}

function openAddManagerModal() {
    show_ajax_modal('{{ route("admin.managers.ajax-add") }}', 'Add New Manager');
}

</script>
@endpush

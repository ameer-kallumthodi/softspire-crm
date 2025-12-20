@extends('layouts.admin')

@section('title', 'Telecallers Management')
@section('page-title', 'Telecallers Management')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Telecallers</li>
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
                    <h4 class="card-title">Telecallers</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="openAddTelecallerModal()">
                            <i data-feather="plus"></i> Add New Telecaller
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
                    <table class="table table-striped table-bordered" id="telecallersTable">
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
    loadTelecallers();
    
    $('#telecallersTable').on('click', '.filter-btn', function() {
        loadTelecallers();
    });
});

function loadTelecallers() {
    const search = $('input[name="search"]').val();
    
    $.ajax({
        url: '{{ route('admin.telecallers.index') }}',
        method: 'GET',
        data: { search: search, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderTelecallersTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading telecallers';
            showToast(errorMessage, 'error');
            $('#telecallersTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderTelecallersTable(telecallers) {
    let html = '';
    if (telecallers.length === 0) {
        html = '<tr><td colspan="7" class="text-center">No telecallers found</td></tr>';
    } else {
        telecallers.forEach(function(telecaller, index) {
            const currentUserId = {{ auth()->id() }};
            const phoneDisplay = (telecaller.country_code && telecaller.phone) ? `${telecaller.country_code} ${telecaller.phone}` : (telecaller.phone || 'N/A');
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${telecaller.name}</td>
                    <td>${telecaller.email}</td>
                    <td>${phoneDisplay}</td>
                    <td>
                        <span class="badge bg-${telecaller.status == 'active' ? 'success' : 'danger'}">
                            ${telecaller.status.charAt(0).toUpperCase() + telecaller.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(telecaller.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/telecallers/${telecaller.id}/ajax/edit', 'Update Telecaller')" title="Edit Telecaller">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-info" onclick="show_ajax_modal('/admin/telecallers/${telecaller.id}/reset-password', 'Reset Password')" title="Reset Password">
                            <i data-feather="key"></i>
                        </button>
                        ${telecaller.id != currentUserId ? `<button class="btn btn-sm btn-danger" onclick="delete_modal('/admin/telecallers/${telecaller.id}')" title="Delete Telecaller">
                            <i data-feather="trash"></i>
                        </button>` : ''}
                    </td>
                </tr>
            `;
        });
    }
    $('#telecallersTable tbody').html(html);
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
    loadTelecallers();
}

function openAddTelecallerModal() {
    show_ajax_modal('{{ route("admin.telecallers.ajax-add") }}', 'Add New Telecaller');
}

</script>
@endpush

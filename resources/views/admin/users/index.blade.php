@extends('layouts.admin')

@section('title', isset($filters['role']) && $filters['role'] == 'manager' ? 'Managers Management' : (isset($filters['role']) && $filters['role'] == 'telecaller' ? 'Telecallers Management' : 'Users Management'))
@section('page-title', isset($filters['role']) && $filters['role'] == 'manager' ? 'Managers Management' : (isset($filters['role']) && $filters['role'] == 'telecaller' ? 'Telecallers Management' : 'Users Management'))
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">{{ isset($filters['role']) && $filters['role'] == 'manager' ? 'Managers' : (isset($filters['role']) && $filters['role'] == 'telecaller' ? 'Telecallers' : 'Users') }}</li>
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
                    <h4 class="card-title">{{ isset($filters['role']) && $filters['role'] == 'manager' ? 'Managers' : (isset($filters['role']) && $filters['role'] == 'telecaller' ? 'Telecallers' : 'Users') }}</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="openAddUserModal()">
                            <i data-feather="plus"></i> Add New {{ isset($filters['role']) && $filters['role'] == 'manager' ? 'Manager' : (isset($filters['role']) && $filters['role'] == 'telecaller' ? 'Telecaller' : 'User') }}
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="{{ $filters['search'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">
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
// Store current role filter for form reset
var currentRoleFilter = '{{ $filters["role"] ?? "" }}';

$(document).ready(function() {
    loadUsers();
    
    $('#usersTable').on('click', '.filter-btn', function() {
        loadUsers();
    });
});

function loadUsers() {
    const search = $('input[name="search"]').val();
    const role = currentRoleFilter;
    
    $.ajax({
        url: '{{ route('admin.users.index') }}',
        method: 'GET',
        data: { search: search, role: role, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderUsersTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading users';
            showToast(errorMessage, 'error');
            $('#usersTable tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderUsersTable(users) {
    let html = '';
    if (users.length === 0) {
        html = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
    } else {
        users.forEach(function(user) {
            const currentUserId = {{ auth()->id() }};
            html += `
                <tr>
                    <td>${user.id}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>
                        <span class="badge bg-${user.status == 'active' ? 'success' : 'danger'}">
                            ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/users/${user.id}/ajax/edit', 'Update User')">
                            <i data-feather="edit"></i>
                        </button>
                        ${user.id != currentUserId ? `<button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                            <i data-feather="trash"></i>
                        </button>` : ''}
                    </td>
                </tr>
            `;
        });
    }
    $('#usersTable tbody').html(html);
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
    loadUsers();
}

function openAddUserModal() {
    const roleFilter = currentRoleFilter || '';
    const title = roleFilter === 'manager' ? 'Add New Manager' : (roleFilter === 'telecaller' ? 'Add New Telecaller' : 'Add New User');
    const url = '{{ route("admin.users.ajax-add") }}' + (roleFilter ? '?role=' + roleFilter : '');
    show_ajax_modal(url, title);
}

function deleteUser(id) {
    showConfirmModal('Are you sure you want to delete this user?', 'Delete User', function() {
        $.ajax({
            url: `/admin/users/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'User deleted successfully', 'success');
                    loadUsers();
                } else {
                    showToast(response.message || 'Error deleting user', 'error');
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Error deleting user';
                showToast(errorMessage, 'error');
            }
        });
    });
}
</script>
@endpush


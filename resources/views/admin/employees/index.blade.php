@extends('layouts.admin')

@section('title', 'Employees Management')
@section('page-title', 'Employees Management')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Employees</li>
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
                    <h4 class="card-title">Employees</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="openAddEmployeeModal()">
                            <i data-feather="plus"></i> Add New Employee
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search name, email or employee ID..." value="">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="employeesTable">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="text-center">
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
    loadEmployees();
    
    $('#employeesTable').on('click', '.filter-btn', function() {
        loadEmployees();
    });
});

function loadEmployees() {
    const search = $('input[name="search"]').val();
    
    $.ajax({
        url: '{{ route('admin.employees.index') }}',
        method: 'GET',
        data: { search: search, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderEmployeesTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading employees';
            showToast(errorMessage, 'error');
            $('#employeesTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderEmployeesTable(employees) {
    let html = '';
    if (employees.length === 0) {
        html = '<tr><td colspan="9" class="text-center">No employees found</td></tr>';
    } else {
        employees.forEach(function(employee, index) {
            const currentUserId = {{ auth()->id() }};
            const phoneDisplay = (employee.country_code && employee.phone) ? `${employee.country_code} ${employee.phone}` : (employee.phone || 'N/A');
            const departmentName = employee.department ? employee.department.name : 'N/A';
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${employee.employee_id || 'N/A'}</td>
                    <td>${employee.name}</td>
                    <td>${employee.email}</td>
                    <td>${phoneDisplay}</td>
                    <td>${departmentName}</td>
                    <td>
                        <span class="badge bg-${employee.status == 'active' ? 'success' : 'danger'}">
                            ${employee.status.charAt(0).toUpperCase() + employee.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(employee.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/employees/${employee.id}/ajax/edit', 'Update Employee')" title="Edit Employee">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-info" onclick="show_ajax_modal('/admin/employees/${employee.id}/reset-password', 'Reset Password')" title="Reset Password">
                            <i data-feather="key"></i>
                        </button>
                        ${employee.id != currentUserId ? `<button class="btn btn-sm btn-danger" onclick="delete_modal('/admin/employees/${employee.id}')" title="Delete Employee">
                            <i data-feather="trash"></i>
                        </button>` : ''}
                    </td>
                </tr>
            `;
        });
    }
    $('#employeesTable tbody').html(html);
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
    loadEmployees();
}

function openAddEmployeeModal() {
    show_ajax_modal('{{ route("admin.employees.ajax-add") }}', 'Add New Employee');
}

</script>
@endpush


@extends('layouts.admin')

@section('title', 'Countries')
@section('page-title', 'Countries')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Countries</li>
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
                    <h4 class="card-title">Countries</h4>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.countries.ajax-add') }}', 'Add New Country')">
                            <i data-feather="plus"></i> Add New Country
                        </button>
                    </div>
                </div>

                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="countriesTable">
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
let countriesTable;
$(document).ready(function() {
    // Wait a bit to ensure all scripts are loaded
    setTimeout(function() {
        loadCountries();
    }, 100);
    
    $('#countriesTable').on('click', '.filter-btn', function() {
        loadCountries();
    });
});

function loadCountries() {
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    
    const search = $('input[name="search"]').val();
    const status = $('select[name="status"]').val();
    
    $.ajax({
        url: '{{ route('admin.countries.index') }}',
        method: 'GET',
        data: { search: search, status: status, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response && response.success) {
                renderCountriesTable(response.data);
            } else {
                $('#countriesTable tbody').html('<tr><td colspan="5" class="text-center text-warning">No data available</td></tr>');
            }
        },
        error: function(xhr) {
            console.error('Error loading countries:', xhr);
            const errorMessage = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error loading countries';
            if (typeof showToast === 'function') {
                showToast(errorMessage, 'error');
            }
            $('#countriesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data. Please refresh the page.</td></tr>');
        }
    });
}

function renderCountriesTable(countries) {
    let html = '';
    if (countries.length === 0) {
        html = '<tr><td colspan="5" class="text-center">No countries found</td></tr>';
    } else {
        countries.forEach(function(country, index) {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${country.name}</td>
                    <td>
                        <span class="badge bg-${country.status == 'active' ? 'success' : 'danger'}">
                            ${country.status.charAt(0).toUpperCase() + country.status.slice(1)}
                        </span>
                    </td>
                    <td>${new Date(country.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <a href="/admin/countries/${country.id}" class="btn btn-sm btn-info">
                            <i data-feather="eye"></i>
                        </a>
                        <button class="btn btn-sm btn-warning" onclick="show_ajax_modal('/admin/countries/${country.id}/ajax/edit', 'Update Country')">
                            <i data-feather="edit"></i>
                        </button>
                        <button class="btn btn-sm btn-${country.status == 'active' ? 'secondary' : 'success'}" onclick="toggleStatus(${country.id})">
                            <i data-feather="${country.status == 'active' ? 'x' : 'check'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="delete_modal('/admin/countries/${country.id}')">
                            <i data-feather="trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#countriesTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function toggleStatus(id) {
    $.ajax({
        url: `/admin/countries/${id}/toggle-status`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message || 'Status updated successfully', 'success');
                loadCountries();
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
    loadCountries();
}
</script>
@endpush


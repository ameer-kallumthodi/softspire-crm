@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customers')
@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Customers</li>
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
                    <h4 class="card-title">Customers</h4>
                </div>

                <form id="filterForm" class="mb-3" onsubmit="return false;">
                    <div class="row">
                        <div class="col-md-4">
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
                            <button type="button" class="btn btn-info filter-btn">Filter</button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="customersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Country</th>
                                <th>Purpose</th>
                                <th>Converted Date</th>
                                <th>Converted By</th>
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
    loadCustomers();
    
    $('#customersTable').on('click', '.filter-btn', function() {
        loadCustomers();
    });
});

function loadCustomers() {
    const search = $('input[name="search"]').val();
    const countryId = $('#countryFilter').val();
    
    $.ajax({
        url: '{{ route('admin.customers.index') }}',
        method: 'GET',
        data: { search: search, country_id: countryId, ajax: true },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            if (response.success) {
                renderCustomersTable(response.data);
            }
        },
        error: function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error loading customers';
            showToast(errorMessage, 'error');
            $('#customersTable tbody').html('<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>');
        }
    });
}

function renderCustomersTable(customers) {
    let html = '';
    if (customers.length === 0) {
        html = '<tr><td colspan="9" class="text-center">No customers found</td></tr>';
    } else {
        customers.forEach(function(customer) {
            const convertedDate = new Date(customer.converted_date);
            html += `
                <tr>
                    <td>${customer.id}</td>
                    <td>${customer.name}</td>
                    <td>${customer.country_code} ${customer.phone}</td>
                    <td>${customer.email || 'N/A'}</td>
                    <td>${customer.country ? customer.country.name : 'N/A'}</td>
                    <td>${customer.purpose ? customer.purpose.name : 'N/A'}</td>
                    <td>${convertedDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>${customer.converted_by && customer.converted_by.name ? customer.converted_by.name : 'N/A'}</td>
                    <td>
                        <a href="/admin/customers/${customer.id}" class="btn btn-sm btn-info" title="View Customer">
                            <i data-feather="eye"></i>
                        </a>
                        <a href="/admin/customers/${customer.id}/quotations" class="btn btn-sm btn-primary" title="View Quotations">
                            <i data-feather="file-text"></i> Quotations
                        </a>
                    </td>
                </tr>
            `;
        });
    }
    $('#customersTable tbody').html(html);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function resetFilters() {
    $('input[name="search"]').val('');
    $('#countryFilter').val('');
    loadCustomers();
}
</script>
@endpush

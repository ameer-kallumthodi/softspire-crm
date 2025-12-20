@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'Payments')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active" aria-current="page">Payments</li>
@endsection

@push('styles')
<style>
    .payment-card {
        border-left: 4px solid #28a745;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    .payment-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .pending-quotation-card {
        border-left: 4px solid #ffc107;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }
    .pending-quotation-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i data-feather="credit-card" class="me-2"></i>Payments Management
                    </h5>
                    <div>
                        <a href="{{ route('admin.payments.pending-quotations') }}" class="btn btn-sm btn-warning me-2">
                            <i data-feather="clock"></i> Pending Quotations
                        </a>
                        <a href="{{ route('admin.payments.create') }}" class="btn btn-sm btn-success">
                            <i data-feather="plus"></i> Add Payment
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- All Payments Section -->
                <div>
                    <h5 class="mb-3">
                        <i data-feather="list" class="me-2"></i>All Payments
                    </h5>
                    @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Payment Number</th>
                                    <th>Quotation</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Payment Type</th>
                                    <th>Transaction ID</th>
                                    <th>Payment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td><strong>{{ $payment->payment_number }}</strong></td>
                                    <td>{{ $payment->quotation->quotation_number }}</td>
                                    <td>{{ $payment->quotation->customer->name }}</td>
                                    <td><strong>&#8377;{{ number_format($payment->amount, 2) }}</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span>
                                    </td>
                                    <td>{{ $payment->transaction_id }}</td>
                                    <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payments.receipt', $payment) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i data-feather="file-text"></i> Receipt PDF
                                            </a>
                                            @if($payment->receipt_path)
                                            <a href="{{ asset('storage/' . $payment->receipt_path) }}" class="btn btn-sm btn-secondary" target="_blank" title="View Uploaded Receipt">
                                                <i data-feather="download"></i> Receipt
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i data-feather="info" class="me-2"></i>No payments found.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush

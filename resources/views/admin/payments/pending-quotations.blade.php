@extends('layouts.admin')

@section('title', 'Pending Quotations')
@section('page-title', 'Pending Quotations')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments</a></li>
<li class="breadcrumb-item active" aria-current="page">Pending Quotations</li>
@endsection

@push('styles')
<style>
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
            <div class="card-header bg-warning-subtle">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <i data-feather="clock" class="me-2"></i>Pending Quotations
                    </h5>
                    <div>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary me-2">
                            <i data-feather="arrow-left"></i> Back to Payments
                        </a>
                        <a href="{{ route('admin.payments.create') }}" class="btn btn-sm btn-success">
                            <i data-feather="plus"></i> Add Payment
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($pendingQuotations->count() > 0)
                <div class="row">
                    @foreach($pendingQuotations as $quotation)
                    <div class="col-md-6 mb-3">
                        <div class="card pending-quotation-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $quotation->quotation_number }}</h6>
                                        <small class="text-muted">{{ $quotation->customer->name }}</small>
                                    </div>
                                    <span class="badge bg-warning">Pending</span>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Total Amount:</span>
                                        <strong>&#8377;{{ number_format($quotation->total_amount, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Paid Amount:</span>
                                        <strong class="text-success">&#8377;{{ number_format($quotation->total_paid, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Pending Amount:</span>
                                        <strong class="text-danger">&#8377;{{ number_format($quotation->pending_amount, 2) }}</strong>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.payments.create', ['quotation_id' => $quotation->id]) }}" class="btn btn-sm btn-success">
                                            <i data-feather="plus"></i> Add Payment
                                        </a>
                                        <a href="{{ route('admin.quotations.pdf', $quotation) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i data-feather="file-text"></i> View Quotation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i data-feather="check-circle" style="width: 64px; height: 64px; color: #28a745;"></i>
                    <p class="text-muted mt-3">No pending quotations found.</p>
                    <p class="text-muted">All accepted quotations have been fully paid.</p>
                </div>
                @endif
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

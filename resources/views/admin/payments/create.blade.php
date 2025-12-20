@extends('layouts.admin')

@section('title', 'Add Payment')
@section('page-title', 'Add Payment')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Payments</a></li>
<li class="breadcrumb-item active" aria-current="page">Add Payment</li>
@endsection

@push('styles')
<style>
    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .amount-display {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .pending-amount {
        color: #dc3545;
    }
    .total-amount {
        color: #28a745;
    }
</style>
@endpush

@section('content')
@if($errors->any())
<div class="row">
    <div class="col-12">
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
<div class="row">
    <div class="col-12">
        <form id="paymentForm" method="POST" action="{{ route('admin.payments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="card mb-3">
                <div class="card-header bg-primary-subtle">
                    <h5 class="card-title mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Quotation <span class="text-danger">*</span></label>
                                <select name="quotation_id" id="quotation_id" class="form-select" required>
                                    <option value="">Select Quotation</option>
                                    @foreach($quotations as $quotation)
                                    @php
                                        $isSelected = old('quotation_id', (isset($selectedQuotationId) && $selectedQuotationId == $quotation->id) ? $quotation->id : (request('quotation_id') == $quotation->id ? $quotation->id : null)) == $quotation->id;
                                    @endphp
                                    <option value="{{ $quotation->id }}" 
                                        data-total="{{ $quotation->total_amount }}"
                                        data-pending="{{ $quotation->pending_amount }}"
                                        data-paid="{{ $quotation->total_paid }}"
                                        {{ $isSelected ? 'selected' : '' }}>
                                        {{ $quotation->quotation_number }} - {{ $quotation->customer->name }} (Pending: &#8377;{{ number_format($quotation->pending_amount, 2) }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Only accepted quotations with pending payments are shown</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Information Box -->
                    <div class="info-box" id="amountInfoBox" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="text-muted mb-1">Total Amount</div>
                                    <div class="amount-display total-amount" id="totalAmountDisplay">&#8377;0.00</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="text-muted mb-1">Paid Amount</div>
                                    <div class="amount-display text-info" id="paidAmountDisplay">&#8377;0.00</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="text-muted mb-1">Pending Amount</div>
                                    <div class="amount-display pending-amount" id="pendingAmountDisplay">&#8377;0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                                <small class="text-muted">Maximum: <span id="maxAmount">&#8377;0.00</span></small>
                                <div class="invalid-feedback" id="amountError" style="display: none;">
                                    Amount cannot exceed pending amount
                                </div>
                                @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Payment Type <span class="text-danger">*</span></label>
                                <select name="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                                    <option value="online" {{ old('payment_type', 'online') == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ old('payment_type') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cheque" {{ old('payment_type') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="other" {{ old('payment_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('payment_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Transaction ID <span class="text-danger">*</span></label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" placeholder="Enter transaction ID" value="{{ old('transaction_id') }}" required>
                                <small class="text-muted" id="transactionIdHelp"></small>
                                <div class="invalid-feedback" id="transactionIdError" style="display: none;">
                                    This Transaction ID already exists
                                </div>
                                @error('transaction_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Receipt Upload <span class="text-danger">*</span></label>
                                <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 5MB)</small>
                                @error('receipt')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-success">
                    <i data-feather="save"></i> Add Payment
                </button>
            </div>
        </form>
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

    // Handle quotation selection change
    $('#quotation_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const totalAmount = parseFloat(selectedOption.data('total')) || 0;
        const paidAmount = parseFloat(selectedOption.data('paid')) || 0;
        const pendingAmount = parseFloat(selectedOption.data('pending')) || 0;

        if ($(this).val()) {
            $('#amountInfoBox').slideDown();
            $('#totalAmountDisplay').html('&#8377;' + totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#paidAmountDisplay').html('&#8377;' + paidAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#pendingAmountDisplay').html('&#8377;' + pendingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#maxAmount').html('&#8377;' + pendingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#amount').attr('max', pendingAmount);
            $('#amount').val('');
        } else {
            $('#amountInfoBox').slideUp();
        }
    });

    // Validate amount on input
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        const pendingAmount = parseFloat($('#quotation_id').find('option:selected').data('pending')) || 0;
        
        if (amount > pendingAmount) {
            $(this).addClass('is-invalid');
            $('#amountError').show();
            $(this).val(pendingAmount);
        } else {
            $(this).removeClass('is-invalid');
            $('#amountError').hide();
        }
    });

    // Form submission validation
    $('#paymentForm').on('submit', function(e) {
        const amount = parseFloat($('#amount').val()) || 0;
        const pendingAmount = parseFloat($('#quotation_id').find('option:selected').data('pending')) || 0;
        
        if (amount > pendingAmount) {
            e.preventDefault();
            
            // Show error modal
            $('#confirmModalTitle').text('Invalid Amount');
            $('#confirmModalBody').html('<div class="alert alert-danger mb-0">' +
                '<i data-feather="alert-circle" class="me-2"></i>' +
                'Payment amount cannot exceed pending amount of <strong>₹' + 
                pendingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong>' +
                '</div>');
            $('#confirmModalBtn').text('Okay').removeClass('btn-primary').addClass('btn-danger');
            
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
            
            // Remove previous event listeners
            $('#confirmModalBtn').off('click');
            
            // Handle button click to close modal
            $('#confirmModalBtn').on('click', function() {
                modal.hide();
                $('#confirmModalBtn').text('Confirm').removeClass('btn-danger').addClass('btn-primary');
            });
            
            // Reinitialize feather icons
            if (typeof feather !== 'undefined') {
                setTimeout(function() {
                    feather.replace();
                }, 100);
            }
            
            return false;
        }
    });

    // Trigger change on page load if quotation is pre-selected
    if ($('#quotation_id').val()) {
        $('#quotation_id').trigger('change');
    }
});
</script>
@endpush

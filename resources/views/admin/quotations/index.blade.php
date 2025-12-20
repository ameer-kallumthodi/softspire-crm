@extends('layouts.admin')

@section('title', 'Customer Quotations')
@section('page-title', 'Customer Quotations')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a></li>
<li class="breadcrumb-item active" aria-current="page">Quotations</li>
@endsection

@push('styles')
<style>
    .quotation-card {
        border-left: 4px solid #dc3545;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    .quotation-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .pdf-viewer-container {
        width: 100%;
        height: 800px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-top: 20px;
    }
    .pdf-viewer-container iframe {
        width: 100%;
        height: 100%;
        border: none;
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
                        <i data-feather="file-text" class="me-2"></i>Quotations for {{ $customer->name }}
                    </h5>
                    <div>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-secondary">
                            <i data-feather="arrow-left"></i> Back to Customer
                        </a>
                        <button class="btn btn-sm btn-primary" onclick="show_ajax_modal('{{ route('admin.quotations.create', $customer) }}', 'Generate Quotation')">
                            <i data-feather="plus"></i> Generate New Quotation
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($quotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Quotation Number</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Duration</th>
                                <th>Technologies</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotations as $quotation)
                            <tr>
                                <td><strong>{{ $quotation->quotation_number }}</strong></td>
                                <td>{{ $quotation->quotation_date->format('d M, Y') }}</td>
                                <td>₹{{ number_format($quotation->total_amount, 2) }}</td>
                                <td>{{ $quotation->duration_months ?? 'N/A' }}</td>
                                <td>{{ $quotation->technologies ?? 'N/A' }}</td>
                                <td>
                                    @if($quotation->is_accepted)
                                        <span class="badge bg-success">Accepted</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-info view-pdf-btn" data-quotation-id="{{ $quotation->id }}" data-url="{{ route('admin.quotations.pdf', $quotation) }}">
                                            <i data-feather="eye"></i> View PDF
                                        </button>
                                        @if(!$quotation->is_accepted)
                                        <button class="btn btn-sm btn-success accept-quotation-btn" data-quotation-id="{{ $quotation->id }}" data-url="{{ route('admin.quotations.accept', $quotation) }}">
                                            <i data-feather="check"></i> Accept
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- PDF Viewer Section -->
                <div id="pdfViewerSection" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>PDF Preview</h5>
                        <button class="btn btn-sm btn-secondary" onclick="closePdfViewer()">
                            <i data-feather="x"></i> Close
                        </button>
                    </div>
                    <div class="pdf-viewer-container">
                        <iframe id="pdfViewer" src=""></iframe>
                    </div>
                </div>
                @else
                <div class="text-center py-5">
                    <i data-feather="file-text" style="width: 64px; height: 64px; color: #ccc;"></i>
                    <p class="text-muted mt-3">No quotations found for this customer.</p>
                    <button class="btn btn-primary" onclick="show_ajax_modal('{{ route('admin.quotations.create', $customer) }}', 'Generate Quotation')">
                        <i data-feather="plus"></i> Generate First Quotation
                    </button>
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
    
    // Handle PDF view button click
    $(document).on('click', '.view-pdf-btn', function() {
        const pdfUrl = $(this).data('url');
        const quotationId = $(this).data('quotation-id');
        
        // Show PDF viewer section
        $('#pdfViewerSection').slideDown();
        
        // Load PDF in iframe
        $('#pdfViewer').attr('src', pdfUrl);
        
        // Scroll to PDF viewer
        $('html, body').animate({
            scrollTop: $('#pdfViewerSection').offset().top - 100
        }, 500);
    });
    
    // Handle Accept quotation button click
    $(document).on('click', '.accept-quotation-btn', function(e) {
        e.preventDefault();
        const button = $(this);
        const quotationId = button.data('quotation-id');
        const acceptUrl = button.data('url');
        
        // Show confirmation modal
        $('#confirmModalTitle').text('Accept Quotation');
        $('#confirmModalBody').html('<p>Are you sure you want to accept this quotation?</p><p class="text-muted small">This action will mark the quotation as accepted.</p>');
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
        
        // Remove previous event listeners
        $('#confirmModalBtn').off('click');
        
        // Handle confirm button click
        $('#confirmModalBtn').on('click', function() {
            modal.hide();
            
            // Disable button to prevent double-click
            button.prop('disabled', true);
            const originalHtml = button.html();
            button.html('<i data-feather="loader"></i> Processing...');
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            
            // Send AJAX request
            $.ajax({
                url: acceptUrl,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message || 'Quotation accepted successfully', 'success');
                        // Reload the page to update the status
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(response.message || 'Failed to accept quotation', 'error');
                        button.prop('disabled', false).html(originalHtml);
                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'An error occurred while accepting the quotation';
                    showToast(message, 'error');
                    button.prop('disabled', false).html(originalHtml);
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                }
            });
        });
    });
});

function closePdfViewer() {
    $('#pdfViewerSection').slideUp();
    $('#pdfViewer').attr('src', '');
}
</script>
@endpush

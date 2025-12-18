<form id="quotationForm" method="POST" action="{{ route('admin.quotations.store', $customer) }}">
    @csrf
    <div class="card mb-3">
        <div class="card-header bg-primary-subtle">
            <h5 class="card-title mb-0">Quotation Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Customer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $customer->name }}" readonly>
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Quotation Date <span class="text-danger">*</span></label>
                        <input type="date" name="quotation_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Duration (in months)</label>
                        <input type="text" name="duration_months" class="form-control" placeholder="Enter duration in months (e.g., 2 or 2 - 3)">
                        <small class="text-muted">You can enter a single number (e.g., 2) or a range (e.g., 2 - 3)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Technologies</label>
                        <input type="text" name="technologies" class="form-control" placeholder="Enter technologies (comma separated)">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Total Amount <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Annual Amount</label>
                        <input type="number" name="annual_amount" class="form-control" step="0.01" min="0" placeholder="Enter annual maintenance amount">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-primary-subtle">
            <h5 class="card-title mb-0">Quotation Items</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Item Name <span class="text-danger">*</span></th>
                            <th>Amount <span class="text-danger">*</span></th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="items[0][item_name]" class="form-control" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][amount]" class="form-control item-amount" step="0.01" min="0" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][description]" class="form-control" placeholder="Item description">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-row">
                                    <i data-feather="trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="button" class="btn btn-primary" id="addItemRow">
                                    <i data-feather="plus"></i> Add Item
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Generate Quotation PDF</button>
    </div>
</form>

<script>
$(document).ready(function() {
    let itemIndex = 1;

    // Add new row
    $('#addItemRow').on('click', function() {
        const newRow = `
            <tr>
                <td>
                    <input type="text" name="items[${itemIndex}][item_name]" class="form-control" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][amount]" class="form-control item-amount" step="0.01" min="0" required>
                </td>
                <td>
                    <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Item description">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-row">
                        <i data-feather="trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#itemsTable tbody').append(newRow);
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        itemIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        if ($('#itemsTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotal();
        } else {
            showToast('At least one item is required', 'error');
        }
    });

    // Calculate total when item amount changes
    $(document).on('input', '.item-amount', function() {
        calculateTotal();
    });

    function calculateTotal() {
        let total = 0;
        $('.item-amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_amount').val(total.toFixed(2));
    }

    // Form submission
    $('#quotationForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('Quotation created successfully!', 'success');
                    $('#ajax_modal').modal('hide');
                    // Reload page or redirect to quotations list
                    if (typeof loadCustomers === 'function') {
                        loadCustomers();
                    }
                    // Redirect to customer quotations page
                    setTimeout(function() {
                        window.location.href = '/admin/customers/{{ $customer->id }}/quotations';
                    }, 1000);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error creating quotation';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    }
                }
                showToast(errorMessage, 'error');
            }
        });
    });
});
</script>


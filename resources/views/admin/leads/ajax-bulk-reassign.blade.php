<form action="{{ route('admin.leads.bulk-reassign.submit') }}" method="post" enctype="multipart/form-data" id="bulkReassignForm">
    @csrf
    <div class="row g-3">
        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="telecaller_id" class="form-label">Re-assign To <span class="text-danger">*</span></label>
                <select class="form-control @error('telecaller_id') is-invalid @enderror" name="telecaller_id" id="telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                    <option value="{{ $telecaller->id }}" {{ old('telecaller_id') == $telecaller->id ? 'selected' : '' }}>{{ $telecaller->name }}</option>
                    @endforeach
                </select>
                @error('telecaller_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="lead_source_id" class="form-label">Lead Source <span class="text-danger">*</span></label>
                <select class="form-control @error('lead_source_id') is-invalid @enderror" name="lead_source_id" id="lead_source_id" required>
                    <option value="">Select Source</option>
                    @foreach ($leadSources as $source)
                    <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                    @endforeach
                </select>
                @error('lead_source_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="lead_status_id" class="form-label">Lead Status <span class="text-danger">*</span></label>
                <select class="form-control @error('lead_status_id') is-invalid @enderror" name="lead_status_id" id="lead_status_id" required>
                    <option value="">Select Status</option>
                    @foreach ($leadStatuses as $status)
                    <option value="{{ $status->id }}" {{ old('lead_status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
                @error('lead_status_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="purpose_id" class="form-label">Purpose <small class="text-muted">(Optional)</small></label>
                <select class="form-control" name="purpose_id" id="reassign_purpose_id">
                    <option value="">All Purposes</option>
                    @foreach ($purposes as $purpose)
                    <option value="{{ $purpose->id }}" {{ old('purpose_id') == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="from_telecaller_id" class="form-label">Re-assign From <span class="text-danger">*</span></label>
                <select class="form-control @error('from_telecaller_id') is-invalid @enderror" name="from_telecaller_id" id="from_telecaller_id" required>
                    <option value="">Select Telecaller</option>
                    @foreach ($telecallers as $telecaller)
                        <option value="{{ $telecaller->id }}" {{ old('from_telecaller_id') == $telecaller->id ? 'selected' : '' }}>{{ $telecaller->name }}</option>
                    @endforeach
                </select>
                @error('from_telecaller_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="lead_from_date" class="form-label">From Date <span class="text-danger">*</span></label>
                <input type="date" id="lead_from_date" name="lead_from_date" class="form-control @error('lead_from_date') is-invalid @enderror" value="{{ old('lead_from_date') }}" required>
                @error('lead_from_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-3 col-md-4">
            <div class="p-1">
                <label for="lead_to_date" class="form-label">To Date <span class="text-danger">*</span></label>
                <input type="date" id="lead_to_date" name="lead_to_date" class="form-control @error('lead_to_date') is-invalid @enderror" value="{{ old('lead_to_date') }}" required>
                @error('lead_to_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-12 pt-2">
            <label for="assign_all_counselor">Select Leads to be Re-assigned.</label>
            <!-- New Number Input for Selecting Top Leads -->
            <div class="d-flex justify-content-end">
                <div class="col-lg-2">
                    <div class="p-1">
                        <label for="select_count" class="form-label">Count</label>
                        <input type="number" id="select_count" class="form-control" min="1" placeholder="Enter count">
                    </div>
                </div>
            </div>
            <div id="telecaller_list">
                <hr>
                <div class="table-responsive bulk-operations-table">
                    <table class="table table-striped table-bordered bulk-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">#</th>
                                <th style="width: 20%;">Lead</th>
                                <th style="width: 15%;">Lead Status</th>
                                <th style="width: 15%;">Purpose</th>
                                <th style="width: 20%;">Remarks</th>
                                <th style="width: 15%; white-space: nowrap;">Date</th>
                                <th style="width: 10%;">Action <input type="checkbox" id="check_all" class="bulk-checkbox"></th>
                            </tr>
                        </thead>
                        <tbody id="lead_table_body">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Please select filters above to load leads</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <button class="btn btn-success float-end" type="submit" id="reassign_btn" disabled>Re-Assign</button>
            <button type="button" class="btn btn-secondary float-end me-2" data-bs-dismiss="modal">Cancel</button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Handle "Check All" functionality
    $('#check_all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#lead_table_body input[type="checkbox"]').prop('checked', isChecked);
        toggleSubmitButton();
    });

    // Handle individual checkbox changes
    $('#lead_table_body').on('change', 'input[type="checkbox"]', function() {
        toggleSubmitButton();
    });

    // Enable/Disable submit button based on checkbox selection
    function toggleSubmitButton() {
        var anyChecked = $('#lead_table_body input[type="checkbox"]:checked').length > 0;
        $('#reassign_btn').prop('disabled', !anyChecked);
    }

    // Select checkboxes based on entered number
    $('#select_count').on('input', function() {
        var count = parseInt($(this).val()) || 0;
        var checkboxes = $('#lead_table_body input[type="checkbox"]');

        // Uncheck all first
        checkboxes.prop('checked', false);

        // Check only the specified number of checkboxes
        checkboxes.slice(0, count).prop('checked', true);

        toggleSubmitButton();
    });
    
    // AJAX to fetch leads
    $('#from_telecaller_id, #lead_source_id, #lead_from_date, #lead_to_date, #lead_status_id, #reassign_purpose_id').on('change', function() {
        var leadSourceId = $('#lead_source_id').val();
        var leadStatusId = $('#lead_status_id').val();
        var teleCallerId = $('#from_telecaller_id').val();
        var leadFromDate = $('#lead_from_date').val();
        var leadToDate = $('#lead_to_date').val();
        var purposeId = $('#reassign_purpose_id').val();

        if (leadSourceId && teleCallerId && leadFromDate && leadToDate && leadStatusId) {
            $.ajax({
                url: '{{ route("admin.leads.get-by-source-reassign") }}',
                type: 'POST',
                data: { 
                    lead_source_id: leadSourceId, 
                    tele_caller_id: teleCallerId, 
                    from_date: leadFromDate, 
                    to_date: leadToDate, 
                    lead_status_id: leadStatusId,
                    purpose_id: purposeId || ''
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#lead_table_body').html(response);
                    $('#check_all').prop('checked', false);
                    toggleSubmitButton();
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                },
                error: function(xhr) {
                    console.log('Error fetching leads:', xhr.responseText);
                    $('#lead_table_body').html('<tr><td colspan="7" class="text-center text-danger">Error loading leads. Please try again.</td></tr>');
                }
            });
        } else {
            $('#lead_table_body').html('<tr><td colspan="7" class="text-center text-muted">Please select all required filters to load leads</td></tr>');
            toggleSubmitButton();
        }
    });

    // Form submission
    $('#bulkReassignForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check if any leads are selected
        var selectedLeads = $('#lead_table_body input[type="checkbox"]:checked').length;
        if (selectedLeads === 0) {
            if (typeof showToast !== 'undefined') {
                showToast('Please select at least one lead to reassign', 'error');
            } else {
                alert('Please select at least one lead to reassign');
            }
            return false;
        }

        // Collect selected lead IDs
        var leadIds = [];
        $('#lead_table_body input[type="checkbox"]:checked').each(function() {
            leadIds.push($(this).val());
        });

        // Add lead IDs to form
        leadIds.forEach(function(id) {
            $('<input>').attr({
                type: 'hidden',
                name: 'lead_id[]',
                value: id
            }).appendTo('#bulkReassignForm');
        });

        var submitBtn = $('#reassign_btn');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true);
        submitBtn.html('<i data-feather="loader"></i> Processing...');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Close modal
                $('#ajax_modal').modal('hide');
                
                // Show success message
                if (response.message) {
                    if (typeof showToast !== 'undefined') {
                        showToast(response.message, 'success');
                    } else {
                        alert(response.message);
                    }
                } else {
                    if (typeof showToast !== 'undefined') {
                        showToast('Leads reassigned successfully!', 'success');
                    } else {
                        alert('Leads reassigned successfully!');
                    }
                }
                
                // Reload the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while reassigning leads.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const errorList = Object.values(errors).flat();
                        if (errorList.length > 0) {
                            errorMessage += '<br><br>' + errorList.join('<br>');
                        }
                    }
                }
                
                // Show error modal
                $('#confirmModalTitle').text('Reassign Error');
                $('#confirmModalBody').html('<div class="alert alert-danger mb-0">' +
                    '<i data-feather="alert-circle" class="me-2"></i>' +
                    errorMessage +
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
                
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    });
});
</script>

<style>
.bulk-operations-table {
    max-height: 300px;
    overflow-y: auto;
}

.bulk-table thead th {
    background-color: #fff;
    position: sticky;
    top: 0;
    border: 1px solid #ddd;
    z-index: 10;
}

.bulk-checkbox {
    width: 22px;
    height: 22px;
    cursor: pointer;
}
</style>

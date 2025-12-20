<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
    padding: 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #0d6efd;
    border: 1px solid #0d6efd;
    border-radius: 0.25rem;
    color: #fff;
    padding: 0.25rem 0.5rem;
    margin: 0.125rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    margin-right: 0.25rem;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #fff;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd;
    color: #fff;
}
</style>

<div class="p-3">
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.leads.bulk-upload.submit') }}" method="post" enctype="multipart/form-data" id="bulkUploadForm">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label" for="excel_file">Select Excel File <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls" required />
                        <a href="{{ route('admin.leads.bulk-upload.template') }}" class="btn btn-outline-info" type="button" target="_blank">
                            <i data-feather="download"></i> Download Template
                        </a>
                    </div>
                    <small class="text-muted">Supported formats: .xlsx, .xls (Max size: 2MB)</small>
                    <div id="excel_file_error" class="text-danger mt-1" style="display: none;"></div>
                    @error('excel_file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_source_id">Lead Source <span class="text-danger">*</span></label>
                    <select class="form-select @error('lead_source_id') is-invalid @enderror" name="lead_source_id" id="lead_source_id" required>
                        <option value="">Select Lead Source</option>
                        @foreach($leadSources as $source)
                            <option value="{{ $source->id }}" {{ old('lead_source_id') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                        @endforeach
                    </select>
                    @error('lead_source_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="lead_status_id">Lead Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('lead_status_id') is-invalid @enderror" name="lead_status_id" id="lead_status_id" required>
                        <option value="">Select Lead Status</option>
                        @foreach($leadStatuses as $status)
                            <option value="{{ $status->id }}" {{ old('lead_status_id', $status->id == 1 ? $status->id : '') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('lead_status_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label" for="purpose_id">Purpose <span class="text-danger">*</span></label>
                    <select class="form-select @error('purpose_id') is-invalid @enderror" name="purpose_id" id="purpose_id" required>
                        <option value="">Select Purpose</option>
                        @foreach($purposes as $purpose)
                            <option value="{{ $purpose->id }}" {{ old('purpose_id') == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                        @endforeach
                    </select>
                    @error('purpose_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="assign_to_all" name="assign_to_all" value="1" {{ old('assign_to_all') ? 'checked' : '' }}>
                        <label class="form-check-label" for="assign_to_all">
                            <strong>Assign to all Telecallers</strong> - Leads will be assigned to all telecallers equally
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-md-12" id="telecaller-selection">
                <div class="mb-3">
                    <label class="form-label" for="telecallers">Assign to Telecallers <span class="text-danger">*</span></label>
                    <select class="form-select select2-multiple @error('telecallers') is-invalid @enderror" name="telecallers[]" id="telecallers" multiple>
                        @foreach($telecallers as $telecaller)
                            <option value="{{ $telecaller->id }}" {{ in_array($telecaller->id, old('telecallers', [])) ? 'selected' : '' }}>{{ $telecaller->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select one or more telecallers to assign leads</small>
                    <div id="telecallers_error" class="text-danger mt-1" style="display: none;"></div>
                    @error('telecallers')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-12">
                <div class="alert alert-info">
                    <h6><i data-feather="info" class="me-2"></i>Excel Format Guide:</h6>
                    <p class="mb-2"><strong>Required columns:</strong> Name, Phone, Place, Remarks</p>
                    <p class="mb-2"><strong>Column order:</strong></p>
                    <ul class="mb-2">
                        <li><strong>Column A:</strong> Name (Required)</li>
                        <li><strong>Column B:</strong> Phone (Required) - Use international format with country code</li>
                        <li><strong>Column C:</strong> Place (Optional)</li>
                        <li><strong>Column D:</strong> Remarks (Optional)</li>
                    </ul>
                    <p class="mb-2"><strong>Phone Format:</strong> Use international format with country code:</p>
                    <ul class="mb-2">
                        <li><code>+91 9876543210</code> (India)</li>
                        <li><code>+1 5551234567</code> (US/Canada)</li>
                        <li><code>+44 7700123456</code> (UK)</li>
                        <li><code>+86 13800138000</code> (China)</li>
                    </ul>
                    <p class="mb-2"><strong>Template:</strong> Click "Download Template" above to get the correct Excel format with sample data.</p>
                    <p class="mb-0"><strong>Note:</strong> Duplicate phone numbers (same code + phone) will be automatically skipped. Place and Remarks fields are optional.</p>
                </div>
            </div>

            <!-- Error Alert -->
            <div id="bulk-upload-error" class="alert alert-danger" style="display: none;">
                <h6><i data-feather="alert-circle"></i> Upload Error</h6>
                <div id="bulk-upload-error-message"></div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">
                <i data-feather="upload"></i> Upload & Process
            </button>
        </div>
    </form>
</div>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Initialize Select2 for telecaller dropdown
    function initializeTelecallerSelect2() {
        const telecallerSelect = $('#telecallers');
        
        // Destroy existing Select2 if any
        if (telecallerSelect.hasClass('select2-hidden-accessible')) {
            telecallerSelect.select2('destroy');
        }
        
        // Initialize Select2 with modal parent
        try {
            telecallerSelect.select2({
                placeholder: 'Select telecallers...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#ajax_modal')
            });
            return true;
        } catch (error) {
            console.error('Select2 initialization error:', error);
            return false;
        }
    }
    
    // Try to initialize Select2
    let attempts = 0;
    const maxAttempts = 5;
    
    function tryInitializeSelect2() {
        attempts++;
        
        if (typeof $.fn.select2 !== 'undefined') {
            if (initializeTelecallerSelect2()) {
                return;
            }
        }
        
        if (attempts < maxAttempts) {
            setTimeout(tryInitializeSelect2, 200 * attempts);
        } else {
            console.warn('Select2 could not be initialized after multiple attempts');
        }
    }
    
    // Start initialization
    tryInitializeSelect2();

    // Handle assign to all checkbox
    $('#assign_to_all').on('change', function() {
        const isChecked = $(this).is(':checked');
        const telecallerSelection = $('#telecaller-selection');
        
        if (isChecked) {
            telecallerSelection.hide();
            $('#telecallers').prop('required', false);
            
            // Show success message
            if (typeof showToast !== 'undefined') {
                showToast('Leads will be assigned to all telecallers equally', 'success');
            }
        } else {
            telecallerSelection.show();
            $('#telecallers').prop('required', true);
        }
    });

    // File size validation
    $('#excel_file').on('change', function() {
        const file = this.files[0];
        const errorDiv = $('#excel_file_error');
        
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            if (fileSize > 2) {
                errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                this.value = '';
                return false;
            } else {
                errorDiv.hide();
            }
        } else {
            errorDiv.hide();
        }
    });

    // Clear errors when form values change
    $('#lead_source_id, #lead_status_id, #purpose_id, #telecallers, #assign_to_all').on('change', function() {
        $('.text-danger').hide();
        $('#bulk-upload-error').hide();
    });

    // Form submission with loading state
    $('#bulkUploadForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check file size before submission
        const fileInput = $('#excel_file')[0];
        const errorDiv = $('#excel_file_error');
        
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            if (fileSize > 2) {
                errorDiv.text('File size must be less than 2MB. Current file size: ' + fileSize.toFixed(2) + 'MB').show();
                return false;
            }
        }
        
        // Check telecaller assignment
        const assignToAll = $('#assign_to_all').is(':checked');
        const selectedTelecallers = $('#telecallers').val();
        
        if (!assignToAll && (!selectedTelecallers || selectedTelecallers.length === 0)) {
            // Show error modal
            $('#confirmModalTitle').text('Validation Error');
            $('#confirmModalBody').html('<div class="alert alert-danger mb-0">' +
                '<i data-feather="alert-circle" class="me-2"></i>' +
                'Please select at least one telecaller or choose "Assign to all telecallers".' +
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
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        const form = $(this);
        const formData = new FormData(this);
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i data-feather="loader"></i> Processing...');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Close the bulk upload modal
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
                        showToast('Leads uploaded successfully!', 'success');
                    } else {
                        alert('Leads uploaded successfully!');
                    }
                }
                
                // Reload the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while uploading leads.';
                let errorDetails = '';
                
                // Clear previous field errors
                $('.text-danger').hide();
                $('#bulk-upload-error').hide();
                
                if (xhr.responseJSON) {
                    // Show main error message
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    // Show detailed validation errors
                    if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const errorList = Object.values(errors).flat();
                        
                        // Show field-specific errors
                        Object.keys(errors).forEach(field => {
                            const errorDiv = $('#' + field + '_error');
                            if (errorDiv.length) {
                                errorDiv.html(errors[field].join('<br>')).show();
                            }
                        });
                        
                        if (errorList.length > 0) {
                            errorDetails = '<br><br><strong>Details:</strong><br>' + errorList.join('<br>');
                        }
                    }
                }
                
                // Show error in alert box
                $('#bulk-upload-error-message').html(errorMessage + errorDetails);
                $('#bulk-upload-error').show();
                
                // Also show modal error
                $('#confirmModalTitle').text('Upload Error');
                $('#confirmModalBody').html('<div class="alert alert-danger mb-0">' +
                    '<i data-feather="alert-circle" class="me-2"></i>' +
                    errorMessage + errorDetails +
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

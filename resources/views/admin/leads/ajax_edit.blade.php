<form id="leadEditForm" method="POST" action="{{ route('admin.leads.update', $lead) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $lead->name }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $lead->email }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Country Code <span class="text-danger">*</span></label>
                <select name="country_code" class="form-control" required>
                    <option value="">Select Country Code</option>
                    @foreach($countryCodes as $code => $name)
                    <option value="{{ $code }}" {{ $lead->country_code == $code ? 'selected' : '' }}>{{ $code }} - {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" value="{{ $lead->phone }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Country <span class="text-danger">*</span></label>
                <select name="country_id" class="form-control" required>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ $lead->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Purpose <span class="text-danger">*</span></label>
                <select name="purpose_id" class="form-control" required>
                    <option value="">Select Purpose</option>
                    @foreach($purposes as $purpose)
                    <option value="{{ $purpose->id }}" {{ $lead->purpose_id == $purpose->id ? 'selected' : '' }}>{{ $purpose->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Lead Status <span class="text-danger">*</span></label>
                <select name="lead_status_id" class="form-control" required>
                    <option value="">Select Status</option>
                    @foreach($leadStatuses as $status)
                    <option value="{{ $status->id }}" {{ $lead->lead_status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Lead Source <span class="text-danger">*</span></label>
                <select name="lead_source_id" class="form-control" required>
                    <option value="">Select Source</option>
                    @foreach($leadSources as $source)
                    <option value="{{ $source->id }}" {{ $lead->lead_source_id == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>User Type</label>
                <select name="user_type" id="user_type" class="form-control">
                    <option value="telecaller" {{ old('user_type', $lead->user_type ?? 'telecaller') == 'telecaller' ? 'selected' : '' }}>Telecaller</option>
                    <option value="digital_marketing" {{ old('user_type', $lead->user_type) == 'digital_marketing' ? 'selected' : '' }}>Digital Marketing</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3" id="telecaller_selection">
                <label>Telecaller</label>
                <select name="telecaller_id" id="telecaller_id" class="form-control">
                    <option value="">Unassigned</option>
                    @foreach($telecallers as $telecaller)
                    <option value="{{ $telecaller->id }}" {{ old('telecaller_id', $lead->telecaller_id) == $telecaller->id ? 'selected' : '' }}>{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3" id="digital_marketing_selection" style="display: none;">
                <label>Digital Marketing Employee</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Unassigned</option>
                    @foreach($digitalMarketingEmployees as $employee)
                    <option value="{{ $employee->id }}" {{ old('user_id', $lead->user_id) == $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->employee_id }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ $lead->date->format('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Followup Date</label>
                <input type="date" name="followup_date" class="form-control" value="{{ $lead->followup_date ? $lead->followup_date->format('Y-m-d') : '' }}" min="{{ now()->format('Y-m-d') }}">
            </div>
        </div>
    </div>
    <div class="form-group mb-3">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control" rows="3">{{ $lead->remarks }}</textarea>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Lead</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Handle user type change
    $('#user_type').on('change', function() {
        const userType = $(this).val();
        if (userType === 'telecaller') {
            $('#telecaller_selection').show();
            $('#digital_marketing_selection').hide();
            $('#telecaller_id').prop('required', false);
            $('#user_id').prop('required', false);
        } else if (userType === 'digital_marketing') {
            $('#telecaller_selection').hide();
            $('#digital_marketing_selection').show();
            $('#telecaller_id').prop('required', false);
            $('#user_id').prop('required', false);
        }
    });
    
    // Trigger on page load based on current value
    const currentUserType = $('#user_type').val();
    if (currentUserType === 'digital_marketing') {
        $('#telecaller_selection').hide();
        $('#digital_marketing_selection').show();
    } else {
        $('#telecaller_selection').show();
        $('#digital_marketing_selection').hide();
    }
    
    $('#leadEditForm').on('submit', function(e) {
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
                    showToast(response.message || 'Lead updated successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadLeads === 'function') {
                        loadLeads();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating lead';
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


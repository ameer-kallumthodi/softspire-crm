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
                <input type="text" name="country_code" class="form-control" value="{{ $lead->country_code }}" required>
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
                <label>Telecaller</label>
                <select name="telecaller_id" class="form-control">
                    <option value="">Unassigned</option>
                    @foreach($telecallers as $telecaller)
                    <option value="{{ $telecaller->id }}" {{ $lead->telecaller_id == $telecaller->id ? 'selected' : '' }}>{{ $telecaller->name }}</option>
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
                <input type="date" name="followup_date" class="form-control" value="{{ $lead->followup_date ? $lead->followup_date->format('Y-m-d') : '' }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_meta" value="1" class="form-check-input" id="is_meta_edit" {{ $lead->is_meta ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_meta_edit">Is Meta</label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_converted" value="1" class="form-check-input" id="is_converted_edit" {{ $lead->is_converted ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_converted_edit">Is Converted</label>
                </div>
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


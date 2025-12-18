<form id="leadForm" method="POST" action="{{ route('admin.leads.store') }}">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
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
                    <option value="{{ $code }}">{{ $code }} - {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Country <span class="text-danger">*</span></label>
                <select name="country_id" class="form-control" required>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
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
                    <option value="{{ $purpose->id }}">{{ $purpose->name }}</option>
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
                    <option value="{{ $status->id }}">{{ $status->name }}</option>
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
                    <option value="{{ $source->id }}">{{ $source->name }}</option>
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
                    <option value="{{ $telecaller->id }}">{{ $telecaller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Followup Date</label>
                <input type="date" name="followup_date" class="form-control" min="{{ now()->format('Y-m-d') }}">
            </div>
        </div>
    </div>
    <div class="form-group mb-3">
        <label>Remarks</label>
        <textarea name="remarks" class="form-control" rows="3"></textarea>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Lead</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#leadForm').on('submit', function(e) {
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
                    showToast(response.message || 'Lead created successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadLeads === 'function') {
                        loadLeads();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error creating lead';
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


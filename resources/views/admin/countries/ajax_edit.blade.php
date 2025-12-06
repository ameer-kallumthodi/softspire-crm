<form id="countryEditForm" method="POST" action="{{ route('admin.countries.update', $country) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $country->name }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ $country->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $country->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Country</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#countryEditForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'Country updated successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadCountries === 'function') {
                        loadCountries();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating country';
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


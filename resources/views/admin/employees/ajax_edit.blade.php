<form id="employeeEditForm" method="POST" action="{{ route('admin.employees.update', $user->id) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
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
                    <option value="{{ $code }}" {{ old('country_code', $user->country_code) == $code ? 'selected' : '' }}>{{ $code }} - {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb-3">
                <label>Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Department <span class="text-danger">*</span></label>
                <select name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Joining Date <span class="text-danger">*</span></label>
                <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $user->joining_date ? $user->joining_date->format('Y-m-d') : '') }}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}" max="{{ now()->subYears(15)->format('Y-m-d') }}" required>
                <small class="text-muted">Must be at least 15 years old</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Enter address">{{ old('address', $user->address) }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Employee</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#employeeEditForm').on('submit', function(e) {
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
                    showToast(response.message || 'Employee updated successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadEmployees === 'function') {
                        loadEmployees();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating employee';
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


<form id="userEditForm" method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <small class="text-muted">Leave blank to keep current password</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>
    </div>
    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update User</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#userEditForm').on('submit', function(e) {
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
                    showToast(response.message || 'User updated successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadUsers === 'function') {
                        loadUsers();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating user';
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


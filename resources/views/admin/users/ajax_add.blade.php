<form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
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
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>
    </div>
    <input type="hidden" name="role_filter" value="{{ $roleFilter }}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save User</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#userForm').on('submit', function(e) {
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
                    showToast(response.message || 'User created successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadUsers === 'function') {
                        loadUsers();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error creating user';
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


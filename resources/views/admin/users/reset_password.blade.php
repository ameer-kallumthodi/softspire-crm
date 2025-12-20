<form id="resetPasswordForm" method="POST" action="{{ route('admin.users.update-password', $user->id) }}">
    @csrf
    @method('PUT')
    <div class="alert alert-info">
        <i data-feather="info" class="me-2"></i>
        <strong>Reset Password for:</strong> {{ $user->name }} ({{ $user->email }})
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label>New Password <span class="text-danger">*</span></label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required minlength="6">
                <small class="text-muted">Password must be at least 6 characters long</small>
                @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group mb-3">
                <label>Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required minlength="6">
                @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">
            <i data-feather="key"></i> Reset Password
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Password confirmation validation
    $('#password_confirmation').on('keyup', function() {
        const password = $('#password').val();
        const confirmation = $(this).val();
        
        if (password && confirmation && password !== confirmation) {
            $(this).addClass('is-invalid');
            if ($(this).next('.invalid-feedback').length === 0) {
                $(this).after('<div class="invalid-feedback">Passwords do not match</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    $('#password').on('keyup', function() {
        $('#password_confirmation').trigger('keyup');
    });

    $('#resetPasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();
        
        // Client-side validation
        if (password.length < 6) {
            showToast('Password must be at least 6 characters long', 'error');
            return false;
        }
        
        if (password !== passwordConfirmation) {
            showToast('Passwords do not match', 'error');
            return false;
        }
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i data-feather="loader"></i> Resetting...');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
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
                    showToast(response.message || 'Password reset successfully', 'success');
                    $('#ajax_modal').modal('hide');
                    if (typeof loadUsers === 'function') {
                        loadUsers();
                    } else {
                        location.reload();
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error resetting password';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    }
                }
                showToast(errorMessage, 'error');
                
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

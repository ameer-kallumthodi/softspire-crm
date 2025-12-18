<!-- All Jquery -->
<script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/app-style-switcher.js') }}"></script>
<script src="{{ asset('dist/js/feather.min.js') }}"></script>
<script src="{{ asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
<script src="{{ asset('dist/js/sidebarmenu.js') }}"></script>
<script src="{{ asset('dist/js/custom.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toast Notification Functions -->
<script>
// Toast notification function
function showToast(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();
    
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    const title = type === 'success' ? 'Success' : 'Error';
    
    const toastHTML = `
        <div id="${toastId}" class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header ${bgClass} text-white">
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Confirmation modal function
function showConfirmModal(message, title = 'Confirm Action', onConfirm) {
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.error('Bootstrap is not loaded');
        if (confirm(title + ': ' + message)) {
            if (onConfirm) {
                onConfirm();
            }
        }
        return;
    }
    
    const modalElement = document.getElementById('confirmModal');
    if (!modalElement) {
        console.error('confirmModal element not found');
        if (confirm(title + ': ' + message)) {
            if (onConfirm) {
                onConfirm();
            }
        }
        return;
    }
    
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    document.getElementById('confirmModalTitle').textContent = title;
    document.getElementById('confirmModalBody').textContent = message;
    
    const confirmBtn = document.getElementById('confirmModalBtn');
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    newConfirmBtn.addEventListener('click', function() {
        modal.hide();
        if (onConfirm) {
            onConfirm();
        }
    });
    
    modal.show();
}
</script>

@stack('scripts')

<!-- Initialize Feather Icons and Sidebar -->
<script>
$(document).ready(function() {
    // Wait a bit for all scripts to load
    setTimeout(function() {
        // Initialize feather icons with error handling
        if (typeof feather !== 'undefined') {
            try {
                // Replace icons one by one to catch invalid ones
                $('[data-feather]').each(function() {
                    try {
                        var iconName = $(this).attr('data-feather');
                        if (iconName && feather.icons && feather.icons[iconName]) {
                            var svg = feather.icons[iconName].toSvg({
                                'stroke-width': 2,
                                width: 20,
                                height: 20
                            });
                            $(this).replaceWith(svg);
                        } else {
                            console.warn('Invalid feather icon:', iconName);
                            // Keep the element but remove the data-feather attribute
                            $(this).removeAttr('data-feather');
                        }
                    } catch(err) {
                        console.warn('Error replacing icon:', $(this).attr('data-feather'), err);
                        $(this).removeAttr('data-feather');
                    }
                });
            } catch(e) {
                console.warn('Feather icons initialization error:', e);
            }
        }
        
        // Ensure sidebar menu is properly initialized
        if (typeof $ !== 'undefined' && $('#sidebarnav').length) {
            // Re-initialize sidebar menu handlers if needed
            $('#sidebarnav .has-arrow').off('click').on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $parent = $this.parent();
                var $submenu = $this.next('ul');
                
                if ($submenu.length) {
                    if ($submenu.hasClass('in')) {
                        $submenu.removeClass('in');
                        $parent.removeClass('active');
                        $this.attr('aria-expanded', 'false');
                    } else {
                        // Close other open menus
                        $('#sidebarnav .first-level').removeClass('in');
                        $('#sidebarnav .sidebar-item').removeClass('active');
                        $('#sidebarnav .has-arrow').attr('aria-expanded', 'false');
                        
                        // Open this menu
                        $submenu.addClass('in');
                        $parent.addClass('active');
                        $this.attr('aria-expanded', 'true');
                    }
                }
            });
        }
    }, 100);
});
</script>


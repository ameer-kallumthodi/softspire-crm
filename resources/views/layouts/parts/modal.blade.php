<!-- Normal Modal -->
<div id="small_modal" class="modal fade" tabindex="-1" aria-labelledby="small_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="small-modal-title"></h5>
                <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="small-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Ajax Modal -->
<div id="ajax_modal" class="modal fade" tabindex="-1" aria-labelledby="ajax_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="ajax-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="ajax-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- X-Large Modal -->
<div id="large_modal" class="modal fade" tabindex="-1" aria-labelledby="large_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="large-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="large-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Full Screen Modal -->
<div id="full_modal" class="modal fade" tabindex="-1" aria-labelledby="full_modal_label" aria-hidden="true" style="display: none;" role="dialog">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable mx-auto" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-2">
                <h5 class="modal-title" id="full-modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body" id="full-modal-content">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmModalBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Wait for jQuery to be available
    (function() {
        function initModalFunctions() {
            if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                setTimeout(initModalFunctions, 50);
                return;
            }
            
            // Define call_ajax_view function
            window.call_ajax_view = function(url, target) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    success: function(response) {
                        $(target).html(response);
                        // Reinitialize feather icons if they exist
                        if (typeof feather !== 'undefined') {
                            try {
                                $('[data-feather]', target).each(function() {
                                    try {
                                        var iconName = $(this).attr('data-feather');
                                        if (iconName && feather.icons && feather.icons[iconName]) {
                                            var svg = feather.icons[iconName].toSvg({
                                                'stroke-width': 2,
                                                width: 20,
                                                height: 20
                                            });
                                            $(this).replaceWith(svg);
                                        }
                                    } catch(err) {
                                        console.warn('Error replacing icon:', $(this).attr('data-feather'), err);
                                    }
                                });
                            } catch(e) {
                                console.warn('Feather icons error:', e);
                            }
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error loading content';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage = 'Server error: ' + xhr.status;
                        }
                        $(target).html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    }
                });
            };

            // Define show_small_modal function
            window.show_small_modal = function(url, header) {
                if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
                    console.error('Bootstrap is not loaded');
                    return;
                }
                $('#small-modal-content').html('<div style="padding:40px; text-align:center;"><img src="https://i.stack.imgur.com/FhHRx.gif"></div>');
                $('#small-modal-title').html('Loading...');
                const modalElement = document.getElementById('small_modal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
                call_ajax_view(url, '#small-modal-content');
                $('#small-modal-title').html(header);
            };

            // Define show_ajax_modal function
            window.show_ajax_modal = function(url, header) {
                if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
                    console.error('Bootstrap is not loaded');
                    return;
                }
                $('#ajax-modal-content').html('<div style="padding:40px; text-align:center;"><img src="https://i.stack.imgur.com/FhHRx.gif"></div>');
                $('#ajax-modal-title').html('Loading...');
                const modalElement = document.getElementById('ajax_modal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
                call_ajax_view(url, '#ajax-modal-content');
                $('#ajax-modal-title').html(header);
            };

            // Define show_large_modal function
            window.show_large_modal = function(url, header) {
                if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
                    console.error('Bootstrap is not loaded');
                    return;
                }
                $('#large-modal-content').html('<div style="padding:40px; text-align:center;"><img src="https://i.stack.imgur.com/FhHRx.gif"></div>');
                $('#large-modal-title').html('Loading...');
                const modalElement = document.getElementById('large_modal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
                call_ajax_view(url, '#large-modal-content');
                $('#large-modal-title').html(header);
            };

            // Define show_full_modal function
            window.show_full_modal = function(url, header) {
                if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
                    console.error('Bootstrap is not loaded');
                    return;
                }
                $('#full-modal-content').html('<div style="padding:40px; text-align:center;"><img src="https://i.stack.imgur.com/FhHRx.gif"></div>');
                $('#full-modal-title').html('Loading...');
                const modalElement = document.getElementById('full_modal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
                call_ajax_view(url, '#full-modal-content');
                $('#full-modal-title').html(header);
            };

            // Define alert_modal_success function
            window.alert_modal_success = function(message = '', message_title = 'Success!', cancel_button = 'Okay') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        html: '<div class="mt-3">' +
                            '<lord-icon src="https://cdn.lordicon.com/lupuorrc.json" trigger="loop" colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px"></lord-icon>' +
                            '<div class="mt-4 pt-2 fs-15">' +
                            '<h4>' + message_title + '</h4>' +
                            '<p class="text-muted mx-4 mb-0">' + message + '</p>' +
                            '</div>' +
                            '</div>',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonClass: 'btn btn-success w-xs mb-1',
                        cancelButtonText: cancel_button,
                        buttonsStyling: false,
                        showCloseButton: true
                    });
                } else {
                    alert(message_title + ': ' + message);
                }
            };

            // Define alert_modal_error function
            window.alert_modal_error = function(message = 'Something went wrong..!', cancel_button = 'Okay') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        html: '<div class="mt-3">' +
                            '<lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop" colors="primary:#f06548,secondary:#f7b84b" style="width:120px;height:120px"></lord-icon>' +
                            '<div class="mt-4 pt-2 fs-15">' +
                            '<h2>Oops...!</h2>' +
                            '<p class="text-muted mx-4 mb-0">' + message +'</p>' +
                            '</div>' +
                            '</div>',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonClass: 'btn btn-danger1 btn-outline-danger w-xs mb-1',
                        cancelButtonText: 'Dismiss',
                        buttonsStyling: false,
                        showCloseButton: true
                    });
                } else {
                    alert('Error: ' + message);
                }
            };

            // Define confirm_modal function
            window.confirm_modal = function(
                confirm_url,
                message = 'Are you Sure?',
                message_description = 'Are you Sure You want to proceed?',
                button_text = 'Yes, Confirm!'
            ) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        html: '<div class="mt-3">' +
                            '<lord-icon src="https://cdn.lordicon.com/hmzvkifi.json" trigger="loop" delay="2500" style="width:250px;height:250px"></lord-icon>' +
                            '<div class="mt-4 pt-2 fs-15 mx-5">' +
                            '<h4>' + message + '</h4>' +
                            '<p class="text-muted mx-4 mb-0"> ' + message_description + '</p>' +
                            '</div>' +
                            '</div>',
                        showCancelButton: true,
                        confirmButtonClass: 'btn btn-primary w-xs me-2 mb-1',
                        confirmButtonText: button_text,
                        cancelButtonClass: 'btn btn-danger w-xs mb-1',
                        buttonsStyling: false,
                        showCloseButton: true,
                        preConfirm: () => {
                            window.location.href = confirm_url;
                        }
                    });
                } else {
                    if (confirm(message + ' ' + message_description)) {
                        window.location.href = confirm_url;
                    }
                }
            };

        }
        
        // Start initialization
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initModalFunctions);
        } else {
            initModalFunctions();
        }
    })();

    // Define delete_modal function globally (doesn't need jQuery)
    window.delete_modal = function(delete_url, message = 'Are you sure?') {
        // Wait for Swal to be available
        function executeDelete() {
            if (typeof Swal === 'undefined') {
                setTimeout(executeDelete, 100);
                return;
            }

            Swal.fire({
                title: message,
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                preConfirm: () => {
                    const params = new URLSearchParams();
                    params.append('_method', 'DELETE');

                    return fetch(delete_url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: params
                    }).then(async response => {
                        let data = {};
                        try {
                            data = await response.json();
                        } catch (e) {
                            // non-JSON response
                        }
                        if (!response.ok || (data && data.success === false)) {
                            const msg = (data && (data.message || data.error)) || 'Delete failed.';
                            throw new Error(msg);
                        }
                        return data;
                    }).catch(error => {
                        Swal.showValidationMessage(error.message || 'Delete failed.');
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Deleted!', 'Item has been deleted.', 'success').then(() => {
                        location.reload();
                    });
                }
            });
        }
        
        executeDelete();
    };
</script>

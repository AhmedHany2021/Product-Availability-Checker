jQuery(document).ready(function($) {
    'use strict';

    /**
     * Show admin notice.
     *
     * @param {string} message Message to display.
     * @param {string} type    Notice type: 'success' or 'error'.
     */
    function showNotice(message, type) {
        type = type || 'success';
        var noticeClass = 'notice notice-' + type + ' is-dismissible';
        var notice = $('<div class="' + noticeClass + '"><p>' + message + '</p></div>');
        
        // Remove existing notices.
        $('.pac-admin-notice').remove();
        
        // Add notice class and prepend to admin container.
        notice.addClass('pac-admin-notice');
        $('#pac-zip-admin').before(notice);
        
        // Auto-dismiss after 5 seconds.
        setTimeout(function() {
            notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Validate ZIP code format.
     *
     * @param {string} zip ZIP code to validate.
     * @return {boolean} True if valid.
     */
    function validateZip(zip) {
        if (!zip || zip.trim() === '') {
            return false;
        }
        // Remove spaces and dashes.
        var cleanZip = zip.replace(/[\s\-]/g, '');
        // Check: 5-10 alphanumeric characters.
        return /^[0-9A-Za-z]{5,10}$/.test(cleanZip);
    }

    // Add new ZIP code.
    $('#pac_add_zip').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var zip = $('#pac_new_zip').val().trim();
        var status = $('#pac_new_zip_status').val();
        var message = $('#pac_new_zip_message').val().trim();
        
        // Validate ZIP code.
        if (!validateZip(zip)) {
            showNotice(pac_admin.i18n.zip_invalid, 'error');
            return;
        }
        
        // Disable button during request.
        $button.prop('disabled', true).text(pac_admin.i18n.adding || 'Adding...');
        
        $.post(pac_admin.ajax_url, {
            action: 'pac_add_zip',
            zip: zip,
            status: status,
            message: message,
            nonce: pac_admin.nonce
        }, function(res) {
            if (res.success) {
                showNotice(pac_admin.i18n.add_success, 'success');
                // Clear form.
                $('#pac_new_zip').val('');
                $('#pac_new_zip_message').val('');
                // Reload after short delay.
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                var errorMsg = res.data && res.data.message ? res.data.message : pac_admin.i18n.error_occurred;
                showNotice(errorMsg, 'error');
                $button.prop('disabled', false).text(pac_admin.i18n.add || 'Add ZIP Code');
            }
        }).fail(function() {
            showNotice(pac_admin.i18n.error_occurred, 'error');
            $button.prop('disabled', false).text(pac_admin.i18n.add || 'Add ZIP Code');
        });
    });

    // Delete ZIP code.
    $(document).on('click', '.pac-delete-zip', function(e) {
        e.preventDefault();
        
        if (!confirm(pac_admin.i18n.delete_confirm)) {
            return;
        }
        
        var $button = $(this);
        var zip = $button.data('zip');
        var $row = $button.closest('tr');
        
        // Disable button during request.
        $button.prop('disabled', true);
        
        $.post(pac_admin.ajax_url, {
            action: 'pac_delete_zip',
            zip: zip,
            nonce: pac_admin.nonce
        }, function(res) {
            if (res.success) {
                showNotice(pac_admin.i18n.delete_success, 'success');
                // Fade out row and reload.
                $row.fadeOut(function() {
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                });
            } else {
                var errorMsg = res.data && res.data.message ? res.data.message : pac_admin.i18n.error_occurred;
                showNotice(errorMsg, 'error');
                $button.prop('disabled', false);
            }
        }).fail(function() {
            showNotice(pac_admin.i18n.error_occurred, 'error');
            $button.prop('disabled', false);
        });
    });

    // Save / edit ZIP code.
    $(document).on('click', '.pac-save-zip', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $row = $button.closest('tr');
        var zip = $button.data('zip');
        var status = $row.find('.pac-edit-status').val();
        var message = $row.find('.pac-edit-message').val().trim();
        
        // Disable button during request.
        $button.prop('disabled', true).text(pac_admin.i18n.saving || 'Saving...');
        
        $.post(pac_admin.ajax_url, {
            action: 'pac_update_zip',
            zip: zip,
            status: status,
            message: message,
            nonce: pac_admin.nonce
        }, function(res) {
            if (res.success) {
                showNotice(pac_admin.i18n.update_success, 'success');
                $button.prop('disabled', false).text(pac_admin.i18n.save || 'Save');
            } else {
                var errorMsg = res.data && res.data.message ? res.data.message : pac_admin.i18n.error_occurred;
                showNotice(errorMsg, 'error');
                $button.prop('disabled', false).text(pac_admin.i18n.save || 'Save');
            }
        }).fail(function() {
            showNotice(pac_admin.i18n.error_occurred, 'error');
            $button.prop('disabled', false).text(pac_admin.i18n.save || 'Save');
        });
    });

    // Character counter for message fields.
    $('.pac-edit-message, #pac_new_zip_message').on('input', function() {
        var $field = $(this);
        var length = $field.val().length;
        var maxLength = $field.attr('maxlength') || 500;
        
        // Remove existing counter.
        $field.siblings('.char-count').remove();
        
        // Add counter if approaching limit.
        if (length > maxLength * 0.8) {
            var $counter = $('<span class="char-count description">' + length + ' / ' + maxLength + '</span>');
            $field.after($counter);
        }
    });
});

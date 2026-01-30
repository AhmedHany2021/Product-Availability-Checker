jQuery(function ($) {
    'use strict';

    const zipInput = $('#zip-code');
    const resultBox = $('#zip-result');
    const addToCartBtn = $('.single_add_to_cart_button');

    // Load saved ZIP on page load.
    if (zipData.savedZip) {
        zipInput.val(zipData.savedZip);
        checkZip(zipData.savedZip);
    }

    // Check ZIP on button click.
    $('#check-zip').on('click', function () {
        const zip = zipInput.val().trim();
        if (!zip) {
            resultBox.html('<span class="zip-error">Please enter a ZIP code.</span>');
            return;
        }
        checkZip(zip);
    });

    // Allow Enter key to trigger check.
    zipInput.on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#check-zip').trigger('click');
        }
    });

    /**
     * Check ZIP code availability via AJAX.
     *
     * @param {string} zip ZIP code to check.
     */
    function checkZip(zip) {
        // Show loading state.
        resultBox.html('<span class="zip-loading">Checking availability...</span>');
        zipInput.prop('disabled', true);
        $('#check-zip').prop('disabled', true);

        $.post(zipData.ajax_url, {
            action: 'pac_check_zip',
            zip: zip,
            nonce: zipData.nonce
        }, function (response) {
            // Re-enable inputs.
            zipInput.prop('disabled', false);
            $('#check-zip').prop('disabled', false);

            if (!response.success) {
                resultBox.html('<span class="zip-error">' + (response.data || 'An error occurred. Please try again.') + '</span>');
                return;
            }

            const available = response.data.available;
            const message = response.data.message || '';

            // Display result with custom message if available.
            if (available) {
                let html = '<span class="zip-available">✓ Delivery available';
                if (message) {
                    html += '<br><small class="zip-message">' + $('<div>').text(message).html() + '</small>';
                }
                html += '</span>';
                resultBox.html(html);
                enableCart();
            } else {
                let html = '<span class="zip-unavailable">✗ Delivery not available';
                if (message) {
                    html += '<br><small class="zip-message">' + $('<div>').text(message).html() + '</small>';
                }
                html += '</span>';
                resultBox.html(html);
                disableCart();
            }
        }).fail(function () {
            // Re-enable inputs on error.
            zipInput.prop('disabled', false);
            $('#check-zip').prop('disabled', false);
            resultBox.html('<span class="zip-error">An error occurred. Please try again.</span>');
        });
    }

    /**
     * Disable Add to Cart button.
     */
    function disableCart() {
        addToCartBtn.prop('disabled', true).addClass('disabled');
    }

    /**
     * Enable Add to Cart button.
     */
    function enableCart() {
        addToCartBtn.prop('disabled', false).removeClass('disabled');
    }
});

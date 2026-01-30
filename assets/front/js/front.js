jQuery(function ($) {

    const zipInput = $('#zip-code');
    const resultBox = $('#zip-result');
    const addToCartBtn = $('.single_add_to_cart_button');

    // Load saved ZIP
    if (zipData.savedZip) {
        zipInput.val(zipData.savedZip);
        checkZip(zipData.savedZip);
    }

    $('#check-zip').on('click', function () {
        const zip = zipInput.val().trim();
        if (!zip) return;
        checkZip(zip);
    });

    function checkZip(zip) {
        $.post(zipData.ajax_url, {
            action: 'pac_check_zip',
            zip: zip,
            nonce: zipData.nonce
        }, function (response) {

            if (!response.success) {
                resultBox.text(response.data);
                return;
            }

            if (response.data.available) {
                resultBox.html('✅ Delivery available');
                enableCart();
            } else {
                resultBox.html('❌ Delivery not available');
                disableCart();
            }
        });
    }

    function disableCart() {
        addToCartBtn.prop('disabled', true).addClass('disabled');
    }

    function enableCart() {
        addToCartBtn.prop('disabled', false).removeClass('disabled');
    }
});

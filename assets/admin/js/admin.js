jQuery(document).ready(function($){

    // Add new zip
    $('#pac_add_zip').on('click', function(e){
        e.preventDefault();
        var zip = $('#pac_new_zip').val();
        var status = $('#pac_new_zip_status').val();

        $.post(pac_admin.ajax_url, {
            action: 'pac_add_zip',
            zip: zip,
            status: status,
            nonce: pac_admin.nonce
        }, function(res){
            if(res.success){
                location.reload();
            } else {
                alert(res.data.message);
            }
        });
    });

    // Delete zip
    $(document).on('click', '.pac-delete-zip', function(e){
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this zip code?')) return;

        var zip = $(this).data('zip');

        $.post(pac_admin.ajax_url, {
            action: 'pac_delete_zip',
            zip: zip,
            nonce: pac_admin.nonce
        }, function(res){
            if(res.success){
                location.reload();
            } else {
                alert(res.data.message);
            }
        });
    });

    // Save / edit zip status
    $(document).on('click', '.pac-save-zip', function(e){
        e.preventDefault();
        var row = $(this).closest('tr');
        var zip = $(this).data('zip');
        var status = row.find('.pac-edit-status').val();

        $.post(pac_admin.ajax_url, {
            action: 'pac_update_zip',
            zip: zip,
            status: status,
            nonce: pac_admin.nonce
        }, function(res){
            if(res.success){
                alert('Updated successfully!');
            } else {
                alert(res.data.message);
            }
        });
    });

});

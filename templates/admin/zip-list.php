<div id="pac-zip-admin">

    <h3><?php _e('Add New Zip Code', 'product-availability-checker'); ?></h3>
    <div style="margin-bottom:15px;">
        <input type="text" id="pac_new_zip" placeholder="<?php _e('Zip Code', 'product-availability-checker'); ?>">
        <select id="pac_new_zip_status">
            <option value="1"><?php _e('Available', 'product-availability-checker'); ?></option>
            <option value="0"><?php _e('Unavailable', 'product-availability-checker'); ?></option>
        </select>
        <button id="pac_add_zip" class="button button-primary"><?php _e('Add', 'product-availability-checker'); ?></button>
    </div>

    <h3><?php _e('Existing Zip Codes', 'product-availability-checker'); ?></h3>
    <table class="wp-list-table widefat fixed striped" id="pac_zip_table">
        <thead>
        <tr>
            <th><?php _e('Zip Code', 'product-availability-checker'); ?></th>
            <th><?php _e('Available', 'product-availability-checker'); ?></th>
            <th><?php _e('Actions', 'product-availability-checker'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($zips_to_show)): ?>
            <?php foreach($zips_to_show as $zip => $status): ?>
                <tr>
                    <td><?php echo esc_html($zip); ?></td>
                    <td>
                        <select class="pac-edit-status" data-zip="<?php echo esc_attr($zip); ?>">
                            <option value="1" <?php selected($status, true); ?>>Yes</option>
                            <option value="0" <?php selected($status, false); ?>>No</option>
                        </select>
                    </td>
                    <td>
                        <button class="button pac-save-zip" data-zip="<?php echo esc_attr($zip); ?>"><?php _e('Save', 'product-availability-checker'); ?></button>
                        <button class="button pac-delete-zip" data-zip="<?php echo esc_attr($zip); ?>"><?php _e('Delete', 'product-availability-checker'); ?></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3"><?php _e('No zip codes added yet.', 'product-availability-checker'); ?></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div id="pac_pagination" style="margin-top:10px;">
        <?php
        // Get the current page URL without 'paged' parameter
        $current_url = remove_query_arg('paged');

        for ($i = 1; $i <= $pages; $i++):
            // Add/update the paged parameter
            $page_url = add_query_arg('paged', $i, $current_url);
            ?>
            <a href="<?php echo esc_url($page_url); ?>"
               class="pac-page-link <?php echo $i == $current_page ? 'current' : ''; ?>"
               data-page="<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>



</div>

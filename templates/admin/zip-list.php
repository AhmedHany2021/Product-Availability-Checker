<div id="pac-zip-admin">

    <h3><?php esc_html_e( 'Add New ZIP Code', 'product-availability-checker' ); ?></h3>
    <div class="pac-add-form">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pac_new_zip"><?php esc_html_e( 'ZIP Code', 'product-availability-checker' ); ?></label>
                </th>
                <td>
                    <input type="text" id="pac_new_zip" class="regular-text" placeholder="<?php esc_attr_e( 'Enter ZIP code', 'product-availability-checker' ); ?>" maxlength="10">
                    <p class="description"><?php esc_html_e( 'Enter a valid ZIP code (5-10 alphanumeric characters).', 'product-availability-checker' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pac_new_zip_status"><?php esc_html_e( 'Availability Status', 'product-availability-checker' ); ?></label>
                </th>
                <td>
                    <select id="pac_new_zip_status">
                        <option value="1"><?php esc_html_e( 'Available', 'product-availability-checker' ); ?></option>
                        <option value="0"><?php esc_html_e( 'Unavailable', 'product-availability-checker' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="pac_new_zip_message"><?php esc_html_e( 'Custom Message', 'product-availability-checker' ); ?></label>
                </th>
                <td>
                    <textarea id="pac_new_zip_message" class="large-text" rows="3" placeholder="<?php esc_attr_e( 'Optional: Enter a custom message for this ZIP code', 'product-availability-checker' ); ?>" maxlength="500"></textarea>
                    <p class="description"><?php esc_html_e( 'Optional custom message to display when checking availability (max 500 characters).', 'product-availability-checker' ); ?></p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="button" id="pac_add_zip" class="button button-primary"><?php esc_html_e( 'Add ZIP Code', 'product-availability-checker' ); ?></button>
        </p>
    </div>

    <h3><?php esc_html_e( 'Existing ZIP Codes', 'product-availability-checker' ); ?></h3>
    <?php if ( ! empty( $zips_to_show ) ) : ?>
        <table class="wp-list-table widefat fixed striped" id="pac_zip_table">
            <thead>
                <tr>
                    <th class="column-zip"><?php esc_html_e( 'ZIP Code', 'product-availability-checker' ); ?></th>
                    <th class="column-status"><?php esc_html_e( 'Available', 'product-availability-checker' ); ?></th>
                    <th class="column-message"><?php esc_html_e( 'Custom Message', 'product-availability-checker' ); ?></th>
                    <th class="column-actions"><?php esc_html_e( 'Actions', 'product-availability-checker' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $zips_to_show as $zip => $entry ) : ?>
                    <?php
                    $status  = isset( $entry['status'] ) ? (bool) $entry['status'] : false;
                    $message = isset( $entry['message'] ) ? esc_html( $entry['message'] ) : '';
                    ?>
                    <tr>
                        <td class="column-zip">
                            <strong><?php echo esc_html( $zip ); ?></strong>
                        </td>
                        <td class="column-status">
                            <select class="pac-edit-status" data-zip="<?php echo esc_attr( $zip ); ?>">
                                <option value="1" <?php selected( $status, true ); ?>><?php esc_html_e( 'Yes', 'product-availability-checker' ); ?></option>
                                <option value="0" <?php selected( $status, false ); ?>><?php esc_html_e( 'No', 'product-availability-checker' ); ?></option>
                            </select>
                        </td>
                        <td class="column-message">
                            <textarea class="pac-edit-message large-text" rows="2" data-zip="<?php echo esc_attr( $zip ); ?>" maxlength="500" placeholder="<?php esc_attr_e( 'No custom message', 'product-availability-checker' ); ?>"><?php echo esc_textarea( $message ); ?></textarea>
                        </td>
                        <td class="column-actions">
                            <button type="button" class="button pac-save-zip" data-zip="<?php echo esc_attr( $zip ); ?>"><?php esc_html_e( 'Save', 'product-availability-checker' ); ?></button>
                            <button type="button" class="button pac-delete-zip" data-zip="<?php echo esc_attr( $zip ); ?>"><?php esc_html_e( 'Delete', 'product-availability-checker' ); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><?php esc_html_e( 'No ZIP codes added yet.', 'product-availability-checker' ); ?></p>
    <?php endif; ?>

    <?php if ( $pages > 1 ) : ?>
        <div class="pac-pagination">
            <?php
            // Get the current page URL without 'paged' parameter.
            $current_url = remove_query_arg( 'paged' );

            for ( $i = 1; $i <= $pages; $i++ ) :
                // Add/update the paged parameter.
                $page_url = add_query_arg( 'paged', $i, $current_url );
                $class    = ( $i === $current_page ) ? 'current' : '';
                ?>
                <a href="<?php echo esc_url( $page_url ); ?>" class="pac-page-link <?php echo esc_attr( $class ); ?>" data-page="<?php echo esc_attr( $i ); ?>">
                    <?php echo esc_html( $i ); ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

</div>

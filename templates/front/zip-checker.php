<div class="zip-checker">
    <label><?php esc_html_e( 'Check delivery availability', 'product-availability-checker' ); ?></label>

    <div class="zip-input">
        <input type="text" id="zip-code" placeholder="<?php esc_attr_e( 'Enter ZIP code', 'product-availability-checker' ); ?>" maxlength="10">
        <button type="button" id="check-zip"><?php esc_html_e( 'Check', 'product-availability-checker' ); ?></button>
    </div>

    <div id="zip-result"></div>
</div>

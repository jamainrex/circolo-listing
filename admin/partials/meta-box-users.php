<?php
// Use nonce for verification to secure data sending
wp_nonce_field( basename( __FILE__ ), 'circolo_owner_nonce' ); ?>
<div id="ppp-product">
    <p><?php 
esc_attr_e( 'This is the ID of the Listing Owner.', 'wc_circolo_listing' );
?>
        <br>
    </p>
    <label for="woocommerce_ppp_product_id"><?php 
esc_attr_e( 'Select Owner', 'wc_circolo_listing' );
?><br>
		<?php 
echo  $drop_down ;
?>
    </label>
</div>
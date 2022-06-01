<?php
// Use nonce for verification to secure data sending
wp_nonce_field( basename( __FILE__ ), 'circolo_owner_nonce' ); ?>
<div class="listing-info-wrapper">
<div id="listing-info-owner" class="listing-info-item" style="padding-right: 20px;">
    <label for="circolo_listing_owner">
    <h4><?php esc_attr_e( 'Owner (This is the ID of the Listing Owner)', 'wc_circolo_listing' ); ?></h4>
		<?php 
echo  $drop_down ;
?>
    </label>
</div>
<div id="listing-info-country" class="listing-info-item">
    <label for="circolo_listing_country">
    <h4><?php esc_attr_e( 'Country', 'wc_circolo_listing' ); ?></h4>    
		<?php 
echo  $countries_drop_down ;
?>
    </label>
</div>
</div>
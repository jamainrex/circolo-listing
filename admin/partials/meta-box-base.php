<?php
// Use nonce for verification to secure data sending
wp_nonce_field( basename( __FILE__ ), 'circolo_nonce' ); ?>

<!-- Start tabs -->
<ul class="wcppp-tab-bar">
    <li class="wcppp-tab-active"><a href="#wc-ppp-product-info"><?php esc_attr_e( 'Product Information', 'wc_circolo_listing' ); ?></a></li>
</ul>
<div class="wcppp-tab-panel" id="wc-ppp-product-info">
    <?php require 'meta-box-product-info.php'; ?>
</div>
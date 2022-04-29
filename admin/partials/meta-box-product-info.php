<div id="ppp-product">
    <p><?php 
esc_attr_e( 'This is the id of the product that is required to have been purchased before this listing will be available on the Marketplace page.', 'wc_circolo_listing' );
?>
        <br>
    </p>
    <label for="woocommerce_ppp_product_id"><?php 
esc_attr_e( 'Select Product', 'wc_circolo_listing' );
?><br>
		<?php 
echo  $drop_down ;
?>
    </label>
    <a href="<?php 
echo  get_admin_url() ;
?>post-new.php?post_type=product"><?php 
esc_attr_e( 'Create New Product', 'wc_circolo_listing' );
?></a>
	
</div>
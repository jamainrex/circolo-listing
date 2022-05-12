<div class="products_wrapper">
<div id="product_items_wrapper"> 
	<?php foreach( $products as $product ): ?>
		<?php require 'product-item.php'; ?>
	<?php endforeach; ?>
</div>

<div id="product_item_detail_wrapper" style="display: none;"> 
	<?php require 'product-item-detail.php'; ?>
</div>

	<div class="product-button-wrapper">
		<a id="next-btn-selected-post-type" href="#" class="next-button" role="button">Next</a>

		<a id="accept-proceed-selected-post-type" href="#" class="accept-button" role="button">ACCEPT & PROCEED</a>
	</div>

</div>
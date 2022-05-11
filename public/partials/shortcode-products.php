
<div class="products_wrapper">
<div id="product_items_wrapper"> 
	<?php foreach( $products as $product ): ?>
		<?php require 'product-item.php'; ?>
	<?php endforeach; ?>
</div>

<div id="product_item_detail_wrapper" style="display: none;"> 
	<?php require 'product-item-detail.php'; ?>
</div>

	<div class="elementor-button-wrapper" style="text-align: center;">
		<a href="#" style="background-color: #50563c;" class="elementor-button-link elementor-button elementor-size-sm" role="button" id="select-category-next-btn">
		<span class="elementor-button-content-wrapper">
			<span class="elementor-button-text">Next</span>
		</span>
		</a>
	</div>

</div>
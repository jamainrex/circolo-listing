
<div class="products_wrapper" style="display: inline-block;">
<?php foreach( $products as $product ): ?>
    <?php require 'product-item.php'; ?>
<?php endforeach; ?>

<div class="elementor-button-wrapper" style="text-align: center;">
<a href="#" style="background-color: #50563c;" 
class="elementor-button-link elementor-button elementor-size-sm" role="button" id="select-category-next-btn">
						<span class="elementor-button-content-wrapper">
						<span class="elementor-button-text">Next</span>
		</span>
					</a>
</div>
</div>
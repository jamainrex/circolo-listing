<div class="product-item" id="product-<?php echo $product->get_id() ?>" product="<?php echo $product->get_id() ?>">
    <h5 class="product-title">
        <?php echo $product->get_name() ?>
    </h5>
    <div class="product-content">
        <?php echo $product->get_short_description() ?>
    </div>
</div>
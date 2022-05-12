<div class="product-item" id="product-<?php echo $product->get_id() ?>" 
    product="<?php echo $product->get_id() ?>"
    data-price="<?php echo $product->get_price() ?>"
    data-name="<?php echo $product->get_name() ?>"
>
    <h3 class="product-title">
        <?php echo $product->get_name() ?>
    </h3>
    <div class="product-content">
        <?php echo $product->get_short_description() ?>
    </div>
</div>
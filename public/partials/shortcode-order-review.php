<div id="product_item_detail_wrapper"> 
    <div class="product-item-detail">
        <h4 class="product-title">
        <?php echo $product->get_name() ?>
        </h4>
        <div class="product-content">
            <?php echo $product->get_short_description() ?>
        </div>
    </div>
    <div class="product-item-price">
        <p>QUOTE</p>
        <h4 class="product-price">
            <?php echo get_woocommerce_currency_symbol() ?> <span class="product-price-text"><?php echo $product->get_price() ?></span>
        </h4>
        <div class="product-sub-content">
        The price for sending this posting is <span class="product-price-text"><?php echo $product->get_price() ?></span> Inc VAT.
        </div>
    </div>
</div>
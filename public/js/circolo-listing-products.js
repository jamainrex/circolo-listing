(function( $ ) {
	'use strict';

    var selectedPostType = 0;

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    //  $('#select-category-next-btn').on('click', function(e){
    //      e.preventDefault();
    //      var url = jQuery(this).attr('href');
    //     if(selectedCategory > 0){
            
    //         window.location.href=url+'?category='+selectedCategory;
    //     } else {
    //         alert('Please choose a category');
    //     }
    //  });
     
     $('.product-item').on('click', function(e){
        e.preventDefault();
        var productEl = $(this);
        var productId = productEl.attr('product');
        console.log("Product: ", productId);
        
        $('.product-item').removeClass('product-selected');
        selectedPostType = 0;
    
        productEl.addClass('product-selected');
        selectedPostType = productId;
     });

	 var selectPostType = function(productId) {
		 var productEl = $('#product-'+productId);
		 if( productEl.length == -1 )
		 	return;

		var name = productEl.data('name');
		var desc = productEl.find('.product-content').text();
		var price = productEl.data('price');
		var notes = productEl.data('notes');

		var detailEl = $('#product_item_detail_wrapper');
		detailEl.find('.product-title').text(name),
		detailEl.find('.product-content').text(desc),
		detailEl.find('.product-price-text').text(price);
		detailEl.find('.product-notes').text(notes);
		$('.product-item').hide();
		detailEl.show();
		$('#accept-proceed-selected-post-type').show();
		$('#accept-proceed-text').show();
		
		var $offset = 150;
		var $speed = 700;
		$('html, body').animate({
			scrollTop: $('#product_item_detail_wrapper').offset().top - $offset
		  }, $speed);
	 }

	 $('#next-btn-selected-post-type').on('click', function(e){
		 e.preventDefault();
		if( selectedPostType == 0 )
			alert("Please select Type of Post");
		else{
			selectPostType( selectedPostType );
			$(this).hide();
		}
	 })

	//  Add to Cart JS
	var addToCart = function(productId) {
		var data = {
            action: 'woocommerce_ajax_add_to_cart',
            product_id: productId,
        };

		$.ajax({
            type: 'post',
            url: circolo_ajax.url,
            data: data,
            beforeSend: function (response) {
				console.log("BeforeSend: ", response);
            },
            complete: function (response) {
                console.log("Complete: ", response);
            },
            success: function (response) {
				console.log("Success: ", response);
				if(response.success) {
					window.location.href = response.redirect_url;
				}

				if( response.error )
					alert("Whoops something went wrong! Please Try again!");
				
                // if (response.error & response.product_url) {
                //     window.location = response.product_url;
                //     return;
                // } else {
                //     $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                // }
            },
        });
	}

	$('#accept-proceed-selected-post-type').on('click', function (e) {
        e.preventDefault();
		addToCart(selectedPostType);
	});

})( jQuery );

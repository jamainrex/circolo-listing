(function( $ ) {
	'use strict';

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

     $(function() {
        
        var urlParams = new URLSearchParams(window.location.search);
        var marketplace_url = circolo_params.marketplace_url;
        console.log(circolo_params.marketplace_url);
        console.log(urlParams.get('category'));

        $('.category-filter').on('click', function(e) {
            e.preventDefault();
            var el = $(this);
            //var category = el.data('category');
            var url = el.attr('href');
            //console.log("Category: ", category);
            if( urlParams && urlParams.has('country') )
            {
                url += '&country=' + urlParams.get('country');
            }

            if( urlParams && urlParams.has('keyword') )
            {
                url += '&keyword=' + urlParams.get('keyword');
            }

            window.location.href = url;
            //console.log("Category: ", category);
        });

        $('#country-filter').on('change', function(e){
            e.preventDefault();
            var el = $(this);
            var country = el.val();
            var url = marketplace_url + '?country=' + country;
            if( urlParams && urlParams.has('category') )
            {
                url += '&category=' + urlParams.get('category');
            }

            if( urlParams && urlParams.has('keyword') )
            {
                url += '&keyword=' + urlParams.get('keyword');
            }

            window.location.href = url;
        });

        $('#apply-search-filter').on('click', function(e) {
            e.preventDefault();
            var keyword = $('#search-filter').val();
            var url = marketplace_url + '?keyword=' + keyword;

            if( urlParams && urlParams.has('category') )
            {
                url += '&category=' + urlParams.get('category');
            }

            if( urlParams && urlParams.has('country') )
            {
                url += '&country=' + urlParams.get('country');
            }

            window.location.href = url;
        });
     });

})( jQuery );

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
        $('input[type=checkbox].add-fav-input').on('click', function() {
            var el = $(this);
            var pid = el.data('post');
            var add = el.is(':checked');
            //console.log("favorite: ",pid);
            //console.log("add: ",add);

            var data = new FormData();
            data.append('action', 'circolo_listing_user_favorites');
            data.append('pid', pid );
            data.append('add', add );

            var addFavEl = $('.add-fav-input');

            $.ajax({
                type: 'post',
                url: circolo_ajax.url,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function (response) {
                    console.log("BeforeSend: ", response);
                    addFavEl.attr('disabled','disabled');
                    addFavEl.addClass('processing');
                },
                complete: function (response) {
                    console.log("Complete: ", response);
                    console.log("Status", response.status);

                    if(response.status != '200') {
                        el.prop('checked', !add);
                    }

                    addFavEl.attr('disabled',false);
                    addFavEl.removeClass('processing');
                },
                success: function (response) {
                    console.log("Success: ", response);
                    addFavEl.attr('disabled',false);
                    addFavEl.removeClass('processing');
        
                    if( response.error ){
                        el.prop('checked', !add);
                        alert("Whoops something went wrong! Please Try again!");
                    }
                        
                },
            });
        })
     });

})( jQuery );

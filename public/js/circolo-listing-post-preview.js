(function( $ ) {
	'use strict';
  
    $('.show-tab').on('click', function(e) {
        e.preventDefault();
        var tab = $(this).data('tab');

        $('.show-tab').removeClass('active');
        $(this).addClass('active');
        $('.preview-tab').addClass('hide-content');
        $('#'+tab).removeClass('hide-content');

        //alert(tab);
    });

})( jQuery );

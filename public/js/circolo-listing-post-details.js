(function( $ ) {
	'use strict';
  
var imageFiles = [];
var num = 0;
var limit = 4;

	$(document).ready(function() {
		document.getElementById('pro-image').addEventListener('change', readImage, false);

		$( ".preview-images-zone" ).sortable();
		$(document).on('click', '.image-cancel', function() {
			let no = $(this).data('no');
			$(".preview-image.preview-show-"+no).remove();
			imageFiles.splice(no,1);
		});
	  });
  
  

  function readImage() {
	  //console.log("readImage");
	if (window.File && window.FileList && window.FileReader) {
		var files = event.target.files; //FileList object
		var output = $(".preview-images-zone");
		
		for (let i = 0; i < files.length; i++) {
			var file = files[i];
			//console.log("File: ", file);
			var checkdiv = $('div.preview-image').length;
		   // lemit line
			if (num <= limit || checkdiv <= limit){  
			 
			  var num = checkdiv;
			  if (!file.type.match('image')) {
				  	alert(file.name+" File type not supported!");
					continue;
			  }
			  
			  imageFiles[num] = file;
			  //imageFiles.push([num]);
			  var picReader = new FileReader();
			  
			  picReader.addEventListener('load', function (event) {
				  var picFile = event.target;
				  console.log("File: ", picFile);
				  var html =  '<div class="preview-image preview-show-' + num + '" data-type="file" data-file="'+num+'">' +
				  			  //'<input type="file" id="img-'+num+'" name="image['+num+']" style="display: none;" />'+
							  '<div class="image-cancel" data-no="' + num + '">x</div>' +
							  '<div class="image-zone"><img id="pro-img-' + num + '" src="' + picFile.result + '"></div>' +
							  '<div class="tools-edit-image"><a href="javascript:void(0)" data-no="' + num + '" class="btn btn-light btn-edit-image">edit</a></div>' +
							  '</div>';
  
				  output.append(html);
				  num = num + 1;
			  });

			  //$('#img-'+(num-1)).val(file);
		  }
			picReader.readAsDataURL(file);
			//console.log("picReader: ", picReader);
		}
		$("#pro-image").val('');
		//console.log("imageFiles: ", imageFiles);
	} else {
		console.log('Browser not support');
	}
  }

  $('#upload-image-launch').on('click', function(e){
	  e.preventDefault();
	$('#pro-image').click();
  });
  
  $('form#new_post').submit( function(e) {
	e.preventDefault();
	var data = new FormData(this);
	console.log("imageFiles: ", imageFiles);
	// data.append('action', 'circolo_listing_save');
	// data.append('title', $('#title').val());
	// data.append('description', $('#description').val());
	console.log($('.preview-image'));
	$.each( $('.preview-image'), function(i, el) {
		console.log($(el));
		if( $(el).data('type') == 'file' ) {
			console.log("file");
			data.append('file-'+i, imageFiles[i]);
		}
		
		if( $(el).data('type') == 'pid' ) {
			console.log("idx");
			data.append('file-'+i, $(el).data('idx'));
		}
	} );
	// for (let i = 0; i <= 4; i++) {
	// 	console.log("preview div: ", i);
	// 	var isPreview = $('.preview-show-'+i);
	// 	console.log("preview div: ", isPreview);
	// 	var idx = isPreview.data('idx');
	// 	var filex = isPreview.data('file');
		
	// 	if( isPreview.length > 0 ){
	// 		if( filex ){
	// 			data.append('file-'+i, imageFiles[filex]);
	// 		}
	// 		else if( idx ){
	// 			data.append('file-'+i, idx);
	// 		}
	// 	}
	//   }
	// $.each(imageFiles, function(i, file) {
	// 	console.log(i,file);
	// 	var isPreview = $('.preview-show-'+i);
	// 	if( isPreview.length > 0 ){
	// 		if( isPreview.data('file') ){
	// 			data.append('file-'+i, file);
	// 		}
	// 		if(idx = isPreview.data('idx') ){
	// 			data.append('file-'+i, idx);
	// 		}
	// 	}
		
	// });

	//console.log(this);
	//console.log($(this));

	$.ajax({
		type: 'post',
		url: circolo_ajax.url,
		data: data,
		processData: false,
		contentType: false,
		cache: false,
		enctype: 'multipart/form-data',
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
		},
	});
  });
  

})( jQuery );

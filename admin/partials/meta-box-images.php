<?php
// Use nonce for verification to secure data sending
wp_nonce_field( basename( __FILE__ ), 'circolo_images_nonce' ); ?>

<div id="gallery_wrapper">
			<div id="img_box_container">
			<?php 
				for( $i = 1; $i <= 3; $i++ ){
                    $image_id = 0;
                    $image_url = '';

                    if( isset( $listingImages[$i] ) ){ 
                        $listingImage = $listingImages[$i];
                        $image_id = $listingImage[0];
                        $image_url = wp_get_attachment_url( $image_id );
                    }
				?>
				<div class="gallery_single_row dolu">
				  <div class="gallery_area image_container ">
					<img class="gallery_img_img" src="<?php esc_html_e( $image_url ); ?>" height="55" width="55" onclick="open_media_uploader_image_this(this)"/>
					<input type="hidden"
							 class="meta_image_url"
							 name="file-<?php echo $i ?>"
							 value="<?php $image_id ?>"
					  />
				  </div>
				  <div class="gallery_area">
					<span class="button remove" onclick="remove_img(this)" title="Remove"/><i class="fas fa-trash-alt"></i></span>
				  </div>
				  <div class="clear" />
				</div> 
				</div>
				<?php
				}
			?>
			</div>
			<!-- <div style="display:none" id="master_box">
				<div class="gallery_single_row">
					<div class="gallery_area image_container" onclick="open_media_uploader_image(this)">
						<input class="meta_image_url" value="" type="hidden" name="gallery[image_url][]" />
					</div> 
					<div class="gallery_area"> 
						<span class="button remove" onclick="remove_img(this)" title="Remove"/><i class="fas fa-trash-alt"></i></span>
					</div>
					<div class="clear"></div>
				</div>
			</div> -->
			<!-- <div id="add_gallery_single_row">
			  <input class="button add" type="button" value="+" onclick="open_media_uploader_image_plus();" title="Add image"/>
			</div> -->
		</div>
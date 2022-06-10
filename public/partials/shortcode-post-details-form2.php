<div class="post_detail_wrapper" style="display: block; text-align: left;">
<div id="postbox">
    <?php 
    if( !empty($errors) ){
        foreach($errors as $error) {?>
        <p class="errors"><?php echo $error ?></p>
        <?php }
    } ?>
    <?php
            /*Retrieving the image*/
            $attachment = get_post_meta($circolo_listing->ID, '_thumbnail_id');

            
            ?>
    <input id="post-type-title" value="<?php echo $product->get_name() ?>" type="hidden" />
	<input id="post-type-category" value="<?php echo isset($categories[0]) ? $categories[0]->slug : ''?>" type="hidden" />
    <form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">
		<div class="form-container">
			<div class="image-field">
				<fieldset class="form-group image-field-form">
                    <input type="file" id="pro-image0" name="file-0" style="display: none;" class="form-control file-image-0" data-formnum="0">
                    <a href="javascript:void(0)" id="upload-image-launch" class="<?php echo isset($listingImages[0]) ? "hide-upload-btn" : "" ?>">Upload Main Image</a>
					<div id="preview-featured-image-zone" class="preview-images-zone">
                    <?php if( isset($listingImages[0]) ){
                        $listingImage = $listingImages[0];
                        $idx = 0;
                        $image_url = wp_get_attachment_url( $listingImage[0] );
                        ?>
                        <div class="preview-image preview-show-<?php echo $idx ?>" data-type="pid" data-idx="<?php echo $listingImage[0] ?>">
                            <div class="image-cancel" data-no="<?php echo $idx ?>">x</div>
                            <div class="image-zone"><img id="pro-img-<?php echo $idx ?>" src="<?php echo $image_url ?>"></div>
                        </div>
                    <?php } ?>
                    </div>
                </fieldset>
                <fieldset class="form-group image-field-form image-field-item">
                    <input type="file" id="pro-image1" name="file-1" style="display: none;" class="form-control file-image-1" data-formnum="1">
                    <a href="javascript:void(0)" id="upload-image-launch1" class="<?php echo isset($listingImages[1]) ? "hide-upload-btn" : "" ?>">Upload Image</a>
					<div id="preview-image-zone-1" class="preview-images-zone preview-images-zone-addtl">
                    <?php if( isset($listingImages[1]) ){
                        $listingImage = $listingImages[1];
                        $idx = 1;
                        $image_url = wp_get_attachment_url( $listingImage[0] );
                        ?>
                        <div class="preview-image preview-show-<?php echo $idx ?>" data-type="pid" data-idx="<?php echo $listingImage[0] ?>">
                            <div class="image-cancel" data-no="<?php echo $idx ?>">x</div>
                            <div class="image-zone"><img id="pro-img-<?php echo $idx ?>" src="<?php echo $image_url ?>"></div>
                        </div>
                    <?php } ?>
                    </div>
                </fieldset>
				
                <fieldset class="form-group image-field-form image-field-item">
                    <input type="file" id="pro-image2" name="file-2" style="display: none;" class="form-control file-image-2" data-formnum="2">
                    <a href="javascript:void(0)" id="upload-image-launch2" class="<?php echo isset($listingImages[2]) ? "hide-upload-btn" : "" ?>">Upload Image</a>
					<div id="preview-image-zone-2" class="preview-images-zone preview-images-zone-addtl">
                    <?php if( isset($listingImages[2]) ){
                        $listingImage = $listingImages[2];
                        $idx = 2;
                        $image_url = wp_get_attachment_url( $listingImage[0] );
                        ?>
                        <div class="preview-image preview-show-<?php echo $idx ?>" data-type="pid" data-idx="<?php echo $listingImage[0] ?>">
                            <div class="image-cancel" data-no="<?php echo $idx ?>">x</div>
                            <div class="image-zone"><img id="pro-img-<?php echo $idx ?>" src="<?php echo $image_url ?>"></div>
                        </div>
                    <?php } ?>
                    </div>
                </fieldset>

                <fieldset class="form-group image-field-form image-field-item">
                    <input type="file" id="pro-image3" name="file-3" style="display: none;" class="form-control file-image-3" data-formnum="3">
                    <a href="javascript:void(0)" id="upload-image-launch3" class="<?php echo isset($listingImages[3]) ? "hide-upload-btn" : "" ?>">Upload Image</a>
					<div id="preview-image-zone-3" class="preview-images-zone preview-images-zone-addtl">
                    <?php if( isset($listingImages[3]) ){
                        $listingImage = $listingImages[3];
                        $idx = 3;
                        $image_url = wp_get_attachment_url( $listingImage[0] );
                        ?>
                        <div class="preview-image preview-show-<?php echo $idx ?>" data-type="pid" data-idx="<?php echo $listingImage[0] ?>">
                            <div class="image-cancel" data-no="<?php echo $idx ?>">x</div>
                            <div class="image-zone"><img id="pro-img-<?php echo $idx ?>" src="<?php echo $image_url ?>"></div>
                        </div>
                    <?php } ?>
                    </div>
                </fieldset>
				<small>We suggest using landscape or square images</small>
				<?php echo do_shortcode('[elementor-template id="4026"]') ?>
			</div>
			<div class="content-field">
				<p>
					<label for="title">Title</label><br />
					<input type="text" id="title" value="<?php echo $circolo_listing->post_title; ?>" tabindex="1" style="width: 100%;" name="title" minlength="10" maxlength="50" required />
					<span id="title_char_count" class="char_count">50/50</span>
                    <small>min 10 characters and max of 50 characters</small>
				</p>
				<p>
					<label for="short_description">Short Description</label><br />
					<textarea id="short_description" tabindex="3" name="short_description" cols="50" rows="6" minlength="50" maxlength="150" required><?php echo $circolo_listing->post_excerpt; ?></textarea>
					<span id="short_desc_char_count" class="char_count">150/150</span>
                    <small>min 50 characters and a max of 150 characters</small>
				</p>
                <p>
					<label for="description">Full Description</label><br />
                    <textarea id="description" tabindex="3" name="description" cols="50" rows="6" minlength="50" required><?php echo $circolo_listing->post_content; ?></textarea>
					
                </p>
			</div>
        
            
		</div>
        <div class="preview-btn">
            <input type="button" value="CANCEL" tabindex="6" id="cancel-btn" name="cancel" />
            <input type="submit" value="PREVIEW POST" tabindex="6" id="submit" name="submit" /></div>
        <input type="hidden" name="action" value="circolo_listing_save" />
        <input type="hidden" id="pid" name="pid" value="<?php echo $circolo_listing->ID; ?>" />
        <?php wp_nonce_field( 'new-post' ); ?>
    </form>
</div>
</div>
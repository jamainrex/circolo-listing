<div class="post_detail_wrapper" style="display: block; text-align: left;">
<div id="postbox">
    <?php 
    if( !empty($errors) ){
        foreach($errors as $error) {?>
        <p class="errors"><?php echo $error ?></p>
        <?php }
    } ?>
    <form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data">
		<div class="form-container">
			<div class="image-field">
				<fieldset class="form-group image-field-form">
					<a href="javascript:void(0)" id="upload-image-launch">Upload Image</a>
					<input type="file" id="pro-image" name="thumbnail" style="display: none;" multiple class="form-control">
				</fieldset>
				<div class="preview-images-zone">

                <?php if( !empty($listingImages) ) {
                    foreach( $listingImages as $idx => $listingImage ) { 
                    if( $listingImage[0]!='' ){
                        $image_url = wp_get_attachment_url( $listingImage[0] );
                ?>

                <div class="preview-image preview-show-<?php echo $idx ?>" data-type="pid" data-idx="<?php echo $listingImage[0] ?>">
                    <div class="image-cancel" data-no="<?php echo $idx ?>">x</div>
                    <div class="image-zone"><img id="pro-img-<?php echo $idx ?>" src="<?php echo $image_url ?>"></div>
                    <div class="tools-edit-image"><a href="javascript:void(0)" data-no="<?php echo $idx ?>" class="btn btn-light btn-edit-image">edit</a></div>
                </div>

                <?php
                        }  
                    }
                } 
                ?>
                
                </div>
			</div>
			<div class="content-field">
				<p>
					<label for="title">Title</label><br />
					<input type="text" id="title" value="<?php echo $circolo_listing->post_title; ?>" tabindex="1" style="width: 100%;" name="title" />
					<small>Min 10 characters and Max of 50 Characters you are at 10</small>
				</p>
				<p>
					<label for="description">Short Description</label><br />
					<textarea id="description" tabindex="3" name="description" cols="50" rows="6"><?php echo $circolo_listing->post_content; ?></textarea>
					<small>Min 200 Chacters and a Max of 400 Characters</small>
				</p>
			</div>
        
            <?php
            /*Retrieving the image*/
            // $attachment = get_post_meta($circolo_listing->ID, '_thumbnail_id');

            // if($attachment[0]!='')
            // {
            //     echo wp_get_attachment_link($attachment[0], 'thumbnail', false, false);
            // }

            ?>
		</div>
        <div class="preview-btn"><input type="submit" value="PREVIEW POST" tabindex="6" id="submit" name="submit" /></div>
        <input type="hidden" name="action" value="circolo_listing_save" />
        <input type="hidden" name="pid" value="<?php echo $circolo_listing->ID; ?>" />
        <?php wp_nonce_field( 'new-post' ); ?>
    </form>
</div>
</div>
<div class="post_detail_wrapper" style="display: block; text-align: left;">
<div id="postbox" style="width: 50%; margin: auto;">
    <form id="new_post" name="new_post" method="post" action="">
        <p>
            <label for="title">Title</label><br />
            <input type="text" id="title" value="<?php echo $circolo_listing->post_title; ?>" tabindex="1" style="width: 100%;
}" name="title" />
        </p>
        <p>
            <label for="description">Short Description</label><br />
            <textarea id="description" tabindex="3" name="description" cols="50" rows="6"><?php echo $circolo_listing->post_content; ?></textarea>
        </p>
        <p style="text-align: center;"><input type="submit" value="PREVIEW POST" tabindex="6" id="submit" name="submit" /></p>
        <input type="hidden" name="action" value="f_edit_post" />
        <input type="hidden" name="pid" value="<?php echo $circolo_listing->ID; ?>" />
        <?php wp_nonce_field( 'new-post' ); ?>
    </form>
</div>
</div>
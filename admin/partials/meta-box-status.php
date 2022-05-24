<?php
// Use nonce for verification to secure data sending
wp_nonce_field( basename( __FILE__ ), 'circolo_status_nonce' ); ?>

<div class="wrap container">
    <input id="force_approve" type="checkbox" value="force_approve" name="force-approve">
    <label for="force_approve"><strong>Force Approved</strong> <small>If checked, it will force approve listing and update approved date today.</small></label>
</div>
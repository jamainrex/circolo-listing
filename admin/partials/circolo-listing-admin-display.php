<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://circolo.club
 * @since      1.0.0
 *
 * @package    Circolo_Listing
 * @subpackage Circolo_Listing/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	 <!--NEED THE settings_errors below so that the errors/success messages are shown after submission - wasn't working once we started using add_menu_page and stopped using add_options_page so needed this-->
    <h3>CREATE A LISTING</h3>
 
    <p>Listing last minute availability for your family villa? Selling your home-made honey and artisanal hampers? In need of a nanny over summer? A trusted handyman in Milano? A maths tutor for the children over the ski season? The world of well-mannered exchanges and mobilised assets just got smaller with Circolo. We’ll guide you through how to list and reach our trusted members with four easy steps.</p> 
    
    <h4>1. CREATE</h4> 
    <p>Select the category for your listing: GOODS, EXPERIENCES,SERVICES, PROPERTY.<br />
    Write a detailed, compelling description and add good quality images.</p>
    
    <h4>2. SUBMIT</h4>
    <p>Review price, grammar and layout, then send your listing off for approval. The Circolo team will vet listings to ensure it meets Circolo standards. NB. you will not be charged if the listing does not pass this approval phase.</p>
    
    <h4>3. APPROVE</h4>
    <p>Once approved, your listing will be launched into the Circolo marketplace where it is showcased to members. You will only be charged once the listing is approved.</p>
    
    <h4>4. CONNECT</h4> 
    <p>Your listing is now live in the Circolo marketplace where members can connect with you and ask questions. Interact and tap into this global-local network, buying, selling and searching for almost anything in a dynamic, safe space.</p>
 
</div>
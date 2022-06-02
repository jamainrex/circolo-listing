<div class="listing-preview-header">
    <ul class="listing-preview-actions">
    <li><a class="<?php if($category == 'all' || empty($category) ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=all">All</a></li>
	<li><a class="<?php if($category == 'goods' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=goods">GOODS</a></li>
    <li><a class="<?php if($category == 'services' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=services">SERVICES</a></li> 
    <li><a class="<?php if($category == 'experiences' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=experiences">EXPERIENCES</a></li>   
    <li><a class="<?php if($category == 'property' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=property">PROPERTY</a></li>
</ul>
</div>
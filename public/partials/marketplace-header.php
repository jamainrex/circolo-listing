<div class="listing-preview-header">
    <ul class="listing-preview-actions">
    <li><a data-category="all" class="category-filter <?php if($category == 'all' || empty($category) ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=all">All</a></li>
	<li><a data-category="goods" class="category-filter <?php if($category == 'goods' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=goods">GOODS</a></li>
    <li><a data-category="services" class="category-filter <?php if($category == 'services' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=services">SERVICES</a></li> 
    <li><a data-category="experiences" class="category-filter <?php if($category == 'experiences' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=experiences">EXPERIENCES</a></li>   
    <li><a data-category="property" class="category-filter <?php if($category == 'property' ) echo 'active'; ?>" href="<?php echo site_url() ?>/marketplace/?category=property">PROPERTY</a></li>
</ul>
<div class="marketplace-header">
	<div class="marketplace-header-countries">
		<?php require 'marketplace-header-countries.php'; ?>
	</div>
	<div class="marketplace-header-search">
		<?php require 'marketplace-header-search.php'; ?>
	</div>
</div>
</div>
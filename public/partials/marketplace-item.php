<div style="background-color: #ffffff;">
    <ul>
    <li><img style="width: 150px;" src="<?php echo $featured_image ?>" />
    <li>Category: <?php echo $listing_category ?></li>
    <li>Title: <?php echo the_title() ?></li>    
    <li>Short Description: <?php echo the_excerpt() ?></li>
    <li><?php echo $days_ago ?> DAYS AGO</li>
    <li><?php the_author() ?></li>
</ul>
</div>
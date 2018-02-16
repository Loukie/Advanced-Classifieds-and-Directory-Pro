<?php

/**
 * This template displays the public-facing aspects of the widget.
 */
?>

<div class="acadp acadp-widget-listing-address">
    <!-- Listing Map -->
    <?php if( $can_show_map ) : ?>
    	<div class="embed-responsive embed-responsive-16by9 acadp-margin-bottom">
    		<div class="acadp-map embed-responsive-item">
				<div class="marker" data-latitude="<?php echo $post_meta['latitude'][0]; ?>" data-longitude="<?php echo $post_meta['longitude'][0]; ?>"></div> 
       		</div>
        </div>
	<?php endif; ?>
    
    <!-- Listing Address -->
	<?php the_acadp_address( $post_meta, $location->term_id ); ?>
</div>
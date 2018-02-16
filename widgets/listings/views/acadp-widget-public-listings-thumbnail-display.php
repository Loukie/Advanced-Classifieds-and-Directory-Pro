<?php

/**
 * This template displays the public-facing aspects of the widget.
 */
?>

<div class="acadp acadp-widget-listings acadp-grid-thumbnail">

	<!-- the loop -->
    <?php 
		$columns = $instance['columns'];
		$span = 'col-md-' . floor( 12 / $columns );
		$i = 0; 
	?>  
      
	<?php while( $acadp_query->have_posts() ) : $acadp_query->the_post(); $post_meta = get_post_meta( $post->ID ); ?>
    
    	<?php if( $i % $columns == 0 ) : ?>
  			<div class="row">
        <?php endif; ?>
        
        	<div class="<?php echo $span; ?>">
            	<div <?php the_acadp_listing_entry_class( $post_meta, 'thumbnail' ); ?>>
                	<?php if( $instance['has_images'] && $instance['show_image'] ) : ?>
                		<a href="<?php the_permalink(); ?>"><?php the_acadp_listing_thumbnail( $post_meta ); ?></a>      	
            		<?php endif; ?>
            
            		<div class="caption">
                    	<div class="acadp-listings-title-block">
                    		<h3 class="acadp-no-margin"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php the_acadp_listing_labels( $post_meta ); ?>
                        </div>
                        
                        <?php
							$info = array();					
		
							if( $instance['show_date'] ) {
								$info[] = sprintf( __( 'Posted %s ago', 'advanced-classifieds-and-directory-pro' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
							}
							
							if( $instance['show_user'] ) {			
								$info[] = '<a href="'.acadp_get_user_page_link( $post->post_author ).'">'.get_the_author().'</a>';
							}

							echo '<p class="acadp-no-margin"><small class="text-muted">'.implode( ' '.__( "by", 'advanced-classifieds-and-directory-pro' ).' ', $info ).'</small></p>';
							
							if( $instance['show_description'] ) {
								echo '<p class="acadp-listings-desc">'.wp_trim_words( get_the_content(), $this->listings_settings['excerpt_length'], '...' ).'</p>';
							}

							$info = array();					
		
							if( $instance['show_category'] && $category = wp_get_object_terms( $post->ID, 'acadp_categories' ) ) {
								$info[] = '<span class="glyphicon glyphicon-briefcase"></span>&nbsp;<a href="'.acadp_get_category_page_link( $category[0] ).'">'.$category[0]->name.'</a>';
							}
					
							if( $instance['has_location'] && $instance['show_location'] && $location = wp_get_object_terms( $post->ID, 'acadp_locations' ) ) {
								$info[] = '<span class="glyphicon glyphicon-map-marker"></span>&nbsp;<a href="'.acadp_get_location_page_link( $location[0] ).'">'.$location[0]->name.'</a>';
							}
					
							if( $instance['show_views'] && ! empty( $post_meta['views'][0] ) ) {
								$info[] = sprintf( __( "%d views", 'advanced-classifieds-and-directory-pro' ), $post_meta['views'][0] );
							}

							echo '<p class="acadp-no-margin"><small>'.implode( ' / ', $info ).'</small></p>';
	
                			if( $instance['has_price'] && $instance['show_price'] && isset( $post_meta['price'] ) && $post_meta['price'][0] > 0 ) {
								$price = acadp_format_amount( $post_meta['price'][0] );						
								echo '<p class="lead acadp-listings-price">'.acadp_currency_filter( $price ).'</p>';
							}            		
                		?>
                    </div>
                </div>
            </div>
            
    	<?php 
			$i++;
			if( $i % $columns == 0 || $i == $acadp_query->post_count ) : ?>
  				</div>
        <?php endif; ?> 
               
  	<?php endwhile; ?>
    <!-- end of the loop -->
    
    <!-- Use reset postdata to restore orginal query -->
    <?php wp_reset_postdata(); ?>
    
</div>
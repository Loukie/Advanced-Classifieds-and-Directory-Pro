<?php

/**
 * This template displays the ACADP locations list.
 */
?>

<div class="acadp acadp-locations">
    <?php			
		$span = 'col-md-' . floor( 12 /  $locations_settings['columns'] );
		--$locations_settings['depth'];
		$i = 0;
			
		foreach( $terms as $term ) {
			
			$locations_settings['term_id'] = $term->term_id;
			
			if( $i % $locations_settings['columns'] == 0 ) {
				echo '<div class="row">';
			}
				
			echo '<div class="' . $span . '">'; 
			echo '<a href="' . acadp_get_location_page_link( $term ) . '" title="' . sprintf( __( "View all posts in %s", 'advanced-classifieds-and-directory-pro' ), $term->name ) . '" ' . '>';
			echo '<strong>' . $term->name . '</strong>';
			if( ! empty( $locations_settings['show_count'] ) ) {
				echo ' (' .  acadp_get_listings_count_by_location( $term->term_id, $locations_settings['pad_counts'] ) . ')';
			}
			echo '</a>';
			echo acadp_list_locations( $locations_settings );
			echo '</div>';
				
			$i++;
			if( $i % $locations_settings['columns'] == 0 || $i == count( $terms ) ) {
				echo '</div>';
			}		
							
		}
	?>
</div>

<?php the_acadp_social_sharing_buttons(); ?>
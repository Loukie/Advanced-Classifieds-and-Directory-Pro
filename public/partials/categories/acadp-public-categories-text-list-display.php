<?php

/**
 * This template displays the ACADP categories list.
 */
?>

<div class="acadp acadp-categories acadp-text-list">
    <?php			
		$span = 'col-md-' . floor( 12 /  $categories_settings['columns'] );
		--$categories_settings['depth'];
		$i = 0;
			
		foreach( $terms as $term ) {
			
			$categories_settings['term_id'] = $term->term_id;
			
			$count = 0;
			if( ! empty( $categories_settings['hide_empty'] ) || ! empty( $categories_settings['show_count'] ) ) {
				$count = acadp_get_listings_count_by_category( $term->term_id, $categories_settings['pad_counts'] );
				
				if( ! empty( $categories_settings['hide_empty'] ) && 0 == $count ) continue;
			}
			
			if( $i % $categories_settings['columns'] == 0 ) {
				echo '<div class="row acadp-no-margin">';
			}
				
			echo '<div class="' . $span . '">'; 
			echo '<a href="' . acadp_get_category_page_link( $term ) . '" title="' . sprintf( __( "View all posts in %s", 'advanced-classifieds-and-directory-pro' ), $term->name ) . '" ' . '>';
			echo '<strong>' . $term->name . '</strong>';
			if( ! empty( $categories_settings['show_count'] ) ) {
				echo ' (' .  $count . ')';
			}
			echo '</a>';
			echo acadp_list_categories( $categories_settings );
			echo '</div>';
				
			$i++;
			if( $i % $categories_settings['columns'] == 0 || $i == count( $terms ) ) {
				echo '</div>';
			}
							
		}
	?>
</div>

<?php the_acadp_social_sharing_buttons(); ?>
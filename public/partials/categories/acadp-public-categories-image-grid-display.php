<?php

/**
 * This template displays the ACADP categories list.
 */
?>

<div class="acadp acadp-categories acadp-image-grid">
    <?php			
		$span = 'col-md-' . floor( 12 /  $categories_settings['columns'] );
		$i = 0;
			
		foreach( $terms as $term ) {
		
			$count = 0;
			if( ! empty( $categories_settings['hide_empty'] ) || ! empty( $categories_settings['show_count'] ) ) {
				$count = acadp_get_listings_count_by_category( $term->term_id, $categories_settings['pad_counts'] );
				
				if( ! empty( $categories_settings['hide_empty'] ) && 0 == $count ) continue;
			}		
			
			if( $i % $categories_settings['columns'] == 0 ) {
				echo '<div class="row acadp-no-margin">';
			}
				
			echo '<div class="' . $span . '">';			
			echo '<div class="thumbnail">';
			
			$image_id = get_term_meta( $term->term_id, 'image', true );
			if( $image_id ) {
				$image_attributes = wp_get_attachment_image_src( (int) $image_id, 'medium' );
				$image = $image_attributes[0];
				
				if( '' !== $image ) {
					echo '<a href="' . acadp_get_category_page_link( $term ) . '" class="acadp-responsive-container" title="' . sprintf( __( "View all posts in %s", 'advanced-classifieds-and-directory-pro' ), $term->name ) . '" ' . '>';
					echo '<img src="'.$image.'" class="acadp-responsive-item" />';
					echo '</a>';
				}
			}
		
			echo '<div class="caption">';
			echo '<h3 class="acadp-no-margin">';
			echo '<a href="' . acadp_get_category_page_link( $term ) . '" title="' . sprintf( __( "View all posts in %s", 'advanced-classifieds-and-directory-pro' ), $term->name ) . '" ' . '>';
			echo '<strong>' . $term->name . '</strong>';
			if( ! empty( $categories_settings['show_count'] ) ) {
				echo ' (' .  $count . ')';
			}
			echo '</a>';
			echo '</h3>';
			echo '</div>';
			
			echo '</div>';			
			echo '</div>';
				
			$i++;
			if( $i % $categories_settings['columns'] == 0 || $i == count( $terms ) ) {
				echo '</div>';
			}
							
		}
	?>
</div>

<?php the_acadp_social_sharing_buttons(); ?>
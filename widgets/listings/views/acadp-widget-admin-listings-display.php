<?php

/**
 * This template displays the administration form of the widget.
 */
?>

<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'advanced-classifieds-and-directory-pro' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
</p>

<?php if( $instance['has_location'] ) : ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Filter by Location', 'advanced-classifieds-and-directory-pro' ); ?></label> 
        <?php
            wp_dropdown_categories( array(
                'show_option_none'  => '-- '.__( 'Select a Location', 'advanced-classifieds-and-directory-pro' ).' --',
                'option_none_value' => $instance['base_location'],
                'child_of'          => $instance['base_location'],
                'taxonomy'          => 'acadp_locations',
                'name' 			    => $this->get_field_name( 'location' ),
                'class'             => 'widefat',
                'orderby'           => 'name',
                'selected'          => (int) $instance['location'],
                'hierarchical'      => true,
                'depth'             => 10,
                'show_count'        => false,
                'hide_empty'        => false,
            ) );
        ?>
    </p>
<?php endif; ?>

<p>
	<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Filter by Category', 'advanced-classifieds-and-directory-pro' ); ?></label> 
	<?php
    	wp_dropdown_categories( array(
        	'show_option_none'  => '-- '.__( 'Select a Category', 'advanced-classifieds-and-directory-pro' ).' --',
			'option_none_value' => 0,
            'taxonomy'          => 'acadp_categories',
            'name' 			    => $this->get_field_name( 'category' ),
			'class'             => 'widefat',
            'orderby'           => 'name',
			'selected'          => (int) $instance['category'],
            'hierarchical'      => true,
            'depth'             => 10,
            'show_count'        => false,
            'hide_empty'        => false,
        ) );
	?>
</p>

<p>
	<input <?php checked( $instance['related_listings'] ); ?> id="<?php echo $this->get_field_id( 'related_listings' ); ?>" name="<?php echo $this->get_field_name( 'related_listings' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'related_listings' ); ?>"><?php _e( 'Related Listings', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<?php if( $instance['has_featured'] ) : ?>
    <p>
        <input <?php checked( $instance['featured'] ); ?> id="<?php echo $this->get_field_id( 'featured' ); ?>" name="<?php echo $this->get_field_name( 'featured' ); ?>" type="checkbox" />
        <label for="<?php echo $this->get_field_id( 'featured' ); ?>"><?php _e( 'Featured Only', 'advanced-classifieds-and-directory-pro' ); ?></label>
    </p>
<?php endif; ?>

<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'advanced-classifieds-and-directory-pro' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By', 'advanced-classifieds-and-directory-pro' ); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>"> 
		<?php
			$options = array(
				'title' => __( 'Title', 'advanced-classifieds-and-directory-pro' ),
				'date'  => __( 'Date posted', 'advanced-classifieds-and-directory-pro' ),
				'price' => __( 'Price', 'advanced-classifieds-and-directory-pro' ),
				'views' => __( 'Views count', 'advanced-classifieds-and-directory-pro' ),
				'rand'  => __( 'Random', 'advanced-classifieds-and-directory-pro' )
			);
		
			foreach( $options as $key => $value ) {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $instance['orderby'] ), $value );
			}
		?>
    </select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order', 'advanced-classifieds-and-directory-pro' ); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>"> 
		<?php
			$options = array(
				'asc'  => __( 'ASC', 'advanced-classifieds-and-directory-pro' ),
				'desc' => __( 'DESC', 'advanced-classifieds-and-directory-pro' )
			);
		
			foreach( $options as $key => $value ) {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $instance['order'] ), $value );
			}
		?>
    </select>
</p>

<div class="widget-title" style="background: #fafafa; border: 1px solid #e5e5e5;">
	<h4 style="text-transform: uppercase;"><?php _e( 'Display Options', 'advanced-classifieds-and-directory-pro' ); ?></h4>
</div>

<p>
	<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php _e( 'View', 'advanced-classifieds-and-directory-pro' ); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>"> 
		<?php
			$options = array(
				'standard' => __( 'Standard', 'advanced-classifieds-and-directory-pro' ),
				'map'      => __( 'Map', 'advanced-classifieds-and-directory-pro' )
			);
		
			foreach( $options as $key => $value ) {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $instance['view'] ), $value );
			}
		?>
    </select>
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'Number of columns', 'advanced-classifieds-and-directory-pro' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="text" value="<?php echo esc_attr( $instance['columns'] ); ?>">
</p>

<?php if( $instance['has_images'] ) : ?>
    <p>
        <input <?php checked( $instance['show_image'] ); ?> id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" type="checkbox" />
        <label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Show Image', 'advanced-classifieds-and-directory-pro' ); ?></label>
    </p>
    
    <p>
        <label for="<?php echo $this->get_field_id( 'image_position' ); ?>"><?php _e( 'Image Position', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id( 'image_position' ); ?>" name="<?php echo $this->get_field_name( 'image_position' ); ?>"> 
            <?php
                $options = array(
                    'top'  => __( 'Top', 'advanced-classifieds-and-directory-pro' ),
                    'left' => __( 'Left', 'advanced-classifieds-and-directory-pro' )
                );
            
                foreach( $options as $key => $value ) {
                    printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $instance['image_position'] ), $value );
                }
            ?>
        </select>
    </p>
<?php endif; ?>

<p>
	<input <?php checked( $instance['show_description'] ); ?> id="<?php echo $this->get_field_id( 'show_description' ); ?>" name="<?php echo $this->get_field_name( 'show_description' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'show_description' ); ?>"><?php _e( 'Show Description', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<p>
	<input <?php checked( $instance['show_category'] ); ?> id="<?php echo $this->get_field_id( 'show_category' ); ?>" name="<?php echo $this->get_field_name( 'show_category' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'show_category' ); ?>"><?php _e( 'Show Category', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<?php if( $instance['has_location'] ) : ?>
    <p>
        <input <?php checked( $instance['show_location'] ); ?> id="<?php echo $this->get_field_id( 'show_location' ); ?>" name="<?php echo $this->get_field_name( 'show_location' ); ?>" type="checkbox" />
        <label for="<?php echo $this->get_field_id( 'show_location' ); ?>"><?php _e( 'Show Location', 'advanced-classifieds-and-directory-pro' ); ?></label>
    </p>
<?php endif; ?>

<?php if( $instance['has_price'] ) : ?>
    <p>
        <input <?php checked( $instance['show_price'] ); ?> id="<?php echo $this->get_field_id( 'show_price' ); ?>" name="<?php echo $this->get_field_name( 'show_price' ); ?>" type="checkbox" />
        <label for="<?php echo $this->get_field_id( 'show_price' ); ?>"><?php _e( 'Show Price', 'advanced-classifieds-and-directory-pro' ); ?></label>
    </p>
<?php endif; ?>

<p>
	<input <?php checked( $instance['show_date'] ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Show Date', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<p>
	<input <?php checked( $instance['show_user'] ); ?> id="<?php echo $this->get_field_id( 'show_user' ); ?>" name="<?php echo $this->get_field_name( 'show_user' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'show_user' ); ?>"><?php _e( 'Show User', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<p>
	<input <?php checked( $instance['show_views'] ); ?> id="<?php echo $this->get_field_id( 'show_views' ); ?>" name="<?php echo $this->get_field_name( 'show_views' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'show_views' ); ?>"><?php _e( 'Show Views', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>
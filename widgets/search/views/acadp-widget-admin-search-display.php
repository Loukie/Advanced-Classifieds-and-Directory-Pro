<?php

/**
 * This template displays the administration form of the widget.
 */
?>

<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'advanced-classifieds-and-directory-pro' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
</p>
     
<p>
	<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style', 'advanced-classifieds-and-directory-pro' ); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>"> 
		<?php
			$options = array(
				'vertical' => __( 'Vertical', 'advanced-classifieds-and-directory-pro' ),
				'inline'   => __( 'Inline', 'advanced-classifieds-and-directory-pro' )
			);
		
			foreach( $options as $key => $value ) {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $instance['style'] ), $value );
			}
		?>
    </select>
</p>
   
<p>
	<input <?php checked( $instance['search_by_category'] ); ?> id="<?php echo $this->get_field_id( 'search_by_category' ); ?>" name="<?php echo $this->get_field_name( 'search_by_category' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'search_by_category' ); ?>"><?php _e( 'Search by Category', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<?php if( $has_location ) : ?>
	<p>
		<input <?php checked( $instance['search_by_location'] ); ?> id="<?php echo $this->get_field_id( 'search_by_location' ); ?>" name="<?php echo $this->get_field_name( 'search_by_location' ); ?>" type="checkbox" />
		<label for="<?php echo $this->get_field_id( 'search_by_location' ); ?>"><?php _e( 'Search by Location', 'advanced-classifieds-and-directory-pro' ); ?></label>
	</p>
<?php endif; ?>

<p>
	<input <?php checked( $instance['search_by_custom_fields'] ); ?> id="<?php echo $this->get_field_id( 'search_by_custom_fields' ); ?>" name="<?php echo $this->get_field_name( 'search_by_custom_fields' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'search_by_custom_fields' ); ?>"><?php _e( 'Search by Custom Fields', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<?php if( $has_price ) : ?>
	<p>
		<input <?php checked( $instance['search_by_price'] ); ?> id="<?php echo $this->get_field_id( 'search_by_price' ); ?>" name="<?php echo $this->get_field_name( 'search_by_price' ); ?>" type="checkbox" />
		<label for="<?php echo $this->get_field_id( 'search_by_price' ); ?>"><?php _e( 'Search by Price', 'advanced-classifieds-and-directory-pro' ); ?></label>
	</p>
<?php endif; ?>
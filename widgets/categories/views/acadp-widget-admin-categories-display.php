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
	<label for="<?php echo $this->get_field_id( 'parent' ); ?>"><?php _e( 'Select Parent', 'advanced-classifieds-and-directory-pro' ); ?></label> 
	<?php
    	wp_dropdown_categories( array(
        	'show_option_none'  => '-- '.__( 'Select Parent', 'advanced-classifieds-and-directory-pro' ).' --',
			'option_none_value' => 0,
            'taxonomy'          => 'acadp_categories',
            'name' 			    => $this->get_field_name( 'parent' ),
			'class'             => 'widefat',
            'orderby'           => 'name',
			'selected'          => (int) $instance['parent'],
            'hierarchical'      => true,
            'depth'             => 10,
            'show_count'        => false,
            'hide_empty'        => false,
        ) );
	?>
</p>

<p>
	<input <?php checked( $instance['imm_child_only'] ); ?> id="<?php echo $this->get_field_id( 'imm_child_only' ); ?>" name="<?php echo $this->get_field_name( 'imm_child_only' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'imm_child_only' ); ?>"><?php _e( 'Show only the immediate children of the selected category. Displays all the top level categories if no parent is selected.', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<p>
	<input <?php checked( $instance['hide_empty'] ); ?> id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php _e( 'Hide Empty Categories', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>

<p>
	<input <?php checked( $instance['show_count'] ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox" />
	<label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Show Listing Counts', 'advanced-classifieds-and-directory-pro' ); ?></label>
</p>
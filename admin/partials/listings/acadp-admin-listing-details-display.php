<?php

/**
 * Display the "Listing Details" meta box.
 */
?>

<table class="acadp-input widefat">
  <tbody>
    <tr>
      <td class="label">
        <label><?php _e( 'Category', 'advanced-classifieds-and-directory-pro' ); ?></label>
      </td>
      <td>
      	<?php
			$selected_category = count( $category ) ? $category[0] : -1;
			
			$args = array(
            	'show_option_none' => '-- '.__( 'Select a Category', 'advanced-classifieds-and-directory-pro' ).' --',
            	'taxonomy'         => 'acadp_categories',
            	'name' 			   => 'acadp_category',
            	'orderby'          => 'name',
            	'selected'         => $selected_category,
            	'hierarchical'     => true,
            	'depth'            => 10,
            	'show_count'       => false,
            	'hide_empty'       => false,
        	);
			
			if( $disable_parent_categories ) {
				$args['walker'] = new ACADP_Walker_CategoryDropdown;
			}
			
			wp_dropdown_categories( $args );
		?>
      </td>
    </tr>
    <?php if( $has_price ) : ?>
    	<tr>
      		<td class="label">
        		<label><?php printf( '%s [%s]', __( "Price", 'advanced-classifieds-and-directory-pro' ), acadp_get_currency() ); ?></label>
      		</td>
      		<td>
        		<div class="acadp-input-wrap">
          			<input type="text" class="text" name="price" placeholder="<?php _e( 'How much do you want it to be listed for?', 'advanced-classifieds-and-directory-pro' ); ?>" value="<?php if( isset( $post_meta['price'] ) ) echo acadp_format_amount( $post_meta['price'][0] ); ?>" />
        		</div>
      		</td>
    	</tr>  
    <?php endif; ?> 
  </tbody>
</table>

<div id="acadp-custom-fields-list" data-post_id="<?php echo $post->ID; ?>">
  <?php do_action( 'wp_ajax_acadp_custom_fields_listings', $post->ID, $selected_category ); ?>
</div>

<table class="acadp-input widefat">
  <tbody>
    <tr>
      <td class="label" style="border-top: 1px solid #f0f0f0;">
        <label><?php _e( "Views count", 'advanced-classifieds-and-directory-pro' ); ?></label>
      </td>
      <td style="border-top: 1px solid #f0f0f0;">
        <div class="acadp-input-wrap">
          <input type="text" class="text" name="views" value="<?php if( isset( $post_meta['views'] ) ) echo $post_meta['views'][0]; ?>" />
        </div>
      </td>
    </tr>   
  </tbody>
</table>
<?php

/**
 * Display the "Images" meta box.
 */
?>

<?php if( $general_settings['has_images'] ) : ?>
	<table id="acadp-images" class="acadp-images acadp-no-border widefat">
		<?php
    		if( isset( $post_meta['images'] ) ) {
	
				$images = unserialize( $post_meta['images'][0] );
		    
    			for( $i = 0; $i < count( $images ); $i++ ) {
		
					$image_attributes = wp_get_attachment_image_src( $images[ $i ] );
			        
        			if( isset( $image_attributes[0] ) )  {
					
        				echo '<tr class="acadp-image-row">' . 
            					'<td class="acadp-handle"><span class="dashicons dashicons-screenoptions"></span></td>' .         	
            					'<td class="acadp-image">' . 
                					'<img src="' . $image_attributes[0] . '" />' . 
                					'<input type="hidden" name="images[]" value="' . $images[ $i ] . '" />' . 
                				'</td>' . 
                				'<td>' .
									$image_attributes[0] . '<br />' . 
									'<a href="post.php?post=' . $images[ $i ] . '&action=edit" target="_blank">' . __( 'Edit', 'advanced-classifieds-and-directory-pro' ) . '</a> | ' . 
                    				'<a href="javascript:void(0)" class="acadp-delete-image" data-attachment_id="' . $images[ $i ] . '">' . 
										__( 'Delete Permanently', 'advanced-classifieds-and-directory-pro' ) .
									'</a>' . 
                				'</td>' .              
        				  	'</tr>';
						  
					} // endif
				
				} // endfor
			
			} // endif
		?>
	</table>

	<p class="hide-if-no-js">
    	<a class="button" href="javascript:;" id="acadp-upload-image"><?php _e( 'Upload Image', 'advanced-classifieds-and-directory-pro' ); ?></a>
	</p>
<?php endif; ?>
<?php

/**
 * Display the "Video" meta box.
 */
?>

<?php if( $general_settings['has_video'] ) : ?>
	<table id="acadp-video" class="acadp-input acadp-video acadp-no-border widefat">
		<tr>
    		<td class="label">
        		<label><?php _e( 'Your Video URL', 'advanced-classifieds-and-directory-pro' ); ?></label>
                <p class="description"><?php _e( 'Only YouTube &  Vimeo URLs', 'advanced-classifieds-and-directory-pro' ); ?></p>
      		</td>
      		<td>
        		<div class="acadp-input-wrap">
          			<input type="text" class="text" name="video" value="<?php if( isset( $post_meta['video'] ) ) echo esc_attr( $post_meta['video'][0] ); ?>" />
        		</div>
      		</td>
    	</tr>
	</table>
<?php endif; ?>
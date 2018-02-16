<?php

/**
 * Add fields to the "Publish" meta box.
 */
?>

<?php if( 0 == $never_expires && isset( $post_meta['expiry_date'] ) ) : ?>
	<div class="misc-pub-section misc-pub-acadp-expiration-time">
		<span id="acadp-timestamp">
			<strong><?php _e( "Expiration", 'advanced-classifieds-and-directory-pro' ); ?></strong>
			<?php _e( "Date & Time", 'advanced-classifieds-and-directory-pro' ); ?>
    	</span>
		<div id="acadp-timestamp-wrap" class="acadp-timestamp-wrap">
    		<label>
        		<select id="acadp-mm" name="acadp_mm">
        			<?php
						$months = array(
							__( "Jan", 'advanced-classifieds-and-directory-pro' ),
							__( "Feb", 'advanced-classifieds-and-directory-pro' ),
							__( "Mar", 'advanced-classifieds-and-directory-pro' ),
							__( "Apr", 'advanced-classifieds-and-directory-pro' ),
							__( "May", 'advanced-classifieds-and-directory-pro' ),
							__( "Jun", 'advanced-classifieds-and-directory-pro' ),
							__( "Jul", 'advanced-classifieds-and-directory-pro' ),
							__( "Aug", 'advanced-classifieds-and-directory-pro' ),
							__( "Sep", 'advanced-classifieds-and-directory-pro' ),
							__( "Oct", 'advanced-classifieds-and-directory-pro' ),
							__( "Nov", 'advanced-classifieds-and-directory-pro' ),
							__( "Dec", 'advanced-classifieds-and-directory-pro' )					
						);
				
						foreach( $months as $key => $month_name ) {
							$key = $key + 1;
					
							$selected = '';
							if( $key == (int) $expiry_date['month'] ) {
								$selected = ' selected="selected"';
							}
				 
							printf( '<option value="%02d"%s>%02d-%s</option>', $key, $selected, $key, $month_name );
						}			
					?>
            	</select>
        	</label>
        	<label>
        		<input type="text" id="acadp-jj" name="acadp_jj" value="<?php echo $expiry_date['day']; ?>" size="2" maxlength="2" />
        	</label>,
        	<label>
        		<input type="text" id="acadp-aa" name="acadp_aa" value="<?php echo $expiry_date['year']; ?>" size="4" maxlength="4" />
        	</label>@
        	<label>
        		<input type="text" id="acadp-hh" name="acadp_hh" value="<?php echo $expiry_date['hour']; ?>" size="2" maxlength="2" />
        	</label> :
        	<label>
        		<input type="text" id="acadp-mn" name="acadp_mn" value="<?php echo $expiry_date['min']; ?>" size="2" maxlength="2" />
        	</label>
    	</div>
	</div>
<?php endif; ?>
    
<div class="misc-pub-section misc-pub-acadp-never-expires">
    <label>
        <input type="checkbox" name="never_expires" value="1" <?php if( isset( $post_meta['never_expires'] ) ) checked( $post_meta['never_expires'][0], 1 ); ?>>
        <strong><?php _e( "Never Expires", 'advanced-classifieds-and-directory-pro' ); ?></strong>
    </label>
</div>

<?php if( $has_featured ) : ?>
	<div class="misc-pub-section misc-pub-acadp-featured">
    	<label>
        	<input type="checkbox" name="featured" value="1" <?php if( isset( $post_meta['featured'] ) ) checked( $post_meta['featured'][0], 1 ); ?>>
			<?php _e( "Mark as", 'advanced-classifieds-and-directory-pro' ); ?>
            <strong><?php _e( "Featured", 'advanced-classifieds-and-directory-pro' ); ?></strong>
       	</label>
	</div>
<?php endif; ?>

<input type="hidden" name="listing_status" value="<?php echo isset( $post_meta['listing_status'] ) ? $post_meta['listing_status'][0] : 'post_status'; ?>" />
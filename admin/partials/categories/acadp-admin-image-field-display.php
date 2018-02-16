<?php

/**
 * Display "Image Field" in the ACADP categories page.
 */
?>

<?php if( 'add' == $page ) : ?>
    <div class="form-field term-group">
        <label for="acadp-categories-image-id"><?php _e( 'Image', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <input type="hidden" id="acadp-categories-image-id" name="image" />
        <div id="acadp-categories-image-wrapper"></div>
        <p>
            <input type="button" class="button button-secondary" id="acadp-categories-upload-image" value="<?php _e( 'Add Image', 'advanced-classifieds-and-directory-pro' ); ?>" />
            <input type="button" class="button button-secondary" id="acadp-categories-remove-image" value="<?php _e( 'Remove Image', 'advanced-classifieds-and-directory-pro' ); ?>" />
        </p>
    </div>
<?php elseif( 'edit' == $page ) : ?>
	<tr class="form-field term-group-wrap">
    	<th scope="row">
        	<label for="acadp-categories-image-id"><?php _e( 'Image', 'advanced-classifieds-and-directory-pro' ); ?></label>
        </th>
        <td>
            <input type="hidden" id="acadp-categories-image-id" name="image" value="<?php echo $image_id; ?>" />
            <div id="acadp-categories-image-wrapper">
            	<?php if( $image_src ) : ?>
            		<img src="<?php echo $image_src; ?>" />
                <?php endif; ?>
            </div>
            <p>
            	<input type="button" class="button button-secondary" id="acadp-categories-upload-image" value="<?php _e( 'Add Image', 'advanced-classifieds-and-directory-pro' ); ?>" />
            	<input type="button" class="button button-secondary" id="acadp-categories-remove-image" value="<?php _e( 'Remove Image', 'advanced-classifieds-and-directory-pro' ); ?>" />
        	</p>
        </td>
    </tr>
<?php endif; ?>
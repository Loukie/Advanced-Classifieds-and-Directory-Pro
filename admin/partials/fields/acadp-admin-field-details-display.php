<?php

/**
 * Display the "Field Details" meta box.
 */
?>

<table class="acadp-input widefat" id="acadp-field-details">
  <tbody>
    <tr class="field-type">
      <td class="label">
        <label><?php _e( 'Field Type', 'advanced-classifieds-and-directory-pro' ); ?></label>
      </td>
      <td>
      	<select class="select" name="type">
      	<?php
			$types = acadp_get_custom_field_types();
			$selected_type = isset( $post_meta['type'] ) ? $post_meta['type'][0] : 'text';
			
			foreach( $types as $key => $label ) {
				printf( '<option value="%s"%s>%s</option>', $key, selected( $selected_type, $key, false ), $label );
			}
		?>
        </select>
      </td>
    </tr>
    <tr class="field-instructions">
      <td class="label">
        <label><?php _e( 'Field Instructions', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'Instructions for authors. Shown when submitting data', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
        <textarea class="textarea" name="instructions" rows="6"><?php if( isset( $post_meta['instructions'] ) ) echo esc_textarea( $post_meta['instructions'][0] ); ?></textarea>
      </td>
    </tr>
    <tr class="field-required">
      <td class="label">
        <label><?php _e( 'Required?', 'advanced-classifieds-and-directory-pro' ); ?></label>
      </td>
      <td>
      	<?php $selected_required = isset( $post_meta['required'] ) ? $post_meta['required'][0] : 0; ?>
        <ul class="acadp-radio-list radio horizontal">
          <li>
            <label><input type="radio" name="required" value="1" <?php echo checked( $selected_required, 1, false ); ?>><?php _e( 'Yes', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
          <li>
            <label><input type="radio" name="required" value="0" <?php echo checked( $selected_required, 0, false ); ?>><?php _e( 'No', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
        </ul>
      </td>
    </tr>
    <tr class="field-options field-option-select field-option-checkbox field-option-radio" style="display:none;">
      <td class="label">
        <label><?php _e( 'Choices', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description">
			<?php _e( 'Enter each choice on a new line.', 'advanced-classifieds-and-directory-pro' ); ?><br /><br />
			<?php _e( 'Red', 'advanced-classifieds-and-directory-pro' ); ?><br />
            <?php _e( 'Blue', 'advanced-classifieds-and-directory-pro' ); ?><br /><br />
            <?php _e( 'red : Red', 'advanced-classifieds-and-directory-pro' ); ?><br />
            <?php _e( 'blue : Blue', 'advanced-classifieds-and-directory-pro' ); ?>
        </p>
      </td>
      <td>
        <textarea class="textarea" name="choices" rows="8"><?php if( isset( $post_meta['choices'] ) ) echo $post_meta['choices'][0]; ?></textarea>
      </td>
    </tr>
    <tr class="field-options field-option-text field-option-select field-option-radio field-option-url">
      <td class="label">
        <label><?php _e( 'Default Value', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'Appears when creating a new post', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
        <div class="acadp-input-wrap">
          <input type="text" class="text" name="default_value" value="<?php if( isset( $post_meta['default_value'] ) ) echo esc_attr( $post_meta['default_value'][0] ); ?>" />
        </div>
      </td>
    </tr>
    <tr class="field-options field-option-textarea" style="display:none;">
      <td class="label">
        <label><?php _e( 'Default Value', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'Appears when creating a new post', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
        <textarea class="textarea" name="default_value_textarea" rows="6"><?php if( isset( $post_meta['default_value'] ) ) echo esc_textarea( $post_meta['default_value'][0] ); ?></textarea>
      </td>
    </tr>
    <tr class="field-options field-option-checkbox" style="display:none;">
      <td class="label">
        <label><?php _e( 'Default Value', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'Enter each default value on a new line', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
        <textarea class="textarea" name="default_value_checkbox" rows="8"><?php if( isset( $post_meta['default_value'] ) ) echo esc_textarea( $post_meta['default_value'][0] ); ?></textarea>
      </td>
    </tr>
    <tr class="field-options field-option-select">
      <td class="label">
        <label><?php _e( 'Allow Null?', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'If selected, the select list will begin with a null value titled "- Select an Option -"', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
      	<?php $selected_allow_null = isset( $post_meta['allow_null'] ) ? $post_meta['allow_null'][0] : 1; ?>
        <ul class="acadp-radio-list radio horizontal">
          <li>
            <label><input type="radio" name="allow_null" value="1" <?php echo checked( $selected_allow_null, 1, false ); ?>><?php _e( 'Yes', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
          <li>
            <label><input type="radio" name="allow_null" value="0" <?php echo checked( $selected_allow_null, 0, false ); ?>><?php _e( 'No', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
        </ul>
      </td>
    </tr>
    <tr class="field-options field-option-text field-option-textarea field-option-url">
      <td class="label">
        <label><?php _e( 'Placeholder Text', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'Appears within the input', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
        <div class="acadp-input-wrap">
          <input type="text" class="text" name="placeholder" value="<?php if( isset( $post_meta['placeholder'] ) ) echo esc_attr( $post_meta['placeholder'][0] ); ?>" />
        </div>
      </td>
    </tr>
    <tr class="field-options field-option-textarea" style="display:none;">
      <td class="label">
        <label><?php _e( 'Rows', 'advanced-classifieds-and-directory-pro' ); ?></label>
        <p class="description"><?php _e( 'Sets the textarea height', 'advanced-classifieds-and-directory-pro' ); ?></p>
      </td>
      <td>
        <div class="acadp-input-wrap">
          <input type="text" class="text" name="rows" placeholder="8" value="<?php if( isset( $post_meta['rows'] ) ) echo esc_attr( $post_meta['rows'][0] ); ?>" />
        </div>
      </td>
    </tr>
    <tr class="field-options field-option-url" style="display:none;">
      <td class="label">
        <label><?php _e( 'Open link in a new window?', 'advanced-classifieds-and-directory-pro' ); ?></label>
      </td>
      <td>
        <?php $selected_target = isset( $post_meta['target'] ) ? $post_meta['target'][0] : '_blank'; ?>
        <ul class="acadp-radio-list radio horizontal">
          <li>
            <label><input type="radio" name="target" value="_blank" <?php echo checked( $selected_target, '_blank', false ); ?>><?php _e( 'Yes', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
          <li>
            <label><input type="radio" name="target" value="_self" <?php echo checked( $selected_target, '_self', false ); ?>><?php _e( 'No', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
        </ul>
      </td>
    </tr>
    <tr class="field-options field-option-url" style="display:none;">
      <td class="label">
        <label><?php _e( 'Use rel="nofollow" when displaying the link?', 'advanced-classifieds-and-directory-pro' ); ?></label>
      </td>
      <td>
        <?php $selected_nofollow = isset( $post_meta['nofollow'] ) ? $post_meta['nofollow'][0] : 1; ?>
        <ul class="acadp-radio-list radio horizontal">
          <li>
            <label><input type="radio" name="nofollow" value="1" <?php echo checked( $selected_nofollow, 1, false ); ?>><?php _e( 'Yes', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
          <li>
            <label><input type="radio" name="nofollow" value="0" <?php echo checked( $selected_nofollow, 0, false ); ?>><?php _e( 'No', 'advanced-classifieds-and-directory-pro' ); ?></label>
          </li>
        </ul>
      </td>
    </tr>
  </tbody>
</table>
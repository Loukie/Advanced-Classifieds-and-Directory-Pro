<?php

/**
 * This template displays custom fields in the search form.
 */
?>

<?php if( $acadp_query->have_posts() ) : $i = 0; ?>	
    <?php while( $acadp_query->have_posts() ) : $acadp_query->the_post(); $field_meta = get_post_meta( $post->ID ); ?>
    
    	<?php if( $i % 3 == 0 ) : ?>
  			<div class="row acadp-no-margin">
        <?php endif; ?>
        
    	<div class="form-group col-md-4">
    		<label><?php the_title(); ?></label>

			<?php if( isset( $field_meta['instructions'] ) ) : ?>
        		<small class="help-block"><?php echo $field_meta['instructions'][0]; ?></small>
        	<?php endif; ?> 
            
            <?php
				$value = '';
				if( isset( $_GET['cf'][ $post->ID ] ) ) {
					$value = $_GET['cf'][ $post->ID ];
				}
						
				switch( $field_meta['type'][0] ) {
					case 'text' :	
						printf( '<input type="text" name="cf[%d]" class="form-control" placeholder="%s" value="%s"/>', $post->ID, esc_attr( $field_meta['placeholder'][0] ), esc_attr( $value ) );
						break;
					case 'textarea' :
						printf( '<textarea name="cf[%d]" class="form-control" rows="%d" placeholder="%s">%s</textarea>', $post->ID, (int) $field_meta['rows'][0], esc_attr( $field_meta['placeholder'][0] ), esc_textarea( $value ) );
						break;
					case 'select' :
						$choices = $field_meta['choices'][0];
						$choices = explode( "\n", $choices );
					
						printf( '<select name="cf[%d]" class="form-control">', $post->ID );
						if( ! empty( $field_meta['allow_null'][0] ) ) {
							printf( '<option value="">%s</option>', '- '.__( 'Select an Option', 'advanced-classifieds-and-directory-pro' ).' -' );
						}
						foreach( $choices as $choice ) {
							if( strpos( $choice, ':' ) !== false ) {
								$_choice = explode( ':', $choice );
								$_choice = array_map( 'trim', $_choice );
								
								$_value  = $_choice[0];
								$_label  = $_choice[1];
							} else {
								$_value  = trim( $choice );
								$_label  = $_value;
							}
					
							$_selected = '';
							if( trim( $value ) == $_value ) $_selected = ' selected="selected"';
				
							printf( '<option value="%s"%s>%s</option>', $_value, $_selected, $_label );
						} 
						echo '</select>';
						break;
					case 'checkbox' :
						$choices = $field_meta['choices'][0];
						$choices = explode( "\n", $choices );
					
						$values = array_map( 'trim', (array) $value );
					
						foreach( $choices as $choice ) {
							if( strpos( $choice, ':' ) !== false ) {
								$_choice = explode( ':', $choice );
								$_choice = array_map( 'trim', $_choice );
								
								$_value  = $_choice[0];
								$_label  = $_choice[1];
							} else {
								$_value  = trim( $choice );
								$_label  = $_value;
							}
						
							$_checked = '';
							if( in_array( $_value, $values ) ) $_checked = ' checked="checked"';
							
							printf( '<div class="checkbox"><label><input type="checkbox" name="cf[%d][]" value="%s"%s>%s</label></div>', $post->ID, $_value, $_checked, $_label );
						}
						break;
					case 'radio' :
						$choices = $field_meta['choices'][0];
						$choices = explode( "\n", $choices );
					
						foreach( $choices as $choice ) {
							if( strpos( $choice, ':' ) !== false ) {
								$_choice = explode( ':', $choice );
								$_choice = array_map( 'trim', $_choice );
								
								$_value  = $_choice[0];
								$_label  = $_choice[1];
							} else {
								$_value  = trim( $choice );
								$_label  = $_value;
							}
						
							$_checked = '';
							if( trim( $value ) == $_value ) $_checked = ' checked="checked"';
						
							printf( '<div class="radio"><label><input type="radio" name="cf[%d]" value="%s"%s>%s</label></div>', $post->ID, $_value, $_checked, $_label );
						}
						break;
					case 'url' :	
						printf( '<input type="text" name="cf[%d]" class="form-control" placeholder="%s" value="%s"/>', $post->ID, esc_attr( $field_meta['placeholder'][0] ), esc_url( $value ) );
						break;
				}
			?>
    	</div>
        
        <?php 
			$i++;
			if( $i % 3 == 0 || $i == $acadp_query->post_count ) : ?>
  				</div>
        <?php endif; ?> 
        
	<?php endwhile; ?>	
<?php endif; ?>
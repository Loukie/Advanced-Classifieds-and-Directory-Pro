<?php

/**
 * This template displays the public-facing aspects of the widget.
 */
?>

<div class="acadp acadp-widget-listing-contact">
	<?php if( ! empty( $general_settings['contact_form_require_login'] ) && ! is_user_logged_in() ) : ?> 
    	<p class="text-muted">
			<?php _e( 'Please, login to contact this listing owner.', 'advanced-classifieds-and-directory-pro' ); ?>
        </p>
   	<?php else : ?>
		<form id="acadp-contact-form" class="form-vertical" role="form">
        	<div class="form-group">
    			<label for="acadp-contact-name"><?php _e( 'Your Name', 'advanced-classifieds-and-directory-pro' ); ?><span class="acadp-star">*</span></label>
    			<input type="text" class="form-control" id="acadp-contact-name" placeholder="<?php _e( 'Name', 'advanced-classifieds-and-directory-pro' ); ?>" required />
  			</div>
      		
            <div class="form-group">
    			<label for="acadp-contact-email"><?php _e( 'Your E-mail Address', 'advanced-classifieds-and-directory-pro' ); ?><span class="acadp-star">*</span></label>
    			<input type="email" class="form-control" id="acadp-contact-email" placeholder="<?php _e( 'Email', 'advanced-classifieds-and-directory-pro' ); ?>" required />
  			</div>  						
            
            <div class="form-group">
    			<label for="acadp-contact-message"><?php _e( 'Your Message', 'advanced-classifieds-and-directory-pro' ); ?><span class="acadp-star">*</span></label>
    			<textarea class="form-control" id="acadp-contact-message" rows="3" placeholder="<?php _e( 'Message', 'advanced-classifieds-and-directory-pro' ); ?>..." required ></textarea>
  			</div>
            
            <div id="acadp-contact-g-recaptcha"></div>
            <p id="acadp-contact-message-display"></p>
      		
            <button type="submit" class="btn btn-primary"><?php _e( 'Submit', 'advanced-classifieds-and-directory-pro' ); ?></button>
     	</form> 
	<?php endif; ?>
</div>
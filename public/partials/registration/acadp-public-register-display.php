<?php

/**
 * This template displays the registration form.
 */
?>

<div class="acadp acadp-register-form">
	<!-- Show errors if there are any -->
	<?php if( count( $attributes['errors'] ) > 0 ) : ?>
    	<div class="alert alert-danger" role="alert">
			<?php foreach( $attributes['errors'] as $error ) : ?>
                <span class="acadp-error"><?php echo $error; ?></span>
            <?php endforeach; ?>
        </div>
	<?php endif; ?>

	<form id="acadp-register-form" class="form-horizontal" action="<?php echo wp_registration_url(); ?>" method="post" role="form">
    	<div class="form-group">
			<label for="acadp-username" class="col-sm-3 control-label"><?php _e( 'Username', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="text" name="username" id="acadp-username" class="form-control" required />
            	<span class="help-block"><?php _e( 'Usernames cannot be changed.', 'advanced-classifieds-and-directory-pro' ); ?></span>
            </div>
		</div>

		<div class="form-group">
			<label for="acadp-first-name" class="col-sm-3 control-label"><?php _e( 'First Name', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<input type="text" name="first_name" id="acadp-first-name" class="form-control" />
            </div>
		</div>

		<div class="form-group">
			<label for="acadp-last-name" class="col-sm-3 control-label"><?php _e( 'Last Name', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<input type="text" name="last_name" id="acadp-last-name" class="form-control" />
            </div>
		</div>
        
        <div class="form-group">
			<label for="acadp-email" class="col-sm-3 control-label"><?php _e( 'E-mail Address', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="text" name="email" id="acadp-email" class="form-control" required />
            </div>
		</div>
        
        <div class="form-group">
			<label for="acadp-pass1" class="col-sm-3 control-label"><?php _e( 'Password', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="password" name="pass1" id="acadp-pass1" class="form-control" autocomplete="off" required />
            </div>
		</div>
        
        <div class="form-group">
			<label for="acadp-pass2" class="col-sm-3 control-label"><?php _e( 'Confirm Password', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="password" name="pass2" id="acadp-pass2" class="form-control" autocomplete="off" data-match="#acadp-pass1" required />
            </div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            	<div id="acadp-registration-g-recaptcha"></div>
        		<div id="acadp-registration-g-recaptcha-message"></div>
        
        		<?php if( $attributes['redirect'] ) : ?>
            		<input type="hidden" name="redirect_to" value="<?php echo esc_url( $attributes['redirect'] ); ?>" />
                <?php endif; ?>
                
        		<input type="submit" name="submit" class="btn btn-primary" value="<?php _e( 'Register', 'advanced-classifieds-and-directory-pro' ); ?>" />
            </div>
        </div>
	</form>
</div>
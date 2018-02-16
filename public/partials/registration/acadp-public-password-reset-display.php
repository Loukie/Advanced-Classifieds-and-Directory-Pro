<?php

/**
 * This template displays the password reset form.
 */
?>

<div class="acadp acadp-password-reset-form">
	<!-- Show errors if there are any -->
	<?php if( count( $attributes['errors'] ) > 0 ) : ?>
    	<div class="alert alert-danger" role="alert">
			<?php foreach( $attributes['errors'] as $error ) : ?>
                <span class="acadp-error"><?php echo $error; ?></span>
            <?php endforeach; ?>
        </div>
	<?php endif; ?>
    
	<form name="resetpassform" id="acadp-password-reset-form" class="form-horizontal" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off" role="form">
		<input type="hidden" id="acadp-user-login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
		<input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />

		<?php if( count( $attributes['errors'] ) > 0 ) : ?>
        	<div class="alert alert-danger" role="alert">
				<?php foreach( $attributes['errors'] as $error ) : ?>
                    <span class="acadp-error"><?php echo $error; ?></span>
                <?php endforeach; ?>
            </div>
		<?php endif; ?>

		<div class="alert alert-info" role="alert">
        	<?php echo wp_get_password_hint(); ?>
        </div>
        
		<div class="form-group">
			<label for="acadp-pass1" class="col-sm-3 control-label"><?php _e( 'New password', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<input type="password" name="pass1" id="acadp-pass1" class="form-control" autocomplete="off" required />
           	</div>
		</div>
        
		<div class="form-group">
			<label for="acadp-pass2" class="col-sm-3 control-label"><?php _e( 'Repeat new password', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<input type="password" name="pass2" id="acadp-pass2" class="form-control" autocomplete="off" data-match="#acadp-pass1" required />
            </div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">
            	<?php if( $attributes['redirect'] ) : ?>
            		<input type="hidden" name="redirect_to" value="<?php echo esc_url( $attributes['redirect'] ); ?>" />
                <?php endif; ?>
                
				<input type="submit" name="submit" id="acadp-resetpass-button" class="btn btn-primary" value="<?php _e( 'Reset Password', 'advanced-classifieds-and-directory-pro' ); ?>" />
      		</div>
     	</div>
	</form>
</div>
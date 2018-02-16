<?php

/**
 * This template displays the login form.
 */
?>

<div class="acadp acadp-login-form">
	<!-- Show errors if there are any -->
	<?php if( count( $attributes['errors'] ) > 0 ) : ?>
    	<div class="alert alert-danger" role="alert">
			<?php foreach( $attributes['errors'] as $error ) : ?>
                <span class="acadp-error"><?php echo $error; ?></span>
            <?php endforeach; ?>
        </div>
	<?php endif; ?>

	<!-- Show logged out message if user just logged out -->
	<?php if( $attributes['logged_out'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php _e( 'You have signed out. Would you like to login again?', 'advanced-classifieds-and-directory-pro' ); ?>
		</div>
	<?php endif; ?>

	<?php if( $attributes['registered'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php
				printf(
					__( 'You have successfully registered to <strong>%s</strong>. We have emailed your account details to the email address you entered.', 'advanced-classifieds-and-directory-pro' ),
					get_bloginfo( 'name' )
				);
			?>
		</div>
	<?php endif; ?>

	<?php if( $attributes['lost_password_sent'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php _e( 'Check your email for a link to reset your password.', 'advanced-classifieds-and-directory-pro' ); ?>
		</div>
	<?php endif; ?>

	<?php if( $attributes['password_updated'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php _e( 'Your password has been changed. You can login now.', 'advanced-classifieds-and-directory-pro' ); ?>
		</div>
	<?php endif; ?>

	<form id="acadp-login-form" class="form-horizontal" action="<?php echo wp_login_url(); ?>" method="post" role="form">
        <div class="form-group">
            <label for="acadp-user-login" class="col-sm-3 control-label"><?php _e( 'Username or E-mail', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
            	<input type="text" name="log" id="acadp-user-login" class="form-control" required />
           	</div>
        </div>
        
        <div class="form-group">
            <label for="acadp-user-pass" class="col-sm-3 control-label"><?php _e( 'Password', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
            	<input type="password" name="pwd" id="acadp-user-pass" class="form-control" required />
            </div>
        </div>
        
        <div class="form-group">
    		<div class="col-sm-offset-3 col-sm-9">
        		<div class="checkbox">
    				<label>
            			<input type="checkbox" name="rememberme" id="acadp-rememberme" value="forever"><?php _e( 'Remember Me', 'advanced-classifieds-and-directory-pro' ); ?>
            		</label>
 				</div>
        	</div>
        </div>
        
        <div class="form-group">
    		<div class="col-sm-offset-3 col-sm-9">
            	<?php if( $attributes['redirect'] ) : ?>
            		<input type="hidden" name="redirect_to" value="<?php echo esc_url( $attributes['redirect'] ); ?>" />
                <?php endif; ?>
                
        		<input type="submit" class="btn btn-primary" value="<?php _e( 'Login', 'advanced-classifieds-and-directory-pro' ); ?>" />
           	</div>
       	</div>   
        
        <div class="form-group">
    		<div class="col-sm-offset-3 col-sm-9">
            	<p class="acadp-forgot-password">  
                    <a href="<?php echo $attributes['forgot_password_url']; ?>"><?php _e( 'Forgot your password?', 'advanced-classifieds-and-directory-pro' ); ?></a>
                </p>
                
                <?php if( get_option( 'users_can_register' ) ) : ?>
                    <p class="acadp-register-account">  
                        <a href="<?php echo $attributes['register_url']; ?>"><?php _e( 'Create an account', 'advanced-classifieds-and-directory-pro' ); ?></a>
                    </p>
                <?php endif; ?>
            </div>
       	</div>
    </form>
</div>
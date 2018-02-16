<?php

/**
 * This template displays the user account page.
 */
?>

<div class="acadp acadp-user-account">
	<!-- Show errors if there are any -->
	<?php if( count( $attributes['errors'] ) > 0 ) : ?>
    	<div class="alert alert-danger" role="alert">
			<?php foreach( $attributes['errors'] as $error ) : ?>
                <span class="acadp-error"><?php echo $error; ?></span>
            <?php endforeach; ?>
        </div>
	<?php endif; ?>
    
    <?php if( $attributes['account_updated'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php _e( 'Your account has been updated!', 'advanced-classifieds-and-directory-pro' ); ?>
		</div>
	<?php endif; ?>

	<form id="acadp-user-account" class="form-horizontal" action="<?php echo acadp_get_user_account_page_link(); ?>" method="post" role="form">
    	<div class="form-group">
			<label for="acadp-username" class="col-sm-3 control-label"><?php _e( 'Username', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<p class="form-control-static"><strong><?php echo $attributes['username']; ?></strong></p>
            </div>
		</div>

		<div class="form-group">
			<label for="acadp-first-name" class="col-sm-3 control-label"><?php _e( 'First Name', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<input type="text" name="first_name" id="acadp-first-name" value="<?php echo $attributes['first_name']; ?>" class="form-control" />
            </div>
		</div>

		<div class="form-group">
			<label for="acadp-last-name" class="col-sm-3 control-label"><?php _e( 'Last Name', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="col-sm-9">
				<input type="text" name="last_name" id="acadp-last-name" value="<?php echo $attributes['last_name']; ?>" class="form-control" />
            </div>
		</div>
        
        <div class="form-group">
			<label for="acadp-email" class="col-sm-3 control-label"><?php _e( 'E-mail Address', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="text" name="email" id="acadp-email" class="form-control" value="<?php echo $attributes['email']; ?>" required />
            </div>
		</div>
        
        <div class="form-group">
    		<div class="col-sm-offset-3 col-sm-9">
        		<div class="checkbox">
    				<label>
            			<input type="checkbox" name="change_password" id="acadp-change-password" value="1"><?php _e( 'Change Password', 'advanced-classifieds-and-directory-pro' ); ?>
            		</label>
 				</div>
        	</div>
        </div>
        
        <div class="form-group acadp-password-fields" style="display: none;">
			<label for="acadp-pass1" class="col-sm-3 control-label"><?php _e( 'New Password', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="password" name="pass1" id="acadp-pass1" class="form-control" autocomplete="off" required />
            </div>
		</div>
        
        <div class="form-group acadp-password-fields" style="display: none">
			<label for="acadp-pass2" class="col-sm-3 control-label"><?php _e( 'Confirm Password', 'advanced-classifieds-and-directory-pro' ); ?> <strong>*</strong></label>
            <div class="col-sm-9">
				<input type="password" name="pass2" id="acadp-pass2" class="form-control" autocomplete="off" data-match="#acadp-pass1" required />
            </div>
		</div>

		<?php wp_nonce_field( 'acadp_update_user_account', 'acadp_user_account_nonce' ); ?>
         
		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-9">   
        		<input type="submit" name="submit" class="btn btn-primary" value="<?php _e( 'Update Account', 'advanced-classifieds-and-directory-pro' ); ?>" />
            </div>
        </div>
	</form>
</div>
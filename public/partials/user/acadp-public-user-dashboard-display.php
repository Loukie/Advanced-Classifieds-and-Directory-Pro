<?php

/**
 * This template displays the ACADP user dashboard.
 */
?>

<div class="acadp acadp-user acadp-user-dashboard">
	<div class="media acadp-margin-bottom">
  		<div class="pull-left">
      		<?php echo get_avatar( $userid ); ?>
  		</div>
  		<div class="media-body">
    		<h4 class="media-heading"><?php echo $user->display_name; ?></h4>
    		<?php echo $user->description; ?>
            <?php the_acadp_user_menu(); ?>
  		</div>
	</div>
    
    <?php if( acadp_current_user_can('edit_acadp_listings') ) : ?>
    	<div class="row">
    		<div class="col-md-6">
        		<div class="panel panel-default">
  					<div class="panel-body text-center">
    					<p class="lead"><?php _e( "Total Listings", 'advanced-classifieds-and-directory-pro' ); ?></p>
                    	<span class="text-muted"><?php echo acadp_get_user_total_listings(); ?></span>
  					</div>
				</div>
        	</div>
        	<div class="col-md-6">
        		<div class="panel panel-default">
  					<div class="panel-body text-center">
    					<p class="lead"><?php _e( "Active Listings", 'advanced-classifieds-and-directory-pro' ); ?></p>
                    	<span class="text-muted"><?php echo acadp_get_user_total_active_listings(); ?></span>
  					</div>
				</div>
        	</div>
    	</div>
    <?php endif; ?>
</div>
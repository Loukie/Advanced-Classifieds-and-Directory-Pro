<?php

/**
 * This template displays the ACADP user listings dashboard.
 */
?>

<div class="acadp acadp-user acadp-manage-listings acadp-list-view">
	<?php if( isset( $_GET['submitted'] ) && 1 == $_GET['submitted'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php _e( 'Thank you for submitting your listing.', 'advanced-classifieds-and-directory-pro' ); ?>
		</div>
	<?php endif; ?>
    
	<?php if( isset( $_GET['renew'] ) && 'success' == $_GET['renew'] ) : ?>
		<div class="alert alert-info" role="alert">
			<?php _e( 'Thank you for renewing your listing.', 'advanced-classifieds-and-directory-pro' ); ?>
		</div>
	<?php endif; ?>
    
	<!-- header here -->
    <div class="row acadp-no-margin">
    	<div class="pull-left">
        	<form action="<?php echo acadp_get_manage_listings_page_link( true ); ?>" class="form-inline" role="form">
            	<?php if( ! get_option('permalink_structure') ) : ?>
        			<input type="hidden" name="page_id" value="<?php if( $page_settings['manage_listings'] > 0 ) echo $page_settings['manage_listings']; ?>">
        		<?php endif; ?>
        
            	<div class="form-group">
                	<?php $search_query = isset( $_REQUEST['u'] ) ? esc_attr( $_REQUEST['u'] ) : ''; ?>
    				<input type="text" name="u" class="form-control" placeholder="<?php _e( "Search by title", 'advanced-classifieds-and-directory-pro' ); ?>" value="<?php echo $search_query; ?>" />
  				</div>
                <button type="submit" class="btn btn-primary"><?php _e( "Search", 'advanced-classifieds-and-directory-pro' ); ?></button>
                <a href="<?php echo acadp_get_manage_listings_page_link(); ?>" class="btn btn-default"><?php _e( "Reset", 'advanced-classifieds-and-directory-pro' ); ?></a>
            </form>
        </div>
        <div class="pull-right">
        	<a href="<?php echo acadp_get_listing_form_page_link(); ?>" class="btn btn-success"><?php _e( 'Add New Listing', 'advanced-classifieds-and-directory-pro' ); ?></a>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <div class="acadp-divider"></div>
    
    <!-- the loop -->
	<?php while( $acadp_query->have_posts() ) : $acadp_query->the_post(); $post_meta = get_post_meta( $post->ID ); ?>
    	<div class="row acadp-no-margin">
        	<?php if( $can_show_images ) : ?>
        		<div class="col-md-2">   
                	<a href="<?php the_permalink(); ?>"><?php the_acadp_listing_thumbnail( $post_meta ); ?></a>      	
            	</div>
            <?php endif; ?>
            
            <div class="<?php echo $span_middle; ?>">            
            	<div class="acadp-listings-title-block">
            		<h3 class="acadp-no-margin"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php the_acadp_listing_labels( $post_meta ); ?>
                </div>

				<p>
                	<small class="text-muted">
						<?php printf( __( 'Posted %s ago', 'advanced-classifieds-and-directory-pro' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
                    </small>
                </p>

				<?php
					$info = array();					
		
					if( $category = wp_get_object_terms( $post->ID, 'acadp_categories' ) ) {
						$info[] = '<span class="glyphicon glyphicon-briefcase"></span>&nbsp;&nbsp;<a href="'.acadp_get_category_page_link( $category[0] ).'">'.$category[0]->name.'</a>';
					}
					
					if( $has_location && $location = wp_get_object_terms( $post->ID, 'acadp_locations' ) ) {
						$info[] = '<span class="glyphicon glyphicon-map-marker"></span>&nbsp;<a href="'.acadp_get_location_page_link( $location[0] ).'">'.$location[0]->name.'</a>';
					}
					
					if( ! empty( $post_meta['views'][0] ) ) {
						$info[] = sprintf( __( "%d views", 'advanced-classifieds-and-directory-pro' ), $post_meta['views'][0] );
					}
					
					if( ! empty( $post_meta['price'][0] ) ) {
						$price = acadp_format_amount( $post_meta['price'][0] );						
						$info[] = acadp_currency_filter( $price );
					}

					echo '<p class=""><small>'.implode( ' / ', $info ).'</small></p>';
				?>
                
                <p>
                	<strong><?php _e( 'Status', 'advanced-classifieds-and-directory-pro' ); ?></strong>: 
                    <?php echo acadp_get_listing_status_i18n( $post->post_status ); ?>
                </p>
                
                <?php if( ! empty( $post_meta['never_expires'] ) ) : ?>
                	<p>
                		<strong><?php _e( 'Expires on', 'advanced-classifieds-and-directory-pro' ); ?></strong>: 
                    	<?php _e( 'Never Expires', 'advanced-classifieds-and-directory-pro' ); ?>
                	</p>                
                <?php elseif( ! empty( $post_meta['expiry_date'] ) ) : ?>
                	<p>
                		<strong><?php _e( 'Expires on', 'advanced-classifieds-and-directory-pro' ); ?></strong>: 
                    	<?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $post_meta['expiry_date'][0] ) ); ?>
                	</p>
                <?php endif; ?>
             </div>
            
            <div class="col-md-3 text-right">
            	<?php
					$can_edit = 1;
					
                	if( ! empty( $post_meta['listing_status'] ) && in_array( $post_meta['listing_status'][0], array( 'renewal', 'expired' ) ) ) {
						
						if( $can_renew ) {
							printf( '<p><a href="%s" class="btn btn-primary btn-sm btn-block">%s</a></p>', acadp_get_listing_renewal_page_link( $post->ID ), __( 'Renew', 'advanced-classifieds-and-directory-pro' ) );
						}
								
					} else {
							
						if( 'pending' == $post->post_status ) {
							$can_edit = 0;
							
							if(	'0000-00-00 00:00:00' == $post->post_date_gmt && $has_checkout_page = apply_filters( 'acadp_has_checkout_page', 0, $post->ID ) ) {
								printf( '<p><a href="%s" class="btn btn-primary btn-sm btn-block">%s</a></p>', acadp_get_checkout_page_link( $post->ID ), __( 'Retry Payment', 'advanced-classifieds-and-directory-pro' ) );
							}
						}
						
						if( $can_promote && empty( $post_meta['featured'][0] ) && 'publish' == $post->post_status ) {
							printf( '<p><a href="%s" class="btn btn-primary btn-sm btn-block">%s</a></p>', acadp_get_listing_promote_page_link( $post->ID ), __( 'Promote', 'advanced-classifieds-and-directory-pro' ) );
						}
	
					}
             	?>
                
                <div class="btn-group btn-group-justified">
                	<?php if( $can_edit ) : ?>
                        <a href="<?php echo acadp_get_listing_edit_page_link( $post->ID ); ?>" class="btn btn-default btn-sm">
                            <?php _e( 'Edit', 'advanced-classifieds-and-directory-pro' ); ?>
                        </a>
                    <?php endif; ?>
                       
               		<a href="<?php echo acadp_get_listing_delete_page_link( $post->ID ); ?>" class="btn btn-danger btn-sm">
						<?php _e( 'Delete', 'advanced-classifieds-and-directory-pro' ); ?>
                	</a>
                </div>
            </div>
    	</div>
        
        <div class="acadp-divider"></div>
    <?php endwhile; ?>
    <!-- end of the loop -->
    
    <!-- pagination here -->
    <?php the_acadp_pagination( $acadp_query->max_num_pages, "", $paged ); ?>
</div>
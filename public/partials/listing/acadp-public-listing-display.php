<?php

/**
 * This template displays the listing detail page content.
 */
?>

<div class="acadp acadp-listing">

	<div class="row">    	
        <!-- Main content -->
        <div class="<?php echo $has_sidebar ? 'col-md-8' : 'col-md-12'; ?>">  
        	<!-- Header -->      
            <div class="pull-left acadp-post-title">        	
                <h1 class="acadp-no-margin"><?php echo $post->post_title; ?></h1>
                <?php the_acadp_listing_labels( $post_meta ); ?>
                <?php				
                    $usermeta = array();
                    
                    if( $can_show_date ) {
                        $usermeta[] = sprintf( __( 'Posted %s ago', 'advanced-classifieds-and-directory-pro' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
                    }
                                
                    if( $can_show_user ) {			
                        $usermeta[] = '<a href="'.acadp_get_user_page_link( $post->post_author ).'">'.get_the_author().'</a>';
                    }
                    
                    $meta = array();
                    
                    if( $can_show_category ) {
                        $meta[] = sprintf( '<span class="glyphicon glyphicon-briefcase"></span>&nbsp;<a href="%s">%s</a>', acadp_get_category_page_link( $category ), $category->name );
                    }
    
                    if( count( $usermeta ) ) {
                        $meta[] = implode( ' '.__( 'by', 'advanced-classifieds-and-directory-pro' ).' ', $usermeta );
                    }
                    
                    if( $can_show_views ) {
                        $meta[] = sprintf( __( "%d views", 'advanced-classifieds-and-directory-pro' ), $post_meta['views'][0] );
                    }
                    
                    if( count( $meta ) ) {
                        echo '<p><small class="text-muted">'.implode( ' / ', $meta ).'</small></p>';
                    }
					
					if( $can_show_category_desc ) {
						echo '<p><small class="text-muted">'.$category->description.'</small></p>';
					}
                ?>
            </div>
            
            <!-- Price -->
            <?php if( $can_show_price ) : ?>
                <div class="pull-right text-right acadp-price-block">
                    <?php
                        $price = acadp_format_amount( $post_meta['price'][0] );						
                        echo '<p class="lead acadp-no-margin">'.acadp_currency_filter( $price ).'</p>';
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="clearfix"></div>
            
            <!-- Image(s) -->
            <?php if( $can_show_images ) : $images = unserialize( $post_meta['images'][0] ); ?>
				<?php if( 1 == count( $images ) ) : $image_attributes = wp_get_attachment_image_src( $images[0], 'large' ); ?>
                    <p><img src="<?php echo $image_attributes[0]; ?>" /></p>
                <?php else : ?>
                    <div id="acadp-slider-wrapper">
                       
                        <!-- Slider for -->
                        <div class="acadp-slider-for">
                            <?php foreach( $images as $index => $image ) : $image_attributes = wp_get_attachment_image_src( $images[ $index ], 'large' ); ?>
                            	<div class="acadp-slider-item">
                                    <div class="acadp-responsive-container">
                                        <img src="<?php echo $image_attributes[0]; ?>" class="acadp-responsive-item" />
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Slider nav -->
                        <div class="acadp-slider-nav">
                            <?php foreach( $images as $index => $image ) : $image_attributes = wp_get_attachment_image_src( $images[ $index ], 'thumbnail' ); ?>
                                <div class="acadp-slider-item">
                                    <div class="acadp-slider-item-inner">
                                        <img src="<?php echo $image_attributes[0]; ?>" />
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
        
                    </div>
                <?php endif; ?> 
            <?php endif; ?>
            
            <!-- Description -->
            <?php echo $description; ?>
            
            <!-- Custom fields -->
            <?php if( count( $fields ) ) : ?>
                <ul class="list-group acadp-margin-bottom">
                    <?php foreach( $fields as $field )  : ?>
                
                        <?php if( ! empty( $post_meta[ $field->ID ][0] ) ) : ?>
                            <li class="list-group-item acadp-no-margin-left acadp-field-<?php echo $field->type; ?>">
                                <span class="text-primary"><?php echo $field->post_title; ?></span> :
                                <span class="text-muted">
                                    <?php
                                        $value = $post_meta[ $field->ID ][0];
                                        
                                        if( 'url' == $field->type && ! filter_var( $value, FILTER_VALIDATE_URL ) === FALSE ) {
                                            $nofollow = ! empty( $field->nofollow ) ? ' rel="nofollow"' : '';
                                            printf( '<a href="%1$s" target="%2$s"%3$s>%1$s</a>', $value, $field->target, $nofollow );
                                        } else if( 'checkbox' == $field->type ) {
                                            echo str_replace( "\n", ', ', $value );
                                        } else {
                                            echo $value;
                                        }
                                    ?>
                                </span>
                            </li>
                        <?php endif; ?>
                    
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
			
			<!-- Custom Contact Form -->
			<h2>Contact Us</h2>
				<!-- change id and title to your contact form 7 id and title -->
				<?php echo do_shortcode( '[contact-form-7 id="52003" title="listings form"]' ); ?>
				
            <!-- Footer -->
            <?php if( $can_show_user || $can_add_favourites || $can_report_abuse ) : ?>
                <ol class="breadcrumb">
                    <?php if( $can_show_user ) : ?>
                        <li class="acadp-no-margin">			
                            <a href="<?php echo acadp_get_user_page_link( $post->post_author ); ?>"><?php _e( 'Check all listings by this user', 'advanced-classifieds-and-directory-pro' ); ?></a>
                        </li>
                    <?php endif; ?>
                        
                    <?php if( $can_add_favourites ) : ?>
                        <li id="acadp-favourites" class="acadp-no-margin"><?php the_acadp_favourites_link(); ?></li>
                    <?php endif; ?>
                        
                    <?php if( $can_report_abuse ) : ?>
                        <li class="acadp-no-margin">
                            <?php if( is_user_logged_in() ) { ?>
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#acadp-report-abuse-modal"><?php _e( 'Report abuse', 'advanced-classifieds-and-directory-pro' ); ?></a>
                                    
                                <!-- Modal (report abuse form) -->
                                <div class="modal fade" id="acadp-report-abuse-modal" tabindex="-1" role="dialog" aria-labelledby="acadp-report-abuse-modal-label">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form id="acadp-report-abuse-form" class="form-vertical" role="form">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                                    <h3 class="modal-title" id="acadp-report-abuse-modal-label"><?php _e( 'Report Abuse', 'advanced-classifieds-and-directory-pro' ); ?></h3>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="acadp-report-abuse-message"><?php _e( 'Your Complaint', 'advanced-classifieds-and-directory-pro' ); ?><span class="acadp-star">*</span></label>
                                                        <textarea class="form-control" id="acadp-report-abuse-message" rows="3" placeholder="<?php _e( 'Message', 'advanced-classifieds-and-directory-pro' ); ?>..." required></textarea>
                                                    </div>
                                                    <div id="acadp-report-abuse-g-recaptcha"></div>
                                                    <div id="acadp-report-abuse-message-display"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e( 'Close', 'advanced-classifieds-and-directory-pro' ); ?></button>
                                                    <button type="submit" class="btn btn-primary"><?php _e( 'Submit', 'advanced-classifieds-and-directory-pro' ); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <a href="javascript:void(0)" class="acadp-require-login"><?php _e( 'Report abuse', 'advanced-classifieds-and-directory-pro' ); ?></a>
                            <?php } ?>
                        </li>
                    <?php endif; ?>
                </ol>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <?php if( $has_sidebar ) : ?>
            <div class="col-md-4">
            	<!-- Video -->
                <?php if( $can_show_video ) : ?>
                	<div class="acadp-margin-bottom">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="<?php echo $video_url; ?>" allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?> 
                
                <!-- Map & Address -->
                <?php if( $has_location ) : ?>
                	<fieldset>
                    	<legend><?php _e( 'Contact details', 'advanced-classifieds-and-directory-pro' ); ?></legend>
						<?php if( $can_show_map ) : ?>
                            <div class="embed-responsive embed-responsive-16by9 acadp-margin-bottom">
                                <div class="acadp-map embed-responsive-item">
                                    <div class="marker" data-latitude="<?php echo $post_meta['latitude'][0]; ?>" data-longitude="<?php echo $post_meta['longitude'][0]; ?>"></div> 
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Address -->
                        <?php the_acadp_address( $post_meta, $location->term_id ); ?>
                    </fieldset>
                <?php endif; ?>
                
                <!-- Contact form -->
                <?php if( $can_show_contact_form ) : ?>
                	<fieldset>
                    	<legend><?php _e( 'Contact this listing owner', 'advanced-classifieds-and-directory-pro' ); ?></legend>
						<?php if( ! empty( $general_settings['contact_form_require_login'] ) && ! is_user_logged_in() ) { ?> 
                            <p class="text-muted">
                                <?php _e( 'Please, login to contact this listing owner.', 'advanced-classifieds-and-directory-pro' ); ?>
                            </p>
                        <?php } else { ?>
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
                        <?php } ?>
                    </fieldset>
                <?php endif; ?>
            </div>
        <?php endif; ?>                
    </div>

	<input type="hidden" id="acadp-post-id" value="<?php echo $post->ID; ?>" />
</div>

<?php the_acadp_social_sharing_buttons(); ?>
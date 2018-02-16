<?php

/**
 * HTML elements
 *
 * A helper file for outputting HTML elements, such as gateway drop downs
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/includes
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
  * Display user menu links.
  *
  * @since    1.0.0
  */
function the_acadp_user_menu() {

	$page_settings = get_option( 'acadp_page_settings' );
	$general_settings = get_option( 'acadp_general_settings' );
	$registration_settings = get_option( 'acadp_registration_settings' );
	
	$links = array();					
	
	if( acadp_current_user_can('edit_acadp_listings') && $page_settings['listing_form'] > 0 ) {
		$links[] = '<a href="'.get_permalink( $page_settings['listing_form'] ).'">'.__( 'Add New Listing', 'advanced-classifieds-and-directory-pro' ).'</a>';
	}
	
	if( acadp_current_user_can('edit_acadp_listings') && $page_settings['manage_listings'] > 0 ) {
		$links[] = '<a href="'.get_permalink( $page_settings['manage_listings'] ).'">'.get_the_title( $page_settings['manage_listings'] ).'</a>';
	}
	
	if( ! empty( $general_settings['has_favourites'] ) && $page_settings['favourite_listings'] > 0 ) {
		$links[] = '<a href="'.get_permalink( $page_settings['favourite_listings'] ).'">'.get_the_title( $page_settings['favourite_listings'] ).'</a>';
	}
	
	if( acadp_current_user_can('edit_acadp_listings') && $page_settings['payment_history'] > 0 ) {
		$links[] = '<a href="'.get_permalink( $page_settings['payment_history'] ).'">'.get_the_title( $page_settings['payment_history'] ).'</a>';
	}
	
	if( ! empty( $registration_settings['engine'] ) && 'acadp' == $registration_settings['engine'] && $page_settings['user_account'] > 0 ) {
		$links[] = '<a href="'.get_permalink( $page_settings['user_account'] ).'">'.__( 'User Account', 'advanced-classifieds-and-directory-pro' ).'</a>';
	}
	
	echo '<p class="acadp-no-margin">'.implode( ' | ', $links ).'</p>';

}

/**
 * Adds "Terms of Agreement" content to the listing form.
 *
 * @since    1.0.0
 */
function the_acadp_terms_of_agreement() {

	$tos_settings = get_option( 'acadp_terms_of_agreement' );
	
	if( ! empty( $tos_settings['show_agree_to_terms'] ) && ! empty( $tos_settings['agree_text'] ) ) {
	
		$agree_text  = trim( $tos_settings['agree_text'] );
		$agree_type  = filter_var( $agree_text, FILTER_VALIDATE_URL ) ? 'url' : 'txt';
		$agree_label = ! empty( $tos_settings['agree_label'] ) ? trim( $tos_settings['agree_label'] ) : __( 'I agree to the terms and conditions', 'advanced-classifieds-and-directory-pro' );
		
		$label = ( 'url' == $agree_type ) ? sprintf( '<a href="%s" target="_blank">%s</a>', $agree_text, $agree_label ) : $agree_label;
		$text  = ( 'txt' == $agree_type ) ? nl2br( $agree_text ) : '';
		
		printf( '<div class="form-group"><div class="checkbox"><label><input type="checkbox" name="terms_of_agreement" required />%s</label></div>%s</div>', $label, $text );
		
	}
	
}

/**
 * Display Social Sharing Buttons.
 *
 * @since    1.0.0
 */
function the_acadp_social_sharing_buttons() {

	global $post;
	
	$page_settings = get_option( 'acadp_page_settings' );
	$socialshare_settings = get_option( 'acadp_socialshare_settings' );
		
	$page = 'none';
	
	if( 'acadp_listings' == $post->post_type ) {
		$page = 'listing';
	}
	
	if( $post->ID == $page_settings['locations'] ) {
		$page = 'locations';
	}
	
	if( $post->ID == $page_settings['categories'] ) {
		$page = 'categories';
	}

	if( in_array( $post->ID, array( $page_settings['listings'], $page_settings['location'], $page_settings['category'], $page_settings['search'] ) ) ) {
		$page = 'listings';
	}
	
	if( isset( $socialshare_settings['pages'] ) && in_array( $page, $socialshare_settings['pages'] ) ) {
	
		// Get current page URL 
		$url = acadp_get_current_url();
 
		// Get current page title
		$title = get_the_title();
			
		if( $post->ID == $page_settings['location'] ) {
			
			if( $slug = get_query_var( 'acadp_location' ) ) {
				$term = get_term_by( 'slug', $slug, 'acadp_locations' );
				$title = $term->name;			
			}
				
		}
		
		if( $post->ID == $page_settings['category'] ) {
			
			if( $slug = get_query_var( 'acadp_category' ) ) {
				$term = get_term_by( 'slug', $slug, 'acadp_categories' );
				$title = $term->name;			
			}
				
		}
			
		if( $post->ID == $page_settings['user_listings'] ) {
			
			if( $slug = get_query_var( 'acadp_user' ) ) {
				$user = get_user_by( 'slug', $slug );
				$title = $user->display_name;		
			}
				
		}
			
		$title = str_replace( ' ', '%20', $title );
	
		// Get Post Thumbnail
		$thumbnail = '';
		
		if( 'listing' == $page ) {
			$images = get_post_meta( $post->ID, 'images', true );
			
			if( ! empty( $images ) ) { 
				$image_attributes = wp_get_attachment_image_src( $images[0], 'full' );
				$thumbnail = is_array( $image_attributes ) ? $image_attributes[0] : '';
			}
		} else {
			$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
			$thumbnail = is_array( $image_attributes ) ? $image_attributes[0] : '';
		}
 
		// Construct sharing buttons
		$buttons = array();
	
		if( isset( $socialshare_settings['services'] ) ) {
		
			if( in_array( 'facebook', $socialshare_settings['services'] ) ) {
				$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$url;
				$buttons[] = '<a class="acadp-social-link acadp-social-facebook" href="'.$facebookURL.'" target="_blank">'.__( 'Facebook', 'advanced-classifieds-and-directory-pro' ).'</a>';
			}
	
			if( in_array( 'twitter', $socialshare_settings['services'] ) ) {
				$twitterURL = 'https://twitter.com/intent/tweet?text='.$title.'&amp;url='.$url;
				$buttons[] = '<a class="acadp-social-link acadp-social-twitter" href="'. $twitterURL .'" target="_blank">'.__( 'Twitter', 'advanced-classifieds-and-directory-pro' ).'</a>';
			}

			if( in_array( 'gplus', $socialshare_settings['services'] ) ) {
				$googleURL = 'https://plus.google.com/share?url='.$url;
				$buttons[] = '<a class="acadp-social-link acadp-social-googleplus" href="'.$googleURL.'" target="_blank">'.__( 'Google+', 'advanced-classifieds-and-directory-pro' ).'</a>';
			}
		
			if( in_array( 'linkedin', $socialshare_settings['services'] ) ) {
				$linkedinURL = 'https://www.linkedin.com/shareArticle?url='.$url.'&amp;title='.$title;
				$buttons[] = '<a class="acadp-social-link acadp-social-linkedin" href="'.$linkedinURL.'" target="_blank">'.__( 'Linkedin', 'advanced-classifieds-and-directory-pro' ).'</a>';
			}
	
			if( in_array( 'pinterest', $socialshare_settings['services'] ) ) {
				$pinterestURL = 'https://pinterest.com/pin/create/button/?url='.$url.'&amp;media='.$thumbnail.'&amp;description='.$title;
				$buttons[] = '<a class="acadp-social-link acadp-social-pinterest" href="'.$pinterestURL.'" target="_blank">'.__( 'Pin It', 'advanced-classifieds-and-directory-pro' ).'</a>';
			}
			
		}
	
		if( count( $buttons ) ) {
			echo '<div class="acadp-social">'.implode( ' ', $buttons ).'</div>';
		}
	
	}

}

/**
 * Display the listing entry classes.
 *
 * @since    1.5.5
 *
 * @param    array    $post_meta    Post Meta.
 * @param    string   $class        CSS Class Names.
 */
function the_acadp_listing_entry_class( $post_meta, $class = '' ) {
	
	$class .= ' acadp-entry';
	
	if( isset( $post_meta['featured'] ) && 1 == (int) $post_meta['featured'][0] ) {
    	$class .= ' acadp-entry-featured';
	}
	
	printf( 'class="%s"', trim( $class ) );
	
}

/**
 * Display the listing thumbnail.
 *
 * @since    1.0.0
 *
 * @param    array    $post_meta    Post Meta.
 */
function the_acadp_listing_thumbnail( $post_meta ) {

	$image = '';
	
	if( isset( $post_meta['images'] ) ) {
	
		$images = unserialize( $post_meta['images'][0] );
		$image_attributes = wp_get_attachment_image_src( $images[0], 'medium' );
		$image = $image_attributes[0];
		
	}
	
	if( ! $image ) $image = ACADP_PLUGIN_URL . 'public/images/no-image.png';
	
	echo '<img src="'.$image.'" />';

}

/**
 * Display the listing labels.
 *
 * @since    1.0.0
 *
 * @param    array    $post_meta    Post Meta.
 */
function the_acadp_listing_labels( $post_meta ) {

	global $post;
	
	$general_settings = get_option( 'acadp_general_settings' );
	$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
	
	if( ! empty( $general_settings['show_new_tag'] ) ) {
		
		$each_hours = 60 * 60 * 24; // seconds in a day
    	$s_date1 = strtotime( current_time( 'mysql' ) ); // seconds for date 1
    	$s_date2 = strtotime( $post->post_date ); // seconds for date 2
    	$s_date_diff = abs( $s_date1 - $s_date2 ); // different of the two dates in seconds
    	$days = round( $s_date_diff / $each_hours ); // divided the different with second in a day
	
		if( $days <= (int) $general_settings['new_listing_threshold'] ) {
			echo '<span class="label label-primary">'.$general_settings['new_listing_label'].'</span>&nbsp;';
		}
		
	}
	
	if( ! empty( $general_settings['show_popular_tag'] ) ) {
	
		if(	isset( $post_meta['views'] ) && (int) $post_meta['views'][0] >= (int) $general_settings['popular_listing_threshold'] ) {
    		echo '<span class="label label-success">'.$general_settings['popular_listing_label'].'</span>&nbsp;';
		}
		
	}
	
	if( ! empty( $featured_listing_settings['show_featured_tag'] ) ) {
	
		if( isset( $post_meta['featured'] ) && 1 == (int) $post_meta['featured'][0] ) {
    		echo '<span class="label label-warning">'.$featured_listing_settings['label'].'</span>&nbsp;';
//			echo '<span class="label label-warning">Recommended</span>&nbsp;';
		}
		
	}

}

/**
  * Display the listing address.
  *
  * @since    1.0.0
  *
  * @param    array    $post_meta    Post Meta.
  * @param    int      $term_id      Custom Taxonomy term ID.
  */
function the_acadp_address( $post_meta, $term_id ) {
	
	// Get all the location term ids
	$locations = array( $term_id );
	$ancestors = get_ancestors( $term_id, 'acadp_locations' );
	
	$locations = array_merge( $locations, $ancestors );
	
	// Build address vars
	echo '<p class="acadp-address">';
	
	if( ! empty( $post_meta['address'][0] ) ) {
		echo '<span class="acadp-street-address">'.$post_meta['address'][0].'</span>';
	}
	
	$pieces = array();
	
	$country = end( $locations );
	
	if( count( $locations ) > 1 ) {
		array_pop( $locations );

		foreach( $locations as $region ) {
			$term = get_term( $region, 'acadp_locations' );
			$pieces[] = '<span class="acadp-locality"><a href="'.acadp_get_location_page_link( $term ).'">'.$term->name.'</a></span>';
		}
	}	

	$term = get_term( $country, 'acadp_locations' );
	$pieces[] = '<span class="acadp-country-name"><a href="'.acadp_get_location_page_link( $term ).'">'.$term->name.'</a></span>';
	
	if( ! empty( $post_meta['zipcode'][0] ) ) {
		$pieces[] = $post_meta['zipcode'][0];
	}
	
	echo implode( '<span class="acadp-delimiter">,</span>', $pieces );
	
	if( ! empty( $post_meta['phone'][0] ) ) {
		echo '<span class="acadp-phone"><span class="glyphicon glyphicon glyphicon-earphone"></span>&nbsp;'.$post_meta['phone'][0].'</span>';
	}
		
	if( ! empty( $post_meta['email'][0] ) ) {
		$email_settings = get_option( 'acadp_email_settings' );
		$show_email_address_publicly = ! empty( $email_settings['show_email_address_publicly'] ) ? 1 : 0;
		
		if( $show_email_address_publicly || is_user_logged_in() ) {
			echo '<span class="acadp-email"><span class="glyphicon glyphicon-envelope"></span>&nbsp;<a href="mailto:'.$post_meta['email'][0].'">'.$post_meta['email'][0].'</a></span>';
		} else {
			echo '<span class="acadp-email"><span class="glyphicon glyphicon-envelope"></span>&nbsp;*****</span>';
		}
	}
	
	if( ! empty( $post_meta['website'][0] ) ) {
		echo '<span class="acadp-website"><span class="glyphicon glyphicon-globe"></span>&nbsp;<a href="'.$post_meta['website'][0].'" target="_blank">'.$post_meta['website'][0].'</a></span>';
	}
	
	echo '</p>';
	
}

/**
 * Get activated payment gateways.
 *
 * @since    1.0.0
 */
function the_acadp_payment_gateways() {

	$gateways = acadp_get_payment_gateways();	
	$settings = get_option( 'acadp_gateway_settings' );	
	
	$list = array();
	
	if( isset( $settings['gateways'] ) ) {
	
		foreach( $gateways as $key => $label ) {
			
			if( in_array( $key, $settings['gateways'] ) ) {			
				$gateway_settings = get_option( 'acadp_gateway_'.$key.'_settings' );
				$label = ! empty( $gateway_settings['label'] ) ? $gateway_settings['label'] : $label;
					
				$html  = '<li class="list-group-item acadp-no-margin-left">';
				$html .= sprintf( '<div class="radio acadp-no-margin"><label><input type="radio" name="payment_gateway" value="%s"%s>%s</label></div>', $key, ( $key == end( $settings['gateways'] ) ? ' checked' : '' ), $label );
				
				if( ! empty( $gateway_settings['description'] ) ) {
					$html .= '<p class="text-muted acadp-no-margin">'.$gateway_settings['description'].'</p>';
				}
				
				$html .= '</li>';
				
				$list[] = $html;
			}
			
		}
		
	}
	
	if( count( $list ) ) {
		echo '<ul class="list-group">'.implode( "\n", $list ).'</ul>';
	}
	
}

/**
 * Get instructions to do offline payment.
 *
 * @since    1.0.0
 */
function the_acadp_offline_payment_instructions() {

	$settings = get_option('acadp_gateway_offline_settings');
	echo '<p>' . nl2br( $settings['instructions'] ) . '</p>';
	
}

/**
 * Retrieve paginated link for listing pages.
 *
 * @since    1.5.4
 *
 * @param    int      $numpages     The total amount of pages.
 * @param    int      $pagerange    How many numbers to either side of current page.
 * @param    int      $paged        The current page number.
 */
function the_acadp_pagination( $numpages = '', $pagerange = '', $paged = '' ) {
	
	if( empty( $pagerange ) ) {
    	$pagerange = 2;
  	}

  	/**
   	 * This first part of our function is a fallback
     * for custom pagination inside a regular loop that
     * uses the global $paged and global $wp_query variables.
     * 
     * It's good because we can now override default pagination
     * in our theme, and use this function in default quries
     * and custom queries.
     */
  	if( empty( $paged ) ) {
    	$paged = acadp_get_page_number();
  	}
	
  	if( $numpages == '' ) {
    	global $wp_query;
    	
		$numpages = $wp_query->max_num_pages;
    	if( ! $numpages ) {
        	$numpages = 1;
    	}
  	}

  	/** 
   	 * We construct the pagination arguments to enter into our paginate_links
   	 * function. 
   	 */
	$arr_params = array( 'order', 'sort', 'view', 'lang' );
	 
	$base = acadp_remove_query_arg( $arr_params, get_pagenum_link( 1 ) );
	
	if( ! get_option('permalink_structure') || isset( $_GET['q'] ) ) {
		$prefix = strpos( $base, '?' ) ? '&' : '?';
    	$format = $prefix.'paged=%#%';
    } else {
		$prefix = ( '/' == substr( $base, -1 ) ) ? '' : '/';
    	$format = $prefix.'page/%#%';
    } 
	
  	$pagination_args = array(
    	'base'         => $base . '%_%',
    	'format'       => $format,
    	'total'        => $numpages,
    	'current'      => $paged,
    	'show_all'     => false,
    	'end_size'     => 1,
    	'mid_size'     => $pagerange,
    	'prev_next'    => true,
    	'prev_text'    => __( '&laquo;' ),
    	'next_text'    => __( '&raquo;' ),
    	'type'         => 'array',
    	'add_args'     => false,
    	'add_fragment' => ''
  	);

  	$paginate_links = paginate_links( $pagination_args );

  	if( $paginate_links ) {
		echo "<div class='row text-center acadp-no-margin'>";
		
		echo "<div class='pull-left text-muted'>";
		printf( __( "Page %d of %d", 'advanced-classifieds-and-directory-pro' ), $paged, $numpages );
		echo "</div>";
		
		echo "<ul class='pagination acadp-no-margin'>"; 		   	
		foreach ( $paginate_links as $key => $page_link ) {
		
			if( strpos( $page_link, 'current' ) !== false ) {
			 	echo '<li class="active">'.$page_link.'</li>';
			} else {
				echo '<li>'.$page_link.'</li>';
			}
			
		}
   		echo "</ul>";
		
		echo "</div>";
  	}

}

/**
 * Outputs the ACADP categories/locations dropdown.
 *
 * @since    1.5.5
 *
 * @param    array     $args    Array of options to control the field output.
 * @param    bool      $echo    Whether to echo or just return the string.
 * @return   string             HTML attribute or empty string.
 */
function acadp_dropdown_terms( $args = array(), $echo = true ) {

	// Vars
	$args = array_merge( array(
		'show_option_none'  => '-- '.__( 'Select a category', 'advanced-classifieds-and-directory-pro' ).' --',
		'option_none_value' => '',
		'taxonomy'          => 'acadp_categories',
		'name' 			    => 'acadp_category',
		'class'             => 'form-control',
		'required'          => false,
		'base_term'         => 0,
		'parent'            => 0,
		'orderby'           => 'name',
		'order'             => 'ASC',
		'selected'          => 0
	), $args );
	
	if( ! empty( $args['selected'] ) ) {
		$ancestors = get_ancestors( $args['selected'], $args['taxonomy'] );
		$ancestors = array_merge( array_reverse( $ancestors ), array( $args['selected'] ) );
	} else {
		$ancestors = array();
	}

	// Build data
	$html = '';
		
	if( isset( $args['walker'] ) ) {

		$selected = count( $ancestors ) >= 2 ? (int) $ancestors[1] : 0;
		
		$html .= '<div class="acadp-terms">';	
		$html .= sprintf( '<input type="hidden" name="%s" class="acadp-term-hidden" value="%d" />', $args['name'], $selected );
		
		$term_args = array(
			'show_option_none'  => $args['show_option_none'],
			'option_none_value' => $args['option_none_value'],			
			'taxonomy'          => $args['taxonomy'],			
			'child_of'          => $args['parent'],
			'orderby'           => $args['orderby'],
			'order'             => $args['order'],
			'selected'          => $selected,
			'hierarchical'      => true,
			'depth'             => 2,
			'show_count'        => false,
			'hide_empty'        => false,
			'walker'            => $args['walker'],
			'echo'              => 0
		);
		
		unset( $args['walker'] );
	
		$select  = wp_dropdown_categories( $term_args );
		$required = $args['required'] ? ' required' : '';
		$replace = sprintf( '<select class="%s" data-taxonomy="%s" data-parent="%d"%s>', $args['class'], $args['taxonomy'], $args['parent'], $required );
				
		$html .= preg_replace( '#<select([^>]*)>#', $replace, $select );
		
		if( $selected > 0 ) { 
			$args['parent'] = $selected;
			$html .= acadp_dropdown_terms( $args, false );
		}
		
		$html .= '</div>'; 
	
	} else { 

		$has_children = 0;
		$child_of     = 0;
	
		$term_args = array(			
			'parent'       => $args['parent'], 
			'orderby'      => 'name',   
			'order'        => 'ASC',  
			'hide_empty'   => false,  
			'hierarchical' => false  
		);		
		$terms = get_terms( $args['taxonomy'], $term_args );
 
		if( ! empty( $terms ) && ! is_wp_error( $terms ) ) { 
		
			if( $args['parent'] == $args['base_term'] ) {
				$required = $args['required'] ? ' required' : '';
				 
				$html .= '<div class="acadp-terms">';	 
				$html .= sprintf( '<input type="hidden" name="%s" class="acadp-term-hidden" value="%d" />', $args['name'], $args['selected'] ); 
				$html .= sprintf( '<select class="%s" data-taxonomy="%s" data-parent="%d"%s>', $args['class'], $args['taxonomy'], $args['parent'], $required ); 
				$html .= sprintf( '<option value="%s">%s</option>', $args['option_none_value'],  $args['show_option_none'] ); 
			} else {
				$html .= sprintf( '<div class="acadp-child-terms acadp-child-terms-%d">', $args['parent'] );	 
				$html .= sprintf( '<select class="%s" data-taxonomy="%s" data-parent="%d">', $args['class'], $args['taxonomy'], $args['parent'] ); 
				$html .= sprintf( '<option value="%d">%s</option>', $args['parent'], '---' );
			} 
		
			foreach( $terms as $term ) { 
				$selected = '';
				if( in_array( $term->term_id, $ancestors ) ) { 
					$has_children = 1;
					$child_of = $term->term_id; 
					$selected = ' selected'; 
				} else if( $term->term_id == $args['selected'] ) { 
					$selected = ' selected';
				}
				$html .= sprintf( '<option value="%d"%s>%s</option>', $term->term_id, $selected, $term->name ); 
			}
			
			$html .= '</select>';	
			if( $has_children ) {
				$args['parent'] = $child_of;
				$html .= acadp_dropdown_terms( $args, false );
			}
			$html .= '</div>';  
			
		} else {
		
			if( $args['parent'] == $args['base_term'] ) {
				$required = $args['required'] ? ' required' : '';
				
				$html .= '<div class="acadp-terms">';	
				$html .= sprintf( '<input type="hidden" name="%s" class="acadp-term-hidden" value="%d" />', $args['name'], $args['selected'] );
				$html .= sprintf( '<select class="%s" data-taxonomy="%s" data-parent="%d"%s>', $args['class'], $args['taxonomy'], $args['parent'], $required ); 
				$html .= sprintf( '<option value="%s">%s</option>', $args['option_none_value'],  $args['show_option_none'] ); 
				$html .= '</select>';
				$html .= '</div>';
			}
			
		}

	}
	
	// Echo or Return
	if( $echo ) {
		echo $html;
		return '';
	} else {
		return $html;
	}

}

<?php

/**
 * This file holds the functions those generate page permalinks.
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
 * Generate a permalink for a category page.
 *
 * @since    1.0.0
 *
 * @param    object    $term    The term object.
 * @return   string             Term link.
 */
function acadp_get_category_page_link( $term ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['category'] > 0 ) {
		$link = get_permalink( $page_settings['category'] );
	
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $term->slug );
  		} else {
    		$link = add_query_arg( 'acadp_category', $term->slug, $link );
  		}
	}
  
	return $link;

}

/**
 * Generate a permalink for a location page.
 *
 * @since    1.0.0
 *
 * @param    object    $term    The term object.
 * @return   string             Term link.
 */
function acadp_get_location_page_link( $term ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['location'] > 0 ) {
		$link = get_permalink( $page_settings['location'] );
	
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $term->slug );
  		} else {
    		$link = add_query_arg( 'acadp_location', $term->slug, $link );
  		}
	}
  
	return $link;

}

/**
 * Generate a permalink for listings archive page.
 *
 * @since    1.5.4
 *
 * @return   string    Listings Archive Page link.
 */
function acadp_get_listings_page_link() {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['listings'] > 0 ) {
		$link = get_permalink( $page_settings['listings'] );
	}
  
	return $link;

}

/**
 * Generate a permalink for an user listings page.
 *
 * @since    1.0.0
 *
 * @param    int       $user_id    User ID.
 * @return   string                User Listings page URL.
 */
function acadp_get_user_page_link( $user_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['user_listings'] > 0 ) {
		$link = get_permalink( $page_settings['user_listings'] );	
		$user_slug = get_the_author_meta( 'user_nicename', $user_id );
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . $user_slug );
  		} else {
    		$link = add_query_arg( 'acadp_user', $user_slug, $link );
  		}
	}
  
	return $link;

}

/**
  * Generate a permalink for search results page.
  *
  * @since    1.0.0
  *
  * @return   string    Favourites page URL.
  */
function acadp_get_search_action_page_link() {

	$link = home_url();
	
	if( get_option('permalink_structure') ) {
	
		$page_settings = get_option( 'acadp_page_settings' );

		if( $page_settings['search'] > 0 ) {
			$link = get_permalink( $page_settings['search'] );	
		}
	
	}
  
	return $link;
	
}

/**
 * Generate a permalink for user login page.
 *
 * @since    1.5.8
 *
 * @return   string    Login page URL.
 */
function acadp_get_user_login_page_link() {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['login_form'] > 0 ) {
		$link = get_permalink( $page_settings['login_form'] );
	}
	
	return $link;
	
}

/**
 * Generate a permalink for user account page.
 *
 * @since    1.5.6
 *
 * @return   string    User account page URL.
 */
function acadp_get_user_account_page_link() {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['user_account'] > 0 ) {
		$link = get_permalink( $page_settings['user_account'] );
	}
	
	return $link;
	
}

/**
 * Generate a permalink for manage listings page.
 *
 * @since    1.0.0
 *
 * @param    bool      $is_form_action    True if the URL is for a
 										  form action, false if not.
 * @return   string                       Manage listings page URL.
 */
function acadp_get_manage_listings_page_link( $is_form_action = false ) {

	$link = $is_form_action ? home_url() : '';
	
	if( false == $is_form_action || get_option('permalink_structure') ) {
	
		$page_settings = get_option( 'acadp_page_settings' );

		if( $page_settings['manage_listings'] > 0 ) {
			$link = get_permalink( $page_settings['manage_listings'] );
		}
	
	}
	
	return $link;
	
}

/**
 * Generate a permalink for listing form page.
 *
 * @since    1.0.0
 *
 * @return   string    Listing form page URL.
 */
function acadp_get_listing_form_page_link() {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['listing_form'] > 0 ) {
		$link = get_permalink( $page_settings['listing_form'] );
	}
	
	return $link;
	
}

/**
 * Generate a permalink for listings edit page.
 *
 * @since    1.0.0
 *
 * @param    int       $listing_id    Listing ID.
 * @return   string                   Listing edit page URL.
 */
function acadp_get_listing_edit_page_link( $listing_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['listing_form'] > 0 ) {
		$link = get_permalink( $page_settings['listing_form'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . 'edit/' . $listing_id );
  		} else {
    		$link = add_query_arg( array( 'acadp_action' => 'edit', 'acadp_listing' => $listing_id ), $link );
  		}
	}
  
	return $link;

}

/**
 * Generate a permalink to delete a listing.
 *
 * @since    1.0.0
 *
 * @param    int       $listing_id    Listing ID.
 * @return   string                   URL to delete listing.
 */
function acadp_get_listing_delete_page_link( $listing_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['listing_form'] > 0 ) {
		$link = get_permalink( $page_settings['listing_form'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . 'delete/' . $listing_id );
  		} else {
    		$link = add_query_arg( array( 'acadp_action' => 'delete', 'acadp_listing' => $listing_id ), $link );
  		}
	}
  
	return $link;

}

/**
  * Generate a permalink for favourites page.
  *
  * @since    1.0.0
  *
  * @return   string    Favourites page URL.
  */
function acadp_get_favourites_page_link() {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['favourite_listings'] > 0 ) {
		$link = get_permalink( $page_settings['favourite_listings'] );	
	}
  
	return $link;
	
}

/**
  * Display the favourites link.
  *
  * @since    1.0.0
  *
  * @param    int      $post_id    Post ID.
  */
function the_acadp_favourites_link( $post_id = 0 ) {

	if( is_user_logged_in() ) {
	
		if( $post_id == 0 ) {
			global $post;
			$post_id = $post->ID;
		}
	
		$favourites = (array) get_user_meta( get_current_user_id(), 'acadp_favourites', true );
	
		if( in_array( $post_id, $favourites ) ) {
			echo '<a href="javascript:void(0)" class="acadp-favourites" data-post_id="'.$post_id.'">'.__( 'Remove from favourites', 'advanced-classifieds-and-directory-pro' ).'</a>';
		} else {
			echo '<a href="javascript:void(0)" class="acadp-favourites" data-post_id="'.$post_id.'">'.__( 'Add to favourites', 'advanced-classifieds-and-directory-pro' ).'</a>';
		}
	
	} else {
	
		echo '<a href="javascript:void(0)" class="acadp-require-login">'.__( 'Add to favourites', 'advanced-classifieds-and-directory-pro' ).'</a>';
		
	}
	
}

/**
 * Generate a permalink to remove from favourites.
 *
 * @since    1.0.0
 *
 * @param    int       $listing_id    Listing ID.
 * @return   string                   URL to remove from favourites.
 */
function acadp_get_remove_favourites_page_link( $listing_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['favourite_listings'] > 0 ) {
		$link = get_permalink( $page_settings['favourite_listings'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . 'remove-favourites/' . $listing_id );
  		} else {
    		$link = add_query_arg( array( 'acadp_action' => 'remove-favourites', 'acadp_listing' => $listing_id ), $link );
  		}
	}
  
	return $link;

}

/**
 * Generate a permalink for checkout page.
 *
 * @since    1.0.0
 *
 * @param    int       $listing_id    Listing ID.
 * @return   string                   Payment checkout page URL.
 */
function acadp_get_checkout_page_link( $listing_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['checkout'] > 0 ) {
		$link = get_permalink( $page_settings['checkout'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
   			$link = user_trailingslashit( trailingslashit( $link ) . 'submission/' . $listing_id );
		} else {
			$link = add_query_arg( array( 'acadp_action' => 'submission', 'acadp_listing' => $listing_id ), $link );
		}
	}
  
	return $link;

}

/**
 * Generate a permalink for listings promote page.
 *
 * @since    1.0.0
 *
 * @param    int       $listing_id    Listing ID.
 * @return   string                   Listing promote page URL.
 */
function acadp_get_listing_promote_page_link( $listing_id ) {
	
	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['checkout'] > 0 ) {
		$link = get_permalink( $page_settings['checkout'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link ) . 'promote/' . $listing_id );
  		} else {
    		$link = add_query_arg( array( 'acadp_action' => 'promote', 'acadp_listing' => $listing_id ), $link );
  		}
	}
  
	return $link;

}

/**
 * Generate a permalink for Payment receipt page.
 *
 * @since    1.0.0
 *
 * @param    int       $order_id    Order ID.
 * @return   string                 Payment receipt page URL.
 */
function acadp_get_payment_receipt_page_link( $order_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';
	
	if( $page_settings['payment_receipt'] > 0 ) {
		$link = get_permalink( $page_settings['payment_receipt'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
   			$link = user_trailingslashit( trailingslashit( $link ) . 'order/' . $order_id );
		} else {
   			$link = add_query_arg( array( 'acadp_action' => 'order', 'acadp_order' => $order_id ), $link );
		}
	}
  
	return $link;

}
	
/**
 * Generate a permalink for Payment failure page.
 *
 * @since    1.0.0
 *
 * @param    int       $order_id    Order ID.
 * @return   string    Payment failure page URL.
 */
function acadp_get_failure_page_link( $order_id = 0 ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['payment_failure'] > 0 ) {
		$link = get_permalink( $page_settings['payment_failure'] );
		
		if( $order_id > 0 ) {
			if( '' != get_option( 'permalink_structure' ) ) {
   				$link = user_trailingslashit( trailingslashit( $link ) . 'order/' . $order_id );
			} else {
   				$link = add_query_arg( array( 'acadp_action' => 'order', 'acadp_order' => $order_id ), $link );
			}
		}
	}
  
	return $link;

}

/**
 * Generate a permalink for listings renewal page.
 *
 * @since    1.0.0
 *
 * @param    int       $listing_id    Listing ID.
 * @return   string                   Listing renewal page URL.
 */
function acadp_get_listing_renewal_page_link( $listing_id ) {

	$page_settings = get_option( 'acadp_page_settings' );
	
	$link = '/';

	if( $page_settings['listing_form'] > 0 ) {
		$link = get_permalink( $page_settings['listing_form'] );	
		
		if( '' != get_option( 'permalink_structure' ) ) {
    		$link = user_trailingslashit( trailingslashit( $link )  . 'renew/' . $listing_id );
  		} else {
    		$link = add_query_arg( array( 'acadp_action' => 'renew', 'acadp_listing' => $listing_id ), $link );
  		}
	}
  
	return $link;

}
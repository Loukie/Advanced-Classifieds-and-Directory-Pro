<?php

/**
 * This file holds the general helper functions.
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
 * Insert required custom pages and return their IDs as array.
 * 
 * @since    1.5.6
 * 
 * @return   array    Array of created page IDs.
 */
function acadp_insert_custom_pages() {

	// Vars
	$page_settings = get_option( 'acadp_page_settings', array() );

	$page_definitions = array(
		'listings' => array( 
			'title'   => __( 'Listings', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_listings]'
		),	
		'locations' => array( 
			'title'   => __( 'Listing Locations', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_locations]'
		),
		'location' => array( 
			'title'   => __( 'Listing Location', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_location]' 
		),
		'categories' => array( 
			'title'   => __( 'Listing Categories', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_categories]' 
		),
		'category' => array( 
			'title'   => __( 'Listing Category', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_category]' 
		),
		'search' => array( 
			'title'   => __( 'Search Listings', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_search]' 
		),
		'user_listings' => array( 
			'title'   => __( 'User Listings', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_user_listings]' 
		),
		'user_dashboard' => array( 
			'title'   => __( 'User Dashboard', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_user_dashboard]' 
		),
		'listing_form' => array( 
			'title'   => __( 'Listing Form', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_listing_form]' 
		),
		'manage_listings' => array( 
			'title'   => __( 'Manage Listings', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_manage_listings]' 
		),
		'favourite_listings' => array( 
			'title'   => __( 'Favourite Listings', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_favourite_listings]' 
		),
		'checkout' => array( 
			'title'   => __( 'Checkout', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_checkout]'
		),
		'payment_receipt' => array( 
			'title'   => __( 'Payment Receipt', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_payment_receipt]' 
		),
		'payment_failure' => array( 
			'title'   => __( 'Transaction Failed', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_payment_errors]'.__( 'Your transaction failed, please try again or contact site support.', 'advanced-classifieds-and-directory-pro' ).'[/acadp_payment_errors]' 
		),
		'payment_history' => array( 
			'title'   => __( 'Payment History', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_payment_history]' 
		),
		'login_form' => array( 
			'title'   => __( 'Login', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_login]' 
		),		
		'register_form' => array(
			'title'   => __( 'Register', 'advanced-classifieds-and-directory-pro' ),
			'content' => '[acadp_register]'
		),
		'user_account' => array( 
			'title'   => __( 'Account', 'advanced-classifieds-and-directory-pro' ), 
			'content' => '[acadp_user_account]' 
		),
		'forgot_password' => array(
			'title'   => __( 'Forgot Password', 'advanced-classifieds-and-directory-pro' ),
			'content' => '[acadp_forgot_password]'
		),
		'password_reset' => array(
			'title'   => __( 'Password Reset', 'advanced-classifieds-and-directory-pro' ),
			'content' => '[acadp_password_reset]'
		)
	);
	
	// ...
	$pages = array();
	
	foreach( $page_definitions as $slug => $page ) {

		$id = 0;
		
		if( array_key_exists( $slug, $page_settings ) ) {
			$id = (int) $page_settings[ $slug ];
		}

		if( ! $id ) {
			$id = wp_insert_post(
				array(
					'post_title'     => $page['title'],
					'post_content'   => $page['content'],
					'post_status'    => 'publish',
					'post_author'    => 1,
					'post_type'      => 'page',
					'comment_status' => 'closed'
				)
			);
		}				
			
		$pages[ $slug ] = $id;
			
	}

	return $pages;

}

/** 
 * Get current address bar URL.
 *
 * @since    1.0.0
 *
 * @return   string    Current Page URL.
 */
function acadp_get_current_url() {

    $current_url = ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) ? "https://" : "http://";
    $current_url .= $_SERVER["SERVER_NAME"];
    if( $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ) {
        $current_url .= ":".$_SERVER["SERVER_PORT"];
    }
    $current_url .= $_SERVER["REQUEST_URI"];
	
    return $current_url;
	
}

/*
 * Enable or disable the plugin's custom registration feature.
 *
 * @since    1.5.6
 *
 * @return   bool    Return true if enabled, false if not.
 */
function acadp_registration_enabled() {

	$registration_settings = get_option( 'acadp_registration_settings', array() );
	
	if( ! empty( $registration_settings['engine'] ) && 'acadp' == $registration_settings['engine'] ) {
		return true;
	}
	
	return false;

}

/*
 * Provides a simple login form.
 *
 * @since    1.0.0
 *
 * @return   string    Login form.
 */
function acadp_login_form() {

	$registration_settings = get_option( 'acadp_registration_settings', array() );
	
	if( ! empty( $registration_settings['engine'] ) && 'acadp' == $registration_settings['engine'] ) {
	
		$redirect = get_permalink();
		$form = do_shortcode( "[acadp_login redirect=$redirect]" );
	
	} else {
	
		// Login Form
		$custom_login = $registration_settings['custom_login'];
		
		if( empty( $custom_login ) ) {
			// Fallback to default login form
			$form = wp_login_form();
		} else {
			if( ! filter_var( $custom_login, FILTER_VALIDATE_URL ) === FALSE ) {
				// If URL redirect here
				echo '<script type="text/javascript">window.location.href="'.$custom_login.'";</script>';
				exit(); 
			} else {
				// If shortcode found
				$form = do_shortcode( $custom_login );
			}
		}
		
		// Forgot Password
		$lostpassword_url = empty( $registration_settings['custom_forgot_password'] ) ? wp_lostpassword_url( get_permalink() ) : $registration_settings['custom_forgot_password'];
		$form .= sprintf( '<p><a href="%s">%s</a></p>', $lostpassword_url, __( 'Forgot your password?', 'advanced-classifieds-and-directory-pro' ) );
		
		// Registration		
		if( get_option( 'users_can_register' ) ) {
			$registration_url = empty( $registration_settings['custom_register'] ) ? wp_registration_url() : $registration_settings['custom_register'];
			$form .= sprintf( '<p><a href="%s">%s</a></p>', $registration_url, __( 'Create an account', 'advanced-classifieds-and-directory-pro' ) );
		}
		
	}
	
	return $form;
	
}

/*
 * Whether the current user has a specific capability.
 *
 * @since    1.0.0
 *
 * @param    string    $capability    Capability name.
 * @param    int       $post_id       Optional. ID of the specific object to check against if
 									  `$capability` is a "meta" cap.
 * @return   bool                     True if the current user has the capability, false if not.
 */
function acadp_current_user_can( $capability, $post_id = 0 ) {

	$user_id = get_current_user_id();
	
	// If editing, deleting, or reading a listing, get the post and post type object.
	if( 'edit_acadp_listing' == $capability || 'delete_acadp_listing' == $capability || 'read_acadp_listing' == $capability ) {
		$post = get_post( $post_id );
		$post_type = get_post_type_object( $post->post_type );

		// If editing a listing, assign the required capability.
		if( 'edit_acadp_listing' == $capability ) {
			if( $user_id == $post->post_author ) {
				$capability = 'edit_acadp_listings';
			} else {
				$capability = 'edit_others_acadp_listings';
			}
		}
		
		// If deleting a listing, assign the required capability.
		else if( 'delete_acadp_listing' == $capability ) {
			if( $user_id == $post->post_author ) {
				$capability = 'delete_acadp_listings';
			} else {
				$capability = 'delete_others_acadp_listings';
			}
		}
		
		// If reading a private listing, assign the required capability.
		else if( 'read_listing' == $capability ) {
			if( 'private' != $post->post_status ) {
				$capability = 'read';
			} else if( $user_id == $post->post_author ) {
				$capability = 'read';
			} else {
				$capability = 'read_private_acadp_listings';
			}
		}
		
	}
		
	return current_user_can( $capability );
	
}

/*
 * Inserts a new key/value after the key in the array.
 *
 * @since    1.0.0
 *
 * @param    string    $key          The key to insert after.
 * @param    array     $array        An array to insert in to.
 * @param    array     $new_array    An array to insert.
 * @return                           The new array if the key exists, FALSE otherwise.
 */
function acadp_array_insert_after( $key, $array, $new_array ) {

	if( array_key_exists( $key, $array ) ) {
    	$new = array();
    	foreach( $array as $k => $value ) {
      		$new[ $k ] = $value;
      		if( $k === $key ) {
				foreach( $new_array as $new_key => $new_value ) {
        			$new[ $new_key ] = $new_value;
				}
      		}
    	}
    	return $new;
  	}
		
  	return $array;
  
}

/**
 * Calculate listing expiry date.
 *
 * @since    1.0.0
 *
 * @param    int      $post_id       Post ID.
 * @param    string   $start_date    Date from which the expiry date must be caluclated.
 * @return   string   $date          Expiry date.
 */
function acadp_listing_expiry_date( $post_id, $start_date = NULL ) {

	// Get number of days to add
	$general_settings = get_option( 'acadp_general_settings' );
	$days = apply_filters( 'acadp_listing_duration', $general_settings['listing_duration'], $post_id );
	
	if( $days <= 0 ) {		
		update_post_meta( $post_id, 'never_expires', 1 );
		$days = 999;
	} else {
		delete_post_meta( $post_id, 'never_expires' );
	}

	if( $start_date == NULL ) {
		// Current time
		$start_date = current_time( 'mysql' );
	}
	
	// Calculate new date
	$date = new DateTime( $start_date );
	$date->add( new DateInterval( "P{$days}D" ) );
	
	// return
	return $date->format( 'Y-m-d H:i:s' );
	
}

/**
 * Parse MySQL date format.
 *
 * @since    1.0.0
 *
 * @param    string    $date    MySQL date string.
 * @return   array     $date    Array of date values.
 */
function acadp_parse_mysql_date_format( $date ) {

	$date = preg_split( '([^0-9])', $date );
	
	return array(
		'year'  => $date[0],
		'month' => $date[1],
		'day'   => $date[2],
		'hour'  => $date[3],
		'min'   => $date[4],
		'sec'   => $date[5]
	);	
				
}

/**
 * Convert to MySQL date format (Y-m-d H:i:s).
 *
 * @since    1.0.0
 *
 * @param    array    $date    Array of date values.
 * @return   string   $date    Formatted date string.
 */
function acadp_mysql_date_format( $date ) {

	$defaults = array(
		'year'  => 0,
		'month' => 0,
		'day'   => 0,
		'hour'  => 0,
		'min'   => 0,
		'sec'   => 0
	);	
	$date = array_merge( $defaults, $date );

	$year = (int) $date['year'];
	$year = str_pad( $year, 4, '0', STR_PAD_RIGHT );
								
	$month = (int) $date['month'];
	$month = max( 1, min( 12, $month ) );
							
	$day = (int) $date['day'];
	$day = max( 1, min( 31, $day ) );
				
	$hour = (int) $date['hour'];
	$hour = max( 1, min( 24, $hour ) );
				
	$min = (int) $date['min'];
	$min = max( 0, min( 59, $min ) );
	
	$sec = (int) $date['sec'];
	$sec = max( 0, min( 59, $sec ) );
	
	return sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $min, $sec );
				
}

/**
 * Get payment statuses.
 *
 * @since    1.0.0
 *
 * @return   array    $statuses    A list of available payment status.
 */
function acadp_get_payment_statuses() {

	$statuses = array(
		'created'   => __( "Created", 'advanced-classifieds-and-directory-pro' ),
		'pending'   => __( "Pending", 'advanced-classifieds-and-directory-pro' ),
		'completed' => __( "Completed", 'advanced-classifieds-and-directory-pro' ),
		'failed'    => __( "Failed", 'advanced-classifieds-and-directory-pro' ),
		'cancelled' => __( "Cancelled", 'advanced-classifieds-and-directory-pro' ),
		'refunded'  => __( "Refunded", 'advanced-classifieds-and-directory-pro' )
	);
			
	return apply_filters( 'acadp_payment_statuses', $statuses );
	
}

/**
 * Retrieve the payment status in localized format.
 *
 * @since    1.5.4
 *
 * @param    string    $status    Payment status.
 * @return   string    $status    Localized payment status.
 */
function acadp_get_payment_status_i18n( $status ) {

	$statuses = acadp_get_payment_statuses();			
	return $statuses[ $status ];
	
}

/**
 * Get bulk actions.
 *
 * @since    1.0.0
 *
 * @return   array    $actions    A list of payment history page bulk actions.
 */
function acadp_get_payment_bulk_actions() {

	$actions = array(
		'set_to_created'   => __( "Set to Created", 'advanced-classifieds-and-directory-pro' ),
		'set_to_pending'   => __( "Set to Pending", 'advanced-classifieds-and-directory-pro' ),
		'set_to_completed' => __( "Set to Completed", 'advanced-classifieds-and-directory-pro' ),
		'set_to_failed'    => __( "Set to Failed", 'advanced-classifieds-and-directory-pro' ),		
		'set_to_cancelled' => __( "Set to Cancelled", 'advanced-classifieds-and-directory-pro' ),
		'set_to_refunded'  => __( "Set to Refunded", 'advanced-classifieds-and-directory-pro' )
	);
			
	return apply_filters( 'acadp_payment_bulk_actions', $actions );
	
}

/**
 * Sanitize Amount
 *
 * Returns a sanitized amount by stripping out thousands separators.
 *
 * @since    1.0.0
 *
 * @param    string    $amount               Price amount to format.
 * @param    array     $currency_settings    Currency Settings.
 * @return   string    $amount               Newly sanitized amount.
 */
function acadp_sanitize_amount( $amount, $currency_settings = array() ) {

	$is_negative = false;
	
	if( empty( $currency_settings ) ) {
		$currency_settings = get_option( 'acadp_currency_settings' );
	}
	
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	$thousands_sep = ! empty( $currency_settings[ 'thousands_separator' ] ) ? $currency_settings[ 'thousands_separator' ] : ',';
	$decimal_sep = ! empty( $currency_settings[ 'decimal_separator' ] ) ? $currency_settings[ 'decimal_separator' ] : '.';

	// Sanitize the amount
	if( $decimal_sep == ',' && false !== ( $found = strpos( $amount, $decimal_sep ) ) ) {
		if( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		} else if( empty( $thousands_sep ) && false !== ( $found = strpos( $amount, '.' ) ) ) {
			$amount = str_replace( '.', '', $amount );
		}

		$amount = str_replace( $decimal_sep, '.', $amount );
	} else if( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( $thousands_sep, '', $amount );
	}

	if( $amount < 0 ) {
		$is_negative = true;
	}

	$amount = preg_replace( '/[^0-9\.]/', '', $amount );
	$decimals = acadp_currency_decimal_count( 2, $currency );
	$amount = number_format( (double) $amount, $decimals, '.', '' );

	if( $is_negative ) {
		$amount *= -1;
	}

	return apply_filters( 'acadp_sanitize_amount', $amount );
	
}

/**
 * Sanitize Paymount Amount
 *
 * Returns a sanitized amount by stripping out thousands separators.
 *
 * @since    1.5.4
 *
 * @param    string    $amount    Price amount to format.
 * @return   string               Newly sanitized amount.
 */
function acadp_sanitize_payment_amount( $amount ) {

	return acadp_sanitize_amount( $amount, acadp_get_payment_currency_settings() );

}

/**
 * Returns a nicely formatted amount.
 *
 * @since    1.0.0
 *
 * @param    string    $amount               Price amount to format
 * @param    string    $decimals             Whether or not to use decimals. Useful when set 
 											 to false for non-currency numbers.
 * @param    array     $currency_settings    Currency Settings.
 * @return   string    $amount               Newly formatted amount or Price Not Available
 */
function acadp_format_amount( $amount, $decimals = true, $currency_settings = array() ) {

	if( empty( $currency_settings ) ) {
		$currency_settings = get_option( 'acadp_currency_settings' );
	}
	
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	$thousands_sep = ! empty( $currency_settings[ 'thousands_separator' ] ) ? $currency_settings[ 'thousands_separator' ] : ',';
	$decimal_sep = ! empty( $currency_settings[ 'decimal_separator' ] ) ? $currency_settings[ 'decimal_separator' ] : '.';

	// Format the amount
	if( $decimal_sep == ',' && false !== ( $sep_found = strpos( $amount, $decimal_sep ) ) ) {
		$whole = substr( $amount, 0, $sep_found );
		$part = substr( $amount, $sep_found + 1, ( strlen( $amount ) - 1 ) );
		$amount = $whole . '.' . $part;
	}

	// Strip , from the amount (if set as the thousands separator)
	if( $thousands_sep == ',' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ',', '', $amount );
	}

	// Strip ' ' from the amount (if set as the thousands separator)
	if( $thousands_sep == ' ' && false !== ( $found = strpos( $amount, $thousands_sep ) ) ) {
		$amount = str_replace( ' ', '', $amount );
	}

	if( empty( $amount ) ) {
		$amount = 0;
	}

	if( $decimals ) {
		$decimals  = acadp_currency_decimal_count( 2, $currency );
	} else {
		$decimals = 0;
	}
	
	$formatted = number_format( $amount, $decimals, $decimal_sep, $thousands_sep );

	return apply_filters( 'acadp_format_amount', $formatted, $amount, $decimals, $decimal_sep, $thousands_sep );
	
}

/**
 * Returns a nicely formatted amount.
 *
 * @since    1.5.4
 *
 * @param    string    $amount      Price amount to format
 * @param    string    $decimals    Whether or not to use decimals. Useful when set to false for non-currency numbers.
 * @return   string                 Newly formatted amount or Price Not Available
 */
function acadp_format_payment_amount( $amount, $decimals = true ) {

	return acadp_format_amount( $amount, $decimals, acadp_get_payment_currency_settings() );
	
}

/**
 * Set the number of decimal places per currency
 *
 * @since    1.0.0
 *
 * @param    int       $decimals    Number of decimal places.
 * @param    string    $currency    Payment currency.
 * @return   int       $decimals
*/
function acadp_currency_decimal_count( $decimals = 2, $currency = 'USD' ) {

	switch( $currency ) {
		case 'RIAL' :
		case 'JPY' :
		case 'TWD' :
		case 'HUF' :
			$decimals = 0;
			break;
	}

	return apply_filters( 'acadp_currency_decimal_count', $decimals, $currency );
	
}

/**
 * Get the directory's set currency
 *
 * @since    1.0.0
 * @return   string    The currency code.
 */
function acadp_get_currency() {

	$currency_settings = get_option( 'acadp_currency_settings' );
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	
	return strtoupper( $currency );
	
}

/**
 * Get the directory's set payment currency
 *
 * @since    1.5.4
 * @return   string    The currency code.
 */
function acadp_get_payment_currency() {

	$currency_settings = acadp_get_payment_currency_settings();
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	
	return strtoupper( $currency );
	
}

/**
 * Given a currency determine the symbol to use. If no currency given, site default is used.
 * If no symbol is determine, the currency string is returned.
 *
 * @since    1.0.0
 *
 * @param    string    $currency    The currency string.
 * @return   string                 The symbol to use for the currency.
 */
function acadp_currency_symbol( $currency = '' ) {

	switch( $currency ) {
		case "GBP" :
			$symbol = '&pound;';
			break;
		case "BRL" :
			$symbol = 'R&#36;';
			break;
		case "EUR" :
			$symbol = '&euro;';
			break;
		case "USD" :
		case "AUD" :
		case "NZD" :
		case "CAD" :
		case "HKD" :
		case "MXN" :
		case "SGD" :
			$symbol = '&#36;';
			break;
		case "JPY" :
			$symbol = '&yen;';
			break;
		default :
			$symbol = $currency;
			break;
	}

	return apply_filters( 'acadp_currency_symbol', $symbol, $currency );
	
}

/**
 * Formats the currency display.
 *
 * @since    1.0.0
 *
 * @param    string    $price                Paid Amount.
 * @param    array     $currency_settings    Currency Settings.
 * @return   string    $formatted            Formatted amount with currency.
 */
function acadp_currency_filter( $price = '', $currency_settings = array() ) {

	if( empty( $currency_settings ) ) {
		$currency_settings = get_option( 'acadp_currency_settings' );
	}
	
	$currency = ! empty( $currency_settings[ 'currency' ] ) ? $currency_settings[ 'currency' ] : 'USD';
	$position = $currency_settings['position'];

	$negative = $price < 0;

	if( $negative ) {
		$price = substr( $price, 1 ); // Remove proceeding "-" -
	}

	$symbol = acadp_currency_symbol( $currency );

	if( $position == 'before' ) {
	
		switch( $currency ) {
			case "GBP" :
			case "BRL" :
			case "EUR" :
			case "USD" :
			case "AUD" :
			case "CAD" :
			case "HKD" :
			case "MXN" :
			case "NZD" :
			case "SGD" :
			case "JPY" :
				$formatted = $symbol . $price;
				break;
			default :
				$formatted = $currency . ' ' . $price;
				break;
		}
		
		$formatted = apply_filters( 'acadp_' . strtolower( $currency ) . '_currency_filter_before', $formatted, $currency, $price );
		
	} else {
	
		switch( $currency ) {
			case "GBP" :
			case "BRL" :
			case "EUR" :
			case "USD" :
			case "AUD" :
			case "CAD" :
			case "HKD" :
			case "MXN" :
			case "SGD" :
			case "JPY" :
				$formatted = $price . $symbol;
				break;
			default :
				$formatted = $price . ' ' . $currency;
				break;
		}
		
		$formatted = apply_filters( 'acadp_' . strtolower( $currency ) . '_currency_filter_after', $formatted, $currency, $price );
		
	}

	if( $negative ) {
		// Prepend the mins sign before the currency sign
		$formatted = '-' . $formatted;
	}

	return $formatted;
	
}

/**
 * Formats the payment currency display.
 *
 * @since    1.5.4
 *
 * @param    string    $price    Paid Amount.
 * @return   string              Formatted amount with currency.
 */
function acadp_payment_currency_filter( $price = '' ) {

	return acadp_currency_filter( $price, acadp_get_payment_currency_settings() );

}

/**
 * Get the directory's payment currency settings.
 *
 * @since    1.5.4
 * @return   array    $currency_settings    Array. Currency Settings.
 */
function acadp_get_payment_currency_settings() {
	
	$gateway_settings = get_option( 'acadp_gateway_settings' );
	
	if( ! empty( $gateway_settings[ 'currency' ] ) ) {
	
		$currency_settings = array(
			'currency'            => $gateway_settings[ 'currency' ],
			'thousands_separator' => ! empty( $gateway_settings[ 'thousands_separator' ] ) ? $gateway_settings[ 'thousands_separator' ] : ',',
			'decimal_separator'   => ! empty( $gateway_settings[ 'decimal_separator' ] ) ? $gateway_settings[ 'decimal_separator' ] : '.',
			'position'            => $gateway_settings[ 'position' ]
		);
		
	} else {
	
		$currency_settings = get_option( 'acadp_currency_settings' );
		
	}
	
	return $currency_settings;
	
}

/**
 * Get the list of listings view options.
 *
 * @since    1.5.2
 *
 * @return   array    $view_options    List of view Options.
 */
function acadp_get_listings_view_options() {

	$general_settings = get_option( 'acadp_general_settings' );
	$listings_settings = get_option( 'acadp_listings_settings' );
	
	$options   = ! empty( $listings_settings['view_options'] ) ? $listings_settings['view_options'] : array();
	$options[] = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : $listings_settings['default_view'];
	$options   = array_unique( $options );
	
	if( empty( $general_settings['has_map'] ) && array_key_exists( 'map', $options ) ) {
		unset( $options['map'] );
	}
	
	$views = array();
	
	foreach( $options as $option ) {
	
		switch( $option ) {
			case 'list' :
				$views[ $option ] = __( 'List', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'grid' :
				$views[ $option ] = __( 'Grid', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'map' :
				$views[ $option ] = __( 'Map', 'advanced-classifieds-and-directory-pro' );
				break;
		}
		
	}
	
	return $views;	

}

/**
 * Get the view(layout) name the listings should be displayed.
 *
 * @since    1.0.0
 *
 * @param    string    $view    Default View.
 * @return   string    $view    Grid or List.
 */
function acadp_get_listings_current_view_name( $view ) {

	$general_settings = get_option( 'acadp_general_settings' );
	
	if( isset( $_GET['view'] ) ) {
		$view = sanitize_text_field( $_GET['view'] );
	}
	
	$allowed_views = array( 'list', 'grid', 'map' );
	if( ! in_array( $view, $allowed_views ) ) {
		$listings_settings = get_option( 'acadp_listings_settings' );
		$view = $listings_settings['default_view'];
	}
	
	if( empty( $general_settings['has_map'] ) && 'map' == $view ) {
		$view = 'list';
	}
	
	return $view;
				
}

/**
 * Get the highest priority ACADP template file that exists.
 *
 * @since    1.0.0
 *
 * @param    string    $name      The name of the specialized template.
 * @param    string    $widget    Name of the Widget(only if applicable).
 * @return   string               The ACADP template file.
 */
function acadp_get_template( $name, $widget = '' ) {
	
	$template_file = '';
	
	if( '' !== $widget ) {
	
		$templates = array(
			"acadp/widgets/$widget/$name",
			"acadp_templates/widgets/$widget/$name" // deprecated in 1.5.4
		);
		
		if( ! $template_file = locate_template( $templates ) ) {		
			$template_file = ACADP_PLUGIN_DIR . "widgets/$widget/views/$name";
		}
	
	} else {
	
		$templates = array(
			"acadp/$name",
			"acadp_templates/$name" // deprecated in 1.5.4
		);
		
		if( ! $template_file = locate_template( $templates ) ) {		
			$template_file = ACADP_PLUGIN_DIR . "public/partials/$name";
		}
		
	}
	
	return apply_filters( 'acadp_get_template', $template_file, $name, $widget );

}

/**
 * List ACADP categories.
 *
 * @since    1.0.0
 *
 * @param    array     $settings    Settings args.
 * @return   string                 HTML code that contain categories list.
 */
function acadp_list_categories( $settings ) {
	
	if( $settings['depth'] <= 0 ) {
		return;
	}
		
	$args = array(
		'orderby'      => $settings['orderby'], 
    	'order'        => $settings['order'],
    	'hide_empty'   => ! empty( $settings['hide_empty'] ) ? 1 : 0, 
		'parent'       => $settings['term_id'],
		'hierarchical' => false
  	);
		
	$terms = get_terms( 'acadp_categories', $args );
	
	$html = '';
				
	if( count( $terms ) > 0 ) {	
			
		--$settings['depth'];
			
		$html .= '<ul class="list-unstyled">';
							
		foreach( $terms as $term ) {
			$settings['term_id'] = $term->term_id;
			
			$count = 0;
			if( ! empty( $settings['hide_empty'] ) || ! empty( $settings['show_count'] ) ) {
				$count = acadp_get_listings_count_by_category( $term->term_id, $settings['pad_counts'] );
				
				if( ! empty( $settings['hide_empty'] ) && 0 == $count ) continue;
			}
			
			$html .= '<li>'; 
			$html .= '<a href="' . acadp_get_category_page_link( $term ) . '" title="' . sprintf( __( "View all posts in %s", 'advanced-classifieds-and-directory-pro' ), $term->name ) . '" ' . '>';
			$html .= $term->name;
			if( ! empty( $settings['show_count'] ) ) {
				$html .= ' (' . $count . ')';
			}
			$html .= '</a>';
			$html .= acadp_list_categories( $settings );
			$html .= '</li>';	
		}	
			
		$html .= '</ul>';
					
	}		
			
	return $html;

}

/**
 * Get total listings count.
 *
 * @since    1.0.0
 *
 * @param    int     $term_id       Custom Taxonomy term ID.
 * @param    bool    $pad_counts    Pad the quantity of children in the count.
 * @return   int                    Listings count.
 */
function acadp_get_listings_count_by_category( $term_id, $pad_counts = true ) {
	
	$args = array(
		'fields'          =>'ids',
		'posts_per_page'  => -1,
   		'post_type'       => 'acadp_listings',
   		'post_status'     => 'publish',
   		'tax_query' 	  => array(
			array(
				'taxonomy'         => 'acadp_categories',
				'field'            => 'term_id',
				'terms'            => $term_id,
				'include_children' => $pad_counts
			)
		)    		
	);

	return count( get_posts( $args ) );

}

/**
 * List ACADP locations.
 *
 * @since    1.0.0
 *
 * @param    array     $settings    Settings args.
 * @return   string                 HTML code that contain locations list.
 */
function acadp_list_locations( $settings ) {
	
	if( $settings['depth'] <= 0 ) {
		return;
	}
		
	$args = array(
		'orderby'      => $settings['orderby'], 
    	'order'        => $settings['order'],
    	'hide_empty'   => ! empty( $settings['hide_empty'] ) ? 1 : 0, 
		'parent'       => $settings['term_id'],
		'hierarchical' => false
  	);
		
	$terms = get_terms( 'acadp_locations', $args );
	
	$html = '';
				
	if( count( $terms ) > 0 ) {	
			
		--$settings['depth'];
			
		$html .= '<ul class="list-unstyled">';
							
		foreach( $terms as $term ) {
			$settings['term_id'] = $term->term_id;
			
			$html .= '<li>'; 
			$html .= '<a href="' . acadp_get_location_page_link( $term ) . '" title="' . sprintf( __( "View all posts in %s", 'advanced-classifieds-and-directory-pro' ), $term->name ) . '" ' . '>';
			$html .= $term->name;
			if( ! empty( $settings['show_count'] ) ) {
				$html .= ' (' . acadp_get_listings_count_by_location( $term->term_id, $settings['pad_counts'] ) . ')';
			}
			$html .= '</a>';
			$html .= acadp_list_locations( $settings );
			$html .= '</li>';	
		}	
			
		$html .= '</ul>';
					
	}		
			
	return $html;

}

/**
 * Get total listings count.
 *
 * @since    1.0.0
 *
 * @param    int     $term_id       Custom Taxonomy term ID.
 * @param    bool    $pad_counts    Pad the quantity of children in the count.
 * @return   int                    Listings count.
 */
function acadp_get_listings_count_by_location( $term_id, $pad_counts = true ) {
	
	$args = array(
		'fields'          =>'ids',
		'posts_per_page'  => -1,
   		'post_type'       => 'acadp_listings',
   		'post_status'     => 'publish',
   		'tax_query' 	  => array(
			array(
				'taxonomy'         => 'acadp_locations',
				'field'            => 'term_id',
				'terms'            => $term_id,
				'include_children' => $pad_counts
			)
		)    		
	);

	return count( get_posts( $args ) );

}

/**
 * Insert/Update listing views count.
 *
 * @since    1.0.0
 *
 * @param    int      $post_id    Post ID.
 */
function acadp_update_listing_views_count( $post_id ) {

    $user_ip = $_SERVER['REMOTE_ADDR']; // retrieve the current IP address of the visitor
    $key     = $user_ip . '_acadp_' . $post_id; // combine post ID & IP to form unique key
    $value   = array( $user_ip, $post_id ); // store post ID & IP as separate values (see note)
    $visited = get_transient( $key ); // get transient and store in variable

    // check to see if the Post ID/IP ($key) address is currently stored as a transient
    if( false === ( $visited ) ) {

        // store the unique key, Post ID & IP address for 12 hours if it does not exist
        set_transient( $key, $value, 60*60*12 );

        // now run post views function
        $count_key = 'views';
        $count = get_post_meta($post_id, $count_key, true);
        if( '' == $count ) {
            $count = 0;
            delete_post_meta( $post_id, $count_key );
            add_post_meta( $post_id, $count_key, '0' );
        } else {
            $count++;
            update_post_meta( $post_id, $count_key, $count );
        }

    }

}

/**
 * Get orderby list.
 *
 * @since    1.0.0
 *
 * @return   array    $options    A list of the orderby options.
 */
function acadp_get_listings_orderby_options() {

	$general_settings = get_option( 'acadp_general_settings' );
	
	$options = array(
		'title-asc'  => __( "A to Z ( title )", 'advanced-classifieds-and-directory-pro' ),
		'title-desc' => __( "Z to A ( title )", 'advanced-classifieds-and-directory-pro' ),
		'date-desc'  => __( "Recently added ( latest )", 'advanced-classifieds-and-directory-pro' ),
		'date-asc'   => __( "Date added ( oldest )", 'advanced-classifieds-and-directory-pro' ),
		'views-desc' => __( "Most viewed", 'advanced-classifieds-and-directory-pro' ),
		'views-asc'  => __( "Less viewed", 'advanced-classifieds-and-directory-pro' )			
	);
	
	if( ! empty( $general_settings['has_price'] ) ) {
		$options['price-asc']  = __( "Price ( low to high )", 'advanced-classifieds-and-directory-pro' );
		$options['price-desc'] = __( "Price ( high to low )", 'advanced-classifieds-and-directory-pro' );						
	}
	
	return apply_filters( 'acadp_get_listings_orderby_options', $options );
	
}

/**
 * Get the current listings order.
 *
 * @since    1.5.5
 *
 * @param    string    $default_order    Default Order.
 * @return   string    $order            Listings Order.
 */
function acadp_get_listings_current_order( $default_order = '' ) {

	$order = $default_order;
	
	if( isset( $_GET['sort'] ) ) {
		$order = sanitize_text_field( $_GET['sort'] );
	} else if( isset( $_GET['order'] ) ) {
		$order = sanitize_text_field( $_GET['order'] );
	}

	return apply_filters( 'acadp_get_listings_current_order', $order );
				
}

/**
 * Get total listings count of the current user.
 *
 * @since    1.0.0
 *
 * @return   int    Total listings count.
 */
function acadp_get_user_total_listings() {

	global $wpdb;

	$where = get_posts_by_author_sql( 'acadp_listings', true, get_current_user_id(), false );
	$count = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts $where" );

  	return $count;
	
}

/**
 * Get active listings count of the current user.
 *
 * @since    1.0.0
 *
 * @return   int    Active listings count.
 */
function acadp_get_user_total_active_listings() {

	global $wpdb;

	$where = get_posts_by_author_sql( 'acadp_listings', true, get_current_user_id(), true );
	$count = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts $where" );

  	return $count;
	
}

/**
 * Parse the video URL and determine it's valid embeddable URL for usage.
 *
 * @since    1.0.0
 *
 * @param    string    $url    YouTube / Vimeo URL.
 * @return   array              An array of video metadata if found.
 */
function acadp_parse_videos( $url ) {
	
	$embeddable_url = '';
	
	// Check for YouTube
	$is_youtube = preg_match( '/youtu\.be/i', $url ) || preg_match( '/youtube\.com\/watch/i', $url );
	
	if( $is_youtube ) {
    	$pattern = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/';
    	preg_match( $pattern, $url, $matches );
    	if( count( $matches ) && strlen( $matches[7] ) == 11 ) {
      		$embeddable_url = 'https://www.youtube.com/embed/'.$matches[7];
    	}
  	}
	
	// Check for Vimeo
	$is_vimeo = preg_match( '/vimeo\.com/i', $url );
	
	if( $is_vimeo ) {
    	$pattern = '/\/\/(www\.)?vimeo.com\/(\d+)($|\/)/';
    	preg_match( $pattern, $url, $matches );
    	if( count( $matches ) ) {
      		$embeddable_url = 'https://player.vimeo.com/video/'.$matches[2];
    	}
  	}
	
	// Return
	return $embeddable_url;

}

/**
 * Get current page number.
 *
 * @since    1.0.0
 *
 * @return    int    $paged    The current page number.
 */
function acadp_get_page_number() {

	global $paged;
	
	if( get_query_var('paged') ) {
    	$paged = get_query_var('paged');
	} else if( get_query_var('page') ) {
    	$paged = get_query_var('page');
	} else {
		$paged = 1;
	}
    	
	return absint( $paged );
		
}

/**
  * Removes an item or list from the query string.
  *
  * @since    1.0.0
  *
  * @param    string|array    $key                Query key or keys to remove.
  * @param    bool|string     $query Optional.    When false uses the $_SERVER value. Default false.
  * @return   string                              New URL query string.
  */
function acadp_remove_query_arg( $key, $query = false ) {

	if( is_array( $key ) ) { // removing multiple keys
		foreach( $key as $k ) {
			$query = str_replace( '#038;', '&', $query );
			$query = add_query_arg( $k, false, $query );
		}
		
		return $query;
	}
		
	return add_query_arg( $key, false, $query );
	
}

/**
  * Verify the captcha answer.
  *
  * @since    1.0.0
  *
  * @param    string    $form    ACADP Form Name.
  * @return   bool               True if not a bot, false if bot.
  */
function acadp_is_human( $form ) {

	$recaptcha_settings = get_option( 'acadp_recaptcha_settings' );
	
	$has_captcha = false;
	if( isset( $recaptcha_settings['forms'] ) && '' !== $recaptcha_settings['site_key'] && '' !== $recaptcha_settings['secret_key'] ) {
		if( in_array( $form, $recaptcha_settings['forms'] ) ) {
			$has_captcha = true;
		}
	}
	
	if( $has_captcha ) {
	
		$response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';
		
		if( '' !== $response ) {			
			
			// make a GET request to the Google reCAPTCHA Server
			$request = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_settings['secret_key'] . '&response=' . $response . '&remoteip=' . $_SERVER["REMOTE_ADDR"] );
			
			// get the request response body
			$response_body = wp_remote_retrieve_body( $request );
			
			$result = json_decode( $response_body, true );
			
			// return true or false, based on users input
			return ( true == $result['success'] ) ? true : false;
	
		} else {
		
			return false;
			
		}
	
	}
	
	return true;
	
}

/**
 * Get payment gateways.
 *
 * @since    1.0.0
 *
 * @return   array    $gateways    A list of the available gateways.
 */
function acadp_get_payment_gateways() {

	$gateways = apply_filters( 'acadp_payment_gateways', array( 'offline' => __( 'Offline Payment', 'advanced-classifieds-and-directory-pro' ) ) );	
	return $gateways;
	
}

/**
 * Update Order details. Send emails to site and listing owners
 * when order completed.
 *
 * @since    1.0.0
 *
 * $param    array    $order   Order details.
 */
function acadp_order_completed( $order ) {	
	
	// update order details
	update_post_meta( $order['id'], 'payment_status', 'completed' );
	update_post_meta( $order['id'], 'transaction_id', $order['transaction_id'] );
	
	// If the order has featured
	$featured = get_post_meta( $order['id'], 'featured', true );
	
	if( ! empty( $featured ) ) {
		$post_id = get_post_meta( $order['id'], 'listing_id', true );
		update_post_meta( $post_id, 'featured', 1 );
	}
	
	// Hook for developers
	do_action( 'acadp_order_completed', $order['id'] );
		
	// send emails
	acadp_email_listing_owner_order_completed( $order['id'] );
	acadp_email_admin_payment_received( $order['id'] );
		
}

/**
 * Rotate images to the correct orientation.
 *
 * @since    1.5.4
 *
 * @param    array    $file    $_FILES array
 * @return   array	           $_FILES array in the correct orientation
 */
function acadp_exif_rotate( $file ){

	if( ! function_exists( 'read_exif_data' ) ) {
		return $file;
	}
	
	$exif = read_exif_data( $file['tmp_name'] );
	$exif_orient = isset( $exif['Orientation'] ) ? $exif['Orientation'] : 0;
	$rotate_image = 0;

	if( 6 == $exif_orient ) {
		$rotate_image = 90;
	} else if ( 3 == $exif_orient ) {
		$rotate_image = 180;
	} else if ( 8 == $exif_orient ) {
		$rotate_image = 270;
	}

	if( $rotate_image ) {
	
		if( class_exists( 'Imagick' ) ) {
		
			$imagick = new Imagick();
			$imagick_pixel = new ImagickPixel();
			$imagick->readImage( $file['tmp_name'] );
			$imagick->rotateImage( $imagick_pixel, $rotate_image );
			$imagick->setImageOrientation( 1 );
			$imagick->writeImage( $file['tmp_name'] );
			$imagick->clear();
			$imagick->destroy();
		
		} else {
		
			$rotate_image = -$rotate_image;
			
			switch( $file['type'] ) {
				case 'image/jpeg' :
					if( function_exists( 'imagecreatefromjpeg' ) ) {
						$source = imagecreatefromjpeg( $file['tmp_name'] );
						$rotate = imagerotate( $source, $rotate_image, 0 );
						imagejpeg( $rotate, $file['tmp_name'] );
					}
					break;
				case 'image/png' :
					if( function_exists( 'imagecreatefrompng' ) ) {
						$source = imagecreatefrompng( $file['tmp_name'] );
						$rotate = imagerotate( $source, $rotate_image, 0 );
						imagepng( $rotate, $file['tmp_name'] );
					}
					break;
				case 'image/gif' :
					if( function_exists( 'imagecreatefromgif' ) ) {
						$source = imagecreatefromgif( $file['tmp_name'] );
						$rotate = imagerotate( $source, $rotate_image, 0 );
						imagegif( $rotate, $file['tmp_name'] );
					}
					break;
			}

		}
	
	}
	
	return $file;

}

/**
 * Retrieve the listing status in localized format.
 *
 * @since    1.5.4
 *
 * @param    string    $status    Listing status.
 * @return   string    $status    Localized listing status.
 */
function acadp_get_listing_status_i18n( $status ) {

	$post_status = get_post_status_object( $status );			
	return $post_status->label;
	
}

/**
 * Check if listing specific widgets are enabled.
 *
 * @since    1.5.5
 *
 * @return   bool    $found    0 or 1.
 */
function acadp_has_active_listing_widgets() {

	// Vars
	$sidebars_widgets = get_option( 'sidebars_widgets' );
	$listing_widgets = array(
		ACADP_PLUGIN_NAME.'-widget-listing-video',
		ACADP_PLUGIN_NAME.'-widget-listing-address',
		ACADP_PLUGIN_NAME.'-widget-listing-contact'
	);
	$found = 0;
	
	// Loop through active widgets list
	foreach( $sidebars_widgets as $sidebar => $widgets ) {
		// Check if the sidebar is active
		if( is_active_sidebar( $sidebar ) ) {
			// Loop through widgets registered inside this sidebar
			foreach( $widgets as $widget ) {
				// Loop through our listing specific widgets list
				foreach( $listing_widgets as $listing_widget ) {
					// Check if the current widget belongs to one of our listing specific widgets
					if( strpos( $widget, $listing_widget ) !== FALSE ) {
						$found = 1;
						break;
					}
				}
			}
		}
	}
	
	return $found;

}

/**
 * Get custom field types.
 *
 * @since     1.5.8
 *
 * @return    array    $types    Array of custom field types.
 */
function acadp_get_custom_field_types() {

	$types = array(
		'text'     => __( 'Text', 'advanced-classifieds-and-directory-pro' ),
		'textarea' => __( 'Text Area', 'advanced-classifieds-and-directory-pro' ),
		'select'   => __( 'Select', 'advanced-classifieds-and-directory-pro' ),
		'checkbox' => __( 'Checkbox', 'advanced-classifieds-and-directory-pro' ),
		'radio'    => __( 'Radio Button', 'advanced-classifieds-and-directory-pro' ),
		'url'      => __( 'URL', 'advanced-classifieds-and-directory-pro' )
	);
		
	// Return
	return $types;

}

/**
 * Get custom fields.
 *
 * @since     1.5.8
 *
 * @param     int      $category     Category ID.
 * @return    array    $field_ids    Array of custom field ids.
 */
function acadp_get_custom_field_ids( $category = 0 ) {

	// Get global fields
	$args = array(
		'post_type'      => 'acadp_fields',
		'post_status'    => 'publish',
		'posts_per_page' => -1,	
		'fields'		 => 'ids',
		'meta_query' 	 => array(
			array(
				'key'   => 'associate',
				'value' => 'form'
			),
		)
	);
	
	$field_ids = get_posts( $args );	
	
	// Get category fields	
	if( $category > 0 ) {
	
		$args = array(
			'post_type'      => 'acadp_fields',
			'post_status'    => 'publish',
			'posts_per_page' => -1,	
			'fields'		 => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy'         => 'acadp_categories',
					'field'            => 'term_id',
					'terms'            => $category,
					'include_children' => false,
				),
			)
		);
		
		$category_fields = get_posts( $args );
		
		$field_ids = array_merge( $field_ids, $category_fields );
		$field_ids = array_unique( $field_ids );
	
	}	
	
	// Return
	if( empty( $field_ids ) ) {
		$field_ids = array( 0 );
	}
	
	return $field_ids;

}
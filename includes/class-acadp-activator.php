<?php

/**
 * Fired during plugin activation.
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
 * ACADP_Activator Class
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Activator {

	/**
	 * Called when the plugin is activated.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @static
	 */
	public static function activate() {
	
		// Insert plugin settings and default values for the first time
		$options = array(
			'acadp_general_settings' => array(
				'load_bootstrap'             => array( 'css', 'javascript' ),	
				'listing_duration'           => 365,
				'has_location'               => 1,						
				'base_location'              => -1,		
				'default_location'           => -1,	
				'disable_parent_categories'  => 0,
				'text_editor'                => 'wp_editor',			
				'has_price'                  => 1,				
				'has_images'                 => 1,
				'maximum_images_per_listing' => 6,
				'has_video'                  => 1,	
				'has_map'                    => 1,			
				'has_contact_form'           => 1,
				'contact_form_require_login' => 0,
				'has_comment_form'           => 1,				
				'has_report_abuse'           => 1,
				'has_favourites'             => 1,	
				'new_listing_status'         => 'publish',
				'edit_listing_status'        => 'publish',
				'show_new_tag'               => 1,
				'new_listing_threshold'      => 3,
				'new_listing_label'          => __( 'New', 'advanced-classifieds-and-directory-pro' ),
				'show_popular_tag'           => 1,
				'popular_listing_threshold'  => 1000,
				'popular_listing_label'      => __( 'Popular', 'advanced-classifieds-and-directory-pro' ),
				'display_options'            => array( 'category', 'date', 'user', 'views' ),
				'has_listing_renewal'        => 1, 
				'delete_expired_listings'    => 15			
			),	
			'acadp_listings_settings' => array(
				'view_options'         => array( 'list', 'grid', 'map' ),
				'default_view'         => 'list',
				'include_results_from' => array( 'child_categories', 'child_locations' ),
				'orderby'              => 'date',
				'order'                => 'desc',
				'columns'              => 3,
				'listings_per_page'    => 10,
				'display_in_header'    => array( 'listings_count', 'views_selector', 'orderby_dropdown' ),
				'display_in_listing'   => array( 'category', 'location', 'price', 'date', 'user', 'views' ), 
				'excerpt_length'       => 25		
			),
			'acadp_locations_settings' => array(
				'columns'    => 3,
				'depth'      => 2,
				'orderby'    => 'name',
				'order'      => 'asc',
				'show_count' => 1,
				'hide_empty' => 0
			),
			'acadp_categories_settings' => array(
				'view'       => 'text_list',
				'columns'    => 3,
				'depth'      => 2,
				'orderby'    => 'name',
				'order'      => 'asc',
				'show_count' => 1,
				'hide_empty' => 0
			),
			'acadp_registration_settings' => array(
				'engine'                 => 'others',
				'custom_login'           => '',
				'custom_register'        => '',
				'custom_forgot_password' => ''
			),
			'acadp_currency_settings' => array(
				'currency'            => 'USD',
				'position'            => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.'
			),
			'acadp_page_settings' => array(
			),
			'acadp_gateway_settings' => array(
				'gateways'            => array( 'offline' ),
				'test_mode'           => 1,
				'use_https'           => 0,
				'currency'            => '',
				'position'            => 'before',
				'thousands_separator' => ',',
				'decimal_separator'   => '.'
			),
			'acadp_gateway_offline_settings' => array(
				'label'        => __( 'Direct Bank Transfer', 'advanced-classifieds-and-directory-pro' ),
				'description'  => __( 'Make your payment directly in our bank account. Please use your Order ID as payment reference. Your order won\'t get approved until the funds have cleared in our account.', 'advanced-classifieds-and-directory-pro' ),
				'instructions' => __( "Make your payment directly in our bank account. Please use your Order ID as payment reference. Your order won't get approved until the funds have cleared in our account.\n\nAccount details :\n\nAccount Name : YOUR ACCOUNT NAME\nAccount Number : YOUR ACCOUNT NUMBER\nBank Name : YOUR BANK NAME\n\nIf we don't receive your payment within 48 hrs, we will cancel the order.", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_featured_listing_settings' => array(
				'enabled'           => 0,				
				'label'             => __( 'Featured', 'advanced-classifieds-and-directory-pro' ),
				'description'       => __( 'Upgrade your listing to featured status. Featured listings will always appear on top of regular listings.', 'advanced-classifieds-and-directory-pro' ),
				'price'             => 39.99,
				'show_featured_tag' => 1
				
			),
			'acadp_email_settings' => array(
				'from_name'                   => get_option( 'blogname' ),
				'from_email'                  => get_option( 'admin_email' ),
				'admin_notice_emails'         => get_option( 'admin_email' ),
				'notify_admin'                => array( 'listing_submitted', 'order_created', 'payment_received' ),
				'notify_users'                => array( 'listing_submitted', 'listing_published', 'listing_renewal', 'listing_expired', 'remind_renewal', 'order_created', 'order_completed' ),
				'show_email_address_publicly' => 1
			),
			'acadp_email_template_listing_submitted' => array(
				'subject' => __( '[{site_name}] Listing "{listing_title}" received', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nYour submission \"{listing_title}\" has been received and it's pending review. This review process could take up to 48 hours.\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_listing_published' => array(
				'subject' => __( '[{site_name}] Listing "{listing_title}" published', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nYour listing \"{listing_title}\" is now available at {listing_url} and can be viewed by the public.\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )	
			),
			'acadp_email_template_listing_renewal' => array(
				'email_threshold' => 3,
				'subject'         => __( '[{site_name}] {listing_title} - Expiration notice', 'advanced-classifieds-and-directory-pro' ),
				'body'            => __( "Dear {name},\n\nYour listing \"{listing_title}\" is about to expire at {site_link}. You can renew it here: {renewal_link}.\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_listing_expired' => array(
				'subject' => __( '[{site_name}] {listing_title} - Expiration notice', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nYour listing \"{listing_title}\" in category \"{category_name}\" expired on {expiration_date}. To renew your listing click the link below.\n{renewal_link}\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_renewal_reminder' => array(
				'reminder_threshold' => 3,
				'subject'            => __( '[{site_name}] {listing_title} - Expiration reminder', 'advanced-classifieds-and-directory-pro' ),
				'body'               => __( "Dear {name},\n\nWe've noticed that you haven't renewed your listing \"{listing_title}\" for category \"{category_name}\" at {site_link} and just wanted to remind you that it expired on {expiration_date}. Please remember you can still renew it here: {renewal_link}.\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_order_created' => array(
				'subject' => __( '[{site_name}] Thank you for your order', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nThe order is now created.\n\nThis notification was for the order #{order_id} on the website {site_name}.\nYou can access the order details directly by clicking on the link below after logging in your account:\n{order_page}\n\n{order_details}\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_order_created_offline' => array(
				'subject' => __( '[{site_name}] Thank you for your order', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nThe order is now created.\n\nThis notification was for the order #{order_id} on the website {site_name}.\n\nMake your payment directly in our bank account. Please use your Order ID as payment reference. Your order won't get approved until the funds have cleared in our account.\n\nAccount details :\n\nAccount Name : YOUR ACCOUNT NAME\nAccount Number : YOUR ACCOUNT NUMBER\nBank Name : YOUR BANK NAME\n\nIf we don't receive your payment within 48 hrs, we will cancel the order.\nYou can access the order details directly by clicking on the link below after logging in your account:\n{order_page}\n\n{order_details}\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_order_completed' => array(
				'subject' => __( '[{site_name}] Order completed', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nYour recent order #{order_id} on {site_name} has been completed.\n\nYou can access the order details directly by clicking on the link below after logging in your account:\n{order_page}\n\n{order_details}\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_email_template_listing_contact' => array(
				'subject' => __( '[{site_name}] Contact via "{listing_title}"', 'advanced-classifieds-and-directory-pro' ),
				'body'    => __( "Dear {name},\n\nYou have received a reply from your listing at {listing_url}.\n\nName: {sender_name}\nEmail: {sender_email}\nMessage: {message}\nTime: {now}\n\nThanks,\nThe Administrator of {site_name}", 'advanced-classifieds-and-directory-pro' )
			),
			'acadp_permalink_settings' => array(
				'listing' => 'acadp_listings'	
			),
			'acadp_socialshare_settings' => array(				
				'services' => array( 'facebook', 'twitter', 'gplus' ),
				'pages'    => array( 'listing' )
			),
			'acadp_map_settings' => array(
				'api_key'    => '',
				'zoom_level' => 5
			),
			'acadp_recaptcha_settings' => array(
				'forms'      => array( 'registration', 'listing', 'contact' ),
				'site_key'   => '',
				'secret_key' => ''
			),
			'acadp_terms_of_agreement' => array(
				'show_agree_to_terms' => 0,
				'agree_label'         => __( 'I agree to the terms and conditions', 'advanced-classifieds-and-directory-pro' ),
				'agree_text'          => ''
			)
		);
		
		foreach( $options as $option_name => $defaults ) {
			if( false == get_option( $option_name ) ) {			
				if( 'acadp_page_settings' == $option_name ) $defaults = acadp_insert_custom_pages();				
        		add_option( $option_name, apply_filters( $option_name.'_defaults', $defaults ) );						
    		}
		}
		
		// Add custom ACADP capabilities
		if( ! get_option( 'acadp_version' ) ) {
			$roles = new ACADP_Roles;
			$roles->add_caps();
		}
		
		// Insert plugin version
		add_option( 'acadp_version', ACADP_VERSION_NUM );
		
	}

}

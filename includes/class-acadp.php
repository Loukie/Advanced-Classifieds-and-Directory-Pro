<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
 * ACADP Class
 *
 * @since    1.0.0
 */
class ACADP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *	 
	 * @since    1.0.0
	 * @access   protected
	 * @var      ACADP_Loader
	 */
	protected $loader;

	/**
	 * Get things started.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->set_meta_caps();
		$this->set_cron();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-loader.php';
		
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-i18n.php';
		
		/**
		 * The class responsible for enabling / disabling listings in parent categories.
		 */
		require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-walker-category-dropdown.php';
		
		/**
		 * The class responsible for the role creation and assignment of capabilities
		 * for those roles.
		 */
		require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-roles.php';
		
		// The class responsible for defining scheduled events
		require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-cron.php';
		
		// The file that holds the general helper functions
		require_once ACADP_PLUGIN_DIR . 'includes/functions-acadp-general.php';
		
		// The file that holds the functions those generate html elements
		require_once ACADP_PLUGIN_DIR . 'includes/functions-acadp-html.php';
		
		// The file that holds the functions those generate ACADP page permalinks
		require_once ACADP_PLUGIN_DIR . 'includes/functions-acadp-permalinks.php';
		
		// The file that holds the email related functions
		require_once ACADP_PLUGIN_DIR . 'includes/functions-acadp-email.php';
		
		// The file that holds the deprecated functions
		require_once ACADP_PLUGIN_DIR . 'includes/functions-acadp-deprecated.php';

		// The classes responsible for defining actions those occur in the admin area
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin.php';
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin-listings.php';
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin-locations.php';
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin-categories.php';		
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin-fields.php';
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin-payments.php';
		require_once ACADP_PLUGIN_DIR . 'admin/class-acadp-admin-settings.php';

		/**
		 * The classes responsible for defining actions those occur in the public-facing
		 * side of the site.
		 */
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public.php';
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-locations.php';	
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-categories.php';		
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-listings.php';
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-search.php';	
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-listing.php';
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-registration.php';
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-user.php';
		require_once ACADP_PLUGIN_DIR . 'public/class-acadp-public-payments.php';
		
		// Create an instance of the loader
		$this->loader = new ACADP_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ACADP_i18n();
		
		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		// Hooks common to all admin pages
		$plugin_admin = new ACADP_Admin();

		$this->loader->add_action( 'wp_loaded', $plugin_admin, 'manage_upgrades' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
		$this->loader->add_action( 'wp_ajax_acadp_dismiss_admin_notice', $plugin_admin, 'ajax_callback_dismiss_admin_notice' );
		$this->loader->add_action( 'wp_ajax_acadp_delete_attachment', $plugin_admin, 'ajax_callback_delete_attachment' );
		
		// Hooks specific to the custom post type "acadp_listings"
		$plugin_admin_listings = new ACADP_Admin_Listings();
		
		$this->loader->add_action( 'init', $plugin_admin_listings, 'register_custom_post_type' );
		
		if( is_admin() ) {
			$this->loader->add_action( 'admin_menu', $plugin_admin_listings, 'remove_meta_boxes' );
			$this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin_listings, 'post_submitbox_misc_actions' );
			$this->loader->add_action( 'add_meta_boxes', $plugin_admin_listings, 'add_meta_boxes' );
			$this->loader->add_action( 'wp_ajax_acadp_custom_fields_listings', $plugin_admin_listings, 'ajax_callback_custom_fields', 10, 2 );
			$this->loader->add_action( 'save_post', $plugin_admin_listings, 'save_meta_data', 10, 2 );		
			$this->loader->add_action( 'transition_post_status', $plugin_admin_listings, 'transition_post_status', 10, 3 );
			$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_listings, 'restrict_manage_posts' );
			$this->loader->add_action( 'manage_acadp_listings_posts_custom_column', $plugin_admin_listings, 'custom_column_content', 10, 2 );	
			$this->loader->add_action( 'before_delete_post', $plugin_admin_listings, 'before_delete_post' );	
		
			$this->loader->add_filter( 'parse_query', $plugin_admin_listings, 'parse_query' );
			$this->loader->add_filter( 'manage_edit-acadp_listings_columns', $plugin_admin_listings, 'get_columns' );
			$this->loader->add_filter( 'post_row_actions', $plugin_admin_listings, 'remove_row_actions', 10, 2 );
		} 
		
		// Hooks specific to the custom taxonomy "acadp_locations"
		$plugin_admin_locations = new ACADP_Admin_Locations();
		
		$this->loader->add_action( 'init', $plugin_admin_locations, 'register_custom_taxonomy' );
		
		$this->loader->add_filter( "manage_edit-acadp_locations_columns", $plugin_admin_locations, 'get_columns' );
		$this->loader->add_filter( "manage_edit-acadp_locations_sortable_columns", $plugin_admin_locations, 'get_columns' );
		$this->loader->add_filter( "manage_acadp_locations_custom_column", $plugin_admin_locations, 'custom_column_content', 10, 3 );
		
		// Hooks specific to the custom taxonomy "acadp_categories"
		$plugin_admin_categories = new ACADP_Admin_Categories();
		
		$this->loader->add_action( 'init', $plugin_admin_categories, 'register_custom_taxonomy' );
		$this->loader->add_action( 'acadp_categories_add_form_fields', $plugin_admin_categories, 'add_image_field' );
		$this->loader->add_action( 'created_acadp_categories', $plugin_admin_categories, 'save_image_field' );
		$this->loader->add_action( 'acadp_categories_edit_form_fields', $plugin_admin_categories, 'edit_image_field' );
		$this->loader->add_action( 'edited_acadp_categories', $plugin_admin_categories, 'update_image_field' );
		
		$this->loader->add_filter( "manage_edit-acadp_categories_columns", $plugin_admin_categories, 'get_columns' );
		$this->loader->add_filter( "manage_edit-acadp_categories_sortable_columns", $plugin_admin_categories, 'get_columns' );
		$this->loader->add_filter( "manage_acadp_categories_custom_column", $plugin_admin_categories, 'custom_column_content', 10, 3 );	
		
		// Hooks specific to the custom post type "acadp_fields"
		$plugin_admin_fields = new ACADP_Admin_Fields();
		
		$this->loader->add_action( 'init', $plugin_admin_fields, 'register_custom_post_type' );
		
		if( is_admin() ) {
			$this->loader->add_action( 'add_meta_boxes', $plugin_admin_fields, 'add_meta_boxes' );
			$this->loader->add_action( 'save_post', $plugin_admin_fields, 'save_meta_data', 10, 2 );
			$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_fields, 'restrict_manage_posts' );
			$this->loader->add_action( 'parse_tax_query', $plugin_admin_fields, 'parse_tax_query' );
			$this->loader->add_action( 'pre_get_posts', $plugin_admin_fields, 'custom_order' );
			$this->loader->add_action( 'manage_acadp_fields_posts_custom_column', $plugin_admin_fields, 'custom_column_content', 10, 2 );
			
			$this->loader->add_filter( 'parse_query', $plugin_admin_fields, 'parse_query' );
			$this->loader->add_filter( 'manage_edit-acadp_fields_columns', $plugin_admin_fields, 'get_columns' );
			$this->loader->add_filter( 'post_row_actions', $plugin_admin_fields, 'remove_row_actions', 10, 2 );
		}
		
		// Hooks specific to the custom post type "acadp_payments"
		$plugin_admin_payments = new ACADP_Admin_Payments();
		
		$this->loader->add_action( 'init', $plugin_admin_payments, 'register_custom_post_type' );
		
		if( is_admin() ) {
			$this->loader->add_action( 'admin_footer-edit.php', $plugin_admin_payments, 'admin_footer_edit' );
			$this->loader->add_action( 'restrict_manage_posts', $plugin_admin_payments, 'restrict_manage_posts' );
			$this->loader->add_action( 'manage_acadp_payments_posts_custom_column', $plugin_admin_payments, 'custom_column_content', 10, 2 );
			$this->loader->add_action( 'load-edit.php', $plugin_admin_payments, 'load_edit' );
			$this->loader->add_action( 'admin_notices', $plugin_admin_payments, 'admin_notices' );
			
			$this->loader->add_filter( 'parse_query', $plugin_admin_payments, 'parse_query' );
			$this->loader->add_filter( 'manage_edit-acadp_payments_columns', $plugin_admin_payments, 'get_columns' );
			$this->loader->add_filter( 'manage_edit-acadp_payments_sortable_columns', $plugin_admin_payments, 'get_sortable_columns' );
		}
			
		// Hooks specific to the 'settings' page of the plugin
		$plugin_admin_settings = new ACADP_Admin_Settings();
		
		if( is_admin() ) {
			$this->loader->add_action( 'admin_menu', $plugin_admin_settings, 'add_settings_menu' );
			$this->loader->add_action( 'admin_init', $plugin_admin_settings, 'initialize_settings' );
		}
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private	 
	 */
	private function define_public_hooks() {

		// Hooks common to all public pages
		$plugin_public = new ACADP_Public();

		$this->loader->add_action( 'template_redirect', $plugin_public, 'template_redirect' );
		$this->loader->add_action( 'init', $plugin_public, 'output_buffer' );
		$this->loader->add_action( 'init', $plugin_public, 'add_rewrites' );
		$this->loader->add_action( 'wp_loaded', $plugin_public, 'maybe_flush_rules' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_enqueue_scripts' );
		$this->loader->add_action( 'wp_print_scripts', $plugin_public, 'dequeue_scripts', 100 );
		$this->loader->add_action( 'wp_title', $plugin_public, 'wp_title', 99, 3 );
		$this->loader->add_action( 'wp_head', $plugin_public, 'og_metatags' );
		$this->loader->add_action( 'wp_ajax_acadp_public_dropdown_terms', $plugin_public, 'ajax_callback_dropdown_terms' );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_dropdown_terms', $plugin_public, 'ajax_callback_dropdown_terms' );
		
		$this->loader->add_filter( 'force_ssl', $plugin_public, 'force_ssl_https', 10, 2 );
		$this->loader->add_filter( 'pre_get_document_title', $plugin_public, 'pre_get_document_title', 999 );
		$this->loader->add_filter( 'document_title_parts', $plugin_public, 'document_title_parts' );
		$this->loader->add_filter( 'the_title', $plugin_public, 'the_title', 99 );
		$this->loader->add_filter( 'single_post_title', $plugin_public, 'the_title', 99 );
		
		// Hooks specific to the locations page
		$plugin_public_locations = new ACADP_Public_Locations();
		
		// Hooks specific to the categories page
		$plugin_public_categories = new ACADP_Public_Categories();

		// Hooks specific to the listings page
		$plugin_public_listings = new ACADP_Public_Listings();
		
		// Hooks specific to the search page
		$plugin_public_search = new ACADP_Public_Search();
		
		$this->loader->add_action( 'wp_ajax_acadp_custom_fields_search', $plugin_public_search, 'ajax_callback_custom_fields', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_custom_fields_search', $plugin_public_search, 'ajax_callback_custom_fields', 10, 2 );
		
		// Hooks specific to the listing detail page
		$plugin_public_listing = new ACADP_Public_Listing();
		
		$this->loader->add_action( 'the_content', $plugin_public_listing, 'the_content', 20 );
		$this->loader->add_action( 'wp_ajax_acadp_public_add_remove_favorites', $plugin_public_listing, 'ajax_callback_add_remove_favorites' );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_add_remove_favorites', $plugin_public_listing, 'ajax_callback_add_remove_favorites' );
		$this->loader->add_action( 'wp_ajax_acadp_public_report_abuse', $plugin_public_listing, 'ajax_callback_report_abuse' );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_report_abuse', $plugin_public_listing, 'ajax_callback_report_abuse' );
		$this->loader->add_action( 'wp_ajax_acadp_public_send_contact_email', $plugin_public_listing, 'ajax_callback_send_contact_email' );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_send_contact_email', $plugin_public_listing, 'ajax_callback_send_contact_email' );
		
		$this->loader->add_filter( 'post_thumbnail_html', $plugin_public_listing, 'post_thumbnail_html' );
		
		// Hooks specific to user registration, login, password reset
		if( acadp_registration_enabled() ) {
			$plugin_public_registration = new ACADP_Public_Registration();
			
			//$this->loader->add_action( 'login_form_login', $plugin_public_registration, 'redirect_to_custom_login' );
			$this->loader->add_action( 'wp_logout', $plugin_public_registration, 'redirect_after_logout' );
			$this->loader->add_action( 'login_form_register', $plugin_public_registration, 'redirect_to_custom_register' );
			$this->loader->add_action( 'login_form_lostpassword', $plugin_public_registration, 'redirect_to_custom_lostpassword' );
			$this->loader->add_action( 'login_form_rp', $plugin_public_registration, 'redirect_to_custom_password_reset' );
			$this->loader->add_action( 'login_form_resetpass', $plugin_public_registration, 'redirect_to_custom_password_reset' );
			
			$this->loader->add_action( 'init', $plugin_public_registration, 'manage_actions' );
			$this->loader->add_action( 'login_form_register', $plugin_public_registration, 'do_register_user' );
			$this->loader->add_action( 'login_form_lostpassword', $plugin_public_registration, 'do_forgot_password' );
			$this->loader->add_action( 'login_form_rp', $plugin_public_registration, 'do_password_reset' );
			$this->loader->add_action( 'login_form_resetpass', $plugin_public_registration, 'do_password_reset' );
			
			$this->loader->add_filter( 'authenticate', $plugin_public_registration, 'maybe_redirect_at_authenticate', 101, 3 );
			$this->loader->add_filter( 'login_redirect', $plugin_public_registration, 'redirect_after_login', 10, 3 );
			$this->loader->add_filter( 'retrieve_password_message', $plugin_public_registration, 'replace_retrieve_password_message', 10, 4 );
		}
		
		// Hooks specific to the user pages
		$plugin_public_user = new ACADP_Public_User();
		
		$this->loader->add_action( 'init', $plugin_public_user, 'manage_actions' );
		$this->loader->add_action( 'parse_request', $plugin_public_user, 'parse_request' );
		$this->loader->add_action( 'wp_ajax_acadp_public_custom_fields_listings', $plugin_public_user, 'ajax_callback_custom_fields', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_custom_fields_listings', $plugin_public_user, 'ajax_callback_custom_fields', 10, 2 );
		$this->loader->add_action( 'wp_ajax_acadp_public_image_upload', $plugin_public_user, 'ajax_callback_image_upload', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_image_upload', $plugin_public_user, 'ajax_callback_image_upload', 10, 2 );
		$this->loader->add_action( 'wp_ajax_acadp_public_delete_attachment_listings', $plugin_public_user, 'ajax_callback_delete_attachment' );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_public_delete_attachment_listings', $plugin_public_user, 'ajax_callback_delete_attachment' );
		
		// Hooks specific to the payment system
		$plugin_public_payments = new ACADP_Public_Payments();
		
		$this->loader->add_action( 'wp_ajax_acadp_checkout_format_total_amount', $plugin_public_payments, 'ajax_callback_format_total_amount' );
		$this->loader->add_action( 'wp_ajax_nopriv_acadp_checkout_format_total_amount', $plugin_public_payments, 'ajax_callback_format_total_amount' );

	}
	
	/**
	 * Map meta caps to primitive caps
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_meta_caps() {

		$plugin_roles = new ACADP_Roles();

		$this->loader->add_filter( 'map_meta_cap', $plugin_roles, 'meta_caps', 10, 4 );

	}
	
	/**
	 * Define CRON Jobs for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private	 
	 */
	private function set_cron() {
	
		$plugin_cron = new ACADP_Cron();
		
		$this->loader->add_action( 'wp', $plugin_cron, 'schedule_events' );
		$this->loader->add_action( 'acadp_hourly_scheduled_events', $plugin_cron, 'hourly_scheduled_events' );
	
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 * @access   public	 
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return   ACADP_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}

<?php

/**
 * Settings
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/admin
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */
 
// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Admin_Settings Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Admin_Settings {

	/**
	 * Array stores all tabs registered using this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $tabs    Tab names.
	 */
	private $tabs = array();
	
	/**
	 * The tab name that is currently active.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $active_tab    Active Tab name.
	 */
	private $active_tab;
	
	/**
	 * Initialize the class.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

	}
	
	/**
	 * Add a settings menu for the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_settings_menu() {
	
		add_submenu_page(
			'edit.php?post_type=acadp_listings',
			__( 'Settings', 'advanced-classifieds-and-directory-pro' ),
			__( 'Settings', 'advanced-classifieds-and-directory-pro' ),
			'manage_acadp_options',
			'acadp_settings',
			array( $this, 'display_settings_form' )
		);
	
	}
	
	/**
	 * Display settings form.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_settings_form() {
	
		require_once ACADP_PLUGIN_DIR . 'admin/partials/settings/acadp-admin-settings-display.php';
	
	}
	
	/**
	 * Initialize settings.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function initialize_settings() { 
		
		$core_tabs = array(
			'general'  => __( 'General', 'advanced-classifieds-and-directory-pro' ),
			'pages'    => __( 'Pages', 'advanced-classifieds-and-directory-pro' ),
			'gateways' => __( 'Payment Gateways', 'advanced-classifieds-and-directory-pro' ),
			'monetize' => __( 'Monetize', 'advanced-classifieds-and-directory-pro' ),
			'email'    => __( 'Email', 'advanced-classifieds-and-directory-pro' ),
			'misc'     => __( 'Misc', 'advanced-classifieds-and-directory-pro' )
		);
		
		// Hook to register custom tabs
		$this->tabs = apply_filters( 'acadp_register_settings_tabs', $core_tabs );
		
		// Find the active tab
		$this->active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->tabs ) ?  $_GET['tab'] : 'general';
		
		// Register core options
		foreach( $core_tabs as $page_hook => $title ) {
			call_user_func( array( $this, 'register_'.$page_hook.'_settings' ), 'acadp_'.$page_hook.'_settings' );
			
			// Hook for developers to register custom settings
			do_action( 'acadp_register_'.$page_hook.'_settings', 'acadp_'.$page_hook.'_settings' );
		}		
	
	}	
	
	/**
	 * Register the Sections, Fields, and Settings for "General Settings" tab.
	 * 
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $page_hook    Admin page on which to add this section of options.
	 */
	public function register_general_settings( $page_hook ) {
	
		$option_group = $page_hook;
		
		// Section : "acadp_general_settings_section"
		add_settings_section(
    		'acadp_general_settings_section',
    		__( 'General settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_general_settings[load_bootstrap][]',
			__( 'Bootstrap options', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'load_bootstrap',
				'options'     => array(
					'css'        => __( 'Include bootstrap CSS', 'advanced-classifieds-and-directory-pro' ),
					'javascript' => __( 'Include bootstrap javascript libraries', 'advanced-classifieds-and-directory-pro' )
				),
				'description' => __( 'This plugin uses bootstrap 3. Disable these options if your theme already include them.', 'advanced-classifieds-and-directory-pro' )
			)
		);	
		
		add_settings_field(	
			'acadp_general_settings[listing_duration]',						
			__( 'Listing duration (in days)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'listing_duration',
				'description' => __( 'Use a value of "0" to keep a listing alive indefinitely.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_location]',						
			__( 'Enable locations', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_location',
				'field_label' => __( 'Allow users to enter listing "Contact Details"', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(
			'acadp_general_settings[base_location]',
			__( 'Base location', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_locations' ),
			$page_hook,
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'base_location',
				'description' => __( 'Where does your directory operate from? (This list is populated using the data from "Locations" menu)', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_general_settings[default_location]',
			__( 'Default location', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_locations' ),
			$page_hook,
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'default_location',
				'description' => __( 'This is the location selected by default when adding a new listing. (This list is populated using the data from "Locations" menu)', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(	
			'acadp_general_settings[disable_parent_categories]',						
			__( 'Prevent listings from being posted to top level categories?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'disable_parent_categories',
				'field_label' => ''
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[text_editor]',						
			__( 'Text Editor', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'text_editor',
				'options'     => array(
					'wp_editor' => __( 'WP Editor', 'advanced-classifieds-and-directory-pro' ),
					'textarea'  => __( 'TextArea', 'advanced-classifieds-and-directory-pro' )
				),
				'description' => __( 'Select the Text Editor you like to have in the front-end listing submission form.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_price]',						
			__( 'Enable price', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_price',
				'field_label' => __( 'Allow users to enter price amount for their listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_images]',						
			__( 'Enable images', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_images',
				'field_label' => __( 'Allow users to upload images for their listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[maximum_images_per_listing]',						
			__( 'Maximum images allowed per listing', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'maximum_images_per_listing'
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_video]',						
			__( 'Enable videos', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_video',
				'field_label' => __( 'Allow users to add videos for their listings', 'advanced-classifieds-and-directory-pro' ),
				'description' => __( 'Only YouTube &  Vimeo URLs.', 'advanced-classifieds-and-directory-pro' )
			)			
		);	
		
		add_settings_field(	
			'acadp_general_settings[has_map]',						
			__( 'Enable map', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_map',
				'field_label' => __( 'Allow users to add map for their listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);	
		
		add_settings_field(	
			'acadp_general_settings[has_contact_form]',						
			__( 'Contact form', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_contact_form',
				'field_label' => __( 'Allows visitors to contact listing authors privately. Authors will receive the messages via email.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[contact_form_require_login]',						
			__( 'Require login for using the contact form?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'contact_form_require_login',
				'field_label' => ''
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_comment_form]',						
			__( 'Comment form', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_comment_form',
				'field_label' => __( 'Allow visitors to discuss listings using the standard WordPress comment form. Comments are public.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_report_abuse]',						
			__( 'Report abuse', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_report_abuse',
				'field_label' => __( 'Check this to enable Report abuse', 'advanced-classifieds-and-directory-pro' )
			)			
		);
				
		add_settings_field(	
			'acadp_general_settings[has_favourites]',						
			__( 'Add to favourites', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_favourites',
				'field_label' => __( 'Check this to enable favourite Listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[new_listing_status]',						
			__( 'Default new listing status', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'new_listing_status',
				'options'     => array(
					'publish' => __( 'Publish', 'advanced-classifieds-and-directory-pro' ),
					'pending' => __( 'Pending', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[edit_listing_status]',						
			__( 'Edit listing status', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'edit_listing_status',
				'options'     => array(
					'publish' => __( 'Publish', 'advanced-classifieds-and-directory-pro' ),
					'pending' => __( 'Pending', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[show_new_tag]',						
			__( 'Show "New" tag', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'show_new_tag',
				'field_label' => __( 'Check this to show "New" label on listings (only if applicable)', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[new_listing_threshold]',						
			__( 'New listing threshold (in days)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'new_listing_threshold',
				'description' => __( 'Enter the number of days the listing will be tagged as "New" from the day it is published.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[new_listing_label]',						
			__( 'Label text for new listings', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'new_listing_label',
				'description' => __( 'Enter the text you want to use inside the "New" tag.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[show_popular_tag]',						
			__( 'Show "Popular" tag', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'show_popular_tag',
				'field_label' => __( 'Check this to show "Popular" label on listings (only if applicable)', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[popular_listing_threshold]',						
			__( 'Popular listing threshold (in views count)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'popular_listing_threshold',
				'description' => __( 'Enter the minimum number of views required for a listing to be tagged as "Popular".', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[popular_listing_label]',						
			__( 'Label text for popular listings', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'popular_listing_label',
				'description' => __( 'Enter the text you want to use inside the "Popular" tag.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[display_options][]',						
			__( 'Show / Hide (in listing detail page)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_multicheck' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'display_options',
				'options'     => array(
					'category' => __( 'Category name', 'advanced-classifieds-and-directory-pro' ),
					'date'     => __( 'Date added', 'advanced-classifieds-and-directory-pro' ),					
					'user'     => __( 'Listing owner name', 'advanced-classifieds-and-directory-pro' ),
					'views'    => __( 'Views count', 'advanced-classifieds-and-directory-pro' ),
					'category_desc'	=> __( 'Category description', 'advanced-classifieds-and-directory-pro' )
				)				
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[has_listing_renewal]',						
			__( 'Turn on listing renewal option?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'has_listing_renewal',
				'field_label' => ''
			)			
		);
		
		add_settings_field(	
			'acadp_general_settings[delete_expired_listings]',						
			__( 'Delete expired Listings (in days)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_general_settings_section',
			array(
				'option_name' => 'acadp_general_settings',
				'field_name'  => 'delete_expired_listings',
				'description' => __( 'If you have the renewal option enabled, this will be the number of days after the "Renewal Reminder" email was sent.', 'advanced-classifieds-and-directory-pro' )
			)			
		);

		register_setting(
        	$option_group,
        	'acadp_general_settings',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_listings_settings_section"
		add_settings_section(
    		'acadp_listings_settings_section',
    		__( 'Listings / Search Results page', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);

		add_settings_field(	
			'acadp_listings_settings[view_options][]',						
			__( 'Display options', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_multicheck' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'view_options',
				'options'     => array(
					'list' => __( 'List view', 'advanced-classifieds-and-directory-pro' ),
					'grid' => __( 'Grid view', 'advanced-classifieds-and-directory-pro' ),
					'map'  => __( 'Map view', 'advanced-classifieds-and-directory-pro' )
				),
				'description' => __( 'You must select at least one view.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_listings_settings[default_view]',						
			__( 'Default view', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'default_view',
				'options'     => array(
					'list' => __( 'List view', 'advanced-classifieds-and-directory-pro' ),
					'grid' => __( 'Grid view', 'advanced-classifieds-and-directory-pro' ),
					'map'  => __( 'Map view', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(
			'acadp_listings_settings[include_results_from][]',
			__( 'Include results from', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'include_results_from',
				'options'     => array(
					'child_categories' => __( 'Child categories', 'advanced-classifieds-and-directory-pro' ),
					'child_locations'  => __( 'Child locations', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
		
		add_settings_field(	
			'acadp_listings_settings[orderby]',						
			__( 'Order listings by', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'orderby',
				'options'     => array(
					'title' => __( 'Title', 'advanced-classifieds-and-directory-pro' ),
					'date'  => __( 'Date posted', 'advanced-classifieds-and-directory-pro' ),
					'price' => __( 'Price', 'advanced-classifieds-and-directory-pro' ),
					'views' => __( 'Views count', 'advanced-classifieds-and-directory-pro' )
				 )
			)			
		);
		
		add_settings_field(	
			'acadp_listings_settings[order]',						
			__( 'Sort listings by', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'order',
				'options'     => array(
					'asc'  => __( 'Ascending', 'advanced-classifieds-and-directory-pro' ),
					'desc' => __( 'Descending', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_listings_settings[columns]',						
			__( 'Number of columns', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'columns',
				'description' => __( 'Enter the number of columns you like to have in the "Grid" view.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_listings_settings[listings_per_page]',						
			__( 'Listings per page', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'listings_per_page',
				'description' => __( 'Number of listings to show per page. Use a value of "0" to show all listings.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(
			'acadp_listings_settings[display_in_header][]',
			__( 'Show / Hide (in header)', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'display_in_header',
				'options'     => array(
					'listings_count'   => __( 'Listings count', 'advanced-classifieds-and-directory-pro' ),
					'views_selector'   => __( 'Views selector', 'advanced-classifieds-and-directory-pro' ),
					'orderby_dropdown' => __( '"Sort by" dropdown', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
		
		add_settings_field(
			'acadp_listings_settings[display_in_listing][]',
			__( 'Show / Hide (in each listing)', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'display_in_listing',
				'options'     => array(
					'category' => __( 'Category name', 'advanced-classifieds-and-directory-pro' ),
					'location' => __( 'Location name', 'advanced-classifieds-and-directory-pro' ), 
					'price'    => __( 'Item price (only if applicable)', 'advanced-classifieds-and-directory-pro' ),					
					'date'     => __( 'Date added', 'advanced-classifieds-and-directory-pro' ),					
					'user'     => __( 'Listing owner name', 'advanced-classifieds-and-directory-pro' ),
					'views'    => __( 'Views count', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
		
		add_settings_field(	
			'acadp_listings_settings[excerpt_length]',						
			__( 'Description length', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_listings_settings_section',
			array(
				'option_name' => 'acadp_listings_settings',
				'field_name'  => 'excerpt_length',
				'description' => __( 'Number of characters.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		register_setting(
        	$option_group,
        	'acadp_listings_settings',
			array( $this, 'sanitize_options' )
    	);	
		
		// Section : "acadp_locations_settings_section"
		add_settings_section(
    		'acadp_locations_settings_section',
    		__( 'Locations page', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);		
		
		add_settings_field(	
			'acadp_locations_settings[columns]',						
			__( 'Number of columns', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_locations_settings_section',
			array(
				'option_name' => 'acadp_locations_settings',
				'field_name'  => 'columns',
				'description' => __( 'Enter the number of columns you like to have in your locations page.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_locations_settings[depth]',						
			__( 'Depth', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_locations_settings_section',
			array(
				'option_name' => 'acadp_locations_settings',
				'field_name'  => 'depth',
				'description' => __( 'Enter the maximum number of location sub-levels to show.', 'advanced-classifieds-and-directory-pro' )
			)			
		);

		add_settings_field(	
			'acadp_locations_settings[orderby]',						
			__( 'Order locations by', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_locations_settings_section',
			array(
				'option_name' => 'acadp_locations_settings',
				'field_name'  => 'orderby',
				'options'     => array(
					'id'    => __( 'ID', 'advanced-classifieds-and-directory-pro' ),
					'count' => __( 'Count', 'advanced-classifieds-and-directory-pro' ),
					'name'  => __( 'Name', 'advanced-classifieds-and-directory-pro' ),
					'slug'  => __( 'Slug', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_locations_settings[order]',						
			__( 'Sort locations by', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_locations_settings_section',
			array(
				'option_name' => 'acadp_locations_settings',
				'field_name'  => 'order',
				'options'     => array(
					'asc'  => __( 'Ascending', 'advanced-classifieds-and-directory-pro' ),
					'desc' => __( 'Descending', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_locations_settings[show_count]',						
			__( 'Show listings count?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_locations_settings_section',
			array(
				'option_name' => 'acadp_locations_settings',
				'field_name'  => 'show_count',
				'field_label' => __( 'Check this to show the listings count next to the location name', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_locations_settings[hide_empty]',						
			__( 'Hide empty locations?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_locations_settings_section',
			array(
				'option_name' => 'acadp_locations_settings',
				'field_name'  => 'hide_empty',
				'field_label' => __( 'Check this to hide locations with no listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		register_setting(
        	$option_group,
        	'acadp_locations_settings',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_categories_settings_section"
		add_settings_section(
    		'acadp_categories_settings_section',
    		__( 'Categories page', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);		
		
		add_settings_field(	
			'acadp_categories_settings[view]',						
			__( 'Display as', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'view',
				'options'     => array(
					'image_grid' => __( 'Thumbnail grid', 'advanced-classifieds-and-directory-pro' ),
					'text_list'  => __( 'Text-only menu items', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_categories_settings[columns]',						
			__( 'Number of columns', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'columns',
				'description' => __( 'Enter the number of columns you like to have in your categories page.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_categories_settings[depth]',						
			__( 'Depth', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'depth',
				'description' => __( 'Enter the maximum number of category sub-levels to show in the "Text-only Menu Items" view.', 'advanced-classifieds-and-directory-pro' )
			)			
		);

		add_settings_field(	
			'acadp_categories_settings[orderby]',						
			__( 'Order categories by', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'orderby',
				'options'     => array(
					'id'    => __( 'ID', 'advanced-classifieds-and-directory-pro' ),
					'count' => __( 'Count', 'advanced-classifieds-and-directory-pro' ),
					'name'  => __( 'Name', 'advanced-classifieds-and-directory-pro' ),
					'slug'  => __( 'Slug', 'advanced-classifieds-and-directory-pro' )				
				)
			)			
		);
		
		add_settings_field(	
			'acadp_categories_settings[order]',						
			__( 'Sort categories by', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_select' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'order',
				'options'     => array(
					'asc'  => __( 'Ascending', 'advanced-classifieds-and-directory-pro' ),
					'desc' => __( 'Descending', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_categories_settings[show_count]',						
			__( 'Show Listings count?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'show_count',
				'field_label' => __( 'Check this to show the listings count next to the category name', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_categories_settings[hide_empty]',						
			__( 'Hide empty categories?', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_categories_settings_section',
			array(
				'option_name' => 'acadp_categories_settings',
				'field_name'  => 'hide_empty',
				'field_label' => __( 'Check this to hide categories with no listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		register_setting(
        	$option_group,
        	'acadp_categories_settings',
			array( $this, 'sanitize_options' )
    	);		
		
		// Section : "acadp_registration_settings_section"
		add_settings_section(
    		'acadp_registration_settings_section',
    		__( 'User Login / Registration', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(	
			'acadp_registration_settings[engine]',						
			__( 'Enable Login / Registraion', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_radio' ),	
			$page_hook,	
			'acadp_registration_settings_section',
			array(
				'option_name' => 'acadp_registration_settings',
				'field_name'  => 'engine',
				'options'     => array(
					'acadp'  => __( 'Check this to allow the plugin to take care of user Login / Registration.', 'advanced-classifieds-and-directory-pro' ),
					'others' => __( 'Check this if you already have a registration system. You will need to add the Login / Registration / Forgot Password Page URLs of your registration system in the fields below to get this work. Checking this option and leaving the following fields empty will simply enable the standard WordPress Login / Registration mechanism.', 'advanced-classifieds-and-directory-pro' )
				)
			)			
		);
		
		add_settings_field(	
			'acadp_registration_settings[custom_login]',						
			__( 'Custom Login URL', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_registration_settings_section',
			array(
				'option_name' => 'acadp_registration_settings',
				'field_name'  => 'custom_login',
				'description' => __( 'Optional. Add your custom Login Page URL or a [shortcode] that renders the Login form. Leave this field empty to add the standard WordPress Login form.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_registration_settings[custom_register]',						
			__( 'Custom Registration URL', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_registration_settings_section',
			array(
				'option_name' => 'acadp_registration_settings',
				'field_name'  => 'custom_register',
				'description' => __( 'Optional. Add your custom Registration Page URL. Leave this field empty to use the standard WordPress Registration URL.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_registration_settings[custom_forgot_password]',						
			__( 'Custom Forgot Password URL', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_registration_settings_section',
			array(
				'option_name' => 'acadp_registration_settings',
				'field_name'  => 'custom_forgot_password',
				'description' => __( 'Optional. Add your custom Forgot Password Page URL. Leave this field empty to use the standard WordPress Forgot Password URL.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		register_setting(
        	$option_group,
        	'acadp_registration_settings',
			array( $this, 'sanitize_options' )
    	);	
		
		// Section : "acadp_currency_settings_section"
		add_settings_section(
    		'acadp_currency_settings_section',
    		__( 'Currency settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_currency_settings[currency]',
			__( 'Currency', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_currency_settings_section',
			array(
				'option_name' => 'acadp_currency_settings',
				'field_name'  => 'currency',
				'description' => __( 'Enter your currency.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_currency_settings[position]',
			__( 'Currency position', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_select' ),
			$page_hook,
			'acadp_currency_settings_section',
			array(
				'option_name' => 'acadp_currency_settings',
				'field_name'  => 'position',
				'options'     => array(
					'before' => __( 'Before - $10', 'advanced-classifieds-and-directory-pro' ),
					'after'  => __( 'After - 10$', 'advanced-classifieds-and-directory-pro' )
				),
				'description' => __( 'Choose the location of the currency sign.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_currency_settings[thousands_separator]',
			__( 'Thousands separator', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_currency_settings_section',
			array(
				'option_name' => 'acadp_currency_settings',
				'field_name'  => 'thousands_separator',
				'description' => __( 'The symbol (usually , or .) to separate thousands.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_currency_settings[decimal_separator]',
			__( 'Decimal separator', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_currency_settings_section',
			array(
				'option_name' => 'acadp_currency_settings',
				'field_name'  => 'decimal_separator',
				'description' => __( 'The symbol (usually , or .) to separate decimal points.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_currency_settings',
			array( $this, 'sanitize_options' )
    	);
	
	}
	
	/**
	 * Register the Sections, Fields, and Settings for "Pages Settings" tab.
	 * 
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $page_hook    Admin page on which to add this section of options.
	 */
	public function register_pages_settings( $page_hook ) {
	
		$option_group = $page_hook;
		
		// Section : "acadp_page_settings_section"
		add_settings_section(
    		'acadp_page_settings_section',
    		__( 'Page settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_page_settings[listings]',
			__( 'Listings page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'listings',
				'options'     => get_pages(),
				'description' => __( 'This is the page where all the active listings are displayed. The [acadp_listings] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[locations]',
			__( 'Locations page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'locations',
				'options'     => get_pages(),
				'description' => __( 'This is the page where all the locations are displayed. The [acadp_locations] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[location]',
			__( 'Single location page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'location',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the listings from a particular location is displayed. The [acadp_location] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[categories]',
			__( 'Categories page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'categories',
				'options'     => get_pages(),
				'description' => __( 'This is the page where all the categories are displayed. The [acadp_categories] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[category]',
			__( 'Single category page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'category',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the listings from a particular category is displayed. The [acadp_category] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);		
		
		add_settings_field(
			'acadp_page_settings[search]',
			__( 'Search page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'search',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the search results are displayed. The [acadp_search] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[user_listings]',
			__( 'User listings page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'user_listings',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the listings from a particular user is displayed. The [acadp_user_listings] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[user_dashboard]',
			__( 'User dashboard page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'user_dashboard',
				'options'     => get_pages(),
				'description' => __( 'This is the user home page where the current user can add, edit listings, manage favourite listings, view payment history, etc... The [acadp_user_dashboard] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[listing_form]',
			__( 'Listing form page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'listing_form',
				'options'     => get_pages(),
				'description' => __( 'This is the listing form page used to add or edit listing details. The [acadp_listing_form] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[manage_listings]',
			__( 'Manage listings page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'manage_listings',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the current user can add a new listing or modify, delete their existing listings. The [acadp_manage_listings] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[favourite_listings]',
			__( 'Favourite listings page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'favourite_listings',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the current user\'s favourite listings are displayed. The [acadp_favourite_listings] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[checkout]',
			__( 'Checkout page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'checkout',
				'options'     => get_pages(),
				'description' => __( 'This is the checkout page where users will complete their purchases. The [acadp_checkout] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[payment_receipt]',
			__( 'Payment receipt page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'payment_receipt',
				'options'     => get_pages(),
				'description' => __( 'This is the page users are sent to after completing their payments. The [acadp_payment_receipt] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[payment_failure]',
			__( 'Failed Transaction Page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'payment_failure',
				'options'     => get_pages(),
				'description' => __( 'This is the page users are sent to if their transaction is cancelled or fails. The [acadp_payment_errors]...[/acadp_payment_errors] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[payment_history]',
			__( 'Payment history page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'payment_history',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the users can view their payment history. The [acadp_payment_history] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[login_form]',
			__( 'Login form', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'login_form',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the users can login to the site. The [acadp_login] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);

		add_settings_field(
			'acadp_page_settings[register_form]',
			__( 'Registration form', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'register_form',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the users can register an account in the site. The [acadp_register] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[user_account]',
			__( 'User account', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'user_account',
				'options'     => get_pages(),
				'description' => __( 'This is the page where the users can view/edit their account info. The [acadp_user_account] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[forgot_password]',
			__( 'Forgot Password', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'forgot_password',
				'options'     => get_pages(),
				'description' => __( 'This is the page users are sent to when clicking the forgot password link. The [acadp_forgot_password] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_page_settings[password_reset]',
			__( 'Password Reset', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_pages' ),
			$page_hook,
			'acadp_page_settings_section',
			array(
				'option_name' => 'acadp_page_settings',
				'field_name'  => 'password_reset',
				'options'     => get_pages(),
				'description' => __( 'This is the page users are sent to when clicking the password reset link. The [acadp_password_reset] short code must be on this page.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_page_settings',
			array( $this, 'sanitize_options' )
    	);	
	
	}	
	
	/**
	 * Register the Sections, Fields, and Settings for "Payment Gateways Settings" tab.
	 * 
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $page_hook    Admin page on which to add this section of options.
	 */
	public function register_gateways_settings( $page_hook ) {
	
		$option_group = $page_hook;
		
		// Section : "acadp_gateway_settings_section"
		add_settings_section(
    		'acadp_gateway_settings_section',
    		__( 'General settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_gateway_settings[gateways][]',
			__( 'Enable / Disable', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'gateways',
				'options'     => acadp_get_payment_gateways()
			)
		);
		
		add_settings_field(
			'acadp_gateway_settings[test_mode]',
			__( 'Test mode', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_checkbox' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'test_mode',
				'field_label' => __( 'While in test mode no live transactions are processed. To fully use test mode, you must have a sandbox (test) account for the payment gateway you are testing.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_gateway_settings[use_https]',
			__( 'Enforce SSL on checkout', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_checkbox' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'use_https',
				'field_label' => __( 'Check this to force users to be redirected to the secure checkout page. You must have an SSL certificate installed to use this option.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_gateway_settings[currency]',
			__( 'Currency', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'currency',
				'description' => __( 'Enter your currency. Note that some payment gateways have currency restrictions.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_gateway_settings[position]',
			__( 'Currency position', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_select' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'position',
				'options'     => array(
					'before' => __( 'Before - $10', 'advanced-classifieds-and-directory-pro' ),
					'after'  => __( 'After - 10$', 'advanced-classifieds-and-directory-pro' )
				),
				'description' => __( 'Choose the location of the currency sign.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_gateway_settings[thousands_separator]',
			__( 'Thousands separator', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'thousands_separator',
				'description' => __( 'The symbol (usually , or .) to separate thousands.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_gateway_settings[decimal_separator]',
			__( 'Decimal separator', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_gateway_settings_section',
			array(
				'option_name' => 'acadp_gateway_settings',
				'field_name'  => 'decimal_separator',
				'description' => __( 'The symbol (usually , or .) to separate decimal points.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_gateway_settings',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_gateway_offline_settings"
		add_settings_section(
    		'acadp_gateway_offline_settings_section',
    		__( 'Offline payment', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(	
			'acadp_gateway_offline_settings[label]',						
			__( 'Title', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_gateway_offline_settings_section',
			array(
				'option_name' => 'acadp_gateway_offline_settings',
				'field_name'  => 'label'
			)			
		);
		
		add_settings_field(	
			'acadp_gateway_offline_settings[description]',						
			__( 'Description', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_textarea' ),	
			$page_hook,	
			'acadp_gateway_offline_settings_section',
			array(
				'option_name' => 'acadp_gateway_offline_settings',
				'field_name'  => 'description'
			)			
		);
		
		add_settings_field(
			'acadp_gateway_offline_settings[instructions]',
			__( 'Instructions', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_gateway_offline_settings_section',
			array(
				'option_name' => 'acadp_gateway_offline_settings',
				'field_name'  => 'instructions'
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_gateway_offline_settings',
			array( $this, 'sanitize_options' )
    	);	
	
	}
	
	/**
	 * Register the Sections, Fields, and Settings for "Monetize Settings" tab.
	 * 
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $page_hook    Admin page on which to add this section of options.
	 */
	public function register_monetize_settings( $page_hook ) {
	
		$option_group = $page_hook;
		
		// Section : "acadp_featured_listing_settings_section"
		add_settings_section(
    		'acadp_featured_listing_settings_section',
    		__( 'Featured listing options', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(	
			'acadp_featured_listing_settings[enabled]',						
			__( 'Enable / Disable', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_featured_listing_settings_section',
			array(
				'option_name' => 'acadp_featured_listing_settings',
				'field_name'  => 'enabled',
				'field_label' => __( 'Check this to enable featured listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_featured_listing_settings[label]',						
			__( 'Title', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_featured_listing_settings_section',
			array(
				'option_name' => 'acadp_featured_listing_settings',
				'field_name'  => 'label',
				'description' => __( 'You can give your own name for this feature using this field.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(	
			'acadp_featured_listing_settings[description]',						
			__( 'Description', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_textarea' ),	
			$page_hook,	
			'acadp_featured_listing_settings_section',
			array(
				'option_name' => 'acadp_featured_listing_settings',
				'field_name'  => 'description'
			)			
		);
		
		add_settings_field(	
			'acadp_featured_listing_settings[price]',						
			sprintf( __( "Price [%s]", 'advanced-classifieds-and-directory-pro' ), acadp_get_payment_currency() ),					
			array( $this, 'callback_price' ),	
			$page_hook,	
			'acadp_featured_listing_settings_section',
			array(
				'option_name' => 'acadp_featured_listing_settings',
				'field_name'  => 'price'
			)			
		);
		
		add_settings_field(	
			'acadp_featured_listing_settings[show_featured_tag]',						
			__( 'Show "Featured" tag', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_checkbox' ),	
			$page_hook,	
			'acadp_featured_listing_settings_section',
			array(
				'option_name' => 'acadp_featured_listing_settings',
				'field_name'  => 'show_featured_tag',
				'field_label' => __( 'Check this to show "Featured" label on featured listings', 'advanced-classifieds-and-directory-pro' )
			)			
		);

		register_setting(
        	$option_group,
        	'acadp_featured_listing_settings',
			array( $this, 'sanitize_options' )
    	);	
		
	}
	
	/**
	 * Register the Sections, Fields, and Settings for "Email Settings" tab.
	 * 
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $page_hook    Admin page on which to add this section of options.
	 */
	public function register_email_settings( $page_hook ) {
		 
		 $option_group = $page_hook;
		 
		// Section : "acadp_email_settings_section"
		add_settings_section(
    		'acadp_email_settings_section',
    		__( 'General settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_settings[from_name]',
			__( 'From name', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_settings_section',
			array(
				'option_name' => 'acadp_email_settings',
				'field_name'  => 'from_name',
				'description' => __( 'The name system generated emails are sent from. This should probably be your site or directory name.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_email_settings[from_email]',
			__( 'From email', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_settings_section',
			array(
				'option_name' => 'acadp_email_settings',
				'field_name'  => 'from_email',
				'description' => __( 'The email id system generated emails are sent from. This will act as the "from" and "reply-to" address.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_email_settings[admin_notice_emails]',
			__( 'Admin notification emails', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_textarea' ),
			$page_hook,
			'acadp_email_settings_section',
			array(
				'option_name' => 'acadp_email_settings',
				'field_name'  => 'admin_notice_emails',
				'description' => __( 'Enter the email address(es) that should receive admin notification emails, one per line.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_email_settings[notify_admin][]',
			__( 'Notify admin via email when', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_email_settings_section',
			array(
				'option_name' => 'acadp_email_settings',
				'field_name'  => 'notify_admin',
				'options'     => array(
					'listing_submitted' => __( 'A new listing is submitted', 'advanced-classifieds-and-directory-pro' ),
					'listing_edited'    => __( 'A listing is edited', 'advanced-classifieds-and-directory-pro' ),
					'listing_expired'   => __( 'A listing expired', 'advanced-classifieds-and-directory-pro' ),
					'order_created'     => __( 'Order created', 'advanced-classifieds-and-directory-pro' ),
					'payment_received'  => __( 'Payment received', 'advanced-classifieds-and-directory-pro' ),
					'listing_contact'   => __( 'A contact message is sent to a listing owner', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
		
		add_settings_field(
			'acadp_email_settings[notify_users][]',
			__( 'Notify users via email when their', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_email_settings_section',
			array(
				'option_name' => 'acadp_email_settings',
				'field_name'  => 'notify_users',
				'options'     => array(
					'listing_submitted' => __( 'Listing is submitted', 'advanced-classifieds-and-directory-pro' ),
					'listing_published' => __( 'Listing is approved/published', 'advanced-classifieds-and-directory-pro' ),
					'listing_renewal'   => __( 'Listing is about to expire (reached renewal email threshold)', 'advanced-classifieds-and-directory-pro' ),
					'listing_expired'   => __( 'Listing expired', 'advanced-classifieds-and-directory-pro' ),					
					'remind_renewal'    => __( 'Listing expired and reached renewal reminder email threshold', 'advanced-classifieds-and-directory-pro' ),
					'order_created'     => __( 'Order created', 'advanced-classifieds-and-directory-pro' ),
					'order_completed'   => __( 'Order completed', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);

		add_settings_field(
			'acadp_email_settings[show_email_address_publicly]',
			__( 'Display email address fields publicly?', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_checkbox' ),
			$page_hook,
			'acadp_email_settings_section',
			array(
				'option_name' => 'acadp_email_settings',
				'field_name'  => 'show_email_address_publicly',
				'field_label' => __( 'Shows the email address of the listing owner to all web users. NOT RECOMMENDED as this increases spam to the address and allows spam bots to harvest it for future use.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_settings',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_listing_submitted_section"
		add_settings_section(
    		'acadp_email_template_listing_submitted_section',
    		__( 'Listing submitted email ( confirmation )', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_listing_submitted[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_listing_submitted_section',
			array(
				'option_name' => 'acadp_email_template_listing_submitted',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_listing_submitted[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_listing_submitted_section',
			array(
				'option_name' => 'acadp_email_template_listing_submitted',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_listing_submitted',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_listing_published_section"
		add_settings_section(
    		'acadp_email_template_listing_published_section',
    		__( 'Listing published/approved email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_listing_published[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_listing_published_section',
			array(
				'option_name' => 'acadp_email_template_listing_published',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_listing_published[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_listing_published_section',
			array(
				'option_name' => 'acadp_email_template_listing_published',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_listing_published',
			array( $this, 'sanitize_options' )
    	);		
		
		// Section : "acadp_email_template_listing_renewal_section"
		add_settings_section(
    		'acadp_email_template_listing_renewal_section',
    		__( 'Listing renewal email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(	
			'acadp_email_template_listing_renewal[email_threshold]',						
			__( 'Listing renewal email threshold (in days)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_email_template_listing_renewal_section',
			array(
				'option_name' => 'acadp_email_template_listing_renewal',
				'field_name'  => 'email_threshold',
				'description' => __( 'Configure how many days before listing expiration is the renewal email sent.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(
			'acadp_email_template_listing_renewal[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_listing_renewal_section',
			array(
				'option_name' => 'acadp_email_template_listing_renewal',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_listing_renewal[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_listing_renewal_section',
			array(
				'option_name' => 'acadp_email_template_listing_renewal',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{expiration_date} - '.__( 'Expiration date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{category_name} - '.__( 'Category name that is going to expire', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{renewal_link} - '.__( 'Link to renewal page', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.	
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.							
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_listing_renewal',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_listing_expired_section"
		add_settings_section(
    		'acadp_email_template_listing_expired_section',
    		__( 'Listing expired email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_listing_expired[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_listing_expired_section',
			array(
				'option_name' => 'acadp_email_template_listing_expired',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_listing_expired[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_listing_expired_section',
			array(
				'option_name' => 'acadp_email_template_listing_expired',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{expiration_date} - '.__( 'Expiration date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{category_name} - '.__( 'Category name that is going to expire', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{renewal_link} - '.__( 'Link to renewal page', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.	
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.							
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_listing_expired',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_renewal_reminder_section"
		add_settings_section(
    		'acadp_email_template_renewal_reminder_section',
    		__( 'Renewal reminder email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(	
			'acadp_email_template_renewal_reminder[reminder_threshold]',						
			__( 'Listing renewal reminder email threshold (in days)', 'advanced-classifieds-and-directory-pro' ),						
			array( $this, 'callback_text' ),	
			$page_hook,	
			'acadp_email_template_renewal_reminder_section',
			array(
				'option_name' => 'acadp_email_template_renewal_reminder',
				'field_name'  => 'reminder_threshold',
				'description' => __( 'Configure how many days after the expiration of a listing an email reminder should be sent to the owner.', 'advanced-classifieds-and-directory-pro' )
			)			
		);
		
		add_settings_field(
			'acadp_email_template_renewal_reminder[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_renewal_reminder_section',
			array(
				'option_name' => 'acadp_email_template_renewal_reminder',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_renewal_reminder[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_renewal_reminder_section',
			array(
				'option_name' => 'acadp_email_template_renewal_reminder',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{expiration_date} - '.__( 'Expiration date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{category_name} - '.__( 'Category name that is going to expire', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{renewal_link} - '.__( 'Link to renewal page', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.	
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.							
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_renewal_reminder',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_order_created_section"
		add_settings_section(
    		'acadp_email_template_order_created_section',
    		__( 'Order created email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_order_created[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_order_created_section',
			array(
				'option_name' => 'acadp_email_template_order_created',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_order_created[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_order_created_section',
			array(
				'option_name' => 'acadp_email_template_order_created',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_id} - '.__( 'Payment Order ID', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_page} - '.__( 'Adds a link so users can view their order directly on your website', 'advanced-classifieds-and-directory-pro' ).'<br>'.		
								'{order_details} - '.__( 'Payment Order details', 'advanced-classifieds-and-directory-pro' ).'<br>'.	
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.													
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_order_created',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_order_created_offline_section"
		add_settings_section(
    		'acadp_email_template_order_created_offline_section',
    		__( 'Order created email ( offline )', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_order_created_offline[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_order_created_offline_section',
			array(
				'option_name' => 'acadp_email_template_order_created_offline',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_order_created_offline[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_order_created_offline_section',
			array(
				'option_name' => 'acadp_email_template_order_created_offline',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_id} - '.__( 'Payment Order ID', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_page} - '.__( 'Adds a link so users can view their order directly on your website', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_details} - '.__( 'Payment Order details', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_order_created_offline',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_order_completed_section"
		add_settings_section(
    		'acadp_email_template_order_completed_section',
    		__( 'Order completed email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_order_completed[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_order_completed_section',
			array(
				'option_name' => 'acadp_email_template_order_completed',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_order_completed[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_order_completed_section',
			array(
				'option_name' => 'acadp_email_template_order_completed',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_id} - '.__( 'Payment Order ID', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{order_page} - '.__( 'Adds a link so users can view their order directly on your website', 'advanced-classifieds-and-directory-pro' ).'<br>'.		
								'{order_details} - '.__( 'Payment Order details', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_order_completed',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_email_template_listing_contact_section"
		add_settings_section(
    		'acadp_email_template_listing_contact_section',
    		__( 'Listing contact email', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_email_template_listing_contact[subject]',
			__( 'Subject', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_email_template_listing_contact_section',
			array(
				'option_name' => 'acadp_email_template_listing_contact',
				'field_name'  => 'subject'
			)
		);
		
		add_settings_field(
			'acadp_email_template_listing_contact[body]',
			__( 'Body', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_email_template_listing_contact_section',
			array(
				'option_name' => 'acadp_email_template_listing_contact',
				'field_name'  => 'body',
				'description' => __( 'HTML is accepted. You can use the following placeholders:', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{name} - '.__( 'The listing owner\'s display name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{username} - '.__( 'The listing owner\'s user name on the site', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_name} - '.__( 'Your site name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_link} - '.__( 'Your site name with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{site_url} - '.__( 'Your site url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_title} - '.__( 'Listing\'s title', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_link} - '.__( 'Listing\'s title with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{listing_url} - '.__( 'Listing\'s url with link', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{sender_name} - '.__( 'Sender\'s name', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{sender_email} - '.__( 'Sender\'s email address', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{message} - '.__( 'Contact message', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{today} - '.__( 'Current date', 'advanced-classifieds-and-directory-pro' ).'<br>'.
								'{now} - '.__( 'Current time', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_email_template_listing_contact',
			array( $this, 'sanitize_options' )
    	);
	
	}
	
	/**
	 * Register the Sections, Fields, and Settings for "Misc Settings" tab.
	 * 
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 string    $page_hook    Admin page on which to add this section of options.
	 */
	public function register_misc_settings( $page_hook ) {
	
		$option_group = $page_hook;
			
		// Section : "acadp_permalink_settings_section"
		add_settings_section(
    		'acadp_permalink_settings_section',
    		__( 'Permalink slugs', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_permalink_settings[listing]',
			__( 'Listing detail page', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_permalink_settings_section',
			array(
				'option_name' => 'acadp_permalink_settings',
				'field_name'  => 'listing',
				'description' => __( 'Replaces the SLUG value used by custom post type "acadp_listings".', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_permalink_settings',
			array( $this, 'sanitize_options' )
    	);

		// Section : "acadp_socialshare_settings_section"
		add_settings_section(
    		'acadp_socialshare_settings_section',
    		__( 'Socialshare buttons', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);

		add_settings_field(
			'acadp_socialshare_settings[services][]',
			__( 'Enable services', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_socialshare_settings_section',
			array(
				'option_name' => 'acadp_socialshare_settings',
				'field_name'  => 'services',
				'options'     => array(
					'facebook'  => __( 'Facebook', 'advanced-classifieds-and-directory-pro' ),
					'twitter'   => __( 'Twitter', 'advanced-classifieds-and-directory-pro' ),
					'gplus'     => __( 'Google plus', 'advanced-classifieds-and-directory-pro' ),
					'linkedin'  => __( 'Linkedin', 'advanced-classifieds-and-directory-pro' ),
					'pinterest' => __( 'Pinterest', 'advanced-classifieds-and-directory-pro' )					
				)
			)
		);
		
		add_settings_field(
			'acadp_socialshare_settings[pages][]',
			__( 'Show buttons in', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_socialshare_settings_section',
			array(
				'option_name' => 'acadp_socialshare_settings',
				'field_name'  => 'pages',
				'options'     => array(
					'listing'    => __( 'Listing detail page', 'advanced-classifieds-and-directory-pro' ),
					'listings'   => __( 'Listings page', 'advanced-classifieds-and-directory-pro' ),
					'categories' => __( 'Categories page', 'advanced-classifieds-and-directory-pro' ),
					'locations'  => __( 'Locations page', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_socialshare_settings',
			array( $this, 'sanitize_options' )
    	);

		// Section : "acadp_map_settings_section"
		add_settings_section(
    		'acadp_map_settings_section',
    		__( 'Map settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_map_settings[api_key]',
			__( 'API key', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_map_settings_section',
			array(
				'option_name' => 'acadp_map_settings',
				'field_name'  => 'api_key',
				'description' => __( 'Your Google Maps API Key.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_map_settings[zoom_level]',
			__( 'Zoom level', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_map_settings_section',
			array(
				'option_name' => 'acadp_map_settings',
				'field_name'  => 'zoom_level',
				'description' => __( '0 = zoomed out; 21 = zoomed in', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_map_settings',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_recaptcha_settings_section"
		add_settings_section(
    		'acadp_recaptcha_settings_section',
    		__( 'reCAPTCHA settings', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_recaptcha_settings[forms][]',
			__( 'Enable reCAPTCHA in', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_multicheck' ),
			$page_hook,
			'acadp_recaptcha_settings_section',
			array(
				'option_name' => 'acadp_recaptcha_settings',
				'field_name'  => 'forms',
				'options'     => array(
					'registration' => __( 'User Registration form', 'advanced-classifieds-and-directory-pro' ),
					'listing'      => __( 'New Listing form', 'advanced-classifieds-and-directory-pro' ),
					'contact'      => __( 'Contact form', 'advanced-classifieds-and-directory-pro' ),
					'report_abuse' => __( 'Report abuse form', 'advanced-classifieds-and-directory-pro' )
				)
			)
		);
		
		add_settings_field(
			'acadp_recaptcha_settings[site_key]',
			__( 'Site key', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_recaptcha_settings_section',
			array(
				'option_name' => 'acadp_recaptcha_settings',
				'field_name'  => 'site_key'
			)
		);
		
		add_settings_field(
			'acadp_recaptcha_settings[secret_key]',
			__( 'Secret key', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_recaptcha_settings_section',
			array(
				'option_name' => 'acadp_recaptcha_settings',
				'field_name'  => 'secret_key'
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_recaptcha_settings',
			array( $this, 'sanitize_options' )
    	);
		
		// Section : "acadp_terms_of_agreement_section"
		add_settings_section(
    		'acadp_terms_of_agreement_section',
    		__( 'Terms of agreement', 'advanced-classifieds-and-directory-pro' ),
    		array( $this, 'settings_section_callback' ),
    		$page_hook
		);
		
		add_settings_field(
			'acadp_terms_of_agreement[show_agree_to_terms]',
			__( 'Agree to terms', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_checkbox' ),
			$page_hook,
			'acadp_terms_of_agreement_section',
			array(
				'option_name' => 'acadp_terms_of_agreement',
				'field_name'  => 'show_agree_to_terms',
				'field_label' => __( 'Check this to show an agree to terms on the listing form that users must agree to before submitting their listing', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_terms_of_agreement[agree_label]',
			__( 'Agree to terms label', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_text' ),
			$page_hook,
			'acadp_terms_of_agreement_section',
			array(
				'option_name' => 'acadp_terms_of_agreement',
				'field_name'  => 'agree_label',
				'description' => __( 'Label shown next to the agree to terms check box.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_settings_field(
			'acadp_terms_of_agreement[agree_text]',
			__( 'Agreement text', 'advanced-classifieds-and-directory-pro' ),
			array( $this, 'callback_wysiwyg' ),
			$page_hook,
			'acadp_terms_of_agreement_section',
			array(
				'option_name' => 'acadp_terms_of_agreement',
				'field_name'  => 'agree_text',
				'description' => __( 'If "Agree to terms" is checked, enter the agreement terms or an URL starting with http. If you use an URL, the "Agree to terms label" will be linked to this given URL.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		register_setting(
        	$option_group,
        	'acadp_terms_of_agreement',
			array( $this, 'sanitize_options' )
    	);
	
	}
	
	/**
 	 * Displays description of each sections.
 	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings section args.
 	 */
	public function settings_section_callback( $args ) {
	
		switch( $args['id'] ) {
			case 'acadp_currency_settings_section' :
				printf( '<div id="currency-settings">%s <a href="%s">%s</a></div>', __( 'Currency settings under this section are used to format the display of listing Price. You can have separate currency to accept payments from your users.', 'advanced-classifieds-and-directory-pro' ), admin_url( 'edit.php?post_type=acadp_listings&page=acadp_settings&tab=gateways' ), __( 'Configure payment currency', 'advanced-classifieds-and-directory-pro' ) );
				break;
			case 'acadp_page_settings_section' :
				_e( 'NOTE: We ourselves have generated all the required pages and configured them right for you here. So, don\'t change these settings unless necessary. Mis-configuration of these settings may break the plugin from working correctly. So, care should be taken while editing these page settings.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_gateway_settings_section' :
				printf( '%s <a href="%s">%s</a>', __( 'Note: Currency settings under this section are used only to accept payments from your users.', 'advanced-classifieds-and-directory-pro' ), admin_url( 'edit.php?post_type=acadp_listings&page=acadp_settings&tab=general#currency-settings' ), __( 'Configure listing currency', 'advanced-classifieds-and-directory-pro' ) );
				break;
			case 'acadp_permalink_settings_section' :
				_e( 'NOTE: Just make sure that, after updating the fields in this section, you flush the rewrite rules by visiting Settings > Permalinks. Otherwise you\'ll still see the old links.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_featured_listing_settings_section' :
				_e( 'Featured listings will always appear on top of regular listings.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_gateway_offline_settings_section' :
				_e( 'Note: There\'s nothing automatic in this offline payment system, you should use this when you don\'t want to collect money automatically. So once money is in your bank account you change the status of the order manually under "Payment History" menu.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_renewal_reminder_section' :
				_e( 'Sent some time after listing expiration and when no renewal has occurred.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_listing_renewal_section' :
				_e( 'Sent at the time of listing expiration.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_renewal_pending_section' :
				_e( 'Sent some time before the listing expires.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_payment_abandoned_section' :
				_e( 'Sent some time after a pending payment is abandoned by users.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_listing_contact_section' :
				_e( 'Sent to listing owners when someone uses the contact form on their listing pages.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_listing_published_section' :
				_e( 'Sent when the listing has been published or approved by an admin.', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'acadp_email_settings_listing_received_section' :
				_e( 'Sent after a listing has been submitted.', 'advanced-classifieds-and-directory-pro' );
				break;
		}
		
	}
	
	/**
	 * Displays a text field with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_text( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? esc_attr( $values[ $args['field_name'] ] ) : '';
	
		// Echo proper textarea
		echo '<input type="text" id="'.$id.'" name="'.$name.'" size="50" value="'.$value.'" />';
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a price field with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_price( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? esc_attr( $values[ $args['field_name'] ] ) : 0;
		$value = acadp_format_amount( $value );
	
		// Echo proper textarea
		echo '<input type="text" id="'.$id.'" name="'.$name.'" size="50" value="'.$value.'" />';
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a textarea with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_textarea( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? esc_textarea( $values[ $args['field_name'] ] ) : '';
	
		// Echo proper textarea
		echo '<textarea id="'.$id.'" name="'.$name.'" rows="6" cols="60">'.$value.'</textarea>';
	
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a rich text textarea with the field description for a settings field.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_wysiwyg( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : '';
		
		// Echo wordpress editor
		wp_editor(
			$value,
			$id,
			array(
				'textarea_name' => $name,
				'media_buttons' => false,
				'quicktags'     => true,
				'editor_height' => 250
			)
	  	);
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<pre>'.$args['description'].'</pre>';
		}
		
	}
	
	/**
	 * Displays a checkbox with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_checkbox( $args ) {
	
		// Get the field name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the value of this setting
		$values = get_option( $args['option_name'], array() );
		$checked = ( isset( $values[ $args['field_name'] ] ) && $values[ $args['field_name'] ] == 1 ) ? ' checked="checked"' : '';
		
		// Echo proper input type="checkbox"
		echo '<label for="'.$id.'">';
		echo '<input type="checkbox" id="'.$id.'" name="'.$name.'" value="1"'.$checked.'/>';
		echo $args['field_label'];
		echo '</label>';
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays multiple checkboxes with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_multicheck( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$values = isset( $values[ $args['field_name'] ] ) ? (array) $values[ $args['field_name'] ] : array();	

		// Echo proper input type="checkbox"
		foreach( $args['options'] as $value => $label ) {
			$checked = in_array( $value, $values ) ? ' checked="checked"' : '';
		
			echo '<p>';
			echo '<label for="'.$id.'_'.$value.'">';
			echo '<input type="checkbox" id="'.$id.'_'.$value.'" name="'.$name.'[]" value="'.$value.'"'.$checked.'/>';
			echo $label;
			echo '</label>';
			echo '</p>';
		}
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a radio button group with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_radio( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$checked = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : '';	

		// Echo proper input type="radio"
		foreach( $args['options'] as $key => $label ) {
			echo '<p>';
			echo "<label for='".$id."_".$key."'>";
			echo "<input type='radio' id='".$id."_".$key."' name='".$name."' value='".$key."'".checked( $checked, $key, false )."/>";
			echo $label;
			echo "</label>";
			echo "</p>";
		}
		
		// Echo the field description (only if applicable)
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a selectbox with the field description for a settings field.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_select( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$selected = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : '';	
	
		// Echo proper selectbox
		echo '<select id="'.$id.'" name="'.$name.'">'; 
		foreach( $args['options'] as $value => $label ) { 
			echo '<option value="'.$value.'"'.selected( $selected, $value, false ).'>'.$label.'</option>'; 
		} 
		echo '</select>';
		
		// Echo the field description from the $args array
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}	
	
	/**
	 * Displays a list of wordpress pages in a select with the field description.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_pages( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$selected = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : -1;	
	
		// Echo proper selectbox
		echo '<select id="'.$id.'" name="'.$name.'">';
		echo '<option value="-1">-- '.__( 'Select a page', 'advanced-classifieds-and-directory-pro' ).' --</option>';
		foreach( $args['options'] as $page ) {
			echo '<option value="'.$page->ID.'"'.selected( $selected, $page->ID, false ).'>'.$page->post_title.'</option>';
		}  
		echo '</select>';
		
		// Echo the field description from the $args array
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
	 * Displays a list of ACADP locations in a select with the field description.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @params	 array    $args    settings field args.
 	 */	
	public function callback_locations( $args ) {
	
		// Get the field id & name from the $args array
		$id = $args['option_name'].'_'.$args['field_name'];
		$name = $args['option_name'].'['.$args['field_name'].']';
		
		// Get the values of this setting
		$values = get_option( $args['option_name'], array() );
		$value = isset( $values[ $args['field_name'] ] ) ? $values[ $args['field_name'] ] : -1;	
	
		// Echo proper selectbox
		wp_dropdown_categories( array(
      		'show_option_none' => '-- '.__( 'Select a location', 'advanced-classifieds-and-directory-pro' ).' --',
        	'taxonomy'         => 'acadp_locations',
        	'name'             => $name,
			'id'               => $id,						
        	'orderby'          => 'name',
			'selected'         => $value,
        	'hierarchical'     => true,
        	'depth'            => 10,
        	'show_count'       => false,
        	'hide_empty'       => false,
      	) );
		
		// Echo the field description from the $args array
		if( isset( $args['description'] ) ) {
			echo '<p class="description">'.$args['description'].'</p>';
		}
		
	}
	
	/**
 	 * Sanitization callback for the settings pages. This function loops through the
	 * incoming option and sanitises values based on the nature of the option.
     *	
	 * @since    1.0.0
	 * @access   public
	 *
 	 * @params	 $input    The unsanitized collection of options.
 	 * @return	           The collection of sanitized values.
 	 */
	public function sanitize_options( $input ) {

		$output = array();
	
		if( ! empty( $input ) ) {
		
			foreach( $input as $key => $value ) {

				switch( $key ) {
					// Sanitize text field
					case 'custom_login' :
					case 'currency' :
					case 'decimal_separator' :
					case 'new_listing_label' :
					case 'popular_listing_label' :
					case 'label' :
					case 'from_name' :
					case 'subject' :
					case 'listing' :
					case 'api_key' :
					case 'site_key' :
					case 'secret_key' :
					case 'agree_label' :
						$output[ $key ] = sanitize_text_field( $input[ $key ] );
						break;
					// Sanitize text field[allow empty values]
					case 'thousands_separator' :
						$output[ $key ] = ( ' ' !== $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : ' ';
						break;
					// Sanitize text field[integer]
					case 'listing_duration' :
					case 'maximum_images_per_listing' :
					case 'new_listing_threshold' :
					case 'popular_listing_threshold' :
					case 'delete_expired_listings' :
					case 'columns' :
					case 'listings_per_page' :
					case 'excerpt_length' :
					case 'depth' :
					case 'email_threshold' :
					case 'reminder_threshold' :
					case 'zoom_level' :
						$output[ $key ] = (int) $input[ $key ];
						break;
					// Sanitize text field[email]
					case 'from_email' :
						$output[ $key ] = sanitize_email( $input[ $key ] );
						break;
					// Sanitize text field[URL]					
					case 'custom_register' :
					case 'custom_forgot_password' :
						$output[ $key ] = esc_url_raw( $input[ $key ] );
						break;
					// Sanitize text field[price]
					case 'price' :
						$output[ $key ] = acadp_sanitize_amount( $input[ $key ] );
						break;
					// Sanitize textarea[plain]
					case 'description' :
					case 'admin_notice_emails' :
						$output[ $key ] = esc_textarea( $input[ $key ] );
						break;
					// Sanitize textarea[html]
					case 'textarea_html' :
						$output[ $key ] = esc_html( $input[ $key ] );
						break;
					// Sanitize wordpress editor field
					case 'instructions' :
					case 'body' :
					case 'agree_text' :
						$output[ $key ] = wp_kses_post( $input[ $key ] );
						break;
					// Sanitize checkbox
					case 'has_location' :
					case 'disable_parent_categories' :		
					case 'has_price' :			
					case 'has_images' :
					case 'has_video' :
					case 'has_map' :
					case 'has_contact_form' :
					case 'contact_form_require_login' :					
					case 'has_comment_form' :
					case 'has_report_abuse' :
					case 'has_favourites' :
					case 'show_new_tag' :
					case 'show_popular_tag' :
					case 'has_listing_renewal' :					
					case 'show_count' :
					case 'hide_empty' :
					case 'enabled' :
					case 'show_featured_tag' :
					case 'test_mode' :
					case 'use_https' :	
					case 'show_email_address_publicly' :	
					case 'show_agree_to_terms' :			
						$output[ $key ] = (int) $input[ $key ];
						break;
					// Sanitize multi-checkbox
					case 'load_bootstrap' :
					case 'display_options' :
					case 'view_options' :
					case 'include_results_from' :
					case 'display_in_header' :
					case 'display_in_listing' :
					case 'gateways' :
					case 'notify_admin' :
					case 'notify_users' :
					case 'services' :
					case 'pages' :
					case 'forms' :
						$output[ $key ] = array_map( 'esc_attr', $input[ $key ] );
						break;
					// Sanitize select or radio field
					case 'base_location' :
					case 'default_location' :
					case 'text_editor' :				
					case 'new_listing_status' :
					case 'edit_listing_status' :
					case 'default_view' :
					case 'orderby' :
					case 'order' :
					case 'view' :
					case 'engine' :
					case 'position' :
					case 'listings' :
					case 'locations' :
					case 'location' :
					case 'categories' :
					case 'category' :					
					case 'search' :
					case 'user_listings' :
					case 'user_dashboard' :
					case 'listing_form' :
					case 'manage_listings' :
					case 'favourite_listings' :
					case 'checkout' :
					case 'payment_receipt' :
					case 'payment_failure' :
					case 'payment_history' :
					case 'login_form' :					
					case 'register_form' :
					case 'user_account' :
					case 'forgot_password' :
					case 'password_reset' :
						$output[ $key ] = sanitize_key( $input[ $key ] );
						break;
					// Default sanitize method
					default :
						$output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );	
				}			
	
			}
		
		}
		
		return apply_filters( 'acadp_sanitize_options', $output, $input );
		
	}
	
}

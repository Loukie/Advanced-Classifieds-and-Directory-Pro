<?php

/**
 * ACADP Listing Contact Widget
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/widgets/listing-contact
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.5.0
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Widget_Listing_Contact Class
 *
 * @since     1.5.0
 * @access    public
 */
class ACADP_Widget_Listing_Contact extends WP_Widget {

	/**
     * Unique identifier for the widget.
     *
     * @since     1.5.0
	 * @access    protected
     * @var       string
     */
    protected $widget_slug;
	
	/**
	 * Get things going.
	 *
	 * @since     1.5.0
	 * @access    public
	 */
	public function __construct() {
	
		$this->widget_slug = ACADP_PLUGIN_NAME.'-widget-listing-contact';
	
		parent::__construct(
			$this->widget_slug,
			__( 'ACADP Listing Contact', 'advanced-classifieds-and-directory-pro' ),
			array(
				'classname'   => $this->widget_slug.'-class',
				'description' => __( 'Contact us form to contact "Advanced Classifieds & Directory Pro" listing owners.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );
	
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since     1.5.0
	 * @access    public
	 *
	 * @param	  array	   $args	    The array of form elements.
	 * @param	  array    $instance    The current instance of the widget.
	 */
	public function widget( $args, $instance ) {
		
		if( is_singular('acadp_listings') ) {

			$general_settings = get_option( 'acadp_general_settings' );
			
			$can_show_contact_form = empty( $general_settings['has_contact_form'] ) ? false : true;	
		
			if( $can_show_contact_form ) {
			
				echo $args['before_widget'];
		
				if( ! empty( $instance['title'] ) ) {
					echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
				}
		
				include( acadp_get_template( 'acadp-widget-public-listing-contact-display.php', 'listing-contact' ) );
		
				echo $args['after_widget'];
				
			}
			
		}

	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since     1.5.0
	 * @access    public
	 *
	 * @param	  array	   $new_instance    The new instance of values to be generated via the update.
	 * @param	  array    $old_instance    The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		
		return $instance;

	}
	
	/**
	 * Generates the administration form for the widget.
	 *
	 * @since     1.5.0
	 * @access    public
	 *
	 * @param	  array    $instance    The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		
 		// Define the array of defaults
		$defaults = array(
			'title' =>  __( 'Contact this listing owner', 'advanced-classifieds-and-directory-pro' ),
		);

		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);

		// Display the admin form
		include( ACADP_PLUGIN_DIR . 'widgets/listing-contact/views/acadp-widget-admin-listing-contact-display.php' );

	}
	
	/**
	 * Enqueues widget-specific styles & scripts.
	 *
	 * @since     1.5.8
	 * @access    public
	 */
	public function enqueue_styles_scripts() {		
	
		if( is_active_widget( false, $this->id, $this->id_base, true ) ) {

			$recaptcha_settings = get_option( 'acadp_recaptcha_settings' );
			
			if( isset( $recaptcha_settings['forms'] ) && in_array( 'contact', $recaptcha_settings['forms'] ) ) {
				wp_enqueue_script( ACADP_PLUGIN_NAME . "-recaptcha" );
			}
			
		}

	}
	
}

add_action( 'widgets_init', create_function( '', 'register_widget("ACADP_Widget_Listing_Contact");' ) );
<?php

/**
 * ACADP Listing Address Widget
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/widgets/listing-address
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.5.0
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Widget_Listing_Address Class
 *
 * @since     1.0.0
 * @access    public
 */
class ACADP_Widget_Listing_Address extends WP_Widget {

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
	
		$this->widget_slug = ACADP_PLUGIN_NAME.'-widget-listing-address';
	
		parent::__construct(
			$this->widget_slug,
			__( 'ACADP Listing Address', 'advanced-classifieds-and-directory-pro' ),
			array(
				'classname'   => $this->widget_slug.'-class',
				'description' => __( '"Advanced Classifieds & Directory Pro" Listing Address and Map.', 'advanced-classifieds-and-directory-pro' )
			)
		);
	
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
		
			global $post;
		
			$general_settings = get_option( 'acadp_general_settings' );
		
			$has_location = empty( $general_settings['has_location'] ) ? false : true;
			
			if( $has_location ) {
			
				$location = wp_get_object_terms( $post->ID, 'acadp_locations' );
			
				if( ! empty( $location ) ) {
				
					$location  = $location[0];
					$post_meta = get_post_meta( $post->ID );
					
					// $can_show_map
					$has_map = ! empty( $general_settings['has_map'] ) && empty( $post_meta['hide_map'][0] ) ? true : false;
					$can_show_map = false;
					
					if( $has_map ) {			
						$can_show_map = ! empty( $post_meta['latitude'][0] ) && ! empty( $post_meta['longitude'][0] ) ? true : false;
					}
				
					// ...
					echo $args['before_widget'];
		
					if( ! empty( $instance['title'] ) ) {
						echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
					}
		
					include( acadp_get_template( 'acadp-widget-public-listing-address-display.php', 'listing-address' ) );
		
					echo $args['after_widget'];
					
				}
				
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
			'title' =>  __( 'Listing Address', 'advanced-classifieds-and-directory-pro' ),
		);

		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);

		// Display the admin form
		include( ACADP_PLUGIN_DIR . 'widgets/listing-address/views/acadp-widget-admin-listing-address-display.php' );

	}
	
}

add_action( 'widgets_init', create_function( '', 'register_widget("ACADP_Widget_Listing_Address");' ) );
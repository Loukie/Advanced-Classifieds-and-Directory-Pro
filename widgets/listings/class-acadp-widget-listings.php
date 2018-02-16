<?php

/**
 * ACADP Listings Widget
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/widgets/locations
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.5.4
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Widget_Listings Class
 *
 * @since     1.5.4
 * @access    public
 */
class ACADP_Widget_Listings extends WP_Widget {
	
	/**
     * Unique identifier for the widget.
     *
     * @since     1.5.4
	 * @access    protected
     * @var       string
     */
    protected $widget_slug;
	
	/**
     * General Settings.
     *
     * @since     1.5.4
	 * @access    private
     * @var       array
     */
    private $general_settings;
	
	/**
     * Listings Settings.
     *
     * @since     1.5.4
	 * @access    private
     * @var       array
     */
    private $listings_settings;
	
	/**
     * Featured Listings Settings.
     *
     * @since     1.5.4
	 * @access    private
     * @var       array
     */
    private $featured_listing_settings;
	
	/**
     * Default Settings.
     *
     * @since     1.5.4
	 * @access    private
     * @var       array
     */
    private $defaults;
	
	/**
	 * Get things going.
	 *
	 * @since     1.5.4
	 * @access    public
	 */
	public function __construct() {
		
		$this->widget_slug = ACADP_PLUGIN_NAME.'-widget-listings';
		$this->general_settings = get_option( 'acadp_general_settings' );
		$this->listings_settings = get_option( 'acadp_listings_settings' );
		$this->featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
		$this->defaults = array(
			'title'             => __( 'Listings', 'advanced-classifieds-and-directory-pro' ),
			'has_location'      => empty( $this->general_settings['has_location'] ) ? 0 : 1,
			'base_location'     => max( 0, $this->general_settings['base_location'] ),
			'location'          => max( 0, $this->general_settings['base_location'] ),
			'category'          => 0,
			'related_listings'  => 0,
			'has_featured'      => empty( $this->featured_listing_settings['enabled'] ) ? 0 : 1,
			'featured'          => 0,
			'limit'             => 15,
			'orderby'           => $this->listings_settings['orderby'],
			'order'             => $this->listings_settings['order'],
			'view'              => 'standard',
			'columns'           => 1,
			'has_images'        => empty( $this->general_settings['has_images'] ) ? 0 : 1,
			'show_image'        => 1,
			'image_position'    => 'left',
			'show_description'  => 0,
			'show_category'     => isset( $this->listings_settings['display_in_listing'] ) && in_array( 'category', $this->listings_settings['display_in_listing'] ) ? 1 : 0,
			'show_location'     => isset( $this->listings_settings['display_in_listing'] ) && in_array( 'location', $this->listings_settings['display_in_listing'] ) ? 1 : 0,
			'has_price'         => empty( $this->general_settings['has_price'] ) ? 0 : 1,
			'show_price'        => isset( $this->listings_settings['display_in_listing'] ) && in_array( 'price', $this->listings_settings['display_in_listing'] ) ? 1 : 0,
			'show_date'         => isset( $this->listings_settings['display_in_listing'] ) && in_array( 'date', $this->listings_settings['display_in_listing'] ) ? 1 : 0,
			'show_user'         => isset( $this->listings_settings['display_in_listing'] ) && in_array( 'user', $this->listings_settings['display_in_listing'] ) ? 1 : 0,
			'show_views'        => isset( $this->listings_settings['display_in_listing'] ) && in_array( 'views', $this->listings_settings['display_in_listing'] ) ? 1 : 0
		);
		
		parent::__construct(
			$this->widget_slug,
			__( 'ACADP Listings', 'advanced-classifieds-and-directory-pro' ),
			array(
				'classname'   => $this->widget_slug.'-class',
				'description' => __( 'Displays "Advanced Classifieds and Directory Pro" Listings.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );
	
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since     1.5.4
	 * @access    public
	 *
	 * @param	  array	   $args	    The array of form elements.
	 * @param	  array    $instance    The current instance of the widget.
	 */
	public function widget( $args, $instance ) {
		
		// Merge incoming $instance array with $defaults
		if( is_array( $instance ) ) {
			$instance = array_merge( $this->defaults, $instance );
		} else {
			$instance = $this->defaults;
		}
		
		// WP Query
		global $post;
		
		$query = array(				
			'post_type'      => 'acadp_listings',
			'post_status'    => 'publish',
			'posts_per_page' => ! empty( $instance['limit'] ) ? (int) $instance['limit'] : -1
		);
		
		$tax_queries  = array();
		$meta_queries = array();
		
		$location = (int) $instance['location'];
		
		if( $instance['has_location'] ) {
		
			if( $instance['related_listings'] ) {
			
				$term_slug = get_query_var( 'acadp_location' );
			
				if( '' != $term_slug ) {		
					$term = get_term_by( 'slug', sanitize_text_field( $term_slug ), 'acadp_locations' );
					$location = $term->term_id;
				}
				
			}
		
			if( $location > 0 ) {
				
				$tax_queries[] = array(
					'taxonomy'         => 'acadp_locations',
					'field'            => 'term_id',
					'terms'            => $location,
					'include_children' => isset( $this->listings_settings['include_results_from'] ) && in_array( 'child_locations', $this->listings_settings['include_results_from'] ) ? true : false,
				);
						
			} else if( $instance['base_location'] > 0 ) {
		
				$tax_queries[] = array(
					'taxonomy'         => 'acadp_locations',
					'field'            => 'term_id',
					'terms'            => $instance['base_location'],
					'include_children' => true,
				);
				
			}
		
		}
		
		$category = (int) $instance['category'];
	
		if( $instance['related_listings'] ) {
		
			if( is_singular( 'acadp_listings' ) ) {
			
				$category = wp_get_object_terms( $post->ID, 'acadp_categories' );
				$category = ! empty( $category ) ? $category[0]->term_id : 0;
				
				$query['post__not_in'] = array( $post->ID );

			} else {
			
				$term_slug = get_query_var( 'acadp_category' );
				
				if( '' != $term_slug ) {		
					$term = get_term_by( 'slug', sanitize_text_field( $term_slug ), 'acadp_categories' );
					$category = $term->term_id;
				}
			
			}
			
		}
		
		if( $category > 0 ) {
		
			$tax_queries[] = array(
				'taxonomy'         => 'acadp_categories',
				'field'            => 'term_id',
				'terms'            => $category,
				'include_children' => isset( $this->listings_settings['include_results_from'] ) && in_array( 'child_categories', $this->listings_settings['include_results_from'] ) ? true : false,
			);
					
		}
		
		$featured_only = 0;
		if( $instance['has_featured'] ) {
			$featured_only = $instance['featured'];
		}
		
		if( $featured_only ) {
			
			$meta_queries[] = array(
				'key'     => 'featured',
				'value'   => 1,
				'compare' => '='
			);
				
		}
		
		$count_tax_queries = count( $tax_queries );
		if( $count_tax_queries ) {
			$query['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $tax_queries ) : array( $tax_queries );
		}
	
		$count_meta_queries = count( $meta_queries );
		if( $count_meta_queries ) {
			$query['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : array( $meta_queries );
		}
		
		$orderby = sanitize_text_field( $instance['orderby'] );
		$order   = sanitize_text_field( $instance['order'] );
	
		switch( $orderby ) {
			case 'price' :
			case 'views' :
				$query['meta_key'] = $orderby;
				$query['orderby']  = 'meta_value_num';
				
				$query['order']    = $order;
				break;
			case 'rand' :
				$query['orderby'] = $orderby;
				break;
			default :
				$query['orderby'] = $orderby;
				$query['order']   = $order;
		}
		
		$acadp_query = new WP_Query( $query );
		
		// Process Output
		if( $acadp_query->have_posts() ) {
		
			echo $args['before_widget'];
		
			if( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
		
			if( 'map' == $instance['view'] ) {
				include( acadp_get_template( 'acadp-widget-public-listings-map-display.php', 'listings' ) );
			} else {
				if( $instance['show_image'] && 'left' == $instance['image_position'] ) {
					include( acadp_get_template( 'acadp-widget-public-listings-media-display.php', 'listings' ) );
				} else {
					include( acadp_get_template( 'acadp-widget-public-listings-thumbnail-display.php', 'listings' ) );
				}
			}
		
			echo $args['after_widget'];
		
		}

	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since     1.5.4
	 * @access    public
	 *
	 * @param	  array	   $new_instance    The new instance of values to be generated via the update.
	 * @param	  array    $old_instance    The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		$instance['title']             = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['location']          = isset( $new_instance['location'] ) ? (int) $new_instance['location'] : 0;
		$instance['category']          = isset( $new_instance['category'] ) ? (int) $new_instance['category'] : 0;
		$instance['related_listings']  = isset( $new_instance['related_listings'] ) ? 1 : 0;
		$instance['featured']          = isset( $new_instance['featured'] ) ? 1 : 0;
		$instance['limit']             = isset( $new_instance['limit'] ) ? (int) $new_instance['limit'] : 0;
		$instance['orderby']           = isset( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : 'title';
		$instance['order']             = isset( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : 'asc';
		$instance['view']              = isset( $new_instance['view'] ) ? sanitize_text_field( $new_instance['view'] ) : 'standard';
		$instance['columns']           = ! empty( $new_instance['columns'] ) ? (int) $new_instance['columns'] : 1;
		$instance['show_image']        = isset( $new_instance['show_image'] ) ? 1 : 0;
		$instance['image_position']    = isset( $new_instance['image_position'] ) ? sanitize_text_field( $new_instance['image_position'] ) : 'left';
		$instance['show_description']  = isset( $new_instance['show_description'] ) ? 1 : 0;
		$instance['show_category']     = isset( $new_instance['show_category'] ) ? 1 : 0;
		$instance['show_location']     = isset( $new_instance['show_location'] ) ? 1 : 0;
		$instance['show_price']        = isset( $new_instance['show_price'] ) ? 1 : 0;
		$instance['show_date']         = isset( $new_instance['show_date'] ) ? 1 : 0;
		$instance['show_user']         = isset( $new_instance['show_user'] ) ? 1 : 0;
		$instance['show_views']        = isset( $new_instance['show_views'] ) ? 1 : 0;
		
		return $instance;

	}
	
	/**
	 * Generates the administration form for the widget.
	 *
	 * @since     1.5.4
	 * @access    public
	 *
	 * @param	  array    $instance    The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$this->defaults
		);
			
		// Display the admin form
		include( ACADP_PLUGIN_DIR . 'widgets/listings/views/acadp-widget-admin-listings-display.php' );

	}
	
	/**
	 * Enqueues widget-specific styles & scripts.
	 *
	 * @since     1.5.4
	 * @access    public
	 */
	public function enqueue_styles_scripts() {		
	
		if( is_active_widget( false, $this->id, $this->id_base, true ) ) {

			wp_enqueue_style( ACADP_PLUGIN_NAME );
			
			wp_enqueue_script( ACADP_PLUGIN_NAME );
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-markerclusterer' );
			
		}

	}
	
}

add_action( 'widgets_init', create_function( '', 'register_widget("ACADP_Widget_Listings");' ) );
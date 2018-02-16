<?php

/**
 * ACADP Categories Widget
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/widgets/categories
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Widget_Categories Class
 *
 * @since     1.0.0
 * @access    public
 */
class ACADP_Widget_Categories extends WP_Widget {
	
	/**
     * Unique identifier for the widget.
     *
     * @since     1.0.0
	 * @access    protected
     * @var       string
     */
    protected $widget_slug;
	
	/**
	 * Get things going.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function __construct() {
		
		$this->widget_slug = ACADP_PLUGIN_NAME.'-widget-categories';
		
		parent::__construct(
			$this->widget_slug,
			__( 'ACADP Categories', 'advanced-classifieds-and-directory-pro' ),
			array(
				'classname'   => $this->widget_slug.'-class',
				'description' => __( 'A list of "Advanced Classifieds and Directory Pro" Categories.', 'advanced-classifieds-and-directory-pro' )
			)
		);
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11 );
	
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param	  array	   $args	    The array of form elements.
	 * @param	  array    $instance    The current instance of the widget.
	 */
	public function widget( $args, $instance ) {

		$listings_settings   = get_option( 'acadp_listings_settings' );
		$categories_settings = get_option( 'acadp_categories_settings' );

		$query_args = array(
			'parent'         => ! empty( $instance['parent'] ) ? (int) $instance['parent'] : 0,
			'term_id'        => ! empty( $instance['parent'] ) ? (int) $instance['parent'] : 0,
			'hide_empty'     => ! empty( $instance['hide_empty'] ) ? 1 : 0,
			'orderby'        => $categories_settings['orderby'], 
    		'order'          => $categories_settings['order'],
			'show_count'     => ! empty( $instance['show_count'] ) ? 1 : 0,
			'pad_counts'     => isset( $listings_settings['include_results_from'] ) && in_array( 'child_categories', $listings_settings['include_results_from'] ) ? true : false,
			'imm_child_only' => ! empty( $instance['imm_child_only'] ) ? 1 : 0,
			'active_term_id' => 0,
			'ancestors'      => array()
		);	
		
		if( $query_args['imm_child_only'] ) {
		
			$term_slug = get_query_var( 'acadp_category' );
			
			if( '' != $term_slug ) {		
				$term = get_term_by( 'slug', $term_slug, 'acadp_categories' );
        		$query_args['active_term_id'] = $term->term_id;
			
				$query_args['ancestors'] = get_ancestors( $query_args['active_term_id'], 'acadp_categories' );
				$query_args['ancestors'][] = $query_args['active_term_id'];
				$query_args['ancestors'] = array_unique( $query_args['ancestors'] );
			}
			
		}

		$categories = $this->list_categories( $query_args );

		if( ! empty( $categories ) ) {
		
			echo $args['before_widget'];
		
			if( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			include( acadp_get_template( 'acadp-widget-public-categories-display.php', 'categories' ) );
		
			echo $args['after_widget'];
		
		}

	}
	
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param	  array	   $new_instance    The new instance of values to be generated via the update.
	 * @param	  array    $old_instance    The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		$instance['title']          = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['parent']         = isset( $new_instance['parent'] ) ? (int) $new_instance['parent'] : 0;
		$instance['imm_child_only'] = isset( $new_instance['imm_child_only'] ) ? 1 : 0;
		$instance['hide_empty']     = isset( $new_instance['hide_empty'] ) ? 1 : 0;
		$instance['show_count']     = isset( $new_instance['show_count'] ) ? 1 : 0;		
		
		return $instance;

	}
	
	/**
	 * Generates the administration form for the widget.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param	  array    $instance    The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$categories_settings = get_option( 'acadp_categories_settings' );
		
		// Define the array of defaults
		$defaults = array(
			'title'          => __( 'Categories', 'advanced-classifieds-and-directory-pro' ),
			'parent'         => 0,
			'imm_child_only' => 0,
			'hide_empty'     => ! empty( $categories_settings['hide_empty'] ) ? 1 : 0,
			'show_count'     => ! empty( $categories_settings['show_count'] ) ? 1 : 0
		);
		
		// Parse incoming $instance into an array and merge it with $defaults
		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);
			
		// Display the admin form
		include( ACADP_PLUGIN_DIR . 'widgets/categories/views/acadp-widget-admin-categories-display.php' );

	}
	
	/**
 	 * List ACADP categories.
 	 *
 	 * @since    1.0.0
 	 *
 	 * @param    array     $settings    Settings args.
 	 * @return   string                 HTML code that contain categories list.
 	 */
	public function list_categories( $settings ) {

		if( $settings['imm_child_only'] ) {
		
			if( $settings['term_id'] > $settings['parent'] && ! in_array( $settings['term_id'], $settings['ancestors'] ) ) {
				return;
			}
			
		}
		
		$args = array(
			'orderby'      => $settings['orderby'], 
    		'order'        => $settings['order'],
    		'hide_empty'   => $settings['hide_empty'], 
			'parent'       => $settings['term_id'],
			'hierarchical' => ! empty( $settings['hide_empty'] ) ? true : false
  		);
		
		$terms = get_terms( 'acadp_categories', $args );
	
		$html = '';
					
		if( count( $terms ) > 0 ) {	

			$html .= '<ul>';
							
			foreach( $terms as $term ) {
				$settings['term_id'] = $term->term_id;
				
				$count = 0;
				if( ! empty( $settings['hide_empty'] ) || ! empty( $settings['show_count'] ) ) {
					$count = acadp_get_listings_count_by_category( $term->term_id, $settings['pad_counts'] );
					
					if( ! empty( $settings['hide_empty'] ) && 0 == $count ) continue;
				}
			
				$html .= '<li>'; 
				$html .= '<a href="' . acadp_get_category_page_link( $term ) . '">';
				$html .= $term->name;
				if( ! empty( $settings['show_count'] ) ) {
					$html .= ' (' . $count . ')';
				}
				$html .= '</a>';
				$html .= $this->list_categories( $settings );
				$html .= '</li>';	
			}	
			
			$html .= '</ul>';
					
		}		
			
		return $html;

	}
	
	/**
	 * Enqueues widget-specific styles & scripts.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function enqueue_styles_scripts() {		
	
		if( is_active_widget( false, $this->id, $this->id_base, true ) ) {

			wp_enqueue_style( ACADP_PLUGIN_NAME );
			
		}

	}
	
}

add_action( 'widgets_init', create_function( '', 'register_widget("ACADP_Widget_Categories");' ) );
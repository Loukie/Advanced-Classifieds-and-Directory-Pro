<?php

/**
 * Search Page [acadp_search]
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/public
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */
 
// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Public_Search Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Public_Search {

	/**
	 * Get things going.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		
		// Register shortcodes used by the search page
		add_shortcode( "acadp_search_form", array( $this, "run_shortcode_search_form" ) );
		add_shortcode( "acadp_search", array( $this, "run_shortcode_search" ) );

	}
	
	/**
	 * Run the shortcode [acadp_search_form].
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params   array     $atts    an associative array of attributes.
	 */
	public function run_shortcode_search_form( $atts ) {
		
		$general_settings    = get_option( 'acadp_general_settings' );
		$locations_settings  = get_option( 'acadp_locations_settings' );
		$categories_settings = get_option( 'acadp_categories_settings' );
		$page_settings       = get_option( 'acadp_page_settings' );
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies
		if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		// ...
		$id = wp_rand();
		
		$style = 'inline';
		if( ! empty( $atts['style'] ) && 'vertical' == $atts['style'] ) {
			$style = 'vertical';
		}
		
		$has_location = empty( $general_settings['has_location'] ) ? 0 : 1;
		$has_price    = empty( $general_settings['has_price'] )    ? 0 : 1;

		$can_search_by_location      = isset( $atts['location'] )      ? (int) $atts['location']      : $has_location;
		$can_search_by_category      = isset( $atts['category'] )      ? (int) $atts['category']      : 1;
		$can_search_by_custom_fields = isset( $atts['custom_fields'] ) ? (int) $atts['custom_fields'] : 1;
		$can_search_by_price         = isset( $atts['price'] )         ? (int) $atts['price']         : $has_price;
		
		$span_top    = 12 / ( 1 + $can_search_by_category  + $can_search_by_location );
		$span_bottom = 12 / ( $can_search_by_price + 1 );
		
		ob_start();
		include( acadp_get_template( "search/acadp-public-search-form-$style-display.php" ) );
		return ob_get_clean();
		
	}
	
	/**
	 * Run the shortcode [acadp_search].
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params   array     $atts    an associative array of attributes.
	 */
	public function run_shortcode_search( $atts ) {
	
		if( ! isset( $_GET['q'] ) ) {
			return '<span>'.__( 'No Results Found.', 'advanced-classifieds-and-directory-pro' ).'</span>';
		}
		
		$shortcode = 'acadp_search';		

		$general_settings = get_option( 'acadp_general_settings' );
		$listings_settings = get_option( 'acadp_listings_settings' );
		$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
		
		$atts = shortcode_atts( array(
			'view'              => $listings_settings['default_view'],
			'featured'          => 1,
			'filterby'          => '',
			'orderby'           => $listings_settings['orderby'],
			'order'             => $listings_settings['order'],
			'listings_per_page' => ! empty( $listings_settings['listings_per_page'] ) ? $listings_settings['listings_per_page'] : -1,
			'pagination'        => 1,
			'header'            => 1
		), $atts );
		
		$view = acadp_get_listings_current_view_name( $atts['view'] );
			
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies
		if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		if( 'map' == $view ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-markerclusterer' );
		}

		// ...		
		$can_show_header           = empty( $listings_settings['display_in_header'] ) ? 0 : (int) $atts['header'];
		$pre_content               = '';
		$can_show_listings_count   = $can_show_header && in_array( 'listings_count', $listings_settings['display_in_header'] )   ? true : false;
		$can_show_views_selector   = $can_show_header && in_array( 'views_selector', $listings_settings['display_in_header'] )   ? true : false;
		$can_show_orderby_dropdown = $can_show_header && in_array( 'orderby_dropdown', $listings_settings['display_in_header'] ) ? true : false;
		
		$can_show_date       = isset( $listings_settings['display_in_listing'] ) && in_array( 'date', $listings_settings['display_in_listing'] )     ? true : false;
		$can_show_user       = isset( $listings_settings['display_in_listing'] ) && in_array( 'user', $listings_settings['display_in_listing'] )     ? true : false;
		$can_show_category   = isset( $listings_settings['display_in_listing'] ) && in_array( 'category', $listings_settings['display_in_listing'] ) ? true : false;
		$can_show_views      = isset( $listings_settings['display_in_listing'] ) && in_array( 'views', $listings_settings['display_in_listing'] )    ? true : false;
		$can_show_images     = empty( $general_settings['has_images'] ) ? false : true;
		$has_featured		 = apply_filters( 'acadp_has_featured', empty( $featured_listing_settings['enabled'] ) ? false : true );
		if( $has_featured ) {
			$has_featured = $atts['featured'];
		}		
			
		$current_order       = acadp_get_listings_current_order( $atts['orderby'].'-'.$atts['order'] );
		$can_show_pagination = (int) $atts['pagination'];
		
		$has_price = empty( $general_settings['has_price'] ) ? false : true;
		$can_show_price = false;
		
		if( $has_price ) {
			$can_show_price = isset( $listings_settings['display_in_listing'] ) && in_array( 'price', $listings_settings['display_in_listing'] ) ? true : false;
		}
			
		$has_location = empty( $general_settings['has_location'] ) ? false : true;
		$can_show_location = false;
		
		if( $has_location ) {
			$can_show_location = isset( $listings_settings['display_in_listing'] ) && in_array( 'location', $listings_settings['display_in_listing'] ) ? true : false;
		}
			
		$span = 12;
		if( $can_show_images ) $span = $span - 2;
		if( $can_show_price ) $span = $span - 3;
		$span_middle = 'col-md-'.$span;

		// Define the query
		$paged = acadp_get_page_number();
		$search_query = sanitize_text_field( $_GET['q'] );
		
		$args = array(				
			'post_type'      => 'acadp_listings',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $atts['listings_per_page'],
			'paged'          => $paged,
			's'              => $search_query,
  		);
			
		// Define tax queries( only if applicable )
		$tax_queries = array();		
		
		if( isset( $_GET['c'] ) && (int) $_GET['c'] > 0 ) {
			
			$tax_queries[] = array(
				'taxonomy'         => 'acadp_categories',
				'field'            => 'term_id',
				'terms'            => (int) $_GET['c'],
				'include_children' => isset( $listings_settings['include_results_from'] ) && in_array( 'child_categories', $listings_settings['include_results_from'] ) ? true : false,
			);
		
		}
		
		if( $has_location ) {
		
			if( isset( $_GET['l'] ) && (int) $_GET['l'] > 0 ) {
			
				$tax_queries[] = array(
					'taxonomy'         => 'acadp_locations',
					'field'            => 'term_id',
					'terms'            => (int) $_GET['l'],
					'include_children' => isset( $listings_settings['include_results_from'] ) && in_array( 'child_locations', $listings_settings['include_results_from'] ) ? true : false,
				);
		
			} else if( $general_settings['base_location'] > 0 ) {
				
				$tax_queries[] = array(
					'taxonomy'         => 'acadp_locations',
					'field'            => 'term_id',
					'terms'            => $general_settings['base_location'],
					'include_children' => true,
				);
				
			}
			
		}
		
		$count_tax_queries = count( $tax_queries );		
		if( $count_tax_queries ) {
			$args['tax_query'] = ( $count_tax_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $tax_queries ) : $tax_queries;
		}
		
		// Define meta queries( only if applicable )
		$meta_queries = array();
		
		if( 'map' == $view ) {
			$meta_queries['hide_map'] = array(
				'key'     => 'hide_map',
				'value'   => 0,
				'compare' => '='
			);
		}
		
		if( isset( $_GET['cf'] ) ) {
			
			$cf = array_filter( $_GET['cf'] );
			
			foreach( $cf as $key => $values ) {
			
				if( is_array( $values ) ) {
				
					if( count( $values ) > 1 ) {
					
						$sub_meta_queries = array();
						
						foreach( $values as $value ) {
							$sub_meta_queries[] = array(
								'key'		=> $key,
								'value'		=> sanitize_text_field( $value ),
								'compare'	=> 'LIKE'
							);
						}
						
						$meta_queries[] = array_merge( array( 'relation' => 'OR' ), $sub_meta_queries );
					
					} else {
					
						$meta_queries[] = array(
							'key'		=> $key,
							'value'		=> sanitize_text_field( $values[0] ),
							'compare'	=> 'LIKE'
						);
					
					}
						
				} else {
					
					$field_type = get_post_meta( $key, 'type', true );					
					$operator = ( 'text' == $field_type || 'textarea' == $field_type || 'url' == $field_type ) ? 'LIKE' : '=';
					$meta_queries[] = array(
						'key'		=> $key,
						'value'		=> sanitize_text_field( $values ),
						'compare'	=> $operator
					);
					
				}
				
			}
				
		}
		
		if( $has_price ) {
		
			$meta_queries['price'] = array(
				'key'     => 'price',
				'type'    => 'NUMERIC',
				'compare' => 'EXISTS',
			);
						
			if( isset( $_GET['price'] ) ) {	
			
				$price = array_filter( $_GET['price'] );
		
				if( $n = count( $price ) ) {
				
					if( 2 == $n ) {
						$meta_queries[] = array(
							'key'		=> 'price',
							'value'		=> array_map( 'intval', $price ),
							'type'      => 'NUMERIC',
							'compare'	=> 'BETWEEN'
						);
					} else {
						if( empty( $price[0] ) ) {
							$meta_queries[] = array(
								'key'		=> 'price',
								'value'		=> (int) $price[1],
								'type'      => 'NUMERIC',
								'compare'	=> '<='
							);
						} else {
							$meta_queries[] = array(
								'key'		=> 'price',
								'value'		=> (int) $price[0],
								'type'      => 'NUMERIC',
								'compare'	=> '>='
							);
						}
					}
			
				}	
					
			}
			
		}
			
		if( $has_featured ) {
			
			if( 'featured' == $atts['filterby'] ) {
				$meta_queries['featured'] = array(
					'key'     => 'featured',
					'value'   => 1,
					'compare' => '='
				);
			} else {
				$meta_queries['featured'] = array(
					'key'     => 'featured',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				);
			}
				
		}

		switch( $current_order ) {
			case 'title-asc' :
				if( $has_featured ) {
					$args['orderby']  = array(
						'featured' => 'DESC',
						'title'    => 'ASC',
					);
				} else {
					$args['orderby'] = 'title';
					$args['order']   = 'ASC';
				};
				break;
			case 'title-desc' :
				if( $has_featured ) {
					$args['orderby']  = array(
						'featured' => 'DESC',
						'title'    => 'DESC',
					);
				} else {
					$args['orderby'] = 'title';
					$args['order']   = 'DESC';
				};
				break;
			case 'date-asc' :
				if( $has_featured ) {
					$args['orderby']  = array(
						'featured' => 'DESC',
						'date'     => 'ASC',
					);
				} else {
					$args['orderby'] = 'date';
					$args['order']   = 'ASC';
				};
				break;
			case 'date-desc' :
				if( $has_featured ) {
					$args['orderby']  = array(
						'featured' => 'DESC',
						'date'     => 'DESC',
					);
				} else {
					$args['orderby'] = 'date';
					$args['order']   = 'DESC';
				};
				break;
			case 'price-asc' :
				if( $has_featured ) {
					$args['orderby']  = array( 
						'featured' => 'DESC',
						'price'    => 'ASC',
					);
				} else {
					$args['meta_key'] = 'price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'ASC';
				};
				break;
			case 'price-desc' :
				if( $has_featured ) {
					$args['orderby']  = array( 
						'featured' => 'DESC',
						'price'    => 'DESC',
					);
				} else {
					$args['meta_key'] = 'price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
				};
				break;
			case 'views-asc' :
				if( $has_featured ) {
					$meta_queries['views'] = array(
						'key'     => 'views',
						'type'    => 'NUMERIC',
						'compare' => 'EXISTS',
					);
		
					$args['orderby']  = array( 
						'featured' => 'DESC',
						'views'    => 'ASC',
					);
				} else {
					$args['meta_key'] = 'views';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'ASC';
				};
				break;
			case 'views-desc' :
				if( $has_featured ) {
					$meta_queries['views'] = array(
						'key'     => 'views',
						'type'    => 'NUMERIC',
						'compare' => 'EXISTS',
					);
					
					$args['orderby']  = array( 
						'featured' => 'DESC',
						'views'    => 'DESC',
					);
				} else {
					$args['meta_key'] = 'views';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
				};
				break;
			case 'rand' :
				if( $has_featured ) {
					$args['meta_key'] = 'featured';
					$args['orderby']  = 'meta_value_num rand';
				} else {
					$args['orderby'] = 'rand';
				};
				break;
		}
		
		$count_meta_queries = count( $meta_queries );
		if( $count_meta_queries ) {
			$args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array( 'relation' => 'AND' ), $meta_queries ) : $meta_queries;
		}

		$acadp_query = new WP_Query( $args );
			
		// Start the Loop
		global $post;
		
		// Process output
		if( $acadp_query->have_posts() ) {
			
			ob_start();
			include( acadp_get_template( "listings/acadp-public-listings-$view-display.php" ) );
			return ob_get_clean();
				
		} else {
		
			return '<span>'.__( 'No Results Found.', 'advanced-classifieds-and-directory-pro' ).'</span>';
			
		}
		
	}
	
	/**
	 * Display custom fields.
	 *
	 * @since     1.0.0
	 * @access    public
	 *
	 * @param	  int       $term_id    Term ID.
	 * @param	  string    $style      Form style.
	 */
	public function ajax_callback_custom_fields( $term_id = 0, $style = 'vertical' ) {
	
		$ajax = false;
		
		if( isset( $_POST['term_id'] ) ) {
			$ajax = true;
			$term_id = (int) $_POST['term_id'];
		}
		
		if( isset( $_POST['style'] ) ) {
			$style = sanitize_text_field( $_POST['style'] );
		}
		
		// Get custom fields
		$custom_field_ids = acadp_get_custom_field_ids( $term_id );
		
		$args = array(
			'post_type'      => 'acadp_fields',
			'post_status'    => 'publish',
			'posts_per_page' => -1,	
			'post__in'		 => $custom_field_ids,		
			'meta_query'     => array(
				array(
					'key'	  => 'searchable',
					'value'	  => 1,
					'type'    => 'NUMERIC',
					'compare' => '='
				),
			),
			'meta_key'       => 'order',
			'orderby'        => 'meta_value_num',			
			'order'          => 'ASC',
	  	);		
		$acadp_query = new WP_Query( $args );
		
		// Start the Loop
		global $post;
		
		// Process output
		ob_start();
		include( acadp_get_template( "search/acadp-public-custom-fields-$style-display.php" ) );
		wp_reset_postdata(); // Restore global post data stomped by the_post()
		$output = ob_get_clean();
			
		print $output;
		
		if( $ajax ) {
			wp_die();
		}
	
	}

}

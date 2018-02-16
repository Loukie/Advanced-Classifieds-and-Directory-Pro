<?php

/**
 * User Page
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
 * ACADP_Public_User Class
 *
 * @since    1.0.0
 * @access   public 
 */
class ACADP_Public_User {

	/**
	 * Get things going.
	 *
	 * @since    1.0.0 
	 * @access   public 
	 */ 
	public function __construct() {
		
		// Register shortcodes used by the user page
		add_shortcode( "acadp_user_listings", array( $this, "run_shortcode_user_listings" ) );
		add_shortcode( "acadp_user_dashboard", array( $this, "run_shortcode_user_dashboard" ) );
		add_shortcode( "acadp_listing_form", array( $this, "run_shortcode_listing_form" ) );
		add_shortcode( "acadp_manage_listings", array( $this, "run_shortcode_manage_listings" ) );
		add_shortcode( "acadp_favourite_listings", array( $this, "run_shortcode_favourite_listings" ) );

	}
	
	/**
	 * Manage form submissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function manage_actions() {	

		if( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['acadp_listing_nonce'] ) && wp_verify_nonce( $_POST['acadp_listing_nonce'], 'acadp_save_listing' ) ) {
		
			if( isset( $_POST['post_id'] ) ) {
			
				if( ! acadp_current_user_can('edit_acadp_listing', (int) $_POST['post_id']) ) {
					return;
				}
				
			} else {
			
				if( ! acadp_current_user_can('edit_acadp_listings') ) {
					return;
				}
			
				if( ! acadp_is_human('listing') ) {
					echo '<span>'.__( 'Invalid Captcha: Please try again.', 'advanced-classifieds-and-directory-pro' ).'</span>';
					exit();
				}
			
			}			
			
			$this->save_listing();
		
		}
	
	}
	
	/**
	 * Parse request to find correct WordPress query.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param	 WP_Query    $wp    WordPress Query object.
	 */
	public function parse_request( $wp ) {
	
		if( array_key_exists( 'acadp_action', $wp->query_vars ) && array_key_exists( 'acadp_listing', $wp->query_vars ) && (int) $wp->query_vars['acadp_listing'] > 0 ) {
		
			$id = (int) $wp->query_vars['acadp_listing'];
			
			if( 'renew' == $wp->query_vars['acadp_action'] ) {
				if( ! acadp_current_user_can('edit_acadp_listing', $id) ) {
					return;
				}
				
				$this->renew_listing( $id );
			}
			
			if( 'delete' == $wp->query_vars['acadp_action'] ) {
				if( ! acadp_current_user_can('delete_acadp_listing', $id) ) {
					return;
				}
				
				$this->delete_listing( $id );
			}
			
			if( 'remove-favourites' == $wp->query_vars['acadp_action'] ) {
				$this->remove_favourites( $id );
			}
			
    	}
		
	}
	
	/**
	 * Process the shortcode [acadp_user_listings].
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params   array     $atts    an associative array of attributes.
	 */
	public function run_shortcode_user_listings( $atts ) {
	
		$shortcode = 'acadp_user_listings';
		
		$user_slug = get_query_var( 'acadp_user' );
		if( '' == $user_slug ) {
			if( ! empty( $atts['id'] ) ) {
				$user_slug = get_the_author_meta( 'user_nicename', (int) $atts['id'] );	
			} else if( is_user_logged_in() ) {
				$user_slug = get_the_author_meta( 'user_nicename', get_current_user_id() );				
			}
		}
		
		if( '' != $user_slug ) {
		
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
			$has_featured        = apply_filters( 'acadp_has_featured', empty( $featured_listing_settings['enabled'] ) ? false : true );
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
			
			$args = array(				
				'post_type'      => 'acadp_listings',
				'post_status'    => 'publish',
				'posts_per_page' => (int) $atts['listings_per_page'],
				'paged'          => $paged,
				'author_name'    => $user_slug,
	  		);
			
			if( $has_location && $general_settings['base_location'] > 0 ) {
			
				$args['tax_query'] = array(
					array(
						'taxonomy'         => 'acadp_locations',
						'field'            => 'term_id',
						'terms'            => $general_settings['base_location'],
						'include_children' => true,
					),
				);
				
			}
			
			$meta_queries = array();
			
			if( 'map' == $view ) {
				$meta_queries['hide_map'] = array(
					'key'     => 'hide_map',
					'value'   => 0,
					'compare' => '='
				);
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
						$args['meta_key'] = 'featured';
						$args['orderby']  = array(
							'meta_value_num' => 'DESC',
							'title'          => 'ASC',
						);
					} else {
						$args['orderby'] = 'title';
						$args['order']   = 'ASC';
					};
					break;
				case 'title-desc' :
					if( $has_featured ) {
						$args['meta_key'] = 'featured';
						$args['orderby']  = array(
							'meta_value_num' => 'DESC',
							'title'          => 'DESC',
						);
					} else {
						$args['orderby'] = 'title';
						$args['order']   = 'DESC';
					};
					break;
				case 'date-asc' :
					if( $has_featured ) {
						$args['meta_key'] = 'featured';
						$args['orderby']  = array(
							'meta_value_num' => 'DESC',
							'date'           => 'ASC',
						);
					} else {
						$args['orderby'] = 'date';
						$args['order']   = 'ASC';
					};
					break;
				case 'date-desc' :
					if( $has_featured ) {
						$args['meta_key'] = 'featured';
						$args['orderby']  = array(
							'meta_value_num' => 'DESC',
							'date'           => 'DESC',
						);
					} else {
						$args['orderby'] = 'date';
						$args['order']   = 'DESC';
					};
					break;
				case 'price-asc' :
					if( $has_featured ) {
						$meta_queries['price'] = array(
							'key'     => 'price',
							'type'    => 'NUMERIC',
							'compare' => 'EXISTS',
						);

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
						$meta_queries['price'] = array(
							'key'     => 'price',
							'type'    => 'NUMERIC',
							'compare' => 'EXISTS',
						);

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
		
	}
	
	/**
	 * Process the shortcode [acadp_user_dashboard].
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run_shortcode_user_dashboard() {
		
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}		

		$shortcode = 'acadp_user_dashboard';
		
		$userid = get_current_user_id();
		$user = get_userdata( $userid );
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies
		if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		// ...		
		ob_start();
		do_action( 'acadp_before_user_dashboard_content' );
		include( acadp_get_template( "user/acadp-public-user-dashboard-display.php" ) );
		do_action( 'acadp_after_user_dashboard_content' );
		return ob_get_clean();
		
	}
	
	/**
	 * Process the shortcode [acadp_listing_form].
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run_shortcode_listing_form() {
		
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}		
		
		$post_id  = 'edit' == get_query_var( 'acadp_action' ) ? get_query_var( 'acadp_listing', 0 ) : 0;
		$has_permission = true;
		
		if( $post_id > 0 ) {
			if( ! acadp_current_user_can('edit_acadp_listing', $post_id) ) $has_permission = false;
		} else if( ! acadp_current_user_can('edit_acadp_listings') ) {
			$has_permission = false;
		}
		
		if( ! $has_permission ) {
			return __( 'You do not have sufficient permissions to access this page.', 'advanced-classifieds-and-directory-pro' );
		}
		
		$shortcode = 'acadp_listing_form';
		
		$general_settings    = get_option( 'acadp_general_settings' );	
		$locations_settings  = get_option( 'acadp_locations_settings' );
		$categories_settings = get_option( 'acadp_categories_settings' );
		$recaptcha_settings  = get_option( 'acadp_recaptcha_settings' );		
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies		
		wp_enqueue_script( 'jquery-form', array('jquery'), false, true );
		
		wp_enqueue_script( "jquery-ui-sortable" );
		
		if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-google-map' );
		
		if( isset( $recaptcha_settings['forms'] ) && in_array( 'listing', $recaptcha_settings['forms'] ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME . "-recaptcha" );
		}

		// ...
		$has_draft = 1;
		$category  = 0;
		$default_location = '';

		$disable_parent_categories = empty( $general_settings['disable_parent_categories'] ) ? false : true;		
		$editor = ! empty( $general_settings['text_editor'] ) ? $general_settings['text_editor'] : 'wp_editor';
		
		$can_add_price    = empty( $general_settings['has_price'] )    ? false : true;
		$can_add_images   = empty( $general_settings['has_images'] )   ? false : true;
		$can_add_video    = empty( $general_settings['has_video'] )    ? false : true;	
		$can_add_location = empty( $general_settings['has_location'] ) ? false : true;
		$has_map          = empty( $general_settings['has_map'] )      ? false : true;
		
		$images_limit = apply_filters( 'acadp_images_limit', (int) $general_settings['maximum_images_per_listing'], $post_id );
		
		if( $can_add_location ) {
			$location = ( $general_settings['default_location'] > 0 ) ? $general_settings['default_location'] : $general_settings['base_location'];
			if( $location > 0 ) {
				$term = get_term_by( 'id', $location, 'acadp_locations' );
				$default_location = $term->name;
			}
		}

		if( $post_id > 0 ) {
			
			$post = get_post( $post_id );
			setup_postdata( $post );
			
			$post_meta = get_post_meta( $post_id);
			
			if( $post->post_status !== 'draft' ) {
				$has_draft = 0;
			}
			
			$category = wp_get_object_terms( $post_id, 'acadp_categories', array( 'fields' => 'ids' ) );
			$category = $category[0];
			
			if( $can_add_location ) {
				$location = wp_get_object_terms( $post_id, 'acadp_locations', array( 'fields' => 'ids' ) );
				$location = ! empty( $location ) ? $location[0] : -1;
			}
			
		}
		
		ob_start();
		include( acadp_get_template( "user/acadp-public-edit-listing-display.php" ) );
		wp_reset_postdata(); // Restore global post data stomped by the_post()
		return ob_get_clean();
	
	}
	
	/**
	 * Display custom fields.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param	 int    $post_id    Post ID.
	 * @param	 int    $term_id    Term ID.
	 */
	public function ajax_callback_custom_fields( $post_id = 0, $term_id = 0 ) {
	
		$ajax = false;
		
		if( isset( $_POST['term_id'] ) ) {
			$ajax = true;
			$post_id = (int) $_POST['post_id'];
			$term_id = (int) $_POST['term_id'];
		}
		
		// Get post meta for the given post_id
		$post_meta = get_post_meta( $post_id  );
		
		// Get custom fields
		$custom_field_ids = acadp_get_custom_field_ids( $term_id );
		
		$args = array(
			'post_type'      => 'acadp_fields',
			'post_status'    => 'publish',
			'posts_per_page' => -1,		
			'post__in'		 => $custom_field_ids,	
			'meta_key'       => 'order',
			'orderby'        => 'meta_value_num',			
			'order'          => 'ASC',
	  	);
		
		$acadp_query = new WP_Query( $args );
		
		// Start the Loop
		global $post;
		
		// Process output
		ob_start();
		include( acadp_get_template( "user/acadp-public-custom-fields-display.php" ) );
		wp_reset_postdata(); // Restore global post data stomped by the_post()
		$output = ob_get_clean();
			
		print $output;
		
		if( $ajax ) {
			wp_die();
		}
	
	}
	
	/**
	 * Upload image.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function ajax_callback_image_upload() {
			
		$data = array();

    	if( $_FILES ) {	
		
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
			
			$files   = $_FILES['acadp_image'];
       		$post_id = 0;
			
			foreach( $files['name'] as $index => $value ) {
			
				 if( $files['name'][ $index ] ) {
				 
				 	$data[ $index ] = array(
						'error'   => 0,
						'message' => ''
					);
					
				 	$file = array(
						'name'     => $files['name'][ $index ],
						'type'     => $files['type'][ $index ],
						'tmp_name' => $files['tmp_name'][ $index ],
						'error'    => $files['error'][ $index ],
						'size'     => $files['size'][ $index ]
					);
					
					if( getimagesize( $file['tmp_name'] ) === FALSE ) {
						$data[ $index ]['error']   = 1;
						$data[ $index ]['message'] = __( 'File is not an image.', 'advanced-classifieds-and-directory-pro' );
					} 
					
					if( ! in_array( $file['type'], array( 'image/jpeg', 'image/jpg', 'image/png' ) ) ) {
						$data[ $index ]['error']   = 1;
						$data[ $index ]['message'] = __( 'Invalid file format', 'advanced-classifieds-and-directory-pro' );
					}
					
					if( $file['error'] !== UPLOAD_ERR_OK ) {
						$data[ $index ]['error']   = 1;
						$data[ $index ]['message'] = $file['error'];
					} 
					
					if( 0 == $data[ $index ]['error'] ) {
						
						$_FILES = array( 'acadp_image' => $file );
						
						$_FILES['acadp_image'] = acadp_exif_rotate( $_FILES['acadp_image'] );
						$img_id = media_handle_upload( 'acadp_image', $post_id );
						
						$data[ $index ]['id'] = $img_id;
						
						$image = wp_get_attachment_image_src( $img_id );
						$data[ $index ]['url'] = $image[0];
						
					} 

				}
								 				 
			}
			
    	}
				
  		echo wp_json_encode( $data );
  		wp_die();
	
	}
	
	/**
	 * Delete an attachment.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function ajax_callback_delete_attachment() {
	
		if( isset( $_POST['attachment_id'] ) ) {
			wp_delete_attachment( $_POST['attachment_id'], true );
		}
		
		wp_die();
	
	}
	
	/**
	 * Save Listing.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function save_listing() {
		
		$is_initial_submission = 1;
		$is_listing_edited     = 0;
		
		$general_settings = get_option( 'acadp_general_settings' );
		
		$new_listing_status = apply_filters( 'acadp_new_listing_status', $general_settings['new_listing_status'] );
		$post_status = $new_listing_status;
		
		if( isset( $_POST['post_id'] ) ) {
			$post_id = (int) $_POST['post_id'];
			
			$post_status = get_post_status( $post_id );
			if( 'pending' === $post_status ) {
				// redirect
    			wp_redirect( $redirect_url );
   				exit();	
			} else if( 'draft' === $post_status ) {
				$post_status = $new_listing_status;	
			} else {
				$post_status = $general_settings['edit_listing_status'];
				$is_initial_submission = 0;	
				$is_listing_edited     = 1;
			}
		}
		
		if( isset( $_POST['action'] ) && __( 'Save Draft', 'advanced-classifieds-and-directory-pro' ) == $_POST['action'] ) {
			$post_status = 'draft';
			$is_initial_submission = 0;
		}
		
		// Add the content of the form to $post as an array
		$post_array = array(
			'post_title'   => wp_strip_all_tags( $_POST['title'] ),
			'post_name'    => sanitize_title( $_POST['title'] ),
			'post_content' => $_POST['description'],
			'post_status'  => $post_status,
			'post_type'	   => 'acadp_listings'
		);
		
		if( isset( $_POST['post_id'] ) ) {
		
			//update the existing post
			$post_array['ID'] = $post_id;
			wp_update_post( $post_array );
			
		} else {
			
			//save the new post
			$post_array['post_author'] = get_current_user_id();
			$post_id = wp_insert_post( $post_array );
			
		}
		
		if( $post_id ) {
		
			// insert category taxonomy
    		wp_set_object_terms( $post_id, (int) $_POST['acadp_category'], 'acadp_categories' );
			
			// insert custom fields
			if( isset( $_POST['acadp_fields'] ) ) {
			
				foreach( $_POST['acadp_fields'] as $key => $value ) {
					$type = get_post_meta( $key, 'type', true );
					
					switch( $type ) {
						case 'text' :
							$value = sanitize_text_field( $value );
							break;
						case 'textarea' :
							$value = esc_textarea( $value );
							break;	
						case 'select' :
						case 'radio'  :
							$value = sanitize_text_field( $value );
							break;					
						case 'checkbox' :
							$value = array_map( 'esc_attr', $value );
							$value = implode( "\n", $value );
							break;
						case 'url' :
							$value = esc_url_raw( $value );
							break;	
						default :
							$value = sanitize_text_field( $value );
					}
					
					update_post_meta( $post_id, $key, $value );
				}
			
			}
			
			// insert images
			if( ! empty( $general_settings['has_images'] ) && isset( $_POST['images'] ) ) {

				// OK to save meta data	
				$images = array_filter( $_POST['images'] );	
				$images = array_map( 'intval', $images );				

        		if( count( $images ) ) {	
				
					$images_limit = apply_filters( 'acadp_images_limit', (int) $general_settings['maximum_images_per_listing'], $post_id );
					if( $images_limit > 0 ) $images = array_slice( $images, 0, $images_limit );
					
            		update_post_meta( $post_id, 'images', $images );
					set_post_thumbnail( $post_id, $images[0] );
					
        		} else { 
            		delete_post_meta( $post_id, 'images' );
					delete_post_thumbnail( $post_id );
				}
					
			} else {
				
				// Nothing received, all fields are empty, delete option					
				delete_post_meta( $post_id, 'images' );
				delete_post_thumbnail( $post_id );
				
			}
			
			// insert video
			if( ! empty( $general_settings['has_video'] ) && isset( $_POST['video'] ) ) {
				$video = esc_url_raw( $_POST['video'] );
    			update_post_meta( $post_id, 'video', $video );
			}
			
			// insert contact details
			if( ! empty( $general_settings['has_location'] ) ) {
				$address = esc_textarea( $_POST['address'] );
    			update_post_meta( $post_id, 'address', $address );
			
				wp_set_object_terms( $post_id, (int) $_POST['acadp_location'], 'acadp_locations' );
					
				$zipcode = sanitize_text_field( $_POST['zipcode'] );
    			update_post_meta( $post_id, 'zipcode', $zipcode );
				
				$phone = sanitize_text_field( $_POST['phone'] );
    			update_post_meta( $post_id, 'phone', $phone );
				
				$email = sanitize_email( $_POST['email'] );
    			update_post_meta( $post_id, 'email', $email );
				
				$website = esc_url_raw( $_POST['website'] );
    			update_post_meta( $post_id, 'website', $website );
				
				$latitude = isset( $_POST['latitude'] ) ? sanitize_text_field( $_POST['latitude'] ) : '';
    			update_post_meta( $post_id, 'latitude', $latitude );
				
				$longitude = isset( $_POST['longitude'] ) ? sanitize_text_field( $_POST['longitude'] ) : '';
    			update_post_meta( $post_id, 'longitude', $longitude );

				$hide_map = isset( $_POST['hide_map'] ) ? (int) $_POST['hide_map'] : 0;
    			update_post_meta( $post_id, 'hide_map', $hide_map );
			}
			
			if( ! empty( $general_settings['has_price'] ) ) {
				$price = acadp_sanitize_amount( $_POST['price'] );
    			update_post_meta( $post_id, 'price', $price );
			}			
			
			// redirect after save
			$redirect_url = home_url();
			
			if( 'draft' == $post_status ) {
				$redirect_url = acadp_get_listing_edit_page_link( $post_id );
			} else {
				$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
				
				$has_checkout_page = 0;
				
				if( ! empty( $featured_listing_settings['enabled'] ) && $featured_listing_settings['price'] > 0 ) {
					$has_checkout_page = 1;
				}
				
				$has_checkout_page = apply_filters( 'acadp_has_checkout_page', $has_checkout_page, $post_id );				
				
				$redirect_url = ( $is_initial_submission && $has_checkout_page ) ? acadp_get_checkout_page_link( $post_id ) : acadp_get_manage_listings_page_link();
				
			}
			
			// send emails
			if( $is_initial_submission ) {	

				update_post_meta( $post_id, 'featured', 0 );
				update_post_meta( $post_id, 'views', 0 );
				update_post_meta( $post_id, 'listing_status', 'post_status' );
				
				acadp_email_admin_listing_submitted( $post_id );
				acadp_email_listing_owner_listing_submitted( $post_id );
				
				if( 'publish' == $post_status ) {
					$expiry_date = acadp_listing_expiry_date( $post_id );
					update_post_meta( $post_id, 'expiry_date', $expiry_date );
				
					acadp_email_listing_owner_listing_approved( $post_id );
				}
				
			} else if( $is_listing_edited ) {
			
				acadp_email_admin_listing_edited( $post_id );
				
			}
			
			do_action( 'acadp_listing_form_after_save', $post_id );
			
			// redirect
			$redirect_url = apply_filters( 'acadp_listing_form_redirect_url', $redirect_url, $post_id );
    		wp_redirect( $redirect_url );
   			exit();
		
		}
	
	}
	
	/**
	 * Renew Listing.
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param	 int    $post_id    Post ID.
	 */
	private function renew_listing( $post_id ) {	
			 
		// Disable featured
		update_post_meta( $post_id, 'featured', 0 );
		
		// Hook for developers
		do_action( 'acadp_before_renewal', $post_id );
		
		// ...
		$has_paid_submission = apply_filters( 'acadp_has_checkout_page', 0, $post_id, 'submission' );	
		
		if( $has_paid_submission ) {
		 
			$redirect_url = acadp_get_checkout_page_link( $post_id );
		
		} else {
			
			$time = current_time('mysql');
			
			// Update post $post_id
  			$post_array = array(
      			'ID'          	=> $post_id,
      			'post_status' 	=> 'publish',
				'post_date'   	=> $time,
				'post_date_gmt' => get_gmt_from_date( $time )
  			);

			// Update the post into the database
 			wp_update_post( $post_array );
			
			// Update the post_meta into the database
			$old_listing_status = get_post_meta( $post_id, 'listing_status', true );
			if( 'expired' == $old_listing_status ) {
				$expiry_date = acadp_listing_expiry_date( $post_id );
			} else {
				$old_expiry_date = get_post_meta( $post_id, 'expiry_date', true ); 	
				$expiry_date = acadp_listing_expiry_date( $post_id, $old_expiry_date );
			}
			update_post_meta( $post_id, 'expiry_date', $expiry_date );
			update_post_meta( $post_id, 'listing_status', 'post_status' );		
		
			// redirect
			$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
				
			$has_checkout_page = 0;
			if( ! empty( $featured_listing_settings['enabled'] ) && $featured_listing_settings['price'] > 0 ) {
				$has_checkout_page = 1;			
			}
			
			$has_checkout_page = apply_filters( 'acadp_has_checkout_page', $has_checkout_page, $post_id );	
			
			if( $has_checkout_page ) {
				$redirect_url = acadp_get_checkout_page_link( $post_id );
			} else {
				$redirect_url = add_query_arg( 'renew', 'success', acadp_get_manage_listings_page_link() );
			}
		
		}
				
    	wp_redirect( $redirect_url );
   		exit();
	
	}

	/**
	 * Delete Listing.
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param	 int    $post_id    Post ID.
	 */
	private function delete_listing( $post_id ) {
	
		$images = get_post_meta( $post_id, 'images', true );
		
		if( ! empty( $images ) ) {
		
			foreach( $images as $image ) {
				wp_delete_attachment( $image, true );
			}
		
		}
		
		wp_delete_post( $post_id, true );
		
		// redirect
		$redirect_url = acadp_get_manage_listings_page_link();
    	wp_redirect( $redirect_url );
   		exit();
	
	}
	
	/**
	 * Process the shortcode [acadp_manage_listings].
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run_shortcode_manage_listings() {
	
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}		

		if( ! acadp_current_user_can('edit_acadp_listings') ) {
			return __( 'You do not have sufficient permissions to access this page.', 'advanced-classifieds-and-directory-pro' );
		}
		
		$shortcode = 'acadp_manage_listings';
		
		$general_settings          = get_option( 'acadp_general_settings' );
		$listings_settings         = get_option( 'acadp_listings_settings' );
		$page_settings             = get_option( 'acadp_page_settings' );
		$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
		
		$can_show_images = empty( $general_settings['has_images'] ) ? false : true;
		$can_renew       = empty( $general_settings['has_listing_renewal'] ) ? false : true;
		$has_location    = empty( $general_settings['has_location'] ) ? false : true;
			
		$span = 9;
		if( $can_show_images ) $span = 7;
		$span_middle = 'col-md-'.$span;
		
		$can_promote = false;
		if( ! empty( $featured_listing_settings['enabled'] ) && $featured_listing_settings['price'] > 0 ) {
			$can_promote = true;
		}
		$can_promote = apply_filters( 'acadp_can_promote', $can_promote );
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies
		if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );			
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );

		// Define the query
		$paged = acadp_get_page_number();
			
		$args = array(				
			'post_type'      => 'acadp_listings',
			'post_status'    => 'any',
			'posts_per_page' => ! empty( $listings_settings['listings_per_page'] ) ? $listings_settings['listings_per_page'] : -1,
			'paged'          => $paged,
			'author'         => get_current_user_id(),
			's'              => isset( $_REQUEST['u'] ) ? sanitize_text_field( $_REQUEST['u'] ) : ''
	  	);
			
		$acadp_query = new WP_Query( $args );
			
		// Start the Loop
		global $post;
			
		// Process output
		ob_start();
		include( acadp_get_template( "user/acadp-public-manage-listings-display.php" ) );
		wp_reset_postdata(); // Use reset postdata to restore orginal query
		return ob_get_clean();
			
	}
	
	/**
	 * Process the shortcode [acadp_favourite_listings].
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @params   array     $atts    an associative array of attributes.
	 */
	public function run_shortcode_favourite_listings( $atts ) {
	
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}	
		
		$shortcode = 'acadp_favourite_listings';
		
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
		$has_featured	     = apply_filters( 'acadp_has_featured', empty( $featured_listing_settings['enabled'] ) ? false : true );
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
		$favourite_posts = get_user_meta( get_current_user_id(), 'acadp_favourites', true );
			
		$args = array(				
			'post_type'      => 'acadp_listings',
			'post_status'    => 'publish',		
			'posts_per_page' => (int) $atts['listings_per_page'],
			'paged'          => $paged,
			'post__in'       => ! empty( $favourite_posts ) ? $favourite_posts : array(0)
	  	);
		
		if( $has_location && $general_settings['base_location'] > 0 ) {
			
			$args['tax_query'] = array(
				array(
					'taxonomy'         => 'acadp_locations',
					'field'            => 'term_id',
					'terms'            => $general_settings['base_location'],
					'include_children' => true,
				),
			);
				
		}
			
		$meta_queries = array();
			
		if( 'map' == $view ) {
			$meta_queries['hide_map'] = array(
				'key'     => 'hide_map',
				'value'   => 0,
				'compare' => '='
			);
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
					$args['meta_key'] = 'featured';
					$args['orderby']  = array(
						'meta_value_num' => 'DESC',
						'title'          => 'ASC',
					);
				} else {
					$args['orderby'] = 'title';
					$args['order']   = 'ASC';
				};
				break;
			case 'title-desc' :
				if( $has_featured ) {
					$args['meta_key'] = 'featured';
					$args['orderby']  = array(
						'meta_value_num' => 'DESC',
						'title'          => 'DESC',
					);
				} else {
					$args['orderby'] = 'title';
					$args['order']   = 'DESC';
				};
				break;
			case 'date-asc' :
				if( $has_featured ) {
					$args['meta_key'] = 'featured';
					$args['orderby']  = array(
						'meta_value_num' => 'DESC',
						'date'           => 'ASC',
					);
				} else {
					$args['orderby'] = 'date';
					$args['order']   = 'ASC';
				};
				break;
			case 'date-desc' :
				if( $has_featured ) {
					$args['meta_key'] = 'featured';
					$args['orderby']  = array(
						'meta_value_num' => 'DESC',
						'date'           => 'DESC',
					);
				} else {
					$args['orderby'] = 'date';
					$args['order']   = 'DESC';
				};
				break;
			case 'price-asc' :
				if( $has_featured ) {
					$meta_queries['price'] = array(
						'key'     => 'price',
						'type'    => 'NUMERIC',
						'compare' => 'EXISTS',
					);
					
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
					$meta_queries['price'] = array(
						'key'     => 'price',
						'type'    => 'NUMERIC',
						'compare' => 'EXISTS',
					);

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
	 * Remove favourites.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param	 int    $post_id    Post ID.
	 */
	public function remove_favourites( $post_id ) {
	
		$favourites = (array) get_user_meta( get_current_user_id(), 'acadp_favourites', true );
		
		if( in_array( $post_id, $favourites ) ) {
			if( ( $key = array_search( $post_id, $favourites ) ) !== false ) {
    			unset( $favourites[ $key ] );
			}
		}
		
		$favourites = array_filter( $favourites );
		$favourites = array_values( $favourites );
		
		delete_user_meta( get_current_user_id(), 'acadp_favourites' );
		update_user_meta( get_current_user_id(), 'acadp_favourites', $favourites );

		// redirect
		$redirect_url = acadp_get_favourites_page_link();
    	wp_redirect( $redirect_url );
   		exit();
		
	}

}

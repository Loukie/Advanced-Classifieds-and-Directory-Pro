<?php

/**
 * Listings
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
 * ACADP_Admin_Listings Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Admin_Listings { 
	
	/**
	 * Register a custom post type "acadp_listings".
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_custom_post_type() {

		$labels = array(
			'name'                => _x( 'Listings', 'Post Type General Name', 'advanced-classifieds-and-directory-pro' ),
			'singular_name'       => _x( 'Listing', 'Post Type Singular Name', 'advanced-classifieds-and-directory-pro' ),
			'menu_name'           => __( 'Classifieds & Directory', 'advanced-classifieds-and-directory-pro' ),
			'name_admin_bar'      => __( 'Listing', 'advanced-classifieds-and-directory-pro' ),
			'all_items'           => __( 'All Listings', 'advanced-classifieds-and-directory-pro' ),
			'add_new_item'        => __( 'Add New Listing', 'advanced-classifieds-and-directory-pro' ),
			'add_new'             => __( 'Add New', 'advanced-classifieds-and-directory-pro' ),
			'new_item'            => __( 'New Listing', 'advanced-classifieds-and-directory-pro' ),
			'edit_item'           => __( 'Edit Listing', 'advanced-classifieds-and-directory-pro' ),
			'update_item'         => __( 'Update Listing', 'advanced-classifieds-and-directory-pro' ),
			'view_item'           => __( 'View Listing', 'advanced-classifieds-and-directory-pro' ),
			'search_items'        => __( 'Search Listing', 'advanced-classifieds-and-directory-pro' ),
			'not_found'           => __( 'Not found', 'advanced-classifieds-and-directory-pro' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'advanced-classifieds-and-directory-pro' ),
		);
		
		$supports = array( 'title', 'editor', 'author' );		
		
		$general_settings = get_option( 'acadp_general_settings' );		
		if( ! empty( $general_settings['has_comment_form'] ) ) {
			array_push( $supports, 'comments' );
		}
		
		$args = array(
			'label'               => __( 'acadp_listings', 'advanced-classifieds-and-directory-pro' ),
			'description'         => __( 'Post Type Description', 'advanced-classifieds-and-directory-pro' ),
			'labels'              => $labels,
			'supports'            => $supports,
			'taxonomies'          => array( '' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => current_user_can( 'administrator' ) ? true : false,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'acadp_listing',
			'map_meta_cap'        => true,
		);
		
		$permalink_settings = get_option( 'acadp_permalink_settings' );
		if( isset( $permalink_settings['listing'] ) ) {
			$args['rewrite'] = array(
				'slug' => $permalink_settings['listing']
			);
		}
				
		register_post_type( 'acadp_listings', $args ); 

	}
	
	/**
	 * Remove meta boxes.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function remove_meta_boxes() {
		
		remove_meta_box( 'acadp_categoriesdiv', 'acadp_listings', 'side' );
		remove_meta_box( 'acadp_locationsdiv', 'acadp_listings', 'side' );
		
	}
	
	/**
	 * Adds custom meta fields in Publish meta box.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function post_submitbox_misc_actions() {
	
		global $post, $post_type;
		
		if( 'acadp_listings' == $post_type ) {
			$featured_settings = get_option( 'acadp_featured_listing_settings' );
			$has_featured = apply_filters( 'acadp_has_featured', isset( $featured_settings['enabled'] ) );
			
			$post_meta = get_post_meta( $post->ID );
			
			$never_expires = ! empty( $post_meta['never_expires'][0] ) ? 1 : 0;
			
			if( isset( $post_meta['expiry_date'] ) ) {
				$expiry_date = acadp_parse_mysql_date_format( $post_meta['expiry_date'][0] );
			}

			// Add a nonce field so we can check for it later
    		wp_nonce_field( 'acadp_save_listing_submitbox', 'acadp_listing_submitbox_nonce' );

			require_once ACADP_PLUGIN_DIR . 'admin/partials/listings/acadp-admin-listing-submitbox-display.php';
		}
		
	}
	
	/**
	 * Register meta boxes.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_meta_boxes() {		

		$general_settings = get_option( 'acadp_general_settings' );
		
		add_meta_box( 'acadp-listing-details', __( 'Listing Details', 'advanced-classifieds-and-directory-pro' ), array( $this, 'display_meta_box_listing_details' ), 'acadp_listings', 'normal', 'high' );
		
		if( ! empty( $general_settings['has_location'] ) ) {
	    	add_meta_box( 'acadp-contact-details', __( 'Contact Details', 'advanced-classifieds-and-directory-pro' ), array( $this, 'display_meta_box_contact_details' ), 'acadp_listings', 'normal', 'high' );
		}
		
		if( ! empty( $general_settings['has_images'] ) ) {
			add_meta_box( 'acadp-listing-images', __( 'Images', 'advanced-classifieds-and-directory-pro' ), array( $this, 'display_meta_box_listing_images' ), 'acadp_listings', 'normal', 'high' );
		}
		
		if( ! empty( $general_settings['has_video'] ) ) {
			add_meta_box( 'acadp-listing-video', __( 'Video', 'advanced-classifieds-and-directory-pro' ), array( $this, 'display_meta_box_listing_video' ), 'acadp_listings', 'normal', 'high' ); 
		}
		
	}
	
	/**
	 * Display a listing details meta box.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Post    $post    WordPress Post object
	 */
	public function display_meta_box_listing_details( $post ) {
	
		$general_settings = get_option( 'acadp_general_settings' );
		
		$disable_parent_categories = empty( $general_settings['disable_parent_categories'] ) ? false : true;
		$has_price = empty( $general_settings['has_price'] ) ? false : true;
		
		$post_meta = get_post_meta( $post->ID );
		
		$category = wp_get_object_terms( $post->ID, 'acadp_categories', array( 'fields' => 'ids' ) );		
		
		// Add a nonce field so we can check for it later
    	wp_nonce_field( 'acadp_save_listing_details', 'acadp_listing_details_nonce' );
		
		require_once ACADP_PLUGIN_DIR . 'admin/partials/listings/acadp-admin-listing-details-display.php';

	}
	
	/**
	 * Display a contact details meta box.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Post    $post    WordPress Post object
	 */
	public function display_meta_box_contact_details( $post ) {
	
		$post_meta = get_post_meta( $post->ID );
		
		$general_settings = get_option( 'acadp_general_settings' );
		
		$location = wp_get_object_terms( $post->ID, 'acadp_locations', array( 'fields' => 'ids' ) );
		$location = count( $location ) ? $location[0] : $general_settings['default_location'];
		
		// Add a nonce field so we can check for it later
    	wp_nonce_field( 'acadp_save_contact_details', 'acadp_contact_details_nonce' );
		
		require_once ACADP_PLUGIN_DIR . 'admin/partials/listings/acadp-admin-contact-details-display.php';

	}
	
	/**
	 * Display a meta box to add images.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Post    $post    WordPress Post object
	 */
	public function display_meta_box_listing_images( $post ) {

		$general_settings = get_option( 'acadp_general_settings' );
		
		$post_meta = get_post_meta( $post->ID );
			
		// Add a nonce field so we can check for it later
    	wp_nonce_field( 'acadp_save_listing_images', 'acadp_listing_images_nonce' );
		
		require_once ACADP_PLUGIN_DIR . 'admin/partials/listings/acadp-admin-listing-images-display.php';

	}
	
	/**
	 * Display a meta box to add video.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Post    $post    WordPress Post object
	 */
	public function display_meta_box_listing_video( $post ) {
		
		$general_settings = get_option( 'acadp_general_settings' );
		
		$post_meta = get_post_meta( $post->ID );
			
		// Add a nonce field so we can check for it later
    	wp_nonce_field( 'acadp_save_listing_video', 'acadp_listing_video_nonce' );
		
		require_once ACADP_PLUGIN_DIR . 'admin/partials/listings/acadp-admin-listing-video-display.php';

	}
	
	/**
	 * Display custom fields.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 int    $post_id	Post ID.
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
		include( ACADP_PLUGIN_DIR . 'admin/partials/listings/acadp-admin-custom-fields-display.php' );
		wp_reset_postdata(); // Restore global post data stomped by the_post()
		$output = ob_get_clean();
			
		print $output;
		
		if( $ajax ) {
			wp_die();
		}
	
	}

	/**
	 * Delete attachments.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function before_delete_post( $post_id ) {
	
		global $post_type;
		
		if ( 'acadp_listings' != $post_type ) {
			return;
		}
		  
		$images = get_post_meta( $post_id, 'images', true );
		
		if( ! empty( $images ) ) {
		
			foreach( $images as $image ) {
				wp_delete_attachment( $image, true );
			}
		
		}
	
	}
	
	/**
	 * Save meta data.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 int    $post_id    Post ID.
	 * @param    post   $post       The post object.
	 * @return 	 int    $post_id    If the save was successful or not.
	 */
	public function save_meta_data( $post_id, $post ) {
	
		if( ! isset( $_POST['post_type'] ) ) {
        	return $post_id;
    	}
	
		// Check this is the "acadp_listings" custom post type
    	if( 'acadp_listings' != $post->post_type ) {
        	return $post_id;
    	}
		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        	return $post_id;
		}
		
		// Check the logged in user has permission to edit this post
    	if( ! acadp_current_user_can( 'edit_acadp_listing', $post_id ) ) {
        	return $post_id;
    	}
		
		// Check if "acadp_listing_submitbox_nonce" nonce is set
    	if( isset( $_POST['acadp_listing_submitbox_nonce'] ) ) {
		
			// Verify that the nonce is valid
    		if( wp_verify_nonce( $_POST['acadp_listing_submitbox_nonce'], 'acadp_save_listing_submitbox' ) ) {
			
				// OK to save meta data
				if( isset( $_POST['acadp_aa'] ) ) {
				
					$date = array(
						'year'  => $_POST['acadp_aa'],
						'month' => $_POST['acadp_mm'],
						'day'   => $_POST['acadp_jj'],
						'hour'  => $_POST['acadp_hh'],
						'min'   => $_POST['acadp_mn']
					);
				
					$expiry_date = acadp_mysql_date_format( $date );
					update_post_meta( $post_id, 'expiry_date', $expiry_date );
					
				} else if( 'publish' == $post->post_status ) {
				
					$expiry_date = acadp_listing_expiry_date( $post_id );
					update_post_meta( $post->ID, 'expiry_date', $expiry_date );
			
				}
				
				if( isset( $_POST['never_expires'] ) ) {
					update_post_meta( $post_id, 'never_expires', 1 );
				} else {
					delete_post_meta( $post_id, 'never_expires' );
				}    			
				
				$featured = isset( $_POST['featured'] ) ? 1 : 0;
    			update_post_meta( $post_id, 'featured', $featured );
				
				if( isset( $_POST['listing_status'] ) ) {
					$listing_status = sanitize_text_field( $_POST['listing_status'] );
					update_post_meta( $post_id, 'listing_status', $listing_status );
				}
				
			}
			
		}		
		
		// Check if "acadp_listing_details_nonce" nonce is set
    	if( isset( $_POST['acadp_listing_details_nonce'] ) ) {
		
        	// Verify that the nonce is valid
    		if( wp_verify_nonce( $_POST['acadp_listing_details_nonce'], 'acadp_save_listing_details' ) ) {
			
        		// OK to save meta data
				wp_set_object_terms( $post_id, (int) $_POST['acadp_category'], 'acadp_categories' );
				
				if( isset( $_POST['price'] ) ) {
				
					$price = acadp_sanitize_amount( $_POST['price'] );
    				update_post_meta( $post_id, 'price', $price );
				
				}
				
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
				
				$views = (int) $_POST['views'];
    			update_post_meta( $post_id, 'views', $views );
				
    		}
			
    	}
				
		// Check if "acadp_contact_details_nonce" nonce is set
    	if( isset( $_POST['acadp_contact_details_nonce'] ) ) {
		
        	// Verify that the nonce is valid
    		if( wp_verify_nonce( $_POST['acadp_contact_details_nonce'], 'acadp_save_contact_details' ) ) {
			
				// OK to save meta data
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
			
		}
		
		// Check if "acadp_listing_images_nonce" nonce is set
    	if( isset( $_POST['acadp_listing_images_nonce'] ) ) {
		
        	// Verify that the nonce is valid
    		if( wp_verify_nonce( $_POST['acadp_listing_images_nonce'], 'acadp_save_listing_images' ) ) {
				
				if( isset( $_POST['images'] ) ) {
				
					// OK to save meta data
					$images = array_filter( $_POST['images'] );
					$images = array_map( 'intval', $images );
	
        			if( count( $images ) ) {						
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
				
			}
				
		}
				
		// Check if "acadp_listing_video_nonce" nonce is set
    	if( isset( $_POST['acadp_listing_video_nonce'] ) ) {
		
        	// Verify that the nonce is valid
    		if( wp_verify_nonce( $_POST['acadp_listing_video_nonce'], 'acadp_save_listing_video' ) ) {
			
				if( isset( $_POST['video'] ) ) {
					$video = esc_url_raw( $_POST['video'] );
    				update_post_meta( $post_id, 'video', $video );
				}
				
			}
			
		}
		
		return $post_id;
	
	}
	
	/**
	 * Notify listing owner when his listing approved/published.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param    string     $new_status    Transition to this post status.
	 * @param    string     $old_status    Previous post status.
	 * @param    WP_Post    $post          Post data.
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {
	
		if( 'acadp_listings' !== $post->post_type ) {
			return;
		}
		
		// Check if we are transitioning from pending to publish
    	if( 'pending' == $old_status && 'publish' == $new_status ) {
					
			acadp_email_listing_owner_listing_approved( $post->ID );
			
		}
		
		// Check if we are transitioning from private to publish
    	if( 'private' == $old_status && 'publish' == $new_status ) {
		
			$listing_status = get_post_meta( $post->ID, 'listing_status', true );
			
			if( 'expired' == $listing_status ) {
				update_post_meta( $post->ID, 'listing_status', 'post_status' );
				
				unset( $_POST['acadp_aa'] );
				unset( $_POST['listing_status'] );
			}
			
		}
		
	}
	
	/**
	 * Add custom filter options.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function restrict_manage_posts() {
	
		global $typenow, $wp_query;
		
		if( 'acadp_listings' == $typenow ) {
			
			$general_settings = get_option( 'acadp_general_settings' );
			$featured_settings = get_option( 'acadp_featured_listing_settings' );

			// Restrict by location
			if( ! empty( $general_settings['has_location'] ) ) {
			
				$base_location = max( 0, $general_settings['base_location'] );
				
				wp_dropdown_categories(array(
            		'show_option_none'  => __( "All Locations", 'advanced-classifieds-and-directory-pro' ),					
					'option_none_value' => $base_location,
					'child_of'          => $base_location,
            		'taxonomy'          => 'acadp_locations',
            		'name'              => 'acadp_locations',
            		'orderby'           => 'name',
            		'selected'          => isset( $wp_query->query['acadp_locations'] ) ? $wp_query->query['acadp_locations'] : '',
            		'hierarchical'      => true,
            		'depth'             => 3,
            		'show_count'        => false,
            		'hide_empty'        => false,
        		));
			
			}
			
			// Restrict by category
        	wp_dropdown_categories(array(
            	'show_option_none'  => __( "All Categories", 'advanced-classifieds-and-directory-pro' ),
				'option_none_value' => 0,
            	'taxonomy'          => 'acadp_categories',
            	'name'              => 'acadp_categories',
            	'orderby'           => 'name',
            	'selected'          => isset( $wp_query->query['acadp_categories'] ) ? $wp_query->query['acadp_categories'] : '',
            	'hierarchical'      => true,
            	'depth'             => 3,
            	'show_count'        => false,
            	'hide_empty'        => false,
        	));

			// Restrict by featured
			$has_featured = apply_filters( 'acadp_has_featured', isset( $featured_settings['enabled'] ) );
			if( $has_featured ) {
			
				$featured = isset( $_GET['featured'] ) ? $_GET['featured'] : 0;
			
				echo '<select name="featured">';
				printf( '<option value="%d"%s>%s</option>', 0, selected( 0, $featured, false ), __( "All listings", 'advanced-classifieds-and-directory-pro' ) );
				printf( '<option value="%d"%s>%s</option>', 1, selected( 1, $featured, false ), __( "Featured only", 'advanced-classifieds-and-directory-pro' ) );
				echo '</select>';
			
			}
		
    	}
	
	}
	
	/**
	 * Parse a query string and filter listings accordingly.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Query    $query    WordPress Query object
	 */
	public function parse_query( $query ) {
	
		global $pagenow, $post_type;
		
    	if( 'edit.php' == $pagenow && 'acadp_listings' == $post_type ) {
			
			// Convert location id to taxonomy term in query
			if( isset( $query->query_vars['acadp_locations'] ) && ctype_digit( $query->query_vars['acadp_locations'] ) && $query->query_vars['acadp_locations'] != 0 ) {
		
        		$term = get_term_by( 'id', $query->query_vars['acadp_locations'], 'acadp_locations' );
        		$query->query_vars['acadp_locations'] = $term->slug;
			
			}
			
			// Convert category id to taxonomy term in query
			if(	isset( $query->query_vars['acadp_categories'] ) && ctype_digit( $query->query_vars['acadp_categories'] ) && $query->query_vars['acadp_categories'] != 0 ) {
		
        		$term = get_term_by( 'id', $query->query_vars['acadp_categories'], 'acadp_categories' );
        		$query->query_vars['acadp_categories'] = $term->slug;
			
    		}

			// Set featured meta in query
			if( isset( $_GET['featured'] ) && 1 == $_GET['featured'] ) {
		
        		$query->query_vars['meta_key'] = 'featured';
        		$query->query_vars['meta_value'] = 1;
			
    		}
			
		}
	
	}
	
	/**
	 * Retrieve the table columns.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    array    $columns    Array of default table columns.
	 * @return   array    $columns    Updated list of table columns.
	 */
	public function get_columns( $columns ) {
	
		$general_settings = get_option( 'acadp_general_settings' );
		$featured_settings = get_option( 'acadp_featured_listing_settings' );

		$new_columns = array(
			'views' => __( 'Views', 'advanced-classifieds-and-directory-pro' )
		);
		
		$has_featured = apply_filters( 'acadp_has_featured', isset( $featured_settings['enabled'] ) );
		if( $has_featured ) {
			$new_columns['featured'] = __( 'Featured', 'advanced-classifieds-and-directory-pro' );
		}	
		
		$new_columns['posted_date'] = __( 'Posted Date', 'advanced-classifieds-and-directory-pro' );
		$new_columns['expiry_date'] = __( 'Expires on', 'advanced-classifieds-and-directory-pro' );
		$new_columns['status'] = __( 'Status', 'advanced-classifieds-and-directory-pro' );
			
		unset( $columns['date'] );
		
		$taxonomy_column = 'taxonomy-acadp_categories';
		
		return acadp_array_insert_after( $taxonomy_column, $columns, $new_columns );
		
	}
	
	/**
	 * This function renders the custom columns in the list table.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    string    $column    The name of the column.
	 * @param    string    $post_id   Post ID.
	 */
	public function custom_column_content( $column, $post_id ) {
	
		switch ( $column ) {
			case 'views' :
				echo get_post_meta( $post_id, 'views', true );
				break;
			case 'featured' :
				$value = get_post_meta( $post_id, 'featured', true );
				echo '<span class="acadp-tick-cross">'.($value == 1 ? '&#x2713;' : '&#x2717;').'</span>';
				break;	
			case 'posted_date' :
				printf( _x( '%s ago', '%s = human-readable time difference', 'advanced-classifieds-and-directory-pro' ), human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) ) );
				break;
			case 'expiry_date' :
				$never_expires = get_post_meta( $post_id, 'never_expires', true );
				
				if( ! empty( $never_expires ) ) {
					_e( 'Never Expires', 'advanced-classifieds-and-directory-pro' );
				} else {
					$expiry_date = get_post_meta( $post_id, 'expiry_date', true );
					
					if( ! empty( $expiry_date ) ) {
						echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $expiry_date ) );
					} else {
						echo '-';
					}
				}
				break;
			case 'status' :
				$listing_status = get_post_meta( $post_id, 'listing_status', true );
				
				echo ( empty( $listing_status ) || 'post_status' == $listing_status ) ? get_post_status( $post_id ) : $listing_status;
				break;
		}
		
	}	
	
	/**
	 * Remove quick edit.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 array      $actions    An array of row action links.
	 * @param	 WP_Post    $post       The post object.
	 * @return	 array      $actions    Updated array of row action links.
	 */
	public function remove_row_actions( $actions, $post ) {
	
		global $current_screen;
		
		if( $current_screen->post_type != 'acadp_listings' ) return $actions;
		
    	unset( $actions['view'] );
    	unset( $actions['inline hide-if-no-js'] );
		
		return $actions;
	
	}

}

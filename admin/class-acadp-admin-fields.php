<?php

/**
 * Custom Fields
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
 * ACADP_Admin_Fields Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Admin_Fields {
	
	/**
	 * Register a custom post type "acadp_fields".
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_custom_post_type() {
	
		$labels = array(
			'name'                => _x( 'Custom Fields', 'Post Type General Name', 'advanced-classifieds-and-directory-pro' ),
			'singular_name'       => _x( 'Custom Field', 'Post Type Singular Name', 'advanced-classifieds-and-directory-pro' ),
			'menu_name'           => __( 'Custom Fields', 'advanced-classifieds-and-directory-pro' ),
			'name_admin_bar'      => __( 'Custom Field', 'advanced-classifieds-and-directory-pro' ),
			'all_items'           => __( 'Custom Fields', 'advanced-classifieds-and-directory-pro' ),
			'add_new_item'        => __( 'Add New Field', 'advanced-classifieds-and-directory-pro' ),
			'add_new'             => __( 'Add New', 'advanced-classifieds-and-directory-pro' ),
			'new_item'            => __( 'New Field', 'advanced-classifieds-and-directory-pro' ),
			'edit_item'           => __( 'Edit Field', 'advanced-classifieds-and-directory-pro' ),
			'update_item'         => __( 'Update Field', 'advanced-classifieds-and-directory-pro' ),
			'view_item'           => __( 'View Field', 'advanced-classifieds-and-directory-pro' ),
			'search_items'        => __( 'Search Field', 'advanced-classifieds-and-directory-pro' ),
			'not_found'           => __( 'Not found', 'advanced-classifieds-and-directory-pro' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'advanced-classifieds-and-directory-pro' ),
		);
		
		$args = array(
			'label'               => __( 'acadp_fields', 'advanced-classifieds-and-directory-pro' ),
			'description'         => __( 'Post Type Description', 'advanced-classifieds-and-directory-pro' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array( 'acadp_categories' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=acadp_listings',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'rewrite'             => false, 
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'acadp_field',
			'map_meta_cap'        => true,
		);
				
		register_post_type( 'acadp_fields', $args ); 

	}
	
	/**
	 * Register meta boxes.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_meta_boxes() {
	
		remove_meta_box( 'slugdiv', 'acadp_fields', 'normal' );
		
		add_meta_box( 'acadp-field-details', __( 'Field Details', 'advanced-classifieds-and-directory-pro' ), array( $this, 'display_meta_box_field_details' ), 'acadp_fields', 'normal', 'high' );
		add_meta_box( 'acadp-field-options', __( 'Display Options', 'advanced-classifieds-and-directory-pro' ), array( $this, 'display_meta_box_field_options' ), 'acadp_fields', 'normal', 'low' ); 
		
	}
	
	/**
	 * Display the field details meta box.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Post    $post    WordPress Post object
	 */
	public function display_meta_box_field_details( $post ) {
	
		$post_meta = get_post_meta( $post->ID );
		
		// Add a nonce field so we can check for it later
    	wp_nonce_field( 'acadp_save_field_details', 'acadp_field_details_nonce' );
	
		require_once ACADP_PLUGIN_DIR . 'admin/partials/fields/acadp-admin-field-details-display.php';

	}
	
	/**
	 * Display the field options meta box.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Post	   $post    WordPress Post object
	 */
	public function display_meta_box_field_options( $post ) {
	
		$post_meta = get_post_meta( $post->ID );
		
		// Add a nonce field so we can check for it later
    	wp_nonce_field( 'acadp_save_field_options', 'acadp_field_options_nonce' );
	
		require_once ACADP_PLUGIN_DIR . 'admin/partials/fields/acadp-admin-field-options-display.php';

	}
	
	/**
	 * Save meta data.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 int    $post_id    Post ID
	 * @param    post   $post       The post object.
	 * @return 	 int    $post_id    If the save was successful or not.
	 */
	public function save_meta_data( $post_id, $post ) {
	
		if( ! isset( $_POST['post_type'] ) ) {
			return $post_id;
		}
		
		// Check this is the "acadp_fields" custom post type
    	if( 'acadp_fields' != $post->post_type ) {
        	return $post_id;
    	}
		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        	return $post_id;
		}
		
		// Check the logged in user has permission to edit this post
    	if( ! acadp_current_user_can( 'edit_acadp_fields' ) ) {
        	return $post_id;
    	}
		
		// Check if "acadp_field_details_nonce" nonce is set
    	if( isset( $_POST['acadp_field_details_nonce'] ) ) {
		
        	// Verify that the nonce is valid
    		if( wp_verify_nonce( $_POST['acadp_field_details_nonce'], 'acadp_save_field_details' ) ) {
			
        		// OK to save meta data
				$field_type = sanitize_key( $_POST['type'] );
    			update_post_meta( $post_id, 'type', $field_type );
		
				$field_instructions = esc_textarea( $_POST['instructions'] );
    			update_post_meta( $post_id, 'instructions', $field_instructions );
		
				$field_required = (int) $_POST['required'];
    			update_post_meta( $post_id, 'required', $field_required );
		
				$field_choices = esc_textarea( $_POST['choices'] );
    			update_post_meta( $post_id, 'choices', $field_choices );
		
				if( 'checkbox' == $field_type || 'textarea' == $field_type ) {
					$field_default_value = esc_textarea( $_POST['default_value_'.$field_type] );
				} else if( 'url' == $field_type ) {
					$field_default_value = esc_url_raw( $_POST['default_value'] );
				} else {
					$field_default_value = sanitize_text_field( $_POST['default_value' ] );
				}				
    			update_post_meta( $post_id, 'default_value', $field_default_value );
				
				$field_allow_null = (int) $_POST['allow_null'];
    			update_post_meta( $post_id, 'allow_null', $field_allow_null );
		
				$field_placeholder = sanitize_text_field( $_POST['placeholder'] );
    			update_post_meta( $post_id, 'placeholder', $field_placeholder );
		
				$field_rows = (int) $_POST['rows'];
    			update_post_meta( $post_id, 'rows', $field_rows );
				
				$field_target = sanitize_text_field( $_POST['target'] );
    			update_post_meta( $post_id, 'target', $field_target );
				
				$field_nofollow = (int) $_POST['nofollow'];
    			update_post_meta( $post_id, 'nofollow', $field_nofollow );
				
    		}
			
    	}		
		
		// Check if "acadp_field_options_nonce" nonce is set
    	if ( isset( $_POST['acadp_field_options_nonce'] ) ) {
		
        	// Verify that the nonce is valid
    		if ( wp_verify_nonce( $_POST['acadp_field_options_nonce'], 'acadp_save_field_options' ) ) {
			
				// OK to save meta data
				$field_associate =  sanitize_text_field( $_POST['associate'] );
    			update_post_meta( $post_id, 'associate', $field_associate );
				
				$field_searchable = (int) $_POST['searchable'];
    			update_post_meta( $post_id, 'searchable', $field_searchable );
				
				$field_order = floatval( $_POST['order'] );
    			update_post_meta( $post_id, 'order', $field_order );
				
			}
			
		}
		
		return $post_id;
	
	}
	
	/**
	 * Add custom filter options.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function restrict_manage_posts() {
	
		global $typenow, $wp_query;
		
		if( 'acadp_fields' == $typenow ) {
			
			// Restrict by category
        	wp_dropdown_categories(array(
				'show_option_none'  => __( "All Categories", 'advanced-classifieds-and-directory-pro' ),
				'option_none_value' => 0,
            	'taxonomy'          =>  'acadp_categories',
            	'name'              =>  'acadp_categories',
            	'orderby'           =>  'name',
            	'selected'          =>  isset( $wp_query->query['acadp_categories'] ) ? $wp_query->query['acadp_categories'] : '',
            	'hierarchical'      =>  true,
            	'depth'             =>  3,
            	'show_count'        =>  false,
            	'hide_empty'        =>  false,
        	));
			
			// Restrict by field associate
			$associate = isset( $_GET['associate'] ) ? $_GET['associate'] : '';
			
			echo '<select name="associate">';
			printf( '<option value="">%s</option>', __( "All fields", 'advanced-classifieds-and-directory-pro' ) );
			printf( '<option value="%s"%s>%s</option>', 'form', selected( 'form', $associate, false ), __( "Form", 'advanced-classifieds-and-directory-pro' ) );
			printf( '<option value="%s"%s>%s</option>', 'categories', selected( 'categories', $associate, false ), __( "Categories", 'advanced-classifieds-and-directory-pro' ) );
			echo '</select>';
		
    	}
	
	}
	
	/**
	 * Filter fields(posts) by categories(taxonomy).
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Query    $query    WordPress Query object
	 */
	public function parse_query( $query ) {
	
		global $pagenow, $post_type;
		
    	if( 'edit.php' == $pagenow && 'acadp_fields' == $post_type ) {
		
			// Convert category id to taxonomy term in query
			if( isset( $query->query_vars['acadp_categories'] ) && ctype_digit( $query->query_vars['acadp_categories'] ) && $query->query_vars['acadp_categories'] != 0 ) {
		
        		$term = get_term_by( 'id', $query->query_vars['acadp_categories'], 'acadp_categories' );
        		$query->query_vars['acadp_categories'] = $term->slug;
			
    		}
			
			// Associate
			if(	isset( $_GET['associate'] ) && ! empty( $_GET['associate'] ) ) {
		
				$query->query_vars['meta_key'] = 'associate';
				$query->query_vars['meta_value'] = sanitize_text_field( $_GET['associate'] );
			
    		}
			
    	}
	
	}
	
	/**
	 * Exclude child categories(taxonomy) from the result.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Query    $query    WordPress Query object
	 */
	public function parse_tax_query( $query ) {
	
		global $pagenow, $post_type;
		
		if( 'edit.php' == $pagenow && 'acadp_fields' == $post_type ) {
		
			if ( ! empty( $query->tax_query->queries ) ) {
							
				$query->tax_query->queries[0]['include_children'] = 0;
				
			}
			
		}
	
	}
	
	/**
	 * Sort fieLds by custom order(meta) value.
	 *
	 * @since	 1.0.0
	 * @access   public
	 *
	 * @param	 WP_Query    $query    WordPress Query object
	 */
	public function custom_order( $query ) {
	
		// The current post type
    	$post_type = $query->get('post_type');
		
		// Check post type
    	if( 'acadp_fields' == $post_type ) {
		
        	// Post Column: field_order
        	if( '' == $query->get('orderby') && '' == $query->get('order') ) {
			
				$query->set('orderby', array( 
					'order' => 'ASC',
					'date'  => 'DESC'
				));
				
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
		
		$new_columns = array(
			'associate'                 => __( 'Assigned to', 'advanced-classifieds-and-directory-pro' ),
			'taxonomy-acadp_categories' => __( 'Categories', 'advanced-classifieds-and-directory-pro' ),
			'type'                      => __( 'Type', 'advanced-classifieds-and-directory-pro' ),
			'required'                  => __( 'Required', 'advanced-classifieds-and-directory-pro' ),
			'searchable'                => __( 'Searchable', 'advanced-classifieds-and-directory-pro' ),
			'order'                     => __( 'Order', 'advanced-classifieds-and-directory-pro' )
		);		
		$columns = acadp_array_insert_after( 'title', $columns, $new_columns );
		
		return $columns;
		
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
			case 'associate' :
				$value = get_post_meta( $post_id, 'associate', true );
				echo ( 'form' == $value ) ? __( 'Form', 'advanced-classifieds-and-directory-pro' ) : __( 'Categories', 'advanced-classifieds-and-directory-pro' );
				break;
			case 'type' :
				$types = acadp_get_custom_field_types();
				
				$value = get_post_meta( $post_id, 'type', true );
				echo $types[ $value ];
				break;
			case 'required' :
				$value = get_post_meta( $post_id, 'required', true );
				echo '<span class="acadp-tick-cross">'.($value == 1 ? '&#x2713;' : '&#x2717;').'</span>';
				break;
			case 'searchable' :
				$value = get_post_meta( $post_id, 'searchable', true );
				echo '<span class="acadp-tick-cross">'.($value == 1 ? '&#x2713;' : '&#x2717;').'</span>';
				break;	
			case 'order' :
				$value = get_post_meta( $post_id, 'order', true );
				echo $value;
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
		
		if( $current_screen->post_type != 'acadp_fields' ) return $actions;
		
    	unset( $actions['view'] );
    	unset( $actions['inline hide-if-no-js'] );
		
		return $actions;
	
	}

}

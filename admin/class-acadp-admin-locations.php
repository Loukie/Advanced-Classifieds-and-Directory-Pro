<?php

/**
 * Locations
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
 * ACADP_Admin_Locations Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Admin_Locations {
	
	/**
	 * Register a custom taxonomy.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_custom_taxonomy() {
	
		$general_settings = get_option( 'acadp_general_settings' );
		$has_location = empty( $general_settings['has_location'] ) ? false : true;
		
		$labels = array(
			'name'                       => _x( 'Locations', 'Taxonomy General Name', 'advanced-classifieds-and-directory-pro' ),
			'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'advanced-classifieds-and-directory-pro' ),
			'menu_name'                  => __( 'Locations', 'advanced-classifieds-and-directory-pro' ),
			'all_items'                  => __( 'All Locations', 'advanced-classifieds-and-directory-pro' ),
			'parent_item'                => __( 'Parent Location', 'advanced-classifieds-and-directory-pro' ),
			'parent_item_colon'          => __( 'Parent Location:', 'advanced-classifieds-and-directory-pro' ),
			'new_item_name'              => __( 'New Location Name', 'advanced-classifieds-and-directory-pro' ),
			'add_new_item'               => __( 'Add New Location', 'advanced-classifieds-and-directory-pro' ),
			'edit_item'                  => __( 'Edit Location', 'advanced-classifieds-and-directory-pro' ),
			'update_item'                => __( 'Update Location', 'advanced-classifieds-and-directory-pro' ),
			'view_item'                  => __( 'View Location', 'advanced-classifieds-and-directory-pro' ),
			'separate_items_with_commas' => __( 'Separate Locations with commas', 'advanced-classifieds-and-directory-pro' ),
			'add_or_remove_items'        => __( 'Add or remove Locations', 'advanced-classifieds-and-directory-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'advanced-classifieds-and-directory-pro' ),
			'popular_items'              => NULL,
			'search_items'               => __( 'Search Locations', 'advanced-classifieds-and-directory-pro' ),
			'not_found'                  => __( 'Not Found', 'advanced-classifieds-and-directory-pro' ),
		);
		
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => $has_location,
			'show_admin_column'          => $has_location,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'query_var'                  => true,
			'capabilities'               => array(
				'manage_terms' => 'manage_acadp_options',
				'edit_terms'   => 'manage_acadp_options',				
				'delete_terms' => 'manage_acadp_options',
				'assign_terms' => 'edit_acadp_listings'
			),
		);
		
		register_taxonomy( 'acadp_locations', array( 'acadp_listings' ), $args );
	
	}
	
	/**
	 * Retrieve the table columns.
	 *
	 * @since     1.5.8
	 * @access    public
	 *
	 * @param     array     $columns    Array of default table columns.
	 * @return    array     $columns    Updated list of table columns.
	 */
	public function get_columns( $columns ) {
	
		$columns['tax_id'] = __( 'ID', 'advanced-classifieds-and-directory-pro' );
    	return $columns;
		
	}
	
	/**
	 * This function renders the custom columns in the list table.
	 *
	 * @since     1.5.8
	 * @access    public
	 *
	 * @param     string    $content    Content of the column.
	 * @param     string    $column     Name of the column.
	 * @param     string    $term_id    Term ID.
	 */
	public function custom_column_content( $content, $column, $term_id ) {
		
		if ( 'tax_id' == $column ) {
        	$content = $term_id;
    	}
		
		return $content;
	
	}
		
}

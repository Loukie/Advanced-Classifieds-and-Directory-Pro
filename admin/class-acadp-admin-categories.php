<?php

/**
 * Categories
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
 * ACADP_Admin_Categories Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Admin_Categories {
	
	/**
	 * Register a custom taxonomy.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_custom_taxonomy() {
	
		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'advanced-classifieds-and-directory-pro' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'advanced-classifieds-and-directory-pro' ),
			'menu_name'                  => __( 'Categories', 'advanced-classifieds-and-directory-pro' ),
			'all_items'                  => __( 'All Categories', 'advanced-classifieds-and-directory-pro' ),
			'parent_item'                => __( 'Parent Category', 'advanced-classifieds-and-directory-pro' ),
			'parent_item_colon'          => __( 'Parent Category:', 'advanced-classifieds-and-directory-pro' ),
			'new_item_name'              => __( 'New Category Name', 'advanced-classifieds-and-directory-pro' ),
			'add_new_item'               => __( 'Add New Category', 'advanced-classifieds-and-directory-pro' ),
			'edit_item'                  => __( 'Edit Category', 'advanced-classifieds-and-directory-pro' ),
			'update_item'                => __( 'Update Category', 'advanced-classifieds-and-directory-pro' ),
			'view_item'                  => __( 'View Category', 'advanced-classifieds-and-directory-pro' ),
			'separate_items_with_commas' => __( 'Separate Categories with commas', 'advanced-classifieds-and-directory-pro' ),
			'add_or_remove_items'        => __( 'Add or remove Categories', 'advanced-classifieds-and-directory-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'advanced-classifieds-and-directory-pro' ),
			'popular_items'              => NULL,
			'search_items'               => __( 'Search Categories', 'advanced-classifieds-and-directory-pro' ),
			'not_found'                  => __( 'Not Found', 'advanced-classifieds-and-directory-pro' ),
		);
		
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,			
			'show_admin_column'          => true,
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
		
		register_taxonomy( 'acadp_categories', array( 'acadp_listings' ), $args );
	
	}
	
	/**
	 * Add Image Field.
	 *
	 * @since    1.5.4
	 * @access   public
	 */
	public function add_image_field() {
	
		$page = 'add';
		
		require_once ACADP_PLUGIN_DIR . 'admin/partials/categories/acadp-admin-image-field-display.php';
	
	}
	
	/**
	 * Save the Image Field.
	 *
	 * @since    1.5.4
	 * @access   public
	 *
	 * @param    int       $term_id    Term ID.
	 */
	public function save_image_field( $term_id ) {
	
		if( isset( $_POST['image'] ) && '' !== $_POST['image'] ) {
     		add_term_meta( $term_id, 'image', (int) $_POST['image'], true );
   		}
   
	}
	
	/**
	 * Edit Image Field.
	 *
	 * @since    1.5.4
	 * @access   public
	 *
	 * @param    object    $term    Taxonomy term object.
	 */
	public function edit_image_field( $term ) {
	
		$page = 'edit';
		
		$image_id  = get_term_meta( $term->term_id, 'image', true );
		$image_src = ( $image_id ) ? wp_get_attachment_url( (int) $image_id ) : '';
		
		require_once ACADP_PLUGIN_DIR . 'admin/partials/categories/acadp-admin-image-field-display.php';
	
	}
	
	/**
	 * Update the Image Field.
	 *
	 * @since    1.5.4
	 * @access   public
	 *
	 * @param    int       $term_id    Term ID.
	 */
	public function update_image_field( $term_id ) {
	
		if( isset( $_POST['image'] ) && '' !== $_POST['image'] ) {
     		update_term_meta( $term_id, 'image', (int) $_POST['image'] );
   		} else {
     		update_term_meta ( $term_id, 'image', '' );
   		}
   
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

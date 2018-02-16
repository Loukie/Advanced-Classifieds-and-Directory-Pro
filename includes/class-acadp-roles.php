<?php

/**
 * Roles and Capabilities
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/includes
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Roles Class
 *
 * This class handles the role creation and assignment of capabilities for those roles.
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Roles {

	/**
	 * Add new capabilities.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_caps() {
	
		global $wp_roles;

		if( class_exists('WP_Roles') ) {
			if( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if( is_object( $wp_roles ) ) {

			// Add the "administrator" capabilities
			$capabilities = $this->get_core_caps();
			foreach( $capabilities as $cap_group ) {
				foreach( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
			$wp_roles->add_cap( 'administrator', 'manage_acadp_options' );
			
			// Add the "editor" capabilities
			$wp_roles->add_cap( 'editor', 'edit_acadp_listings' );			
			$wp_roles->add_cap( 'editor', 'edit_others_acadp_listings' );			
			$wp_roles->add_cap( 'editor', 'publish_acadp_listings' );			
			$wp_roles->add_cap( 'editor', 'read_private_acadp_listings' );	
			$wp_roles->add_cap( 'editor', 'delete_acadp_listings' );			
			$wp_roles->add_cap( 'editor', 'delete_private_acadp_listings' );
			$wp_roles->add_cap( 'editor', 'delete_published_acadp_listings' );
			$wp_roles->add_cap( 'editor', 'delete_others_acadp_listings' );
			$wp_roles->add_cap( 'editor', 'edit_private_acadp_listings' );
			$wp_roles->add_cap( 'editor', 'edit_published_acadp_listings' );
			
			// Add the "author" capabilities
			$wp_roles->add_cap( 'author', 'edit_acadp_listings' );						
			$wp_roles->add_cap( 'author', 'publish_acadp_listings' );
			$wp_roles->add_cap( 'author', 'delete_acadp_listings' );
			$wp_roles->add_cap( 'author', 'delete_published_acadp_listings' );
			$wp_roles->add_cap( 'author', 'edit_published_acadp_listings' );
			
			// Add the "contributor" capabilities
			$wp_roles->add_cap( 'contributor', 'edit_acadp_listings' );						
			$wp_roles->add_cap( 'contributor', 'publish_acadp_listings' );
			$wp_roles->add_cap( 'contributor', 'delete_acadp_listings' );
			$wp_roles->add_cap( 'contributor', 'delete_published_acadp_listings' );
			$wp_roles->add_cap( 'contributor', 'edit_published_acadp_listings' );
			
			// Add the "subscriber" capabilities
			$wp_roles->add_cap( 'subscriber', 'edit_acadp_listings' );						
			$wp_roles->add_cap( 'subscriber', 'publish_acadp_listings' );
			$wp_roles->add_cap( 'subscriber', 'delete_acadp_listings' );
			$wp_roles->add_cap( 'subscriber', 'delete_published_acadp_listings' );
			$wp_roles->add_cap( 'subscriber', 'edit_published_acadp_listings' );
			
		}
	}

	/**
	 * Gets the core post type capabilities.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return   array    $capabilities    Core post type capabilities.
	 */
	public function get_core_caps() {
	
		$capabilities = array();

		$capability_types = array( 'acadp_listing', 'acadp_field', 'acadp_payment' );

		foreach( $capability_types as $capability_type ) {
		
			$capabilities[ $capability_type ] = array(
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",
			);
		}

		return $capabilities;
		
	}
	
	/**
	 * Filter a user's capabilities depending on specific context and/or privilege.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    array     $caps       Returns the user's actual capabilities.
	 * @param    string    $cap        Capability name.
	 * @param    int       $user_id    The user ID.
	 * @param    array     $args       Adds the context to the cap. Typically the object ID.
	 * @return   array                 Actual capabilities for meta capability.
	 */
	public function meta_caps( $caps, $cap, $user_id, $args ) {
		
		// If editing, deleting, or reading a listing, get the post and post type object.
		if( 'edit_acadp_listing' == $cap || 'delete_acadp_listing' == $cap || 'read_acadp_listing' == $cap ) {
			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );

			// Set an empty array for the caps.
			$caps = array();
		}

		// If editing a listing, assign the required capability.
		if( 'edit_acadp_listing' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->edit_acadp_listings;
			else
				$caps[] = $post_type->cap->edit_others_acadp_listings;
		}

		// If deleting a listing, assign the required capability.
		else if( 'delete_acadp_listing' == $cap ) {
			if( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_acadp_listings;
			else
				$caps[] = $post_type->cap->delete_others_acadp_listings;
		}

		// If reading a private listing, assign the required capability.
		else if( 'read_acadp_listing' == $cap ) {
			if( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_acadp_listings;
		}

		// Return the capabilities required by the user.
		return $caps;

	}
	
	/**
	 * Remove core post type capabilities (called on uninstall).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function remove_caps() {

		global $wp_roles;

		if( class_exists( 'WP_Roles' ) ) {
			if( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if( is_object( $wp_roles ) ) {
		
			// Remove the "administrator" Capabilities
			$capabilities = $this->get_core_caps();

			foreach( $capabilities as $cap_group ) {
				foreach( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'administrator', $cap );
				}
			}
			$wp_roles->remove_cap( 'administrator', 'manage_acadp_options' );
			
			// Remove the "editor" capabilities
			$wp_roles->remove_cap( 'editor', 'edit_acadp_listings' );			
			$wp_roles->remove_cap( 'editor', 'edit_others_acadp_listings' );			
			$wp_roles->remove_cap( 'editor', 'publish_acadp_listings' );			
			$wp_roles->remove_cap( 'editor', 'read_private_acadp_listings' );
			$wp_roles->remove_cap( 'editor', 'delete_acadp_listings' );			
			$wp_roles->remove_cap( 'editor', 'delete_private_acadp_listings' );
			$wp_roles->remove_cap( 'editor', 'delete_published_acadp_listings' );
			$wp_roles->remove_cap( 'editor', 'delete_others_acadp_listings' );
			$wp_roles->remove_cap( 'editor', 'edit_private_acadp_listings' );
			$wp_roles->remove_cap( 'editor', 'edit_published_acadp_listings' );
			
			// Remove the "author" capabilities
			$wp_roles->remove_cap( 'author', 'edit_acadp_listings' );						
			$wp_roles->remove_cap( 'author', 'publish_acadp_listings' );
			$wp_roles->remove_cap( 'author', 'delete_acadp_listings' );
			$wp_roles->remove_cap( 'author', 'delete_published_acadp_listings' );
			$wp_roles->remove_cap( 'author', 'edit_published_acadp_listings' );
			
			// Remove the "contributor" capabilities
			$wp_roles->remove_cap( 'contributor', 'edit_acadp_listings' );						
			$wp_roles->remove_cap( 'contributor', 'publish_acadp_listings' );
			$wp_roles->remove_cap( 'contributor', 'delete_acadp_listings' );
			$wp_roles->remove_cap( 'contributor', 'delete_published_acadp_listings' );
			$wp_roles->remove_cap( 'contributor', 'edit_published_acadp_listings' );
			
			// Remove the "subscriber" capabilities
			$wp_roles->remove_cap( 'subscriber', 'edit_acadp_listings' );						
			$wp_roles->remove_cap( 'subscriber', 'publish_acadp_listings' );
			$wp_roles->remove_cap( 'subscriber', 'delete_acadp_listings' );
			$wp_roles->remove_cap( 'subscriber', 'delete_published_acadp_listings' );
			$wp_roles->remove_cap( 'subscriber', 'edit_published_acadp_listings' );

		}
	}
}

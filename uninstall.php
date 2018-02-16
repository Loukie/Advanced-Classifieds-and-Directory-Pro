<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package       advanced-classifieds-and-directory-pro
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */

// If uninstall not called from WordPress, then exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete All the Custom Post Types
$acadp_post_types = array( 'acadp_listings', 'acadp_fields', 'acadp_payments' );

foreach( $acadp_post_types as $post_type ) {

	$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	
	if( $items ) {
		foreach( $items as $item ) {
			// Delete attachments (only if applicable)
			if( 'acadp_listings' == $post_type ) {
				$images = get_post_meta( $item, 'images', true );
				
				if( ! empty( $images ) ) {
				
					foreach( $images as $image ) {
						wp_delete_attachment( $image, true );
					}
				
				}
			}
			
			// Delete the actual post
			wp_delete_post( $item, true );
		}
	}
			
}

// Delete All the Terms & Taxonomies
$acadp_taxonomies = array( 'acadp_categories', 'acadp_locations' );

foreach( $acadp_taxonomies as $taxonomy ) {

	$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );
	
	// Delete Terms
	if( $terms ) {
		foreach( $terms as $term ) {
			$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
			$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
		}
	}
	
	// Delete Taxonomies
	$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );

}

// Delete the Plugin Pages
if( $acadp_created_pages = get_option( 'acadp_page_settings' ) ) {

	foreach( $acadp_created_pages as $page => $id ) {

		if( $id > 0 ) {
			wp_delete_post( $id, true );
		}
	
	}

}

// Delete all the Plugin Options
$acadp_settings = array(
	'acadp_general_settings',
	'acadp_listings_settings',
	'acadp_locations_settings',
	'acadp_categories_settings',
	'acadp_registration_settings',
	'acadp_currency_settings',
	'acadp_page_settings',
	'acadp_featured_listing_settings',						
	'acadp_gateway_settings',
	'acadp_gateway_offline_settings',
	'acadp_email_settings',
	'acadp_email_template_listing_submitted',
	'acadp_email_template_listing_published',
	'acadp_email_template_listing_renewal',
	'acadp_email_template_listing_expired',	
	'acadp_email_template_renewal_reminder',
	'acadp_email_template_order_created',
	'acadp_email_template_order_created_offline',
	'acadp_email_template_order_completed',
	'acadp_email_template_listing_contact',
	'acadp_permalink_settings',
	'acadp_socialshare_settings',	
	'acadp_map_settings',
	'acadp_recaptcha_settings',
	'acadp_terms_of_agreement'
);

foreach( $acadp_settings as $settings ) {
	delete_option( $settings );
}

delete_option( 'acadp_categories_children' );
delete_option( 'acadp_locations_children' );
delete_option( 'acadp_version' );

// Delete Capabilities
require_once plugin_dir_path( __FILE__ ) . 'includes/class-acadp-roles.php';

$roles = new ACADP_Roles;
$roles->remove_caps();
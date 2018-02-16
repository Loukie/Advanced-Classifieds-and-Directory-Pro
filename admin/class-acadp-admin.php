<?php

/**
 * The admin-specific functionality of the plugin.
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
 * ACADP_Admin Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Admin {

	/**
	 * Check and update plugin options to the latest version.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function manage_upgrades() {

		if( ACADP_VERSION_NUM !== get_option( 'acadp_version' ) ) {
		
			// Insert new custom pages and update page settings.
			$page_settings = get_option( 'acadp_page_settings' );
			
			if( ! array_key_exists( 'login_form', $page_settings ) ) {
			
				$pages = acadp_insert_custom_pages();
				update_option( 'acadp_page_settings', $pages );
				
			}
			
			// Update plugin version
			update_option( 'acadp_version', ACADP_VERSION_NUM );
		
		}

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_styles() {

		wp_enqueue_style( ACADP_PLUGIN_NAME, ACADP_PLUGIN_URL . 'admin/css/acadp-admin.css', array(), ACADP_VERSION_NUM, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_scripts() {

		$map_settings = get_option( 'acadp_map_settings' );
		$screen = get_current_screen();	
		
		wp_enqueue_media();		
			
		if( 'acadp_listings' == $screen->post_type ) {
			$map_api_key = ! empty( $map_settings['api_key'] ) ? '&key='.$map_settings['api_key'] : '';
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-google-map', 'https://maps.googleapis.com/maps/api/js?v=3.exp'.$map_api_key );
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME, ACADP_PLUGIN_URL . 'admin/js/acadp-admin.js', array( 'jquery' ), ACADP_VERSION_NUM, false );
		
		wp_localize_script( ACADP_PLUGIN_NAME, 'acadp', array(
				'edit'               => __( 'Edit', 'advanced-classifieds-and-directory-pro' ),
				'delete_permanently' => __( 'Delete Permanently', 'advanced-classifieds-and-directory-pro' ),
				'zoom_level'         => $map_settings['zoom_level']
			)
		);

	}
	
	/**
	 * Display Admin Notices.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_notices() { 
		
		// Notice: forever
		$screen = get_current_screen();
		
		if( in_array( $screen->post_type, array( 'acadp_listings', 'acadp_fields', 'acadp_payments' ) ) ) {
			?>    
    		<div class="updated notice notice-info">
    			<p>
					<?php _e( 'ADVANCED CLASSIFIEDS & DIRECTORY PRO', 'advanced-classifieds-and-directory-pro' ); ?>:
                    <a href="https://pluginsware.com/documentation/" target="_blank"><?php _e( 'Read our Online Documentation', 'advanced-classifieds-and-directory-pro' ); ?></a> | 					<a href="https://pluginsware.com/submit-a-ticket/" target="_blank"><?php _e( 'Get help from our Support', 'advanced-classifieds-and-directory-pro' ); ?></a> | 
                    <a href="https://pluginsware.com/add-ons/" target="_blank"><?php _e( 'Check our Premium Add-ons', 'advanced-classifieds-and-directory-pro' ); ?></a>
                </p>
			</div>        
			<?php
    	}
		
	}
	
	/**
	 * Dismiss admin notice.
	 *
	 * @since    1.5.0
	 * @access   public
	 */
	public function ajax_callback_dismiss_admin_notice() {
	
		delete_transient( 'acadp_show_upgrade_notice' );		
		wp_die();
	
	}
	
	/**
	 * Delete an attachment.
	 *
	 * @since    1.5.4
	 * @access   public
	 */
	public function ajax_callback_delete_attachment() {
	
		if( isset( $_POST['attachment_id'] ) ) {
			wp_delete_attachment( $_POST['attachment_id'], true );
		}
		
		wp_die();
	
	}

}

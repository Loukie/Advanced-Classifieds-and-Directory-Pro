<?php

/**
 * Fired during plugin deactivation.
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
 * ACADP_Deactivator Class
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Deactivator {

	/**
	 * Called when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @static	 
	 */
	public static function deactivate() {
	
		delete_option( 'rewrite_rules' );
		
		// Un-schedules all previously-scheduled cron jobs
		wp_clear_scheduled_hook('acadp_hourly_scheduled_events');

	}

}

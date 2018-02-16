<?php

/**
 * Plugin Name:    Advanced Classifieds and Directory Pro
 * Plugin URI:     https://pluginsware.com/
 * Description:    Provides an ability to build any kind of business directory site: classifieds, cars, bikes, boats and other vehicles dealers site, pets, real estate portal, wedding site, yellow pages, etc...
 * Author:         PluginsWare
 * Author URI:     https://pluginsware.com/
 * Version:        1.5.9
 * Text Domain:    advanced-classifieds-and-directory-pro
 * Domain Path:    /languages
 *
 * Advanced Classifieds and Directory Pro is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 2 of the License, or any later
 * version.
 *
 * Advanced Classifieds and Directory Pro is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Advanced Classifieds and Directory Pro. If not,
 * see <http://www.gnu.org/licenses/>.
 *
 * @package    advanced-classifieds-and-directory-pro
 * @author     PluginsWare
 * @version    1.5.9
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

// The unique identifier of this plugin
if( ! defined( 'ACADP_PLUGIN_NAME' ) ) {
    define( 'ACADP_PLUGIN_NAME', 'advanced-classifieds-and-directory-pro' );
}

// The current version of the plugin
if( ! defined( 'ACADP_VERSION_NUM' ) ) {
    define( 'ACADP_VERSION_NUM', '1.5.9' );
}

// Path to the plugin directory
if( ! defined( 'ACADP_PLUGIN_DIR' ) ) {
    define( 'ACADP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// URL of the plugin
if( ! defined( 'ACADP_PLUGIN_URL' ) ) {
    define( 'ACADP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// The code that runs during plugin activation
function activate_acadp() {

	require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-activator.php';
	ACADP_Activator::activate();
	
}

// The code that runs during plugin deactivation
function deactivate_acadp() {

	require_once ACADP_PLUGIN_DIR . 'includes/class-acadp-deactivator.php';
	ACADP_Deactivator::deactivate();
	
}

register_activation_hook( __FILE__, 'activate_acadp' );
register_deactivation_hook( __FILE__, 'deactivate_acadp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ACADP_PLUGIN_DIR . 'includes/class-acadp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acadp() {

	$plugin = new ACADP();
	$plugin->run();
	
	// Include the core widgets
	require ACADP_PLUGIN_DIR . 'widgets/search/class-acadp-widget-search.php';
	require ACADP_PLUGIN_DIR . 'widgets/locations/class-acadp-widget-locations.php';
	require ACADP_PLUGIN_DIR . 'widgets/categories/class-acadp-widget-categories.php';
	require ACADP_PLUGIN_DIR . 'widgets/listings/class-acadp-widget-listings.php';
	require ACADP_PLUGIN_DIR . 'widgets/listing-address/class-acadp-widget-listing-address.php';
	require ACADP_PLUGIN_DIR . 'widgets/listing-contact/class-acadp-widget-listing-contact.php';
	require ACADP_PLUGIN_DIR . 'widgets/listing-video/class-acadp-widget-listing-video.php';

}
run_acadp();

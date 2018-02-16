<?php

/**
 * Deprecated Functions
 *
 * All functions that have been deprecated.
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/includes
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.5.4
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Retrieve paginated link for listing pages.
 *
 * @since         1.0.0
 * @deprecated    1.5.4
 *
 * @param    int      $numpages     The total amount of pages.
 * @param    int      $pagerange    How many numbers to either side of current page.
 * @param    int      $paged        The current page number.
 */
function acadp_pagination( $numpages = '', $pagerange = '', $paged = '' ) {

	the_acadp_pagination( $numpages, $pagerange, $paged );

}

/**
 * Get orderby list.
 *
 * @since         1.0.0
 * @deprecated    1.5.6
 *
 * @return   array    $options    A list of the orderby options.
 */
function acadp_get_orderby_options() {

	return acadp_get_listings_orderby_options();
	
}

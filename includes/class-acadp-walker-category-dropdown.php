<?php

/**
 * Walker Category Dropdown
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
 * ACADP_Walker_CategoryDropdown Class
 *
 * @since    1.5.4
 * @access   public
 */
class ACADP_Walker_CategoryDropdown extends Walker_CategoryDropdown {

	/**
	 * The key that's responsible for enabling / disabling optgroup.
	 *	 
	 * @since    1.5.4
	 * @access   public
	 * @var      bool
	 */
	public $optgroup = false;

	/**
     * Starts the element output.
     *
     * @since    1.5.4
     * @access   public
     *
     * @param    string    $output      Passed by reference. Used to append additional content.
     * @param    object    $category    Category data object.
     * @param    int       $depth       Depth of category. Used for padding.
     * @param    array     $args        Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
     *                                  See wp_dropdown_categories().
     * @param    int       $id          Optional. ID of the current category. Default 0 (unused).
     */
  	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {

    	$pad = str_repeat( ' ', $depth * 3 );
    	$cat_name = apply_filters( 'list_cats', $category->name, $category );
		
		// Set parent optgroup
    	if( 0 == $depth ) {
			$this->optgroup = true;
      		$output .= '<optgroup class="level-$depth" label="' . $cat_name . '" >'; 
    	} else {
			$this->optgroup = false;
			$output .= '<option class="level-' . $depth. '" value="' . $category->term_id . '"';
      		if( $category->term_id == $args['selected'] ) {
           		$output .= ' selected="selected"';
			}
      		$output .= '>' . $pad . $cat_name;
      		if( $args['show_count'] ) {
            	$output .= ' ('. $category->count .')';
			}
      		$output .= "</option>";
    	}

  	}	

	/**
     * Ends the element output, if needed.
     *
     * @since    1.5.4
     * @access   public
     *
     * @param    string    $output    Passed by reference. Used to append additional content.
     * @param    object    $page      Not used.
     * @param    int       $depth     Optional. Depth of category. Not used.
     * @param    array     $args      Optional. An array of arguments. Only uses 'list' for whether should append
     *                                to output. See wp_list_categories(). Default empty array.
     */

  	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
    	
		if( 0 == $depth && true == $this->optgroup ) {
      		$output .= '</optgroup>';
    	}
		
  	}

}

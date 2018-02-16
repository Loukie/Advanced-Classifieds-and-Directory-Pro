<?php

/**
 * This file is used to markup the settings page of the plugin.
 *
 * @link       http://pluginsware.com/
 * @since      1.0.0
 *
 * @package    advanced-classifieds-and-directory-pro
 * @subpackage advanced-classifieds-and-directory-pro/admin/partials
 */
?>

<div class="wrap acadp-settings">
	<?php settings_errors(); ?>
    
    <h2 class="nav-tab-wrapper">
    	<?php
			foreach( $this->tabs as $slug => $title ) {
				$class = "nav-tab";
				if( $this->active_tab == $slug ) $class .= ' nav-tab-active';
				
				echo '<a href="?post_type=acadp_listings&page=acadp_settings&tab='.$slug.'" class="'.$class.'">'.$title.'</a>';
			}
		?>
	</h2>

	<form method="post" action="options.php"> 
    	<?php
			settings_fields( 'acadp_'.$this->active_tab.'_settings' );
			do_settings_sections( 'acadp_'.$this->active_tab.'_settings' );
					
			submit_button();
		?>
    </form>
</div>
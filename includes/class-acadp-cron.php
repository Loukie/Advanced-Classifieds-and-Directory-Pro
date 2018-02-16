<?php

/**
 * CRON.
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
 * ACADP_Cron Class
 *
 * Loads and defines the scheduled callbacks.
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Cron {

	/**
	 * Schedule hourly events.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function schedule_events() {
	
		if( ! wp_next_scheduled( 'acadp_hourly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'acadp_hourly_scheduled_events' );
		}
		
	}
	
	/**
	 * Define actions to execute during the cron event.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function hourly_scheduled_events() {
	
		$this->move_listings_to_renewal();
		$this->move_listings_to_expired();
		$this->send_renewal_reminders();
		$this->delete_expired_listings();
		
	}
	
	/**
	 * Move listings to renewal status (only if applicable).
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function move_listings_to_renewal() {
	
		$general_settings = get_option( 'acadp_general_settings' );	
		$email_settings   = get_option( 'acadp_email_template_listing_renewal' );
			
		$can_renew       = empty( $general_settings['has_listing_renewal'] ) ? false : true;	
		$email_threshold = (int) $email_settings['email_threshold'];		
		
		if( $can_renew && $email_threshold > 0 ) {
			
			$email_threshold_date = date( 'Y-m-d H:i:s', strtotime( "+".$email_threshold." days" ) );
			
			// Define the query
			$args = array(				
				'post_type'      => 'acadp_listings',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
				'meta_query'     => array(
					'relation'    => 'AND',
					array(
						'key'	  => 'listing_status',
						'value'	  => 'post_status',
						'compare' => '='
					),
					array(
						'key'	  => 'expiry_date',
						'value'	  => $email_threshold_date,
						'compare' => '<',
						'type'    => 'DATETIME'
					),
					array(
						'key'	  => 'never_expires',
						'compare' => 'NOT EXISTS',
					)
				)
	  		);
		
			$acadp_query = new WP_Query( $args );
		
			// Start the Loop
			global $post;
		
			if( $acadp_query->have_posts() ) {
		
				while( $acadp_query->have_posts() ) {
				
					$acadp_query->the_post();
					
					// Update the post_meta into the database
					update_post_meta( $post->ID, 'listing_status', 'renewal' );
				
					// Send emails
					acadp_email_listing_owner_listing_renewal( $post->ID );			
					
				}
		
			}
		
			// Use reset postdata to restore orginal query
			wp_reset_postdata();
		
		}
		
	}
	
	/**
	 * Move listings to expired status (only if applicable).
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function move_listings_to_expired() {
		
		$general_settings = get_option( 'acadp_general_settings' );
		$email_settings   = get_option( 'acadp_email_template_renewal_reminder' );
		
		$can_renew = empty( $general_settings['has_listing_renewal'] ) ? false : true;
		if( $can_renew ) {
			$delete_threshold = (int) $email_settings['reminder_threshold'] + (int) $general_settings['delete_expired_listings'];
		} else {
			$delete_threshold = (int) $general_settings['delete_expired_listings'];	
		}

		// Define the query
		$args = array(				
			'post_type'      => 'acadp_listings',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				'relation'   => 'AND',
				array(
					'key'	  => 'expiry_date',
					'value'	  => current_time( 'mysql' ),
					'compare' => '<',
					'type'    => 'DATETIME'
				),
				array(
					'key'	  => 'never_expires',
					'compare' => 'NOT EXISTS',
				)
			)				
	  	);
		
		$acadp_query = new WP_Query( $args );
		
		// Start the Loop
		global $post;
		
		if( $acadp_query->have_posts() ) {
		
			while( $acadp_query->have_posts() ) {
				
				$acadp_query->the_post();
				
				// Update the post into the database
				$acadp_post = array(
					'ID'           => $post->ID,
      				'post_status' => 'private'
				);
				
				wp_update_post( $acadp_post );	

				update_post_meta( $post->ID, 'listing_status', 'expired' );
				update_post_meta( $post->ID, 'featured', 0 );
				update_post_meta( $post->ID, 'renewal_reminder_sent', 0 );
				
				if( $delete_threshold > 0 ) {
					$deletion_date_time = date( 'Y-m-d H:i:s', strtotime( "+".$delete_threshold." days" ) );
					update_post_meta( $post->ID, 'deletion_date', $deletion_date_time );
				}
				
				// Hook for developers
				do_action( 'acadp_listing_expired', $post->ID );
				
				// Send emails
				acadp_email_listing_owner_listing_expired( $post->ID );
				acadp_email_admin_listing_expired( $post->ID );			
					
			}
		
		}
		
		// Use reset postdata to restore orginal query
		wp_reset_postdata();
		
	}
	
	/**
	 * Send renewal reminders to expired listings (only if applicable)
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function send_renewal_reminders() {
		
		$general_settings = get_option( 'acadp_general_settings' );	
		$email_settings   = get_option( 'acadp_email_template_renewal_reminder' );
			
		$can_renew          = empty( $general_settings['has_listing_renewal'] ) ? false : true;	
		$reminder_threshold = (int) $email_settings['reminder_threshold'];		
		
		if( $can_renew && $reminder_threshold > 0 ) {

			// Define the query
			$args = array(				
				'post_type'      => 'acadp_listings',
				'posts_per_page' => -1,
				'post_status'    => 'private',
				'meta_query'     => array(
					'relation'    => 'AND',
					array(
						'key'	  => 'listing_status',
						'value'	  => 'expired',
						'compare' => '='
					),
					array(
						'key'	  => 'renewal_reminder_sent',
						'value'	  => 0,
						'compare' => '='
					),
					array(
						'key'	  => 'never_expires',
						'compare' => 'NOT EXISTS',
					)
				)
	  		);
		
			$acadp_query = new WP_Query( $args );
		
			// Start the Loop
			global $post;
		
			if( $acadp_query->have_posts() ) {
		
				while( $acadp_query->have_posts() ) {
					
					$acadp_query->the_post();
					
					// Send emails
					$expiration_date = get_post_meta( $post->ID, 'expiry_date', true );
            		$expiration_date_time = strtotime( $expiration_date );
					$reminder_date_time = strtotime( "+".$reminder_threshold." days", strtotime( $expiration_date_time ) );
					
					if( current_time( 'timestamp' ) > $reminder_date_time ) {
						update_post_meta( $post->ID, 'renewal_reminder_sent', 1 );
						acadp_email_listing_owner_listing_renewal_reminder( $post->ID );	
					}		
					
				}
		
			}
		
			// Use reset postdata to restore orginal query
			wp_reset_postdata();
		
		}
		
	}
	
	/**
	 * Delete expired listings (only if applicable)
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function delete_expired_listings() {
	
		$general_settings = get_option( 'acadp_general_settings' );
		$email_settings   = get_option( 'acadp_email_template_renewal_reminder' );
		
		$can_renew = empty( $general_settings['has_listing_renewal'] ) ? false : true;
		if( $can_renew ) {
			$delete_threshold = (int) $email_settings['reminder_threshold'] + (int) $general_settings['delete_expired_listings'];	
		} else {
			$delete_threshold = (int) $general_settings['delete_expired_listings'];	
		}
		
		if( $delete_threshold > 0 ) {

			// Define the query
			$args = array(				
				'post_type'      => 'acadp_listings',
				'posts_per_page' => -1,
				'post_status'    => 'private',
				'meta_query'     => array(
					'relation'    => 'AND',
					array(
						'key'	  => 'listing_status',
						'value'	  => 'expired',
						'compare' => '='
					),
					array(
						'key'	  => 'deletion_date',
						'value'	  => current_time( 'mysql' ),
						'compare' => '<',
						'type'    => 'DATETIME'
					),
					array(
						'key'	  => 'never_expires',
						'compare' => 'NOT EXISTS',
					)
				)
	  		);
		
			$acadp_query = new WP_Query( $args );
		
			// Start the Loop
			global $post;
		
			if( $acadp_query->have_posts() ) {
		
				while( $acadp_query->have_posts() ) {
					
					$acadp_query->the_post();

					// Delete the listing
					wp_delete_post( $post->ID, true );	
					
				}
		
			}
		
			// Use reset postdata to restore orginal query
			wp_reset_postdata();
		
		}
		
	}
	
}

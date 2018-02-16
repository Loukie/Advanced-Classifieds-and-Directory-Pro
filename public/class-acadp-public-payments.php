<?php

/**
 * Payments
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/public
 * @copyright     Copyright (c) 2015, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.0.0
 */
 
// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Public_Payments Class
 *
 * @since    1.0.0
 * @access   public
 */
class ACADP_Public_Payments {
	
	/**
	 * Get things going.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Register shortcodes used by the payments page
		add_shortcode( "acadp_checkout", array( $this, "run_shortcode_checkout" ) );
		add_shortcode( "acadp_payment_errors", array( $this, "run_shortcode_payment_errors" ) );
		add_shortcode( "acadp_payment_receipt", array( $this, "run_shortcode_payment_receipt" ) );
		add_shortcode( "acadp_payment_history", array( $this, "run_shortcode_payment_history" ) );

	} 
	
	/**
	 * Process the shortcode [acadp_checkout].
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run_shortcode_checkout() {
	
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();		
		}
		
		$shortcode = 'acadp_checkout';
		
		$post_id = get_query_var('acadp_listing');
		
		if( ! empty( $post_id ) && 'acadp_listings' == get_post_type( $post_id ) ) {			 
			
			if( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['acadp_checkout_nonce'] ) && wp_verify_nonce( $_POST['acadp_checkout_nonce'], 'acadp_process_payment' ) ) {
									
				$this->place_order();
				
			} else {
	
				$options = apply_filters( 'acadp_checkout_form_data', array(), $post_id );
				
				$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
				if( ! empty( $featured_listing_settings['enabled'] ) ) {
					$options[] = array(
						'type'  => 'header',
						'label' => $featured_listing_settings['label']
					);
					
					$options[] = array( 
						'type'        => 'checkbox',
						'name'        => 'featured',
						'value'       => 1,
						'selected'    => 1,
						'description' => $featured_listing_settings['description'],
						'price'       => $featured_listing_settings['price']
					);
				}
				
				// Enqueue style dependencies
				wp_enqueue_style( ACADP_PLUGIN_NAME );
		
				// Enqueue script dependencies
				if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
					wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
				}
				wp_enqueue_script( ACADP_PLUGIN_NAME );
				
				// Hook for developers
				do_action( 'acadp_before_checkout_form' );
			
				// ...	
				ob_start();
				include( acadp_get_template( "payments/acadp-public-checkout-display.php" ) );
				return ob_get_clean();
			
			}
			
		} else {
		
			return '<span>'.__( 'Sorry, something went wrong.', 'advanced-classifieds-and-directory-pro' ).'</span>';
			
		}
	
	}
	
	/**
 	 * Display formatted amount.
     *
     * @since    1.0.0
	 * @access   public
     */
	public function ajax_callback_format_total_amount() {
	
		if( isset( $_POST['amount'] ) ) {	
			echo acadp_format_payment_amount( $_POST['amount'] );					
		}
									
		wp_die();
		
	}
	
	/**
	 * Create Orders. Send emails to site and listing owners
	 * when order placed.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function place_order() {	

		$post_id = (int) $_POST['post_id'];
		
		// place order
		$new_order = array(
			'post_title'   => sprintf( __( '[Order] Listing #%d' ), $post_id ),
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_type'	   => 'acadp_payments'
		);
		
		$order_id = wp_insert_post( $new_order );
		
		if( $order_id ) {
		
			// save meta fields			
			update_post_meta( $order_id, 'listing_id', $post_id );

			do_action( 'acadp_order_created', $order_id );
			
			$order_details = apply_filters( 'acadp_order_details', array(), $order_id );
			
			if( isset( $_POST['featured'] ) ) {
				update_post_meta( $order_id, 'featured', 1 );
				$order_details[] = get_option( 'acadp_featured_listing_settings' );
			}

			$amount = 0.00;
			foreach( $order_details as $order_detail ) {
				$amount += $order_detail['price'];
			}
			
			update_post_meta( $order_id, 'amount', $amount );			
			
			$gateway = ! empty( $amount ) ? sanitize_key( $_POST['payment_gateway'] ) : 'free';
			update_post_meta( $order_id, 'payment_gateway', $gateway );
			
			update_post_meta( $order_id, 'payment_status', 'created' );

			// send email to site admin after order placed successfully
			acadp_email_admin_order_created( $post_id, $order_id );

			// process payment
			if( $amount > 0 ) {
			
				if( 'offline' == $gateway ) {
					update_post_meta( $order_id, 'transaction_id', wp_generate_password( 12, false ) );
				
					acadp_email_listing_owner_order_created_offline( $post_id, $order_id );
			
					$redirect_url = acadp_get_payment_receipt_page_link( $order_id );
					wp_redirect( $redirect_url );
				} else {
					acadp_email_listing_owner_order_created( $post_id, $order_id );
				
					// executes the action hook named 'acadp_process_payment'
					do_action( 'acadp_process_'.$gateway.'_payment', $order_id );
				}
				
			} else {
			
				acadp_email_listing_owner_order_created( $post_id, $order_id );
				
				acadp_order_completed( array( 'id' => $order_id, 'transaction_id' => wp_generate_password( 12, false ) ) );
				
				$redirect_url = acadp_get_payment_receipt_page_link( $order_id );
				wp_redirect( $redirect_url );
					
			}
			
			exit();
			
		}
		
	}
	
	/**
	 * Process the shortcode [acadp_payment_errors].
	 *
	 * @since    1.4.1
	 * @access   public
	 *
	 * @params   array     $atts       An associative array of attributes.
	 * @params   string    $content    Content to display.
	 */
	public function run_shortcode_payment_errors( $atts, $content = '' ) {
	
		if( $order_id = get_query_var('acadp_order') ) {

			if( $error = get_transient( "acadp_payment_errors_{$order_id}" ) ) {
				$content = $error;
    			delete_transient( "acadp_payment_errors_{$order_id}" );
			}
			
		}
		
		return $content;
	
	}
	
	/**
	 * Process the shortcode [acadp_payment_receipt].
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run_shortcode_payment_receipt() {
	
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}
		
		$shortcode = 'acadp_payment_receipt';
		
		if( $order_id = get_query_var('acadp_order') ) {

			$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
			
			// Enqueue style dependencies
			wp_enqueue_style( ACADP_PLUGIN_NAME );
		
			// Enqueue script dependencies
			if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
				wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
			}
			
			wp_enqueue_script( ACADP_PLUGIN_NAME );
			
			// ...
			$order = get_post( $order_id );
			$post_meta = get_post_meta( $order_id );
			
			$order_details = apply_filters( 'acadp_order_details', array(), $order_id );

			if( ! empty( $featured_listing_settings['enabled'] ) && isset( $post_meta['featured'] ) ) {
				$order_details[] = $featured_listing_settings;
			}
			
			ob_start();
			include( acadp_get_template( "payments/acadp-public-payment-receipt-display.php" ) );
			return ob_get_clean();
		
		} else {
		
			return '<span>'.__( 'Sorry, something went wrong.', 'advanced-classifieds-and-directory-pro' ).'</span>';
			
		}
	
	}
	
	/**
	 * Process the shortcode [acadp_payment_history].
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function run_shortcode_payment_history() {
	
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}
		
		if( ! acadp_current_user_can('edit_acadp_listings') ) {
			return '<span>'.__( 'You do not have sufficient permissions to access this page.', 'advanced-classifieds-and-directory-pro' ).'</span>';
		}
		
		$shortcode = 'acadp_payment_history';
		
		$listings_settings = get_option( 'acadp_listings_settings' );

		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies
		if( wp_script_is( ACADP_PLUGIN_NAME.'-bootstrap', 'registered' ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME.'-bootstrap' );
		}
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );

		// Define the query
		$paged = acadp_get_page_number();
			
		$args = array(				
			'post_type'      => 'acadp_payments',
			'posts_per_page' => isset( $listings_settings['listings_per_page'] ) ? $listings_settings['listings_per_page'] : 10,
			'paged'          => $paged,
			'author'         => get_current_user_id(),
	  	);
			
		$acadp_query = new WP_Query( $args );
		
		// Start the Loop
		global $post;
			
		// Process output
		if( $acadp_query->have_posts() ) {
		
			ob_start();
			include( acadp_get_template( "payments/acadp-public-payment-history-display.php" ) );
			wp_reset_postdata(); // Use reset postdata to restore orginal query
			return ob_get_clean();
		
		} else {
		
			return '<span>'.__( 'No Results Found.', 'advanced-classifieds-and-directory-pro' ).'</span>';
		
		}
			
	}
		
}

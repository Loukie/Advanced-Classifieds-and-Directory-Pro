<?php

/**
 * User Registration / Login / Password Reset.
 *
 * @package       advanced-classifieds-and-directory-pro
 * @subpackage    advanced-classifieds-and-directory-pro/public
 * @copyright     Copyright (c) 2017, PluginsWare
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since         1.5.6
 */

// Exit if accessed directly
if( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ACADP_Public_Registration Class
 *
 * @since    1.5.6
 * @access   public
 */
class ACADP_Public_Registration {

	/**
	 * Get things going.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function __construct() {

		// Shortcodes
		add_shortcode( 'acadp_login', array( $this, 'render_login_form' ) );
		add_shortcode( 'acadp_logout', array( $this, 'render_logout_button' ) );		
		add_shortcode( 'acadp_register', array( $this, 'render_register_form' ) );
		add_shortcode( 'acadp_user_account', array( $this, 'render_user_account_page' ) );
		add_shortcode( 'acadp_forgot_password', array( $this, 'render_forgot_password_form' ) );
		add_shortcode( 'acadp_password_reset', array( $this, 'render_password_reset_form' ) );
		
	}

	//
	// REDIRECT FUNCTIONS
	//

	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function redirect_to_custom_login() {
	
		if( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
		
			if( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}
			
			// The rest are redirected to the login page
			$page_settings = get_option( 'acadp_page_settings' );
			
        	$login_url = get_permalink( $page_settings['login_form'] );
			
			if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
				$login_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $login_url );
			}

			if( ! empty( $_REQUEST['checkemail'] ) ) {
				$login_url = add_query_arg( 'checkemail', $_REQUEST['checkemail'], $login_url );
			}

			wp_redirect( $login_url );
			exit;
			
		}
		
	}

	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param    Wp_User|Wp_Error    $user        The signed in user, or the errors that have occurred during login.
	 * @param    string              $username    The user name used to log in.
	 * @param    string              $password    The password used to log in.
	 * @return   Wp_User|Wp_Error                 The logged in user, or error information if there were errors.
	 */
	public function maybe_redirect_at_authenticate( $user, $username, $password ) {
	
		// Check if the earlier authenticate filter (most likely,
		// the default WordPress authentication) functions have found errors
		if( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		
			if( is_wp_error( $user ) ) {
				$page_settings = get_option( 'acadp_page_settings' );
				
				$error_codes = join( ',', $user->get_error_codes() );

				$login_url = get_permalink( $page_settings['login_form'] );
				$login_url = add_query_arg( 'login', $error_codes, $login_url );
				
				if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
					$login_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $login_url );
				}

				wp_redirect( $login_url );
				exit;
			}
			
		}

		return $user;
		
	}

	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param    string              $redirect_to              The redirect destination URL.
	 * @param    string              $requested_redirect_to    The requested redirect destination URL passed as a parameter.
	 * @param    WP_User|WP_Error    $user                     WP_User object if login was successful, WP_Error object otherwise.
	 * @return   string                                        Redirect URL
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {

		if( ! isset( $user->ID ) ) {
			return $redirect_to;
		}

		// Use the redirect_to parameter if one is set, otherwise redirect to their account page.
		if( '' == $requested_redirect_to ) {
			$page_settings = get_option( 'acadp_page_settings' );
			$redirect_url  = get_permalink( $page_settings['user_account'] );
		} else {
			$redirect_url = $redirect_to;
		}			

		return wp_validate_redirect( $redirect_url, home_url() );
		
	}

	/**
	 * Redirect to custom login page after the user has been logged out.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function redirect_after_logout() {
	
		$page_settings = get_option( 'acadp_page_settings' );
		
		$redirect_url = get_permalink( $page_settings['login_form'] );
		$redirect_url = add_query_arg( 'logged_out', 'true', $redirect_url );
		
		wp_redirect( $redirect_url );
		exit;
		
	}

	/**
	 * Redirects the user to the custom registration page instead
	 * of wp-login.php?action=register.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function redirect_to_custom_register() {
	
		if( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
		
			if( is_user_logged_in() ) {
			
				$this->redirect_logged_in_user();
				
			} else {
			
				$page_settings = get_option( 'acadp_page_settings' );
		
				$redirect_url = get_permalink( $page_settings['register_form'] );
				wp_redirect( $redirect_url );
				
			}
			exit;
			
		}
		
	}

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of
	 * wp-login.php?action=lostpassword.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function redirect_to_custom_lostpassword() {
	
		if( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
		
			if( is_user_logged_in() ) {
				$this->redirect_logged_in_user();
				exit;
			}

			$page_settings = get_option( 'acadp_page_settings' );
		
			$redirect_url = get_permalink( $page_settings['forgot_password'] );
			wp_redirect( $redirect_url );
			exit;
			
		}
		
	}

	/**
	 * Redirects to the custom password reset page, or the login page
	 * if there are errors.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function redirect_to_custom_password_reset() {
	
		if( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
		
			$page_settings = get_option( 'acadp_page_settings' );
			
			// Verify key / login combo
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
			
			if( ! $user || is_wp_error( $user ) ) {
				$redirect_url = get_permalink( $page_settings['login_form'] );
				
				if( $user && $user->get_error_code() === 'expired_key' ) {
					$redirect_url = add_query_arg( 'login', 'expiredkey', $redirect_url );
				} else {
					$redirect_url = add_query_arg( 'login', 'invalidkey', $redirect_url );
				}
				
				wp_redirect( $redirect_url );
				exit;
			}

			$redirect_url = get_permalink( $page_settings['password_reset'] );
			$redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
			$redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

			wp_redirect( $redirect_url );
			exit;
		}
		
	}


	//
	// FORM RENDERING SHORTCODES
	//

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param     array     $attributes    Shortcode attributes.
     * @param     string    $content       The text content for shortcode. Not used.
	 * @return    string                   The shortcode output
	 */
	public function render_login_form( $attributes, $content = null ) {
	
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		if( is_user_logged_in() ) {
			return do_shortcode( '[acadp_logout]' );
		}
		
		// Parse shortcode attributes
		$default_attributes = array( 'redirect' => '' );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		// Pass the redirect parameter to the WordPress login functionality: by default,
		// don't specify a redirect, but if a valid redirect URL has been passed as
		// request parameter, use it.
		if( isset( $_REQUEST['redirect_to'] ) ) {
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
		}

		// Error messages
		$errors = array();
		if( isset( $_REQUEST['login'] ) ) {
			$error_codes = explode( ',', $_REQUEST['login'] );

			foreach( $error_codes as $code ) {
				if( 'invalid_username' == $code ) $code = 'invalidcombo';
				$errors[] = $this->get_error_message( $code );
			}
		}
		$attributes['errors'] = $errors;

		// Check if user just logged out
		$attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;

		// Check if the user just registered
		$attributes['registered'] = isset( $_REQUEST['registered'] );

		// Check if the user just requested a new password
		$attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';

		// Check if user just updated password
		$attributes['password_updated'] = isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed';
		
		// Forgot Password, Registration URLs
		$page_settings = get_option( 'acadp_page_settings' );
			
        $attributes['forgot_password_url'] = get_permalink( $page_settings['forgot_password'] );
		$attributes['register_url'] = get_permalink( $page_settings['register_form'] );
		
		if( ! empty( $attributes['redirect'] ) ) {
			$attributes['forgot_password_url'] = add_query_arg( 'redirect_to', rawurlencode( $attributes['redirect'] ), $attributes['forgot_password_url'] );
			$attributes['register_url'] = add_query_arg( 'redirect_to', rawurlencode( $attributes['redirect'] ), $attributes['register_url'] );
		}

		// Render the login form using an external template
		return $this->get_template_html( 'login', $attributes );
		
	}
	
	/**
	 * A shortcode for rendering the logout button.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param     array     $attributes    Shortcode attributes.
     * @param     string    $content       The text content for shortcode. Not used.
	 * @return    string                   The shortcode output
	 */
	public function render_logout_button( $attributes, $content = null ) {
	
		return $this->get_template_html( 'logout', $attributes );
	
	}

	/**
	 * A shortcode for rendering the new user registration form.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param    array    $attributes    Shortcode attributes.
	 * @param    string   $content       The text content for shortcode. Not used.
	 * @return   string                  The shortcode output
	 */
	public function render_register_form( $attributes, $content = null ) {
	
		if( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'advanced-classifieds-and-directory-pro' );
		} else if( ! get_option( 'users_can_register' ) ) {
			return __( 'Registering new users is currently not allowed.', 'advanced-classifieds-and-directory-pro' );
		} 
		
		$recaptcha_settings = get_option( 'acadp_recaptcha_settings' );
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );
		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		if( isset( $recaptcha_settings['forms'] ) && in_array( 'registration', $recaptcha_settings['forms'] ) ) {
			wp_enqueue_script( ACADP_PLUGIN_NAME . "-recaptcha" );
		}
		
		// Parse shortcode attributes
		$default_attributes = array( 'redirect' => '' );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		// If a valid redirect URL has been passed as request parameter, use it.
		if( isset( $_REQUEST['redirect_to'] ) ) {
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
		}
		
		// Retrieve possible errors from request parameters
		$attributes['errors'] = array();
		if( isset( $_REQUEST['register-errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['register-errors'] );

			foreach( $error_codes as $error_code ) {
				$attributes['errors'][] = $this->get_error_message( $error_code );
			}
		}

		return $this->get_template_html( 'register', $attributes );
		
	}
	
	/**
	 * A shortcode for rendering the user account page.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param     array     $attributes    Shortcode attributes.
     * @param     string    $content       The text content for shortcode. Not used.
	 * @return    string                   The shortcode output
	 */
	public function render_user_account_page( $attributes, $content = null ) {
		
		if( ! is_user_logged_in() ) {		
			return acadp_login_form();			
		}	
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		// Parse shortcode attributes
		$default_attributes = array();
		$attributes = shortcode_atts( $default_attributes, $attributes );
		
		// Error messages
		$errors = array();
		if( isset( $_REQUEST['update-errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['update-errors'] );

			foreach( $error_codes as $code ) {
				$errors[] = $this->get_error_message( $code );
			}
		}
		$attributes['errors'] = $errors;
		
		// Check if user just updated his/her account
		$attributes['account_updated'] = isset( $_REQUEST['update'] ) && $_REQUEST['update'] == 'success';
		
		// Get Userdata
		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );
		
		$attributes['username']   = $user->user_login;
		$attributes['email']      = $user->user_email;
      	$attributes['first_name'] = $user->first_name;
      	$attributes['last_name']  = $user->last_name;

		// Render the user account page using an external template
		return $this->get_template_html( 'user-account', $attributes );
		
	}

	/**
	 * A shortcode for rendering the form used to initiate the password reset.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param    array    $attributes    Shortcode attributes.
	 * @param    string   $content       The text content for shortcode. Not used.
	 * @return   string                  The shortcode output
	 */
	public function render_forgot_password_form( $attributes, $content = null ) {
	
		if( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'advanced-classifieds-and-directory-pro' );
		}
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		// Parse shortcode attributes
		$default_attributes = array( 'redirect' => '' );
		$attributes = shortcode_atts( $default_attributes, $attributes );
		
		// If a valid redirect URL has been passed as request parameter, use it.
		if( isset( $_REQUEST['redirect_to'] ) ) {
			$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
		}

		// Retrieve possible errors from request parameters
		$attributes['errors'] = array();
		if( isset( $_REQUEST['errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['errors'] );

			foreach( $error_codes as $error_code ) {
				$attributes['errors'][] = $this->get_error_message( $error_code );
			}
		}

		return $this->get_template_html( 'forgot-password', $attributes );
		
	}

	/**
	 * A shortcode for rendering the form used to reset a user's password.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param    array    $attributes    Shortcode attributes.
	 * @param    string   $content       The text content for shortcode. Not used.
	 * @return   string                  The shortcode output
	 */
	public function render_password_reset_form( $attributes, $content = null ) {
	
		if( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'advanced-classifieds-and-directory-pro' );
		}
		
		// Enqueue style dependencies
		wp_enqueue_style( ACADP_PLUGIN_NAME );
		
		// Enqueue script dependencies		
		wp_enqueue_script( ACADP_PLUGIN_NAME.'-validator' );		
		wp_enqueue_script( ACADP_PLUGIN_NAME );
		
		// Parse shortcode attributes
		$default_attributes = array( 'redirect' => '' );
		$attributes = shortcode_atts( $default_attributes, $attributes );

		if( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
			$attributes['login'] = $_REQUEST['login'];
			$attributes['key']   = $_REQUEST['key'];
			
			// If a valid redirect URL has been passed as request parameter, use it.
			if( isset( $_REQUEST['redirect_to'] ) ) {
				$attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
			}

			// Error messages
			$errors = array();
			if( isset( $_REQUEST['error'] ) ) {
				$error_codes = explode( ',', $_REQUEST['error'] );

				foreach( $error_codes as $code ) {
					$errors[] = $this->get_error_message( $code );
				}
			}
			$attributes['errors'] = $errors;

			return $this->get_template_html( 'password-reset', $attributes );
		} else {
			return __( 'Invalid password reset link.', 'advanced-classifieds-and-directory-pro' );
		}
		
	}

	/**
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @since    1.5.6
	 * @access   private
	 *
	 * @param    string    $template_name    The name of the template to render (without .php)
	 * @param    array     $attributes       The PHP variables for the template
	 * @return   string                      The contents of the template.
	 */
	private function get_template_html( $template_name, $attributes = null ) {
	
		if( ! $attributes ) {
			$attributes = array();
		}

		ob_start();

		do_action( 'acadp_login_before_' . $template_name );

		require( acadp_get_template( 'registration/acadp-public-' . $template_name . '-display.php' ) );

		do_action( 'acadp_login_after_' . $template_name );

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
		
	}


	//
	// ACTION HANDLERS FOR FORMS IN FLOW
	//

	/**
	 * Manage form submissions.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function manage_actions() {	

		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		
			if( isset( $_POST['acadp_user_account_nonce'] ) && wp_verify_nonce( $_POST['acadp_user_account_nonce'], 'acadp_update_user_account' ) ) {
				$this->do_update_user_account();
			}
			
		}
		
	}
	
	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function do_register_user() {
	
		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		
			$page_settings = get_option( 'acadp_page_settings' );
			
			$redirect_url = get_permalink( $page_settings['register_form'] );

			if( ! get_option( 'users_can_register' ) ) {
				// Registration closed, display error
				$redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
			} else if( ! acadp_is_human( 'registration' ) ) {
				// Recaptcha check failed, display error
				$redirect_url = add_query_arg( 'register-errors', 'captcha', $redirect_url );
			} else {
				$result = $this->register_user();

				if( is_wp_error( $result ) ) {
					// Parse errors into a string and append as parameter to redirect
					$errors = join( ',', $result->get_error_codes() );
					$redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				} else {
					// Success, redirect to login page.
					$email = sanitize_email( $_POST['email'] );
					
					$redirect_url = get_permalink( $page_settings['login_form'] );
					$redirect_url = add_query_arg( 'registered', $email, $redirect_url );
					
					if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
						$redirect_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $redirect_url );
					}
				}
			}

			wp_redirect( $redirect_url );
			exit;
			
		}
		
	}
	
	/**
	 * Updates the user account.
	 *
	 * @since    1.5.6
	 * @access   private
	 */
	private function do_update_user_account() {
	
		$page_settings = get_option( 'acadp_page_settings' );
			
		$redirect_url = get_permalink( $page_settings['user_account'] );
		
		$result = $this->update_user_account();

		if( is_wp_error( $result ) ) {
			// Parse errors into a string and append as parameter to redirect
			$errors = join( ',', $result->get_error_codes() );
			$redirect_url = add_query_arg( 'update-errors', $errors, $redirect_url );
		} else {
			// Success, redirect to user account page.
			$redirect_url = get_permalink( $page_settings['user_account'] );
			$redirect_url = add_query_arg( 'update', 'success', $redirect_url );
		}

		wp_redirect( $redirect_url );
		exit;
		
	}

	/**
	 * Initiates password reset.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function do_forgot_password() {
	
		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		
			$page_settings = get_option( 'acadp_page_settings' );
			
			$errors = retrieve_password();
			if( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = get_permalink( $page_settings['forgot_password'] );
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = get_permalink( $page_settings['login_form'] );
				$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
				
				if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
					$redirect_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $redirect_url );
				}
			}

			wp_safe_redirect( $redirect_url );
			exit;
			
		}
		
	}

	/**
	 * Resets the user's password if the password reset form was submitted.
	 *
	 * @since    1.5.6
	 * @access   public
	 */
	public function do_password_reset() {
	
		if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$page_settings = get_option( 'acadp_page_settings' );
			
			$rp_key   = $_REQUEST['rp_key'];
			$rp_login = $_REQUEST['rp_login'];

			$user = check_password_reset_key( $rp_key, $rp_login );

			if( ! $user || is_wp_error( $user ) ) {
				$redirect_url = get_permalink( $page_settings['login_form'] );
				
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					$redirect_url = add_query_arg( 'login', 'expiredkey', $redirect_url );
				} else {
					$redirect_url = add_query_arg( 'login', 'invalidkey', $redirect_url );
				}
				
				if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
					$redirect_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $redirect_url );
				}
				
				wp_redirect( $redirect_url );
				exit;
			}

			if( isset( $_POST['pass1'] ) ) {
				if( $_POST['pass1'] != $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = get_permalink( $page_settings['password_reset'] );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_mismatch', $redirect_url );
					
					if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
						$redirect_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $redirect_url );
					}

					wp_redirect( $redirect_url );
					exit;
				}

				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = get_permalink( $page_settings['password_reset'] );

					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'empty_password', $redirect_url );
					
					if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
						$redirect_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $redirect_url );
					}

					wp_redirect( $redirect_url );
					exit;

				}

				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				
				$redirect_url = get_permalink( $page_settings['login_form'] );
				$redirect_url = add_query_arg( 'password', 'changed', $redirect_url );
				
				if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
					$redirect_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $redirect_url );
				}
				
				wp_redirect( $redirect_url );
			} else {
				_e( "Invalid request.", 'advanced-classifieds-and-directory-pro' );
			}

			exit;
		}
		
	}


	//
	// OTHER CUSTOMIZATIONS
	//

	/**
	 * Returns the message body for the password reset mail.
	 * Called through the retrieve_password_message filter.
	 *
	 * @since    1.5.6
	 * @access   public
	 *
	 * @param    string     $message       Default mail message.
	 * @param    string     $key           The activation key.
	 * @param    string     $user_login    The username for the user.
	 * @param    WP_User    $user_data     WP_User object.
	 * @return   string                    The mail message to send.
	 */
	public function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
	
		// Create new message
		$password_reset_url = site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ) );
		if( ! empty( $_REQUEST['redirect_to'] ) && $redirect_to = wp_validate_redirect( $_REQUEST['redirect_to'] ) ) {
			$password_reset_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $password_reset_url );
		}
		
		$message  = __( 'Hi there,', 'advanced-classifieds-and-directory-pro' ) . "\r\n\r\n";
		$message .= __( 'Someone has requested a password reset for the following account:', 'advanced-classifieds-and-directory-pro' ) . "\r\n\r\n";
   	 	$message .= home_url( '/' ) . "\r\n\r\n";
    	$message .= sprintf( __( 'Username: %s', 'advanced-classifieds-and-directory-pro' ), $user_login ) . "\r\n\r\n";
    	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'advanced-classifieds-and-directory-pro' ) . "\r\n\r\n";
    	$message .= __( 'To reset your password, visit the following address:', 'advanced-classifieds-and-directory-pro' ) . "\r\n\r\n";
    	$message .= '<' . $password_reset_url . ">\r\n";
		$message .= __( 'Thanks!', 'advanced-classifieds-and-directory-pro' ) . "\r\n";

		return $message;
		
	}


	//
	// HELPER FUNCTIONS
	//

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @since    1.5.6
	 * @access   private
	 *
	 * @return   int|WP_Error    The id of the user that was created, or error if failed.
	 */
	private function register_user() {
	
		$errors = new WP_Error();

		$first_name = sanitize_text_field( $_POST['first_name'] );
		$last_name = sanitize_text_field( $_POST['last_name'] );

		// Validate username
		$username = sanitize_text_field( $_POST['username'] );
		
		if( ! validate_username( $username ) ) {
			$errors->add( 'invalid_username', $this->get_error_message( 'invalid_username' ) );
			return $errors;
		}

		if( username_exists( $username ) ) {
			$errors->add( 'username_exists', $this->get_error_message( 'username_exists' ) );
			return $errors;
		}
		
		// Validate email		
		$email = sanitize_email( $_POST['email'] );
		
		if( ! is_email( $email ) ) {
			$errors->add( 'invalid_email', $this->get_error_message( 'invalid_email' ) );
			return $errors;
		}

		if( email_exists( $email ) ) {
			$errors->add( 'email_exists', $this->get_error_message( 'email_exists' ) );
			return $errors;
		}
		
		// Validate password	
		$password = sanitize_text_field( $_POST['pass1'] );
		
		if( empty( $password ) ) {
			// Password is empty
			$errors->add( 'empty_password', $this->get_error_message( 'empty_password' ) );
			return $errors;
		}
					
		if( $password != $_POST['pass2'] ) {
			// Passwords don't match
			$errors->add( 'password_mismatch', $this->get_error_message( 'password_mismatch' ) );
			return $errors;
		}

		// Generate the password so that the subscriber will have to check email...
		$user_data = array(
			'user_login' => $username,
			'user_email' => $email,
			'user_pass'  => $password,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'nickname'   => $first_name,
		);

		$user_id = wp_insert_user( $user_data );
		acadp_new_user_notification( $user_id, $password );

		return $user_id;
		
	}
	
	/**
	 * Validates and then updates the user account.
	 *
	 * @since    1.5.6
	 * @access   private
	 *
	 * @return   int|WP_Error    The id of the user that was updated, or error if failed.
	 */
	private function update_user_account() {
	
		$errors = new WP_Error();
		
		$user_id = get_current_user_id();
		$first_name = sanitize_text_field( $_POST['first_name'] );
		$last_name = sanitize_text_field( $_POST['last_name'] );
		
		// Validate email	
		$email = sanitize_email( $_POST['email'] );
			
		if( ! is_email( $email ) ) {
			$errors->add( 'invalid_email', $this->get_error_message( 'invalid_email' ) );
			return $errors;
		}

		if( $id = email_exists( $email ) ) {
		
			if( $id != $user_id ) {
				$errors->add( 'email_exists', $this->get_error_message( 'email_exists' ) );
				return $errors;
			}
			
		}
		
		// Validate password	
		$password = '';
		
		if( isset( $_POST['change_password'] ) ) {
			$password = sanitize_text_field( $_POST['pass1'] );
			
			if( empty( $password ) ) {
				// Password is empty
				$errors->add( 'empty_password', $this->get_error_message( 'empty_password' ) );
				return $errors;
			}
						
			if( $password != $_POST['pass2'] ) {
				// Passwords don't match
				$errors->add( 'password_mismatch', $this->get_error_message( 'password_mismatch' ) );
				return $errors;
			}
		}

		// Generate the password so that the subscriber will have to check email...
		$user_data = array(
			'ID'         => $user_id,
			'user_email' => $email,
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'nickname'   => $first_name,
		);
		
		if( ! empty( $password ) ) {
			$user_data['user_pass'] = $password;
		}

		$user_id = wp_update_user( $user_data );

		return $user_id;
		
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @since    1.5.6
	 * @access   private
	 *
	 * @param    string    $redirect_to    An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {
	
		$user = wp_get_current_user();
		
		if( user_can( $user, 'manage_options' ) ) {
		
			if( $redirect_to ) {
				wp_safe_redirect( $redirect_to );
			} else {
				wp_redirect( admin_url() );
			}
			
		} else {
		
			$page_settings = get_option( 'acadp_page_settings' );
			
        	$redirect_to = get_permalink( $page_settings['user_account'] );
			wp_redirect( $redirect_to );
			
		}
		
	}

	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @since    1.5.6
	 * @access   private
	 *
	 * @param    string    $error_code    The error code to look up.
	 * @return   string                   An error message.
	 */
	private function get_error_message( $error_code ) {
	
		switch( $error_code ) {

			case 'empty_username':
				return __( 'The username field is empty.', 'advanced-classifieds-and-directory-pro' );
				
			case 'invalid_username':
				return __( "Invalid username.", 'advanced-classifieds-and-directory-pro' );
				
			case 'username_exists':
				return __( 'Sorry, that username already exists!', 'advanced-classifieds-and-directory-pro' );

			case 'empty_email':
				return __( 'The email field is empty.', 'advanced-classifieds-and-directory-pro' );
				
			case 'invalid_email':
				return __( 'Invalid email address.', 'advanced-classifieds-and-directory-pro' );

			case 'email_exists':
				return __( 'Sorry, that email address already exists!', 'advanced-classifieds-and-directory-pro' );
				
			case 'empty_password':
				return __( 'The password field is empty.', 'advanced-classifieds-and-directory-pro' );	
				
			case 'password_mismatch':
				return __( "The two passwords you entered don't match.", 'advanced-classifieds-and-directory-pro' );		

			case 'incorrect_password':
				$err  = __( 'The password you entered is incorrect.', 'advanced-classifieds-and-directory-pro' );
				$err .= sprintf( ' <a href="%s">%s</a>', wp_lostpassword_url(), __( 'Lost your password?', 'advanced-classifieds-and-directory-pro' ) );
				
				return $err;
				
			case 'invalidcombo':
				return __( 'Invalid username or email.', 'advanced-classifieds-and-directory-pro' );

			case 'closed':
				return __( 'Registering new users is currently not allowed.', 'advanced-classifieds-and-directory-pro' );

			case 'captcha':
				return __( 'Invalid Captcha: Please try again.', 'advanced-classifieds-and-directory-pro' );

			case 'expiredkey':
			case 'invalidkey':
				return __( 'The password reset link you used is not valid anymore.', 'advanced-classifieds-and-directory-pro' );

			default:
				break;
		}

		return __( 'An unknown error occurred. Please try again later.', 'advanced-classifieds-and-directory-pro' );
		
	}

}
<?php

/**
 * Email functions.
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
 * Send mail, similar to PHP’s mail.
 *
 * @since    1.0.0
 *
 * @param    string|array    $to             Array or comma-separated list of email addresses to send message.
 * @param    string          $subject        Email subject.
 * @param    string          $message        Message contents.
 * @param    string|array    $headers        Additional headers.
 * @param    string|array    $attachments    Files to attach.
 * @return   bool                            Whether the email contents were sent successfully.
 */
function acadp_send_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {

	add_filter( 'wp_mail_content_type', 'acadp_set_html_mail_content_type' );
	$success = wp_mail( $to, html_entity_decode( $subject ), $message, $headers );
	remove_filter( 'wp_mail_content_type', 'acadp_set_html_mail_content_type' );
		
	return $success;
	
}

/**
 * Get the admin notification email IDs.
 *
 * @since    1.4.0
 *
 * @param	 array           $settings    ACADP Email Settings.
 * @return	 string|array    $to          Array or comma-separated list of email addresses to send message.
 */
function acadp_get_admin_email_id_s( $settings = array() ) {
	
	$to = '';
	
	if( empty( $settings ) ) {
		$settings = get_option( 'acadp_email_settings' );	
	}
	
	if( ! empty( $settings['admin_notice_emails'] ) ) {
		$to = explode( "\n", $settings['admin_notice_emails'] );
		$to = array_map( 'trim', $to );
		$to = array_filter( $to );
	}
	
	if( empty( $to ) ) {
		$to = get_bloginfo( 'admin_email' );
	}

	return $to;
	
}

/**
 * Get the email headers.
 *
 * @since    1.4.0
 *
 * @param	 array           $settings    ACADP Email Settings.
 * @return	 string|array    $to          Email headers.
 */
function acadp_get_email_headers( $settings = array() ) {
	
	$headers = '';
	
	$name  = get_option( 'blogname');
	$email = get_option( 'admin_email' );
	
	if( empty( $settings ) ) {
		$settings = get_option( 'acadp_email_settings' );	
	}
		
	if( ! empty( $settings['from_name'] ) ) {
		$name = $settings['from_name'];
	}
	
	if( ! empty( $settings['from_email'] ) ) {
		$email = $settings['from_email'];
	}
	
	$headers .= "From: {$name} <{$email}>\r\n";
	$headers .= "Reply-To: {$email}\r\n";

	return $headers;
	
}

/**
 * Set the email content type.
 *
 * @since    1.0.0
 *
 * @params	 string    $content_type    Default content type.
 */
function acadp_set_html_mail_content_type( $content_type ) {
	
	return 'text/html';
	
}

/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @since    1.5.6
 *
 * @param    int       $user_id           User ID.
 * @param    string    $plaintext_pass    Plain text password.
 */
function acadp_new_user_notification( $user_id, $plaintext_pass = '' ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	$user = get_userdata( $user_id );
    $blog_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$headers = acadp_get_email_headers( $email_settings );

	// Notify administrators
	$admin_email_ids_s = acadp_get_admin_email_id_s( $email_settings );
	
	$subject  = sprintf( __( '[%s] New User Registration', 'advanced-classifieds-and-directory-pro' ), $blog_name );
	
	$message  = sprintf( __( 'New user registration on your site %s:', 'advanced-classifieds-and-directory-pro' ), $blog_name ) . "<br /><br />";
    $message .= sprintf( __( 'Username: %s', 'advanced-classifieds-and-directory-pro' ), $user->user_login ) . "<br /><br />";
    $message .= sprintf( __( 'Email: %s', 'advanced-classifieds-and-directory-pro' ), '<a href="mailto:'.$user->user_email.'">'.$user->user_email.'</a>' ) . "<br />";

	acadp_send_mail( $admin_email_ids_s, $subject, $message, $headers );

	if( empty( $plaintext_pass ) ) return;

	// Notify user
	$subject  = sprintf( __( '[%s] Your username and password info', 'advanced-classifieds-and-directory-pro' ), $blog_name );
	
	$message  = __( 'Hi there,', 'advanced-classifieds-and-directory-pro' ) . "<br /><br />";
	$message .= sprintf( __( "Welcome to %s! Here's how to log in:", 'advanced-classifieds-and-directory-pro' ), $blog_name ) . "<br /><br />";
	$message .= acadp_get_user_login_page_link() . "<br />";
	$message .= sprintf( __( 'Username: %s', 'advanced-classifieds-and-directory-pro' ), $user->user_login ) . "<br />";
	$message .= sprintf( __( 'Password: %s', 'advanced-classifieds-and-directory-pro' ), $plaintext_pass ) . "<br /><br />";
	$message .= __( 'Thanks!', 'advanced-classifieds-and-directory-pro' );

	acadp_send_mail( $user->user_email, $subject, $message, $headers );  
  
}

/**
 * Notify admin when a new listing submitted.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_admin_listing_submitted( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_admin'] ) && in_array( 'listing_submitted', $email_settings['notify_admin'] ) ) {
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		
		$placeholders = array(
			'{name}'           => $user->display_name,
			'{username}'       => $user->user_login,
			'{site_name}'      => get_bloginfo( 'name' ),
			'{listing_id}'     => $post_id,
			'{listing_title}'  => get_the_title( $post_id ),
			'{listing_status}' => ( 'publish' == get_post_status( $post_id ) ) ? __( 'Active', 'advanced-classifieds-and-directory-pro' ) : sprintf( '<a href="%s">%s</a>', admin_url( "post.php?post=$post_id&action=edit" ), __( 'Pending review', 'advanced-classifieds-and-directory-pro' ) )
		);
			
		$to = acadp_get_admin_email_id_s( $email_settings );
		
		$subject = __( '[{site_name}] New Listing received', 'advanced-classifieds-and-directory-pro' );
		$subject = strtr( $subject, $placeholders );
		
		$message = __( "Dear Administrator,<br /><br />You have received a new listing on the website {site_name}.<br />This e-mail contains the listing details:<br /><br />Listing ID:{listing_id}<br />Listing Title:{listing_title}<br />Listing Status:{listing_status}<br /><br />Please do not respond to this message. It is automatically generated and is for information purposes only.", 'advanced-classifieds-and-directory-pro' );
		$message = strtr( $message, $placeholders );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}


/**
 * Notify listing owner when his listing submitted.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_listing_owner_listing_submitted( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'listing_submitted', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_listing_submitted' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => $site_name,
			'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{listing_title}' => $listing_title,
			'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{today}'         => date_i18n( $date_format, $current_time ),
			'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
	
		$headers = acadp_get_email_headers( $email_settings );
		
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify listing owner when his listing approved/published.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_listing_owner_listing_approved( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'listing_published', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_listing_published' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => $site_name,
			'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{listing_title}' => $listing_title,
			'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{today}'         => date_i18n( $date_format, $current_time ),
			'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}

}

/**
 * Notify admin when a listing is edited.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_admin_listing_edited( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_admin'] ) && in_array( 'listing_edited', $email_settings['notify_admin'] ) ) {
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		
		$placeholders = array(
			'{name}'           => $user->display_name,
			'{username}'       => $user->user_login,
			'{site_name}'      => get_bloginfo( 'name' ),
			'{listing_id}'     => $post_id,
			'{listing_title}'  => get_the_title( $post_id ),
			'{listing_status}' => ( 'publish' == get_post_status( $post_id ) ) ? __( 'Active', 'advanced-classifieds-and-directory-pro' ) : sprintf( '<a href="%s">%s</a>', admin_url( "post.php?post=$post_id&action=edit" ), __( 'Pending review', 'advanced-classifieds-and-directory-pro' ) )
		);
			
		$to = acadp_get_admin_email_id_s( $email_settings );
		
		$subject = __( '[{site_name}] Listing "{listing_title}" edited', 'advanced-classifieds-and-directory-pro' );
		$subject = strtr( $subject, $placeholders );
		
		$message = __( "Dear Administrator,<br /><br />This notification was for the listing on the website {site_name} \"{listing_title}\" and is edited.<br />This e-mail contains the listing details:<br /><br />Listing ID:{listing_id}<br />Listing Title:{listing_title}<br />Listing Status:{listing_status}<br /><br />Please do not respond to this message. It is automatically generated and is for information purposes only.", 'advanced-classifieds-and-directory-pro' );
		$message = strtr( $message, $placeholders );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify listing owner when his listing is about to expire.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_listing_owner_listing_renewal( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'listing_renewal', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_listing_renewal' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$never_expires  = get_post_meta( $post_id, 'never_expires', true );
		$expiry_date    = get_post_meta( $post_id, 'expiry_date', true );
		$categories     = wp_get_object_terms( $post_id, 'acadp_categories', array( 'fields' => 'names' ) );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'            => $user->display_name,
			'{username}'        => $user->user_login,
			'{site_name}'       => $site_name,
			'{site_link}'       => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'        => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{expiration_date}' => ! empty( $never_expires ) ? __( 'Never Expires', 'advanced-classifieds-and-directory-pro' ) : date_i18n( $date_format, strtotime( $expiry_date ) ),
			'{category_name}'   => ! empty( $categories ) ? $categories[0] : '',
			'{renewal_link}'    => acadp_get_listing_renewal_page_link( $post_id ),
			'{listing_title}'   => $listing_title,
			'{listing_link}'    => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'     => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{today}'           => date_i18n( $date_format, $current_time ),
			'{now}'             => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify admin when a listing expired.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_admin_listing_expired( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_admin'] ) && in_array( 'listing_expired', $email_settings['notify_admin'] ) ) {

		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$never_expires  = get_post_meta( $post_id, 'never_expires', true );
		$expiry_date    = get_post_meta( $post_id, 'expiry_date', true );
		$date_format    = get_option( 'date_format' );
		
		$placeholders = array(
			'{name}'            => $user->display_name,
			'{username}'        => $user->user_login,
			'{site_name}'       => get_bloginfo( 'name' ),
			'{listing_id}'      => $post_id,
			'{listing_title}'   => get_the_title( $post_id ),
			'{expiration_date}' => ! empty( $never_expires ) ? __( 'Never Expires', 'advanced-classifieds-and-directory-pro' ) : date_i18n( $date_format, strtotime( $expiry_date ) )
		);
			
		$to = acadp_get_admin_email_id_s( $email_settings );
		
		$subject = __( '[{site_name}] Listing "{listing_title}" expired', 'advanced-classifieds-and-directory-pro' );
		$subject = strtr( $subject, $placeholders );
		
		$message = __( "Dear Administrator,<br /><br />This notification was for the listing on the website {site_name} \"{listing_title}\" and is expired.<br />This e-mail contains the listing details:<br /><br />Listing ID:{listing_id}<br />Listing Title:{listing_title}<br />Expired on:{expiration_date}<br /><br />Please do not respond to this message. It is automatically generated and is for information purposes only.", 'advanced-classifieds-and-directory-pro' );
		$message = strtr( $message, $placeholders );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify listing owner when his listing expired.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_listing_owner_listing_expired( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'listing_expired', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_listing_expired' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$never_expires  = get_post_meta( $post_id, 'never_expires', true );
		$expiry_date    = get_post_meta( $post_id, 'expiry_date', true );
		$categories     = wp_get_object_terms( $post_id, 'acadp_categories', array( 'fields' => 'names' ) );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'            => $user->display_name,
			'{username}'        => $user->user_login,
			'{site_name}'       => $site_name,
			'{site_link}'       => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'        => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{expiration_date}' => ! empty( $never_expires ) ? __( 'Never Expires', 'advanced-classifieds-and-directory-pro' ) : date_i18n( $date_format, strtotime( $expiry_date ) ),
			'{category_name}'   => ! empty( $categories ) ? $categories[0] : '',
			'{renewal_link}'    => acadp_get_listing_renewal_page_link( $post_id ),
			'{listing_title}'   => $listing_title,
			'{listing_link}'    => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'     => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{today}'           => date_i18n( $date_format, $current_time ),
			'{now}'             => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Send renewal reminder to the listing owner.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id    Post ID.
 */
function acadp_email_listing_owner_listing_renewal_reminder( $post_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'remind_renewal', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_renewal_reminder' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$never_expires  = get_post_meta( $post_id, 'never_expires', true );
		$expiry_date    = get_post_meta( $post_id, 'expiry_date', true );
		$categories     = wp_get_object_terms( $post_id, 'acadp_categories', array( 'fields' => 'names' ) );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'            => $user->display_name,
			'{username}'        => $user->user_login,
			'{site_name}'       => $site_name,
			'{site_link}'       => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'        => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{expiration_date}' => ! empty( $never_expires ) ? __( 'Never Expires', 'advanced-classifieds-and-directory-pro' ) : date_i18n( $date_format, strtotime( $expiry_date ) ),
			'{category_name}'   => ! empty( $categories ) ? $categories[0] : '',
			'{renewal_link}'    => acadp_get_listing_renewal_page_link( $post_id ),
			'{listing_title}'   => $listing_title,
			'{listing_link}'    => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'     => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{today}'           => date_i18n( $date_format, $current_time ),
			'{now}'             => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify admin when a new order placed.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id     Post ID.
 * @param    int    $order_id    Payment Order ID.
 */
function acadp_email_admin_order_created( $post_id, $order_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_admin'] ) && in_array( 'order_created', $email_settings['notify_admin'] ) ) {

		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => get_bloginfo( 'name' ),
			'{listing_id}'    => $post_id,
			'{listing_title}' => get_the_title( $post_id ),
			'{order_id}'      => $order_id,
			'{order_page}'    => admin_url( "edit.php?post_type=acadp_payments" ),
			'{order_details}' => acadp_email_get_order_details( $order_id )
		);
			
		$to = acadp_get_admin_email_id_s( $email_settings );
		
		$subject = __( '[{site_name}] A new order has been created on your website', 'advanced-classifieds-and-directory-pro' );
		$subject = strtr( $subject, $placeholders );
		
		$message = __( "Dear Administrator,<br /><br />The order is now created.<br /><br />This notification was for the order #{order_id} on the website {site_name}.<br />You can access the order details directly by clicking on the link below after logging in your back end:<br />{order_page}<br /><br />{order_details}<br /><br />Please do not respond to this message. It is automatically generated and is for information purposes only.", 'advanced-classifieds-and-directory-pro' );
		$message = strtr( $message, $placeholders );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify listing owner when his order placed.
 *
 * @since    1.0.0
 *
 * @param    int    $post_id     Post ID.
 * @param    int    $order_id    Payment Order ID.
 */
function acadp_email_listing_owner_order_created( $post_id, $order_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'order_created', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_order_created' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => $site_name,
			'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{listing_title}' => $listing_title,
			'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{order_id}'      => $order_id,
			'{order_page}'    => acadp_get_payment_receipt_page_link( $order_id ),
			'{today}'         => date_i18n( $date_format, $current_time ),
			'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		$message = str_replace( '{order_details}', acadp_email_get_order_details( $order_id ), $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify listing owner when his order placed(offline).
 *
 * @since    1.0.0
 *
 * @param    int    $post_id     Post ID.
 * @param    int    $order_id    Payment Order ID.
 */
function acadp_email_listing_owner_order_created_offline( $post_id, $order_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'order_created', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_order_created_offline' );
		
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => $site_name,
			'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{listing_title}' => $listing_title,
			'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{order_id}'      => $order_id,
			'{order_page}'    => acadp_get_payment_receipt_page_link( $order_id ),
			'{today}'         => date_i18n( $date_format, $current_time ),
			'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		$message = str_replace( '{order_details}', acadp_email_get_order_details( $order_id ), $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify admin when he received a payment.
 *
 * @since    1.0.0
 *
 * @param    int    $order_id    Payment Order ID.
 */
function acadp_email_admin_payment_received( $order_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_admin'] ) && in_array( 'payment_received', $email_settings['notify_admin'] ) ) {
	
		$post_id        = get_post_meta( $order_id, 'listing_id', true );
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => get_bloginfo( 'name' ),
			'{listing_id}'    => $post_id,
			'{listing_title}' => get_the_title( $post_id ),
			'{order_id}'      => $order_id,
			'{order_page}'    => admin_url( "edit.php?post_type=acadp_payments" ),
			'{order_details}' => acadp_email_get_order_details( $order_id )
		);
			
		$to = acadp_get_admin_email_id_s( $email_settings );
		
		$subject = __( '[{site_name}] Payment notification : payment Completed for order no.{order_id}', 'advanced-classifieds-and-directory-pro' );
		$subject = strtr( $subject, $placeholders );
		
		$message = __( "Dear Administrator,<br /><br />A Payment notification was received with the status Completed. The order is now confirmed.<br /><br />This notification was for the order #{order_id} on the website {site_name}.<br />You can access the order details directly by clicking on the link below after logging in your back end:<br />{order_page}<br /><br />{order_details}<br /><br />Please do not respond to this message. It is automatically generated and is for information purposes only.", 'advanced-classifieds-and-directory-pro' );
		$message = strtr( $message, $placeholders );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Notify listing owner when his order status changed "completed".
 *
 * @since    1.0.0
 *
 * @param    int    $order_id    Payment Order ID.
 */
function acadp_email_listing_owner_order_completed( $order_id ) {

	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_users'] ) && in_array( 'order_completed', $email_settings['notify_users'] ) ) {
	
		$email_template_settings = get_option( 'acadp_email_template_order_completed' );
		
		$post_id        = get_post_meta( $order_id, 'listing_id', true );
		$post_author_id = get_post_field( 'post_author', $post_id );
 		$user           = get_userdata( $post_author_id );
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
		
		$placeholders = array(
			'{name}'          => $user->display_name,
			'{username}'      => $user->user_login,
			'{site_name}'     => $site_name,
			'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{listing_title}' => $listing_title,
			'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{order_id}'      => $order_id,
			'{order_page}'    => acadp_get_payment_receipt_page_link( $order_id ),
			'{today}'         => date_i18n( $date_format, $current_time ),
			'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);
			
		$to = $user->user_email;		
		$subject = strtr( $email_template_settings['subject'], $placeholders );
		$message = strtr( $email_template_settings['body'], $placeholders );
		$message = nl2br( $message );
		$message = str_replace( '{order_details}', acadp_email_get_order_details( $order_id ), $message );
		
		$headers = acadp_get_email_headers( $email_settings );
	
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Send contact message to the admin.
 *
 * @since    1.0.0
 */
function acadp_email_admin_listing_contact() {
	
	$email_settings = get_option( 'acadp_email_settings' );	
	
	if( isset( $email_settings['notify_admin'] ) && in_array( 'listing_contact', $email_settings['notify_admin'] ) ) {
	
		// sanitize form values
		$post_id = (int) $_POST["post_id"];
		$name    = sanitize_text_field( $_POST["name"] );
		$email   = sanitize_email( $_POST["email"] );
		$message = esc_textarea( $_POST["message"] );
	
		// vars
		$site_name      = get_bloginfo( 'name' );
		$site_url       = get_bloginfo( 'url' );
		$listing_title  = get_the_title( $post_id );
		$listing_url    = get_permalink( $post_id );
		$date_format    = get_option( 'date_format' );
		$time_format    = get_option( 'time_format' );
		$current_time   = current_time( 'timestamp' );
	
		$placeholders = array(
			'{site_name}'     => $site_name,
			'{site_link}'     => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
			'{site_url}'      => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
			'{listing_title}' => $listing_title,
			'{listing_link}'  => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
			'{listing_url}'   => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
			'{sender_name}'   => $name,
			'{sender_email}'  => $email,
			'{message}'       => $message,
			'{today}'         => date_i18n( $date_format, $current_time ),
			'{now}'           => date_i18n( $date_format . ' ' . $time_format, $current_time )
		);	
		
		$to = acadp_get_admin_email_id_s( $email_settings );
		
		$subject = __( '[{site_name}] Contact via "{listing_title}"', 'advanced-classifieds-and-directory-pro' );
		$subject = strtr( $subject, $placeholders );
	
		$message =  __( "Dear Administrator,<br /><br />A listing on your website {site_name} received a message.<br /><br />Listing URL: {listing_url}<br /><br />Name: {sender_name}<br />Email: {sender_email}<br />Message: {message}<br />Time: {now}<br /><br />This is just a copy of the original email and was already sent to the listing owner. You don't have to reply this unless necessary.", 'advanced-classifieds-and-directory-pro' );
		$message = strtr( $message, $placeholders );
	
		$headers  = "From: {$name} <{$email}>\r\n";
		$headers .= "Reply-To: {$email}\r\n";
		
		acadp_send_mail( $to, $subject, $message, $headers );
	
	}
	
}

/**
 * Send contact message to the listing owner.
 *
 * @since    1.0.0
 *
 * @return   string    $result    Message based on the result.
 */
function acadp_email_listing_owner_listing_contact() {

	$email_template_settings = get_option( 'acadp_email_template_listing_contact' );

	// sanitize form values
	$post_id = (int) $_POST["post_id"];
	$name    = sanitize_text_field( $_POST["name"] );
	$email   = sanitize_email( $_POST["email"] );
	$message = stripslashes( esc_textarea( $_POST["message"] ) );
		
	// vars
	$post_author_id = get_post_field( 'post_author', $post_id );
	$user           = get_userdata( $post_author_id );
	$site_name      = get_bloginfo( 'name' );
	$site_url       = get_bloginfo( 'url' );
	$listing_title  = get_the_title( $post_id );
	$listing_url    = get_permalink( $post_id );
	$date_format    = get_option( 'date_format' );
	$time_format    = get_option( 'time_format' );
	$current_time   = current_time( 'timestamp' );
	
	$placeholders = array(
		'{name}'            => $user->display_name,
		'{username}'        => $user->user_login,
		'{site_name}'       => $site_name,
		'{site_link}'       => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
		'{site_url}'        => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
		'{listing_title}'   => $listing_title,
		'{listing_link}'    => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
		'{listing_url}'     => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
		'{sender_name}'     => $name,
		'{sender_email}'    => $email,
		'{message}'         => $message,
		'{today}'           => date_i18n( $date_format, $current_time ),
		'{now}'             => date_i18n( $date_format . ' ' . $time_format, $current_time )
	);	
		
	$to      = $user->user_email;
	
	$subject = strtr( $email_template_settings['subject'], $placeholders );
	
	$message = strtr( $email_template_settings['body'], $placeholders );
	$message = nl2br( $message );
	
	$headers  = "From: {$name} <{$email}>\r\n";
	$headers .= "Reply-To: {$email}\r\n";
		
	// return true or false, based on the result
	return acadp_send_mail( $to, $subject, $message, $headers ) ? true : false;
		
}

/**
 * Send report about a listing to the admin.
 *
 * @since    1.0.0
 *
 * @return   bool    $result    True if mail was sent, false if not.
 */
function acadp_email_admin_report_abuse() {

	// sanitize form values
	$post_id = (int) $_POST["post_id"];
	$message = esc_textarea( $_POST["message"] );
		
	// vars
	$user          = wp_get_current_user();
	$site_name     = get_bloginfo( 'name' );
	$site_url      = get_bloginfo( 'url' );
	$listing_title = get_the_title( $post_id );
	$listing_url   = get_permalink( $post_id );
	$date_format   = get_option( 'date_format' );
	$time_format   = get_option( 'time_format' );
	$current_time  = current_time( 'timestamp' );
	
	$placeholders = array(
		'{site_name}'       => $site_name,
		'{site_link}'       => sprintf( '<a href="%s">%s</a>', $site_url, $site_name ),
		'{site_url}'        => sprintf( '<a href="%s">%s</a>', $site_url, $site_url ),
		'{listing_title}'   => $listing_title,
		'{listing_link}'    => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_title ),
		'{listing_url}'     => sprintf( '<a href="%s">%s</a>', $listing_url, $listing_url ),
		'{sender_name}'     => $user->display_name,
		'{sender_email}'    => $user->user_email,
		'{message}'         => $message,
		'{today}'           => date_i18n( $date_format, $current_time ),
		'{now}'             => date_i18n( $date_format . ' ' . $time_format, $current_time )
	);	
	
	$to = acadp_get_admin_email_id_s();	
	
	$subject = __( '[{site_name}] Report Abuse via "{listing_title}"', 'advanced-classifieds-and-directory-pro' );
	$subject = strtr( $subject, $placeholders );
	
	$message =  __( "Dear Administrator,<br /><br />This is an email abuse report for a listing at {listing_url}.<br /><br />Name: {sender_name}<br />Email: {sender_email}<br />Message: {message}<br />Time: {now}", 'advanced-classifieds-and-directory-pro' );
	$message = strtr( $message, $placeholders );
	
	$headers  = "From: {$user->display_name} <{$user->user_email}>\r\n";
	$headers .= "Reply-To: {$user->user_email}\r\n";
		
	// return true or false, based on the result
	return acadp_send_mail( $to, $subject, $message, $headers ) ? true : false;

}

/**
 * Get Order details to attach in email.
 *
 * @since    1.0.0
 *
 * @param    int       $order_id    Payment Order ID.
 * @return   string    $html        Order details.
 */
function acadp_email_get_order_details( $order_id ) {

	$order_details = apply_filters( 'acadp_order_details', array(), $order_id );
	
	$featured = get_post_meta( $order_id, 'featured', true );
	if( $featured ) {
		$featured_listing_settings = get_option( 'acadp_featured_listing_settings' );
		$order_details[] = $featured_listing_settings;
	}
	
	$currency = acadp_get_payment_currency();

	ob_start();
	?>
	<table border="0" cellspacing="0" cellpadding="7" style="border:1px solid #CCC;">
        <tr style="background-color:#F0F0F0;">
       		<th style="border-right:1px solid #CCC; border-bottom:1px solid #CCC; text-align:left;"><?php _e( 'Item(s)', 'advanced-classifieds-and-directory-pro' ); ?></th>
        	<th style="border-bottom:1px solid #CCC;"><?php printf( __( 'Price [%s]', 'advanced-classifieds-and-directory-pro' ), $currency ); ?></th>
       	</tr>
        <?php foreach( $order_details as $order_detail ) : ?>
       		<tr>
       			<td style="border-right:1px solid #CCC; border-bottom:1px solid #CCC;">
					<h3><?php echo $order_detail['label']; ?></h3>
           			<?php if( isset( $order_detail['description'] ) ) echo $order_detail['description']; ?>
        		</td>
       			<td style="border-bottom:1px solid #CCC;">
					<?php echo acadp_format_payment_amount( $order_detail['price'] ); ?>
                </td>
       		</tr>
        <?php endforeach; ?>
       	<tr>
       		<td style="border-right:1px solid #CCC; text-align:right; vertical-align:middle;">
				<?php printf( __( 'Total amount [%s]', 'advanced-classifieds-and-directory-pro' ), $currency ); ?>
            </td>
        	<td>
				<?php
                	$amount = get_post_meta( $order_id, 'amount', true );
					echo acadp_format_payment_amount( $amount );
				?>
            </td>
       	</tr>
    </table>
	<?php 
	return ob_get_clean();
	
}
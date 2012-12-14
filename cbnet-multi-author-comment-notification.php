<?php
/*
 * Plugin Name:   cbnet Multi Author Comment Notification
 * Plugin URI:    http://www.chipbennett.net/wordpress/plugins/cbnet-multi-author-comment-notification/
 * Description:   Send comment notification emails to multiple users. Select users individually or by user role, or send emails to arbitrary email addresses.
 * Version:       2.0.2
 * Author:        chipbennett
 * Author URI:    http://www.chipbennett.net/
 *
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * Version 2.0 and later of this Plugin: Copyright (C) 2012 Chip Bennett,
 * Released under the GNU General Public License, version 2.0 (or newer)
 * 
 * Previous versions of this program were modified from MaxBlogPress Multi Author Comment Notification plugin, version 1.0.5, 
 * Copyright (C) 2007 www.maxblogpress.com, released under the GNU General Public License.
 */

 
/**
 * Bootstrap Plugin settings
 */
include( plugin_dir_path( __FILE__ ) . 'settings.php' );

/**
 * Globalize Plugin options
 */
global $cbnet_rscc_options;
$cbnet_macn_options = cbnet_macn_get_options();
 
/**
 * Bootstrap Plugin custom user meta
 */
include( plugin_dir_path( __FILE__ ) . 'custom-user-meta.php' );
 
/**
 * Bootstrap Plugin pluggable function overrides
 */
include( plugin_dir_path( __FILE__ ) . 'pluggable.php' );


/**
 * Build array of notification email addresses
 */
function cbnet_macn_get_notification_email_addresses() {
	global $cbnet_macn_options;
	
	// Instantiate array
	$email_addresses = array();
	
	// Add email addresses from user meta
	$user_email_addresses = array();
	$users_email_notify = get_users( array( 'meta_key' => 'cbnet_macn_comment_notify', 'meta_value' => true ) );
	if ( ! empty( $users_email_notify ) ) {
		foreach ( $users_email_notify as $user ) {
			$user_email_addresses[] = $user->user_email;
		}
		$email_addresses = array_merge( $email_addresses, $user_email_addresses );
	}
	
	// Add email addresses for User Roles
	$roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
	$role_email_addresses = array();
	foreach ( $roles as $role ) {
		if ( true == $cbnet_macn_options['all_' . $role . 's'] ) {
			$role_email_notify = get_users( array( 'role' => $role ) );
			if ( ! empty( $role_email_notify ) ) {
				foreach ( $role_email_notify as $user ) {
					$role_email_addresses[] = $user->user_email;
				}
			}
		}
	}
	if ( ! empty( $role_email_addresses ) ) {
		$email_addresses = array_merge( $email_addresses, $role_email_addresses );
	}
	
	// Add additional email addresses
	if ( false != $cbnet_macn_options['additional_emails'] ) {
		$email_addresses = array_merge( $email_addresses, $cbnet_macn_options['additional_emails'] );
	}
	
	// Return array
	return apply_filters( 'cbnet_macn_notify_email_addresses', $email_addresses );
}

?>
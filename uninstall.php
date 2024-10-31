<?php
/**
 * Uninstall
 *
 * @package Post Date Time Change
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;
$option_names = array();
$wp_options = $wpdb->get_results(
	"
				SELECT option_name
				FROM $wpdb->options
				WHERE option_name LIKE '%%postdatetimechange_%%'
				"
);
foreach ( $wp_options as $wp_option ) {
	$option_names[] = $wp_option->option_name;
}

/* For Single site */
if ( ! is_multisite() ) {
	delete_option( 'postdatetimechange' );
	$blogusers = get_users( array( 'fields' => array( 'ID' ) ) );
	foreach ( $blogusers as $user ) {
		delete_user_option( $user->ID, 'postdatetimechange', false );
	}
	foreach ( $option_names as $option_name ) {
		delete_option( $option_name );
	}
} else {
	/* For Multisite */
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ( $blog_ids as $blogid ) {
		switch_to_blog( $blogid );
		delete_option( 'postdatetimechange' );
		$blogusers = get_users(
			array(
				'blog_id' => $blogid,
				'fields' => array( 'ID' ),
			)
		);
		foreach ( $blogusers as $user ) {
			delete_user_option( $user->ID, 'postdatetimechange', false );
		}
		foreach ( $option_names as $option_name ) {
			delete_option( $option_name );
		}
	}
	switch_to_blog( $original_blog_id );

	/* For site options. */
	delete_site_option( 'postdatetimechange' );
	foreach ( $option_names as $option_name ) {
		delete_site_option( $option_name );
	}
}



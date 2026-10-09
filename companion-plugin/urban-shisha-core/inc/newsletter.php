<?php
/**
 * Local newsletter subscription management.
 *
 * Stores subscriber emails locally in WordPress admin.
 * No emails are sent and no external services are contacted.
 *
 * @package UrbanShishaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register private Custom Post Type for storing newsletter subscribers in WordPress admin.
 */
function urban_shisha_register_subscriber_cpt() {
	$labels = array(
		'name'               => __( 'Newsletter Subscribers', 'urban-shisha-core' ),
		'singular_name'      => __( 'Subscriber', 'urban-shisha-core' ),
		'menu_name'          => __( 'Subscribers', 'urban-shisha-core' ),
		'name_admin_bar'     => __( 'Subscriber', 'urban-shisha-core' ),
		'all_items'          => __( 'All Subscribers', 'urban-shisha-core' ),
		'search_items'       => __( 'Search Subscribers', 'urban-shisha-core' ),
		'not_found'          => __( 'No subscribers found.', 'urban-shisha-core' ),
		'not_found_in_trash' => __( 'No subscribers found in Trash.', 'urban-shisha-core' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => 26,
		'menu_icon'           => 'dashicons-email',
		'supports'            => array( 'title' ),
		'capabilities'        => array(
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap'        => true,
	);

	register_post_type( 'us_subscriber', $args );
}
add_action( 'init', 'urban_shisha_register_subscriber_cpt' );

/**
 * Custom admin list columns for subscribers.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function urban_shisha_subscriber_admin_columns( $columns ) {
	return array(
		'cb'    => '<input type="checkbox" />',
		'title' => __( 'Email address', 'urban-shisha-core' ),
		'date'  => __( 'Subscribed date', 'urban-shisha-core' ),
	);
}
add_filter( 'manage_us_subscriber_posts_columns', 'urban_shisha_subscriber_admin_columns' );

/**
 * Handle AJAX newsletter subscription requests.
 */
function urban_shisha_ajax_subscribe_newsletter() {
	check_ajax_referer( 'urban_shisha_newsletter_nonce', 'nonce' );

	$raw_email = isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
	$email     = sanitize_email( trim( (string) $raw_email ) );

	if ( ! $email || ! is_email( $email ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'Please enter a valid email address.', 'urban-shisha-core' ),
			),
			400
		);
	}

	// Rate limiting: 5 requests per 5 minutes per client hash.
	$remote_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
	$rate_key  = 'us_nl_rate_' . md5( $remote_ip );
	$attempts  = (int) get_transient( $rate_key );

	if ( $attempts >= 5 ) {
		wp_send_json_error(
			array(
				'message' => __( 'Too many requests. Please wait a few moments and try again.', 'urban-shisha-core' ),
			),
			429
		);
	}
	set_transient( $rate_key, $attempts + 1, 300 );

	// Deduplication: check if subscriber already exists.
	global $wpdb;
	$existing_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'us_subscriber' AND post_title = %s AND post_status != 'trash' LIMIT 1",
			$email
		)
	);

	if ( $existing_id ) {
		wp_send_json_success(
			array(
				'status'  => 'existing',
				'message' => __( 'You are already subscribed to Urban Shisha updates.', 'urban-shisha-core' ),
			)
		);
	}

	// Insert subscriber post.
	$post_id = wp_insert_post(
		array(
			'post_title'  => $email,
			'post_type'   => 'us_subscriber',
			'post_status' => 'publish',
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_send_json_error(
			array(
				'message' => __( 'Could not save your subscription. Please try again later.', 'urban-shisha-core' ),
			),
			500
		);
	}

	update_post_meta( $post_id, '_subscriber_email', $email );
	update_post_meta( $post_id, '_subscriber_date', current_time( 'mysql' ) );

	wp_send_json_success(
		array(
			'status'  => 'subscribed',
			'message' => __( 'Thank you for subscribing! Your email has been saved.', 'urban-shisha-core' ),
		)
	);
}
add_action( 'wp_ajax_urban_shisha_newsletter', 'urban_shisha_ajax_subscribe_newsletter' );
add_action( 'wp_ajax_nopriv_urban_shisha_newsletter', 'urban_shisha_ajax_subscribe_newsletter' );

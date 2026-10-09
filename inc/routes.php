<?php
/** Canonical URLs for shared navigation; no database writes or page creation. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_route_url( $route, $query = array() ) {
	$slugs = array(
		'home' => '', 'shop' => 'shop', 'product' => 'product', 'cart' => 'cart',
		'checkout' => 'checkout', 'account' => 'my-account', 'about' => 'about',
		'contact' => 'contact', 'wholesale' => 'bulk-orders', 'shipping' => 'shipping',
		'returns' => 'returns', 'privacy' => 'privacy-policy',
		'terms' => 'terms-and-conditions', 'age-policy' => 'age-policy',
	);
	$url = home_url( '/' . ( empty( $slugs[ $route ] ) ? '' : $slugs[ $route ] . '/' ) );
	$wc_route = 'account' === $route ? 'myaccount' : $route;
	if ( in_array( $wc_route, array( 'shop', 'cart', 'checkout', 'myaccount' ), true ) && function_exists( 'wc_get_page_id' ) ) {
		$page_id = wc_get_page_id( $wc_route );
		if ( $page_id > 0 && get_post( $page_id ) ) {
			$url = get_permalink( $page_id );
		}
	}
	if ( 'privacy' === $route && get_privacy_policy_url() ) {
		$url = get_privacy_policy_url();
	}
	if ( 'account' === $route && isset( $query['tab'] ) ) {
		$endpoints = array( 'orders' => 'orders', 'addresses' => 'edit-address', 'details' => 'edit-account', 'wishlist' => 'wishlist' );
		$tab = $query['tab'];
		if ( isset( $endpoints[ $tab ] ) && function_exists( 'wc_get_endpoint_url' ) ) {
			$url = wc_get_endpoint_url( $endpoints[ $tab ], '', $url );
			unset( $query['tab'] );
		}
	}
	return $query ? add_query_arg( $query, $url ) : $url;
}

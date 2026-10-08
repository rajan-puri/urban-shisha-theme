<?php
/** Local design assets, enqueued through WordPress with explicit dependencies. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_asset_version( $file ) {
	$path = get_theme_file_path( $file );
	return is_file( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );
}

function urban_shisha_enqueue_assets() {
	wp_enqueue_style( 'urban-shisha-design', get_theme_file_uri( 'assets/site.css' ), array(), urban_shisha_asset_version( 'assets/site.css' ) );
	wp_enqueue_style( 'urban-shisha-theme', get_stylesheet_uri(), array( 'urban-shisha-design' ), urban_shisha_asset_version( 'style.css' ) );

	$footer_defer = array( 'in_footer' => true, 'strategy' => 'defer' );
	wp_register_script( 'urban-shisha-gsap', get_theme_file_uri( 'assets/js/gsap.min.js' ), array(), urban_shisha_asset_version( 'assets/js/gsap.min.js' ), $footer_defer );
	wp_register_script( 'urban-shisha-scroll-trigger', get_theme_file_uri( 'assets/js/ScrollTrigger.min.js' ), array( 'urban-shisha-gsap' ), urban_shisha_asset_version( 'assets/js/ScrollTrigger.min.js' ), $footer_defer );
	wp_register_script( 'urban-shisha-catalog', get_theme_file_uri( 'assets/js/catalog.js' ), array(), urban_shisha_asset_version( 'assets/js/catalog.js' ), $footer_defer );
	wp_register_script( 'urban-shisha-store-config', get_theme_file_uri( 'assets/js/config.js' ), array(), urban_shisha_asset_version( 'assets/js/config.js' ), $footer_defer );
	wp_enqueue_script( 'urban-shisha-shell', get_theme_file_uri( 'assets/js/theme-shell.js' ), array(), urban_shisha_asset_version( 'assets/js/theme-shell.js' ), $footer_defer );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
	wp_enqueue_script( 'urban-shisha-navigation', get_theme_file_uri( 'assets/js/navigation.js' ), array( 'urban-shisha-shell' ), urban_shisha_asset_version( 'assets/js/navigation.js' ), $footer_defer );
	wp_enqueue_script( 'urban-shisha-gsap' );

	$context = array(
		'assetBase' => trailingslashit( get_theme_file_uri( 'assets' ) ),
		'shopUrl' => urban_shisha_route_url( 'shop' ),
		'contactUrl' => urban_shisha_route_url( 'contact' ),
		'requireAgeConfirmation' => ! is_page( array( 'shipping', 'returns', 'privacy-policy', 'terms-and-conditions', 'age-policy' ) ),
	);
	wp_add_inline_script( 'urban-shisha-shell', 'window.UrbanShishaTheme = ' . wp_json_encode( $context, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'urban_shisha_enqueue_assets' );

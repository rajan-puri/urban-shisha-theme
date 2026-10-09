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

	wp_enqueue_style( 'urban-shisha-commerce-drawers', get_theme_file_uri( 'assets/commerce-drawers.css' ), array( 'urban-shisha-design', 'urban-shisha-theme' ), urban_shisha_asset_version( 'assets/commerce-drawers.css' ) );
	wp_enqueue_style( 'urban-shisha-footer-dynamic', get_theme_file_uri( 'assets/footer-dynamic.css' ), array( 'urban-shisha-design', 'urban-shisha-theme' ), urban_shisha_asset_version( 'assets/footer-dynamic.css' ) );

	$footer_defer = array( 'in_footer' => true, 'strategy' => 'defer' );
	wp_register_script( 'urban-shisha-gsap', get_theme_file_uri( 'assets/js/gsap.min.js' ), array(), urban_shisha_asset_version( 'assets/js/gsap.min.js' ), $footer_defer );
	wp_register_script( 'urban-shisha-scroll-trigger', get_theme_file_uri( 'assets/js/ScrollTrigger.min.js' ), array( 'urban-shisha-gsap' ), urban_shisha_asset_version( 'assets/js/ScrollTrigger.min.js' ), $footer_defer );
	wp_register_script( 'urban-shisha-catalog', get_theme_file_uri( 'assets/js/catalog.js' ), array(), urban_shisha_asset_version( 'assets/js/catalog.js' ), $footer_defer );
	wp_register_script( 'urban-shisha-store-config', get_theme_file_uri( 'assets/js/config.js' ), array(), urban_shisha_asset_version( 'assets/js/config.js' ), $footer_defer );
	wp_enqueue_script( 'urban-shisha-shell', get_theme_file_uri( 'assets/js/theme-shell.js' ), array(), urban_shisha_asset_version( 'assets/js/theme-shell.js' ), $footer_defer );
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
	wp_enqueue_script( 'urban-shisha-commerce-drawers', get_theme_file_uri( 'assets/js/commerce-drawers.js' ), array( 'urban-shisha-shell' ), urban_shisha_asset_version( 'assets/js/commerce-drawers.js' ), $footer_defer );
	wp_enqueue_script( 'urban-shisha-navigation', get_theme_file_uri( 'assets/js/navigation.js' ), array( 'urban-shisha-shell' ), urban_shisha_asset_version( 'assets/js/navigation.js' ), $footer_defer );
	wp_enqueue_script( 'urban-shisha-gsap' );

	$drawers_config = array(
		'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
		'wishlistNonce'   => wp_create_nonce( 'urban_shisha_wishlist_nonce' ),
		'cartNonce'       => wp_create_nonce( 'urban_shisha_cart_nonce' ),
		'isLoggedIn'      => is_user_logged_in(),
		'initialWishlist' => ( is_user_logged_in() && function_exists( 'urban_shisha_get_user_wishlist' ) ) ? urban_shisha_get_user_wishlist() : array(),
		'cartUrl'         => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : urban_shisha_route_url( 'cart' ),
		'checkoutUrl'     => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : urban_shisha_route_url( 'checkout' ),
		'shopUrl'         => urban_shisha_route_url( 'shop' ),
	);
	wp_add_inline_script( 'urban-shisha-commerce-drawers', 'window.UrbanCommerceDrawers = ' . wp_json_encode( $drawers_config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );

	$context = array(
		'assetBase'              => trailingslashit( get_theme_file_uri( 'assets' ) ),
		'ajaxUrl'                => admin_url( 'admin-ajax.php' ),
		'newsletterNonce'        => wp_create_nonce( 'urban_shisha_newsletter_nonce' ),
		'shopUrl'                => urban_shisha_route_url( 'shop' ),
		'contactUrl'             => urban_shisha_route_url( 'contact' ),
		'requireAgeConfirmation' => ! is_page( array( 'shipping', 'returns', 'privacy-policy', 'terms-and-conditions', 'age-policy' ) ),
	);
	wp_add_inline_script( 'urban-shisha-shell', 'window.UrbanShishaTheme = ' . wp_json_encode( $context, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );

	if ( is_front_page() || is_home() ) {
		wp_enqueue_style( 'urban-shisha-home-integration', get_theme_file_uri( 'assets/home-wordpress.css' ), array( 'urban-shisha-design', 'urban-shisha-theme' ), urban_shisha_asset_version( 'assets/home-wordpress.css' ) );

        wp_enqueue_style( 'urban-shisha-builder-picker', get_theme_file_uri( 'assets/builder-picker.css' ), array( 'urban-shisha-home-integration' ), urban_shisha_asset_version( 'assets/builder-picker.css' ) );
        wp_enqueue_script( 'urban-shisha-builder-picker', get_theme_file_uri( 'assets/js/builder-picker.js' ), array( 'urban-shisha-home' ), urban_shisha_asset_version( 'assets/js/builder-picker.js' ), $footer_defer );

		wp_enqueue_script( 'urban-shisha-scroll-trigger' );
		wp_enqueue_script( 'urban-shisha-next-motion', get_theme_file_uri( 'assets/js/next-motion.js' ), array( 'urban-shisha-gsap', 'urban-shisha-scroll-trigger' ), urban_shisha_asset_version( 'assets/js/next-motion.js' ), $footer_defer );
		wp_enqueue_script( 'urban-shisha-home', get_theme_file_uri( 'assets/js/home.js' ), array( 'urban-shisha-shell', 'urban-shisha-commerce-drawers', 'urban-shisha-next-motion' ), urban_shisha_asset_version( 'assets/js/home.js' ), $footer_defer );

		$hookahs     = function_exists( 'urban_shisha_query_products' ) ? urban_shisha_query_products( array( 'limit' => 12, 'category' => 'hookahs' ) ) : array();
		$accessories = function_exists( 'urban_shisha_query_products' ) ? urban_shisha_query_products( array( 'limit' => 12, 'category' => array( 'bowls', 'heat-management', 'hoses-mouthpieces', 'tools-spares', 'charcoal', 'accessories' ) ) ) : array();

		$home_config = array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'homeNonce'   => wp_create_nonce( 'urban_shisha_home_nonce' ),
			'cartNonce'   => wp_create_nonce( 'urban_shisha_cart_nonce' ),
			'hookahs'     => $hookahs,
			'accessories' => $accessories,
			'isEditor'    => current_user_can( 'edit_products' ),
            'budgetRanges' => urban_shisha_home_budget_ranges(),
		);
		wp_add_inline_script( 'urban-shisha-home', 'window.UrbanHomeConfig = ' . wp_json_encode( $home_config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}
}
add_action( 'wp_enqueue_scripts', 'urban_shisha_enqueue_assets' );

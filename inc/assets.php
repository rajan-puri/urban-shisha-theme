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

	wp_enqueue_style( 'urban-shisha-commerce-integration', get_theme_file_uri( 'assets/commerce-wordpress.css' ), array( 'urban-shisha-commerce-drawers', 'urban-shisha-theme' ), urban_shisha_asset_version( 'assets/commerce-wordpress.css' ) );

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

	if ( ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) {
		wp_enqueue_script( 'urban-shisha-shop', get_theme_file_uri( 'assets/js/shop-wordpress.js' ), array( 'urban-shisha-shell', 'urban-shisha-commerce-drawers' ), urban_shisha_asset_version( 'assets/js/shop-wordpress.js' ), $footer_defer );

		$shop_config = function_exists( 'urban_shisha_get_shop_client_config' ) ? urban_shisha_get_shop_client_config() : array();
		wp_add_inline_script( 'urban-shisha-shop', 'window.UrbanShopConfig = ' . wp_json_encode( $shop_config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_style( 'urban-shisha-product-integration', get_theme_file_uri( 'assets/product-wordpress.css' ), array( 'urban-shisha-design', 'urban-shisha-theme' ), urban_shisha_asset_version( 'assets/product-wordpress.css' ) );
		wp_enqueue_script( 'urban-shisha-product', get_theme_file_uri( 'assets/js/product-wordpress.js' ), array( 'urban-shisha-commerce-drawers', 'urban-shisha-gsap', 'wc-add-to-cart-variation' ), urban_shisha_asset_version( 'assets/js/product-wordpress.js' ), $footer_defer );
		wp_add_inline_script( 'urban-shisha-product', 'window.UrbanProductConfig = ' . wp_json_encode( urban_shisha_get_single_product_client_config(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}

	if ( is_page( 'about' ) || is_page( array( 'shipping', 'returns', 'privacy-policy', 'terms-and-conditions', 'age-policy' ) ) ) {
		wp_enqueue_script( 'urban-shisha-pages', get_theme_file_uri( 'assets/js/pages.js' ), array( 'urban-shisha-shell', 'urban-shisha-gsap' ), urban_shisha_asset_version( 'assets/js/pages.js' ), $footer_defer );
	}

	if ( is_page( 'contact' ) ) {
		wp_enqueue_script( 'urban-shisha-contact', get_theme_file_uri( 'assets/js/enquiry-wordpress.js' ), array( 'urban-shisha-shell', 'urban-shisha-gsap' ), urban_shisha_asset_version( 'assets/js/enquiry-wordpress.js' ), $footer_defer );
	}

	if ( is_page( array( 'contact', 'bulk-orders' ) ) ) {
		wp_enqueue_style( 'urban-shisha-enquiry', get_theme_file_uri( 'assets/enquiry-wordpress.css' ), array( 'urban-shisha-commerce-integration' ), urban_shisha_asset_version( 'assets/enquiry-wordpress.css' ) );
	}
	if ( is_page( 'bulk-orders' ) ) {
		wp_enqueue_script( 'urban-shisha-wholesale', get_theme_file_uri( 'assets/js/wholesale-wordpress.js' ), array( 'urban-shisha-shell', 'urban-shisha-gsap' ), urban_shisha_asset_version( 'assets/js/wholesale-wordpress.js' ), $footer_defer );
		$products = function_exists( 'urban_shisha_query_products' ) ? urban_shisha_query_products( array( 'limit' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) : array();
		$products = array_values( array_filter( $products, static function( $product ) { return 'publish' === get_post_status( $product['id'] ) && in_array( wc_get_product( $product['id'] )->get_catalog_visibility(), array( 'visible', 'catalog' ), true ); } ) );
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true ) );
		$categories = is_wp_error( $terms ) ? array() : array_map( static function( $term ) { return array( 'slug' => $term->slug, 'name' => $term->name ); }, $terms );
		wp_add_inline_script( 'urban-shisha-wholesale', 'window.UrbanWholesaleConfig = ' . wp_json_encode( array( 'products' => $products, 'categories' => $categories ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}

	if ( function_exists( 'is_cart' ) && is_cart() ) {
		wp_enqueue_script( 'urban-shisha-cart', get_theme_file_uri( 'assets/js/cart-wordpress.js' ), array( 'urban-shisha-shell', 'urban-shisha-commerce-drawers' ), urban_shisha_asset_version( 'assets/js/cart-wordpress.js' ), $footer_defer );
		$cart_cfg = array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'cartNonce' => wp_create_nonce( 'urban_shisha_cart_nonce' ),
			'shopUrl'   => urban_shisha_route_url( 'shop' ),
		);
		wp_add_inline_script( 'urban-shisha-cart', 'window.UrbanCartConfig = ' . wp_json_encode( $cart_cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		wp_enqueue_script( 'urban-shisha-checkout', get_theme_file_uri( 'assets/js/checkout-wordpress.js' ), array( 'urban-shisha-shell', 'urban-shisha-commerce-drawers' ), urban_shisha_asset_version( 'assets/js/checkout-wordpress.js' ), $footer_defer );
		$checkout_cfg = array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'cartNonce' => wp_create_nonce( 'urban_shisha_cart_nonce' ),
		);
		wp_add_inline_script( 'urban-shisha-checkout', 'window.UrbanCheckoutConfig = ' . wp_json_encode( $checkout_cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_script( 'urban-shisha-account', get_theme_file_uri( 'assets/js/account-wordpress.js' ), array( 'urban-shisha-shell', 'urban-shisha-commerce-drawers' ), urban_shisha_asset_version( 'assets/js/account-wordpress.js' ), $footer_defer );
		$account_cfg = array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'wishlistNonce' => wp_create_nonce( 'urban_shisha_wishlist_nonce' ),
			'cartNonce'     => wp_create_nonce( 'urban_shisha_cart_nonce' ),
		);
		wp_add_inline_script( 'urban-shisha-account', 'window.UrbanAccountConfig = ' . wp_json_encode( $account_cfg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	}
}
add_action( 'wp_enqueue_scripts', 'urban_shisha_enqueue_assets' );

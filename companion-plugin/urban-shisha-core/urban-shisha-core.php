<?php
/**
 * Plugin Name: Urban Shisha Core
 * Description: Version-controlled editable content schemas for Urban Shisha. Commerce remains owned by WooCommerce.
 * Version: 0.2.0
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Requires Plugins: secure-custom-fields, woocommerce
 * Author: Mates Creation
 * Text Domain: urban-shisha-core
 */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/inc/cms.php';
require_once __DIR__ . '/inc/product-admin.php';
require_once __DIR__ . '/inc/wishlist.php';
require_once __DIR__ . '/inc/newsletter.php';
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once __DIR__ . '/inc/catalog-import.php';
}

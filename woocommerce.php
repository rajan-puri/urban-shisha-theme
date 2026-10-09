<?php
/**
 * WooCommerce root fallback wrapper.
 *
 * Dispatches directly to exact approved archive or single templates
 * rather than generic woocommerce_content() that bypasses the design.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

if ( is_singular( 'product' ) ) {
	wc_get_template( 'single-product.php' );
} else {
	wc_get_template( 'archive-product.php' );
}

<?php
/** Actual WooCommerce product presentation and purchase endpoint. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_product_field( string $name, int $id, $fallback = '' ) {
	$value = function_exists( 'get_field' ) ? get_field( $name, $id ) : get_post_meta( $id, $name, true );
	return false === $value || null === $value || '' === $value ? $fallback : $value;
}

function urban_shisha_get_single_product_context( $product_or_id = null ): ?array {
	$product = $product_or_id instanceof WC_Product ? $product_or_id : wc_get_product( $product_or_id ?: get_queried_object_id() );
	if ( ! $product ) { return null; }
	$data = urban_shisha_get_product_data( $product );
	if ( ! $data ) { return null; }
	$id = $product->get_id();
	$gallery = array();
	if ( ! empty( $data['cutout_url'] ) ) {
		$gallery[] = array( 'src' => $data['cutout_url'], 'type' => 'studio', 'name' => $data['name'] );
	}
	foreach ( array_unique( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) as $image_id ) {
		$src = wp_get_attachment_image_url( $image_id, 'full' );
		if ( $src && ! in_array( $src, array_column( $gallery, 'src' ), true ) ) {
			$gallery[] = array( 'src' => $src, 'type' => 'photo', 'name' => $data['name'] );
		}
	}
	if ( ! $gallery ) { $gallery[] = array( 'src' => wc_placeholder_img_src(), 'type' => 'photo', 'name' => $data['name'] ); }
	$specs = (array) urban_shisha_product_field( 'us_product_specifications', $id, array() );
	foreach ( $product->get_attributes() as $attribute ) {
		$specs[] = array( 'spec_label' => wc_attribute_label( $attribute->get_name() ), 'spec_value' => $product->get_attribute( $attribute->get_name() ) );
	}
	if ( $product->get_weight() ) { $specs[] = array( 'spec_label' => 'Weight', 'spec_value' => wc_format_weight( $product->get_weight() ) ); }
	if ( $product->has_dimensions() ) { $specs[] = array( 'spec_label' => 'Dimensions', 'spec_value' => wc_format_dimensions( $product->get_dimensions( false ) ) ); }
	$categories = wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'slugs' ) );
	$related = urban_shisha_query_products( array( 'limit' => 4, 'category' => is_wp_error( $categories ) ? array() : $categories ) );
	$related = array_slice( array_values( array_filter( $related, fn( $item ) => $item['id'] !== $id ) ), 0, 3 );
	$accessories = urban_shisha_query_products( array( 'limit' => 4, 'category' => array( 'bowls', 'heat-management', 'hoses-mouthpieces', 'tools-spares', 'charcoal' ) ) );
	$accessories = array_slice( array_values( array_filter( $accessories, fn( $item ) => $item['id'] !== $id ) ), 0, 3 );
	return compact( 'product', 'data', 'gallery', 'specs', 'related', 'accessories' );
}

function urban_shisha_get_single_product_client_config(): array {
	$context = urban_shisha_get_single_product_context();
	if ( ! $context ) { return array(); }
	return array( 'productId' => $context['data']['id'], 'gallery' => $context['gallery'], 'priceHtml' => $context['data']['price_html'], 'published' => 'publish' === $context['product']->get_status(), 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'cartNonce' => wp_create_nonce( 'urban_shisha_cart_nonce' ) );
}

/** Protect native and AJAX purchase flows while imported products are drafts. */
function urban_shisha_product_purchase_validation( $valid, $product_id ) {
	if ( 'publish' !== get_post_status( $product_id ) ) {
		wc_add_notice( __( 'This product is not available to purchase yet.', 'urban-shisha' ), 'error' );
		return false;
	}
	return $valid;
}
add_filter( 'woocommerce_add_to_cart_validation', 'urban_shisha_product_purchase_validation', 10, 2 );

function urban_shisha_product_add_to_cart(): void {
	check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );
	if ( ! WC()->cart ) { wc_load_cart(); }
	$id = absint( $_POST['product_id'] ?? 0 );
	$variation_id = absint( $_POST['variation_id'] ?? 0 );
	$quantity = wc_stock_amount( wp_unslash( $_POST['quantity'] ?? '1' ) );
	$product = wc_get_product( $id );
	$attributes = array();
	foreach ( $_POST as $key => $value ) {
		if ( str_starts_with( $key, 'attribute_' ) && is_scalar( $value ) ) { $attributes[ sanitize_title( $key ) ] = wc_clean( wp_unslash( $value ) ); }
	}
	try {
		if ( ! $product || 'publish' !== $product->get_status() || $quantity <= 0 || ! $product->is_purchasable() ) { throw new Exception( 'This product is not available to purchase yet.' ); }
		if ( $product->is_type( 'variable' ) ) {
			$variation = wc_get_product( $variation_id );
			if ( ! $variation || $variation->get_parent_id() !== $id ) { throw new Exception( 'Please choose your product options.' ); }
		} elseif ( ! $product->is_type( 'simple' ) || $variation_id ) { throw new Exception( 'Please use this product’s purchase options.' ); }
		if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $id, $quantity, $variation_id, $attributes ) ) { throw new Exception( 'Unable to add this selection.' ); }
		$key = WC()->cart->add_to_cart( $id, $quantity, $variation_id, $attributes );
		if ( ! $key ) { throw new Exception( 'Please check the product options and stock.' ); }
		wc_clear_notices();
		do_action( 'woocommerce_ajax_added_to_cart', $id );
		WC_AJAX::get_refreshed_fragments();
	} catch ( Exception $error ) {
		$notices = wc_get_notices( 'error' );
		$message = $notices ? html_entity_decode( wp_strip_all_tags( implode( ' ', array_column( $notices, 'notice' ) ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ) : $error->getMessage();
		wc_clear_notices();
		wp_send_json_error( array( 'message' => $message ) );
	}
}
add_action( 'wp_ajax_urban_shisha_product_add_to_cart', 'urban_shisha_product_add_to_cart' );
add_action( 'wp_ajax_nopriv_urban_shisha_product_add_to_cart', 'urban_shisha_product_add_to_cart' );

/** Keep a dedicated AJAX purchase from also running the native POST handler. */
add_action( 'wp_loaded', function () {
	if ( wp_doing_ajax() && 'urban_shisha_product_add_to_cart' === ( $_REQUEST['action'] ?? '' ) ) {
		remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );
	}
}, 5 );

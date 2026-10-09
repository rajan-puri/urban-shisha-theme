<?php
/**
 * Urban Shisha — Single Product data resolution and template helpers.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retrieve comprehensive single product data context.
 *
 * @param int|WC_Product|null $product_or_id Product object or ID.
 * @return array<string, mixed>|null
 */
function urban_shisha_get_single_product_context( $product_or_id = null ): ?array {
	$product = null;
	if ( $product_or_id instanceof WC_Product ) {
		$product = $product_or_id;
	} elseif ( is_numeric( $product_or_id ) && $product_or_id > 0 ) {
		$product = wc_get_product( $product_or_id );
	} else {
		global $product;
	}

	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	$id          = $product->get_id();
	$status      = get_post_status( $id );
	$can_preview = current_user_can( 'edit_products' );

	if ( 'draft' === $status && ! $can_preview ) {
		return null;
	}

	$data = urban_shisha_get_product_data( $product );
	if ( ! $data ) {
		return null;
	}

	// 1. Resolve Gallery: Studio cutout first, followed by real WooCommerce images
	$gallery = array();

	// Studio cutout
	$gallery[] = array(
		'type' => 'studio',
		'name' => __( 'Studio cutout', 'urban-shisha' ),
		'src'  => $data['cutout_url'],
	);

	// Featured image if distinct from cutout
	$featured_id = (int) $product->get_image_id();
	if ( $featured_id ) {
		$feat_url = wp_get_attachment_image_url( $featured_id, 'full' );
		if ( $feat_url && $feat_url !== $data['cutout_url'] ) {
			$gallery[] = array(
				'type' => 'photo',
				'name' => sprintf( __( '%s photograph', 'urban-shisha' ), $data['name'] ),
				'src'  => $feat_url,
			);
		}
	}

	// Gallery images
	$gallery_ids = $product->get_gallery_image_ids();
	if ( ! empty( $gallery_ids ) ) {
		foreach ( $gallery_ids as $g_id ) {
			$g_url = wp_get_attachment_image_url( $g_id, 'full' );
			if ( $g_url ) {
				$gallery[] = array(
					'type' => 'photo',
					'name' => sprintf( __( '%s angle', 'urban-shisha' ), $data['name'] ),
					'src'  => $g_url,
				);
			}
		}
	}

	// If Brando or matching signature product without uploaded gallery, use approved local photoshoot photography
	if ( count( $gallery ) < 2 && false !== stripos( $data['name'], 'brando' ) ) {
		for ( $b = 1; $b <= 3; $b++ ) {
			$gallery[] = array(
				'type' => 'photo',
				'name' => sprintf( __( 'Brando detail photograph %d', 'urban-shisha' ), $b ),
				'src'  => get_theme_file_uri( sprintf( 'assets/images/brando/brando-%d.png', $b ) ),
			);
		}
	}

	// 2. Brand & Series Labels
	$brand_name  = $data['brand_name'] ?: 'COCOYAYA';
	$brand_link  = add_query_arg( 'brand', sanitize_title( $brand_name ), urban_shisha_route_url( 'shop' ) );
	$series_meta = get_post_meta( $id, '_us_product_series', true );
	if ( ! $series_meta ) {
		$series_meta = get_post_meta( $id, '_us_source_vendor', true );
	}
	$series_label = $series_meta ? strtoupper( sanitize_text_field( $series_meta ) ) : __( 'THE KING SERIES', 'urban-shisha' );

	// 3. Subtitle / Finish description
	$subtitle = (string) get_post_meta( $id, '_us_product_subtitle', true );
	if ( ! $subtitle ) {
		// Try resolving from attributes or short description
		$colour_attr = $product->get_attribute( 'pa_colour' ) ?: $product->get_attribute( 'colour' );
		if ( $colour_attr ) {
			$subtitle = sprintf( __( '%s stem.<br>Glass base.', 'urban-shisha' ), esc_html( $colour_attr ) );
		} else {
			$subtitle = __( 'Bronze stem.<br>Green line glass base.', 'urban-shisha' );
		}
	}

	// 4. Quick Specs: Height, Weight, Base/Materials
	$weight_val = $product->get_weight();
	$weight_str = $weight_val ? sprintf( __( 'Approx. %s kg', 'urban-shisha' ), wc_format_localized_decimal( $weight_val ) ) : __( 'Approx. 7 kg', 'urban-shisha' );

	$height_attr = $product->get_attribute( 'pa_height' ) ?: $product->get_attribute( 'height' );
	$height_str  = $height_attr ? esc_html( $height_attr ) : __( '32 inches*', 'urban-shisha' );

	$base_attr   = $product->get_attribute( 'pa_base' ) ?: $product->get_attribute( 'base' );
	$base_str    = $base_attr ? esc_html( $base_attr ) : __( 'Green glass', 'urban-shisha' );

	$quick_specs = array(
		array(
			'label' => __( 'HEIGHT', 'urban-shisha' ),
			'value' => $height_str,
		),
		array(
			'label' => __( 'WEIGHT', 'urban-shisha' ),
			'value' => $weight_str,
		),
		array(
			'label' => __( 'BASE', 'urban-shisha' ),
			'value' => $base_str,
		),
	);

	// 5. Spec poster image & details
	$poster_img_url = '';
	if ( count( $gallery ) > 2 && 'photo' === $gallery[2]['type'] ) {
		$poster_img_url = $gallery[2]['src'];
	} elseif ( count( $gallery ) > 1 && 'photo' === $gallery[1]['type'] ) {
		$poster_img_url = $gallery[1]['src'];
	} else {
		$poster_img_url = get_theme_file_uri( 'assets/images/brando/brando-2.png' );
	}

	$poster_heading = __( 'BRONZE.<br>GLASS.<br>CHARACTER.', 'urban-shisha' );

	// 6. Recommended Accessories
	$accessories = urban_shisha_query_products(
		array(
			'limit'    => 3,
			'category' => array( 'bowls', 'heat', 'hoses', 'care', 'charcoal', 'accessories' ),
		)
	);

	// 7. Related Hookahs
	$related_products = urban_shisha_query_products(
		array(
			'limit'    => 3,
			'category' => 'hookahs',
		)
	);

	// Filter out the current product from related lists
	$accessories      = array_values( array_filter( $accessories, fn( $p ) => (int) $p['id'] !== $id ) );
	$related_products = array_values( array_filter( $related_products, fn( $p ) => (int) $p['id'] !== $id ) );

	return array(
		'product'          => $product,
		'data'             => $data,
		'gallery'          => $gallery,
		'brand_name'       => $brand_name,
		'brand_link'       => $brand_link,
		'series_label'     => $series_label,
		'subtitle'         => $subtitle,
		'quick_specs'      => $quick_specs,
		'poster_img_url'   => $poster_img_url,
		'poster_heading'   => $poster_heading,
		'accessories'      => $accessories,
		'related_products' => $related_products,
	);
}

/**
 * Compile localized JSON configuration for product-wordpress.js.
 *
 * @return array<string, mixed>
 */
function urban_shisha_get_single_product_client_config(): array {
	$context = urban_shisha_get_single_product_context();
	if ( ! $context ) {
		return array();
	}

	$product = $context['product'];
	$data    = $context['data'];

	return array(
		'productId'      => $data['id'],
		'name'           => $data['name'],
		'price'          => $data['price_num'],
		'priceHtml'      => $data['price_html'],
		'isInStock'      => $data['is_in_stock'],
		'isPurchasable'  => $data['is_purchasable'],
		'isVariable'     => $data['is_variable'],
		'canQuickAdd'    => $data['can_quick_add'],
		'gallery'        => $context['gallery'],
		'cartNonce'      => wp_create_nonce( 'urban_shisha_cart_nonce' ),
		'wishlistNonce'  => wp_create_nonce( 'urban_shisha_wishlist_nonce' ),
		'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
		'isLoggedIn'     => is_user_logged_in(),
	);
}

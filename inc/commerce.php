<?php
/**
 * Shared real WooCommerce product helpers and commerce AJAX endpoints.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Standard image frame viewboxes for canonical cutouts.
 * Matches index 0 to 11 in catalog.js.
 *
 * @return array<int, array{0: int, 1: int, 2: int, 3: int, 4: int, 5: int}>
 */
function urban_shisha_get_cutout_frames(): array {
	return array(
		0  => array( 251, 0, 767, 1254, 1254, 1254 ),
		1  => array( 140, 0, 985, 1254, 1254, 1254 ),
		2  => array( 0, 71, 1254, 1088, 1254, 1254 ),
		3  => array( 173, 0, 740, 1476, 1056, 1489 ),
		4  => array( 7, 116, 1230, 1084, 1254, 1254 ),
		5  => array( 7, 218, 1247, 834, 1254, 1254 ),
		6  => array( 105, 18, 1018, 1235, 1254, 1254 ),
		7  => array( 74, 43, 1104, 1175, 1254, 1254 ),
		8  => array( 33, 269, 1221, 769, 1254, 1254 ),
		9  => array( 0, 40, 1230, 1198, 1254, 1254 ),
		10 => array( 100, 47, 1103, 1182, 1254, 1254 ),
		11 => array( 173, 20, 945, 1205, 1254, 1254 ),
	);
}

/**
 * Resolve transparent cutout art index for a product based on verified titles/slugs.
 *
 * @param int $product_id Product post ID.
 * @return int Index 0-11.
 */
function urban_shisha_get_product_art_index( int $product_id ): int {
    $attachment = (int) get_post_meta( $product_id, 'us_product_showcase_image', true );
    $index = get_post_meta( $attachment, '_us_cutout_index', true );
    return '' !== $index ? (int) $index : -1;
}

/**
 * Build structured data array from a WooCommerce product.
 *
 * @param int|WC_Product $product_or_id Product object or ID.
 * @return array<string, mixed>|null
 */
function urban_shisha_get_product_data( $product_or_id ): ?array {
	$product = is_numeric( $product_or_id ) ? wc_get_product( $product_or_id ) : $product_or_id;
	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	$id             = $product->get_id();
	$post_status    = get_post_status( $id );
	$can_preview    = current_user_can( 'edit_products' );

	// Guard direct draft access: drafts are strictly protected from non-editors.
	if ( 'publish' !== $post_status && ! current_user_can( 'edit_post', $id ) ) {
		return null;
	}

	$name           = $product->get_name();
	$permalink      = ( 'publish' !== $post_status && current_user_can( 'edit_post', $id ) ) ? get_preview_post_link( $id ) : $product->get_permalink();
	$type           = $product->get_type();
	$is_variable    = $product->is_type( 'variable' );
	$is_in_stock    = $product->is_in_stock();
	$is_purchasable = $product->is_purchasable();

	// Truthful price empty
	$raw_price      = $product->get_price();
	$price_num      = ( '' !== $raw_price && false !== $raw_price ) ? (float) $raw_price : null;
	$raw_regular    = $product->get_regular_price();
	$regular_num    = ( '' !== $raw_regular && false !== $raw_regular ) ? (float) $raw_regular : null;
	$price_html     = $product->get_price_html();
	if ( '' === $price_html || false === $price_html ) {
		$price_html = ( null !== $price_num ) ? wc_price( $price_num ) : '<span class="price-unpriced">' . esc_html__( 'Price on request', 'urban-shisha' ) . '</span>';
	}

	// Primary brand from product_brand taxonomy
	$brand_terms = get_the_terms( $id, 'product_brand' );
	$brand_name  = ( ! empty( $brand_terms ) && ! is_wp_error( $brand_terms ) ) ? $brand_terms[0]->name : get_post_meta( $id, '_us_source_vendor', true );

	// Category label
	$cat_terms = get_the_terms( $id, 'product_cat' );
	$cat_name  = ( ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->name : '';
	$cat_slug  = ( ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->slug : '';

	// Badge
	$badge = '';
	if ( $product->is_on_sale() ) {
		$badge = 'Sale';
	} elseif ( $product->is_featured() ) {
		$badge = 'Signature';
	} elseif ( 'yes' === get_post_meta( $id, '_us_badge_bestseller', true ) ) {
		$badge = 'Bestseller';
	} elseif ( 'yes' === get_post_meta( $id, '_us_badge_new', true ) ) {
		$badge = 'New';
	}

	// Showcase cutout image from SCF or attachment meta
	$showcase_id = (int) get_post_meta( $id, 'us_product_showcase_image', true );
	if ( ! $showcase_id && function_exists( 'get_field' ) ) {
		$showcase_id = (int) get_field( 'us_product_showcase_image', $id );
	}

    $art_index = urban_shisha_get_product_art_index( $id );
    $image_id = $showcase_id ?: (int) $product->get_image_id();
    $cutout_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : wc_placeholder_img_src();
    $photo = $image_id ? urban_shisha_render_media_photo( $image_id ) : '<img class="catalog-photo" src="' . esc_url( $cutout_url ) . '" alt="">';
    $art_html = sprintf( '<span class="product-art has-cutout%s" role="img" aria-label="%s">%s</span>', $art_index >= 0 ? ' art-' . $art_index : '', esc_attr( $name ), $photo );

	// Action behavior: can quick-add if simple, in stock, and purchasable
	$can_quick_add = 'publish' === $post_status && $product->is_type( 'simple' ) && $is_in_stock && $is_purchasable;
	if ( $can_quick_add ) {
		$cta_label = __( 'Add to bag', 'urban-shisha' );
	} elseif ( $is_variable ) {
		$cta_label = __( 'Select options', 'urban-shisha' );
	} elseif ( ! $is_in_stock ) {
		$cta_label = __( 'Out of stock', 'urban-shisha' );
	} else {
		$cta_label = __( 'View product', 'urban-shisha' );
	}

	return array(
		'id'             => $id,
		'name'           => $name,
		'permalink'      => $permalink,
		'type'           => $type,
		'is_variable'    => $is_variable,
		'is_in_stock'    => $is_in_stock,
		'is_purchasable' => $is_purchasable,
		'can_quick_add'  => $can_quick_add,
		'cta_label'      => $cta_label,
		'price_num'      => $price_num,
		'regular_num'    => $regular_num,
		'price_html'     => $price_html ?: wc_price( $price_num ),
		'brand_name'     => $brand_name,
		'category_name'  => $cat_name ?: __( 'Hookah', 'urban-shisha' ),
		'category_slug'  => $cat_slug,
        'categories' => ! is_wp_error( $cat_terms ) && $cat_terms ? wp_list_pluck( $cat_terms, 'slug' ) : array(),
		'badge'          => $badge,
		'art_index'      => $art_index,
		'cutout_url'     => $cutout_url,
		'art_html'       => $art_html,
        'image_html' => $photo,
		'short_desc'     => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
	);
}

/**
 * Query products with truthful permissions check.
 * Admins with edit capability can preview drafts; public output strictly excludes drafts.
 *
 * @param array<string, mixed> $args Query arguments.
 * @return array<int, array<string, mixed>>
 */
function urban_shisha_query_products( array $args = array() ): array {
	$can_preview = current_user_can( 'edit_products' );
	// Public output strictly excludes drafts. No fallback to drafts for public.
	$status = $can_preview ? array( 'publish', 'draft' ) : 'publish';

	$limit     = $args['limit'] ?? 8;
	$orderby   = $args['orderby'] ?? 'date';
	$order     = $args['order'] ?? 'DESC';
	$tax_query = array();

	if ( ! empty( $args['category'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => (array) $args['category'],
		);
	}

	if ( ! empty( $args['brand'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_brand',
			'field'    => 'slug',
			'terms'    => (array) $args['brand'],
		);
	}

	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => $status,
		'posts_per_page' => $limit,
		'orderby'        => $orderby,
		'order'          => $order,
		'fields'         => 'ids',
	);

	if ( ! empty( $args['post__in'] ) ) {
		$query_args['post__in'] = (array) $args['post__in'];
		$query_args['orderby']  = 'post__in';
	}

	if ( ! empty( $tax_query ) ) {
		$query_args['tax_query'] = $tax_query;
	}

	$ids = get_posts( $query_args );

	$results = array();
	foreach ( $ids as $id ) {
		$data = urban_shisha_get_product_data( $id );
		if ( $data ) {
			$results[] = $data;
		}
	}

	return $results;
}

/** Validate real purchasable public inventory before any cart mutation. */
function urban_shisha_cart_product_valid( $product, int $quantity = 1 ): bool {
    return $product instanceof WC_Product
        && 'publish' === $product->get_status()
        && $product->is_type( 'simple' )
        && $product->is_purchasable()
        && $product->is_in_stock()
        && $quantity > 0 && $quantity <= 9999
        && ( ! $product->is_sold_individually() || 1 === $quantity )
        && $product->has_enough_stock( $quantity );
}

function urban_shisha_ajax_add_to_cart(): void {
    check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );
    $id = absint( $_POST['product_id'] ?? 0 );
    $raw = wp_unslash( $_POST['quantity'] ?? '1' );
    $quantity = is_numeric( $raw ) ? (int) $raw : 0;
    $product = wc_get_product( $id );
    if ( ! urban_shisha_cart_product_valid( $product, $quantity ) || (string) $quantity !== (string) $raw ) {
        wp_send_json_error( array( 'message' => __( 'This selection is unavailable. Check the product page for options and stock.', 'urban-shisha' ) ), 400 );
    }
    if ( ! WC()->cart ) { wc_load_cart(); }
    if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $id, $quantity ) || ! WC()->cart->add_to_cart( $id, $quantity ) ) {
        wp_send_json_error( array( 'message' => __( 'This quantity could not be added. Please check availability.', 'urban-shisha' ) ), 400 );
    }
    do_action( 'woocommerce_ajax_added_to_cart', $id );
    WC_AJAX::get_refreshed_fragments();
}
add_action( 'wp_ajax_urban_shisha_add_to_cart', 'urban_shisha_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_urban_shisha_add_to_cart', 'urban_shisha_ajax_add_to_cart' );

function urban_shisha_ajax_builder_add_to_cart(): void {
    check_ajax_referer( 'urban_shisha_home_nonce', 'nonce' );
    $raw = wp_unslash( $_POST['product_ids'] ?? '' );
    if ( ! is_string( $raw ) || ! preg_match( '/^\d+,\d+,\d+,\d+$/', $raw ) ) {
        wp_send_json_error( array( 'message' => __( 'Select one product for each of the four setup pieces.', 'urban-shisha' ) ), 400 );
    }
    $ids = array_map( 'absint', explode( ',', $raw ) );
    $sections = get_field( 'home_sections', (int) get_option( 'page_on_front' ) );
    $builder = array();
    foreach ( (array) $sections as $section ) {
        if ( 'builder' === ( $section['acf_fc_layout'] ?? '' ) && ! empty( $section['home_builder_enabled'] ) ) { $builder = $section; break; }
    }
    if ( ! WC()->cart ) { wc_load_cart(); }
    $groups = array( 'hookahs' => 'hookahs', 'bowls' => 'bowls', 'heat' => 'heat-management', 'charcoal' => 'charcoal' );
    foreach ( array_keys( $groups ) as $i => $group ) {
        $product = wc_get_product( $ids[$i] );
        $term = get_term_by( 'slug', $groups[$group], 'product_cat' );
        $category_ids = $term ? array_merge( array( (int) $term->term_id ), get_term_children( $term->term_id, 'product_cat' ) ) : array();
        $allowed = $category_ids && has_term( $category_ids, 'product_cat', $ids[$i] );
        if ( ! $builder || ! $allowed || ! urban_shisha_cart_product_valid( $product ) || ! apply_filters( 'woocommerce_add_to_cart_validation', true, $ids[$i], 1 ) ) {
            wp_send_json_error( array( 'message' => __( 'One or more setup pieces are unavailable. Please check stock and options.', 'urban-shisha' ) ), 400 );
        }
    }
    $before = WC()->cart->get_cart();
    try {
        foreach ( $ids as $id ) {
            if ( ! WC()->cart->add_to_cart( $id, 1 ) ) { throw new Exception( 'unavailable' ); }
        }
    } catch ( Throwable $e ) {
        WC()->cart->set_cart_contents( $before );
        WC()->cart->calculate_totals();
        wp_send_json_error( array( 'message' => __( 'The setup could not be added. Your bag has been preserved.', 'urban-shisha' ) ), 400 );
    }
    foreach ( $ids as $id ) { do_action( 'woocommerce_ajax_added_to_cart', $id ); }
    wp_send_json_success( array( 'message' => __( 'Your setup is in the bag.', 'urban-shisha' ), 'count' => WC()->cart->get_cart_contents_count() ) );
}
add_action( 'wp_ajax_urban_shisha_builder_add_to_cart', 'urban_shisha_ajax_builder_add_to_cart' );
add_action( 'wp_ajax_nopriv_urban_shisha_builder_add_to_cart', 'urban_shisha_ajax_builder_add_to_cart' );

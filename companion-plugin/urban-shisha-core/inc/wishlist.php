<?php
/**
 * Wishlist management and WooCommerce product data persistence.
 *
 * Persists guest IDs in client storage and logged-in user IDs in user meta.
 * Validates product IDs and ensures unpublished products are never exposed.
 *
 * @package UrbanShishaCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta key used for user wishlist storage.
 */
define( 'URBAN_SHISHA_WISHLIST_META_KEY', '_urban_shisha_wishlist' );

/**
 * Maximum items allowed in wishlist.
 */
define( 'URBAN_SHISHA_WISHLIST_MAX_ITEMS', 100 );

/**
 * Retrieve wishlist product IDs for a user.
 *
 * @param int $user_id User ID, defaults to current user.
 * @return array Array of integer product IDs.
 */
function urban_shisha_get_user_wishlist( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( ! $user_id ) {
		return array();
	}

	$saved = get_user_meta( $user_id, URBAN_SHISHA_WISHLIST_META_KEY, true );
	if ( ! is_array( $saved ) ) {
		return array();
	}

	return urban_shisha_sanitize_wishlist_ids( $saved );
}

/**
 * Update wishlist product IDs for a user.
 *
 * @param array $product_ids Product IDs.
 * @param int   $user_id     User ID, defaults to current user.
 * @return bool True on success, false on failure.
 */
function urban_shisha_update_user_wishlist( $product_ids, $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	if ( ! $user_id ) {
		return false;
	}

	$sanitized = urban_shisha_sanitize_wishlist_ids( $product_ids );
	return (bool) update_user_meta( $user_id, URBAN_SHISHA_WISHLIST_META_KEY, $sanitized );
}

/**
 * Sanitize and bound wishlist product IDs.
 *
 * @param mixed $ids Raw product IDs.
 * @return array Sanitized array of unique integers.
 */
function urban_shisha_sanitize_wishlist_ids( $ids ) {
	if ( is_string( $ids ) ) {
		$decoded = json_decode( $ids, true );
		if ( is_array( $decoded ) ) {
			$ids = $decoded;
		} else {
			$ids = explode( ',', $ids );
		}
	}

	if ( ! is_array( $ids ) ) {
		return array();
	}

	$clean = array();
	foreach ( $ids as $id ) {
		$int_id = absint( $id );
		if ( $int_id > 0 && ! in_array( $int_id, $clean, true ) ) {
			$clean[] = $int_id;
		}
	}

	return array_slice( $clean, 0, URBAN_SHISHA_WISHLIST_MAX_ITEMS );
}

/**
 * Verify whether a product is visible to the current visitor.
 *
 * Unpublished products (drafts, pending, trash) are never exposed to guests
 * or customers without edit permissions.
 *
 * @param int|WC_Product $product Product ID or object.
 * @return bool
 */
function urban_shisha_is_product_visible_for_wishlist( $product ) {
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( absint( $product ) );
	}

	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return false;
	}

	$status = $product->get_status();
	if ( 'publish' === $status ) {
		return true;
	}

	// Administrators/editors can preview drafts during testing.
	return current_user_can( 'edit_post', $product->get_id() );
}

/**
 * Get structured data for wishlist products, filtering out invisible products.
 *
 * @param array $product_ids Array of product IDs.
 * @return array Array of formatted product data arrays.
 */
function urban_shisha_get_wishlist_products_data( $product_ids = array() ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}

	$clean_ids = urban_shisha_sanitize_wishlist_ids( $product_ids );
	$items     = array();

	foreach ( $clean_ids as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product || ! urban_shisha_is_product_visible_for_wishlist( $product ) ) {
			continue;
		}

		$image_id = $product->get_image_id();
		$image_html = '';
		if ( $image_id ) {
			$image_html = wp_get_attachment_image(
				$image_id,
				'woocommerce_thumbnail',
				false,
				array(
					'class'   => 'wishlist-item-img',
					'alt'     => esc_attr( $product->get_name() ),
					'loading' => 'lazy',
				)
			);
		}
		if ( ! $image_html ) {
			$image_html = wc_placeholder_img( 'woocommerce_thumbnail' );
		}

		$terms    = get_the_terms( $id, 'product_cat' );
		$category = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : __( 'Essential', 'urban-shisha' );

		$price_html = $product->get_price_html();
		if ( ! $price_html && '' === $product->get_price() ) {
			$price_html = '<span class="price-unpriced">' . esc_html__( 'Price on enquiry', 'urban-shisha' ) . '</span>';
		}

		$items[] = array(
			'id'         => $id,
			'name'       => $product->get_name(),
			'permalink'  => $product->get_permalink(),
			'price_html' => $price_html,
			'image_html' => $image_html,
			'category'   => $category,
			'in_stock'   => $product->is_in_stock(),
		);
	}

	return $items;
}

/**
 * AJAX: Retrieve wishlist data (READ-ONLY).
 *
 * Does not mutate user meta on GET. For logged-in users, reads user meta.
 * For guests, validates supplied IDs and returns visible product details.
 */
function urban_shisha_ajax_get_wishlist() {
	if ( is_user_logged_in() ) {
		$active_ids = urban_shisha_get_user_wishlist();
	} else {
		$active_ids = isset( $_GET['ids'] ) ? urban_shisha_sanitize_wishlist_ids( wp_unslash( $_GET['ids'] ) ) : array();
	}

	$products_data = urban_shisha_get_wishlist_products_data( $active_ids );
	$visible_ids   = wp_list_pluck( $products_data, 'id' );

	$html = function_exists( 'urban_shisha_get_drawer_wishlist_html' )
		? urban_shisha_get_drawer_wishlist_html( $visible_ids )
		: '';

	wp_send_json_success(
		array(
			'ids'      => $visible_ids,
			'products' => $products_data,
			'html'     => $html,
			'count'    => count( $visible_ids ),
		)
	);
}
add_action( 'wp_ajax_urban_shisha_get_wishlist', 'urban_shisha_ajax_get_wishlist' );
add_action( 'wp_ajax_nopriv_urban_shisha_get_wishlist', 'urban_shisha_ajax_get_wishlist' );

/**
 * AJAX: Toggle wishlist item with nonce verification.
 *
 * Logged-in users persist state in user meta.
 * Guests persist state in browser localStorage with server-side validation.
 */
function urban_shisha_ajax_toggle_wishlist() {
	check_ajax_referer( 'urban_shisha_wishlist_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product ID.', 'urban-shisha' ) ) );
	}

	$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
	if ( ! $product || ! urban_shisha_is_product_visible_for_wishlist( $product ) ) {
		wp_send_json_error( array( 'message' => __( 'Product is unavailable.', 'urban-shisha' ) ) );
	}

	$is_saved = false;
	$ids      = array();

	if ( is_user_logged_in() ) {
		$current = urban_shisha_get_user_wishlist();
		if ( in_array( $product_id, $current, true ) ) {
			$current  = array_values( array_diff( $current, array( $product_id ) ) );
			$is_saved = false;
		} else {
			$current[] = $product_id;
			$is_saved  = true;
		}
		$ids = array_slice( array_unique( $current ), 0, URBAN_SHISHA_WISHLIST_MAX_ITEMS );
		urban_shisha_update_user_wishlist( $ids );
	} else {
		// Guest mode: explicit client action ('add' or 'remove')
		$client_action = isset( $_POST['client_action'] ) ? sanitize_key( wp_unslash( $_POST['client_action'] ) ) : '';
		if ( 'add' === $client_action ) {
			$is_saved = true;
		} elseif ( 'remove' === $client_action ) {
			$is_saved = false;
		} else {
			$pre_ids  = isset( $_POST['pre_ids'] ) ? urban_shisha_sanitize_wishlist_ids( wp_unslash( $_POST['pre_ids'] ) ) : array();
			$is_saved = ! in_array( $product_id, $pre_ids, true );
		}

		$submitted_ids = isset( $_POST['ids'] ) ? urban_shisha_sanitize_wishlist_ids( wp_unslash( $_POST['ids'] ) ) : array();
		$ids           = $submitted_ids;
	}

	$products_data = urban_shisha_get_wishlist_products_data( $ids );
	$visible_ids   = wp_list_pluck( $products_data, 'id' );

	$html = function_exists( 'urban_shisha_get_drawer_wishlist_html' )
		? urban_shisha_get_drawer_wishlist_html( $visible_ids )
		: '';

	wp_send_json_success(
		array(
			'is_saved'   => $is_saved,
			'product_id' => $product_id,
			'ids'        => $visible_ids,
			'products'   => $products_data,
			'html'       => $html,
			'count'      => count( $visible_ids ),
		)
	);
}
add_action( 'wp_ajax_urban_shisha_toggle_wishlist', 'urban_shisha_ajax_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_urban_shisha_toggle_wishlist', 'urban_shisha_ajax_toggle_wishlist' );

/**
 * AJAX: Nonce-protected POST endpoint to merge guest wishlist into user account upon login.
 */
function urban_shisha_ajax_sync_wishlist() {
	check_ajax_referer( 'urban_shisha_wishlist_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Not logged in.', 'urban-shisha' ) ) );
	}

	$client_ids = isset( $_POST['ids'] ) ? urban_shisha_sanitize_wishlist_ids( wp_unslash( $_POST['ids'] ) ) : array();
	$user_ids   = urban_shisha_get_user_wishlist();

	if ( ! empty( $client_ids ) ) {
		$merged   = array_slice( array_unique( array_merge( $user_ids, $client_ids ) ), 0, URBAN_SHISHA_WISHLIST_MAX_ITEMS );
		urban_shisha_update_user_wishlist( $merged );
		$user_ids = $merged;
	}

	$products_data = urban_shisha_get_wishlist_products_data( $user_ids );
	$visible_ids   = wp_list_pluck( $products_data, 'id' );

	$html = function_exists( 'urban_shisha_get_drawer_wishlist_html' )
		? urban_shisha_get_drawer_wishlist_html( $visible_ids )
		: '';

	wp_send_json_success(
		array(
			'ids'      => $visible_ids,
			'products' => $products_data,
			'html'     => $html,
			'count'    => count( $visible_ids ),
		)
	);
}
add_action( 'wp_ajax_urban_shisha_sync_wishlist', 'urban_shisha_ajax_sync_wishlist' );

/**
 * Handle login transitions: sync guest cookie wishlist into user meta if present.
 *
 * @param string  $user_login Username.
 * @param WP_User $user       WP_User object.
 */
function urban_shisha_wishlist_on_user_login( $user_login, $user ) {
	if ( ! $user || ! isset( $user->ID ) ) {
		return;
	}

	if ( isset( $_COOKIE['urban_shisha_guest_wishlist'] ) ) {
		$cookie_ids = urban_shisha_sanitize_wishlist_ids( sanitize_text_field( wp_unslash( $_COOKIE['urban_shisha_guest_wishlist'] ) ) );
		if ( ! empty( $cookie_ids ) ) {
			$saved  = urban_shisha_get_user_wishlist( $user->ID );
			$merged = array_unique( array_merge( $saved, $cookie_ids ) );
			urban_shisha_update_user_wishlist( $merged, $user->ID );
		}
	}
}
add_action( 'wp_login', 'urban_shisha_wishlist_on_user_login', 10, 2 );

/**
 * Inject wishlist heart button into WooCommerce catalog loops if not already rendered.
 */
function urban_shisha_render_loop_wishlist_heart() {
	global $product;
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	$product_id = $product->get_id();
	$name       = $product->get_name();

	echo '<button type="button" class="save-product" data-save="' . esc_attr( $product_id ) . '" data-product-id="' . esc_attr( $product_id ) . '" aria-label="' . esc_attr( sprintf( __( 'Save %s', 'urban-shisha' ), $name ) ) . '" aria-pressed="false">';
	echo '<svg aria-hidden="true"><use href="#i-heart"/></svg>';
	echo '</button>';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'urban_shisha_render_loop_wishlist_heart', 5 );

/**
 * Inject wishlist heart button into WooCommerce single product page.
 */
function urban_shisha_render_single_product_wishlist_heart() {
	global $product;
	if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	$product_id = $product->get_id();
	$name       = $product->get_name();

	echo '<button type="button" class="save-product pdp-save" data-save="' . esc_attr( $product_id ) . '" data-product-id="' . esc_attr( $product_id ) . '" aria-label="' . esc_attr( sprintf( __( 'Save %s', 'urban-shisha' ), $name ) ) . '" aria-pressed="false">';
	echo '<svg aria-hidden="true"><use href="#i-heart"/></svg>';
	echo '</button>';
}
add_action( 'woocommerce_after_add_to_cart_button', 'urban_shisha_render_single_product_wishlist_heart', 20 );

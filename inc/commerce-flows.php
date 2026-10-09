<?php
/**
 * Real WooCommerce cart, checkout, account and order-received flows.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add page-specific body classes for Cart, Checkout, and My Account.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function urban_shisha_flows_body_classes( array $classes ): array {
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		$classes[] = 'cart-page';
		$classes[] = 'shop-page';
	}
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$classes[] = 'checkout-page';
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$classes[] = 'account-page';
	}
	return array_unique( $classes );
}
add_filter( 'body_class', 'urban_shisha_flows_body_classes' );

/**
 * Add custom account endpoints to WooCommerce query vars.
 *
 * @param array $vars WooCommerce query vars.
 * @return array
 */
function urban_shisha_account_query_vars( array $vars ): array {
	$vars['wishlist'] = 'wishlist';
	return $vars;
}
add_filter( 'woocommerce_get_query_vars', 'urban_shisha_account_query_vars' );

/**
 * Register custom rewrite endpoint for My Account Wishlist tab.
 */
function urban_shisha_register_account_endpoints(): void {
	add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'urban_shisha_register_account_endpoints' );

/**
 * Flush rewrite rules if custom endpoint rule is missing.
 */
function urban_shisha_maybe_flush_account_rewrites(): void {
	$rules = get_option( 'rewrite_rules' );
	if ( ! is_array( $rules ) || ! isset( $rules['(.?.+?)/wishlist(/(.*))?/?$'] ) ) {
		flush_rewrite_rules();
	}
}
add_action( 'init', 'urban_shisha_maybe_flush_account_rewrites', 99 );

/**
 * Configure account navigation items to match approved design.
 *
 * @param array $items Existing account menu items.
 * @return array
 */
function urban_shisha_account_menu_items( array $items ): array {
	return array(
		'dashboard'       => __( 'Overview', 'urban-shisha' ),
		'orders'          => __( 'Orders', 'urban-shisha' ),
		'edit-address'    => __( 'Addresses', 'urban-shisha' ),
		'wishlist'        => __( 'Wishlist', 'urban-shisha' ),
		'edit-account'    => __( 'Account details', 'urban-shisha' ),
		'customer-logout' => __( 'Log out', 'urban-shisha' ),
	);
}
add_filter( 'woocommerce_account_menu_items', 'urban_shisha_account_menu_items' );

/**
 * Validate age and confirmations on checkout submission.
 */
function urban_shisha_validate_checkout_confirmations(): void {
	if ( empty( $_POST['confirm_age'] ) ) {
		wc_add_notice( __( 'You must confirm that you are 18 years of age or older to place an order.', 'urban-shisha' ), 'error' );
	}
}
add_action( 'woocommerce_checkout_process', 'urban_shisha_validate_checkout_confirmations' );

/**
 * Save custom checkout confirmations in order metadata (HPOS compatible).
 *
 * Uses woocommerce_checkout_create_order so metadata is persisted to HPOS custom tables
 * in the same atomic transaction as order creation.
 *
 * @param WC_Order $order Order object.
 * @param array    $data  Posted checkout data.
 */
function urban_shisha_save_checkout_meta_hpos( WC_Order $order, array $data ): void {
	if ( ! empty( $_POST['confirm_age'] ) ) {
		$order->update_meta_data( '_us_age_confirmed', 'yes' );
	}
	if ( ! empty( $_POST['consent_marketing'] ) ) {
		$order->update_meta_data( '_us_marketing_consent', 'yes' );
	} else {
		$order->update_meta_data( '_us_marketing_consent', 'no' );
	}
}
add_action( 'woocommerce_checkout_create_order', 'urban_shisha_save_checkout_meta_hpos', 10, 2 );

/**
 * Synchronize delivery and billing addresses when "Billing same as delivery" is checked.
 * Delivery address is mapped to shipping_* and billing address is mapped to billing_*.
 *
 * @param array $data Posted checkout data.
 * @return array Synchronized checkout data.
 */
function urban_shisha_sync_checkout_posted_data( array $data ): array {
	$billing_same = ! empty( $_POST['billing_same'] );
	if ( $billing_same || empty( $data['billing_address_1'] ) ) {
		$fields = array( 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country' );
		foreach ( $fields as $f ) {
			$val = $data[ "shipping_{$f}" ] ?? ( $_POST[ "shipping_{$f}" ] ?? '' );
			if ( 'country' === $f && empty( $val ) ) {
				$val = 'IN';
			}
			$data[ "billing_{$f}" ] = $val;
			$_POST[ "billing_{$f}" ] = $val;
		}
	}
	// Always instruct WooCommerce to persist both shipping and billing addresses
	$data['ship_to_different_address'] = 1;
	$_POST['ship_to_different_address'] = 1;
	return $data;
}
add_filter( 'woocommerce_checkout_posted_data', 'urban_shisha_sync_checkout_posted_data' );

/**
 * Pre-synchronize $_POST before WooCommerce checkout process validation.
 */
function urban_shisha_pre_validate_checkout(): void {
	if ( ! empty( $_POST['billing_same'] ) || empty( $_POST['billing_address_1'] ) ) {
		$fields = array( 'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country' );
		foreach ( $fields as $f ) {
			if ( isset( $_POST[ "shipping_{$f}" ] ) ) {
				$_POST[ "billing_{$f}" ] = sanitize_text_field( wp_unslash( $_POST[ "shipping_{$f}" ] ) );
			}
		}
		$_POST['ship_to_different_address'] = 1;
	}
}
add_action( 'woocommerce_checkout_process', 'urban_shisha_pre_validate_checkout', 1 );

/**
 * Synchronize customer location during AJAX checkout review updates.
 *
 * @param string $post_data_str Serialized post data string.
 */
function urban_shisha_sync_ajax_order_review( string $post_data_str ): void {
	parse_str( $post_data_str, $post_data );
	$billing_same  = ! empty( $post_data['billing_same'] );
	$ship_country  = ! empty( $post_data['shipping_country'] ) ? sanitize_text_field( $post_data['shipping_country'] ) : ( $_POST['s_country'] ?? 'IN' );
	$ship_state    = ! empty( $post_data['shipping_state'] ) ? sanitize_text_field( $post_data['shipping_state'] ) : ( $_POST['s_state'] ?? '' );
	$ship_postcode = ! empty( $post_data['shipping_postcode'] ) ? sanitize_text_field( $post_data['shipping_postcode'] ) : ( $_POST['s_postcode'] ?? '' );
	$ship_city     = ! empty( $post_data['shipping_city'] ) ? sanitize_text_field( $post_data['shipping_city'] ) : ( $_POST['s_city'] ?? '' );
	$ship_address1 = ! empty( $post_data['shipping_address_1'] ) ? sanitize_text_field( $post_data['shipping_address_1'] ) : ( $_POST['s_address'] ?? '' );
	$ship_address2 = ! empty( $post_data['shipping_address_2'] ) ? sanitize_text_field( $post_data['shipping_address_2'] ) : ( $_POST['s_address_2'] ?? '' );

	$bill_country  = ( $billing_same || empty( $post_data['billing_country'] ) ) ? $ship_country : sanitize_text_field( $post_data['billing_country'] );
	$bill_state    = ( $billing_same || empty( $post_data['billing_state'] ) ) ? $ship_state : sanitize_text_field( $post_data['billing_state'] );
	$bill_postcode = ( $billing_same || empty( $post_data['billing_postcode'] ) ) ? $ship_postcode : sanitize_text_field( $post_data['billing_postcode'] );
	$bill_city     = ( $billing_same || empty( $post_data['billing_city'] ) ) ? $ship_city : sanitize_text_field( $post_data['billing_city'] );
	$bill_address1 = ( $billing_same || empty( $post_data['billing_address_1'] ) ) ? $ship_address1 : sanitize_text_field( $post_data['billing_address_1'] );
	$bill_address2 = ( $billing_same || empty( $post_data['billing_address_2'] ) ) ? $ship_address2 : sanitize_text_field( $post_data['billing_address_2'] );

	// Populate $_POST so WooCommerce class-wc-ajax.php sets props truthfully without blanks
	$_POST['s_country']   = $ship_country;
	$_POST['s_state']     = $ship_state;
	$_POST['s_postcode']  = $ship_postcode;
	$_POST['s_city']      = $ship_city;
	$_POST['s_address']   = $ship_address1;
	$_POST['s_address_2'] = $ship_address2;

	$_POST['country']   = $bill_country;
	$_POST['state']     = $bill_state;
	$_POST['postcode']  = $bill_postcode;
	$_POST['city']      = $bill_city;
	$_POST['address']   = $bill_address1;
	$_POST['address_2'] = $bill_address2;

	if ( ! empty( $ship_postcode ) || ! empty( $ship_state ) ) {
		$_POST['has_full_address'] = '1';
	}

	if ( WC()->customer ) {
		WC()->customer->set_shipping_location( $ship_country, $ship_state, $ship_postcode, $ship_city );
		WC()->customer->set_shipping_address( $ship_address1 );
		WC()->customer->set_shipping_address_2( $ship_address2 );

		WC()->customer->set_billing_location( $bill_country, $bill_state, $bill_postcode, $bill_city );
		WC()->customer->set_billing_address( $bill_address1 );
		WC()->customer->set_billing_address_2( $bill_address2 );
		WC()->customer->set_calculated_shipping( true );
	}
}
add_action( 'woocommerce_checkout_update_order_review', 'urban_shisha_sync_ajax_order_review' );

/**
 * Validate shared inventory across variations during standard cart quantity updates.
 *
 * @param bool   $passed        Whether validation passed.
 * @param string $cart_item_key Cart item key.
 * @param array  $values        Cart item values.
 * @param int    $quantity      New quantity.
 * @return bool
 */
function urban_shisha_validate_shared_stock_on_cart_update( bool $passed, string $cart_item_key, array $values, int $quantity ): bool {
	if ( ! $passed ) {
		return false;
	}
	$product = $values['data'] ?? null;
	if ( ! $product ) {
		return $passed;
	}
	$stock_owner_id = $product->get_stock_managed_by_id();
	$stock_owner    = ( $stock_owner_id === $product->get_id() ) ? $product : wc_get_product( $stock_owner_id );

	if ( $stock_owner && $stock_owner->managing_stock() && ! $stock_owner->backorders_allowed() ) {
		$stock_qty = $stock_owner->get_stock_quantity();
		$total_in_cart = 0;
		if ( WC()->cart ) {
			foreach ( WC()->cart->get_cart() as $ckey => $citem ) {
				$cprod = $citem['data'] ?? null;
				if ( $cprod && $cprod->get_stock_managed_by_id() === $stock_owner_id ) {
					$total_in_cart += ( $ckey === $cart_item_key ) ? $quantity : $citem['quantity'];
				}
			}
		}
		if ( null !== $stock_qty && $total_in_cart > $stock_qty ) {
			$already_in_cart = $total_in_cart - $quantity;
			$msg = ( $already_in_cart > 0 )
				? sprintf( __( 'You cannot add that amount — we have %1$s in stock and you already have %2$s in your bag.', 'urban-shisha' ), $stock_qty, $already_in_cart )
				: sprintf( __( 'Only %s items are available in stock.', 'urban-shisha' ), $stock_qty );
			wc_add_notice( $msg, 'error' );
			return false;
		}
	}
	return true;
}
add_filter( 'woocommerce_update_cart_validation', 'urban_shisha_validate_shared_stock_on_cart_update', 10, 4 );

/**
 * Validate shared inventory across variations when adding to bag.
 *
 * @param bool  $passed       Whether validation passed.
 * @param int   $product_id   Product ID.
 * @param int   $quantity     Quantity added.
 * @param int   $variation_id Variation ID.
 * @param array $variations   Attribute variations.
 * @return bool
 */
function urban_shisha_validate_shared_stock_on_add_to_cart( bool $passed, int $product_id, int $quantity, int $variation_id = 0, array $variations = array() ): bool {
	if ( ! $passed ) {
		return false;
	}
	$product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
	if ( ! $product ) {
		return $passed;
	}
	$stock_owner_id = $product->get_stock_managed_by_id();
	$stock_owner    = ( $stock_owner_id === $product->get_id() ) ? $product : wc_get_product( $stock_owner_id );

	if ( $stock_owner && $stock_owner->managing_stock() && ! $stock_owner->backorders_allowed() ) {
		$stock_qty = $stock_owner->get_stock_quantity();
		$total_in_cart = $quantity;
		if ( WC()->cart ) {
			foreach ( WC()->cart->get_cart() as $citem ) {
				$cprod = $citem['data'] ?? null;
				if ( $cprod && $cprod->get_stock_managed_by_id() === $stock_owner_id ) {
					$total_in_cart += $citem['quantity'];
				}
			}
		}
		if ( null !== $stock_qty && $total_in_cart > $stock_qty ) {
			$already_in_cart = $total_in_cart - $quantity;
			$msg = ( $already_in_cart > 0 )
				? sprintf( __( 'You cannot add that amount — we have %1$s in stock and you already have %2$s in your bag.', 'urban-shisha' ), $stock_qty, $already_in_cart )
				: sprintf( __( 'Only %s items are available in stock.', 'urban-shisha' ), $stock_qty );
			wc_add_notice( $msg, 'error' );
			return false;
		}
	}
	return true;
}
add_filter( 'woocommerce_add_to_cart_validation', 'urban_shisha_validate_shared_stock_on_add_to_cart', 10, 5 );

/**
 * Synchronize desktop and mobile summary fragments on AJAX order review updates.
 *
 * @param array $fragments Existing fragments.
 * @return array
 */
function urban_shisha_checkout_review_fragments( array $fragments ): array {
	if ( ! WC()->cart ) {
		return $fragments;
	}

	$subtotal = WC()->cart->get_cart_subtotal();
	$fragments['#summary-subtotal'] = '<strong id="summary-subtotal">' . $subtotal . '</strong>';
	$fragments['#mobile-calc-subtotal'] = '<strong id="mobile-calc-subtotal">' . $subtotal . '</strong>';

	$shipping_total = WC()->cart->needs_shipping() ? WC()->cart->get_cart_shipping_total() : __( 'Free', 'urban-shisha' );
	if ( empty( $shipping_total ) && WC()->cart->needs_shipping() ) {
		$shipping_total = __( 'Calculated at checkout', 'urban-shisha' );
	}
	$fragments['#summary-shipping-status'] = '<span class="summary-shipping-status" id="summary-shipping-status">' . $shipping_total . '</span>';
	$fragments['.mobile-summary-shipping-status'] = '<span class="summary-shipping-status mobile-summary-shipping-status">' . $shipping_total . '</span>';

	if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
		$fragments['#summary-taxes'] = '<strong id="summary-taxes">' . wc_price( WC()->cart->get_taxes_total() ) . '</strong>';
	}

	$total = WC()->cart->get_total();
	$fragments['#summary-total'] = '<strong class="summary-payable-amount" id="summary-total">' . $total . '</strong>';
	$fragments['#mobile-calc-payable'] = '<strong class="summary-payable-amount" id="mobile-calc-payable">' . $total . '</strong>';
	$fragments['#mobile-summary-total'] = '<strong id="mobile-summary-total">' . $total . '</strong>';

	if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
		ob_start();
		wc_cart_totals_shipping_html();
		$shipping_html = ob_get_clean();
		$fragments['#checkout-shipping-methods'] = '<div id="checkout-shipping-methods">' . $shipping_html . '</div>';
	}

	return $fragments;
}
add_filter( 'woocommerce_update_order_review_fragments', 'urban_shisha_checkout_review_fragments' );

/**
 * Validate registration fields on My Account.
 *
 * @param WP_Error $errors   Validation errors.
 * @param string   $username Username.
 * @param string   $email    Email.
 * @return WP_Error
 */
function urban_shisha_validate_registration( $errors, string $username, string $email ) {
	if ( ! is_checkout() ) {
		if ( empty( $_POST['register_age'] ) ) {
			$errors->add( 'error', __( 'You must confirm that you are 18 years of age or older to create an account.', 'urban-shisha' ) );
		}
		if ( empty( $_POST['register_terms'] ) ) {
			$errors->add( 'error', __( 'You must agree to the terms & conditions and privacy policy.', 'urban-shisha' ) );
		}
	}
	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'urban_shisha_validate_registration', 10, 3 );

/**
 * Save customer full name upon registration.
 *
 * @param int   $customer_id Customer user ID.
 * @param array $new_customer_data New customer data.
 * @param bool  $password_generated Whether password was generated.
 */
function urban_shisha_save_customer_name( int $customer_id, array $new_customer_data, bool $password_generated ): void {
	if ( ! empty( $_POST['register_name'] ) ) {
		$name = sanitize_text_field( wp_unslash( $_POST['register_name'] ) );
		$parts = explode( ' ', $name, 2 );
		$first_name = $parts[0];
		$last_name = $parts[1] ?? '';

		wp_update_user( array(
			'ID'           => $customer_id,
			'display_name' => $name,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
		) );

		update_user_meta( $customer_id, 'billing_first_name', $first_name );
		if ( $last_name ) {
			update_user_meta( $customer_id, 'billing_last_name', $last_name );
		}
	}
}
add_action( 'woocommerce_created_customer', 'urban_shisha_save_customer_name', 10, 3 );

/**
 * Handle AJAX cart item quantity updates with full stock, variation and backorder validation.
 */
function urban_shisha_ajax_cart_update_quantity(): void {
	check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );

	if ( ! WC()->cart ) {
		wc_load_cart();
	}

	$cart_item_key = sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ?? '' ) );
	$quantity      = absint( $_POST['quantity'] ?? 1 );

	$cart = WC()->cart->get_cart();
	if ( ! isset( $cart[ $cart_item_key ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Item not found in bag.', 'urban-shisha' ) ), 404 );
	}

	$cart_item    = $cart[ $cart_item_key ];
	$current_qty  = $cart_item['quantity'];
	$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

	if ( ! $_product || ! $_product->exists() ) {
		wp_send_json_error( array( 'message' => __( 'Product not available.', 'urban-shisha' ) ), 400 );
	}

	// 1. Core update cart validation filter
	$passed_validation = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $cart_item, $quantity );
	if ( ! $passed_validation ) {
		$notices = wc_get_notices( 'error' );
		wc_clear_notices();
		$msg = ! empty( $notices ) ? wp_strip_all_tags( $notices[0]['notice'] ) : __( 'Invalid quantity selection for this product.', 'urban-shisha' );
		wp_send_json_error( array(
			'message'       => $msg,
			'cart_item_key' => $cart_item_key,
			'current_qty'   => $current_qty,
		), 400 );
	}

	// 2. Sold-individually rule
	if ( $_product->is_sold_individually() && $quantity > 1 ) {
		wp_send_json_error( array(
			'message'       => sprintf( __( 'You can only purchase 1 unit of %s.', 'urban-shisha' ), $_product->get_name() ),
			'cart_item_key' => $cart_item_key,
			'current_qty'   => 1,
		), 400 );
	}

	// 3. Stock validation & shared variation stock management using get_stock_managed_by_id
	if ( $quantity > 0 ) {
		$stock_owner_id = $_product->get_stock_managed_by_id();
		$stock_owner    = ( $stock_owner_id === $_product->get_id() ) ? $_product : wc_get_product( $stock_owner_id );

		if ( $stock_owner && $stock_owner->managing_stock() && ! $stock_owner->backorders_allowed() ) {
			$stock_qty = $stock_owner->get_stock_quantity();

			// Sum requested quantity across all cart items sharing this managed stock item
			$total_in_cart_for_stock = 0;
			foreach ( $cart as $ckey => $citem ) {
				$cprod = $citem['data'] ?? null;
				if ( ! $cprod ) {
					continue;
				}
				if ( $cprod->get_stock_managed_by_id() === $stock_owner_id ) {
					$total_in_cart_for_stock += ( $ckey === $cart_item_key ) ? $quantity : $citem['quantity'];
				}
			}

			if ( null !== $stock_qty && $total_in_cart_for_stock > $stock_qty ) {
				$already_in_cart = $total_in_cart_for_stock - $quantity;
				$available_remaining = max( 0, $stock_qty - $already_in_cart );
				$msg = ( $already_in_cart > 0 )
					? sprintf( __( 'You cannot add that amount — we have %1$s in stock and you already have %2$s in your bag.', 'urban-shisha' ), $stock_qty, $already_in_cart )
					: sprintf( __( 'Only %s items are available in stock.', 'urban-shisha' ), $stock_qty );

				wp_send_json_error( array(
					'message'       => $msg,
					'cart_item_key' => $cart_item_key,
					'current_qty'   => $current_qty,
					'stock_qty'     => $stock_qty,
				), 400 );
			}
		} elseif ( ! $_product->has_enough_stock( $quantity ) && ! $_product->backorders_allowed() ) {
			wp_send_json_error( array(
				'message'       => sprintf( __( 'Only %s items are available in stock.', 'urban-shisha' ), $_product->get_stock_quantity() ?: 0 ),
				'cart_item_key' => $cart_item_key,
				'current_qty'   => $current_qty,
			), 400 );
		}
	}

	if ( $quantity > 0 ) {
		WC()->cart->set_quantity( $cart_item_key, $quantity, true );
	} else {
		WC()->cart->remove_cart_item( $cart_item_key );
	}

	WC()->cart->calculate_totals();

	$updated_cart = WC()->cart->get_cart();
	$line_total_html = '';
	if ( isset( $updated_cart[ $cart_item_key ] ) ) {
		$line_total_html = WC()->cart->get_product_subtotal( $_product, $updated_cart[ $cart_item_key ]['quantity'] );
	}

	wp_send_json_success( array(
		'cart_item_key'    => $cart_item_key,
		'quantity'         => $quantity,
		'line_total'       => $line_total_html,
		'cart_count'       => WC()->cart->get_cart_contents_count(),
		'subtotal'         => WC()->cart->get_cart_subtotal(),
		'total'            => WC()->cart->get_total(),
		'is_empty'         => WC()->cart->is_empty(),
		'fragments'        => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
	) );
}
add_action( 'wp_ajax_urban_shisha_cart_update_quantity', 'urban_shisha_ajax_cart_update_quantity' );
add_action( 'wp_ajax_nopriv_urban_shisha_cart_update_quantity', 'urban_shisha_ajax_cart_update_quantity' );

/**
 * Handle AJAX cart item removal.
 */
function urban_shisha_ajax_cart_remove_item(): void {
	check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );

	if ( ! WC()->cart ) {
		wc_load_cart();
	}

	$cart_item_key = sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ?? '' ) );

	if ( WC()->cart->remove_cart_item( $cart_item_key ) ) {
		WC()->cart->calculate_totals();

		wp_send_json_success( array(
			'cart_item_key' => $cart_item_key,
			'cart_count'    => WC()->cart->get_cart_contents_count(),
			'subtotal'      => WC()->cart->get_cart_subtotal(),
			'total'         => WC()->cart->get_total(),
			'is_empty'      => WC()->cart->is_empty(),
			'fragments'     => apply_filters( 'woocommerce_add_to_cart_fragments', array() ),
		) );
	}

	wp_send_json_error( array( 'message' => __( 'Item could not be removed.', 'urban-shisha' ) ), 400 );
}
add_action( 'wp_ajax_urban_shisha_cart_remove_item', 'urban_shisha_ajax_cart_remove_item' );
add_action( 'wp_ajax_nopriv_urban_shisha_cart_remove_item', 'urban_shisha_ajax_cart_remove_item' );

/**
 * Handle AJAX coupon application on Cart and Checkout.
 */
function urban_shisha_ajax_apply_coupon(): void {
	check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );

	if ( ! WC()->cart ) {
		wc_load_cart();
	}

	$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon_code'] ?? '' ) );
	if ( empty( $coupon_code ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a promo code.', 'urban-shisha' ) ), 400 );
	}

	if ( WC()->cart->has_discount( $coupon_code ) ) {
		wp_send_json_error( array( 'message' => __( 'Coupon code already applied.', 'urban-shisha' ) ), 400 );
	}

	$result = WC()->cart->apply_coupon( $coupon_code );

	if ( $result ) {
		WC()->cart->calculate_totals();
		wp_send_json_success( array(
			'message'   => __( 'Coupon applied successfully.', 'urban-shisha' ),
			'subtotal'  => WC()->cart->get_cart_subtotal(),
			'total'     => WC()->cart->get_total(),
			'coupons'   => WC()->cart->get_applied_coupons(),
		) );
	}

	// Capture notice if available
	$notices = wc_get_notices( 'error' );
	wc_clear_notices();
	$message = ! empty( $notices ) ? wp_strip_all_tags( $notices[0]['notice'] ) : __( 'Invalid coupon code.', 'urban-shisha' );

	wp_send_json_error( array( 'message' => $message ), 400 );
}
add_action( 'wp_ajax_urban_shisha_apply_coupon', 'urban_shisha_ajax_apply_coupon' );
add_action( 'wp_ajax_nopriv_urban_shisha_apply_coupon', 'urban_shisha_ajax_apply_coupon' );

/**
 * Handle AJAX coupon removal.
 */
function urban_shisha_ajax_remove_coupon(): void {
	check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );

	if ( ! WC()->cart ) {
		wc_load_cart();
	}

	$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon_code'] ?? '' ) );
	if ( empty( $coupon_code ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid coupon.', 'urban-shisha' ) ), 400 );
	}

	if ( WC()->cart->remove_coupon( $coupon_code ) ) {
		WC()->cart->calculate_totals();
		wp_send_json_success( array(
			'message'  => __( 'Coupon removed.', 'urban-shisha' ),
			'subtotal' => WC()->cart->get_cart_subtotal(),
			'total'    => WC()->cart->get_total(),
		) );
	}

	wp_send_json_error( array( 'message' => __( 'Could not remove coupon.', 'urban-shisha' ) ), 400 );
}
add_action( 'wp_ajax_urban_shisha_remove_coupon', 'urban_shisha_ajax_remove_coupon' );
add_action( 'wp_ajax_nopriv_urban_shisha_remove_coupon', 'urban_shisha_ajax_remove_coupon' );

/**
 * Render Wishlist items for My Account Wishlist endpoint.
 */
function urban_shisha_account_wishlist_content(): void {
	$user_id = get_current_user_id();
	$wishlist_ids = function_exists( 'urban_shisha_get_user_wishlist' ) ? urban_shisha_get_user_wishlist( $user_id ) : array();
	$wishlist_products = array_filter( array_map( 'urban_shisha_get_product_data', $wishlist_ids ) );
	$wishlist_ids = array_values( wp_list_pluck( $wishlist_products, 'id' ) );
	?>
	<div class="account-section" id="section-wishlist">
		<div class="section-card">
			<div class="section-header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'SAVED GEAR', 'urban-shisha' ); ?></p>
					<h2><?php esc_html_e( 'Your Wishlist', 'urban-shisha' ); ?> <span id="wishlist-section-count">(<?php echo esc_html( (string) count( $wishlist_ids ) ); ?>)</span>.</h2>
					<p><?php esc_html_e( 'Products you have marked with a heart across the site.', 'urban-shisha' ); ?></p>
				</div>
			</div>

			<div class="wishlist-grid" id="account-wishlist-grid">
				<?php
				if ( ! empty( $wishlist_ids ) ) :
					foreach ( $wishlist_ids as $p_id ) :
						$product = wc_get_product( $p_id );
						if ( ! $product ) {
							continue;
						}
						$data = urban_shisha_get_product_data( $product );
						if ( ! $data ) {
							continue;
						}
						?>
						<article class="wishlist-card" data-id="<?php echo esc_attr( $p_id ); ?>">
							<a href="<?php echo esc_url( $data['permalink'] ); ?>" class="wishlist-card-media" aria-label="<?php echo esc_attr( $data['name'] ); ?>">
								<?php echo $data['art_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
							<div class="wishlist-card-body">
								<span class="wishlist-brand"><?php echo esc_html( $data['brand_name'] ?: 'URBAN SHISHA' ); ?></span>
								<h3 class="wishlist-title"><a href="<?php echo esc_url( $data['permalink'] ); ?>"><?php echo esc_html( $data['name'] ); ?></a></h3>
								<div class="wishlist-price"><?php echo $data['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
								<div class="wishlist-actions">
									<?php if ( $data['can_quick_add'] ) : ?>
										<button type="button" class="button button-small" data-action="add-to-bag" data-product-id="<?php echo esc_attr( $p_id ); ?>">
											<?php esc_html_e( 'Add to bag', 'urban-shisha' ); ?>
										</button>
									<?php else : ?>
										<a href="<?php echo esc_url( $data['permalink'] ); ?>" class="button button-small">
											<?php echo esc_html( $data['cta_label'] ); ?>
										</a>
									<?php endif; ?>
									<button type="button" class="wishlist-remove-btn" data-action="remove-wishlist" data-product-id="<?php echo esc_attr( $p_id ); ?>" aria-label="<?php esc_attr_e( 'Remove from wishlist', 'urban-shisha' ); ?>">
										<svg aria-hidden="true"><use href="#i-trash"/></svg>
									</button>
								</div>
							</div>
						</article>
						<?php
					endforeach;
				endif;
				?>
			</div>

			<div class="empty-state-box" id="wishlist-empty"<?php echo ! empty( $wishlist_ids ) ? ' hidden' : ''; ?>>
				<div class="empty-state-icon"><svg aria-hidden="true"><use href="#i-heart"/></svg></div>
				<p class="eyebrow"><?php esc_html_e( 'YOUR COLLECTION AWAITS', 'urban-shisha' ); ?></p>
				<h3><?php esc_html_e( 'YOUR NEXT FAVOURITE', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'IS WAITING.', 'urban-shisha' ); ?></span></h3>
				<p><?php esc_html_e( 'Your wishlist is currently empty. Explore our collection of premium hookahs, bowls and essentials to save items here.', 'urban-shisha' ); ?></p>
				<div class="empty-actions" style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
					<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>"><?php esc_html_e( 'Explore the shop', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
					<a class="button button-cream" href="<?php echo esc_url( home_url( '/#builder' ) ); ?>"><?php esc_html_e( 'Build your setup', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				</div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_account_wishlist_endpoint', 'urban_shisha_account_wishlist_content' );

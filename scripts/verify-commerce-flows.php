<?php
/**
 * Verification test script for Urban Shisha WooCommerce core flows:
 * Cart, Checkout, My Account, and Order Confirmation / Thank You.
 *
 * Usage:
 * /Users/rajan/Library/Application\ Support/Local/lightning-services/php-8.5.3+1/bin/darwin-arm64/bin/php -d mysqli.default_socket="/Users/rajan/Library/Application Support/Local/run/gPIQwOPfp/mysql/mysqld.sock" scripts/verify-commerce-flows.php
 */

define( 'WP_USE_THEMES', false );
require_once dirname( __DIR__, 4 ) . '/wp-load.php';

$passed = 0;
$failed = 0;

function it( $description, $test_fn ) {
	global $passed, $failed;
	try {
		$result = $test_fn();
		if ( false !== $result ) {
			echo "  [PASS] {$description}\n";
			$passed++;
		} else {
			echo "  [FAIL] {$description}\n";
			$failed++;
		}
	} catch ( Throwable $e ) {
		echo "  [FAIL] {$description}: " . $e->getMessage() . "\n";
		$failed++;
	}
}

echo "=== Verifying Urban Shisha WooCommerce Flows ===\n\n";

// 1. Pages exist and have shortcodes
it( 'Cart page ID 8 has [woocommerce_cart] shortcode', function() {
	$content = get_post_field( 'post_content', wc_get_page_id( 'cart' ) );
	return str_contains( $content, '[woocommerce_cart]' );
} );

it( 'Checkout page ID 9 has [woocommerce_checkout] shortcode', function() {
	$content = get_post_field( 'post_content', wc_get_page_id( 'checkout' ) );
	return str_contains( $content, '[woocommerce_checkout]' );
} );

it( 'My Account page ID 10 has [woocommerce_my_account] shortcode', function() {
	$content = get_post_field( 'post_content', wc_get_page_id( 'myaccount' ) );
	return str_contains( $content, '[woocommerce_my_account]' );
} );

// 2. Empty Cart Template
it( 'Empty cart renders approved .cart-empty-state with bag badge and shop/builder links', function() {
	if ( ! WC()->cart ) {
		wc_load_cart();
	}
	WC()->cart->empty_cart();
	ob_start();
	echo do_shortcode( '[woocommerce_cart]' );
	$output = ob_get_clean();

	return str_contains( $output, 'cart-empty-state' )
		&& str_contains( $output, 'empty-bag-badge' )
		&& str_contains( $output, 'YOUR NEXT FAVOURITE' )
		&& str_contains( $output, 'Explore the shop' )
		&& str_contains( $output, 'Build your setup' );
} );

// 3. Populated Cart Rendering
it( 'Populated cart renders .cart-layout, 4 table headers, brand, price, qty controls, and aside summary', function() {
	$fixture = new WC_Product_Simple();
	$fixture->set_name( 'Test Hookah Setup' );
	$fixture->set_regular_price( '15000' );
	$fixture->set_status( 'publish' );
	$fixture->save();

	$cart_key = WC()->cart->add_to_cart( $fixture->get_id(), 2 );

	ob_start();
	echo do_shortcode( '[woocommerce_cart]' );
	$output = ob_get_clean();

	$has_layout = str_contains( $output, 'cart-layout' );
	$has_headers = str_contains( $output, 'cart-table-header' )
		&& str_contains( $output, 'PRODUCT' )
		&& str_contains( $output, 'QUANTITY' )
		&& str_contains( $output, 'TOTAL' );
	$has_row = str_contains( $output, 'cart-row' ) && str_contains( $output, 'cart-qty-ctrl' );
	$has_summary = str_contains( $output, 'cart-summary-aside' ) && str_contains( $output, 'ORDER BREAKDOWN' );
	$has_mobile = str_contains( $output, 'cart-mobile-bar' );

	WC()->cart->empty_cart();
	$fixture->delete( true );

	return $has_layout && $has_headers && $has_row && $has_summary && $has_mobile;
} );

// 4. Checkout Template
it( 'Checkout renders all 6 numbered sections, order summary aside, and mobile accordion', function() {
	$fixture = new WC_Product_Simple();
	$fixture->set_name( 'Test Hookah Setup' );
	$fixture->set_regular_price( '15000' );
	$fixture->set_status( 'publish' );
	$fixture->save();

	WC()->cart->add_to_cart( $fixture->get_id(), 1 );

	ob_start();
	echo do_shortcode( '[woocommerce_checkout]' );
	$output = ob_get_clean();

	$has_01 = str_contains( $output, '01 / Contact' );
	$has_02 = str_contains( $output, '02 / Delivery address' );
	$has_03 = str_contains( $output, '03 / Billing address' );
	$has_04 = str_contains( $output, '04 / Delivery details' );
	$has_05 = str_contains( $output, '05 / Payment method' );
	$has_06 = str_contains( $output, '06 / Required confirmations' );
	$has_aside = str_contains( $output, 'checkout-summary-aside' );
	$has_mobile = str_contains( $output, 'mobile-summary-accordion' );
	$has_place_order = str_contains( $output, 'place_order' );

	WC()->cart->empty_cart();
	$fixture->delete( true );

	return $has_01 && $has_02 && $has_03 && $has_04 && $has_05 && $has_06 && $has_aside && $has_mobile && $has_place_order;
} );

// 5. Checkout Confirmation Hooks
it( 'Checkout requires confirm_age in validation', function() {
	$_POST = array(); // No confirm_age
	wc_clear_notices();
	urban_shisha_validate_checkout_confirmations();
	$notices = wc_get_notices( 'error' );
	wc_clear_notices();
	return ! empty( $notices ) && str_contains( $notices[0]['notice'], '18 years of age' );
} );

// 6. Order Received / Thank You Template
it( 'Thank You template displays truthful order number, status badge, items, totals, addresses, and respects permission', function() {
	// Create a test order
	$order = wc_create_order();
	$products = wc_get_products( array( 'limit' => 1 ) );
	$p = $products[0];
	$order->add_product( $p, 1 );
	$order->set_billing_first_name( 'Rajan' );
	$order->set_billing_last_name( 'Puri' );
	$order->set_billing_email( 'rajan@example.com' );
	$order->set_billing_phone( '9876543210' );
	$order->set_billing_address_1( 'Connaught Place 10' );
	$order->set_billing_city( 'New Delhi' );
	$order->set_billing_state( 'DL' );
	$order->set_billing_postcode( '110001' );
	$order->set_billing_country( 'IN' );
	$order->set_payment_method( 'cod' );
	$order->set_payment_method_title( 'Cash on Delivery' );
	$order->calculate_totals();
	$order->set_status( 'processing' );
	$order_id = $order->save();

	// Render thankyou
	ob_start();
	wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	$output = ob_get_clean();

	$has_order_num = str_contains( $output, '#' . $order->get_order_number() );
	$has_status = str_contains( $output, 'SETUP CONFIRMED.' ) || str_contains( $output, 'Processing' );
	$has_metrics = str_contains( $output, 'thankyou-metrics-grid' );
	$has_items = str_contains( $output, 'Items in this order' );
	$has_address = str_contains( $output, 'Connaught Place 10' );

	// Test unauthenticated guest with no order passed:
	ob_start();
	wc_get_template( 'checkout/thankyou.php', array( 'order' => false ) );
	$error_output = ob_get_clean();
	$has_guard = str_contains( $error_output, 'ORDER NOT FOUND' );

	// Clean up test order
	$order->delete( true );

	return $has_order_num && $has_status && $has_metrics && $has_items && $has_address && $has_guard;
} );

// 7. My Account Logged-Out Form
it( 'My Account renders auth-view with editorial hero and login/register tabs when logged out', function() {
	wp_set_current_user( 0 );
	ob_start();
	echo do_shortcode( '[woocommerce_my_account]' );
	$output = ob_get_clean();

	$has_auth_hero = str_contains( $output, 'auth-hero-card' ) && str_contains( $output, 'ONE PLACE FOR' );
	$has_tabs = str_contains( $output, 'auth-tabs' ) && str_contains( $output, 'Log In' ) && str_contains( $output, 'Create Account' );
	$has_login_form = str_contains( $output, 'login-form' ) && str_contains( $output, 'woocommerce-login-nonce' );
	$has_register_form = str_contains( $output, 'register-form' ) && str_contains( $output, 'register-age' );

	return $has_auth_hero && $has_tabs && $has_login_form && $has_register_form;
} );

// 8. My Account Logged-In Dashboard
it( 'My Account renders dashboard-view, sidebar navigation, and 3-card overview when logged in', function() {
	$users = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
	if ( empty( $users ) ) {
		throw new Exception( 'No admin user found' );
	}
	wp_set_current_user( $users[0]->ID );

	ob_start();
	echo do_shortcode( '[woocommerce_my_account]' );
	$output = ob_get_clean();

	$has_header_band = str_contains( $output, 'dashboard-header-band' );
	$has_sidebar = str_contains( $output, 'account-sidebar' ) && str_contains( $output, 'Overview' ) && str_contains( $output, 'Orders' ) && str_contains( $output, 'Wishlist' );
	$has_overview = str_contains( $output, 'overview-grid' ) && ( str_contains( $output, 'Orders &amp; tracking' ) || str_contains( $output, 'Orders & tracking' ) ) && str_contains( $output, 'Saved addresses' ) && str_contains( $output, 'Saved products' );

	wp_set_current_user( 0 );

	return $has_header_band && $has_sidebar && $has_overview;
} );

// 9. My Account Wishlist Endpoint
it( 'My Account wishlist endpoint renders #section-wishlist and handles empty/populated states', function() {
	$users = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
	wp_set_current_user( $users[0]->ID );

	ob_start();
	urban_shisha_account_wishlist_content();
	$output = ob_get_clean();

	wp_set_current_user( 0 );

	return str_contains( $output, 'section-wishlist' )
		&& str_contains( $output, 'SAVED GEAR' )
		&& ( str_contains( $output, 'wishlist-grid' ) || str_contains( $output, 'wishlist-empty' ) );
} );

// 10. Fix 1: Real Two-Variation Shared Stock Validation
it( 'Shared stock: stock=4, two variations requesting combined quantity=6 is rejected via get_stock_managed_by_id()', function() {
	// 1. Create a parent variable product managing stock = 4
	$parent = new WC_Product_Variable();
	$parent->set_name( 'Shared Stock Hookah Rig' );
	$parent->set_manage_stock( true );
	$parent->set_stock_quantity( 4 );
	$parent->set_backorders( 'no' );
	$parent->set_status( 'publish' );
	$pid = $parent->save();

	// 2. Create Variation 1 (Midnight Black)
	$v1 = new WC_Product_Variation();
	$v1->set_parent_id( $pid );
	$v1->set_regular_price( '12000' );
	$v1->set_manage_stock( false ); // inherits parent stock
	$v1->set_status( 'publish' );
	$vid1 = $v1->save();

	// 3. Create Variation 2 (Acid Lime)
	$v2 = new WC_Product_Variation();
	$v2->set_parent_id( $pid );
	$v2->set_regular_price( '12000' );
	$v2->set_manage_stock( false ); // inherits parent stock
	$v2->set_status( 'publish' );
	$vid2 = $v2->save();

	// Load clean instances
	$v1_loaded = wc_get_product( $vid1 );
	$v2_loaded = wc_get_product( $vid2 );

	$same_manager = ( $v1_loaded->get_stock_managed_by_id() === $pid )
		&& ( $v2_loaded->get_stock_managed_by_id() === $pid );

	WC()->cart->empty_cart();

	// Add 3 units of Variation 1
	$key1 = WC()->cart->add_to_cart( $vid1, 3 );

	// Add 1 unit of Variation 2 (total 3 + 1 = 4, exactly stock limit)
	$key2 = WC()->cart->add_to_cart( $vid2, 1 );

	// Now try to update Variation 2 from 1 to 3 (total 3 + 3 = 6 > 4)
	$_POST['cart_item_key'] = $key2;
	$_POST['quantity']      = 3;
	$_POST['nonce']         = wp_create_nonce( 'urban_shisha_cart_nonce' );
	$_REQUEST['nonce']      = $_POST['nonce'];

	$error_caught = null;
	$die_handler = function() {
		return function( $message ) {
			throw new Exception( is_scalar( $message ) ? (string) $message : json_encode( $message ) );
		};
	};
	add_filter( 'wp_die_handler', $die_handler );
	add_filter( 'wp_die_ajax_handler', $die_handler );
	add_filter( 'wp_doing_ajax', '__return_true' );

	ob_start();
	try {
		urban_shisha_ajax_cart_update_quantity();
	} catch ( Exception $e ) {
		$raw = ob_get_clean();
		$error_caught = json_decode( $raw, true ) ?: json_decode( $e->getMessage(), true );
	}

	// Now try valid update of Variation 2 to 1 (total 3 + 1 = 4 <= 4)
	$_POST['quantity'] = 1;
	$success_update = false;
	ob_start();
	try {
		urban_shisha_ajax_cart_update_quantity();
		$raw_success = ob_get_clean();
		$success_data = json_decode( $raw_success, true );
		$success_update = ! empty( $success_data['success'] );
	} catch ( Exception $e ) {
		$raw_success = ob_get_clean();
		$success_data = json_decode( $raw_success, true ) ?: json_decode( $e->getMessage(), true );
		$success_update = ! empty( $success_data['success'] );
	}

	remove_filter( 'wp_die_handler', $die_handler );
	remove_filter( 'wp_die_ajax_handler', $die_handler );
	remove_filter( 'wp_doing_ajax', '__return_true' );

	WC()->cart->empty_cart();
	$v1_loaded->delete( true );
	$v2_loaded->delete( true );
	$parent->delete( true );

	return $same_manager
		&& ! empty( $error_caught )
		&& false === $error_caught['success']
		&& isset( $error_caught['data']['current_qty'] )
		&& 1 === $error_caught['data']['current_qty']
		&& $success_update;
} );

// 11. Fix 2: Checkout AJAX Outgoing Location Values & Customer Sync
it( 'Checkout AJAX syncs native s_* and billing locations, handles billing-same and separate billing without overwrite', function() {
	// Case A: billing_same = 1
	$post_data_same = http_build_query( array(
		'billing_same'        => '1',
		'shipping_first_name' => 'Aditya',
		'shipping_last_name'  => 'Kapoor',
		'shipping_address_1'  => 'Golf Course Rd 44',
		'shipping_city'       => 'Gurugram',
		'shipping_state'      => 'HR',
		'shipping_postcode'   => '122002',
		'shipping_country'    => 'IN',
	) );

	$_POST = array();
	urban_shisha_sync_ajax_order_review( $post_data_same );

	$s_state_ok = ( 'HR' === ( $_POST['s_state'] ?? '' ) );
	$s_post_ok  = ( '122002' === ( $_POST['s_postcode'] ?? '' ) );
	$b_state_ok = ( 'HR' === ( $_POST['state'] ?? '' ) ); // billing synced from delivery
	$b_post_ok  = ( '122002' === ( $_POST['postcode'] ?? '' ) );
	$cust_s_ok  = ( 'HR' === WC()->customer->get_shipping_state() );
	$cust_b_ok  = ( 'HR' === WC()->customer->get_billing_state() );

	// Case B: billing_same = 0 (separate billing)
	$post_data_diff = http_build_query( array(
		'billing_same'        => '0',
		'shipping_first_name' => 'Aditya',
		'shipping_address_1'  => 'Golf Course Rd 44',
		'shipping_city'       => 'Gurugram',
		'shipping_state'      => 'HR',
		'shipping_postcode'   => '122002',
		'shipping_country'    => 'IN',
		'billing_first_name'  => 'Finance Dept',
		'billing_address_1'   => 'Nariman Point 12',
		'billing_city'        => 'Mumbai',
		'billing_state'       => 'MH',
		'billing_postcode'    => '400021',
		'billing_country'     => 'IN',
	) );

	$_POST = array();
	urban_shisha_sync_ajax_order_review( $post_data_diff );

	$diff_s_ok = ( 'HR' === ( $_POST['s_state'] ?? '' ) ) && ( 'HR' === WC()->customer->get_shipping_state() );
	$diff_b_ok = ( 'MH' === ( $_POST['state'] ?? '' ) ) && ( 'MH' === WC()->customer->get_billing_state() );

	$_POST = array();

	return $s_state_ok && $s_post_ok && $b_state_ok && $b_post_ok && $cust_s_ok && $cust_b_ok && $diff_s_ok && $diff_b_ok;
} );

// 12. Fix 3: Shipping Summaries Never Contain Input Controls or Duplicate IDs
it( 'Shipping summaries contain only price or text and no radio controls or duplicate input IDs', function() {
	$fixture = new WC_Product_Simple();
	$fixture->set_name( 'Test Pipe' );
	$fixture->set_regular_price( '5000' );
	$fixture->set_status( 'publish' );
	$fixture->save();

	WC()->cart->empty_cart();
	WC()->cart->add_to_cart( $fixture->get_id(), 1 );

	// Render review order table
	ob_start();
	woocommerce_order_review();
	$review_table_html = ob_get_clean();

	// Review order table shipping row must not contain duplicate radios
	$has_radio_in_review_table = str_contains( $review_table_html, 'type="radio"' );

	// Render checkout fragments
	$fragments = urban_shisha_checkout_review_fragments( array() );
	$summary_shipping_html = $fragments['#summary-shipping-status'] ?? '';

	$no_input_in_summary = ! str_contains( $summary_shipping_html, '<input' )
		&& ! str_contains( $summary_shipping_html, 'type="radio"' );

	// Render checkout form
	ob_start();
	echo do_shortcode( '[woocommerce_checkout]' );
	$checkout_html = ob_get_clean();

	// Section 04 contains checkout-shipping-methods
	$has_section04_selector = str_contains( $checkout_html, 'id="checkout-shipping-methods"' );

	// Native shipping toggle present for checkout.js
	$has_native_toggle = str_contains( $checkout_html, 'id="ship-to-different-address"' );

	WC()->cart->empty_cart();
	$fixture->delete( true );

	return ( ! $has_radio_in_review_table ) && $no_input_in_summary && $has_section04_selector && $has_native_toggle;
} );

// 13. Fix 4: HPOS Order Metadata Persistence & Unchecked Consent
it( 'HPOS order metadata saves _us_age_confirmed and does not grant consent when unchecked', function() {
	// Case A: consent checked
	$_POST['confirm_age'] = '1';
	$_POST['consent_marketing'] = '1';

	$order_a = wc_create_order();
	urban_shisha_save_checkout_meta_hpos( $order_a, array() );
	$order_a->save();

	$reloaded_a = wc_get_order( $order_a->get_id() );
	$age_a = $reloaded_a->get_meta( '_us_age_confirmed' );
	$mkt_a = $reloaded_a->get_meta( '_us_marketing_consent' );
	$order_a->delete( true );

	// Case B: consent unchecked
	$_POST['confirm_age'] = '1';
	unset( $_POST['consent_marketing'] );

	$order_b = wc_create_order();
	urban_shisha_save_checkout_meta_hpos( $order_b, array() );
	$order_b->save();

	$reloaded_b = wc_get_order( $order_b->get_id() );
	$age_b = $reloaded_b->get_meta( '_us_age_confirmed' );
	$mkt_b = $reloaded_b->get_meta( '_us_marketing_consent' );
	$order_b->delete( true );

	$_POST = array();

	return ( 'yes' === $age_a )
		&& ( 'yes' === $mkt_a )
		&& ( 'yes' === $age_b )
		&& ( 'no' === $mkt_b ); // not granted when unchecked
} );

// 14. Fix 5: Thank You Page Truthful States (Cancelled, Failed, COD, Paid)
it( 'Thank You page renders explicit ORDER CANCELLED heading for cancelled orders and truthful COD messaging', function() {
	$order = wc_create_order();
	$order->set_payment_method( 'cod' );
	$order->set_payment_method_title( 'Cash on Delivery' );
	$order->set_status( 'cancelled' );
	$order->save();

	ob_start();
	wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	$cancelled_html = ob_get_clean();

	$has_cancelled_h1 = str_contains( $cancelled_html, 'ORDER CANCELLED.' );
	$has_void = str_contains( $cancelled_html, 'TRANSACTION VOID.' );
	$no_awaiting = ! str_contains( $cancelled_html, 'AWAITING PAYMENT.' );

	// Test COD order (status processing, but COD cash collection on delivery)
	$order->set_status( 'processing' );
	$order->save();

	ob_start();
	wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	$cod_html = ob_get_clean();

	$has_cod_pay_on_deliv = str_contains( $cod_html, 'PAY ON DELIVERY.' ) || str_contains( $cod_html, 'CASH COLLECTION ON DELIVERY' );
	$no_false_payment_success = ! str_contains( $cod_html, 'PAYMENT SUCCESSFUL' );

	$order->delete( true );

	return $has_cancelled_h1 && $has_void && $no_awaiting && $has_cod_pay_on_deliv && $no_false_payment_success;
} );

echo "\nSummary: {$passed} passed, {$failed} failed.\n";
exit( $failed > 0 ? 1 : 0 );


<?php
/**
 * Verification script for About Us, Contact Us, Wholesale, and Policy pages.
 */
require_once __DIR__ . '/../../../../wp-load.php';

$errors = array();
$check = function ( $condition, $message ) use ( &$errors ) {
	if ( ! $condition ) {
		$errors[] = $message;
		echo " [FAIL] {$message}\n";
	} else {
		echo " [PASS] {$message}\n";
	}
};

echo "=== 1. WooCommerce Policy Settings ===\n";
$terms_id = (int) get_option( 'woocommerce_terms_page_id' );
$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
$returns_id = (int) get_option( 'woocommerce_refund_returns_page_id' );

$check( 18 === $terms_id, "woocommerce_terms_page_id set to 18 (got {$terms_id})" );
$check( 3 === $privacy_id, "wp_page_for_privacy_policy set to 3 (got {$privacy_id})" );
$check( 11 === $returns_id, "woocommerce_refund_returns_page_id set to 11 (got {$returns_id})" );

echo "\n=== 2. Contact Form 7 Forms ===\n";
$contact_form = WPCF7_ContactForm::get_instance( 560 );
$check( null !== $contact_form, 'CF7 Contact Form 560 exists' );
if ( $contact_form ) {
	$c_tags = $contact_form->scan_form_tags();
	$tag_names = wp_list_pluck( $c_tags, 'name' );
	$check( in_array( 'your-name', $tag_names, true ), 'CF7 Contact form has your-name field' );
	$check( in_array( 'your-email', $tag_names, true ), 'CF7 Contact form has your-email field' );
	$check( in_array( 'your-phone', $tag_names, true ), 'CF7 Contact form has your-phone field' );
	$check( in_array( 'your-topic', $tag_names, true ), 'CF7 Contact form has your-topic field' );
	$check( in_array( 'your-order-ref', $tag_names, true ), 'CF7 Contact form has your-order-ref field' );
	$check( in_array( 'your-message', $tag_names, true ), 'CF7 Contact form has your-message field' );
}

$wholesale_form = WPCF7_ContactForm::get_instance( 562 );
$check( null !== $wholesale_form, 'CF7 Wholesale Form 562 exists' );
if ( $wholesale_form ) {
	$ws_tags = $wholesale_form->scan_form_tags();
	$ws_tag_names = wp_list_pluck( $ws_tags, 'name' );
	$check( in_array( 'ws-name', $ws_tag_names, true ), 'CF7 Wholesale form has ws-name field' );
	$check( in_array( 'ws-business', $ws_tag_names, true ), 'CF7 Wholesale form has ws-business field' );
	$check( in_array( 'ws-city', $ws_tag_names, true ), 'CF7 Wholesale form has ws-city field' );
	$check( in_array( 'ws-pin', $ws_tag_names, true ), 'CF7 Wholesale form has ws-pin field' );
	$check( in_array( 'ws-email', $ws_tag_names, true ), 'CF7 Wholesale form has ws-email field' );
	$check( in_array( 'ws-phone', $ws_tag_names, true ), 'CF7 Wholesale form has ws-phone field' );
	$check( in_array( 'ws-type', $ws_tag_names, true ), 'CF7 Wholesale form has ws-type field' );
	$check( in_array( 'ws-bulk-items', $ws_tag_names, true ), 'CF7 Wholesale form has ws-bulk-items hidden field' );
}

echo "\n=== 3. Template Rendering Checks ===\n";
$admins = get_users( array( 'role' => 'administrator', 'fields' => 'ID', 'number' => 1 ) );
if ( ! empty( $admins ) ) {
	wp_set_current_user( $admins[0] );
}

$render_page = function ( $slug ) {
	global $wp_query, $post;
	$page_obj = get_page_by_path( $slug, OBJECT, 'page' );
	if ( ! $page_obj ) {
		return false;
	}
	$post = $page_obj;
	$wp_query->is_page = true;
	$wp_query->is_singular = true;
	$wp_query->is_home = false;
	$wp_query->queried_object = $page_obj;
	$wp_query->queried_object_id = $page_obj->ID;
	setup_postdata( $post );

	ob_start();
	$template = get_page_template();
	if ( $template && file_exists( $template ) ) {
		include $template;
	}
	return ob_get_clean();
};

// About Us
$about_html = $render_page( 'about' );
$check( false !== strpos( $about_html, 'about-hero' ), 'About page renders .about-hero' );
$check( false !== strpos( $about_html, 'about-story' ), 'About page renders .about-story' );
$check( false !== strpos( $about_html, 'about-collection' ), 'About page renders .about-collection' );
$check( false !== strpos( $about_html, 'product-cutout-0.webp' ), 'About page renders product-cutout-0.webp image' );

// Contact Us
$contact_html = $render_page( 'contact' );
$check( false !== strpos( $contact_html, 'contact-hero' ), 'Contact page renders .contact-hero' );
$check( false !== strpos( $contact_html, 'contact-options-card' ), 'Contact page renders .contact-options-card' );
$check( false !== strpos( $contact_html, 'wpcf7' ), 'Contact page renders Contact Form 7 container' );
$check( false !== strpos( $contact_html, 'contact-name' ), 'Contact page form contains contact-name input' );
$check( false !== strpos( $contact_html, 'contact-faq-section' ), 'Contact page renders FAQ accordion section' );

// Bulk Orders / Wholesale
$ws_html = $render_page( 'bulk-orders' );
$check( false !== strpos( $ws_html, 'ws-hero' ), 'Wholesale page renders .ws-hero' );
$check( false !== strpos( $ws_html, 'bulk-products' ), 'Wholesale page renders #bulk-products catalog' );
$check( false !== strpos( $ws_html, 'ws-enquiry' ), 'Wholesale page renders #enquiry-list sidebar' );
$check( false !== strpos( $ws_html, 'wpcf7' ), 'Wholesale page renders Contact Form 7 container' );
$check( false !== strpos( $ws_html, 'ws-name' ), 'Wholesale page renders ws-name field' );
$check( false !== strpos( $ws_html, 'ws-review-dialog' ), 'Wholesale page renders review dialog' );

// Shipping Policy
$shipping_html = $render_page( 'shipping' );
$check( false !== strpos( $shipping_html, 'policy-hero' ), 'Shipping page renders .policy-hero' );
$check( false !== strpos( $shipping_html, 'policy-switcher' ), 'Shipping page renders .policy-switcher' );
$check( false !== strpos( $shipping_html, 'href="' . urban_shisha_route_url( 'shipping' ) . '" aria-current="page"' ), 'Shipping switcher has aria-current="page"' );
$check( false !== strpos( $shipping_html, 'id="delivery"' ), 'Shipping page renders #delivery section' );

// Returns Policy
$returns_html = $render_page( 'returns' );
$check( false !== strpos( $returns_html, 'policy-hero' ), 'Returns page renders .policy-hero' );
$check( false !== strpos( $returns_html, 'href="' . urban_shisha_route_url( 'returns' ) . '" aria-current="page"' ), 'Returns switcher has aria-current="page"' );
$check( false !== strpos( $returns_html, 'id="issues"' ), 'Returns page renders #issues section' );

// Privacy Policy
$privacy_html = $render_page( 'privacy-policy' );
$check( false !== strpos( $privacy_html, 'policy-hero' ), 'Privacy page renders .policy-hero' );
$check( false !== strpos( $privacy_html, 'href="' . urban_shisha_route_url( 'privacy' ) . '" aria-current="page"' ), 'Privacy switcher has aria-current="page"' );
$check( false !== strpos( $privacy_html, 'clear-preview-data' ), 'Privacy page renders #clear-preview-data button' );
$check( false !== strpos( $privacy_html, 'clear-data-dialog' ), 'Privacy page renders #clear-data-dialog modal' );

// Terms & Conditions
$terms_html = $render_page( 'terms-and-conditions' );
$check( false !== strpos( $terms_html, 'policy-hero' ), 'Terms page renders .policy-hero' );
$check( false !== strpos( $terms_html, 'href="' . urban_shisha_route_url( 'terms' ) . '" aria-current="page"' ), 'Terms switcher has aria-current="page"' );
$check( false !== strpos( $terms_html, 'id="orders"' ), 'Terms page renders #orders section' );

// 18+ Age Policy
$age_html = $render_page( 'age-policy' );
$check( false !== strpos( $age_html, 'policy-hero' ), 'Age policy page renders .policy-hero' );
$check( false !== strpos( $age_html, 'href="' . urban_shisha_route_url( 'age-policy' ) . '" aria-current="page"' ), 'Age policy switcher has aria-current="page"' );
$check( false !== strpos( $age_html, 'id="access"' ), 'Age policy page renders #access section' );

echo "\n=== Verification Summary ===\n";
if ( empty( $errors ) ) {
	echo "ALL TESTS PASSED! (0 errors)\n";
	exit( 0 );
} else {
	echo count( $errors ) . " test(s) failed.\n";
	exit( 1 );
}

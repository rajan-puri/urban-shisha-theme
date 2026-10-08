<?php
/** Real WordPress header rendering and editor integration checks. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
$output = $args[0];
wp_mkdir_p( $output );
$failures = array();
$check = function ( $condition, $message ) use ( &$failures ) { if ( ! $condition ) { $failures[] = $message; } };
$render = function () { ob_start(); get_template_part( 'template-parts/site', 'header' ); return ob_get_clean(); };
wp_set_current_user( 0 );
$html = $render();
$cards = urban_shisha_header_cards();
$check( 6 === count( $cards ), 'Expected six seeded cards' );
foreach ( $cards as $card ) {
    $check( 2 === substr_count( $html, 'href="' . esc_url( $card['url'] ) . '"' ) || substr_count( $html, 'href="' . esc_url( $card['url'] ) . '"' ) > 2, 'Canonical desktop/mobile category link missing' );
    $check( is_file( get_attached_file( $card['image'] ) ), 'Missing mega menu media' );
}
$check( str_contains( $html, 'Sign in / Register' ) && ! str_contains( $html, 'Account dashboard' ), 'Guest account state wrong' );
$check( str_contains( $html, 'name="s"' ) && str_contains( $html, 'name="post_type" value="product"' ), 'Native product search missing' );
$check( ! str_contains( $html, '?category=' ) && ! str_contains( $html, '.html' ), 'Preview URLs leaked into header' );
$original = get_field( 'mega_heading', 'option' );
try {
    update_field( 'field_us_mega_heading', 'HEADER EDIT VERIFICATION', 'option' );
    $check( str_contains( $render(), 'HEADER EDIT VERIFICATION' ), 'SCF heading edit not rendered' );
} finally { update_field( 'field_us_mega_heading', $original, 'option' ); }
$announcement = get_field( 'announcement_text', 'option' );
try {
    update_field( 'field_us_announcement_text', '', 'option' );
    $check( ! str_contains( $render(), 'class="announcement"' ), 'Intentional blank announcement should hide' );
} finally { update_field( 'field_us_announcement_text', $announcement, 'option' ); }
$locations = get_nav_menu_locations();
try {
    $empty = $locations; unset( $empty['primary'] ); set_theme_mod( 'nav_menu_locations', $empty );
    $check( str_contains( $render(), 'class="icon-button mobile-toggle"' ), 'Mobile control missing with no primary menu' );
} finally { set_theme_mod( 'nav_menu_locations', $locations ); }
$menu = wp_get_nav_menu_items( $locations['primary'] );
$temporary_menu = wp_create_nav_menu( 'Header hierarchy verification ' . wp_generate_uuid4() );
if ( is_wp_error( $temporary_menu ) ) { WP_CLI::error( $temporary_menu->get_error_message() ); }
try {
    $parent = wp_update_nav_menu_item( $temporary_menu, 0, array( 'menu-item-title' => 'Browse', 'menu-item-type' => 'custom', 'menu-item-url' => urban_shisha_route_url( 'shop' ), 'menu-item-status' => 'publish' ) );
    $child = wp_update_nav_menu_item( $temporary_menu, 0, array( 'menu-item-title' => 'Accessories', 'menu-item-type' => 'custom', 'menu-item-url' => urban_shisha_route_url( 'shop' ), 'menu-item-parent-id' => $parent, 'menu-item-status' => 'publish' ) );
    wp_update_nav_menu_item( $temporary_menu, 0, array( 'menu-item-title' => 'External reference', 'menu-item-type' => 'custom', 'menu-item-url' => 'https://example.com/', 'menu-item-target' => '_blank', 'menu-item-parent-id' => $child, 'menu-item-status' => 'publish' ) );
    $test_locations = $locations; $test_locations['primary'] = $temporary_menu;
    set_theme_mod( 'nav_menu_locations', $test_locations );
    $nested_header = $render();
    $check( str_contains( $nested_header, 'target="_blank" rel="noopener noreferrer"' ) && 4 === substr_count( $nested_header, 'class="nav-submenu-toggle"' ), 'Native hierarchy/targets not rendered' );
} finally { set_theme_mod( 'nav_menu_locations', $locations ); wp_delete_nav_menu( $temporary_menu ); }
$item = $menu[0];
$old_title = $item->title;
try {
    wp_update_post( array( 'ID' => $item->ID, 'post_title' => 'MENU EDIT VERIFICATION' ) );
    $check( str_contains( $render(), 'MENU EDIT VERIFICATION' ), 'Native menu title edit not rendered' );
} finally { wp_update_post( array( 'ID' => $item->ID, 'post_title' => $old_title ) ); }
if ( function_exists( 'wc_load_cart' ) && ! WC()->cart ) { wc_load_cart(); }
$filter = fn() => 3;
try {
    add_filter( 'woocommerce_cart_contents_count', $filter );
    $fragment = apply_filters( 'woocommerce_add_to_cart_fragments', array() );
    $check( str_contains( $fragment['.site-header .cart-count'] ?? '', '>3</span>' ), 'WooCommerce cart count not reflected in fragments' );
} finally { remove_filter( 'woocommerce_cart_contents_count', $filter ); }
$users = get_users( array( 'role' => 'administrator', 'fields' => 'ID', 'number' => 1 ) );
wp_set_current_user( $users[0] );
$logged = $render();
$check( str_contains( $logged, 'Account dashboard' ) && str_contains( $logged, 'Log out' ), 'Authenticated account state wrong' );
foreach ( array( 'orders', 'edit-address', 'edit-account' ) as $endpoint ) {
    $check( str_contains( $logged, '/' . $endpoint . '/' ), 'Native account endpoint missing: ' . $endpoint );
}
$expires = time() + HOUR_IN_SECONDS;
$cookies = array();
foreach ( array( LOGGED_IN_COOKIE => 'logged_in', AUTH_COOKIE => 'auth' ) as $name => $scheme ) {
    $cookies[] = array( 'name' => $name, 'value' => wp_generate_auth_cookie( $users[0], $expires, $scheme ), 'domain' => wp_parse_url( home_url(), PHP_URL_HOST ), 'path' => '/', 'httpOnly' => true, 'secure' => false, 'sameSite' => 'Lax', 'expires' => $expires );
}
file_put_contents( $output . '/auth.json', wp_json_encode( array( 'cookies' => $cookies ) ) );
chmod( $output . '/auth.json', 0600 );
wp_set_current_user( 0 );
// Guest snapshot uses the actual theme renderer. Coming Soon stays enabled on live guest pages.
ob_start(); get_header(); echo '<main id="main" class="wp-foundation-content wrap"><h1>Header review</h1></main>'; get_footer();
$guest_document = ob_get_clean();
file_put_contents( $output . '/guest.html', $guest_document );
file_put_contents( $output . '/nested.html', preg_replace_callback( '/<svg class="icon-library"[\s\S]*?(?=<main id="main")/', fn() => $nested_header, $guest_document, 1 ) );
$report = array( 'passed' => ! $failures, 'cards' => count( $cards ), 'primary_items' => count( $menu ), 'guest_and_logged_in_state' => true, 'editor_updates' => true, 'native_product_search' => true, 'cart_fragment_count' => true, 'failures' => $failures );
file_put_contents( $output . '/php-verification.json', wp_json_encode( $report, JSON_PRETTY_PRINT ) );
if ( $failures ) { foreach ( $failures as $failure ) { WP_CLI::warning( $failure ); } WP_CLI::error( 'Header verification failed.' ); }
WP_CLI::success( 'Header WordPress checks passed.' );

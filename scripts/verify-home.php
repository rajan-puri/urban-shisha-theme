<?php
/** Verify homepage editor changes and draft-safe commerce against real WordPress. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
$failures = array();
$check = function ( $ok, $message ) use ( &$failures ) { if ( ! $ok ) { $failures[] = $message; } };
$page = (int) get_option( 'page_on_front' );
$sections = get_field( 'home_sections', $page );
$render = function () { ob_start(); urban_shisha_render_home_sections(); return ob_get_clean(); };
$check( 'page' === get_option( 'show_on_front' ) && $page > 0, 'Static Home assignment missing' );
$check( is_array( $sections ) && 14 === count( $sections ), 'Expected fourteen migrated sections' );
$layouts = array_column( (array) $sections, 'acf_fc_layout' );
foreach ( array( 'hero', 'categories', 'brands', 'moods', 'hookah_rail', 'spotlight', 'kit', 'accessories', 'budgets', 'arrivals', 'builder', 'editorial', 'guides', 'closing' ) as $layout ) {
    $check( in_array( $layout, $layouts, true ), 'Missing ' . $layout );
}
wp_set_current_user( 0 );
$check( null === urban_shisha_get_product_data( 51 ), 'Guest direct draft exposure' );
$check( ! urban_shisha_cart_product_valid( wc_get_product( 51 ) ), 'Draft allowed in cart' );
$check( ! urban_shisha_cart_product_valid( wc_get_product( 51 ), 0 ), 'Zero quantity accepted' );
$check( 0 === count( urban_shisha_query_products( array( 'limit' => 200 ) ) ), 'Draft query exposure' );
$admins = get_users( array( 'role' => 'administrator', 'fields' => 'ID', 'number' => 1 ) );
wp_set_current_user( $admins[0] );
$product = urban_shisha_get_product_data( 51 );
$check( is_array( $product ) && str_contains( $product['permalink'] ?? '', 'preview' ), 'Admin draft preview missing' );
$check( ! $product['can_quick_add'], 'Draft quick add enabled' );
$hero = $sections[0];
$media = (int) $hero['home_hero_image'];
$check( is_file( get_attached_file( $media ) ), 'Hero attachment missing' );
$check( hash_file( 'sha256', get_attached_file( $media ) ) === hash_file( 'sha256', get_theme_file_path( 'assets/images/product-cutout-0.webp' ) ), 'Hero asset changed' );
$check( str_contains( urban_shisha_render_media_photo( $media, 'hero-photo' ), '245 0 795 1254' ), 'Original hero framing lost' );
try {
    $edited = $sections;
    $edited[0]['home_hero_heading'] = "EDITOR CHECK. YOUR CHANGE.";
    update_field( 'home_sections', $edited, $page );
    $check( str_contains( $render(), 'EDITOR CHECK.' ), 'Saved heading not rendered' );
    $edited[0]['home_hero_enabled'] = 0;
    update_field( 'home_sections', $edited, $page );
    $check( ! str_contains( $render(), 'EDITOR CHECK.' ), 'Disabled hero rendered' );
    $edited = $sections;
    $first = array_shift( $edited );
    $edited[] = $first;
    update_field( 'home_sections', $edited, $page );
    $html = $render();
    $check( strpos( $html, 'id="categories"' ) < strpos( $html, 'class="hero drop-hero"' ), 'Saved order ignored' );
    update_field( 'home_sections', array(), $page );
    $check( '' === trim( $render() ), 'Intentionally empty sections replaced with defaults' );
} finally {
    update_field( 'home_sections', $sections, $page );
}
$check( get_field( 'home_sections', $page ) == $sections, 'Editor fields not restored' );
$check( 'yes' === get_option( 'woocommerce_coming_soon' ), 'Coming Soon changed' );
$check( 0 === (int) wp_count_posts( 'product' )->publish, 'Imported draft products published' );
WP_CLI::log( wp_json_encode( array( 'pass' => ! $failures, 'sections' => count( (array) $sections ), 'failures' => $failures ), JSON_PRETTY_PRINT ) );
if ( $failures ) { WP_CLI::error( 'Home verification failed.' ); }
WP_CLI::success( 'Home editor, media and draft-safe commerce verified.' );

<?php
/** One-time header migration. Only absent options/unassigned menus are seeded. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
if ( ! function_exists( 'update_field' ) ) { WP_CLI::error( 'Secure Custom Fields is required.' ); }
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
add_filter( 'big_image_size_threshold', '__return_false' );
$seed = function ( $name, $value ) {
    $missing = new stdClass();
    if ( get_option( 'options_' . $name, $missing ) !== $missing ) { return; }
    update_field( 'field_us_' . $name, $value, 'option' );
    WP_CLI::log( 'Seeded ' . $name );
};
$cards = array();
$config = array( array( 'Hookahs', 'Hookahs', 0 ), array( 'Bowls', 'Bowls', 4 ), array( 'Heat Management', 'Heat management', 5 ), array( 'Charcoal', 'Charcoal', 10 ), array( 'Accessories', 'Accessories', 6 ), array( 'Tongs', 'Tongs', 8 ) );
foreach ( $config as $row ) {
    $term = get_term_by( 'name', $row[0], 'product_cat' );
    if ( ! $term ) { WP_CLI::error( 'Missing imported category: ' . $row[0] ); }
    $filename = 'product-cutout-' . $row[2] . '.webp';
    $path = get_theme_file_path( 'assets/images/' . $filename );
    $ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_us_seed_image', 'meta_value' => $filename, 'fields' => 'ids', 'posts_per_page' => 1 ) );
    $id = $ids ? (int) $ids[0] : 0;
    if ( ! $id ) {
        if ( ! is_file( $path ) ) { WP_CLI::error( 'Missing approved cutout: ' . $filename ); }
        $upload = wp_upload_bits( $filename, null, file_get_contents( $path ) );
        if ( $upload['error'] ) { WP_CLI::error( $upload['error'] ); }
        $id = wp_insert_attachment( array( 'post_title' => $row[1], 'post_mime_type' => 'image/webp', 'post_status' => 'inherit' ), $upload['file'], 0, true );
        if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
        update_post_meta( $id, '_us_seed_image', $filename );
        wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
        update_post_meta( $id, '_wp_attachment_image_alt', $row[1] );
    }
    $cards[] = array( 'mega_card_category' => $term->term_id, 'mega_card_label' => $row[1], 'mega_card_image' => $id, 'mega_card_link' => array() );
}
$seed( 'mega_cards', $cards );
$seed( 'mega_eyebrow', 'YOUR NEXT SETUP STARTS HERE.' );
$seed( 'mega_heading', 'Explore the collection.' );
$seed( 'mega_shop_link', array( 'url' => urban_shisha_route_url( 'shop' ), 'title' => 'Shop all products', 'target' => '' ) );
$seed( 'mega_footer_note', 'For the 18+ crew.' );
$seed( 'mobile_category_heading', 'SHOP BY CATEGORY' );
$seed( 'announcement_text', 'For adults 18+. Make it yours.' );
$seed( 'announcement_enabled', 1 );
$locations = get_nav_menu_locations();
$hookahs = get_term_by( 'name', 'Hookahs', 'product_cat' );
$accessories = get_term_by( 'name', 'Accessories', 'product_cat' );
$bulk = get_page_by_path( 'bulk-orders' );
$about = get_page_by_path( 'about' );
$shop_id = wc_get_page_id( 'shop' );
$definitions = array(
    'primary' => array( 'name' => 'Urban Shisha — Main navigation', 'items' => array(
        array( 'title' => 'Shop', 'type' => 'post_type', 'object' => 'page', 'id' => $shop_id, 'classes' => 'urban-mega-trigger' ),
        array( 'title' => 'Hookahs', 'type' => 'taxonomy', 'object' => 'product_cat', 'id' => $hookahs->term_id ),
        array( 'title' => 'Accessories', 'type' => 'taxonomy', 'object' => 'product_cat', 'id' => $accessories->term_id ),
        array( 'title' => 'Bulk Orders', 'type' => 'post_type', 'object' => 'page', 'id' => $bulk->ID ),
        array( 'title' => 'About us', 'type' => 'post_type', 'object' => 'page', 'id' => $about->ID ),
    ) ),
    'header-shortcuts' => array( 'name' => 'Urban Shisha — Mega menu shortcuts', 'items' => array(
        array( 'title' => 'All accessories', 'type' => 'taxonomy', 'object' => 'product_cat', 'id' => $accessories->term_id ),
        array( 'title' => 'Bulk orders', 'type' => 'post_type', 'object' => 'page', 'id' => $bulk->ID ),
    ) ),
);
foreach ( $definitions as $location => $definition ) {
    if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) { continue; }
    $menu = wp_get_nav_menu_object( $definition['name'] );
    $id = $menu ? $menu->term_id : wp_create_nav_menu( $definition['name'] );
    if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
    if ( ! wp_get_nav_menu_items( $id ) ) {
        foreach ( $definition['items'] as $item ) {
            $result = wp_update_nav_menu_item( $id, 0, array( 'menu-item-title' => $item['title'], 'menu-item-type' => $item['type'], 'menu-item-object' => $item['object'], 'menu-item-object-id' => $item['id'], 'menu-item-classes' => $item['classes'] ?? '', 'menu-item-status' => 'publish' ) );
            if ( is_wp_error( $result ) ) { WP_CLI::error( $result->get_error_message() ); }
        }
    }
    $locations[ $location ] = (int) $id;
}
set_theme_mod( 'nav_menu_locations', $locations );
WP_CLI::success( 'Header content, six category images and native menus ready. Existing editor settings preserved.' );

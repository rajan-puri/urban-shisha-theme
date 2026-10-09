<?php
/** One-time approved homepage migration. Does not overwrite saved editor content. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) { exit; }
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function us_home_seed_media( string $relative ): int {
	$path = get_theme_file_path( $relative );
	$existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_query' => array( 'relation' => 'OR', array( 'key' => '_us_home_asset', 'value' => $relative ), array( 'key' => '_us_seed_image', 'value' => basename( $relative ) ), array( 'key' => '_us_seed_image_key', 'value' => 'cutout_' . ( preg_match( '/cutout-(\d+)/', $relative, $m ) ? $m[1] : 'none' ) ) ) ) );
	$id = (int) ( $existing[0] ?? 0 );
	if ( ! $id ) {
		if ( ! is_file( $path ) ) { WP_CLI::error( 'Missing homepage asset: ' . $relative ); }
		$upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) );
		if ( $upload['error'] ) { WP_CLI::error( $upload['error'] ); }
		$type = wp_check_filetype( $upload['file'] );
		$id = wp_insert_attachment( array( 'post_title' => ucwords( str_replace( array( '-', '.webp', '.png' ), ' ', basename( $relative ) ) ), 'post_mime_type' => $type['type'], 'post_status' => 'inherit' ), $upload['file'], 0, true );
		if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	}
	update_post_meta( $id, '_us_home_asset', $relative );
	if ( preg_match( '/product-cutout-(\d+)\.webp$/', $relative, $match ) ) { update_post_meta( $id, '_us_cutout_index', (int) $match[1] ); }
	return $id;
}

function us_home_seed_resolve( $value ) {
	if ( ! is_array( $value ) ) { return 'shop.html' === $value ? urban_shisha_route_url( 'shop' ) : $value; }
	if ( isset( $value['brand'] ) ) {
		$term = get_term_by( 'slug', $value['slug'], 'product_brand' );
		if ( ! $term ) {
			$added = wp_insert_term( $value['brand'], 'product_brand', array( 'slug' => $value['slug'] ) );
			if ( is_wp_error( $added ) ) { WP_CLI::error( $added->get_error_message() ); }
			$term = get_term( $added['term_id'], 'product_brand' );
		}
		if ( ! get_term_meta( $term->term_id, 'thumbnail_id', true ) ) { update_term_meta( $term->term_id, 'thumbnail_id', us_home_seed_media( $value['asset'] ) ); }
		return $term->term_id;
	}
	if ( isset( $value['asset'] ) ) { return us_home_seed_media( $value['asset'] ); }
	foreach ( $value as $key => $child ) { $value[ $key ] = us_home_seed_resolve( $child ); }
	return $value;
}

$page = get_page_by_path( 'home' );
if ( ! $page ) { WP_CLI::error( 'Create the Home page before this migration.' ); }
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page->ID );
if ( 'publish' !== $page->post_status ) { wp_update_post( array( 'ID' => $page->ID, 'post_status' => 'publish' ) ); }

$source = json_decode( file_get_contents( __DIR__ . '/home-content.json' ), true );
$sections = us_home_seed_resolve( $source );
$slugs = array( 'cocoyaya-king-series-brando-hookah-bronze-stem-green-line-base', 'cocoyaya-slims-sterling-hookah-golden', 'dark-knight-slash-hookah-with-bag-gold-stem-gold-base', 'cocoyaya-denali-bonzai-hookah-golden-black-base', 'american-funnel-chilum', 'nagrani-hmd-hookah-heat-management', 'vg-big-handle-silicone-pipe-gold-handle-black-pipe', 'vg-small-handle-silicone-pipe-gold-handle-black-pipe', 'cocoyaya-design-7-hookah-accessories-shisha-nargila-tongs-gold', 'foil-puncher-for-hookah-chillum-black', 'cocoyaya-coconut-coal-250g-18-pcs', 'cocoyaya-silicon-chillum-for-all-hookah-colour-may-vary' );
$ids = array();
foreach ( $slugs as $index => $slug ) {
	$product = get_page_by_path( $slug, OBJECT, 'product' );
	$ids[ $index ] = $product ? $product->ID : 0;
    if ( $product && in_array( $index, array( 4, 5, 6, 7, 8, 9, 10, 11 ), true ) && ! get_option( 'us_home_category_migration_v1' ) ) {
        $category = array( 4 => 'bowls', 5 => 'heat-management', 6 => 'hoses-mouthpieces', 7 => 'hoses-mouthpieces', 8 => 'tools-spares', 9 => 'tools-spares', 10 => 'charcoal', 11 => 'bowls' )[$index];
        $term = get_term_by( 'slug', $category, 'product_cat' );
        if ( $term ) { wp_set_object_terms( $product->ID, array( $term->term_id ), 'product_cat', true ); }
    }
    if ( $product && ! get_post_meta( $product->ID, 'us_product_showcase_image', true ) ) {
		update_field( 'us_product_showcase_image', us_home_seed_media( 'assets/images/product-cutout-' . $index . '.webp' ), $product->ID );
	}
}
foreach ( $sections as &$section ) {
	$layout = $section['acf_fc_layout'];
	$indexes = array( 'hookah_rail' => array( 0, 1, 2, 3 ), 'spotlight' => array( 0, 1, 2, 3 ), 'accessories' => array( 4, 5, 6, 7, 8, 9, 10, 11 ), 'arrivals' => array( 1, 11, 5, 6 ) );
	if ( isset( $indexes[ $layout ] ) ) {
		$section[ 'home_' . $layout . '_products' ] = array_values( array_filter( array_map( fn( $i ) => $ids[ $i ], $indexes[ $layout ] ) ) );
		$section[ 'home_' . $layout . '_limit' ] = count( $indexes[ $layout ] );
		$section[ 'home_' . $layout . '_mode' ] = 'selected';
	}
	if ( 'builder' === $layout ) {
		foreach ( array( 'hookahs' => array( 0, 1, 2, 3 ), 'bowls' => array( 4, 11 ), 'heat' => array( 5 ), 'charcoal' => array( 10 ) ) as $key => $set ) { $section[ 'home_builder_' . $key ] = array_values( array_filter( array_map( fn( $i ) => $ids[ $i ], $set ) ) ); }
	}
}
unset( $section );
if ( ! metadata_exists( 'post', $page->ID, 'home_sections' ) ) {
	update_field( 'field_us_home_sections', $sections, $page->ID );
	WP_CLI::success( 'Saved 14 approved homepage sections and linked real WooCommerce products.' );
} else { WP_CLI::success( 'Homepage already configured; preserved saved editor content.' ); }

update_option( 'us_home_category_migration_v1', 1, false );

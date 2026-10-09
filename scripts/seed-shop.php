<?php
/**
 * Urban Shisha — Idempotent Shop Content & Media Seeder
 *
 * Ensures WooCommerce shop page is designated and published (page only, NOT products),
 * idempotently imports approved shop poster cutout media into the Media Library,
 * and seeds default Shop editorial fields if unset, preserving existing editor changes.
 *
 * Usage:
 *   wp eval-file scripts/seed-shop.php
 *
 * @package UrbanShishaTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	$paths = array(
		dirname( __DIR__, 4 ) . '/wp-load.php',
		dirname( __DIR__, 5 ) . '/public/wp-load.php',
	);
	foreach ( $paths as $path ) {
		if ( file_exists( $path ) ) {
			require_once $path;
			break;
		}
	}
}

defined( 'ABSPATH' ) || exit( "Error: WordPress environment not loaded.\n" );

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

echo "=== Urban Shisha Shop Seeder ===\n";

// 1. Locate or create published Shop page and configure WooCommerce shop page option
$shop_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
if ( $shop_page_id <= 0 ) {
	$shop_page = get_page_by_path( 'shop' );
	if ( $shop_page ) {
		$shop_page_id = $shop_page->ID;
	} else {
		$shop_page_id = wp_insert_post(
			array(
				'post_title'  => 'Shop',
				'post_name'   => 'shop',
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);
	}
}

$page = get_post( $shop_page_id );
if ( $page && 'publish' !== $page->post_status ) {
	wp_update_post(
		array(
			'ID'          => $shop_page_id,
			'post_status' => 'publish',
		)
	);
	echo "Published Shop Page #{$shop_page_id}.\n";
}

update_option( 'woocommerce_shop_page_id', $shop_page_id );
echo "Configured Page #{$shop_page_id} as WooCommerce Shop page.\n";

// 2. Helper to idempotently import seed images
function urban_shisha_seed_shop_media( string $rel_path, string $key, string $title ): int {
	$theme_dir = get_template_directory();
	$full_path = $theme_dir . '/' . ltrim( $rel_path, '/' );

	if ( ! file_exists( $full_path ) ) {
		echo "Warning: Image file {$full_path} not found.\n";
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_query' => array( 'relation' => 'OR', array( 'key' => '_us_seed_image_key', 'value' => $key ), array( 'key' => '_us_cutout_index', 'value' => 1 ) ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$filename = basename( $full_path );
	$contents = file_get_contents( $full_path );
	$upload   = wp_upload_bits( $filename, null, $contents );

	if ( ! empty( $upload['error'] ) ) {
		echo "Error uploading {$filename}: {$upload['error']}\n";
		return 0;
	}

	$type      = wp_check_filetype( $upload['file'] );
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => sanitize_text_field( $title ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file'],
		0,
		true
	);

	if ( is_wp_error( $attach_id ) ) {
		echo "Error creating attachment for {$filename}: " . $attach_id->get_error_message() . "\n";
		return 0;
	}

	$meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
	wp_update_attachment_metadata( $attach_id, $meta );
	update_post_meta( $attach_id, '_us_seed_image_key', $key );
	update_post_meta( $attach_id, '_wp_attachment_image_alt', sanitize_text_field( $title ) );

	echo "Imported media #{$attach_id}: {$title}\n";
	return (int) $attach_id;
}

// 3. Import Shop poster image
$poster_attach_id = urban_shisha_seed_shop_media(
	'assets/images/product-cutout-1.webp',
	'shop_poster_cutout_1',
	'Shop Edit Poster Cutout'
);

// 4. Seed editorial fields on Shop page if unset
$default_shop_fields = array(
	'shop_hero_eyebrow'          => 'THE WHOLE SETUP. ALL IN ONE PLACE.',
	'shop_hero_heading'          => "GOOD TASTE.<br><span>GREAT GEAR.</span>",
	'shop_hero_description'      => 'Statement hookahs. The right extras. Find your kind of setup.',
	'shop_hero_poster_word'      => "THE<br>EDIT",
	'shop_hero_poster_sticker'   => "18+<br>ONLY",
	'shop_hero_poster_image'     => $poster_attach_id,
	'shop_filter_heading'        => 'Refine your edit.',
	'shop_filter_help_eyebrow'   => 'NEED A LITTLE DIRECTION?',
	'shop_filter_help_text'      => 'Build your setup',
	'shop_filter_help_url'       => '/#builder',
	'shop_empty_eyebrow'         => 'LET’S FIND YOUR NEXT PIECE.',
	'shop_empty_heading'         => 'A different edit?',
	'shop_empty_description'     => 'No products match these filters. Try widening your search.',
	'shop_build_band_eyebrow'    => 'GOOD TOGETHER.',
	'shop_build_band_heading'    => "MAKE THE<br>WHOLE THING YOURS.",
	'shop_build_band_button_text' => 'Build your setup',
	'shop_build_band_button_url' => '/#builder',
	'shop_end_note_eyebrow'      => 'YOU’VE SEEN THE WHOLE EDIT.',
	'shop_end_note_text'         => 'Still deciding? <a href="' . esc_url( home_url( '/#builder' ) ) . '">Build a setup that feels like you.</a>',
);

$seeded_count = 0;
foreach ( $default_shop_fields as $meta_key => $default_val ) {
	$current_val = get_post_meta( $shop_page_id, $meta_key, true );
	if ( ! metadata_exists( 'post', $shop_page_id, $meta_key ) ) {
		update_post_meta( $shop_page_id, $meta_key, $default_val );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'field_us_' . $meta_key, $default_val, $shop_page_id );
		}
		$seeded_count++;
	}
}

echo "Seeded {$seeded_count} default editorial fields for Shop page #{$shop_page_id}.\n";
echo "Shop seeding completed successfully.\n";

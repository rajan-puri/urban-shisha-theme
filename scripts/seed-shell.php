<?php
/**
 * Idempotent migration script to seed Urban Shisha global settings, media, and navigation menus.
 *
 * WP-CLI only: wp eval-file scripts/seed-shell.php
 */
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "This script must be run via WP-CLI: wp eval-file scripts/seed-shell.php\n";
	exit( 1 );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

WP_CLI::log( 'Starting Urban Shisha shell content and menu seeding...' );

/**
 * Helper to check if an option exists in the database.
 *
 * @param string $name Option field name without 'options_' prefix.
 * @return bool
 */
function us_option_exists( $name ) {
	$sentinel = new stdClass();
	$raw = get_option( 'options_' . $name, $sentinel );
	if ( $raw !== $sentinel ) {
		return true;
	}
	$raw_direct = get_option( $name, $sentinel );
	return $raw_direct !== $sentinel;
}

/**
 * Helper to seed a single option if absent.
 *
 * @param string $name Field name.
 * @param string $field_key Field key from group-us-global.json.
 * @param mixed  $value Value to seed.
 */
function us_seed_option_if_absent( $name, $field_key, $value ) {
	if ( us_option_exists( $name ) ) {
		WP_CLI::log( "  [Skip] Option {$name} already exists." );
		return;
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( $field_key ?: $name, $value, 'option' );
	} else {
		update_option( 'options_' . $name, $value );
		if ( $field_key ) {
			update_option( '_options_' . $name, $field_key );
		}
	}
	WP_CLI::log( "  [Seeded] Option {$name} set." );
}

/**
 * Import local theme asset into Media Library idempotently.
 *
 * @param string $filename File inside assets/images/.
 * @return int Attachment ID.
 */
function us_import_seed_image( $filename ) {
	$existing = get_posts( array(
		'post_type'      => 'attachment',
		'meta_key'       => '_us_seed_image',
		'meta_value'     => $filename,
		'posts_per_page' => 1,
		'post_status'    => 'inherit',
		'fields'         => 'ids',
	) );

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$source_path = get_template_directory() . '/assets/images/' . $filename;
	if ( ! file_exists( $source_path ) ) {
		WP_CLI::warning( "Source image not found: {$source_path}" );
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, file_get_contents( $source_path ) );
	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( "Failed to upload {$filename}: " . $upload['error'] );
		return 0;
	}

	$file_type = wp_check_filetype( $filename, null );
	$attachment = array(
		'post_mime_type' => $file_type['type'],
		'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		WP_CLI::warning( "Failed to create attachment for {$filename}" );
		return 0;
	}

	$metadata = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
	wp_update_attachment_metadata( $attach_id, $metadata );
	update_post_meta( $attach_id, '_us_seed_image', $filename );

	WP_CLI::log( "  [Media] Imported {$filename} (Attachment ID: {$attach_id})" );
	return (int) $attach_id;
}

if ( ! function_exists( 'urban_shisha_route_url' ) ) {
	$routes_file = dirname( __DIR__ ) . '/inc/routes.php';
	if ( file_exists( $routes_file ) ) {
		require_once $routes_file;
	}
}

// Ensure approved accessory taxonomy terms exist so canonical links resolve.
$missing_accessory_terms = array(
	'hoses-mouthpieces' => 'Hoses & mouthpieces',
	'tools-spares'      => 'Tools & spares',
);
foreach ( $missing_accessory_terms as $term_slug => $term_name ) {
	if ( ! term_exists( $term_slug, 'product_cat' ) && ! term_exists( $term_name, 'product_cat' ) ) {
		$inserted = wp_insert_term( $term_name, 'product_cat', array( 'slug' => $term_slug ) );
		if ( ! is_wp_error( $inserted ) ) {
			WP_CLI::log( "  [Taxonomy] Created product_cat term '{$term_name}' ({$term_slug})." );
		}
	}
}

/**
 * Helper to resolve canonical WooCommerce taxonomy term link.
 *
 * @param string $slug Term slug.
 * @param string $taxonomy Taxonomy name.
 * @return string
 */
function us_get_canonical_term_url( $slug, $taxonomy = 'product_cat' ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( ! $term || is_wp_error( $term ) ) {
		$alt_map = array(
			'heat'              => 'heat-management',
			'heat-management'   => 'heat',
			'hoses'             => 'hoses-mouthpieces',
			'hoses-mouthpieces' => 'hoses',
			'care'              => 'tools-spares',
			'tools-spares'      => 'care',
			'accessories'       => 'accessories',
		);
		if ( isset( $alt_map[ $slug ] ) ) {
			$term = get_term_by( 'slug', $alt_map[ $slug ], $taxonomy );
		}
	}
	if ( ! $term || is_wp_error( $term ) ) {
		$term = get_term_by( 'name', $slug, $taxonomy );
	}
	if ( ! $term || is_wp_error( $term ) ) {
		$name_map = array(
			'hookahs'           => 'Hookahs',
			'bowls'             => 'Bowls',
			'heat'              => 'Heat management',
			'heat-management'   => 'Heat management',
			'charcoal'          => 'Charcoal',
			'hoses'             => 'Hoses & mouthpieces',
			'hoses-mouthpieces' => 'Hoses & mouthpieces',
			'care'              => 'Tools & spares',
			'tools-spares'      => 'Tools & spares',
			'accessories'       => 'Accessories',
		);
		if ( isset( $name_map[ $slug ] ) ) {
			$term = get_term_by( 'name', $name_map[ $slug ], $taxonomy );
		}
	}
	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term, $taxonomy );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	$category_base = get_option( 'woocommerce_product_category_slug', 'product-category' );
	if ( empty( $category_base ) ) {
		$category_base = 'product-category';
	}
	if ( get_option( 'permalink_structure' ) ) {
		return home_url( '/' . trim( $category_base, '/' ) . '/' . $slug . '/' );
	}
	return add_query_arg( 'product_cat', $slug, home_url( '/' ) );
}

// 1. Seed Media for Mega Menu Cards
WP_CLI::log( 'Checking mega menu card media...' );
$mega_card_configs = array(
	array(
		'filename' => 'product-cutout-0.webp',
		'label'    => 'Hookahs',
		'slug'     => 'hookahs',
	),
	array(
		'filename' => 'product-cutout-4.webp',
		'label'    => 'Bowls',
		'slug'     => 'bowls',
	),
	array(
		'filename' => 'product-cutout-5.webp',
		'label'    => 'Heat management',
		'slug'     => 'heat',
	),
	array(
		'filename' => 'product-cutout-10.webp',
		'label'    => 'Charcoal',
		'slug'     => 'charcoal',
	),
	array(
		'filename' => 'product-cutout-6.webp',
		'label'    => 'Hoses & tips',
		'slug'     => 'hoses',
	),
	array(
		'filename' => 'product-cutout-9.webp',
		'label'    => 'Tools & spares',
		'slug'     => 'care',
	),
);

$seeded_mega_cards = array();
foreach ( $mega_card_configs as $card ) {
	$img_id = us_import_seed_image( $card['filename'] );
	$term = get_term_by( 'slug', $card['slug'], 'product_cat' );
	$cat_id = ( $term && ! is_wp_error( $term ) ) ? (int) $term->term_id : 0;
	$url = us_get_canonical_term_url( $card['slug'] );
	$seeded_mega_cards[] = array(
		'mega_card_category' => $cat_id,
		'mega_card_label'    => $card['label'],
		'mega_card_image'    => $img_id,
		'mega_card_link'     => array( 'url' => $url, 'title' => $card['label'], 'target' => '' ),
	);
}

// 2. Seed Global SCF Options if absent
WP_CLI::log( 'Checking global options...' );

$options_to_seed = array(
	'brand_name'             => array( 'field_us_brand_name', 'Urban Shisha' ),
	'brand_domain'           => array( 'field_us_brand_domain', 'urbanshisha.in' ),
	'announcement_enabled'   => array( 'field_us_announcement_enabled', 1 ),
	'announcement_text'      => array( 'field_us_announcement_text', 'A new perspective on your setup.' ),
	'announcement_link'      => array(
		'field_us_announcement_link',
		array(
			'url'    => us_get_canonical_term_url( 'hookahs' ),
			'title'  => 'Discover the collection',
			'target' => '',
		),
	),
	'business_location'      => array( 'field_us_business_location', "Rohini Sector 24, Delhi, 110085, India" ),
	'owners'                 => array(
		'field_us_owners',
		array(
			array(
				'owner_name'  => 'Rajan Puri',
				'owner_phone' => '+91 87001 66924',
			),
			array(
				'owner_name'  => 'Akshay Sadhu',
				'owner_phone' => '+91 78278 77268',
			),
		),
	),
	'mega_eyebrow'           => array( 'field_us_mega_eyebrow', 'THE WHOLE SETUP.' ),
	'mega_heading'           => array( 'field_us_mega_heading', 'Shop your next favourite.' ),
	'mega_cards'             => array( 'field_us_mega_cards', $seeded_mega_cards ),
	'footer_description'     => array( 'field_us_footer_description', "Hookahs with character.\nEssentials with purpose.\nA setup that feels like you." ),
	'newsletter_eyebrow'     => array( 'field_us_newsletter_eyebrow', 'STAY IN THE KNOW.' ),
	'newsletter_heading'     => array( 'field_us_newsletter_heading', 'Good things, in your inbox.' ),
	'newsletter_description' => array( 'field_us_newsletter_description', 'Fresh drops, restocks. Zero boring emails.' ),
	'newsletter_placeholder' => array( 'field_us_newsletter_placeholder', 'Your email address' ),
	'newsletter_note'        => array( 'field_us_newsletter_note', 'Curated drops and restocks. Zero boring emails.' ),
	'footer_copyright'       => array( 'field_us_footer_copyright', '© %YEAR% Urban Shisha.' ),
	'credit_label'           => array( 'field_us_credit_label', 'Made by Mates Creation' ),
	'credit_link'            => array(
		'field_us_credit_link',
		array(
			'url'    => 'https://matescreation.com/',
			'title'  => 'Made by Mates Creation',
			'target' => '_blank',
		),
	),
	'age_eyebrow'            => array( 'field_us_age_eyebrow', 'A CONSIDERED COLLECTION. FOR ADULTS ONLY.' ),
	'age_heading'            => array( 'field_us_age_heading', "GOOD TO\nSEE YOU." ),
	'age_description'        => array( 'field_us_age_description', 'You must be 18 or older to explore Urban Shisha.' ),
	'age_accept_label'       => array( 'field_us_age_accept_label', 'I’m 18 or older' ),
	'age_decline_label'      => array( 'field_us_age_decline_label', 'I’m under 18' ),
	'age_declined_message'   => array( 'field_us_age_declined_message', 'This store is for adults aged 18 and over.' ),
	'health_notice'          => array( 'field_us_health_notice', 'For adults 18+. Smoking is injurious to health.' ),
);

foreach ( $options_to_seed as $opt_name => $config ) {
	list( $field_key, $val ) = $config;
	us_seed_option_if_absent( $opt_name, $field_key, $val );
}

// 3. Seed Menus and Assign Locations for Inactive Theme
WP_CLI::log( 'Checking WordPress navigation menus...' );

$theme_mods = get_option( 'theme_mods_urban-shisha-theme', array() );
if ( ! isset( $theme_mods['nav_menu_locations'] ) || ! is_array( $theme_mods['nav_menu_locations'] ) ) {
	$theme_mods['nav_menu_locations'] = array();
}

$shop_base_url = function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'shop' ) : home_url( '/shop/' );

$menu_definitions = array(
	'primary' => array(
		'name'  => 'Primary Navigation',
		'items' => array(
			array( 'title' => 'Shop', 'url' => $shop_base_url, 'classes' => 'urban-mega-trigger' ),
			array( 'title' => 'Hookahs', 'url' => us_get_canonical_term_url( 'hookahs' ) ),
			array( 'title' => 'Accessories', 'url' => us_get_canonical_term_url( 'accessories' ) ),
			array( 'title' => 'Bulk Orders', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'wholesale' ) : home_url( '/bulk-orders/' ) ),
			array( 'title' => 'The journal', 'url' => home_url( '/#guides' ) ),
		),
	),
	'footer-shop' => array(
		'name'  => 'Shop',
		'items' => array(
			array( 'title' => 'All products', 'url' => $shop_base_url ),
			array( 'title' => 'Hookahs', 'url' => us_get_canonical_term_url( 'hookahs' ) ),
			array( 'title' => 'Bowls', 'url' => us_get_canonical_term_url( 'bowls' ) ),
			array( 'title' => 'Heat management', 'url' => us_get_canonical_term_url( 'heat-management' ) ),
			array( 'title' => 'Charcoal', 'url' => us_get_canonical_term_url( 'charcoal' ) ),
			array( 'title' => 'Hoses & mouthpieces', 'url' => us_get_canonical_term_url( 'hoses-mouthpieces' ) ),
			array( 'title' => 'Tools & spares', 'url' => us_get_canonical_term_url( 'tools-spares' ) ),
			array( 'title' => 'All accessories', 'url' => us_get_canonical_term_url( 'accessories' ) ),
		),
	),
	'footer-discover' => array(
		'name'  => 'Discover',
		'items' => array(
			array( 'title' => 'About Urban Shisha', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'about' ) : home_url( '/about/' ) ),
			array( 'title' => 'Wholesale enquiries', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'wholesale' ) : home_url( '/bulk-orders/' ) ),
			array( 'title' => 'New arrivals', 'url' => home_url( '/#arrivals' ) ),
			array( 'title' => 'Featured hookahs', 'url' => us_get_canonical_term_url( 'hookahs' ) ),
			array( 'title' => 'Build your setup', 'url' => home_url( '/#builder' ) ),
			array( 'title' => 'Shop by budget', 'url' => $shop_base_url ),
			array( 'title' => 'The journal', 'url' => home_url( '/#guides' ) ),
			array( 'title' => 'Your wishlist', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) : home_url( '/my-account/?tab=wishlist' ), 'classes' => 'open-wishlist' ),
		),
	),
	'footer-care' => array(
		'name'  => 'Customer care',
		'items' => array(
			array( 'title' => 'Contact us', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'contact' ) : home_url( '/contact/' ) ),
			array( 'title' => 'Shipping & delivery', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'shipping' ) : home_url( '/shipping/' ) ),
			array( 'title' => 'Returns & refunds', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'returns' ) : home_url( '/returns/' ) ),
			array( 'title' => 'Track your order', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) : home_url( '/my-account/orders/' ) ),
		),
	),
	'footer-account' => array(
		'name'  => 'Account & policies',
		'items' => array(
			array( 'title' => 'My account', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account' ) : home_url( '/my-account/' ) ),
			array( 'title' => 'My orders', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) : home_url( '/my-account/orders/' ) ),
			array( 'title' => 'Saved products', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) : home_url( '/my-account/?tab=wishlist' ), 'classes' => 'open-wishlist' ),
			array( 'title' => 'Privacy policy', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'privacy' ) : home_url( '/privacy-policy/' ) ),
			array( 'title' => 'Terms & conditions', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'terms' ) : home_url( '/terms-and-conditions/' ) ),
			array( 'title' => '18+ policy', 'url' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'age-policy' ) : home_url( '/age-policy/' ) ),
		),
	),
);

$updated_locations = false;

foreach ( $menu_definitions as $location => $def ) {
	$menu_obj = false;

	// Check if already assigned
	if ( ! empty( $theme_mods['nav_menu_locations'][ $location ] ) ) {
		$assigned_id = (int) $theme_mods['nav_menu_locations'][ $location ];
		$menu_obj = wp_get_nav_menu_object( $assigned_id );
		if ( $menu_obj ) {
			WP_CLI::log( "  [Skip] Location '{$location}' already assigned to menu '{$menu_obj->name}' (ID: {$assigned_id})." );
			continue;
		}
	}

	// Look for menu by name
	$menu_obj = wp_get_nav_menu_object( $def['name'] );
	if ( ! $menu_obj ) {
		$created_id = wp_create_nav_menu( $def['name'] );
		if ( is_wp_error( $created_id ) ) {
			WP_CLI::warning( "Failed to create menu '{$def['name']}': " . $created_id->get_error_message() );
			continue;
		}
		$menu_obj = wp_get_nav_menu_object( $created_id );
		WP_CLI::log( "  [Created] Menu '{$def['name']}' (ID: {$created_id})." );

		// Populate items
		foreach ( $def['items'] as $item ) {
			$item_data = array(
				'menu-item-title'  => $item['title'],
				'menu-item-url'    => $item['url'],
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			);
			if ( ! empty( $item['classes'] ) ) {
				$item_data['menu-item-classes'] = $item['classes'];
			}
			wp_update_nav_menu_item( $menu_obj->term_id, 0, $item_data );
		}
		WP_CLI::log( "  [Items] Added " . count( $def['items'] ) . " items to menu '{$def['name']}'." );
	}

	$theme_mods['nav_menu_locations'][ $location ] = (int) $menu_obj->term_id;
	$updated_locations = true;
	WP_CLI::log( "  [Assigned] Location '{$location}' -> Menu ID {$menu_obj->term_id}." );
}

if ( $updated_locations ) {
	update_option( 'theme_mods_urban-shisha-theme', $theme_mods );
	WP_CLI::success( 'Updated nav_menu_locations in theme_mods_urban-shisha-theme.' );
} else {
	WP_CLI::log( 'Nav menu locations up to date.' );
}

// 4. Narrowly scoped idempotent correction for already-seeded menu items
WP_CLI::log( 'Checking assigned menus for known seeded URL corrections...' );

$location_item_corrections = array(
	'primary' => array(
		'Shop'        => array( 'match' => '#^https?://[^/]+/shop/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'shop' ) : home_url( '/shop/' ) ),
		'Hookahs'     => array( 'match' => '#/shop/\?category=hookahs#i', 'new' => us_get_canonical_term_url( 'hookahs' ) ),
		'Accessories' => array( 'match' => '#/shop/\?category=accessories#i', 'new' => us_get_canonical_term_url( 'accessories' ) ),
		'Bulk Orders' => array( 'match' => '#/wholesale/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'wholesale' ) : home_url( '/bulk-orders/' ) ),
	),
	'footer-shop' => array(
		'All products'        => array( 'match' => '#^https?://[^/]+/shop/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'shop' ) : home_url( '/shop/' ) ),
		'Hookahs'             => array( 'match' => '#(/shop/\?category=hookahs|/product-category/hookahs/)#i', 'new' => us_get_canonical_term_url( 'hookahs' ) ),
		'Bowls'               => array( 'match' => '#(/shop/\?category=bowls|/product-category/bowls/)#i', 'new' => us_get_canonical_term_url( 'bowls' ) ),
		'Heat management'     => array( 'match' => '#(/shop/\?category=heat|/product-category/heat/?)#i', 'new' => us_get_canonical_term_url( 'heat-management' ) ),
		'Charcoal'            => array( 'match' => '#(/shop/\?category=charcoal|/product-category/charcoal/)#i', 'new' => us_get_canonical_term_url( 'charcoal' ) ),
		'Hoses & mouthpieces' => array( 'match' => '#(/shop/\?category=hoses|/product-category/hoses/?)#i', 'new' => us_get_canonical_term_url( 'hoses-mouthpieces' ) ),
		'Tools & spares'      => array( 'match' => '#(/shop/\?category=care|/product-category/care/?)#i', 'new' => us_get_canonical_term_url( 'tools-spares' ) ),
		'All accessories'     => array( 'match' => '#(/shop/\?category=accessories|/product-category/accessories/)#i', 'new' => us_get_canonical_term_url( 'accessories' ) ),
	),
	'footer-discover' => array(
		'About Urban Shisha'  => array( 'match' => '#/about/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'about' ) : home_url( '/about/' ) ),
		'Wholesale enquiries' => array( 'match' => '#/wholesale/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'wholesale' ) : home_url( '/bulk-orders/' ) ),
		'Featured hookahs'    => array( 'match' => '#(/shop/\?category=hookahs|/product-category/hookahs/)#i', 'new' => us_get_canonical_term_url( 'hookahs' ) ),
		'Shop by budget'      => array( 'match' => '#^https?://[^/]+/shop/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'shop' ) : home_url( '/shop/' ) ),
		'Your wishlist'       => array( 'match' => '#(/account/\?tab=wishlist|/account/?$)#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) : home_url( '/my-account/?tab=wishlist' ), 'class' => 'open-wishlist' ),
	),
	'footer-care' => array(
		'Contact us'          => array( 'match' => '#/contact/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'contact' ) : home_url( '/contact/' ) ),
		'Shipping & delivery' => array( 'match' => '#/shipping/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'shipping' ) : home_url( '/shipping/' ) ),
		'Returns & refunds'   => array( 'match' => '#/returns/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'returns' ) : home_url( '/returns/' ) ),
		'Track your order'    => array( 'match' => '#/account/\?tab=orders#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) : home_url( '/my-account/orders/' ) ),
	),
	'footer-account' => array(
		'My account'          => array( 'match' => '#^https?://[^/]+/account/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account' ) : home_url( '/my-account/' ) ),
		'My orders'           => array( 'match' => '#/account/\?tab=orders#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) : home_url( '/my-account/orders/' ) ),
		'Saved products'      => array( 'match' => '#(/account/\?tab=wishlist|/account/?$)#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) : home_url( '/my-account/?tab=wishlist' ), 'class' => 'open-wishlist' ),
		'Privacy policy'      => array( 'match' => '#/privacy/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'privacy' ) : home_url( '/privacy-policy/' ) ),
		'Terms & conditions'  => array( 'match' => '#/terms/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'terms' ) : home_url( '/terms-and-conditions/' ) ),
		'18+ policy'          => array( 'match' => '#/age-policy/?$#i', 'new' => function_exists( 'urban_shisha_route_url' ) ? urban_shisha_route_url( 'age-policy' ) : home_url( '/age-policy/' ) ),
	),
);

foreach ( $location_item_corrections as $loc => $items_to_check ) {
	if ( empty( $theme_mods['nav_menu_locations'][ $loc ] ) ) {
		continue;
	}
	$menu_id = (int) $theme_mods['nav_menu_locations'][ $loc ];
	$menu_obj = wp_get_nav_menu_object( $menu_id );
	if ( ! $menu_obj ) {
		continue;
	}
	$menu_items = wp_get_nav_menu_items( $menu_obj );
	if ( empty( $menu_items ) || ! is_array( $menu_items ) ) {
		continue;
	}
	foreach ( $menu_items as $mi ) {
		$title = trim( $mi->title );
		if ( ! isset( $items_to_check[ $title ] ) ) {
			continue;
		}
		$spec = $items_to_check[ $title ];
		$cur_url = (string) $mi->url;
		if ( preg_match( $spec['match'], $cur_url ) ) {
			if ( $cur_url !== $spec['new'] ) {
				update_post_meta( $mi->ID, '_menu_item_url', esc_url_raw( $spec['new'] ) );
				clean_post_cache( $mi->ID );
				WP_CLI::log( "  [Corrected URL] Location '{$loc}' -> Item '{$title}': '{$cur_url}' -> '{$spec['new']}'" );
			}
		}
		if ( ! empty( $spec['class'] ) ) {
			$classes = (array) get_post_meta( $mi->ID, '_menu_item_classes', true );
			if ( ! in_array( $spec['class'], $classes, true ) ) {
				$classes[] = $spec['class'];
				update_post_meta( $mi->ID, '_menu_item_classes', array_values( array_filter( $classes ) ) );
				clean_post_cache( $mi->ID );
				WP_CLI::log( "  [Added class] Location '{$loc}' -> Item '{$title}': added '{$spec['class']}'" );
			}
		}
	}
	wp_cache_delete( $menu_id, 'nav_menu_items' );
}

WP_CLI::success( 'Urban Shisha shell seeding completed successfully.' );

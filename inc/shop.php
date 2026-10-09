<?php
/**
 * Urban Shisha — Shop page data resolution and WooCommerce archive helpers.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get approved canonical shop category definitions.
 *
 * @return array<string, string>
 */
function urban_shisha_get_shop_category_definitions(): array {
	return array(
		'hookahs'  => __( 'Hookahs', 'urban-shisha' ),
		'bowls'    => __( 'Bowls', 'urban-shisha' ),
		'heat'     => __( 'Heat management', 'urban-shisha' ),
		'hoses'    => __( 'Hoses & tips', 'urban-shisha' ),
		'care'     => __( 'Tools & spares', 'urban-shisha' ),
		'charcoal' => __( 'Charcoal', 'urban-shisha' ),
	);
}

/**
 * Retrieve editable SCF/ACF fields for the Shop page with faithful defaults.
 *
 * @return array<string, mixed>
 */
function urban_shisha_get_shop_fields(): array {
	$shop_page_id = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	if ( $shop_page_id <= 0 ) {
		$page = get_page_by_path( 'shop' );
		if ( $page ) {
			$shop_page_id = $page->ID;
		}
	}

	$get_meta = function( string $key, $fallback = '' ) use ( $shop_page_id ) {
		if ( $shop_page_id > 0 ) {
			if ( function_exists( 'get_field' ) ) {
				$val = get_field( $key, $shop_page_id );
				if ( ! empty( $val ) ) {
					return $val;
				}
			}
			$val = get_post_meta( $shop_page_id, $key, true );
			if ( '' !== $val && false !== $val ) {
				return $val;
			}
		}
		// Fallback to options page if set
		if ( function_exists( 'get_field' ) ) {
			$val = get_field( $key, 'option' );
			if ( ! empty( $val ) ) {
				return $val;
			}
		}
		return $fallback;
	};

	$poster_img_id = (int) $get_meta( 'shop_hero_poster_image', 0 );
	$poster_url    = '';
	if ( $poster_img_id > 0 ) {
		$poster_url = wp_get_attachment_image_url( $poster_img_id, 'full' );
	}
	if ( ! $poster_url ) {
		$poster_url = get_theme_file_uri( 'assets/images/product-cutout-1.webp' );
	}

	$builder_url = home_url( '/#builder' );

	return array(
		'hero_eyebrow'           => (string) $get_meta( 'shop_hero_eyebrow', 'THE WHOLE SETUP. ALL IN ONE PLACE.' ),
		'hero_heading'           => (string) $get_meta( 'shop_hero_heading', "GOOD TASTE.<br><span>GREAT GEAR.</span>" ),
		'hero_description'       => (string) $get_meta( 'shop_hero_description', 'Statement hookahs. The right extras. Find your kind of setup.' ),
		'hero_poster_word'       => (string) $get_meta( 'shop_hero_poster_word', "THE<br>EDIT" ),
		'hero_poster_sticker'    => (string) $get_meta( 'shop_hero_poster_sticker', "18+<br>ONLY" ),
		'hero_poster_image_url'  => $poster_url,
		'filter_heading'         => (string) $get_meta( 'shop_filter_heading', __( 'Refine your edit.', 'urban-shisha' ) ),
		'filter_help_eyebrow'    => (string) $get_meta( 'shop_filter_help_eyebrow', __( 'NEED A LITTLE DIRECTION?', 'urban-shisha' ) ),
		'filter_help_text'       => (string) $get_meta( 'shop_filter_help_text', __( 'Build your setup', 'urban-shisha' ) ),
		'filter_help_url'        => (string) $get_meta( 'shop_filter_help_url', $builder_url ),
		'empty_eyebrow'          => (string) $get_meta( 'shop_empty_eyebrow', __( 'LET’S FIND YOUR NEXT PIECE.', 'urban-shisha' ) ),
		'empty_heading'          => (string) $get_meta( 'shop_empty_heading', __( 'A different edit?', 'urban-shisha' ) ),
		'empty_description'      => (string) $get_meta( 'shop_empty_description', __( 'No products match these filters. Try widening your search.', 'urban-shisha' ) ),
		'build_band_eyebrow'     => (string) $get_meta( 'shop_build_band_eyebrow', 'GOOD TOGETHER.' ),
		'build_band_heading'     => (string) $get_meta( 'shop_build_band_heading', "MAKE THE<br>WHOLE THING YOURS." ),
		'build_band_button_text' => (string) $get_meta( 'shop_build_band_button_text', __( 'Build your setup', 'urban-shisha' ) ),
		'build_band_button_url'  => (string) $get_meta( 'shop_build_band_button_url', $builder_url ),
		'end_note_eyebrow'       => (string) $get_meta( 'shop_end_note_eyebrow', 'YOU’VE SEEN THE WHOLE EDIT.' ),
		'end_note_text'          => (string) $get_meta( 'shop_end_note_text', sprintf( __( 'Still deciding? <a href="%s">Build a setup that feels like you.</a>', 'urban-shisha' ), esc_url( $builder_url ) ) ),
	);
}

/**
 * Retrieve active categories and their real product counts.
 *
 * @return array<string, array{name: string, count: int, slug: string}>
 */
function urban_shisha_get_shop_categories(): array {
	$can_preview = current_user_can( 'edit_products' );
	$status      = $can_preview ? array( 'publish', 'draft' ) : 'publish';
	$defs        = urban_shisha_get_shop_category_definitions();
	$categories  = array();

	foreach ( $defs as $slug => $label ) {
		// Map aliases like heat-management -> heat, care -> accessories
		$search_slugs = array( $slug );
		if ( 'heat' === $slug ) {
			$search_slugs[] = 'heat-management';
		} elseif ( 'care' === $slug ) {
			$search_slugs[] = 'accessories';
		}

		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => $status,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $search_slugs,
					),
				),
			)
		);

		$categories[ $slug ] = array(
			'slug'  => $slug,
			'name'  => $label,
			'count' => $query->found_posts,
		);
	}

	return $categories;
}

/**
 * Retrieve active brands from product_brand taxonomy and their real product counts.
 *
 * @return array<int, array{id: string, name: string, count: int}>
 */
function urban_shisha_get_shop_brands(): array {
	$can_preview = current_user_can( 'edit_products' );
	$status      = $can_preview ? array( 'publish', 'draft' ) : 'publish';
	$brands      = array();

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_brand',
			'hide_empty' => false,
		)
	);

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$query = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => $status,
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_brand',
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						),
					),
				)
			);

			$brands[] = array(
				'id'    => $term->slug,
				'name'  => $term->name,
				'count' => $query->found_posts,
			);
		}
	}

	// Fallback reference brands if taxonomy empty
	if ( empty( $brands ) ) {
		$fallback_brands = array(
			'COCOYAYA'    => 'COCOYAYA',
			'Dark Knight' => 'Dark Knight',
			'NaGrani'     => 'NaGrani',
			'VG'          => 'VG',
			'al-fakher'   => 'Al Fakher',
			'nakhla'      => 'Nakhla',
			'serbetli'    => 'Serbetli',
			'revoshi'     => 'Revoshi',
			'jibiar'      => 'Jibiar',
			'musthave'    => 'MustHave',
		);
		foreach ( $fallback_brands as $slug => $name ) {
			$brands[] = array(
				'id'    => $slug,
				'name'  => $name,
				'count' => 0,
			);
		}
	}

	return $brands;
}

/**
 * Build sanitized query filter parameters from request or custom arguments.
 *
 * @param array<string, mixed> $input Raw input parameters.
 * @return array<string, mixed>
 */
function urban_shisha_sanitize_shop_params( array $input = array() ): array {
	$src = ! empty( $input ) ? $input : $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$category = array();
	if ( ! empty( $src['category'] ) ) {
		$cats = is_array( $src['category'] ) ? $src['category'] : explode( ',', (string) $src['category'] );
		$category = array_values( array_filter( array_map( 'sanitize_title', $cats ) ) );
	} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$queried = get_queried_object();
		if ( $queried instanceof WP_Term ) {
			$category = array( $queried->slug );
		}
	}

	$brand = array();
	if ( ! empty( $src['brand'] ) ) {
		$brs = is_array( $src['brand'] ) ? $src['brand'] : explode( ',', (string) $src['brand'] );
		$brand = array_values( array_filter( array_map( 'sanitize_text_field', $brs ) ) );
	} elseif ( function_exists( 'is_tax' ) && is_tax( 'product_brand' ) ) {
		$queried = get_queried_object();
		if ( $queried instanceof WP_Term ) {
			$brand = array( $queried->slug );
		}
	}

	$raw_min = $src['min_price'] ?? ( $src['min'] ?? null );
	$raw_max = $src['max_price'] ?? ( $src['max'] ?? null );

	$min = ( null !== $raw_min && '' !== (string) $raw_min ) ? max( 0, (int) $raw_min ) : 0;
	$max = ( null !== $raw_max && '' !== (string) $raw_max ) ? max( $min, (int) $raw_max ) : null;

	$q = isset( $src['q'] ) ? sanitize_text_field( mb_substr( (string) $src['q'], 0, 100 ) ) : '';

	$allowed_sorts = array( 'featured', 'price-low', 'price-high', 'name', 'date' );
	$sort = isset( $src['sort'] ) && in_array( $src['sort'], $allowed_sorts, true ) ? $src['sort'] : 'featured';

	$is_new = ! empty( $src['new'] ) && ( '1' === (string) $src['new'] || true === $src['new'] );
	$paged  = isset( $src['paged'] ) ? max( 1, (int) $src['paged'] ) : 1;

	return array(
		'category' => $category,
		'brand'    => $brand,
		'min'      => $min,
		'max'      => $max,
		'q'        => $q,
		'sort'     => $sort,
		'new'      => $is_new,
		'paged'    => $paged,
	);
}

/**
 * Execute server-side WooCommerce query for shop archive.
 *
 * @param array<string, mixed> $params Sanitized filter parameters.
 * @return array{products: array<int, array<string, mixed>>, total: int, max_num_pages: int}
 */
function urban_shisha_get_shop_products( array $params = array() ): array {
	$can_preview = current_user_can( 'edit_products' );
	$status      = $can_preview ? array( 'publish', 'draft' ) : 'publish';

	$tax_query  = array();
	$meta_query = array();

	// Category taxonomy query
	if ( ! empty( $params['category'] ) ) {
		$cats = (array) $params['category'];
		if ( in_array( 'accessories', $cats, true ) ) {
			$cats = array_merge( $cats, array( 'bowls', 'heat', 'heat-management', 'hoses', 'hoses-mouthpieces', 'care', 'tools-spares', 'tongs', 'charcoal', 'coal-burners', 'hookah-bags' ) );
		}
		if ( in_array( 'heat', $cats, true ) ) {
			$cats[] = 'heat-management';
		}
		if ( in_array( 'hoses', $cats, true ) ) {
			$cats[] = 'hoses-mouthpieces';
		}
		if ( in_array( 'care', $cats, true ) ) {
			$cats[] = 'tools-spares';
			$cats[] = 'tongs';
		}
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => array_unique( $cats ),
		);
	}

	// Brand taxonomy query
	if ( ! empty( $params['brand'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_brand',
			'field'    => 'slug',
			'terms'    => (array) $params['brand'],
		);
	}

	// Price meta query with support for closed and open-ended ranges
	$min_price = isset( $params['min'] ) ? (int) $params['min'] : 0;
	$max_price = isset( $params['max'] ) && null !== $params['max'] ? (int) $params['max'] : null;

	if ( $min_price > 0 && null !== $max_price ) {
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => array( $min_price, $max_price ),
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		);
	} elseif ( $min_price > 0 && null === $max_price ) {
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => $min_price,
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	} elseif ( $min_price === 0 && null !== $max_price && $max_price < 20000 ) {
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => $max_price,
			'type'    => 'NUMERIC',
			'compare' => '<=',
		);
	}

	// "New" badge filter
	if ( ! empty( $params['new'] ) ) {
		$meta_query[] = array(
			'key'     => '_us_badge_new',
			'value'   => 'yes',
			'compare' => '=',
		);
	}

	// Sorting
	$orderby = 'date';
	$order   = 'DESC';
	if ( 'price-low' === ( $params['sort'] ?? '' ) ) {
		$orderby = 'meta_value_num';
		$order   = 'ASC';
	} elseif ( 'price-high' === ( $params['sort'] ?? '' ) ) {
		$orderby = 'meta_value_num';
		$order   = 'DESC';
	} elseif ( 'name' === ( $params['sort'] ?? '' ) ) {
		$orderby = 'title';
		$order   = 'ASC';
	}

	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => $status,
		'posts_per_page' => 48,
		'paged'          => $params['paged'] ?? 1,
		'orderby'        => $orderby,
		'order'          => $order,
		'fields'         => 'ids',
	);

	if ( 'meta_value_num' === $orderby ) {
		$query_args['meta_key'] = '_price';
	}

	if ( ! empty( $params['q'] ) ) {
		$query_args['s'] = $params['q'];
	}

	if ( ! empty( $tax_query ) ) {
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		$query_args['tax_query'] = $tax_query;
	}

	if ( ! empty( $meta_query ) ) {
		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = 'AND';
		}
		$query_args['meta_query'] = $meta_query;
	}

	$query    = new WP_Query( $query_args );
	$products = array();

	if ( ! empty( $query->posts ) ) {
		foreach ( $query->posts as $post_id ) {
			$data = urban_shisha_get_product_data( $post_id );
			if ( $data ) {
				$products[] = $data;
			}
		}
	}

	return array(
		'products'      => $products,
		'total'         => $query->found_posts,
		'max_num_pages' => $query->max_num_pages,
	);
}

/**
 * Compile localized JSON configuration for shop-wordpress.js.
 *
 * @return array<string, mixed>
 */
function urban_shisha_get_shop_client_config(): array {
	$can_preview = current_user_can( 'edit_products' );
	$status      = $can_preview ? array( 'publish', 'draft' ) : 'publish';

	// Query catalog products for high-speed client-side filtering while respecting draft access
	$all_ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => $status,
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$products_data = array();
	foreach ( $all_ids as $id ) {
		$data = urban_shisha_get_product_data( $id );
		if ( $data ) {
			// Flatten essential search fields for instant matching
			$data['type_slug'] = $data['category_slug'] ?: 'hookahs';
			$products_data[]   = $data;
		}
	}

	$categories = urban_shisha_get_shop_categories();
	$brands     = urban_shisha_get_shop_brands();
	$params     = urban_shisha_sanitize_shop_params();

	return array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'cartNonce'    => wp_create_nonce( 'urban_shisha_cart_nonce' ),
		'shopUrl'      => urban_shisha_route_url( 'shop' ),
		'isEditor'     => $can_preview,
		'products'     => $products_data,
		'categories'   => $categories,
		'brands'       => $brands,
		'initialState' => $params,
		'priceMin'     => 0,
		'priceMax'     => 20000,
	);
}

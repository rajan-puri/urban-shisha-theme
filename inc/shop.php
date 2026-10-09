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
 * Map canonical filter slugs and URL aliases to real taxonomy terms in the database.
 *
 * @param array<int|string, string> $slugs Array of category slugs.
 * @return array<int, string>
 */
function urban_shisha_map_category_slugs( array $slugs ): array {
	$mapped = array();
	foreach ( $slugs as $s ) {
		$clean = sanitize_title( (string) $s );
		switch ( $clean ) {
			case 'hookahs':
				$mapped = array_merge( $mapped, array( 'hookahs', 'portable-hookahs' ) );
				break;
			case 'bowls':
				$mapped[] = 'bowls';
				break;
			case 'heat':
			case 'heat-management':
				$mapped = array_merge( $mapped, array( 'heat', 'heat-management' ) );
				break;
			case 'hoses':
			case 'hoses-mouthpieces':
				$mapped = array_merge( $mapped, array( 'hoses', 'hoses-mouthpieces' ) );
				break;
			case 'care':
			case 'tools-spares':
			case 'tongs':
				$mapped = array_merge( $mapped, array( 'tools-spares', 'tongs' ) );
				break;
			case 'charcoal':
				$mapped = array_merge( $mapped, array( 'charcoal', 'coal-burners' ) );
				break;
			case 'accessories':
				$mapped = array_merge( $mapped, array( 'accessories', 'bowls', 'heat', 'heat-management', 'hoses', 'hoses-mouthpieces', 'tools-spares', 'tongs', 'charcoal', 'coal-burners', 'hookah-bags' ) );
				break;
			default:
				if ( '' !== $clean ) {
					$mapped[] = $clean;
				}
				break;
		}
	}
	return array_values( array_unique( $mapped ) );
}

/**
 * Dynamically calculate eligible catalogue price bounds (min and ceiling max).
 *
 * @return array{min: float, max: float}
 */
function urban_shisha_get_shop_price_bounds(): array {
	$can_preview = current_user_can( 'edit_products' );
    static $cache = array();
    $cache_key = get_current_user_id() . ':' . (int) $can_preview;
    if ( isset( $cache[$cache_key] ) ) { return $cache[$cache_key]; }
	$status      = $can_preview ? array( 'publish', 'draft' ) : 'publish';

	$all_ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => $status,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	$found_max = 0.0;
	$found_min = PHP_FLOAT_MAX;

	if ( ! empty( $all_ids ) ) {
		foreach ( $all_ids as $pid ) {
			$product = wc_get_product( $pid );
			if ( $product ) {
				$price = $product->get_price();
				if ( '' !== $price && false !== $price ) {
					$val = (float) $price;
                    $upper = $product->is_type( 'variable' ) ? (float) $product->get_variation_price( 'max' ) : $val;
					if ( $upper > $found_max ) {
						$found_max = $upper;
					}
					if ( $val < $found_min ) {
						$found_min = $val;
					}
				}
			}
		}
	}

	$max_ceiling = ( $found_max > 0 ) ? (float) ( ceil( $found_max / 1000.0 ) * 1000 ) : 30000.0;
	$min_floor   = 0.0;

	return $cache[$cache_key] = array(
		'min' => $min_floor,
		'max' => max( 1000.0, $max_ceiling ),
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
        if ( $shop_page_id > 0 && metadata_exists( 'post', $shop_page_id, $key ) ) {
            return function_exists( 'get_field' ) ? get_field( $key, $shop_page_id ) : get_post_meta( $shop_page_id, $key, true );
        }
        return $fallback;
    };

	$poster_img_id = (int) $get_meta( 'shop_hero_poster_image', 0 );
	$poster_url    = '';
	if ( $poster_img_id > 0 ) {
		$poster_url = wp_get_attachment_image_url( $poster_img_id, 'full' );
	}
	if ( ! $poster_url && ! metadata_exists( 'post', $shop_page_id, 'shop_hero_poster_image' ) ) {
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
		$search_slugs = urban_shisha_map_category_slugs( array( $slug ) );

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
						'operator' => 'IN',
					),
				),
			)
		);

		$categories[ $slug ] = array(
			'slug'  => $slug,
			'name'  => $label,
			'count' => (int) $query->found_posts,
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
				'count' => (int) $query->found_posts,
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
	$src = wp_unslash( ! empty( $input ) ? $input : $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$category = array();
	if ( ! empty( $src['category'] ) ) {
		$cats     = is_array( $src['category'] ) ? $src['category'] : explode( ',', (string) $src['category'] );
		$category = array_values( array_filter( array_map( 'sanitize_title', array_filter( $cats, 'is_scalar' ) ) ) );
	} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$queried = get_queried_object();
		if ( $queried instanceof WP_Term ) {
			$category = array( $queried->slug );
		}
	}

	$brand = array();
	if ( ! empty( $src['brand'] ) ) {
		$brs   = is_array( $src['brand'] ) ? $src['brand'] : explode( ',', (string) $src['brand'] );
		$brand = array_values( array_filter( array_map( 'sanitize_title', array_filter( $brs, 'is_scalar' ) ) ) );
	} elseif ( function_exists( 'is_tax' ) && is_tax( 'product_brand' ) ) {
		$queried = get_queried_object();
		if ( $queried instanceof WP_Term ) {
			$brand = array( $queried->slug );
		}
	}

	$bounds      = urban_shisha_get_shop_price_bounds();
	$catalog_max = $bounds['max'];

	$raw_min = $src['min_price'] ?? ( $src['min'] ?? null );
	$raw_max = $src['max_price'] ?? ( $src['max'] ?? null );

	$min = ( is_scalar( $raw_min ) && is_numeric( $raw_min ) && is_finite( (float) $raw_min ) ) ? max( 0.0, (float) $raw_min ) : 0.0;
	$max = ( is_scalar( $raw_max ) && is_numeric( $raw_max ) && is_finite( (float) $raw_max ) ) ? max( 0.0, (float) $raw_max ) : null;

	// Inverted bounds validation: swap min and max if min > max
	if ( null !== $max && $min > $max ) {
		$temp = $min;
		$min  = $max;
		$max  = $temp;
	}

	$q = isset( $src['q'] ) && is_scalar( $src['q'] ) ? sanitize_text_field( mb_substr( (string) $src['q'], 0, 100 ) ) : '';

	$allowed_sorts = array( 'featured', 'price-low', 'price-high', 'name', 'date' );
	$sort          = isset( $src['sort'] ) && in_array( $src['sort'], $allowed_sorts, true ) ? $src['sort'] : 'featured';

	$is_new = ! empty( $src['new'] ) && ( '1' === (string) $src['new'] || true === $src['new'] );
	$paged  = isset( $src['paged'] ) && is_scalar( $src['paged'] ) ? max( 1, (int) $src['paged'] ) : max( 1, (int) get_query_var( 'paged' ) );
    $context = urban_shisha_shop_archive_context();
    foreach ( array( 'archive_category', 'archive_brand' ) as $key ) {
        if ( isset( $src[$key] ) && is_scalar( $src[$key] ) ) { $context[$key] = sanitize_title( $src[$key] ); }
    }

	return array(
		'category' => $category,
		'brand'    => $brand,
		'min'      => $min,
		'max'      => $max,
		'q'        => $q,
		'sort'     => $sort,
		'new'      => $is_new,
		'paged'    => $paged,
        'archive_category' => $context['archive_category'],
        'archive_brand' => $context['archive_brand'],
	);
}

/**
 * Execute server-side WooCommerce query for shop archive.
 *
 * @param array<string, mixed> $params Sanitized filter parameters.
 * @return array{products: array<int, array<string, mixed>>, total: int, max_num_pages: int, paged: int}
 */
function urban_shisha_get_shop_products( array $params = array() ): array {
	$can_preview = current_user_can( 'edit_products' );
	$status      = $can_preview ? array( 'publish', 'draft' ) : 'publish';

	$tax_query  = array();
	$meta_query = array();

	// Category taxonomy query (OR within selected categories)
	if ( ! empty( $params['category'] ) ) {
		$target_slugs = urban_shisha_map_category_slugs( (array) $params['category'] );
		$tax_query[]  = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $target_slugs,
			'operator' => 'IN',
		);
	}

	// Brand taxonomy query (OR within selected brands)
	if ( ! empty( $params['brand'] ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_brand',
			'field'    => 'slug',
			'terms'    => (array) $params['brand'],
			'operator' => 'IN',
		);
	}

    foreach ( array( 'archive_category' => 'product_cat', 'archive_brand' => 'product_brand' ) as $key => $taxonomy ) {
        if ( ! empty( $params[$key] ) ) { $tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $params[$key] ); }
    }
    if ( ! $can_preview ) {
        $visibility = wc_get_product_visibility_term_ids();
        $excluded = array( $visibility['exclude-from-catalog'] );
        if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) { $excluded[] = $visibility['outofstock']; }
        $tax_query[] = array( 'taxonomy' => 'product_visibility', 'field' => 'term_taxonomy_id', 'terms' => $excluded, 'operator' => 'NOT IN' );
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
	} elseif ( 'featured' === ( $params['sort'] ?? '' ) ) {
		$orderby = 'menu_order date';
		$order   = 'ASC';
	}

	$current_page = max( 1, (int) ( $params['paged'] ?? 1 ) );

	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => $status,
		'posts_per_page' => 24,
		'paged'          => $current_page,
		'orderby'        => $orderby,
		'order'          => $order,
		'fields'         => 'ids',
        'urban_shisha_shop_params' => $params,
	);


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
    if ( $query->max_num_pages > 0 && $current_page > $query->max_num_pages ) {
        $current_page = (int) $query->max_num_pages; $query_args['paged'] = $current_page; $query = new WP_Query( $query_args );
    }
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
		'total'         => (int) $query->found_posts,
		'max_num_pages' => (int) $query->max_num_pages,
		'paged'         => $current_page,
	);
}

/**
 * Render accessible numbered pagination matching approved tokens.
 *
 * @param int                  $current_page Current page number.
 * @param int                  $max_pages    Total pages count.
 * @param array<string, mixed> $params       Current active filters.
 * @return string
 */
function urban_shisha_render_shop_pagination( int $current_page, int $max_pages, array $params = array() ): string {
	if ( $max_pages <= 1 ) {
		return '';
	}

	$current_page = max( 1, min( $current_page, $max_pages ) );
	$base_url     = urban_shisha_route_url( 'shop' );

	$build_url = function( int $page ) use ( $base_url, $params ) {
		$args = array();
		if ( ! empty( $params['category'] ) ) {
			$args['category'] = implode( ',', (array) $params['category'] );
		}
		if ( ! empty( $params['brand'] ) ) {
			$args['brand'] = implode( ',', (array) $params['brand'] );
		}
		if ( ! empty( $params['min'] ) && (float) $params['min'] > 0 ) {
			$args['min_price'] = $params['min'];
		}
		if ( isset( $params['max'] ) && null !== $params['max'] && '' !== (string) $params['max'] ) {
			$args['max_price'] = $params['max'];
		}
		if ( ! empty( $params['q'] ) ) {
			$args['q'] = $params['q'];
		}
		if ( ! empty( $params['sort'] ) && 'featured' !== $params['sort'] ) {
			$args['sort'] = $params['sort'];
		}
		if ( ! empty( $params['new'] ) ) {
			$args['new'] = '1';
		}
		if ( $page > 1 ) {
			$args['paged'] = $page;
		}
		return ! empty( $args ) ? add_query_arg( $args, $base_url ) : $base_url;
	};

	$html = '<nav class="shop-pagination" id="shop-pagination" aria-label="' . esc_attr__( 'Shop pagination', 'urban-shisha' ) . '">';

	// Previous button
	if ( $current_page > 1 ) {
		$prev_page = $current_page - 1;
		$html     .= sprintf(
			'<a href="%s" class="pagination-btn pagination-prev" data-page="%d" aria-label="%s">&larr; %s</a>',
			esc_url( $build_url( $prev_page ) ),
			$prev_page,
			esc_attr__( 'Previous page', 'urban-shisha' ),
			esc_html__( 'Previous', 'urban-shisha' )
		);
	} else {
		$html .= sprintf(
			'<button type="button" class="pagination-btn pagination-prev" disabled aria-disabled="true" aria-label="%s">&larr; %s</button>',
			esc_attr__( 'Previous page', 'urban-shisha' ),
			esc_html__( 'Previous', 'urban-shisha' )
		);
	}

	$html .= '<span class="pagination-pages">';

	// Page numbers logic: show pages with ellipses if > 7 pages
	$links = array();
	if ( $max_pages <= 7 ) {
		$links = range( 1, $max_pages );
	} else {
		$links[] = 1;
		$start   = max( 2, $current_page - 1 );
		$end     = min( $max_pages - 1, $current_page + 1 );
		if ( $start > 2 ) {
			$links[] = '...';
		}
		for ( $i = $start; $i <= $end; $i++ ) {
			$links[] = $i;
		}
		if ( $end < $max_pages - 1 ) {
			$links[] = '...';
		}
		$links[] = $max_pages;
	}

	foreach ( $links as $link ) {
		if ( '...' === $link ) {
			$html .= '<span class="pagination-ellipsis" aria-hidden="true">&hellip;</span>';
		} elseif ( $link === $current_page ) {
			$html .= sprintf(
				'<span class="pagination-page is-active" aria-current="page" data-page="%d">%d</span>',
				$link,
				$link
			);
		} else {
			$html .= sprintf(
				'<a href="%s" class="pagination-page" data-page="%d" aria-label="%s">%d</a>',
				esc_url( $build_url( (int) $link ) ),
				(int) $link,
				esc_attr( sprintf( __( 'Page %d', 'urban-shisha' ), $link ) ),
				(int) $link
			);
		}
	}

	$html .= '</span>';

	// Next button
	if ( $current_page < $max_pages ) {
		$next_page = $current_page + 1;
		$html     .= sprintf(
			'<a href="%s" class="pagination-btn pagination-next" data-page="%d" aria-label="%s">%s &rarr;</a>',
			esc_url( $build_url( $next_page ) ),
			$next_page,
			esc_attr__( 'Next page', 'urban-shisha' ),
			esc_html__( 'Next', 'urban-shisha' )
		);
	} else {
		$html .= sprintf(
			'<button type="button" class="pagination-btn pagination-next" disabled aria-disabled="true" aria-label="%s">%s &rarr;</button>',
			esc_attr__( 'Next page', 'urban-shisha' ),
			esc_html__( 'Next', 'urban-shisha' )
		);
	}

	$html .= '</nav>';

	return $html;
}

/**
 * Format accurate count and range description.
 *
 * @param int $total    Total matching products count.
 * @param int $paged    Current active page.
 * @param int $per_page Products per page.
 * @return string
 */
function urban_shisha_get_shop_result_count_text( int $total, int $paged, int $per_page = 24 ): string {
	if ( 0 === $total ) {
		return __( '0 products in your edit', 'urban-shisha' );
	}
	$start = ( $paged - 1 ) * $per_page + 1;
	$end   = min( $total, $paged * $per_page );
	return sprintf(
		/* translators: 1: start product number, 2: end product number, 3: total products */
		esc_html__( 'Showing %1$d–%2$d of %3$d products', 'urban-shisha' ),
		$start,
		$end,
		$total
	);
}

/**
 * Compile localized JSON configuration for shop-wordpress.js.
 *
 * @return array<string, mixed>
 */
function urban_shisha_get_shop_client_config(): array {
	$can_preview = current_user_can( 'edit_products' );
	$categories  = urban_shisha_get_shop_categories();
	$brands      = urban_shisha_get_shop_brands();
	$params      = urban_shisha_sanitize_shop_params();
	$bounds      = urban_shisha_get_shop_price_bounds();

	return array(
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'nonce'        => wp_create_nonce( 'urban_shisha_shop_nonce' ),
		'cartNonce'    => wp_create_nonce( 'urban_shisha_cart_nonce' ),
		'shopUrl'      => urban_shisha_route_url( 'shop' ),
		'isEditor'     => $can_preview,
		'categories'   => $categories,
		'brands'       => $brands,
		'initialState' => $params,
        'archiveContext' => urban_shisha_shop_archive_context(),
		'priceMin'     => $bounds['min'],
		'priceMax'     => $bounds['max'],
	);
}

/**
 * AJAX endpoint for coherent server-backed Shop filtering and pagination.
 */
function urban_shisha_ajax_shop_filter(): void {
	// Read-only catalogue requests use the same permission checks as archive rendering.
	// Optional shop nonce check with graceful fallback
	$nonce = $_REQUEST['nonce'] ?? '';
	if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'urban_shisha_shop_nonce' ) ) {
		// allow public read filter even if expired
	}

	$params = urban_shisha_sanitize_shop_params( $_REQUEST );
	$result = urban_shisha_get_shop_products( $params );
	$total  = $result['total'];
	$pages  = $result['max_num_pages'];
	$paged  = $result['paged'];

	ob_start();
	if ( ! empty( $result['products'] ) ) {
		foreach ( $result['products'] as $item ) {
			get_template_part( 'template-parts/product-card', null, array( 'product_data' => $item ) );
		}
	}
	$products_html = ob_get_clean();

	$pagination_html = urban_shisha_render_shop_pagination( $paged, $pages, $params );
	$count_text      = urban_shisha_get_shop_result_count_text( $total, $paged, 24 );

	$title = __( 'All products', 'urban-shisha' );
    foreach ( array( 'archive_category' => 'product_cat', 'archive_brand' => 'product_brand' ) as $key => $taxonomy ) {
        if ( ! empty( $params[$key] ) ) { $term = get_term_by( 'slug', $params[$key], $taxonomy ); if ( $term ) { $title = $term->name; } }
    }
	if ( 1 === count( $params['category'] ) ) {
		$defs = urban_shisha_get_shop_category_definitions();
		$cat  = $params['category'][0];
		if ( isset( $defs[ $cat ] ) ) {
			$title = $defs[ $cat ];
		}
	} elseif ( 1 === count( $params['brand'] ) ) {
		$brand_term = get_term_by( 'slug', $params['brand'][0], 'product_brand' );
		if ( $brand_term ) {
			$title = $brand_term->name;
		}
	}

	wp_send_json_success(
		array(
			'html'            => $products_html,
			'pagination_html' => $pagination_html,
			'count_text'      => $count_text,
			'title'           => $title,
			'total'           => $total,
			'max_num_pages'   => $pages,
			'paged'           => $paged,
			'empty'           => ( 0 === $total ),
		)
	);
}
add_action( 'wp_ajax_urban_shisha_shop_filter', 'urban_shisha_ajax_shop_filter' );
add_action( 'wp_ajax_nopriv_urban_shisha_shop_filter', 'urban_shisha_ajax_shop_filter' );

/** Keep taxonomy archives scoped during AJAX, reload and browser navigation. */
function urban_shisha_shop_archive_context(): array {
    $context = array( 'archive_category' => '', 'archive_brand' => '' );
    $term = get_queried_object();
    if ( $term instanceof WP_Term && in_array( $term->taxonomy, array( 'product_cat', 'product_brand' ), true ) ) {
        $context[ 'product_cat' === $term->taxonomy ? 'archive_category' : 'archive_brand' ] = $term->slug;
    }
    return $context;
}

/** Price intervals and ordering use WooCommerce's parent/variation lookup data. */
add_filter( 'posts_clauses', function ( $clauses, $query ) {
    $params = $query->get( 'urban_shisha_shop_params' );
    if ( ! is_array( $params ) ) { return $clauses; }
    global $wpdb;
    $lookup = $wpdb->prefix . 'wc_product_meta_lookup';
    $clauses['join'] .= " LEFT JOIN {$lookup} us_shop_price ON {$wpdb->posts}.ID = us_shop_price.product_id ";
    $min = max( 0, (float) ( $params['min'] ?? 0 ) );
    $max = isset( $params['max'] ) ? max( $min, (float) $params['max'] ) : null;
    if ( $min > 0 || null !== $max ) { $clauses['where'] .= " AND EXISTS (SELECT 1 FROM {$wpdb->postmeta} us_priced WHERE us_priced.post_id = {$wpdb->posts}.ID AND us_priced.meta_key = '_price' AND us_priced.meta_value <> '')"; }
    if ( $min > 0 ) { $clauses['where'] .= $wpdb->prepare( ' AND us_shop_price.max_price >= %f', $min ); }
    if ( null !== $max ) { $clauses['where'] .= $wpdb->prepare( ' AND us_shop_price.min_price <= %f', $max ); }
    $sort = $params['sort'] ?? 'featured';
    if ( in_array( $sort, array( 'price-low', 'price-high' ), true ) ) {
        $field = 'price-high' === $sort ? 'max_price' : 'min_price';
        $order = 'price-high' === $sort ? 'DESC' : 'ASC';
        $clauses['orderby'] = "us_shop_price.{$field} IS NULL ASC, us_shop_price.{$field} {$order}, {$wpdb->posts}.ID DESC";
    } elseif ( 'featured' === $sort ) {
        $featured = get_term_by( 'slug', 'featured', 'product_visibility' );
        $id = $featured ? (int) $featured->term_taxonomy_id : 0;
        $clauses['orderby'] = "EXISTS (SELECT 1 FROM {$wpdb->term_relationships} us_featured WHERE us_featured.object_id = {$wpdb->posts}.ID AND us_featured.term_taxonomy_id = {$id}) DESC, {$wpdb->posts}.menu_order ASC, {$wpdb->posts}.post_date DESC, {$wpdb->posts}.ID DESC";
    }
    return $clauses;
}, 20, 2 );

add_filter( 'posts_search', function ( $search, $query ) {
    $params = $query->get( 'urban_shisha_shop_params' );
    if ( ! is_array( $params ) || empty( $params['q'] ) ) { return $search; }
    global $wpdb;
    $like = '%' . $wpdb->esc_like( $params['q'] ) . '%';
    return $wpdb->prepare( " AND ({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR EXISTS (SELECT 1 FROM {$wpdb->postmeta} us_sku WHERE us_sku.post_id = {$wpdb->posts}.ID AND us_sku.meta_key = '_sku' AND us_sku.meta_value LIKE %s) OR EXISTS (SELECT 1 FROM {$wpdb->term_relationships} us_rel INNER JOIN {$wpdb->term_taxonomy} us_tax ON us_tax.term_taxonomy_id = us_rel.term_taxonomy_id INNER JOIN {$wpdb->terms} us_term ON us_term.term_id = us_tax.term_id WHERE us_rel.object_id = {$wpdb->posts}.ID AND us_tax.taxonomy = 'product_brand' AND us_term.name LIKE %s)) ", $like, $like, $like, $like, $like );
}, 20, 2 );

// The theme paginates its own filtered query; WP's published-only main query must not
// turn a valid draft-preview/filtered archive page into a 404 before rendering it.
add_filter( 'pre_handle_404', function ( $handled, $query ) {
    if ( ! is_admin() && ( is_shop() || is_product_taxonomy() ) ) { return true; }
    return $handled;
}, 10, 2 );
add_filter( 'redirect_canonical', function ( $redirect ) {
    if ( ( is_shop() || is_product_taxonomy() ) && get_query_var( 'paged' ) > 1 ) { return false; }
    return $redirect;
} );

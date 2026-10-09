<?php
/** Homepage field resolution and shared photograph rendering. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_render_home_sections(): void {
	$page_id = (int) get_option( 'page_on_front' );
	$sections = function_exists( 'get_field' ) ? get_field( 'home_sections', $page_id ) : array();
	$templates = array( 'hero', 'categories', 'brands', 'moods', 'spotlight', 'budgets', 'builder', 'editorial', 'guides', 'closing' );
	foreach ( (array) $sections as $section ) {
		if ( ! is_array( $section ) ) { continue; }
		$layout = $section['acf_fc_layout'] ?? '';
		if ( empty( $section[ 'home_' . $layout . '_enabled' ] ) ) { continue; }
		if ( in_array( $layout, array( 'hookah_rail', 'kit', 'accessories', 'arrivals' ), true ) ) {
			$section['layout'] = $layout;
			get_template_part( 'template-parts/home/products', null, $section );
		} elseif ( in_array( $layout, $templates, true ) ) {
			get_template_part( 'template-parts/home/' . $layout, null, $section );
		}
	}
}

/** Frames are only applied to the verified original cutouts, never arbitrary media. */
function urban_shisha_render_media_photo( int $attachment_id, string $extra_class = '' ): string {
	$src = wp_get_attachment_image_src( $attachment_id, 'full' );
	if ( ! $src ) { return ''; }
	$index = get_post_meta( $attachment_id, '_us_cutout_index', true );
	$frames = urban_shisha_get_cutout_frames();
	$frame = '' !== $index && isset( $frames[ (int) $index ] ) ? $frames[ (int) $index ] : array( 0, 0, $src[1], $src[2], $src[1], $src[2] );
	if ( 'hero-photo' === $extra_class && '0' === (string) $index ) { $frame = array( 245, 0, 795, 1254, 1254, 1254 ); }
	return sprintf( '<svg class="catalog-photo %s" viewBox="%d %d %d %d" preserveAspectRatio="xMidYMid meet" aria-hidden="true"><image href="%s" width="%d" height="%d"/></svg>', esc_attr( $extra_class ), $frame[0], $frame[1], $frame[2], $frame[3], esc_url( $src[0] ), $frame[4], $frame[5] );
}

function urban_shisha_home_art( int $attachment_id, string $label = '', string $class = '', string $tag = 'span' ): string {
	$tag = in_array( $tag, array( 'div', 'span' ), true ) ? $tag : 'span';
	$index = get_post_meta( $attachment_id, '_us_cutout_index', true );
	$art = '' !== $index ? ' art-' . (int) $index : '';
	return sprintf( '<%1$s class="product-art has-cutout%2$s %3$s" role="img" aria-label="%4$s">%5$s</%1$s>', $tag, esc_attr( $art ), esc_attr( $class ), esc_attr( $label ), urban_shisha_render_media_photo( $attachment_id ) );
}

function urban_shisha_get_hero_data( array $data = array() ): array {
	$heading = (string) ( $data['home_hero_heading'] ?? '' );
	$parts = preg_split( '/(?<=[.!?])\s+|\n/u', $heading, 2 );
	$front = preg_split( '/\s+/u', $parts[1] ?? '', 2 );
	$image_id = (int) ( $data['home_hero_image'] ?? 0 );
	$hookahs_term = get_term_by( 'slug', 'hookahs', 'product_cat' );
	$hookahs_url  = ( $hookahs_term && ! is_wp_error( $hookahs_term ) ) ? get_term_link( $hookahs_term ) : ( wc_get_page_permalink( 'shop' ) ?: urban_shisha_route_url( 'shop' ) );
	$acc_term     = get_term_by( 'slug', 'accessories', 'product_cat' );
	$acc_url      = ( $acc_term && ! is_wp_error( $acc_term ) ) ? get_term_link( $acc_term ) : add_query_arg( 'category', 'accessories', $hookahs_url );

	$cta_primary = ( $data['home_hero_cta'] ?? array() ) ?: array( 'url' => '', 'title' => '' );
	if ( empty( $cta_primary['url'] ) || '#collection' === $cta_primary['url'] ) {
		$cta_primary['url'] = $hookahs_url;
	}
	if ( empty( $cta_primary['title'] ) ) {
		$cta_primary['title'] = __( 'Shop hookahs', 'urban-shisha' );
	}

	$cta_secondary = ( $data['home_hero_secondary_cta'] ?? array() ) ?: array( 'url' => '', 'title' => '' );
	if ( empty( $cta_secondary['url'] ) || '#accessories' === $cta_secondary['url'] ) {
		$cta_secondary['url'] = $acc_url;
	}
	if ( empty( $cta_secondary['title'] ) ) {
		$cta_secondary['title'] = __( 'Shop accessories', 'urban-shisha' );
	}

	return array(
		'headline_back' => $parts[0] ?? '',
		'headline_front_first' => $front[0] ?? '',
		'headline_front_second' => $front[1] ?? '',
		'eyebrow' => $data['home_hero_eyebrow'] ?? '',
		'description' => $data['home_hero_description'] ?? '',
		'cta_primary' => $cta_primary,
		'cta_secondary' => $cta_secondary,
		'hero_image_url' => wp_get_attachment_image_url( $image_id, 'full' ),
		'image_id' => $image_id,
		'sticker_drop' => $data['home_hero_sticker_drop'] ?? '',
		'price_label' => $data['home_hero_sticker_price_label'] ?? '',
		'price_val' => $data['home_hero_sticker_price_value'] ?? '',
		'vibe_label' => $data['home_hero_sticker_vibe_label'] ?? '',
		'hot_label' => $data['home_hero_sticker_hot_label'] ?? '',
		'pulse_text' => $data['home_hero_pulse_text'] ?? '',
		'pulse_note' => $data['home_hero_pulse_note'] ?? '',
		'chips' => array_filter( array_map( 'trim', explode( "\n", $data['home_hero_chips'] ?? '' ) ) ),
		'price_note' => $data['home_hero_price_note'] ?? '',
		'side_note' => $data['home_hero_side_note'] ?? '',
		'ticker_text' => $data['home_hero_ticker_text'] ?? '',
	);
}

function urban_shisha_get_brands_data( array $data = array() ): array {
	$resolved_brands = array();
	foreach ( (array) ( $data['home_brands_cards'] ?? array() ) as $card ) {
		$term = get_term( (int) ( $card['home_brands_card_term'] ?? 0 ), 'product_brand' );
		if ( ! $term || is_wp_error( $term ) ) { continue; }
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) { continue; }
		$resolved_brands[] = array( 'name' => $term->name, 'url' => $url, 'logo_url' => wp_get_attachment_image_url( (int) get_term_meta( $term->term_id, 'thumbnail_id', true ), 'full' ) );
	}
	return array( 'eyebrow' => $data['home_brands_eyebrow'] ?? '', 'heading' => $data['home_brands_heading'] ?? '', 'cta' => ( $data['home_brands_cta'] ?? array() ) ?: array( 'url' => '', 'title' => '' ), 'resolved_brands' => $resolved_brands );
}

function urban_shisha_home_budget_ranges(): array {
	$sections = get_field( 'home_sections', (int) get_option( 'page_on_front' ) );
	$result = array();
	foreach ( (array) $sections as $section ) {
		if ( 'budgets' !== ( $section['acf_fc_layout'] ?? '' ) ) { continue; }
		foreach ( (array) ( $section['home_budgets_ranges'] ?? array() ) as $range ) {
			$result[ $range['home_budgets_jump_key'] ] = array( 'label' => wp_strip_all_tags( $range['home_budgets_range_title'] ), 'min' => (float) ( $range['home_budgets_min'] ?? 0 ), 'max' => isset( $range['home_budgets_max'] ) && '' !== $range['home_budgets_max'] ? (float) $range['home_budgets_max'] : null );
		}
	}
	return $result;
}

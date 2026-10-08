<?php
/** Independent post-import audit. Run with wp eval-file; never mutates products. */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    exit;
}
$plan = json_decode( file_get_contents( $args[0] ), true, 512, JSON_THROW_ON_ERROR );
$output = $args[1];
$only = isset( $args[2] ) ? explode( ',', $args[2] ) : array();
$records = array_values( array_filter( $plan['products'], fn( $r ) => ! $only || in_array( $r['handle'], $only, true ) ) );
$failures = array();
$check = function ( bool $pass, string $message ) use ( &$failures ) {
    if ( ! $pass ) {
        $failures[] = $message;
    }
};
$media = array_column( $plan['media'], null, 'url' );
$counts = array( 'parents' => 0, 'simple' => 0, 'variable' => 0, 'source_variants' => 0, 'variation_records' => 0, 'image_references' => 0, 'unique_media_verified' => 0, 'specifications' => 0, 'features' => 0, 'included_items' => 0, 'usage_steps' => 0 );
$verified_images = array();
$ids = array();
$image_check = function ( int $id, string $url, string $context ) use ( &$verified_images, $media, $check ) {
    $check( $id > 0 && 'attachment' === get_post_type( $id ), $context . ': missing image' );
    if ( ! $id || ! isset( $media[ $url ] ) ) {
        return;
    }
    $hash = $media[ $url ]['sha256'];
    $check( get_post_meta( $id, '_us_source_media_sha256', true ) === $hash, $context . ': incorrect source image mapping' );
    $aliases = get_post_meta( $id, '_us_source_media_urls', true );
    $check( is_array( $aliases ) && in_array( $url, $aliases, true ), $context . ': source URL missing from attachment provenance' );
    if ( ! isset( $verified_images[ $id ] ) ) {
        $path = get_attached_file( $id );
        $check( $path && is_file( $path ) && hash_file( 'sha256', $path ) === $hash, $context . ': original image was modified or missing' );
        $metadata = wp_get_attachment_metadata( $id );
        $check( is_array( $metadata ) && ! empty( $metadata['width'] ) && ! empty( $metadata['height'] ), $context . ': image metadata missing' );
        $verified_images[ $id ] = $hash;
    }
};
$commerce_check = function ( WC_Product $p, array $v, string $context ) use ( $check ) {
    $expected_regular = (float) ( $v['compare_at_price'] ?? 0 ) > (float) $v['price'] ? $v['compare_at_price'] : $v['price'];
    $expected_sale = (float) ( $v['compare_at_price'] ?? 0 ) > (float) $v['price'] ? $v['price'] : '';
    $check( (float) $p->get_regular_price() === (float) $expected_regular, $context . ': regular price mismatch' );
    $check( '' === $expected_sale ? '' === $p->get_sale_price() : (float) $p->get_sale_price() === (float) $expected_sale, $context . ': sale price mismatch' );
    $check( (float) $p->get_price() === (float) $v['price'], $context . ': current price mismatch' );
    $check( ! $p->get_manage_stock() && null === $p->get_stock_quantity() && 'outofstock' === $p->get_stock_status() && 'no' === $p->get_backorders(), $context . ': stock must be unconfirmed/out of stock, without fabricated quantity' );
    $check( get_post_meta( $p->get_id(), '_us_source_variant_id', true ) === $v['source_id'], $context . ': source variant missing' );
    $snapshot = get_post_meta( $p->get_id(), '_us_source_variant_snapshot', true );
    $check( is_array( $snapshot ) && $snapshot === $v, $context . ': source availability/price/options snapshot mismatch' );
};
foreach ( $records as $record ) {
    $posts = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_us_source_product_id', 'meta_value' => $record['source_id'] ) );
    $check( 1 === count( $posts ), $record['handle'] . ': expected exactly one imported product' );
    if ( 1 !== count( $posts ) ) {
        continue;
    }
    $id = (int) $posts[0];
    $ids[] = $id;
    $p = wc_get_product( $id );
    $check( $p && $p->get_type() === $record['type'], $record['handle'] . ': incorrect product type' );
    if ( ! $p ) {
        continue;
    }
    $counts['parents']++;
    $counts[ $record['type'] ]++;
    $counts['source_variants'] += count( $record['variants'] );
    $check( 'draft' === $p->get_status() && 'outofstock' === $p->get_stock_status(), $record['handle'] . ': parent must remain a draft/out of stock' );
    $check( $p->get_name() === $record['title'], $record['handle'] . ': title mismatch' );
    $check( 'yes' === get_post_meta( $id, '_us_catalog_import_complete', true ), $record['handle'] . ': completion marker missing' );
    $check( 'pending' === get_field( 'us_product_review_status', $id ), $record['handle'] . ': review status incorrect' );
    $check( ! preg_match( '/frequently asked|shipping\s*(?:&amp;|&)\s*delivery|prepaid only/i', $p->get_description() ), $record['handle'] . ': excluded source content leaked into description' );
    $check( trim( $p->get_description() ) === trim( wp_kses_post( $record['editorial']['description'] ) ), $record['handle'] . ': description mismatch' );
    $actual_categories = wp_get_object_terms( $id, 'product_cat', array( 'fields' => 'names' ) );
    $expected_categories = $record['categories'];
    sort( $actual_categories );
    sort( $expected_categories );
    $check( $actual_categories === $expected_categories, $record['handle'] . ': category mapping mismatch' );
    $brands = wp_get_object_terms( $id, 'product_brand', array( 'fields' => 'names' ) );
    $check( $brands === ( $record['brand'] ? array( $record['brand'] ) : array() ), $record['handle'] . ': brand mapping mismatch' );
    $check( get_post_meta( $id, '_us_source_collections', true ) === $record['source_collections'], $record['handle'] . ': source collections lost' );
    $gallery = array_merge( array( $p->get_image_id() ), $p->get_gallery_image_ids() );
    $check( count( $gallery ) === count( $record['image_urls'] ), $record['handle'] . ': gallery count mismatch' );
    foreach ( $record['image_urls'] as $index => $url ) {
        $image_check( (int) ( $gallery[ $index ] ?? 0 ), $url, $record['handle'] );
        $counts['image_references']++;
    }
    $field_map = array( 'specifications' => array( 'label' => 'spec_label', 'value' => 'spec_value' ), 'features' => array( 'feature' => 'feature_name', 'availability' => 'feature_availability', 'notes' => 'feature_notes' ), 'included_items' => array( 'item' => 'included_item' ), 'usage_steps' => array( 'step' => 'usage_step', 'instructions' => 'usage_instructions' ) );
    foreach ( $field_map as $name => $keys ) {
        $actual = get_field( 'us_product_' . $name, $id ) ?: array();
        $expected = $record['editorial'][ $name ];
        $check( count( $actual ) === count( $expected ), $record['handle'] . ': ' . $name . ' row count mismatch' );
        foreach ( $expected as $i => $row ) {
            foreach ( $keys as $source_key => $field_key ) {
                $check( ( $actual[ $i ][ $field_key ] ?? null ) === $row[ $source_key ], $record['handle'] . ': ' . $name . ' value mismatch at row ' . $i );
            }
        }
        $counts[ $name ] += count( $actual );
    }
    if ( 'simple' === $record['type'] ) {
        $commerce_check( $p, $record['variants'][0], $record['handle'] );
    } else {
        $children = $p->get_children();
        $check( count( $children ) === count( $record['variants'] ), $record['handle'] . ': variation count mismatch' );
        $counts['variation_records'] += count( $children );
        foreach ( $record['variants'] as $v ) {
            $matched = array_values( array_filter( $children, fn( $child ) => get_post_meta( $child, '_us_source_variant_id', true ) === $v['source_id'] ) );
            $check( 1 === count( $matched ), $record['handle'] . ': variant mapping missing/duplicated' );
            if ( 1 !== count( $matched ) ) {
                continue;
            }
            $variation = wc_get_product( $matched[0] );
            $commerce_check( $variation, $v, $record['handle'] . ' / ' . $v['title'] );
            $attrs = $variation->get_attributes();
            foreach ( $record['options'] as $i => $o ) {
                $slug = 'Colour' === $o['name'] ? 'pa_colour' : 'pa_' . wc_sanitize_taxonomy_name( $o['name'] );
                $term = get_term_by( 'name', $v['options'][ $i ], $slug );
                $check( $term && isset( $attrs[ $slug ] ) && $attrs[ $slug ] === $term->slug, $record['handle'] . ': variant option mismatch' );
                $parent_attr = $p->get_attributes()[ $slug ] ?? null;
                $check( $parent_attr && $parent_attr->get_variation() && in_array( (int) $term->term_id, $parent_attr->get_options(), true ), $record['handle'] . ': parent attribute terms missing' );
            }
            if ( $v['image_url'] ) {
                $image_check( $variation->get_image_id(), $v['image_url'], $record['handle'] . ' / ' . $v['title'] );
            }
        }
    }
}
$counts['unique_media_verified'] = count( $verified_images );
$group = acf_get_field_group( 'group_us_products' );
$check( $group && $group['location'] === array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'product' ) ) ), 'SCF group must apply only to products' );
$imported = get_posts( array( 'post_type' => 'product', 'post_status' => 'any', 'meta_key' => '_us_source_product_id', 'fields' => 'ids', 'posts_per_page' => -1 ) );
if ( ! $only ) {
    $check( count( $imported ) === count( $plan['products'] ), 'Total imported parent count mismatch' );
    $check( count( $verified_images ) === count( array_unique( array_column( $plan['media'], 'sha256' ) ) ), 'Unique original media count mismatch' );
}
$result = array( 'passed' => ! $failures, 'checked_at' => gmdate( 'c' ), 'counts' => $counts, 'product_ids' => $ids, 'failures' => $failures );
file_put_contents( $output, wp_json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
WP_CLI::log( wp_json_encode( $result['counts'] ) );
if ( $failures ) {
    foreach ( array_slice( $failures, 0, 20 ) as $failure ) {
        WP_CLI::warning( $failure );
    }
    WP_CLI::error( count( $failures ) . ' audit checks failed; see ' . $output );
}
WP_CLI::success( 'Catalogue audit passed.' );

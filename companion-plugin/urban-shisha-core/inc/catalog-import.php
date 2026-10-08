<?php
/** Local WP-CLI import only. WooCommerce owns all commerce values. */
defined( 'ABSPATH' ) || exit;

class Urban_Shisha_Catalog_Import {
    private array $media = array();
    private array $image_ids = array();
    private string $data_root;
    private array $report;

    /**
     * Import a verified catalogue as draft, out-of-stock products.
     *
     * ## OPTIONS
     *
     * --file=<path>
     * : Prepared woocommerce-plan.json; images are relative to its directory.
     *
     * --report=<path>
     * : Write an import result JSON outside the public web root.
     *
     * [--only=<handles>]
     * : Comma-separated source handles for a representative sample.
     *
     * [--limit=<number>]
     * : Maximum number of selected products.
     *
     * ## EXAMPLES
     *
     *     wp urban-shisha catalog-import --file=/path/woocommerce-plan.json --report=/path/import-report.json
     */
    public function __invoke( $args, $options ) {
        if ( ! class_exists( 'WC_Product_Simple' ) || ! function_exists( 'update_field' ) ) {
            WP_CLI::error( 'Active WooCommerce and Secure Custom Fields are required.' );
        }
        $file = realpath( $options['file'] );
        $report_path = $options['report'];
        if ( ! $file || ! is_readable( $file ) || ! is_dir( dirname( $report_path ) ) || ! is_writable( dirname( $report_path ) ) ) {
            WP_CLI::error( 'Input file must be readable and the report directory writable.' );
        }
        $plan = json_decode( file_get_contents( $file ), true, 512, JSON_THROW_ON_ERROR );
        if ( 1 !== ( $plan['schema'] ?? null ) || 'INR' !== ( $plan['currency'] ?? '' ) || 'INR' !== get_woocommerce_currency() || 'https://shishastore.in' !== ( $plan['source'] ?? '' ) ) {
            WP_CLI::error( 'Unsupported plan/source or WooCommerce currency mismatch.' );
        }
        $this->data_root = dirname( $file );
        $this->report = array( 'source' => $plan['source'], 'started_at' => gmdate( 'c' ), 'created' => 0, 'resumed' => 0, 'skipped' => 0, 'products' => array(), 'errors' => array() );
        foreach ( $plan['media'] as $image ) {
            $this->media[ $image['url'] ] = $image;
        }
        $products = $plan['products'];
        if ( isset( $options['only'] ) ) {
            $handles = array_filter( array_map( 'trim', explode( ',', $options['only'] ) ) );
            $products = array_values( array_filter( $products, fn( $p ) => in_array( $p['handle'], $handles, true ) ) );
            if ( count( $products ) !== count( array_unique( $handles ) ) ) {
                WP_CLI::error( 'One or more requested source handles were not found.' );
            }
        }
        if ( isset( $options['limit'] ) ) {
            if ( (int) $options['limit'] < 1 ) {
                WP_CLI::error( 'Limit must be positive.' );
            }
            $products = array_slice( $products, 0, (int) $options['limit'] );
        }
        if ( ! $products ) {
            WP_CLI::error( 'No products selected.' );
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        // Keep uploaded originals byte-identical; WordPress may still create thumbnails.
        add_filter( 'big_image_size_threshold', '__return_false' );
        foreach ( $products as $index => $record ) {
            try {
                $result = $this->product( $record );
                $this->report[ $result['action'] ]++;
                $this->report['products'][] = $result;
                WP_CLI::log( sprintf( '[%d/%d] %s #%d — %s', $index + 1, count( $products ), $result['action'], $result['id'], $record['title'] ) );
            } catch ( Throwable $error ) {
                $this->report['errors'][] = array( 'handle' => $record['handle'], 'error' => $error->getMessage() );
                WP_CLI::warning( $record['handle'] . ': ' . $error->getMessage() );
            }
            $this->write_report( $report_path );
        }
        $this->report['finished_at'] = gmdate( 'c' );
        $this->write_report( $report_path );
        if ( $this->report['errors'] ) {
            WP_CLI::error( count( $this->report['errors'] ) . ' product(s) failed. Completed products are preserved; rerun to resume incomplete records.' );
        }
        WP_CLI::success( sprintf( 'Created %d, resumed %d, skipped %d. All newly imported parent products are drafts.', $this->report['created'], $this->report['resumed'], $this->report['skipped'] ) );
    }

    private function write_report( string $path ): void {
        if ( false === file_put_contents( $path, wp_json_encode( $this->report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n", LOCK_EX ) ) {
            throw new RuntimeException( 'Cannot write import report.' );
        }
    }

    private function find( string $type, string $key, string $value ): int {
        $ids = get_posts( array( 'post_type' => $type, 'post_status' => array( 'publish', 'draft', 'pending', 'private', 'future', 'inherit', 'trash' ), 'meta_key' => $key, 'meta_value' => $value, 'fields' => 'ids', 'posts_per_page' => 2, 'orderby' => 'ID', 'order' => 'ASC' ) );
        if ( count( $ids ) > 1 ) {
            throw new RuntimeException( 'Duplicate source identifier: ' . $key . '=' . $value );
        }
        if ( $ids && 'trash' === get_post_status( $ids[0] ) ) {
            throw new RuntimeException( 'Existing imported record is in Trash; restore or resolve it before reimporting.' );
        }
        return $ids ? (int) $ids[0] : 0;
    }

    private function term( string $name, string $taxonomy, int $parent = 0 ): int {
        $existing = term_exists( $name, $taxonomy, $parent );
        if ( $existing ) {
            return (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
        }
        $result = wp_insert_term( $name, $taxonomy, array( 'parent' => $parent ) );
        if ( is_wp_error( $result ) ) {
            throw new RuntimeException( $result->get_error_message() );
        }
        return (int) $result['term_id'];
    }

    private function image( string $url, int $parent, string $title ): int {
        if ( isset( $this->image_ids[ $url ] ) ) {
            return $this->image_ids[ $url ];
        }
        $source = $this->media[ $url ] ?? null;
        if ( ! $source ) {
            throw new RuntimeException( 'Missing source media mapping: ' . $url );
        }
        $path = realpath( $this->data_root . '/' . $source['local_file'] );
        if ( ! $path || ! str_starts_with( $path, $this->data_root . DIRECTORY_SEPARATOR ) || ! is_file( $path ) || hash_file( 'sha256', $path ) !== $source['sha256'] ) {
            throw new RuntimeException( 'Missing or corrupt original image: ' . $source['local_file'] );
        }
        $id = $this->find( 'attachment', '_us_source_media_sha256', $source['sha256'] );
        if ( ! $id ) {
            $upload = wp_upload_bits( basename( $path ), null, file_get_contents( $path ) );
            if ( $upload['error'] ) {
                throw new RuntimeException( $upload['error'] );
            }
            $type = wp_check_filetype( $upload['file'] );
            $id = wp_insert_attachment( array( 'post_mime_type' => $type['type'], 'post_title' => sanitize_text_field( $title ), 'post_status' => 'inherit', 'post_parent' => $parent ), $upload['file'], $parent, true );
            if ( is_wp_error( $id ) ) {
                throw new RuntimeException( $id->get_error_message() );
            }
            update_post_meta( $id, '_us_source_media_sha256', $source['sha256'] );
            update_post_meta( $id, '_us_source_media_url', esc_url_raw( $url ) );
            update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $title ) );
        }
        $urls = get_post_meta( $id, '_us_source_media_urls', true );
        $urls = is_array( $urls ) ? $urls : array();
        if ( ! in_array( $url, $urls, true ) ) {
            $urls[] = $url;
            update_post_meta( $id, '_us_source_media_urls', $urls );
        }
        if ( ! wp_get_attachment_metadata( $id ) ) {
            $metadata = wp_generate_attachment_metadata( $id, get_attached_file( $id ) );
            if ( ! is_array( $metadata ) || empty( $metadata['width'] ) ) {
                throw new RuntimeException( 'Image metadata could not be generated for #' . $id );
            }
            wp_update_attachment_metadata( $id, $metadata );
        }
        if ( ! is_file( get_attached_file( $id ) ) || hash_file( 'sha256', get_attached_file( $id ) ) !== $source['sha256'] ) {
            throw new RuntimeException( 'Uploaded original checksum mismatch for #' . $id );
        }
        $this->image_ids[ $url ] = (int) $id;
        return (int) $id;
    }

    private function attributes( array $options ): array {
        $attrs = array();
        foreach ( $options as $position => $option ) {
            $slug = 'Colour' === $option['name'] ? 'colour' : wc_sanitize_taxonomy_name( $option['name'] );
            $taxonomy = wc_attribute_taxonomy_name( $slug );
            $id = wc_attribute_taxonomy_id_by_name( $slug );
            if ( ! $id ) {
                $id = wc_create_attribute( array( 'name' => $option['name'], 'slug' => $slug, 'type' => 'select', 'order_by' => 'menu_order', 'has_archives' => false ) );
                if ( is_wp_error( $id ) ) {
                    throw new RuntimeException( $id->get_error_message() );
                }
                delete_transient( 'wc_attribute_taxonomies' );
                WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );
            }
            if ( ! taxonomy_exists( $taxonomy ) ) {
                register_taxonomy( $taxonomy, array( 'product' ), array( 'hierarchical' => false, 'show_ui' => true, 'query_var' => true, 'rewrite' => false ) );
            }
            $terms = array_map( fn( $value ) => $this->term( $value, $taxonomy ), $option['values'] );
            $attr = new WC_Product_Attribute();
            $attr->set_id( $id );
            $attr->set_name( $taxonomy );
            $attr->set_options( $terms );
            $attr->set_position( $position );
            $attr->set_visible( true );
            $attr->set_variation( true );
            $attrs[] = $attr;
        }
        return $attrs;
    }

    private function commerce( WC_Product $product, array $variant ): void {
        $price = wc_format_decimal( $variant['price'] );
        $compare = wc_format_decimal( $variant['compare_at_price'] ?? '' );
        if ( '' === $price || ! is_numeric( $price ) || (float) $price < 0 ) {
            throw new RuntimeException( 'Invalid source price.' );
        }
        $on_sale = '' !== $compare && (float) $compare > (float) $price;
        $product->set_regular_price( $on_sale ? $compare : $price );
        $product->set_sale_price( $on_sale ? $price : '' );
        $product->set_price( $price );
        $product->set_manage_stock( false );
        $product->set_stock_quantity( null );
        $product->set_stock_status( 'outofstock' );
        $product->set_backorders( 'no' );
        $product->set_virtual( ! $variant['requires_shipping'] );
        $product->set_weight( (float) $variant['weight'] > 0 ? wc_get_weight( $variant['weight'], get_option( 'woocommerce_weight_unit', 'kg' ), $variant['weight_unit'] ) : '' );
        $sku = trim( $variant['source_sku'] ?? '' );
        if ( $sku && 'SKU' !== strtoupper( $sku ) ) {
            $product->set_sku( $sku );
        }
        if ( ! empty( $variant['barcode'] ) && preg_match( '/^\d{8,14}$/', $variant['barcode'] ) ) {
            $product->set_global_unique_id( $variant['barcode'] );
        }
        $product->update_meta_data( '_us_source_variant_id', $variant['source_id'] );
        $product->update_meta_data( '_us_source_variant_snapshot', $variant );
        $product->update_meta_data( '_us_price_review_required', 'yes' );
        $product->update_meta_data( '_us_stock_review_required', 'yes' );
    }

    private function product( array $record ): array {
        $existing = $this->find( 'product', '_us_source_product_id', $record['source_id'] );
        if ( $existing && 'yes' === get_post_meta( $existing, '_us_catalog_import_complete', true ) ) {
            return array( 'id' => $existing, 'handle' => $record['handle'], 'action' => 'skipped' );
        }
        if ( $existing && 'draft' !== get_post_status( $existing ) ) {
            throw new RuntimeException( 'Incomplete imported product has been edited/published; refusing to overwrite it.' );
        }
        $product = 'variable' === $record['type'] ? new WC_Product_Variable( $existing ) : new WC_Product_Simple( $existing );
        $product->set_name( sanitize_text_field( $record['title'] ) );
        $product->set_slug( sanitize_title( $record['handle'] ) );
        $product->set_status( 'draft' );
        $product->set_description( wp_kses_post( $record['editorial']['description'] ) );
        $product->set_short_description( '' );
        $product->set_catalog_visibility( 'visible' );
        $product->set_manage_stock( false );
        $product->set_stock_quantity( null );
        $product->set_stock_status( 'outofstock' );
        $product->set_backorders( 'no' );
        $product->set_category_ids( array_map( fn( $name ) => $this->term( $name, 'product_cat' ), $record['categories'] ) );
        if ( 'variable' === $record['type'] ) {
            $product->set_attributes( $this->attributes( $record['options'] ) );
            $product->set_default_attributes( array() );
        } else {
            $this->commerce( $product, $record['variants'][0] );
        }
        $product->update_meta_data( '_us_source_product_id', $record['source_id'] );
        $product->update_meta_data( '_us_source_url', esc_url_raw( $record['source_url'] ) );
        $product->update_meta_data( '_us_source_vendor', sanitize_text_field( $record['vendor'] ) );
        $product->update_meta_data( '_us_source_collections', $record['source_collections'] );
        $product->update_meta_data( '_us_source_tags', $record['source_tags'] );
        $product->update_meta_data( '_us_imported_at', gmdate( 'c' ) );
        $id = $product->save();
        if ( $record['brand'] ) {
            if ( ! taxonomy_exists( 'product_brand' ) ) {
                throw new RuntimeException( 'WooCommerce brand taxonomy is unavailable.' );
            }
            $result = wp_set_object_terms( $id, array( $this->term( $record['brand'], 'product_brand' ) ), 'product_brand' );
            if ( is_wp_error( $result ) ) {
                throw new RuntimeException( $result->get_error_message() );
            }
        }
        $images = array_map( fn( $url ) => $this->image( $url, $id, $record['title'] ), $record['image_urls'] );
        if ( ! $images ) {
            throw new RuntimeException( 'Product has no images.' );
        }
        $product->set_image_id( $images[0] );
        $product->set_gallery_image_ids( array_slice( $images, 1 ) );
        $product->save();
        if ( 'variable' === $record['type'] ) {
            foreach ( $record['variants'] as $index => $source_variant ) {
                $variation_id = $this->find( 'product_variation', '_us_source_variant_id', $source_variant['source_id'] );
                if ( $variation_id && (int) wp_get_post_parent_id( $variation_id ) !== $id ) {
                    throw new RuntimeException( 'Variant source ID belongs to another product.' );
                }
                $variation = new WC_Product_Variation( $variation_id );
                $variation->set_parent_id( $id );
                $variation->set_status( 'publish' ); // WC-enabled variation; its parent remains draft.
                $variation->set_menu_order( $index );
                $attrs = array();
                foreach ( $record['options'] as $i => $option ) {
                    $slug = 'Colour' === $option['name'] ? 'colour' : wc_sanitize_taxonomy_name( $option['name'] );
                    $taxonomy = wc_attribute_taxonomy_name( $slug );
                    $term = get_term( $this->term( $source_variant['options'][ $i ], $taxonomy ), $taxonomy );
                    $attrs[ $taxonomy ] = $term->slug;
                }
                $variation->set_attributes( $attrs );
                $this->commerce( $variation, $source_variant );
                if ( $source_variant['image_url'] ) {
                    $variation->set_image_id( $this->image( $source_variant['image_url'], $id, $record['title'] . ' — ' . $source_variant['title'] ) );
                }
                $variation->save();
            }
            WC_Product_Variable::sync( $id );
        }
        $editorial = $record['editorial'];
        update_field( 'field_us_product_specifications', array_map( fn( $r ) => array( 'spec_label' => $r['label'], 'spec_value' => $r['value'] ), $editorial['specifications'] ), $id );
        update_field( 'field_us_product_features', array_map( fn( $r ) => array( 'feature_name' => $r['feature'], 'feature_availability' => $r['availability'], 'feature_notes' => $r['notes'] ), $editorial['features'] ), $id );
        update_field( 'field_us_product_included_items', array_map( fn( $r ) => array( 'included_item' => $r['item'] ), $editorial['included_items'] ), $id );
        update_field( 'field_us_product_usage_steps', array_map( fn( $r ) => array( 'usage_step' => $r['step'], 'usage_instructions' => $r['instructions'] ), $editorial['usage_steps'] ), $id );
        update_field( 'field_us_product_review_status', 'pending', $id );
        update_post_meta( $id, '_us_price_review_required', 'yes' );
        update_post_meta( $id, '_us_stock_review_required', 'yes' );
        update_post_meta( $id, '_us_catalog_import_complete', 'yes' );
        wc_delete_product_transients( $id );
        return array( 'id' => $id, 'handle' => $record['handle'], 'action' => $existing ? 'resumed' : 'created', 'type' => $record['type'], 'source_variants' => count( $record['variants'] ), 'images' => count( $images ) );
    }
}
WP_CLI::add_command( 'urban-shisha catalog-import', 'Urban_Shisha_Catalog_Import' );

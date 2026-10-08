<?php
/** Product-specific import provenance; no customer-facing changes. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_product_source_box() {
    add_meta_box( 'us-product-source', 'Urban Shisha — Import source', 'urban_shisha_product_source_content', 'product', 'side', 'default' );
}
add_action( 'add_meta_boxes_product', 'urban_shisha_product_source_box' );

function urban_shisha_product_source_content( $post ) {
    $url = get_post_meta( $post->ID, '_us_source_url', true );
    if ( ! $url ) {
        echo '<p>This product was not imported from the source catalogue.</p>';
        return;
    }
    echo '<p><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">Original product</a></p>';
    foreach ( array( '_us_source_product_id' => 'Source ID', '_us_source_vendor' => 'Source vendor', '_us_source_collections' => 'Source collections', '_us_imported_at' => 'Imported' ) as $key => $label ) {
        $value = get_post_meta( $post->ID, $key, true );
        echo '<p><strong>' . esc_html( $label ) . '</strong><br>' . esc_html( is_array( $value ) ? implode( ', ', $value ) : $value ) . '</p>';
    }
    echo '<p>Imported prices are source reference values. Source availability is not Urban Shisha stock. Confirm prices, stock, descriptions and included items before publishing.</p>';
    echo '<p>Original source data remains in the catalogue export. FAQs and source delivery/payment promises were excluded.</p>';
}

function urban_shisha_product_import_notice() {
    $screen = get_current_screen();
    if ( ! $screen || 'product' !== $screen->post_type || 'post' !== $screen->base ) {
        return;
    }
    $id = absint( get_the_ID() );
    if ( $id && get_post_meta( $id, '_us_source_product_id', true ) && 'approved' !== get_post_meta( $id, 'us_product_review_status', true ) ) {
        echo '<div class="notice notice-info"><p><strong>Urban Shisha import review:</strong> This is source-store catalogue data. Review the content, your selling price and your own stock before publishing.</p></div>';
    }
}
add_action( 'admin_notices', 'urban_shisha_product_import_notice' );

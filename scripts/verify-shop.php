<?php
/**
 * Shop Page End-to-End Verification Audit
 */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    exit;
}

$failures = array();
$check = function( $condition, $message ) use ( &$failures ) {
    if ( ! $condition ) {
        $failures[] = $message;
    }
};

WP_CLI::log( 'Starting Shop Page End-to-End Verification...' );

// 1. Check Preserved Files
$check( file_exists( get_theme_file_path( 'template-parts/home/builder.php' ) ), 'builder.php missing' );
$check( file_exists( get_theme_file_path( 'assets/builder-picker.css' ) ), 'builder-picker.css missing' );
$check( file_exists( get_theme_file_path( 'assets/js/builder-picker.js' ) ), 'builder-picker.js missing' );
$check( 'yes' === get_option( 'woocommerce_coming_soon' ), 'woocommerce_coming_soon altered' );
$check( 0 === (int) wp_count_posts( 'product' )->publish, 'Draft products were published' );

// 2. Admin Preview - Real Catalogue & 24 Products/Page Pagination across 5 pages
$admins = get_users( array( 'role' => 'administrator', 'fields' => 'ID', 'number' => 1 ) );
wp_set_current_user( $admins[0] );

$page1 = urban_shisha_get_shop_products( array( 'paged' => 1 ) );
$check( 107 === $page1['total'], 'Total products should be 107, got ' . $page1['total'] );
$check( 5 === $page1['max_num_pages'], 'Max pages should be 5, got ' . $page1['max_num_pages'] );
$check( 24 === count( $page1['products'] ), 'Page 1 should have 24 products, got ' . count( $page1['products'] ) );

$page2 = urban_shisha_get_shop_products( array( 'paged' => 2 ) );
$check( 24 === count( $page2['products'] ), 'Page 2 should have 24 products, got ' . count( $page2['products'] ) );

$page3 = urban_shisha_get_shop_products( array( 'paged' => 3 ) );
$check( 24 === count( $page3['products'] ), 'Page 3 should have 24 products, got ' . count( $page3['products'] ) );

$page4 = urban_shisha_get_shop_products( array( 'paged' => 4 ) );
$check( 24 === count( $page4['products'] ), 'Page 4 should have 24 products, got ' . count( $page4['products'] ) );

$page5 = urban_shisha_get_shop_products( array( 'paged' => 5 ) );
$check( 11 === count( $page5['products'] ), 'Page 5 should have 11 products, got ' . count( $page5['products'] ) );

$total_accessible = count( $page1['products'] ) + count( $page2['products'] ) + count( $page3['products'] ) + count( $page4['products'] ) + count( $page5['products'] );
$check( 107 === $total_accessible, 'All 107 products must be accessible across 5 pages, got ' . $total_accessible );

// 3. Guest Access Protection
wp_set_current_user( 0 );
$guest_res = urban_shisha_get_shop_products( array( 'paged' => 1 ) );
$check( 0 === $guest_res['total'], 'Guests must see 0 draft products' );
$check( 0 === count( $guest_res['products'] ), 'Guests must get 0 products array' );

// Switch back to admin for filter checks
wp_set_current_user( $admins[0] );

// 4. Dynamic Price Bounds
$bounds = urban_shisha_get_shop_price_bounds();
$check( 0.0 === (float) $bounds['min'], 'Price bound min should be 0' );
$check( $bounds['max'] >= 29999, 'Price bound max should be at least 29999, got ' . $bounds['max'] );
$check( 30000.0 === (float) $bounds['max'], 'Price bound max ceiling should be 30000, got ' . $bounds['max'] );

// 5. Price Filtering
// 5a. Open-ended min (e.g. min_price=10000 from homepage budget card)
$budget_high = urban_shisha_get_shop_products( array( 'min' => 10000 ) );
$check( $budget_high['total'] > 0, 'Open-ended min price 10000 should return products' );
foreach ( $budget_high['products'] as $p ) {
    $check( (float) $p['price_num'] >= 10000, 'Product price ' . $p['price_num'] . ' should be >= 10000' );
}

// 5b. Closed range (e.g. min_price=3000, max_price=7000)
$budget_mid = urban_shisha_get_shop_products( array( 'min' => 3000, 'max' => 7000 ) );
$check( $budget_mid['total'] > 0, 'Closed range 3000-7000 should return products' );
foreach ( $budget_mid['products'] as $p ) {
    $check( (float) $p['price_num'] >= 3000 && (float) $p['price_num'] <= 7000, 'Product price ' . $p['price_num'] . ' should be between 3000 and 7000' );
}

// 5c. Valid high maximum price (e.g. max_price=25000) should never be silently ignored
$valid_max = urban_shisha_get_shop_products( array( 'min' => 0, 'max' => 25000 ) );
$check( $valid_max['total'] > 0, 'Max price 25000 should return products' );
foreach ( $valid_max['products'] as $p ) {
    $check( (float) $p['price_num'] <= 25000, 'Product price ' . $p['price_num'] . ' should be <= 25000' );
}

// 6. Category Mapping & Counts
$categories = urban_shisha_get_shop_categories();
$check( 76 === $categories['hookahs']['count'], 'Hookahs count should be 76, got ' . $categories['hookahs']['count'] );
$check( 8 === $categories['bowls']['count'], 'Bowls count should be 8, got ' . $categories['bowls']['count'] );
$check( 6 === $categories['heat']['count'], 'Heat management count should be 6, got ' . $categories['heat']['count'] );
$check( 2 === $categories['hoses']['count'], 'Hoses count should be 2, got ' . $categories['hoses']['count'] );
$check( 3 === $categories['care']['count'], 'Tools & spares count should be 3 (not broad accessories), got ' . $categories['care']['count'] );
$check( 4 === $categories['charcoal']['count'], 'Charcoal count should be 4, got ' . $categories['charcoal']['count'] );

// 7. Combined Filtering
// Category + Brand
$cat_brand = urban_shisha_get_shop_products( array( 'category' => array( 'hookahs' ), 'brand' => array( 'cocoyaya' ) ) );
$check( $cat_brand['total'] > 0 && $cat_brand['total'] <= 35, 'Hookahs + COCOYAYA should return matching products' );

// Category OR Category
$two_cats = urban_shisha_get_shop_products( array( 'category' => array( 'bowls', 'heat' ) ) );
$check( ( 8 + 6 ) === $two_cats['total'], 'Bowls + Heat should return 14 products, got ' . $two_cats['total'] );

// 8. Sorting
$sort_low = urban_shisha_get_shop_products( array( 'sort' => 'price-low' ) );
$prices = array_column( $sort_low['products'], 'price_num' );
$sorted_prices = $prices;
sort( $sorted_prices );
$check( $prices === $sorted_prices, 'price-low sort order failed' );

// 9. Pagination Markup
$pag_html = urban_shisha_render_shop_pagination( 1, 5, array() );
$check( str_contains( $pag_html, 'class="shop-pagination"' ), 'Pagination markup missing container' );
$check( str_contains( $pag_html, 'disabled aria-disabled="true"' ), 'Page 1 previous button should be disabled' );
$check( str_contains( $pag_html, 'is-active" aria-current="page" data-page="1">1</span>' ), 'Page 1 should be active' );
$check( str_contains( $pag_html, 'data-page="2"' ) && str_contains( $pag_html, '>2</a>' ), 'Page 2 link missing' );
$check( str_contains( $pag_html, 'class="pagination-btn pagination-next" data-page="2"' ), 'Next page button missing' );

// 10. Result Count Text
$count_text_p1 = urban_shisha_get_shop_result_count_text( 107, 1, 24 );
$check( 'Showing 1–24 of 107 products' === $count_text_p1, 'Expected "Showing 1–24 of 107 products", got ' . $count_text_p1 );

$count_text_p5 = urban_shisha_get_shop_result_count_text( 107, 5, 24 );
$check( 'Showing 97–107 of 107 products' === $count_text_p5, 'Expected "Showing 97–107 of 107 products", got ' . $count_text_p5 );

// 11. Product Card Markup Layout (Bottom row alignment)
ob_start();
get_template_part( 'template-parts/product-card', null, array( 'product_data' => $page1['products'][0] ) );
$card_html = ob_get_clean();
$check( str_contains( $card_html, 'product-info-top' ), 'Product card missing product-info-top' );
$check( str_contains( $card_html, 'product-info-bottom' ), 'Product card missing product-info-bottom' );
$check( str_contains( $card_html, 'class="price"' ), 'Product card missing price' );
$check( str_contains( $card_html, 'class="add-product"' ), 'Product card missing add-product' );

// 12. Enqueue check for Shop
$check( file_exists( get_theme_file_path( 'assets/js/shop-wordpress.js' ) ), 'shop-wordpress.js missing' );
$client_config = urban_shisha_get_shop_client_config();
$check( ! empty( $client_config['nonce'] ), 'Shop config nonce missing' );
$check( 30000 === (int) $client_config['priceMax'], 'Shop config priceMax should be 30000' );

WP_CLI::log( wp_json_encode( array(
    'pass' => empty( $failures ),
    'failures' => $failures,
    'total_products' => $page1['total'],
    'max_pages' => $page1['max_num_pages'],
    'price_bounds' => $bounds,
    'categories' => array_map( fn($c) => $c['count'], $categories ),
), JSON_PRETTY_PRINT ) );

if ( ! empty( $failures ) ) {
    WP_CLI::error( 'Shop verification failed: ' . implode( '; ', $failures ) );
} else {
    WP_CLI::success( 'All Shop page requirements verified successfully!' );
}

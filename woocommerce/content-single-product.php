<?php
/** Approved Urban Shisha product composition backed by WooCommerce. */
defined( 'ABSPATH' ) || exit;
$context = urban_shisha_get_single_product_context();
if ( ! $context ) { return; }
global $product;
$product = $context['product'];
$data = $context['data'];
$gallery = $context['gallery'];
$specs = $context['specs'];
$id = $product->get_id();
// Purchase partial already supplies the shared wishlist control.
remove_action( 'woocommerce_after_add_to_cart_button', 'urban_shisha_render_single_product_wishlist_heart', 20 );
$title = urban_shisha_product_field( 'us_product_display_title', $id, $data['name'] );
$subtitle = urban_shisha_product_field( 'us_product_subtitle', $id );
$features = urban_shisha_product_field( 'us_product_features', $id, array() );
$included = urban_shisha_product_field( 'us_product_included_items', $id, array() );
$steps = urban_shisha_product_field( 'us_product_usage_steps', $id, array() );
$care = urban_shisha_product_field( 'us_product_care', $id );
$phone = preg_replace( '/\D/', '', urban_shisha_get_option( 'primary_whatsapp', '918700166924' ) );
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) { echo get_the_password_form(); return; }
?>
<main id="product-<?php echo esc_attr( $id ); ?>" <?php wc_product_class( 'product-main urban-pdp', $product ); ?>>
<div class="wrap">
<nav class="product-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a><span>/</span><a href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">Shop</a><span>/</span><span><?php echo esc_html( $title ); ?></span></nav>
<div class="product-layout">
<section class="pdp-gallery" aria-label="Product gallery">
<div class="pdp-stage" data-view="<?php echo esc_attr( $gallery[0]['type'] ); ?>">
<span class="pdp-gallery-label"><?php echo esc_html( $data['brand_name'] ); ?></span>
<button type="button" class="pdp-gallery-main" id="gallery-zoom" aria-label="Enlarge product image"><span class="pdp-gallery-art" id="gallery-art"><img src="<?php echo esc_url( $gallery[0]['src'] ); ?>" alt="<?php echo esc_attr( $data['name'] ); ?>" fetchpriority="high"></span><span class="pdp-zoom-label">View closer ↗</span></button>
<span class="pdp-gallery-counter" id="gallery-counter">01 / <?php echo esc_html( sprintf( '%02d', count( $gallery ) ) ); ?></span>
<div class="pdp-gallery-controls"><button type="button" class="rail-arrow" id="gallery-prev" aria-label="Previous image">←</button><button type="button" class="rail-arrow" id="gallery-next" aria-label="Next image">→</button></div>
</div><div class="pdp-thumbnails" id="gallery-thumbnails"><?php foreach ( $gallery as $index => $image ) : ?><button type="button" class="pdp-thumbnail" data-gallery-index="<?php echo esc_attr( $index ); ?>" aria-label="View image <?php echo esc_attr( $index + 1 ); ?>" aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"><img src="<?php echo esc_url( $image['src'] ); ?>" alt="" loading="lazy"></button><?php endforeach; ?></div>
<p id="gallery-status" class="screen-reader-text" aria-live="polite"></p></section>
<section class="pdp-buy-panel" aria-label="Product information">
<div class="pdp-brand-row"><?php if ( $data['brand_name'] ) : ?><a href="<?php echo esc_url( add_query_arg( 'brand', sanitize_title( $data['brand_name'] ), urban_shisha_route_url( 'shop' ) ) ); ?>"><?php echo esc_html( $data['brand_name'] ); ?></a><?php endif; ?><span><?php echo esc_html( urban_shisha_product_field( 'us_product_series', $id, $data['category_name'] ) ); ?></span></div>
<h1 id="product-title"><?php echo esc_html( $title ); ?></h1>
<?php if ( $subtitle ) : ?><p class="pdp-subtitle"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
<div class="pdp-price-row"><strong id="pdp-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></strong></div>
<?php if ( $product->get_short_description() ) : ?><div class="pdp-description"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div><?php endif; ?>
<?php if ( 'publish' !== $product->get_status() ) : ?><p class="pdp-preview-note">Product preview — not available to purchase yet.</p><?php else : echo wc_get_stock_html( $product ); endif; ?>
<?php if ( $product->is_type( 'simple' ) ) : ?>
<form class="cart pdp-cart" method="post" action="<?php echo esc_url( $product->get_permalink() ); ?>">
<?php do_action( 'woocommerce_before_add_to_cart_button' ); get_template_part( 'template-parts/product/purchase', null, array( 'product' => $product ) ); do_action( 'woocommerce_after_add_to_cart_button' ); ?>
<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $id ); ?>"><input type="hidden" name="product_id" value="<?php echo esc_attr( $id ); ?>"></form>
<?php else : woocommerce_template_single_add_to_cart(); endif; ?>
<p id="pdp-purchase-status" role="status" aria-live="polite"></p>
<?php if ( $phone ) : ?><a class="pdp-whatsapp" href="<?php echo esc_url( 'https://wa.me/' . $phone . '?text=' . rawurlencode( 'Hi Urban Shisha, I have a question about ' . $data['name'] . '. ' . $product->get_permalink() ) ); ?>" target="_blank" rel="noopener">Ask us about this product <span>↗</span></a><?php endif; ?>
<?php if ( $specs ) : ?><div class="pdp-quick-specs"><?php foreach ( array_slice( $specs, 0, 3 ) as $spec ) : ?><div><span><?php echo esc_html( $spec['spec_label'] ); ?></span><strong><?php echo esc_html( $spec['spec_value'] ); ?></strong></div><?php endforeach; ?></div><?php endif; ?>
<a class="pdp-spec-link" href="#product-details">Explore the details <span>↓</span></a>
</section></div></div>
<section class="pdp-details section" id="product-details"><div class="wrap"><div class="section-heading"><h2>THE DETAILS.<br>MAKE THE DIFFERENCE<span>.</span></h2></div>
<div class="pdp-detail-layout"><div class="pdp-spec-poster"><span><?php echo esc_html( $data['category_name'] ); ?></span><div class="pdp-detail-image"><img src="<?php echo esc_url( $gallery[ count( $gallery ) - 1 ]['src'] ); ?>" alt="<?php echo esc_attr( $data['name'] ); ?>" loading="lazy"></div><h3><?php echo esc_html( $title ); ?></h3></div>
<div class="pdp-accordion">
<?php if ( $product->get_description() ) : ?><details open><summary>About this product <span>↓</span></summary><div class="pdp-description-content"><?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?></div></details><?php endif; ?>
<?php if ( $specs ) : ?><details<?php echo $product->get_description() ? '' : ' open'; ?>><summary>Specifications <span>↓</span></summary><dl><?php foreach ( $specs as $spec ) : ?><div><dt><?php echo esc_html( $spec['spec_label'] ); ?></dt><dd><?php echo esc_html( $spec['spec_value'] ); ?></dd></div><?php endforeach; ?></dl></details><?php endif; ?>
<?php if ( $features ) : ?><details><summary>Features <span>↓</span></summary><dl><?php foreach ( $features as $feature ) : ?><div><dt><?php echo esc_html( $feature['feature_name'] ); ?></dt><dd><?php echo esc_html( 'unspecified' !== $feature['feature_availability'] ? ucfirst( $feature['feature_availability'] ) : '' ); ?><p><?php echo esc_html( $feature['feature_notes'] ); ?></p></dd></div><?php endforeach; ?></dl></details><?php endif; ?>
<?php if ( $included ) : ?><details><summary>What's in the box <span>↓</span></summary><ul class="pdp-box-items"><?php foreach ( $included as $item ) : ?><li><?php echo esc_html( $item['included_item'] ); ?></li><?php endforeach; ?></ul></details><?php endif; ?>
<?php if ( $steps ) : ?><details><summary>How to use <span>↓</span></summary><?php foreach ( $steps as $step ) : ?><p><strong><?php echo esc_html( $step['usage_step'] ); ?></strong><br><?php echo nl2br( esc_html( $step['usage_instructions'] ) ); ?></p><?php endforeach; ?></details><?php endif; ?>
<?php if ( $care ) : ?><details><summary>Care & cleaning <span>↓</span></summary><p><?php echo nl2br( esc_html( $care ) ); ?></p></details><?php endif; ?>
<details><summary>Shipping & returns <span>↓</span></summary><p><a class="text-link" href="<?php echo esc_url( urban_shisha_route_url( 'shipping' ) ); ?>">Shipping information ↗</a></p><p><a class="text-link" href="<?php echo esc_url( urban_shisha_route_url( 'returns' ) ); ?>">Returns policy ↗</a></p></details>
</div></div></div></section>
<?php foreach ( array( 'accessories' => 'COMPLETE YOUR SETUP.', 'related' => 'MORE WORTH DISCOVERING.' ) as $key => $heading ) : if ( ! $context[ $key ] ) { continue; } ?>
<section class="section pdp-<?php echo 'related' === $key ? 'related' : 'accessories'; ?>"><div class="wrap"><div class="section-heading"><h2><?php echo esc_html( $heading ); ?></h2><a class="text-link" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">Explore the shop ↗</a></div><div class="product-grid shop-product-grid"><?php foreach ( $context[ $key ] as $item ) { get_template_part( 'template-parts/product-card', null, array( 'product_data' => $item ) ); } ?></div></div></section>
<?php endforeach; ?>
</main>
<div class="pdp-mobile-buy"><div><span><?php echo esc_html( $title ); ?></span><strong id="pdp-mobile-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></strong></div><button type="button" class="button" id="pdp-mobile-action">Choose your setup ↑</button></div>
<dialog class="pdp-gallery-dialog" id="gallery-dialog" aria-label="Enlarged product images"><div class="pdp-zoom-header"><h2><?php echo esc_html( $title ); ?></h2><button type="button" id="gallery-close" aria-label="Close gallery">✕</button></div><div class="pdp-enlarged-image" id="gallery-enlarged"></div><div class="pdp-zoom-navigation"><button type="button" class="rail-arrow" id="zoom-prev" aria-label="Previous image">←</button><span id="zoom-counter"></span><button type="button" class="rail-arrow" id="zoom-next" aria-label="Next image">→</button></div></dialog>
<?php do_action( 'woocommerce_after_single_product' ); ?>

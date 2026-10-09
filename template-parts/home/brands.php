<?php
/**
 * Homepage Brands section template part.
 * Bound to WooCommerce product_brand taxonomy.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$brands_data = urban_shisha_get_brands_data( $args ?? array() );
?>
<section class="section brands-section wrap" id="brands" aria-labelledby="brands-title">
	<div class="section-heading">
		<div>
			<p class="eyebrow"><?php echo esc_html( $brands_data['eyebrow'] ); ?></p>
			<h2 id="brands-title"><?php echo esc_html( $brands_data['heading'] ); ?><span class="punct">.</span></h2>
		</div>
		<?php if ( ! empty( $brands_data['cta']['url'] ) ) : ?>
			<a class="text-link" href="<?php echo esc_url( $brands_data['cta']['url'] ); ?>">
				<?php echo esc_html( $brands_data['cta']['title'] ?: __( 'Explore the shop', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		<?php endif; ?>
	</div>
	<div class="brand-grid">
		<?php foreach ( $brands_data['resolved_brands'] as $brand ) : ?>
			<a class="brand-tile" href="<?php echo esc_url( $brand['url'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Shop %s', 'urban-shisha' ), $brand['name'] ) ); ?>">
				<span class="brand-logo">
					<img src="<?php echo esc_url( $brand['logo_url'] ); ?>" alt="<?php echo esc_attr( $brand['name'] ); ?>" loading="lazy" width="200" height="140">
				</span>
				<span class="brand-name"><?php echo esc_html( $brand['name'] ); ?><svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

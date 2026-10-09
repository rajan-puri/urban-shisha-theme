<?php
/**
 * Shared product card template part.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$product_data = $args['product_data'] ?? null;
if ( ! $product_data && ! empty( $args['product_id'] ) ) {
	$product_data = urban_shisha_get_product_data( $args['product_id'] );
}
if ( ! $product_data ) {
	global $product;
	if ( $product instanceof WC_Product ) {
		$product_data = urban_shisha_get_product_data( $product );
	}
}

if ( ! $product_data ) {
	return;
}

$product_id     = $product_data['id'];
$name           = $product_data['name'];
$permalink      = $product_data['permalink'];
$category_name  = $product_data['category_name'];
$price_html     = $product_data['price_html'];
$price_num      = $product_data['price_num'] ?? 0;
$badge          = $args['override_badge'] ?? $product_data['badge'];
$can_quick_add  = $product_data['can_quick_add'];
$art_html       = $product_data['art_html'];
$cta_label      = $product_data['cta_label'];
$accessory_tabs = $args['accessory_tabs'] ?? '';
$is_hidden      = ! empty( $args['hidden'] );
?>
<article class="product-card" data-home-default="<?php echo ( $args['home_default_visible'] ?? true ) ? '1' : '0'; ?>" <?php if ( $is_hidden || ( isset( $args['home_default_visible'] ) && ! $args['home_default_visible'] ) ) : ?>hidden<?php endif; ?> data-id="<?php echo esc_attr( $product_id ); ?>" data-price="<?php echo esc_attr( (string) $price_num ); ?>" data-categories="<?php echo esc_attr( implode( ' ', $product_data['categories'] ?? array() ) ); ?>"<?php if ( $accessory_tabs ) : ?> data-accessory-tabs="<?php echo esc_attr( $accessory_tabs ); ?>"<?php endif; ?>>
	<div class="product-image">
		<a class="product-picture" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'urban-shisha' ), $name ) ); ?>">
			<?php echo $art_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<?php if ( $can_quick_add ) : ?>
			<button type="button" class="quick-add" data-add="<?php echo esc_attr( $product_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Quick add %s to bag', 'urban-shisha' ), $name ) ); ?>">
				<?php esc_html_e( 'Quick add', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-plus"/></svg>
			</button>
		<?php else : ?>
			<a class="quick-add" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'urban-shisha' ), $name ) ); ?>">
				<?php echo esc_html( $cta_label ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		<?php endif; ?>
		<?php if ( $badge ) : ?>
			<span class="product-badge badge-<?php echo esc_attr( sanitize_html_class( strtolower( $badge ) ) ); ?>"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<button type="button" class="save-product" data-save="<?php echo esc_attr( $product_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Save %s', 'urban-shisha' ), $name ) ); ?>" aria-pressed="false">
			<svg aria-hidden="true"><use href="#i-heart"/></svg>
		</button>
	</div>
	<div class="product-info">
		<div class="product-info-top">
			<p class="product-type"><?php echo esc_html( $category_name ); ?></p>
			<a class="product-name" href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
		</div>
		<div class="product-info-bottom">
			<p class="price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php if ( $can_quick_add ) : ?>
				<button type="button" class="add-product" data-add="<?php echo esc_attr( $product_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to bag', 'urban-shisha' ), $name ) ); ?>">
					<svg aria-hidden="true"><use href="#i-plus"/></svg>
				</button>
			<?php else : ?>
				<a class="add-product" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'urban-shisha' ), $name ) ); ?>">
					<svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			<?php endif; ?>
		</div>
	</div>
</article>

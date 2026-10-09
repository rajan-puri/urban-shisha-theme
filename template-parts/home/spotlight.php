<?php
/**
 * Homepage Spotlight section template part.
 * 1 large feature card + 4 small selectable choice cards with next/prev controls.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$eyebrow     = $args['home_spotlight_eyebrow'] ?? 'FOUR PICKS. YOUR NEXT STATEMENT.';
$heading     = $args['home_spotlight_heading'] ?? "MEET YOUR NEXT<br>FAVOURITE";
$description = $args['home_spotlight_description'] ?? "Take a closer look.<br>Pick a silhouette that feels like you.";
$selected    = $args['home_spotlight_products'] ?? array();

$spotlight_items = array();
if ( ! empty( $selected ) ) {
	foreach ( $selected as $prod_id ) {
		$d = urban_shisha_get_product_data( $prod_id );
		if ( $d ) {
			$spotlight_items[] = $d;
		}
	}
}

if ( empty( $selected ) ) {
    $spotlight_items = urban_shisha_query_products( array( 'limit' => 4, 'category' => 'hookahs' ) );
}
if ( ! $spotlight_items ) { return; }

$first = $spotlight_items[0] ?? null;
?>
<section class="section spotlight-section" id="product-spotlight" aria-labelledby="spotlight-title">
	<div class="wrap">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 id="spotlight-title"><?php echo wp_kses_post( $heading ); ?><span class="punct">.</span></h2>
			</div>
			<p class="section-note"><?php echo wp_kses_post( $description ); ?></p>
		</div>
		<div class="spotlight-layout">
			<article class="spotlight-feature" aria-label="<?php esc_attr_e( 'Featured hookah', 'urban-shisha' ); ?>">
				<div class="spotlight-feature-top">
					<span>THE HOOKAH EDIT</span>
					<span id="spotlight-position">01 / <?php echo esc_html( sprintf( '%02d', max( 1, count( $spotlight_items ) ) ) ); ?></span>
				</div>
				<a class="spotlight-main-image" href="<?php echo esc_url( $first['permalink'] ); ?>" id="spotlight-image" aria-label="<?php echo esc_attr( $first ? sprintf( __( 'View %s', 'urban-shisha' ), $first['name'] ) : '' ); ?>" data-product="<?php echo esc_attr( $first['id'] ?? '' ); ?>">
					<?php if ( $first ) : echo $first['art_html']; endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<div class="spotlight-feature-copy" id="spotlight-copy">
					<?php if ( $first ) : ?>
						<p><?php echo esc_html( $first['category_name'] ); ?></p>
						<h3><?php echo esc_html( $first['name'] ); ?></h3>
						<strong><?php echo $first['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					<?php endif; ?>
				</div>
				<div class="spotlight-feature-bottom">
                    <a class="button" id="spotlight-add" href="<?php echo esc_url( $first['permalink'] ); ?>" <?php if ( $first['can_quick_add'] ) : ?>data-add="<?php echo esc_attr( $first['id'] ); ?>"<?php endif; ?>>
                        <?php echo esc_html( $first['can_quick_add'] ? __( 'Add to bag', 'urban-shisha' ) : __( 'View product', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
                    </a>
					<div class="spotlight-controls" role="group" aria-label="<?php esc_attr_e( 'Choose featured product', 'urban-shisha' ); ?>">
						<button type="button" class="rail-arrow" id="spotlight-prev" aria-label="<?php esc_attr_e( 'Previous featured product', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
						<button type="button" class="rail-arrow" id="spotlight-next" aria-label="<?php esc_attr_e( 'Next featured product', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
					</div>
				</div>
			</article>
			<div class="spotlight-choices" id="spotlight-choices" role="group" aria-label="<?php esc_attr_e( 'Select a hookah to feature', 'urban-shisha' ); ?>">
				<?php foreach ( $spotlight_items as $index => $item ) : ?>
					<button type="button" class="spotlight-choice" data-spotlight-index="<?php echo esc_attr( $index ); ?>" data-product-id="<?php echo esc_attr( $item['id'] ); ?>" data-product-url="<?php echo esc_url( $item['permalink'] ); ?>" data-quick-add="<?php echo $item['can_quick_add'] ? '1' : '0'; ?>" aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Feature %s', 'urban-shisha' ), $item['name'] ) ); ?>">
						<span class="spotlight-choice-number"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
						<span class="spotlight-choice-tag"><?php echo esc_html( $item['category_name'] ); ?></span>
						<span class="spotlight-choice-image"><?php echo $item['art_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="spotlight-choice-copy">
							<strong><?php echo esc_html( $item['name'] ); ?></strong>
							<span><?php echo $item['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            <span class="spotlight-choice-arrow" aria-hidden="true"><svg><use href="#i-arrow"/></svg></span>
						</span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>
		<p class="sr-only" id="spotlight-status" role="status" aria-live="polite" aria-atomic="true"></p>
	</div>
</section>

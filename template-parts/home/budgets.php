<?php
/**
 * Homepage Budgets section template part.
 * Opens the Shop page with saved min_price and max_price applied.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$shop_url = wc_get_page_permalink( 'shop' ) ?: urban_shisha_route_url( 'shop' );
?>
<section class="budget-section wrap" aria-labelledby="budget-title">
	<div class="section-heading reveal">
		<div>
			<p class="eyebrow"><?php echo esc_html( $args['home_budgets_eyebrow'] ?? '' ); ?></p>
			<h2 id="budget-title"><?php echo wp_kses_post( $args['home_budgets_heading'] ?? '' ); ?><span class="punct">.</span></h2>
		</div>
		<p class="section-note"><?php echo wp_kses_post( $args['home_budgets_description'] ?? '' ); ?></p>
	</div>
	<div class="budget-cards">
		<?php
		foreach ( (array) ( $args['home_budgets_ranges'] ?? array() ) as $range ) :
			$query_args = array();
			$min_val    = isset( $range['home_budgets_min'] ) && '' !== (string) $range['home_budgets_min'] ? (float) $range['home_budgets_min'] : null;
			$max_val    = isset( $range['home_budgets_max'] ) && '' !== (string) $range['home_budgets_max'] && null !== $range['home_budgets_max'] ? (float) $range['home_budgets_max'] : null;

			if ( null !== $min_val && $min_val > 0 ) {
				$query_args['min_price'] = (int) floor( $min_val );
			}
			if ( null !== $max_val && $max_val > 0 ) {
				$query_args['max_price'] = (int) ceil( $max_val );
			}
			$card_url = ! empty( $query_args ) ? add_query_arg( $query_args, $shop_url ) : $shop_url;
		?>
			<a class="budget-card" href="<?php echo esc_url( $card_url ); ?>">
				<div class="budget-copy">
					<span class="eyebrow"><?php echo esc_html( $range['home_budgets_eyebrow'] ?? '' ); ?></span>
					<h3><?php echo wp_kses_post( $range['home_budgets_range_title'] ?? '' ); ?></h3>
					<p><?php echo wp_kses_post( $range['home_budgets_copy'] ?? '' ); ?></p>
					<span class="text-link">
						<?php echo esc_html( $range['home_budgets_cta_label'] ?? __( 'Shop this range', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
					</span>
				</div>
				<?php echo urban_shisha_home_art( (int) ( $range['home_budgets_range_image'] ?? 0 ), wp_strip_all_tags( $range['home_budgets_range_title'] ?? '' ) ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</section>

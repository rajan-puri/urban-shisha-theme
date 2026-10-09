<?php
/**
 * Homepage Builder section template part.
 * 4-piece character loadout builder with live price calculation and stock validation.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$eyebrow     = $args['home_builder_eyebrow'] ?? 'INDIVIDUALLY GOOD. BETTER TOGETHER.';
$heading     = $args['home_builder_heading'] ?? 'Build it your way';
$description = $args['home_builder_description'] ?? "Pick your pieces.<br>Make the setup yours.";
$disclaimer  = $args['home_builder_disclaimer'] ?? __( 'Illustrative setup. Compatibility will be verified with the live catalog.', 'urban-shisha' );

// List the complete real category inventory; saved choices only set the initial order.
$catalog = array();
foreach ( array( 'hookahs' => 'hookahs', 'bowls' => 'bowls', 'heat' => 'heat-management', 'charcoal' => 'charcoal' ) as $group => $category ) {
    $items = urban_shisha_query_products( array( 'category' => $category, 'limit' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
    $preferred = array_map( 'absint', (array) ( $args['home_builder_' . $group] ?? array() ) );
    usort( $items, function ( $a, $b ) use ( $preferred ) {
        $first = array_search( $a['id'], $preferred, true ); $second = array_search( $b['id'], $preferred, true );
        return ( false === $first ? PHP_INT_MAX : $first ) <=> ( false === $second ? PHP_INT_MAX : $second );
    } );
    $catalog[$group] = $items;
}
$hookahs = $catalog['hookahs']; $bowls = $catalog['bowls']; $heat = $catalog['heat']; $charcoal = $catalog['charcoal'];

$initial_hookah   = $hookahs[0] ?? null;
$initial_bowl     = $bowls[0] ?? null;
$initial_heat     = $heat[0] ?? null;
$initial_charcoal = $charcoal[0] ?? null;

$initial_total = ( $initial_hookah['price_num'] ?? 0 ) + ( $initial_bowl['price_num'] ?? 0 ) + ( $initial_heat['price_num'] ?? 0 ) + ( $initial_charcoal['price_num'] ?? 0 );
?>
<section class="builder-section" id="builder" aria-labelledby="builder-title">
	<div class="builder-inner wrap">
		<div class="section-heading reveal">
			<div>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 id="builder-title"><?php echo esc_html( $heading ); ?><span class="punct">.</span></h2>
			</div>
			<p class="section-note"><?php echo wp_kses_post( $description ); ?></p>
		</div>
		<div class="builder-layout">
			<div class="builder-stage">
				<span class="builder-stage-label"><?php esc_html_e( 'CHARACTER BUILDER / YOUR LOADOUT', 'urban-shisha' ); ?></span>
				<div class="builder-hookah product-art has-cutout <?php echo esc_attr( $initial_hookah ? 'art-' . $initial_hookah['art_index'] : 'art-2' ); ?>" id="builder-hookah" role="img" aria-label="<?php echo esc_attr( $initial_hookah['name'] ?? __( 'Selected hookah', 'urban-shisha' ) ); ?>">
					<?php if ( $initial_hookah ) : echo $initial_hookah['image_html']; endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="builder-pieces">
					<span class="product-art has-cutout <?php echo esc_attr( $initial_bowl ? 'art-' . $initial_bowl['art_index'] : 'art-4' ); ?>" id="builder-bowl" role="img" aria-label="<?php echo esc_attr( $initial_bowl['name'] ?? __( 'Selected bowl', 'urban-shisha' ) ); ?>">
						<?php if ( $initial_bowl ) : echo $initial_bowl['image_html']; endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="product-art has-cutout <?php echo esc_attr( $initial_heat ? 'art-' . $initial_heat['art_index'] : 'art-5' ); ?>" id="builder-heat" role="img" aria-label="<?php echo esc_attr( $initial_heat['name'] ?? __( 'Selected heat management', 'urban-shisha' ) ); ?>">
						<?php if ( $initial_heat ) : echo $initial_heat['image_html']; endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="product-art has-cutout <?php echo esc_attr( $initial_charcoal ? 'art-' . $initial_charcoal['art_index'] : 'art-10' ); ?>" id="builder-charcoal" role="img" aria-label="<?php echo esc_attr( $initial_charcoal['name'] ?? __( 'Selected charcoal', 'urban-shisha' ) ); ?>">
						<?php if ( $initial_charcoal ) : echo $initial_charcoal['image_html']; endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</div>
				<span class="builder-caption"><?php esc_html_e( 'YOUR HOOKAH. YOUR EXTRAS. YOUR RULES.', 'urban-shisha' ); ?></span>
			</div>
			<form class="builder-controls" id="setup-form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="urban_shisha_builder_add_to_cart">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'urban_shisha_home_nonce' ) ); ?>">
				<div class="builder-heading">
					<h3><?php esc_html_e( 'Your signature setup', 'urban-shisha' ); ?></h3>
					<span>01 — 04</span>
				</div>

				<div class="builder-field">
					<span><b>01</b> <?php esc_html_e( 'Hookah', 'urban-shisha' ); ?></span>
					<select id="setup-hookah" name="hookah" aria-label="Hookah">
						<?php foreach ( $hookahs as $p ) : ?>
							<option value="<?php echo esc_attr( $p['id'] ); ?>" data-price="<?php echo esc_attr( $p['price_num'] ); ?>" data-art="<?php echo esc_attr( $p['art_index'] ); ?>" data-variable="<?php echo $p['is_variable'] ? '1' : '0'; ?>" data-name="<?php echo esc_attr( $p['name'] ); ?>" data-available="<?php echo $p['can_quick_add'] ? '1' : '0'; ?>">
								<?php echo esc_html( $p['name'] . ' (' . ( null === $p['price_num'] ? __( 'Price on request', 'urban-shisha' ) : ( $p['is_variable'] ? __( 'From ', 'urban-shisha' ) : '' ) . html_entity_decode( wp_strip_all_tags( wc_price( $p['price_num'] ) ), ENT_QUOTES, 'UTF-8' ) ) . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="builder-field">
					<span><b>02</b> <?php esc_html_e( 'Bowl', 'urban-shisha' ); ?></span>
					<select id="setup-bowl" name="bowl" aria-label="Bowl">
						<?php foreach ( $bowls as $p ) : ?>
							<option value="<?php echo esc_attr( $p['id'] ); ?>" data-price="<?php echo esc_attr( $p['price_num'] ); ?>" data-art="<?php echo esc_attr( $p['art_index'] ); ?>" data-variable="<?php echo $p['is_variable'] ? '1' : '0'; ?>" data-name="<?php echo esc_attr( $p['name'] ); ?>" data-available="<?php echo $p['can_quick_add'] ? '1' : '0'; ?>">
								<?php echo esc_html( $p['name'] . ' (' . ( null === $p['price_num'] ? __( 'Price on request', 'urban-shisha' ) : ( $p['is_variable'] ? __( 'From ', 'urban-shisha' ) : '' ) . html_entity_decode( wp_strip_all_tags( wc_price( $p['price_num'] ) ), ENT_QUOTES, 'UTF-8' ) ) . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="builder-field">
					<span><b>03</b> <?php esc_html_e( 'Heat management', 'urban-shisha' ); ?></span>
					<select id="setup-heat" name="heat" aria-label="Heat management">
						<?php foreach ( $heat as $p ) : ?>
							<option value="<?php echo esc_attr( $p['id'] ); ?>" data-price="<?php echo esc_attr( $p['price_num'] ); ?>" data-art="<?php echo esc_attr( $p['art_index'] ); ?>" data-variable="<?php echo $p['is_variable'] ? '1' : '0'; ?>" data-name="<?php echo esc_attr( $p['name'] ); ?>" data-available="<?php echo $p['can_quick_add'] ? '1' : '0'; ?>">
								<?php echo esc_html( $p['name'] . ' (' . ( null === $p['price_num'] ? __( 'Price on request', 'urban-shisha' ) : ( $p['is_variable'] ? __( 'From ', 'urban-shisha' ) : '' ) . html_entity_decode( wp_strip_all_tags( wc_price( $p['price_num'] ) ), ENT_QUOTES, 'UTF-8' ) ) . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="builder-field">
					<span><b>04</b> <?php esc_html_e( 'Charcoal', 'urban-shisha' ); ?></span>
					<select id="setup-charcoal" name="charcoal" aria-label="Charcoal">
						<?php foreach ( $charcoal as $p ) : ?>
							<option value="<?php echo esc_attr( $p['id'] ); ?>" data-price="<?php echo esc_attr( $p['price_num'] ); ?>" data-art="<?php echo esc_attr( $p['art_index'] ); ?>" data-variable="<?php echo $p['is_variable'] ? '1' : '0'; ?>" data-name="<?php echo esc_attr( $p['name'] ); ?>" data-available="<?php echo $p['can_quick_add'] ? '1' : '0'; ?>">
								<?php echo esc_html( $p['name'] . ' (' . ( null === $p['price_num'] ? __( 'Price on request', 'urban-shisha' ) : ( $p['is_variable'] ? __( 'From ', 'urban-shisha' ) : '' ) . html_entity_decode( wp_strip_all_tags( wc_price( $p['price_num'] ) ), ENT_QUOTES, 'UTF-8' ) ) . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

                <?php foreach ( array_merge( $hookahs, $bowls, $heat, $charcoal ) as $piece ) : ?>
                <template data-builder-art="<?php echo esc_attr( $piece['id'] ); ?>"><?php echo $piece['image_html']; ?></template>
                <?php endforeach; ?>
                <div class="vibe-panel">
					<div>
						<span><?php esc_html_e( 'YOUR VIBE METER', 'urban-shisha' ); ?></span>
						<strong id="vibe-label"><?php esc_html_e( '0 / 4 personalised', 'urban-shisha' ); ?></strong>
					</div>
					<div class="vibe-meter" role="meter" aria-label="<?php esc_attr_e( 'Personalised setup choices', 'urban-shisha' ); ?>" aria-valuemin="0" aria-valuemax="4" aria-valuenow="0">
						<span id="vibe-fill"></span>
					</div>
				</div>

				<div class="setup-total">
					<span><?php esc_html_e( 'Your complete setup', 'urban-shisha' ); ?></span>
					<strong id="setup-total" data-total="<?php echo esc_attr( $initial_total ); ?>"><?php echo esc_html( '₹' . number_format( $initial_total ) ); ?></strong>
				</div>

				<button class="button" type="submit" disabled>
					<?php esc_html_e( 'Add setup to bag', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-bag"/></svg>
				</button>
				<p class="builder-availability" id="builder-availability" role="status"><?php esc_html_e( 'Check availability for each selected piece before ordering.', 'urban-shisha' ); ?></p>
                <p class="builder-disclaimer"><?php echo esc_html( $disclaimer ); ?></p>
			</form>
		</div>
	</div>
</section>

<?php
/**
 * Homepage Moods section template part.
 * Links to real shopping destinations (Hookahs archive by default).
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$hookahs_term = get_term_by( 'slug', 'hookahs', 'product_cat' );
$default_url  = ( $hookahs_term && ! is_wp_error( $hookahs_term ) ) ? get_term_link( $hookahs_term ) : ( wc_get_page_permalink( 'shop' ) ?: urban_shisha_route_url( 'shop' ) );
?>
<section class="section moods wrap" id="moods" aria-labelledby="mood-title">
	<div class="section-heading reveal">
		<div>
			<p class="eyebrow"><?php echo esc_html( $args['home_moods_eyebrow'] ?? '' ); ?></p>
			<h2 id="mood-title"><?php echo wp_kses_post( $args['home_moods_heading'] ?? '' ); ?><span class="punct">.</span></h2>
		</div>
		<p class="section-note"><?php echo wp_kses_post( $args['home_moods_description'] ?? '' ); ?></p>
	</div>
	<div class="mood-grid">
		<?php
		foreach ( (array) ( $args['home_moods_cards'] ?? array() ) as $card ) :
			$key      = in_array( $card['home_moods_card_key'] ?? '', array( 'chill', 'party', 'date' ), true ) ? $card['home_moods_card_key'] : 'chill';
			$saved_url = $card['home_moods_card_link']['url'] ?? '';
			$card_url  = ( ! empty( $saved_url ) && '#collection' !== $saved_url ) ? $saved_url : $default_url;
		?>
			<a class="mood-card mood-<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $card_url ); ?>">
				<span class="mood-number"><?php echo esc_html( $card['home_moods_card_eyebrow'] ?? '' ); ?></span>
				<h3><?php echo wp_kses_post( $card['home_moods_card_title'] ?? '' ); ?></h3>
				<p><?php echo esc_html( $card['home_moods_card_copy'] ?? '' ); ?></p>
				<div class="mood-scene scene-<?php echo esc_attr( $key ); ?>" aria-hidden="true">
					<span class="scene-orbit"></span>
					<?php if ( 'chill' === $key ) : ?>
						<svg class="scene-wisp" viewBox="0 0 220 220"><path d="M70 200C0 120 200 160 110 60S170 30 160 5"/><path d="M120 210C230 150 60 120 125 50"/></svg>
					<?php elseif ( 'party' === $key ) : ?>
						<svg class="scene-spark" viewBox="0 0 240 240"><path d="M46 10 56 44 91 53 56 62 46 98 37 62 3 53 37 44Z M197 96 205 120 230 127 205 134 197 158 190 134 166 127 190 120Z"/><path class="confetti" d="m32 160 10 12m162-131 12-11m-75 154-7 16m80-21 14-1"/></svg>
						<span class="disco-ball"></span>
					<?php else : ?>
						<svg class="scene-hearts" viewBox="0 0 240 240"><use href="#i-heart" x="20" y="20" width="45" height="45"/><use href="#i-heart" x="176" y="124" width="38" height="38"/><use href="#i-heart" x="165" y="20" width="22" height="22"/></svg>
					<?php endif; ?>
					<?php echo urban_shisha_home_art( (int) ( $card['home_moods_card_image'] ?? 0 ), '', 'mood-product' ); ?>
				</div>
				<span class="mood-cta">
					<?php echo esc_html( $card['home_moods_card_cta_label'] ?? __( 'Explore', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>

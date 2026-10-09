<?php
/**
 * Homepage Hero template part with interactive stickers and ticker ribbon.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$hero = urban_shisha_get_hero_data( $args ?? array() );
?>
<div class="campaign-shell">
<section class="hero drop-hero" aria-labelledby="hero-title">
	<svg class="hero-smoke" viewBox="0 0 300 380" aria-hidden="true">
		<path d="M142 360C75 300 230 290 139 223S222 95 156 8"/>
		<path d="M157 360C236 302 100 290 171 203S88 86 175 3"/>
		<path d="M150 355C114 319 159 270 147 249S124 131 167 37"/>
	</svg>
	<div class="hero-glow" aria-hidden="true"></div>
	<div class="hero-ground" aria-hidden="true"></div>
	<p class="hero-eyebrow eyebrow"><?php echo esc_html( $hero['eyebrow'] ); ?> <span>18+ ONLY</span></p>
	<h1 id="hero-title" class="hero-title">
		<span class="headline-back"><span class="headline-piece"><?php echo esc_html( $hero['headline_back'] ); ?></span></span>
		<span class="headline-front"><span class="headline-piece"><?php echo esc_html( $hero['headline_front_first'] ); ?></span><span class="headline-piece outlined-word"><?php echo esc_html( $hero['headline_front_second'] ); ?></span></span>
	</h1>
	<div class="hero-visual">
		<div class="hero-parallax">
			<div class="hero-float">
                <?php echo urban_shisha_render_media_photo( $hero['image_id'], 'hero-photo' ); ?>
			</div>
		</div>
	</div>
	<div class="hero-sticker sticker-drop">
		<span class="sticker-inner"><?php echo wp_kses_post( $hero['sticker_drop'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
	</div>
	<div class="hero-sticker sticker-price">
		<span class="sticker-inner"><small><?php echo esc_html( $hero['price_label'] ); ?></small><?php echo esc_html( $hero['price_val'] ); ?></span>
	</div>
	<a class="hero-sticker sticker-vibe" href="#moods">
		<span class="sticker-inner"><?php echo wp_kses_post( $hero['vibe_label'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
	</a>
	<div class="hero-sticker sticker-hot">
		<span class="sticker-inner"><?php echo esc_html( $hero['hot_label'] ); ?></span>
	</div>
	<div class="hero-content">
		<p class="hero-description"><?php echo wp_kses_post( $hero['description'] ); ?></p>
		<div class="hero-ctas">
			<a class="button" href="<?php echo esc_url( $hero['cta_primary']['url'] ); ?>">
				<?php echo esc_html( $hero['cta_primary']['title'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
			<a class="button button-outline" href="<?php echo esc_url( $hero['cta_secondary']['url'] ); ?>">
				<?php echo esc_html( $hero['cta_secondary']['title'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
		<div class="setup-pulse">
			<span aria-hidden="true">🔥</span> <?php echo esc_html( $hero['pulse_text'] ); ?> <small><?php echo esc_html( $hero['pulse_note'] ); ?></small>
		</div>
		<div class="trust-chips" aria-label="<?php esc_attr_e( 'Store features', 'urban-shisha' ); ?>">
			<?php foreach ( $hero['chips'] as $chip ) : ?>
				<span><?php echo esc_html( $chip ); ?></span>
			<?php endforeach; ?>
		</div>
		<?php if ( ! empty( $hero['price_note'] ) ) : ?>
			<p class="hero-price-note"><?php echo esc_html( $hero['price_note'] ); ?></p>
		<?php endif; ?>
	</div>
	<span class="hero-side-note" aria-hidden="true"><?php echo esc_html( $hero['side_note'] ); ?></span>
</section>
<div class="ticker-frame">
	<div class="ticker" aria-hidden="true">
		<div class="ticker-track">
			<span class="ticker-copy"><?php echo wp_kses_post( $hero['ticker_text'] ); ?></span>
			<span class="ticker-copy"><?php echo wp_kses_post( $hero['ticker_text'] ); ?></span>
		</div>
	</div>
</div>
</div>

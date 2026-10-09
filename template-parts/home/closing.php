<?php
/**
 * Homepage Closing CTA band template part.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$heading   = $args['home_closing_heading'] ?? 'FOUND YOUR NEXT';
$highlight = $args['home_closing_highlight'] ?? 'STATEMENT?';
$cta       = $args['home_closing_cta'] ?? array( 'url' => '#collection', 'title' => __( 'Make it yours', 'urban-shisha' ) );
?>
<section class="closing-band">
	<div class="wrap">
		<p><?php echo wp_kses_post( $heading ); ?><br><span><?php echo esc_html( $highlight ); ?></span></p>
		<a class="button button-cream" href="<?php echo esc_url( $cta['url'] ?? '#collection' ); ?>">
			<?php echo esc_html( $cta['title'] ?? __( 'Make it yours', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	</div>
</section>

<?php
/**
 * Empty cart page template.
 *
 * Matches approved cart.html empty state layout and typography.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart-empty-state" id="cart-empty">
	<div class="empty-bag-badge" aria-hidden="true">
		<svg><use href="#i-bag"/></svg>
	</div>
	<p class="eyebrow"><?php esc_html_e( 'YOUR COLLECTION AWAITS', 'urban-shisha' ); ?></p>
	<h2><?php esc_html_e( 'YOUR NEXT FAVOURITE', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'IS WAITING.', 'urban-shisha' ); ?></span></h2>
	<p><?php esc_html_e( 'Your bag is empty. Explore statement hookahs, precision bowls and curated loadout essentials.', 'urban-shisha' ); ?></p>
	<div class="empty-actions">
		<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
			<?php esc_html_e( 'Explore the shop', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
		<a class="button button-cream" href="<?php echo esc_url( home_url( '/#builder' ) ); ?>">
			<?php esc_html_e( 'Build your setup', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	</div>
</div>

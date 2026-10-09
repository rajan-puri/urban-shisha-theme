<?php
/**
 * Commerce side drawers template part (Cart and Wishlist right-side panels).
 *
 * Reuses the approved HTML classes, dimensions, typography and empty states
 * with real WooCommerce cart and persistent wishlist product integration.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<dialog class="drawer" id="shop-dialog" aria-labelledby="drawer-title" aria-modal="true">
	<div class="drawer-header">
		<div>
			<p class="eyebrow" id="drawer-eyebrow"><?php esc_html_e( 'MAKE IT YOURS.', 'urban-shisha' ); ?></p>
			<h2 id="drawer-title"><?php esc_html_e( 'Your bag.', 'urban-shisha' ); ?></h2>
		</div>
		<button type="button" class="icon-button close-dialog" aria-label="<?php esc_attr_e( 'Close panel', 'urban-shisha' ); ?>">
			<svg aria-hidden="true"><use href="#i-close"/></svg>
		</button>
	</div>
	<div class="drawer-body" id="drawer-body">
		<div class="drawer-panel drawer-panel-cart" id="drawer-panel-cart">
			<div class="drawer-cart-content">
				<?php echo urban_shisha_get_drawer_cart_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<div class="drawer-panel drawer-panel-wishlist" id="drawer-panel-wishlist" hidden>
			<div class="drawer-wishlist-content">
				<?php echo urban_shisha_get_drawer_wishlist_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</dialog>
<div class="toast" id="toast" role="status" aria-live="polite" aria-atomic="true"></div>

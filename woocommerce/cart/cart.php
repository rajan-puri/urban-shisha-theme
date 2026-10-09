<?php
/**
 * Cart Page Template.
 *
 * Matches approved cart.html layout, cards, typography and mobile checkout bar.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

if ( WC()->cart->is_empty() ) {
	wc_get_template( 'cart/cart-empty.php' );
	return;
}

$cart_items = WC()->cart->get_cart();
$total_qty  = WC()->cart->get_cart_contents_count();
$user_id    = get_current_user_id();
$saved_ids  = function_exists( 'urban_shisha_get_user_wishlist' ) ? urban_shisha_get_user_wishlist( $user_id ) : array();
?>
<div class="cart-layout" id="cart-layout">
	<section class="cart-products-section" aria-label="<?php esc_attr_e( 'Items in your bag', 'urban-shisha' ); ?>">
		<div class="cart-table-header">
			<span class="col-product"><?php esc_html_e( 'PRODUCT', 'urban-shisha' ); ?></span>
			<span class="col-price"><?php esc_html_e( 'PRICE', 'urban-shisha' ); ?></span>
			<span class="col-qty"><?php esc_html_e( 'QUANTITY', 'urban-shisha' ); ?></span>
			<span class="col-total"><?php esc_html_e( 'TOTAL', 'urban-shisha' ); ?></span>
		</div>

		<div class="cart-items-list" id="cart-items-list">
			<?php
			foreach ( $cart_items as $cart_item_key => $cart_item ) :
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					continue;
				}

				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$product_name      = $_product->get_name();
				$product_data      = urban_shisha_get_product_data( $product_id );
				$brand_name        = $product_data['brand_name'] ?? 'URBAN SHISHA';
				$is_saved          = in_array( $product_id, $saved_ids, true );

				// Formatted variations / specs
				$item_data_html = wc_get_formatted_cart_item_data( $cart_item );

				$max_qty = $_product->get_max_purchase_quantity();
				if ( $max_qty <= 0 ) {
					$max_qty = 99;
				}
				?>
				<article class="cart-row" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" id="cart-row-<?php echo esc_attr( $cart_item_key ); ?>">
					<div class="cart-row-grid">
						<a href="<?php echo esc_url( $product_permalink ?: '#' ); ?>" class="cart-row-thumb" aria-label="<?php echo esc_attr( $product_name ); ?>">
							<?php
							if ( ! empty( $product_data['art_html'] ) ) {
								echo $product_data['art_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								echo $_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</a>
						<div class="cart-row-content">
							<div class="cart-row-info">
								<span class="cart-row-brand"><?php echo esc_html( $brand_name ); ?></span>
								<h3 class="cart-row-title">
									<a href="<?php echo esc_url( $product_permalink ?: '#' ); ?>"><?php echo esc_html( $product_name ); ?></a>
								</h3>
								<?php if ( ! empty( $item_data_html ) ) : ?>
									<div class="cart-row-variant">
										<?php echo $item_data_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
								<div class="cart-row-actions">
									<button type="button" class="cart-action-btn cart-save-btn <?php echo $is_saved ? 'is-saved' : ''; ?>" data-save="<?php echo esc_attr( $product_id ); ?>" data-cart-save="<?php echo esc_attr( $product_id ); ?>" aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>">
										<svg aria-hidden="true"><use href="#i-heart"/></svg>
										<span><?php echo $is_saved ? esc_html__( 'Saved to wishlist', 'urban-shisha' ) : esc_html__( 'Save to wishlist', 'urban-shisha' ); ?></span>
									</button>
									<button type="button" class="cart-action-btn cart-remove-btn" data-cart-remove="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from bag', 'urban-shisha' ), $product_name ) ); ?>">
										<svg aria-hidden="true"><use href="#i-close"/></svg>
										<span><?php esc_html_e( 'Remove', 'urban-shisha' ); ?></span>
									</button>
								</div>
							</div>
							<div class="cart-row-pricing">
								<div class="cart-row-unit-price">
									<span class="cart-field-label"><?php esc_html_e( 'Price:', 'urban-shisha' ); ?></span>
									<span><?php echo WC()->cart->get_product_price( $_product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</div>
								<div class="cart-row-qty">
									<span class="cart-field-label"><?php esc_html_e( 'Qty:', 'urban-shisha' ); ?></span>
									<div class="cart-qty-ctrl" role="group" aria-label="<?php echo esc_attr( sprintf( __( 'Quantity for %s', 'urban-shisha' ), $product_name ) ); ?>">
										<button type="button" class="cart-qty-btn" data-cart-qty="<?php echo esc_attr( $cart_item_key ); ?>" data-delta="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'urban-shisha' ); ?>" <?php echo $cart_item['quantity'] <= 1 ? 'disabled' : ''; ?>>−</button>
										<label class="sr-only" for="qty-<?php echo esc_attr( $cart_item_key ); ?>"><?php esc_html_e( 'Quantity', 'urban-shisha' ); ?></label>
										<input type="number" id="qty-<?php echo esc_attr( $cart_item_key ); ?>" class="cart-qty-input" data-input-qty="<?php echo esc_attr( $cart_item_key ); ?>" min="1" max="<?php echo esc_attr( $max_qty ); ?>" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" inputmode="numeric">
										<button type="button" class="cart-qty-btn" data-cart-qty="<?php echo esc_attr( $cart_item_key ); ?>" data-delta="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'urban-shisha' ); ?>" <?php echo ( $max_qty && $cart_item['quantity'] >= $max_qty ) ? 'disabled' : ''; ?>>+</button>
									</div>
								</div>
								<div class="cart-row-total">
									<span class="cart-field-label"><?php esc_html_e( 'Total:', 'urban-shisha' ); ?></span>
									<strong class="line-total-amount" id="line-total-<?php echo esc_attr( $cart_item_key ); ?>">
										<?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</strong>
								</div>
							</div>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="cart-table-footer">
			<a class="cart-continue-link" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
				<svg aria-hidden="true"><use href="#i-arrow"/></svg> <?php esc_html_e( 'Continue shopping', 'urban-shisha' ); ?>
			</a>
			<button type="button" class="cart-clear-btn" id="cart-clear"><?php esc_html_e( 'Clear bag', 'urban-shisha' ); ?></button>
		</div>
	</section>

	<aside class="cart-summary-aside" id="cart-summary-aside" aria-label="<?php esc_attr_e( 'Order summary', 'urban-shisha' ); ?>">
		<div class="order-summary-card">
			<div class="summary-header">
				<p class="eyebrow"><?php esc_html_e( 'ORDER BREAKDOWN', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'Summary.', 'urban-shisha' ); ?></h2>
			</div>

			<div class="summary-lines">
				<div class="summary-line">
					<span><?php esc_html_e( 'Subtotal', 'urban-shisha' ); ?> (<span id="summary-item-count"><?php printf( esc_html( _n( '%s item', '%s items', $total_qty, 'urban-shisha' ) ), esc_html( (string) $total_qty ) ); ?></span>)</span>
					<strong id="summary-subtotal"><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</div>

				<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
					<div class="summary-line summary-discount-line" data-coupon="<?php echo esc_attr( $code ); ?>">
						<span><?php printf( esc_html__( 'Coupon: %s', 'urban-shisha' ), esc_html( $code ) ); ?> <button type="button" class="remove-coupon-btn" data-coupon="<?php echo esc_attr( $code ); ?>" aria-label="<?php esc_attr_e( 'Remove coupon', 'urban-shisha' ); ?>">(remove)</button></span>
						<strong>-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code, WC()->cart->display_cart_ex_tax ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</div>
				<?php endforeach; ?>

				<div class="summary-line">
					<span><?php esc_html_e( 'Shipping', 'urban-shisha' ); ?></span>
					<span class="summary-shipping-note" id="summary-shipping">
						<?php
						if ( WC()->cart->needs_shipping() ) {
							$shipping_total = WC()->cart->get_cart_shipping_total();
							echo ! empty( $shipping_total ) ? $shipping_total : esc_html__( 'Calculated at checkout', 'urban-shisha' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							esc_html_e( 'Free', 'urban-shisha' );
						}
						?>
					</span>
				</div>

				<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
					<div class="summary-line">
						<span><?php esc_html_e( 'Taxes', 'urban-shisha' ); ?></span>
						<strong id="summary-tax"><?php echo wc_price( WC()->cart->get_taxes_total() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</div>
				<?php endif; ?>
			</div>

			<div class="summary-coupon-section" style="margin-top: 16px; margin-bottom: 16px;">
				<div class="summary-coupon-form" style="display: flex; gap: 8px;">
					<label for="cart-coupon-code" class="sr-only"><?php esc_html_e( 'Promo code', 'urban-shisha' ); ?></label>
					<input type="text" id="cart-coupon-code" class="form-input" placeholder="<?php esc_attr_e( 'Promo code', 'urban-shisha' ); ?>" style="flex: 1; padding: 10px 14px; font-size: 13px;">
					<button type="button" id="cart-coupon-apply" class="button button-small" style="padding: 10px 16px; font-size: 13px;">
						<?php esc_html_e( 'Apply', 'urban-shisha' ); ?>
					</button>
				</div>
				<p class="coupon-feedback" id="coupon-feedback" style="font-size: 12px; margin-top: 6px;" hidden></p>
			</div>

			<div class="summary-divider"></div>

			<div class="summary-total-line">
				<div class="summary-total-label">
					<strong><?php esc_html_e( 'Total', 'urban-shisha' ); ?></strong>
					<span class="summary-sublabel"><?php esc_html_e( 'Final total at checkout', 'urban-shisha' ); ?></span>
				</div>
				<strong class="summary-total-amount" id="summary-total">
					<?php echo WC()->cart->get_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</strong>
			</div>

			<div class="summary-actions">
				<a class="checkout-btn" id="checkout-btn" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
					<?php esc_html_e( 'Proceed to checkout', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
				<a class="summary-continue-btn" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
					<?php esc_html_e( 'Continue shopping', 'urban-shisha' ); ?>
				</a>
			</div>

			<p class="summary-preview-disclaimer"><?php esc_html_e( 'Stock, delivery charges and live payment processed securely at checkout.', 'urban-shisha' ); ?></p>

			<div class="summary-age-notice">
				<svg aria-hidden="true"><use href="#i-check"/></svg>
				<span><?php esc_html_e( 'Adults 18+ only. Age verification required at checkout.', 'urban-shisha' ); ?></span>
			</div>
		</div>
	</aside>
</div>

<nav class="cart-mobile-bar" id="cart-mobile-bar" aria-label="<?php esc_attr_e( 'Mobile checkout bar', 'urban-shisha' ); ?>">
	<div class="cart-mobile-bar-info">
		<span><?php esc_html_e( 'Total payable', 'urban-shisha' ); ?></span>
		<strong id="mobile-bar-total"><?php echo WC()->cart->get_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
	</div>
	<a class="checkout-btn" id="mobile-checkout-btn" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
		<?php esc_html_e( 'Checkout', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
	</a>
</nav>

<div class="toast" id="toast" role="status" aria-live="polite" aria-atomic="true"></div>

<?php do_action( 'woocommerce_after_cart' ); ?>

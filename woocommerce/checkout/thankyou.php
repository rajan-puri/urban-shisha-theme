<?php
/**
 * Order Received / Confirmation Template.
 *
 * Matches approved Urban Shisha visual identity: typography, ivory, violet, acid lime, cards, stickers.
 * Respects WooCommerce order permissions, key validation, and truthful payment status.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="thankyou-layout wrap" style="max-width: 1040px; margin-inline: auto; padding-block: 40px 90px;">
	<?php
	if ( ! $order ) :
		?>
		<div class="thankyou-error-card" style="background: var(--bg); border: 2px solid var(--ink); border-radius: 20px; padding: 48px 36px; text-align: center; box-shadow: 4px 4px 0 var(--ink);">
			<div class="empty-bag-badge" aria-hidden="true" style="margin-inline: auto; margin-bottom: 20px;"><svg><use href="#i-close"/></svg></div>
			<p class="eyebrow" style="color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: .08em;"><?php esc_html_e( 'ORDER LOOKUP', 'urban-shisha' ); ?></p>
			<h2 style="font-size: clamp(28px, 4vw, 44px); text-transform: uppercase; margin: 0 0 16px;"><?php esc_html_e( 'ORDER NOT FOUND', 'urban-shisha' ); ?></h2>
			<p style="color: var(--muted); max-width: 520px; margin: 0 auto 28px;"><?php esc_html_e( 'The requested order could not be located or you do not have permission to view this receipt.', 'urban-shisha' ); ?></p>
			<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
				<?php esc_html_e( 'Explore the shop', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
		<?php
		return;
	endif;

	$is_failed    = $order->has_status( 'failed' );
	$is_cancelled = $order->has_status( 'cancelled' );
	$is_cod       = 'cod' === $order->get_payment_method();
	$is_paid      = ! $is_failed && ! $is_cancelled && ! $is_cod && $order->is_paid();
	$is_pending   = ! $is_failed && ! $is_cancelled && ! $is_paid && ! $is_cod;
	$order_number = $order->get_order_number();
	$order_date   = wc_format_datetime( $order->get_date_created() );
	$order_total  = $order->get_formatted_order_total();
	$payment_title = $order->get_payment_method_title();
	$billing_email = $order->get_billing_email();
	$billing_phone = $order->get_billing_phone();
	?>

	<div class="thankyou-hero" style="border-bottom: 2px solid var(--ink); padding-bottom: 28px; margin-bottom: 36px;">
		<p class="eyebrow" style="color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: .08em; margin-bottom: 8px;">
			<?php
			if ( $is_cancelled ) {
				esc_html_e( 'ORDER STATUS: CANCELLED', 'urban-shisha' );
			} elseif ( $is_failed ) {
				esc_html_e( 'PAYMENT STATUS: FAILED', 'urban-shisha' );
			} elseif ( $is_cod ) {
				esc_html_e( 'ORDER CONFIRMED · CASH COLLECTION ON DELIVERY', 'urban-shisha' );
			} elseif ( $is_paid ) {
				esc_html_e( 'ORDER CONFIRMED · PAYMENT SUCCESSFUL', 'urban-shisha' );
			} else {
				esc_html_e( 'ORDER RECEIVED · PAYMENT PENDING', 'urban-shisha' );
			}
			?>
		</p>
		<div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
			<div>
				<h1 style="font-size: clamp(38px, 4.8vw, 68px); line-height: .98; letter-spacing: -.05em; text-transform: uppercase; margin: 0 0 12px;">
					<?php if ( $is_cancelled ) : ?>
						<?php esc_html_e( 'ORDER CANCELLED.', 'urban-shisha' ); ?><br><span style="color: var(--pop, #ff3b30);"><?php esc_html_e( 'TRANSACTION VOID.', 'urban-shisha' ); ?></span>
					<?php elseif ( $is_failed ) : ?>
						<?php esc_html_e( 'PAYMENT UNRESOLVED.', 'urban-shisha' ); ?>
					<?php elseif ( $is_cod ) : ?>
						<?php esc_html_e( 'ORDER CONFIRMED.', 'urban-shisha' ); ?><br><span style="color: var(--primary);"><?php esc_html_e( 'PAY ON DELIVERY.', 'urban-shisha' ); ?></span>
					<?php elseif ( $is_paid ) : ?>
						<?php esc_html_e( 'SETUP CONFIRMED.', 'urban-shisha' ); ?><br><span style="color: var(--primary);"><?php esc_html_e( 'RIG PREPARING.', 'urban-shisha' ); ?></span>
					<?php else : ?>
						<?php esc_html_e( 'ORDER RECEIVED.', 'urban-shisha' ); ?><br><span style="color: var(--primary);"><?php esc_html_e( 'AWAITING PAYMENT.', 'urban-shisha' ); ?></span>
					<?php endif; ?>
				</h1>
				<p style="font-size: 15px; color: var(--muted); margin: 0;">
					<?php
					if ( $is_cancelled ) {
						esc_html_e( 'This order was cancelled and will not be processed or dispatched. If this was a mistake, you can return to the shop to build a new order.', 'urban-shisha' );
					} elseif ( $is_failed ) {
						esc_html_e( 'Unfortunately the payment transaction could not be completed. Please retry or choose another payment method below.', 'urban-shisha' );
					} elseif ( $is_cod ) {
						/* translators: %s: customer email */
						printf( esc_html__( 'Thank you for choosing Urban Shisha. Your order has been placed and is being prepared. Cash payment will be collected upon physical delivery. Invoice sent to %s.', 'urban-shisha' ), esc_html( $billing_email ) );
					} elseif ( $is_paid ) {
						/* translators: %s: customer email */
						printf( esc_html__( 'Thank you for choosing Urban Shisha. Your receipt and dispatch details have been sent to %s.', 'urban-shisha' ), esc_html( $billing_email ) );
					} else {
						/* translators: %s: customer email */
						printf( esc_html__( 'Thank you. We have received your order details and sent a confirmation invoice to %s. Awaiting payment confirmation.', 'urban-shisha' ), esc_html( $billing_email ) );
					}
					?>
				</p>
			</div>

			<div style="display: flex; gap: 12px; align-items: center;">
				<?php
				$badge_bg = 'var(--soft)';
				if ( $is_cancelled || $is_failed ) {
					$badge_bg = 'var(--pop, #ff3b30)';
				} elseif ( $is_paid || $is_cod ) {
					$badge_bg = 'var(--accent)';
				}
				?>
				<span class="thankyou-status-badge" style="background: <?php echo esc_attr( $badge_bg ); ?>; color: var(--ink); border: 1.5px solid var(--ink); border-radius: 999px; padding: 6px 16px; font-size: 12px; font-weight: 800; letter-spacing: .04em; box-shadow: 2px 2px 0 var(--ink);">
					<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
				</span>
				<span class="thankyou-number-pill" style="background: var(--bg); border: 1.5px solid var(--ink); border-radius: 999px; padding: 6px 16px; font-size: 12px; font-weight: 700; box-shadow: 2px 2px 0 var(--ink);">
					#<?php echo esc_html( $order_number ); ?>
				</span>
			</div>
		</div>
	</div>

	<?php if ( $is_failed ) : ?>
		<div class="thankyou-failed-actions" style="background: var(--bg); border: 2px solid var(--ink); border-radius: 20px; padding: 24px; margin-bottom: 32px; box-shadow: 3px 3px 0 var(--ink); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
			<div>
				<h3 style="margin: 0 0 6px;"><?php esc_html_e( 'Retry your payment', 'urban-shisha' ); ?></h3>
				<p style="margin: 0; color: var(--muted); font-size: 14px;"><?php esc_html_e( 'You can securely attempt payment again without re-entering your items.', 'urban-shisha' ); ?></p>
			</div>
			<a class="button" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>">
				<?php esc_html_e( 'Pay for this order', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
	<?php endif; ?>

	<!-- 4-Metric Strip -->
	<div class="thankyou-metrics-grid">
		<div class="thankyou-metric-card" style="background: var(--bg); border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; box-shadow: 2px 2px 0 var(--ink);">
			<span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; display: block; margin-bottom: 6px;"><?php esc_html_e( 'Order Number', 'urban-shisha' ); ?></span>
			<strong style="font-size: 18px;">#<?php echo esc_html( $order_number ); ?></strong>
		</div>
		<div class="thankyou-metric-card" style="background: var(--bg); border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; box-shadow: 2px 2px 0 var(--ink);">
			<span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; display: block; margin-bottom: 6px;"><?php esc_html_e( 'Date Placed', 'urban-shisha' ); ?></span>
			<strong style="font-size: 16px;"><?php echo esc_html( $order_date ); ?></strong>
		</div>
		<div class="thankyou-metric-card" style="background: var(--bg); border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; box-shadow: 2px 2px 0 var(--ink);">
			<span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; display: block; margin-bottom: 6px;"><?php esc_html_e( 'Payment Method', 'urban-shisha' ); ?></span>
			<strong style="font-size: 16px;"><?php echo esc_html( $payment_title ?: __( 'N/A', 'urban-shisha' ) ); ?></strong>
		</div>
		<div class="thankyou-metric-card" style="background: var(--bg); border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; box-shadow: 2px 2px 0 var(--ink);">
			<span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; display: block; margin-bottom: 6px;"><?php esc_html_e( 'Total Amount', 'urban-shisha' ); ?></span>
			<strong style="font-size: 18px; color: var(--primary);"><?php echo $order_total; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
		</div>
	</div>

	<!-- 2-Column Details Layout -->
	<div class="thankyou-content-grid">
		
		<!-- Left: Items List & Instructions -->
		<div class="thankyou-left-col" style="display: flex; flex-direction: column; gap: 28px;">
			<section class="checkout-card" aria-label="<?php esc_attr_e( 'Ordered products', 'urban-shisha' ); ?>" style="background: var(--bg); border: 2px solid var(--ink); border-radius: 20px; padding: 28px; box-shadow: 3px 3px 0 var(--ink);">
				<div class="checkout-card-header" style="border-bottom: 1.5px solid var(--ink); padding-bottom: 16px; margin-bottom: 20px;">
					<h2 style="font-size: 20px; text-transform: uppercase; margin: 0;"><?php esc_html_e( 'Items in this order', 'urban-shisha' ); ?></h2>
				</div>
				<div class="thankyou-items-list" style="display: flex; flex-direction: column; gap: 16px;">
					<?php
					foreach ( $order->get_items() as $item_id => $item ) :
						$product = $item->get_product();
						$product_data = $product ? urban_shisha_get_product_data( $product ) : null;
						?>
						<div class="thankyou-item-row" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; padding-bottom: 16px; border-bottom: 1px solid rgba(20, 18, 31, 0.12);">
							<div style="display: flex; align-items: center; gap: 16px;">
								<div class="thankyou-item-thumb" style="width: 60px; height: 60px; border-radius: 10px; border: 1.5px solid var(--ink); background: var(--soft); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
									<?php
									if ( $product_data && ! empty( $product_data['art_html'] ) ) {
										echo $product_data['art_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									} elseif ( $product ) {
										echo $product->get_image( array( 60, 60 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
								</div>
								<div>
									<h4 style="margin: 0 0 4px; font-size: 15px; font-weight: 700;">
										<?php if ( $product && $product->is_visible() ) : ?>
											<a href="<?php echo esc_url( $product->get_permalink() ); ?>" style="color: var(--ink); text-decoration: underline; text-underline-offset: 3px;"><?php echo esc_html( $item->get_name() ); ?></a>
										<?php else : ?>
											<?php echo esc_html( $item->get_name() ); ?>
										<?php endif; ?>
									</h4>
									<span style="font-size: 13px; color: var(--muted);"><?php printf( esc_html__( 'Qty: %s', 'urban-shisha' ), esc_html( (string) $item->get_quantity() ) ); ?></span>
									<?php wc_display_item_meta( $item ); ?>
								</div>
							</div>
							<strong style="font-size: 15px;"><?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
						</div>
					<?php endforeach; ?>
				</div>
			</section>

			<!-- Payment Instructions (e.g. BACS bank details, COD note) -->
			<?php
			ob_start();
			do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
			do_action( 'woocommerce_thankyou', $order->get_id() );
			$gateway_instructions = ob_get_clean();

			if ( ! empty( trim( $gateway_instructions ) ) ) :
				?>
				<section class="checkout-card" style="background: var(--bg); border: 2px solid var(--ink); border-radius: 20px; padding: 28px; box-shadow: 3px 3px 0 var(--ink);">
					<div class="checkout-card-header" style="border-bottom: 1.5px solid var(--ink); padding-bottom: 16px; margin-bottom: 20px;">
						<h2 style="font-size: 20px; text-transform: uppercase; margin: 0;"><?php esc_html_e( 'Payment instructions', 'urban-shisha' ); ?></h2>
					</div>
					<div class="thankyou-instructions-body" style="font-size: 14px; line-height: 1.6;">
						<?php echo $gateway_instructions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</section>
			<?php endif; ?>

			<div class="summary-age-notice" style="margin-top: 4px;">
				<svg aria-hidden="true"><use href="#i-check"/></svg>
				<span><?php esc_html_e( 'Strictly for adults aged 18 and older. Smoking is injurious to health. Physical age verification required upon delivery.', 'urban-shisha' ); ?></span>
			</div>
		</div>

		<!-- Right: Order Breakdown & Addresses -->
		<aside class="thankyou-right-col" style="display: flex; flex-direction: column; gap: 28px;">
			<div class="order-summary-card" style="background: var(--bg); border: 2px solid var(--ink); border-radius: 20px; padding: 28px; box-shadow: 3px 3px 0 var(--ink);">
				<div class="summary-header" style="border-bottom: 1.5px solid var(--ink); padding-bottom: 16px; margin-bottom: 20px;">
					<p class="eyebrow" style="color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: .08em; margin: 0 0 6px;"><?php esc_html_e( 'RECEIPT', 'urban-shisha' ); ?></p>
					<h2 style="font-size: 24px; text-transform: uppercase; margin: 0;"><?php esc_html_e( 'Summary.', 'urban-shisha' ); ?></h2>
				</div>
				<div class="summary-lines" style="display: flex; flex-direction: column; gap: 12px;">
					<?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
						<div class="summary-line" style="display: flex; justify-content: space-between; align-items: baseline; font-size: 14px;">
							<span><?php echo esc_html( $total['label'] ); ?></span>
							<strong><?php echo wp_kses_post( $total['value'] ); ?></strong>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="summary-divider" style="height: 1.5px; background: var(--ink); margin-block: 20px;"></div>
				<div class="summary-actions" style="display: flex; flex-direction: column; gap: 12px;">
					<a class="button button-block" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
						<?php esc_html_e( 'Continue shopping', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
					</a>
					<?php if ( is_user_logged_in() ) : ?>
						<a class="button button-cream button-block" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
							<?php esc_html_e( 'View order in your account', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Delivery Address Card -->
			<div class="checkout-card" style="background: var(--bg); border: 2px solid var(--ink); border-radius: 20px; padding: 24px; box-shadow: 3px 3px 0 var(--ink);">
				<div class="checkout-card-header" style="border-bottom: 1.5px solid var(--ink); padding-bottom: 12px; margin-bottom: 16px;">
					<h3 style="font-size: 16px; text-transform: uppercase; margin: 0;"><?php esc_html_e( 'Delivery address', 'urban-shisha' ); ?></h3>
				</div>
				<address style="font-style: normal; font-size: 14px; line-height: 1.6; color: var(--ink);">
					<?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ); ?>
					<?php if ( $billing_phone ) : ?>
						<div style="margin-top: 10px; font-weight: 700;"><?php esc_html_e( 'Phone:', 'urban-shisha' ); ?> <?php echo esc_html( $billing_phone ); ?></div>
					<?php endif; ?>
				</address>
			</div>
		</aside>
	</div>
</div>

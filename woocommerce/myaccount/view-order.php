<?php
/**
 * View Single Order Template for My Account.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
?>
<div class="account-section" id="section-view-order">
	<div class="section-card">
		<div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'ORDER DETAILS', 'urban-shisha' ); ?></p>
				<h2>
					<?php
					/* translators: 1: order number */
					printf( esc_html__( 'Order #%s', 'urban-shisha' ), esc_html( $order->get_order_number() ) );
					?>
				</h2>
				<p>
					<?php
					/* translators: 1: order date, 2: order status */
					printf(
						esc_html__( 'Placed on %1$s · Status: %2$s', 'urban-shisha' ),
						esc_html( wc_format_datetime( $order->get_date_created() ) ),
						'<strong>' . esc_html( wc_get_order_status_name( $order->get_status() ) ) . '</strong>'
					);
					?>
				</p>
			</div>
			<a class="button button-small" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
				&larr; <?php esc_html_e( 'Back to all orders', 'urban-shisha' ); ?>
			</a>
		</div>

		<!-- Items List -->
		<div class="view-order-items" style="margin-block: 24px; border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; background: var(--bg);">
			<h3 style="font-size: 16px; text-transform: uppercase; margin: 0 0 16px;"><?php esc_html_e( 'Purchased Items', 'urban-shisha' ); ?></h3>
			<div style="display: flex; flex-direction: column; gap: 14px;">
				<?php
				foreach ( $order->get_items() as $item_id => $item ) :
					$product = $item->get_product();
					?>
					<div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; padding-bottom: 12px; border-bottom: 1px solid rgba(20,18,31,0.08);">
						<div>
							<strong style="font-size: 15px;"><?php echo esc_html( $item->get_name() ); ?></strong> &times; <?php echo esc_html( (string) $item->get_quantity() ); ?>
							<?php wc_display_item_meta( $item ); ?>
						</div>
						<strong><?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- Totals -->
			<div style="margin-top: 20px; border-top: 1.5px solid var(--ink); padding-top: 16px; display: flex; flex-direction: column; gap: 8px;">
				<?php foreach ( $order->get_order_item_totals() as $key => $total ) : ?>
					<div style="display: flex; justify-content: space-between; font-size: 14px;">
						<span><?php echo esc_html( $total['label'] ); ?></span>
						<strong><?php echo wp_kses_post( $total['value'] ); ?></strong>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Addresses Grid -->
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
			<div style="border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; background: var(--bg);">
				<h4 style="margin: 0 0 10px; font-size: 14px; text-transform: uppercase;"><?php esc_html_e( 'Delivery Address', 'urban-shisha' ); ?></h4>
				<address style="font-style: normal; font-size: 14px; line-height: 1.6;">
					<?php echo wp_kses_post( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() ); ?>
				</address>
			</div>

			<div style="border: 1.5px solid var(--ink); border-radius: 16px; padding: 20px; background: var(--bg);">
				<h4 style="margin: 0 0 10px; font-size: 14px; text-transform: uppercase;"><?php esc_html_e( 'Billing Address', 'urban-shisha' ); ?></h4>
				<address style="font-style: normal; font-size: 14px; line-height: 1.6;">
					<?php echo wp_kses_post( $order->get_formatted_billing_address() ); ?>
					<?php if ( $order->get_billing_phone() ) : ?>
						<div style="margin-top: 8px;"><strong><?php esc_html_e( 'Phone:', 'urban-shisha' ); ?></strong> <?php echo esc_html( $order->get_billing_phone() ); ?></div>
					<?php endif; ?>
				</address>
			</div>
		</div>
	</div>
</div>

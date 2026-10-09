<?php
/**
 * Checkout Payment Section.
 *
 * Matches approved checkout.html payment card styling and integrates live WooCommerce gateways.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}

$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : ?>
		<?php if ( ! empty( $available_gateways ) ) : ?>
			<ul class="wc_payment_methods payment_methods methods" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px;">
				<?php
				foreach ( $available_gateways as $gateway ) :
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				endforeach;
				?>
			</ul>
		<?php else : ?>
			<div class="payment-preview-card">
				<p class="eyebrow"><?php esc_html_e( 'PAYMENT GATEWAYS', 'urban-shisha' ); ?></p>
				<h3><?php esc_html_e( 'No live payment methods currently enabled.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'Payment gateways (UPI, Cards, Net Banking, COD) will be active when store checkout launches.', 'urban-shisha' ); ?></p>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="form-row place-order" style="display: none;">
		<noscript>
			<button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'urban-shisha' ); ?>"><?php esc_html_e( 'Update totals', 'urban-shisha' ); ?></button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
	</div>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}

<?php
/**
 * Checkout Form Template.
 *
 * Matches approved checkout.html 6-card single-page layout, sticky summary and real WooCommerce processing.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

// If cart is empty, show empty state matching checkout.html
if ( ! WC()->cart || WC()->cart->is_empty() ) {
	?>
	<div class="checkout-empty-state" id="checkout-empty">
		<div class="empty-bag-badge" aria-hidden="true"><svg><use href="#i-bag"/></svg></div>
		<h2><?php esc_html_e( 'YOUR BAG IS EMPTY.', 'urban-shisha' ); ?></h2>
		<p><?php esc_html_e( 'Select your hookah, bowls, or accessories from our collection before checking out.', 'urban-shisha' ); ?></p>
		<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
			<?php esc_html_e( 'Explore the shop', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	</div>
	<?php
	return;
}

$checkout = WC()->checkout();
$cart_items = WC()->cart->get_cart();
$total_qty  = WC()->cart->get_cart_contents_count();
$user_id    = get_current_user_id();
$user       = wp_get_current_user();

// Prefill values
$billing_email       = $checkout->get_value( 'billing_email' ) ?: ( $user->user_email ?? '' );
$billing_phone       = $checkout->get_value( 'billing_phone' ) ?: get_user_meta( $user_id, 'billing_phone', true );

$shipping_first_name = $checkout->get_value( 'shipping_first_name' ) ?: ( get_user_meta( $user_id, 'shipping_first_name', true ) ?: ( $user->first_name ?? '' ) );
$shipping_last_name  = $checkout->get_value( 'shipping_last_name' ) ?: ( get_user_meta( $user_id, 'shipping_last_name', true ) ?: ( $user->last_name ?? '' ) );
$shipping_address_1  = $checkout->get_value( 'shipping_address_1' ) ?: get_user_meta( $user_id, 'shipping_address_1', true );
$shipping_address_2  = $checkout->get_value( 'shipping_address_2' ) ?: get_user_meta( $user_id, 'shipping_address_2', true );
$shipping_city       = $checkout->get_value( 'shipping_city' ) ?: get_user_meta( $user_id, 'shipping_city', true );
$shipping_state      = $checkout->get_value( 'shipping_state' ) ?: get_user_meta( $user_id, 'shipping_state', true );
$shipping_postcode   = $checkout->get_value( 'shipping_postcode' ) ?: get_user_meta( $user_id, 'shipping_postcode', true );
$shipping_country    = $checkout->get_value( 'shipping_country' ) ?: 'IN';

$billing_first_name  = $checkout->get_value( 'billing_first_name' ) ?: ( get_user_meta( $user_id, 'billing_first_name', true ) ?: $shipping_first_name );
$billing_last_name   = $checkout->get_value( 'billing_last_name' ) ?: ( get_user_meta( $user_id, 'billing_last_name', true ) ?: $shipping_last_name );
$billing_address_1   = $checkout->get_value( 'billing_address_1' ) ?: ( get_user_meta( $user_id, 'billing_address_1', true ) ?: $shipping_address_1 );
$billing_address_2   = $checkout->get_value( 'billing_address_2' ) ?: ( get_user_meta( $user_id, 'billing_address_2', true ) ?: $shipping_address_2 );
$billing_city        = $checkout->get_value( 'billing_city' ) ?: ( get_user_meta( $user_id, 'billing_city', true ) ?: $shipping_city );
$billing_state       = $checkout->get_value( 'billing_state' ) ?: ( get_user_meta( $user_id, 'billing_state', true ) ?: $shipping_state );
$billing_postcode    = $checkout->get_value( 'billing_postcode' ) ?: ( get_user_meta( $user_id, 'billing_postcode', true ) ?: $shipping_postcode );
$billing_country     = $checkout->get_value( 'billing_country' ) ?: 'IN';

$billing_same = empty( $_POST ) ? 1 : ! empty( $_POST['billing_same'] );
?>

<!-- Mobile Summary Accordion -->
<details class="mobile-summary-accordion" id="mobile-summary-accordion">
	<summary class="mobile-summary-trigger">
		<span><?php esc_html_e( 'Order summary ·', 'urban-shisha' ); ?> <strong id="mobile-summary-total"><?php echo WC()->cart->get_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong> (<span id="mobile-summary-qty"><?php printf( esc_html( _n( '%s item', '%s items', $total_qty, 'urban-shisha' ) ), esc_html( (string) $total_qty ) ); ?></span>)</span>
		<svg aria-hidden="true"><use href="#i-chevron"/></svg>
	</summary>
	<div class="mobile-summary-content">
		<div class="summary-products-list">
			<?php
			foreach ( $cart_items as $cart_item_key => $cart_item ) :
				$_product = $cart_item['data'];
				if ( ! $_product || ! $_product->exists() ) {
					continue;
				}
				$product_id   = $cart_item['product_id'];
				$product_data = urban_shisha_get_product_data( $product_id );
				?>
				<div class="summary-product-item">
					<div class="summary-product-thumb">
						<?php
						if ( ! empty( $product_data['art_html'] ) ) {
							echo $product_data['art_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo $_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
					<div class="summary-product-info">
						<span class="summary-product-brand"><?php echo esc_html( $product_data['brand_name'] ?? 'URBAN SHISHA' ); ?></span>
						<h4 class="summary-product-name"><?php echo esc_html( $_product->get_name() ); ?></h4>
						<span class="summary-product-qty"><?php printf( esc_html__( 'Qty: %s', 'urban-shisha' ), esc_html( (string) $cart_item['quantity'] ) ); ?></span>
					</div>
					<strong class="summary-product-price"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="summary-calculation-lines">
			<div class="summary-calc-row">
				<span><?php esc_html_e( 'Subtotal', 'urban-shisha' ); ?></span>
				<strong id="mobile-calc-subtotal"><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</div>
			<div class="summary-calc-row">
				<span><?php esc_html_e( 'Shipping', 'urban-shisha' ); ?></span>
				<span class="summary-shipping-status"><?php echo WC()->cart->needs_shipping() ? WC()->cart->get_cart_shipping_total() ?: esc_html__( 'Calculated at checkout', 'urban-shisha' ) : esc_html__( 'Free', 'urban-shisha' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</div>
			<div class="summary-divider"></div>
			<div class="summary-payable-row">
				<div class="summary-payable-label">
					<strong><?php esc_html_e( 'Payable total', 'urban-shisha' ); ?></strong>
					<span><?php esc_html_e( 'Final total at checkout', 'urban-shisha' ); ?></span>
				</div>
				<strong class="summary-payable-amount" id="mobile-calc-payable"><?php echo WC()->cart->get_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</div>
		</div>
	</div>
</details>

<div class="checkout-layout" id="checkout-layout">
	<form name="checkout" method="post" class="checkout-form checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
		
		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<!-- 01 Contact -->
			<section class="checkout-card" aria-labelledby="contact-heading">
				<div class="checkout-card-header">
					<h2 id="contact-heading"><?php esc_html_e( '01 / Contact', 'urban-shisha' ); ?></h2>
				</div>
				<div class="form-grid">
					<div class="form-group">
						<label class="form-label" for="billing_email"><?php esc_html_e( 'Email address', 'urban-shisha' ); ?> <span class="required">*</span></label>
						<input class="form-input" id="billing_email" name="billing_email" type="email" autocomplete="email" required placeholder="name@example.com" value="<?php echo esc_attr( $billing_email ); ?>">
						<p class="field-error" id="error-billing_email" role="alert" hidden></p>
						<p class="form-helper"><?php esc_html_e( 'Order confirmation and invoice will be sent here.', 'urban-shisha' ); ?></p>
					</div>

					<div class="form-group">
						<label class="form-label" for="billing_phone"><?php esc_html_e( 'Mobile number (India)', 'urban-shisha' ); ?> <span class="required">*</span></label>
						<div class="phone-input-wrap">
							<span class="phone-prefix">+91</span>
							<input class="form-input" id="billing_phone" name="billing_phone" type="tel" inputmode="numeric" autocomplete="tel" required placeholder="9876543210" maxlength="15" value="<?php echo esc_attr( preg_replace( '/^\+?91/', '', $billing_phone ) ); ?>">
						</div>
						<p class="field-error" id="error-billing_phone" role="alert" hidden></p>
						<p class="form-helper"><?php esc_html_e( '10-digit mobile number for dispatch updates.', 'urban-shisha' ); ?></p>
					</div>

					<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
						<div class="form-group">
							<label class="checkbox-option">
								<input type="checkbox" id="createaccount" name="createaccount" value="1" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?>>
								<span class="checkbox-label">
									<strong><?php esc_html_e( 'Create an account for faster checkout next time', 'urban-shisha' ); ?></strong>
								</span>
							</label>
							<div class="create-account" style="margin-top: 14px;" hidden>
								<label class="form-label" for="account_password"><?php esc_html_e( 'Create account password', 'urban-shisha' ); ?></label>
								<input class="form-input" type="password" name="account_password" id="account_password" placeholder="<?php esc_attr_e( 'Password (8+ characters)', 'urban-shisha' ); ?>" autocomplete="new-password">
							</div>
						</div>
					<?php else : ?>
						<div class="checkout-guest-note">
							<svg aria-hidden="true"><use href="#i-check"/></svg>
							<span><?php echo is_user_logged_in() ? esc_html__( 'Logged in as ' . $user->display_name, 'urban-shisha' ) : esc_html__( 'Guest checkout active — no account required.', 'urban-shisha' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</section>

			<!-- 02 Delivery Address -->
			<section class="checkout-card" aria-labelledby="delivery-heading">
				<div class="checkout-card-header">
					<h2 id="delivery-heading"><?php esc_html_e( '02 / Delivery address', 'urban-shisha' ); ?></h2>
				</div>
				<div class="form-grid">
					<div class="form-row-2">
						<div class="form-group">
							<label class="form-label" for="shipping_first_name"><?php esc_html_e( 'First name', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="shipping_first_name" name="shipping_first_name" type="text" autocomplete="given-name" required placeholder="<?php esc_attr_e( 'First name', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $shipping_first_name ); ?>">
							<p class="field-error" id="error-shipping_first_name" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<label class="form-label" for="shipping_last_name"><?php esc_html_e( 'Last name', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="shipping_last_name" name="shipping_last_name" type="text" autocomplete="family-name" required placeholder="<?php esc_attr_e( 'Last name', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $shipping_last_name ); ?>">
							<p class="field-error" id="error-shipping_last_name" role="alert" hidden></p>
						</div>
					</div>

					<div class="form-group">
						<label class="form-label" for="shipping_address_1"><?php esc_html_e( 'Street address', 'urban-shisha' ); ?> <span class="required">*</span></label>
						<input class="form-input" id="shipping_address_1" name="shipping_address_1" type="text" autocomplete="address-line1" required placeholder="<?php esc_attr_e( 'Flat, house no., building, street', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $shipping_address_1 ); ?>">
						<p class="field-error" id="error-shipping_address_1" role="alert" hidden></p>
					</div>

					<div class="form-group">
						<label class="form-label" for="shipping_address_2">
							<span><?php esc_html_e( 'Apartment, suite, landmark', 'urban-shisha' ); ?></span>
							<span class="form-label-optional"><?php esc_html_e( 'Optional', 'urban-shisha' ); ?></span>
						</label>
						<input class="form-input" id="shipping_address_2" name="shipping_address_2" type="text" autocomplete="address-line2" placeholder="<?php esc_attr_e( 'Landmark or area', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $shipping_address_2 ); ?>">
					</div>

					<div class="form-row-2">
						<div class="form-group">
							<label class="form-label" for="shipping_city"><?php esc_html_e( 'City', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="shipping_city" name="shipping_city" type="text" autocomplete="address-level2" required placeholder="<?php esc_attr_e( 'City / Town', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $shipping_city ); ?>">
							<p class="field-error" id="error-shipping_city" role="alert" hidden></p>
						</div>

						<div class="form-group">
							<label class="form-label" for="shipping_state"><?php esc_html_e( 'State / Union Territory', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<select class="form-select" id="shipping_state" name="shipping_state" autocomplete="address-level1" required>
								<option value=""><?php esc_html_e( 'Select State / UT', 'urban-shisha' ); ?></option>
								<?php
								$states = WC()->countries->get_states( 'IN' );
								if ( is_array( $states ) ) {
									foreach ( $states as $code => $name ) {
										echo '<option value="' . esc_attr( $code ) . '" ' . selected( $shipping_state, $code, false ) . '>' . esc_html( $name ) . '</option>';
									}
								}
								?>
							</select>
							<p class="field-error" id="error-shipping_state" role="alert" hidden></p>
						</div>
					</div>

					<div class="form-row-2">
						<div class="form-group">
							<label class="form-label" for="shipping_postcode"><?php esc_html_e( 'PIN code', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="shipping_postcode" name="shipping_postcode" type="text" inputmode="numeric" autocomplete="postal-code" required placeholder="<?php esc_attr_e( '6-digit PIN', 'urban-shisha' ); ?>" maxlength="6" value="<?php echo esc_attr( $shipping_postcode ); ?>">
							<p class="field-error" id="error-shipping_postcode" role="alert" hidden></p>
						</div>

						<div class="form-group">
							<label class="form-label" for="shipping_country_display"><?php esc_html_e( 'Country', 'urban-shisha' ); ?></label>
							<input class="form-input" id="shipping_country_display" type="text" value="India" readonly aria-readonly="true">
							<input type="hidden" id="shipping_country" name="shipping_country" value="IN">
						</div>
					</div>
				</div>
			</section>

			<!-- 03 Billing Address -->
			<section class="checkout-card" aria-labelledby="billing-heading">
				<div class="checkout-card-header">
					<h2 id="billing-heading"><?php esc_html_e( '03 / Billing address', 'urban-shisha' ); ?></h2>
				</div>
				<div class="form-grid">
					<label class="checkbox-option">
						<input type="checkbox" id="billing-same" name="billing_same" value="1" <?php checked( $billing_same, 1 ); ?>>
						<span class="checkbox-label">
							<strong><?php esc_html_e( 'Billing address is same as delivery address', 'urban-shisha' ); ?></strong>
						</span>
					</label>

					<!-- Native WooCommerce shipping toggle -->
					<div id="ship-to-different-address" style="display: none;">
						<input type="checkbox" name="ship_to_different_address" value="1" checked>
					</div>

					<div id="alternate-billing-fields" class="alternate-fields" <?php echo $billing_same ? 'hidden' : ''; ?>>
						<div class="form-grid" style="margin-top: 18px;">
							<div class="form-row-2">
								<div class="form-group">
									<label class="form-label" for="billing_first_name"><?php esc_html_e( 'Billing first name', 'urban-shisha' ); ?></label>
									<input class="form-input" id="billing_first_name" name="billing_first_name" type="text" placeholder="<?php esc_attr_e( 'First name', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $billing_first_name ); ?>">
								</div>
								<div class="form-group">
									<label class="form-label" for="billing_last_name"><?php esc_html_e( 'Billing last name', 'urban-shisha' ); ?></label>
									<input class="form-input" id="billing_last_name" name="billing_last_name" type="text" placeholder="<?php esc_attr_e( 'Last name', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $billing_last_name ); ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="form-label" for="billing_address_1"><?php esc_html_e( 'Billing street address', 'urban-shisha' ); ?></label>
								<input class="form-input" id="billing_address_1" name="billing_address_1" type="text" placeholder="<?php esc_attr_e( 'Street address', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $billing_address_1 ); ?>">
							</div>
							<div class="form-row-2">
								<div class="form-group">
									<label class="form-label" for="billing_city"><?php esc_html_e( 'City', 'urban-shisha' ); ?></label>
									<input class="form-input" id="billing_city" name="billing_city" type="text" placeholder="<?php esc_attr_e( 'City', 'urban-shisha' ); ?>" value="<?php echo esc_attr( $billing_city ); ?>">
								</div>
								<div class="form-group">
									<label class="form-label" for="billing_state"><?php esc_html_e( 'State / UT', 'urban-shisha' ); ?></label>
									<select class="form-select" id="billing_state" name="billing_state">
										<option value=""><?php esc_html_e( 'Select State / UT', 'urban-shisha' ); ?></option>
										<?php
										if ( is_array( $states ) ) {
											foreach ( $states as $code => $name ) {
												echo '<option value="' . esc_attr( $code ) . '" ' . selected( $billing_state, $code, false ) . '>' . esc_html( $name ) . '</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="form-label" for="billing_postcode"><?php esc_html_e( 'PIN code', 'urban-shisha' ); ?></label>
								<input class="form-input" id="billing_postcode" name="billing_postcode" type="text" inputmode="numeric" placeholder="<?php esc_attr_e( '6-digit PIN', 'urban-shisha' ); ?>" maxlength="6" value="<?php echo esc_attr( $billing_postcode ); ?>">
							</div>
							<input type="hidden" id="billing_country" name="billing_country" value="IN">
						</div>
					</div>
				</div>
			</section>

			<!-- 04 Delivery Details -->
			<section class="checkout-card" aria-labelledby="delivery-notice-heading">
				<div class="checkout-card-header">
					<h2 id="delivery-notice-heading"><?php esc_html_e( '04 / Delivery details', 'urban-shisha' ); ?></h2>
				</div>
				<div class="delivery-notice-card">
					<div id="checkout-shipping-methods">
						<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
							<?php wc_cart_totals_shipping_html(); ?>
						<?php else : ?>
							<p><strong><?php esc_html_e( 'Service & courier confirmation:', 'urban-shisha' ); ?></strong> <?php esc_html_e( 'Delivery availability, serviceable postal codes, and shipping charges are calculated automatically across India.', 'urban-shisha' ); ?></p>
							<p class="sub-notice"><?php esc_html_e( 'All orders will be dispatched in discreet, secure protective packaging.', 'urban-shisha' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</section>

			<!-- 05 Payment Method -->
			<section class="checkout-card" aria-labelledby="payment-heading">
				<div class="checkout-card-header">
					<h2 id="payment-heading"><?php esc_html_e( '05 / Payment method', 'urban-shisha' ); ?></h2>
				</div>
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php woocommerce_order_review(); ?>
					<?php woocommerce_checkout_payment(); ?>
				</div>
			</section>

			<!-- 06 Confirmations -->
			<section class="checkout-card" aria-labelledby="confirmations-heading">
				<div class="checkout-card-header">
					<h2 id="confirmations-heading"><?php esc_html_e( '06 / Required confirmations', 'urban-shisha' ); ?></h2>
				</div>
				<div class="form-grid">
					<div class="form-group">
						<label class="checkbox-option">
							<input type="checkbox" id="confirm-age" name="confirm_age" value="1" required>
							<span class="checkbox-label">
								<strong><?php esc_html_e( 'I confirm that I am 18 years of age or older.', 'urban-shisha' ); ?></strong>
								<small><?php esc_html_e( 'Urban Shisha products are exclusively for adult use. Smoking is injurious to health.', 'urban-shisha' ); ?></small>
							</span>
						</label>
						<p class="field-error" id="error-confirm_age" role="alert" hidden></p>
					</div>

					<div class="form-group">
						<label class="checkbox-option">
							<input type="checkbox" id="confirm-terms" name="terms" value="1" required>
							<span class="checkbox-label">
								<strong>
									<?php
									/* translators: 1: terms url, 2: privacy url */
									printf(
										__( 'I agree to the <a class="policy-inline-link" target="_blank" rel="noopener noreferrer" href="%1$s">terms & conditions</a> and <a class="policy-inline-link" target="_blank" rel="noopener noreferrer" href="%2$s">privacy policy</a>.', 'urban-shisha' ),
										esc_url( urban_shisha_route_url( 'terms' ) ),
										esc_url( urban_shisha_route_url( 'privacy' ) )
									);
									?>
								</strong>
							</span>
						</label>
						<p class="field-error" id="error-confirm_terms" role="alert" hidden></p>
					</div>

					<div class="form-group">
						<label class="checkbox-option">
							<input type="checkbox" id="consent-marketing" name="consent_marketing" value="1">
							<span class="checkbox-label"><?php esc_html_e( 'Keep me updated on new drops, restocks and setup guides (optional).', 'urban-shisha' ); ?></span>
						</label>
					</div>
				</div>
			</section>

			<!-- Submit Card -->
			<div class="checkout-actions-card">
				<button type="submit" class="checkout-submit-btn" id="place_order" name="woocommerce_checkout_place_order" value="<?php esc_attr_e( 'Place order', 'urban-shisha' ); ?>">
					<?php esc_html_e( 'Place order', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</button>
				<p class="checkout-footer-disclaimer"><?php esc_html_e( 'Secure 256-bit SSL encrypted checkout. Adults aged 18+ only.', 'urban-shisha' ); ?></p>
			</div>

		<?php endif; ?>
	</form>

	<!-- Sticky Order Summary Aside -->
	<aside class="checkout-summary-aside" id="checkout-summary-aside" aria-label="<?php esc_attr_e( 'Order summary', 'urban-shisha' ); ?>">
		<div class="checkout-summary-card">
			<div class="checkout-summary-header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'ORDER BREAKDOWN', 'urban-shisha' ); ?></p>
					<h2><?php esc_html_e( 'Summary.', 'urban-shisha' ); ?></h2>
				</div>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="summary-edit-cart-link"><?php esc_html_e( 'Edit bag', 'urban-shisha' ); ?></a>
			</div>

			<div class="summary-products-list" id="summary-products-list">
				<?php
				foreach ( $cart_items as $cart_item_key => $cart_item ) :
					$_product = $cart_item['data'];
					if ( ! $_product || ! $_product->exists() ) {
						continue;
					}
					$product_id   = $cart_item['product_id'];
					$product_data = urban_shisha_get_product_data( $product_id );
					?>
					<div class="summary-product-item">
						<div class="summary-product-thumb">
							<?php
							if ( ! empty( $product_data['art_html'] ) ) {
								echo $product_data['art_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								echo $_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<div class="summary-product-info">
							<span class="summary-product-brand"><?php echo esc_html( $product_data['brand_name'] ?? 'URBAN SHISHA' ); ?></span>
							<h4 class="summary-product-name"><?php echo esc_html( $_product->get_name() ); ?></h4>
							<span class="summary-product-qty"><?php printf( esc_html__( 'Qty: %s', 'urban-shisha' ), esc_html( (string) $cart_item['quantity'] ) ); ?></span>
						</div>
						<strong class="summary-product-price"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="summary-calculation-lines">
				<div class="summary-calc-row">
					<span><?php esc_html_e( 'Subtotal', 'urban-shisha' ); ?> (<span id="summary-qty"><?php printf( esc_html( _n( '%s item', '%s items', $total_qty, 'urban-shisha' ) ), esc_html( (string) $total_qty ) ); ?></span>)</span>
					<strong id="summary-subtotal"><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</div>

				<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
					<div class="summary-calc-row summary-coupon-row">
						<span><?php printf( esc_html__( 'Coupon: %s', 'urban-shisha' ), esc_html( $code ) ); ?></span>
						<strong>-<?php echo wc_price( WC()->cart->get_coupon_discount_amount( $code, WC()->cart->display_cart_ex_tax ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</div>
				<?php endforeach; ?>

				<div class="summary-calc-row">
					<span><?php esc_html_e( 'Shipping', 'urban-shisha' ); ?></span>
					<span class="summary-shipping-status" id="summary-shipping-status">
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
					<div class="summary-calc-row">
						<span><?php esc_html_e( 'Taxes', 'urban-shisha' ); ?></span>
						<strong id="summary-taxes"><?php echo wc_price( WC()->cart->get_taxes_total() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</div>
				<?php endif; ?>
			</div>

			<div class="summary-divider"></div>

			<div class="summary-payable-row">
				<div class="summary-payable-label">
					<strong><?php esc_html_e( 'Payable total', 'urban-shisha' ); ?></strong>
					<span><?php esc_html_e( 'Includes all items and shipping', 'urban-shisha' ); ?></span>
				</div>
				<strong class="summary-payable-amount" id="summary-total"><?php echo WC()->cart->get_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
			</div>

			<div class="summary-safe-badge">
				<svg aria-hidden="true"><use href="#i-check"/></svg>
				<span><?php esc_html_e( 'Adults 18+ only. Age verification required at checkout.', 'urban-shisha' ); ?></span>
			</div>
		</div>
	</aside>
</div>

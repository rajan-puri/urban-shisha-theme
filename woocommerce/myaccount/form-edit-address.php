<?php
/**
 * Edit Address Template for My Account.
 *
 * Matches approved account.html address management styling.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? __( 'Billing address', 'urban-shisha' ) : __( 'Shipping address', 'urban-shisha' );
do_action( 'woocommerce_before_edit_account_address_form' );
?>
<div class="account-section" id="section-addresses">
	<div class="section-card">
		<?php if ( ! $load_address ) : ?>
			<div class="section-header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'DELIVERY & BILLING', 'urban-shisha' ); ?></p>
					<h2><?php esc_html_e( 'Saved addresses.', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The following addresses will be used on the checkout page by default.', 'urban-shisha' ); ?></p>
				</div>
			</div>

			<div class="u-columns woocommerce-Addresses col2-set addresses">
				<?php
				$get_addresses = apply_filters(
					'woocommerce_my_account_get_addresses',
					array(
						'billing'  => __( 'Billing address', 'urban-shisha' ),
						'shipping' => __( 'Shipping address', 'urban-shisha' ),
					),
					get_current_user_id()
				);

				foreach ( $get_addresses as $name => $title ) :
					$address = wc_get_account_formatted_address( $name );
					?>
					<div class="woocommerce-Address address-card">
                        <header class="woocommerce-Address-title title">
                            <h3><?php echo esc_html( $title ); ?></h3>
                            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit"><?php echo $address ? esc_html__( 'Edit', 'urban-shisha' ) : esc_html__( 'Add', 'urban-shisha' ); ?></a>
                        </header>
                        <address style="font-style: normal; font-size: 14px; line-height: 1.6; color: var(--ink);">
							<?php echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'urban-shisha' ); ?>
						</address>
					</div>
				<?php endforeach; ?>
			</div>

		<?php else : ?>
			<div class="section-header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'EDIT ADDRESS', 'urban-shisha' ); ?></p>
					<h2><?php echo esc_html( $page_title ); ?>.</h2>
				</div>
			</div>

			<form method="post" class="address-form" style="margin-top: 24px;">
				<div class="woocommerce-address-fields">
					<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

					<div class="woocommerce-address-fields__field-wrapper form-grid">
						<?php
						foreach ( $address as $key => $field ) {
							woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
						}
						?>
					</div>

					<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

					<div style="margin-top: 24px; display: flex; gap: 14px;">
						<button type="submit" class="button" name="save_address" value="<?php esc_attr_e( 'Save address', 'urban-shisha' ); ?>">
							<?php esc_html_e( 'Save address', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</button>
						<a class="button button-cream" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
							<?php esc_html_e( 'Cancel', 'urban-shisha' ); ?>
						</a>
						<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
						<input type="hidden" name="action" value="edit_address" />
					</div>
				</div>
			</form>
		<?php endif; ?>
	</div>
</div>
<?php
do_action( 'woocommerce_after_edit_account_address_form' );

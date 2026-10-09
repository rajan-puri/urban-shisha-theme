<?php
/**
 * Individual Payment Method Item.
 *
 * Matches approved Urban Shisha card styles with radio inputs.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>" style="border: 1.5px solid var(--ink); border-radius: 14px; padding: 16px 20px; background: var(--bg); box-shadow: 2px 2px 0 var(--ink);">
	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" style="display: flex; align-items: center; gap: 12px; font-weight: 700; cursor: pointer;">
		<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" style="accent-color: var(--primary); width: 18px; height: 18px;">
		<span><?php echo $gateway->get_title(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></span> <?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
	</label>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php echo $gateway->chosen ? '' : 'style="display:none;"'; ?> style="margin-top: 10px; font-size: 13px; color: var(--muted); padding-left: 30px;">
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>

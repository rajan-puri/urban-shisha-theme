<?php
/**
 * Lost Password Form Template.
 *
 * Matches approved auth card styling and tokens.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>
<div class="auth-view" id="auth-view" style="max-width: 580px; margin: 40px auto 80px;">
	<div class="auth-form-card" style="width: 100%;">
		<div class="auth-panel-heading" style="margin-bottom: 24px;">
			<p class="eyebrow"><?php esc_html_e( 'ACCOUNT RECOVERY', 'urban-shisha' ); ?></p>
			<h2><?php esc_html_e( 'Lost your password?', 'urban-shisha' ); ?></h2>
			<p><?php esc_html_e( 'Please enter your username or email address. You will receive a secure link to create a new password via email.', 'urban-shisha' ); ?></p>
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password auth-form">
			<div class="form-grid">
				<div class="form-group">
					<label class="form-label" for="user_login"><?php esc_html_e( 'Username or email', 'urban-shisha' ); ?> <span class="required">*</span></label>
					<input class="form-input" type="text" name="user_login" id="user_login" autocomplete="username" required placeholder="name@example.com" />
				</div>

				<?php do_action( 'woocommerce_lostpassword_form' ); ?>

				<div class="form-group" style="margin-top: 10px;">
					<input type="hidden" name="wc_reset_password" value="true" />
					<button type="submit" class="button button-block" value="<?php esc_attr_e( 'Reset password', 'urban-shisha' ); ?>">
						<?php esc_html_e( 'Reset password', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
					</button>
				</div>

				<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

				<p style="text-align: center; margin-top: 14px; font-size: 13px;">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" style="color: var(--ink); text-decoration: underline;">
						&larr; <?php esc_html_e( 'Back to login', 'urban-shisha' ); ?>
					</a>
				</p>
			</div>
		</form>
	</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );

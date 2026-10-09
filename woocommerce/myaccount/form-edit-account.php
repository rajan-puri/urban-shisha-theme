<?php
/**
 * Edit Account Details Template.
 *
 * Matches approved account.html details-grid layout with personal details and password change cards.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>
<div class="account-section" id="section-details">
	<div class="section-card">
		<div class="section-header">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'PROFILE & SECURITY', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'Account details.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Manage your personal profile information and update your password.', 'urban-shisha' ); ?></p>
			</div>
		</div>

		<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
			<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

			<div class="details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-top: 24px;">
				
				<!-- Personal Details Card -->
				<div class="details-card" style="border: 1.5px solid var(--ink); border-radius: 16px; padding: 24px; background: var(--bg); box-shadow: 2px 2px 0 var(--ink);">
					<div class="details-card-header" style="border-bottom: 1.5px solid var(--ink); padding-bottom: 12px; margin-bottom: 18px;">
						<h3 style="margin: 0 0 4px; font-size: 16px; text-transform: uppercase;"><?php esc_html_e( 'Personal details', 'urban-shisha' ); ?></h3>
						<p style="margin: 0; font-size: 13px; color: var(--muted);"><?php esc_html_e( 'Manage your contact name and email address.', 'urban-shisha' ); ?></p>
					</div>

					<div class="form-grid">
						<div class="form-group">
							<label class="form-label" for="account_first_name"><?php esc_html_e( 'First name', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input type="text" class="form-input" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" required />
						</div>

						<div class="form-group">
							<label class="form-label" for="account_last_name"><?php esc_html_e( 'Last name', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input type="text" class="form-input" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" required />
						</div>

						<div class="form-group">
							<label class="form-label" for="account_display_name"><?php esc_html_e( 'Display name', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input type="text" class="form-input" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" required />
							<p class="form-helper" style="font-size: 12px; color: var(--muted); margin-top: 4px;"><?php esc_html_e( 'This will be shown in the account greeting and reviews.', 'urban-shisha' ); ?></p>
						</div>

						<div class="form-group">
							<label class="form-label" for="account_email"><?php esc_html_e( 'Email address', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input type="email" class="form-input" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" required />
						</div>
					</div>
				</div>

				<!-- Password Change Card -->
				<div class="details-card" style="border: 1.5px solid var(--ink); border-radius: 16px; padding: 24px; background: var(--bg); box-shadow: 2px 2px 0 var(--ink);">
					<div class="details-card-header" style="border-bottom: 1.5px solid var(--ink); padding-bottom: 12px; margin-bottom: 18px;">
						<h3 style="margin: 0 0 4px; font-size: 16px; text-transform: uppercase;"><?php esc_html_e( 'Change password', 'urban-shisha' ); ?></h3>
						<p style="margin: 0; font-size: 13px; color: var(--muted);"><?php esc_html_e( 'Leave blank to keep your current password.', 'urban-shisha' ); ?></p>
					</div>

					<div class="form-grid">
						<div class="form-group">
							<label class="form-label" for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'urban-shisha' ); ?></label>
							<div class="password-field-wrap">
								<input type="password" class="form-input" name="password_current" id="password_current" autocomplete="current-password" placeholder="<?php esc_attr_e( 'Current password', 'urban-shisha' ); ?>" />
								<button type="button" class="password-toggle-btn" aria-label="<?php esc_attr_e( 'Show password', 'urban-shisha' ); ?>" aria-pressed="false" data-target="password_current">
									<svg aria-hidden="true"><use href="#i-eye"/></svg>
								</button>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'urban-shisha' ); ?></label>
							<div class="password-field-wrap">
								<input type="password" class="form-input" name="password_1" id="password_1" autocomplete="new-password" placeholder="<?php esc_attr_e( 'New password (8+ characters)', 'urban-shisha' ); ?>" />
								<button type="button" class="password-toggle-btn" aria-label="<?php esc_attr_e( 'Show password', 'urban-shisha' ); ?>" aria-pressed="false" data-target="password_1">
									<svg aria-hidden="true"><use href="#i-eye"/></svg>
								</button>
							</div>
						</div>

						<div class="form-group">
							<label class="form-label" for="password_2"><?php esc_html_e( 'Confirm new password', 'urban-shisha' ); ?></label>
							<div class="password-field-wrap">
								<input type="password" class="form-input" name="password_2" id="password_2" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Confirm new password', 'urban-shisha' ); ?>" />
								<button type="button" class="password-toggle-btn" aria-label="<?php esc_attr_e( 'Show password', 'urban-shisha' ); ?>" aria-pressed="false" data-target="password_2">
									<svg aria-hidden="true"><use href="#i-eye"/></svg>
								</button>
							</div>
						</div>
					</div>
				</div>

			</div>

			<?php do_action( 'woocommerce_edit_account_form' ); ?>

			<div style="margin-top: 24px;">
				<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
				<button type="submit" class="button" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'urban-shisha' ); ?>">
					<?php esc_html_e( 'Save changes', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</button>
				<input type="hidden" name="action" value="save_account_details" />
			</div>

			<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
		</form>
	</div>
</div>
<?php
do_action( 'woocommerce_after_edit_account_form' );

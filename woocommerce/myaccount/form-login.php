<?php
/**
 * Login & Registration form template for My Account.
 *
 * Matches approved account.html 2-column layout, branded editorial hero, and tabbed auth card.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_customer_login_form' );
?>
<div class="auth-view" id="auth-view">
	<div class="auth-layout" id="auth-layout">
		<!-- Left Column: Branded Editorial Card -->
		<div class="auth-hero-card" id="auth-hero-card">
			<div class="auth-hero-content">
				<div class="auth-hero-header">
					<p class="eyebrow"><?php esc_html_e( 'YOUR SPACE. YOUR SETUP.', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'ONE PLACE FOR', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'YOUR RIG.', 'urban-shisha' ); ?></span></h1>
				</div>
				<p class="auth-hero-lead"><?php esc_html_e( 'Welcome to your Urban Shisha account space. Track orders, keep your preferred delivery details ready, and curate your personalised wishlist.', 'urban-shisha' ); ?></p>
				<ul class="auth-feature-list">
					<li class="auth-feature-item">
						<svg aria-hidden="true"><use href="#i-check"/></svg>
						<span><?php esc_html_e( 'Unified wishlist synced with your local loadout', 'urban-shisha' ); ?></span>
					</li>
					<li class="auth-feature-item">
						<svg aria-hidden="true"><use href="#i-check"/></svg>
						<span><?php esc_html_e( 'Manage multiple delivery and billing addresses', 'urban-shisha' ); ?></span>
					</li>
					<li class="auth-feature-item">
						<svg aria-hidden="true"><use href="#i-check"/></svg>
						<span><?php esc_html_e( 'Live order status & invoice tracking across India', 'urban-shisha' ); ?></span>
					</li>
				</ul>
			</div>

			<div class="auth-demo-callout">
				<p class="callout-eyebrow"><?php esc_html_e( 'STORE DISPATCH READY', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'Curated for adults.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Urban Shisha provides an adult-only hookah catalog with strict 18+ verification and prompt customer support.', 'urban-shisha' ); ?></p>
				<a class="button button-cream auth-demo-btn" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
					<?php esc_html_e( 'Explore the shop', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
				<p class="auth-hero-disclaimer"><?php esc_html_e( 'Passphrases are securely encrypted. Strictly for adults aged 18+.', 'urban-shisha' ); ?></p>
			</div>
		</div>

		<!-- Right Column: Login & Register Forms Card -->
		<div class="auth-form-card" id="auth-form-card">
			<div class="auth-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Account access tabs', 'urban-shisha' ); ?>">
				<button type="button" class="auth-tab is-active" id="tab-login" role="tab" aria-selected="true" aria-controls="panel-login"><?php esc_html_e( 'Log In', 'urban-shisha' ); ?></button>
				<button type="button" class="auth-tab" id="tab-register" role="tab" aria-selected="false" aria-controls="panel-register"><?php esc_html_e( 'Create Account', 'urban-shisha' ); ?></button>
			</div>

			<!-- Tabpanel 1: Login -->
			<div class="auth-panel" id="panel-login" role="tabpanel" aria-labelledby="tab-login">
				<div class="auth-panel-heading">
					<h2><?php esc_html_e( 'Welcome back.', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Access your saved setups and past order records.', 'urban-shisha' ); ?></p>
				</div>
				<form class="woocommerce-form woocommerce-form-login login auth-form" id="login-form" method="post">
					<?php do_action( 'woocommerce_login_form_start' ); ?>
					<div class="form-grid">
						<div class="form-group">
							<label class="form-label" for="username"><?php esc_html_e( 'Username or email address', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="username" name="username" type="text" autocomplete="username" required placeholder="name@example.com" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>">
							<p class="field-error" id="error-login-email" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<div class="form-label-row">
								<label class="form-label" for="password"><?php esc_html_e( 'Password', 'urban-shisha' ); ?> <span class="required">*</span></label>
								<a class="forgot-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot password?', 'urban-shisha' ); ?></a>
							</div>
							<div class="password-field-wrap">
								<input class="form-input" id="password" name="password" type="password" autocomplete="current-password" required placeholder="<?php esc_attr_e( 'Enter your password', 'urban-shisha' ); ?>">
								<button type="button" class="password-toggle-btn" aria-label="<?php esc_attr_e( 'Show password', 'urban-shisha' ); ?>" aria-pressed="false" data-target="password">
									<svg aria-hidden="true"><use href="#i-eye"/></svg>
								</button>
							</div>
							<p class="field-error" id="error-login-password" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<label class="checkbox-option">
								<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever">
								<span class="checkbox-label"><?php esc_html_e( 'Remember me', 'urban-shisha' ); ?></span>
							</label>
						</div>
						<?php do_action( 'woocommerce_login_form' ); ?>
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						<button type="submit" class="button button-block" name="login" value="Log in">
							<?php esc_html_e( 'Log in to setup', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</button>
						<p class="auth-helper-note"><?php esc_html_e( 'Passphrases are securely authenticated via WooCommerce.', 'urban-shisha' ); ?></p>
						<?php do_action( 'woocommerce_login_form_end' ); ?>
					</div>
				</form>
			</div>

			<!-- Tabpanel 2: Register -->
			<div class="auth-panel" id="panel-register" role="tabpanel" aria-labelledby="tab-register" hidden>
				<div class="auth-panel-heading">
					<h2><?php esc_html_e( 'Join Urban Shisha.', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Create your personal profile for future drops and saved loadouts.', 'urban-shisha' ); ?></p>
				</div>
				<form class="woocommerce-form woocommerce-form-register register auth-form" id="register-form" method="post">
					<?php do_action( 'woocommerce_register_form_start' ); ?>
					<div class="form-grid">
						<div class="form-group">
							<label class="form-label" for="reg_name"><?php esc_html_e( 'Full name', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="reg_name" name="register_name" type="text" autocomplete="name" required placeholder="<?php esc_attr_e( 'First and last name', 'urban-shisha' ); ?>" value="<?php echo ( ! empty( $_POST['register_name'] ) ) ? esc_attr( wp_unslash( $_POST['register_name'] ) ) : ''; ?>">
							<p class="field-error" id="error-register-name" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<label class="form-label" for="reg_email"><?php esc_html_e( 'Email address', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<input class="form-input" id="reg_email" name="email" type="email" autocomplete="email" required placeholder="name@example.com" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>">
							<p class="field-error" id="error-register-email" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<label class="form-label" for="reg_password"><?php esc_html_e( 'Password (8+ characters)', 'urban-shisha' ); ?> <span class="required">*</span></label>
							<div class="password-field-wrap">
								<input class="form-input" id="reg_password" name="password" type="password" autocomplete="new-password" minlength="8" required placeholder="<?php esc_attr_e( 'Create a strong password', 'urban-shisha' ); ?>">
								<button type="button" class="password-toggle-btn" aria-label="<?php esc_attr_e( 'Show password', 'urban-shisha' ); ?>" aria-pressed="false" data-target="reg_password">
									<svg aria-hidden="true"><use href="#i-eye"/></svg>
								</button>
							</div>
							<p class="field-error" id="error-register-password" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<label class="checkbox-option">
								<input type="checkbox" id="register-age" name="register_age" value="1" required>
								<span class="checkbox-label">
									<strong><?php esc_html_e( 'I confirm that I am 18 years of age or older.', 'urban-shisha' ); ?></strong>
									<small><?php esc_html_e( 'Urban Shisha products are exclusively for adults aged 18+. Smoking is injurious to health.', 'urban-shisha' ); ?></small>
								</span>
							</label>
							<p class="field-error" id="error-register-age" role="alert" hidden></p>
						</div>
						<div class="form-group">
							<label class="checkbox-option">
								<input type="checkbox" id="register-terms" name="register_terms" value="1" required>
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
							<p class="field-error" id="error-register-terms" role="alert" hidden></p>
						</div>
						<?php do_action( 'woocommerce_register_form' ); ?>
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="button button-block" name="register" value="Register">
							<?php esc_html_e( 'Create account', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</button>
						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
do_action( 'woocommerce_after_customer_login_form' );

<?php
/**
 * My Account Dashboard Container Template.
 *
 * Matches approved account.html header band, mobile pill nav, and 2-column dashboard layout.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
?>
<div class="dashboard-view" id="dashboard-view">
	<!-- Header Banner -->
	<div class="dashboard-header-band" id="dashboard-header-band">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'YOUR ACCOUNT SPACE', 'urban-shisha' ); ?></p>
			<h1><?php esc_html_e( 'YOUR ACCOUNT.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'YOUR KIND OF SETUP.', 'urban-shisha' ); ?></span></h1>
		</div>
		<div class="dashboard-header-meta">
			<span class="demo-badge"><?php esc_html_e( 'Customer Profile', 'urban-shisha' ); ?></span>
			<span class="user-greeting"><?php printf( esc_html__( 'Welcome, %s', 'urban-shisha' ), esc_html( $current_user->display_name ) ); ?></span>
		</div>
	</div>

	<!-- Mobile Horizontally Scrollable Navigation -->
	<nav class="dashboard-mobile-nav" id="dashboard-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile account navigation', 'urban-shisha' ); ?>">
		<?php
		$menu_items = wc_get_account_menu_items();
		$icons = array(
			'dashboard'       => 'user',
			'orders'          => 'box',
			'edit-address'    => 'map-pin',
			'wishlist'        => 'heart',
			'edit-account'    => 'edit',
			'customer-logout' => 'logout',
		);
		foreach ( $menu_items as $endpoint => $label ) :
			$icon = $icons[ $endpoint ] ?? 'arrow';
			$is_active = ( 'dashboard' === $endpoint && is_account_page() && ! WC()->query->get_current_endpoint() ) || is_wc_endpoint_url( $endpoint );
			?>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="mobile-nav-pill <?php echo $is_active ? 'is-active' : ''; ?>">
				<svg aria-hidden="true"><use href="#i-<?php echo esc_attr( $icon ); ?>"/></svg>
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<!-- 2-Column Desktop Dashboard Layout -->
	<div class="dashboard-layout" id="dashboard-layout">
		<?php do_action( 'woocommerce_account_navigation' ); ?>

		<section class="account-panel-container" id="account-panel-container" tabindex="-1" aria-label="<?php esc_attr_e( 'Account content panel', 'urban-shisha' ); ?>">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</section>
	</div>
</div>

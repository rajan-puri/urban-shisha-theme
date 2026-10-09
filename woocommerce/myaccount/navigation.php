<?php
/**
 * My Account Navigation Template.
 *
 * Matches approved account.html sidebar navigation, icons and pills.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

$icons = array(
	'dashboard'       => 'user',
	'orders'          => 'box',
	'edit-address'    => 'map-pin',
	'wishlist'        => 'heart',
	'edit-account'    => 'edit',
	'customer-logout' => 'logout',
);

$wishlist_count = ( is_user_logged_in() && function_exists( 'urban_shisha_get_user_wishlist' ) ) ? count( array_filter( array_map( 'urban_shisha_get_product_data', urban_shisha_get_user_wishlist() ) ) ) : 0;
?>
<aside class="account-sidebar" id="account-sidebar" aria-label="<?php esc_attr_e( 'Account navigation', 'urban-shisha' ); ?>">
	<ul class="sidebar-nav-list">
		<?php
		foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			if ( 'customer-logout' === $endpoint ) {
				continue;
			}
			$icon      = $icons[ $endpoint ] ?? 'arrow';
			$is_active = ( 'dashboard' === $endpoint && is_account_page() && ! WC()->query->get_current_endpoint() ) || is_wc_endpoint_url( $endpoint );
			?>
			<li>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="sidebar-btn <?php echo $is_active ? 'is-active' : ''; ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
					<span class="sidebar-btn-content">
						<svg aria-hidden="true"><use href="#i-<?php echo esc_attr( $icon ); ?>"/></svg>
						<?php echo esc_html( $label ); ?>
					</span>
					<?php if ( 'wishlist' === $endpoint ) : ?>
						<span class="sidebar-pill" id="sidebar-wishlist-count"><?php echo esc_html( (string) $wishlist_count ); ?></span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<a href="<?php echo esc_url( wc_logout_url() ); ?>" class="sidebar-exit-btn">
		<svg aria-hidden="true"><use href="#i-logout"/></svg> <?php esc_html_e( 'Log out', 'urban-shisha' ); ?>
	</a>
</aside>
<?php
do_action( 'woocommerce_after_account_navigation' );

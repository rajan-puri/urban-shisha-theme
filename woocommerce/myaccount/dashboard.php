<?php
/**
 * My Account Dashboard (Overview) Template.
 *
 * Matches approved account.html 3-card overview grid and status notice.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$user_id        = get_current_user_id();
$customer_orders = wc_get_orders( array(
	'customer_id' => $user_id,
	'limit'       => -1,
	'return'      => 'ids',
) );
$order_count    = is_array( $customer_orders ) ? count( $customer_orders ) : 0;
$wishlist_count = function_exists( 'urban_shisha_get_user_wishlist' ) ? count( urban_shisha_get_user_wishlist( $user_id ) ) : 0;
?>
<div class="account-section" id="section-overview">
	<div class="section-card">
		<div class="section-header">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'SETUP SUMMARY', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'Overview.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Welcome to your dashboard. Manage your gear, addresses and saved favourites in one place.', 'urban-shisha' ); ?></p>
			</div>
		</div>

		<div class="overview-grid">
			<!-- Card 1: Orders -->
			<div class="overview-card">
				<div class="overview-card-top">
					<div class="overview-icon-badge"><svg aria-hidden="true"><use href="#i-box"/></svg></div>
					<span class="overview-metric"><?php printf( esc_html( _n( '%s order', '%s orders', $order_count, 'urban-shisha' ) ), esc_html( (string) $order_count ) ); ?></span>
				</div>
				<div class="overview-card-info">
					<h3><?php esc_html_e( 'Orders & tracking', 'urban-shisha' ); ?></h3>
					<p><?php esc_html_e( 'Track live courier dispatches and past purchases.', 'urban-shisha' ); ?></p>
				</div>
				<a class="overview-card-btn" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
					<?php esc_html_e( 'View orders', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			</div>

			<!-- Card 2: Addresses -->
			<div class="overview-card">
				<div class="overview-card-top">
					<div class="overview-icon-badge"><svg aria-hidden="true"><use href="#i-map-pin"/></svg></div>
					<span class="overview-metric" id="overview-address-count"><?php esc_html_e( 'Delivery & billing', 'urban-shisha' ); ?></span>
				</div>
				<div class="overview-card-info">
					<h3><?php esc_html_e( 'Saved addresses', 'urban-shisha' ); ?></h3>
					<p><?php esc_html_e( 'Manage your delivery and billing locations for seamless ordering.', 'urban-shisha' ); ?></p>
				</div>
				<a class="overview-card-btn" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
					<?php esc_html_e( 'Manage addresses', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			</div>

			<!-- Card 3: Wishlist -->
			<div class="overview-card">
				<div class="overview-card-top">
					<div class="overview-icon-badge"><svg aria-hidden="true"><use href="#i-heart"/></svg></div>
					<span class="overview-metric" id="overview-wishlist-count"><?php printf( esc_html( _n( '%s item saved', '%s items saved', $wishlist_count, 'urban-shisha' ) ), esc_html( (string) $wishlist_count ) ); ?></span>
				</div>
				<div class="overview-card-info">
					<h3><?php esc_html_e( 'Saved products', 'urban-shisha' ); ?></h3>
					<p><?php esc_html_e( 'Your curated wishlist of statement hookahs and accessories.', 'urban-shisha' ); ?></p>
				</div>
				<a class="overview-card-btn" href="<?php echo esc_url( wc_get_account_endpoint_url( 'wishlist' ) ); ?>">
					<?php esc_html_e( 'Open wishlist', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			</div>
		</div>

		<div class="overview-notice-box">
			<svg aria-hidden="true"><use href="#i-check"/></svg>
			<span><?php esc_html_e( 'Orders and customer records are authenticated securely via WooCommerce.', 'urban-shisha' ); ?></span>
		</div>
	</div>
</div>
<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );

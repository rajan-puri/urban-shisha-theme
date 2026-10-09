<?php
/**
 * Policy switcher sidebar and help link.
 */
defined( 'ABSPATH' ) || exit;
?>
<aside class="policy-navigation" aria-label="<?php esc_attr_e( 'Policies', 'urban-shisha' ); ?>">
	<p class="eyebrow"><?php esc_html_e( 'THE STORE DETAILS', 'urban-shisha' ); ?></p>
	<nav class="policy-switcher" aria-label="<?php esc_attr_e( 'Browse policies', 'urban-shisha' ); ?>">
		<a href="<?php echo esc_url( urban_shisha_route_url( 'shipping' ) ); ?>"<?php echo is_page( 'shipping' ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Shipping & delivery', 'urban-shisha' ); ?></a>
		<a href="<?php echo esc_url( urban_shisha_route_url( 'returns' ) ); ?>"<?php echo is_page( 'returns' ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Returns & refunds', 'urban-shisha' ); ?></a>
		<a href="<?php echo esc_url( urban_shisha_route_url( 'privacy' ) ); ?>"<?php echo is_page( array( 'privacy', 'privacy-policy' ) ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Privacy policy', 'urban-shisha' ); ?></a>
		<a href="<?php echo esc_url( urban_shisha_route_url( 'terms' ) ); ?>"<?php echo is_page( array( 'terms', 'terms-and-conditions' ) ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Terms & conditions', 'urban-shisha' ); ?></a>
		<a href="<?php echo esc_url( urban_shisha_route_url( 'age-policy' ) ); ?>"<?php echo is_page( 'age-policy' ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( '18+ policy', 'urban-shisha' ); ?></a>
	</nav>
	<a class="policy-help-link" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>">
		<?php esc_html_e( 'Need a hand? Contact us', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
	</a>
</aside>

<?php
/**
 * Dedicated Cart Page Template.
 *
 * Matches approved cart.html layout and typography while rendering real WooCommerce cart.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
?>
<main id="main" class="cart-main wrap">
	<nav class="cart-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
		<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
		<span aria-hidden="true">/</span>
		<span aria-current="page"><?php esc_html_e( 'Your Bag', 'urban-shisha' ); ?></span>
	</nav>

	<div class="cart-intro">
		<p class="eyebrow"><?php esc_html_e( 'CONFIRM YOUR LOADOUT.', 'urban-shisha' ); ?></p>
		<div class="cart-title-row">
			<h1><?php esc_html_e( 'YOUR BAG.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'YOUR NEXT SETUP.', 'urban-shisha' ); ?></span></h1>
			<span class="cart-count-badge" id="cart-item-count">
				<?php
				/* translators: %s: number of items in bag */
				printf( esc_html( _n( '%s item in your bag', '%s items in your bag', $cart_count, 'urban-shisha' ) ), esc_html( (string) $cart_count ) );
				?>
			</span>
		</div>
	</div>

	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();

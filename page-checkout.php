<?php
/**
 * Dedicated Checkout Page Template.
 *
 * Matches approved checkout.html layout and typography while processing real WooCommerce checkout.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$is_order_received = function_exists( 'is_order_received_page' ) && is_order_received_page();
?>
<main id="main" class="checkout-main wrap">
	<?php if ( ! $is_order_received ) : ?>
		<div class="checkout-intro">
			<p class="eyebrow"><?php esc_html_e( 'CHECKOUT', 'urban-shisha' ); ?></p>
			<h1><?php esc_html_e( 'MAKE IT YOURS.', 'urban-shisha' ); ?></h1>
			<p><?php esc_html_e( 'A few details, then you’re all set.', 'urban-shisha' ); ?></p>
		</div>
	<?php endif; ?>

	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();

<?php
/**
 * Dedicated My Account Page Template.
 *
 * Matches approved account.html layout and typography while connecting real WooCommerce account data.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="account-main wrap">
	<nav class="account-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
		<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
		<span aria-hidden="true">/</span>
		<span aria-current="page"><?php esc_html_e( 'My Account', 'urban-shisha' ); ?></span>
	</nav>

	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</main>
<?php
get_footer();

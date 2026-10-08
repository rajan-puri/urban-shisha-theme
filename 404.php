<?php
/** Missing-page fallback. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="wrap wp-foundation-content">
	<p class="eyebrow">404 / URBAN SHISHA</p>
	<h1><?php esc_html_e( 'Page not found.', 'urban-shisha' ); ?></h1>
	<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
</main>
<?php get_footer(); ?>

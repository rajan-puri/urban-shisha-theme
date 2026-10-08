<?php
/** Standard WordPress fallback; the approved homepage is converted in the next step. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="wrap wp-foundation-content">
<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/content' ); ?>
	<?php endwhile; ?>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<h1><?php esc_html_e( 'Urban Shisha', 'urban-shisha' ); ?></h1>
<?php endif; ?>
</main>
<?php get_footer(); ?>

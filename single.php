<?php
/** Single post fallback. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="wrap wp-foundation-content">
<?php while ( have_posts() ) : the_post(); ?>
	<?php get_template_part( 'template-parts/content', 'page' ); ?>
	<?php the_post_navigation(); ?>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>

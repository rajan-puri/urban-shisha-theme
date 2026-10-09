<?php
/** Basic editable page scaffold; designed page templates follow in later steps. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="wrap wp-foundation-content">
<?php while ( have_posts() ) : the_post(); ?>
	<?php get_template_part( 'template-parts/content', 'page' ); ?>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>

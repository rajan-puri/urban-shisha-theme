<?php
/** Standard page/single content scaffold. */
defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h1><?php the_title(); ?></h1>
	<div class="entry-content"><?php the_content(); ?></div>
	<?php wp_link_pages(); ?>
</article>

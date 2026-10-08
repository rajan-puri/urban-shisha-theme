<?php
/** Shared footer, dialogs and WordPress footer hook. */
defined( 'ABSPATH' ) || exit;
get_template_part( 'template-parts/site', 'footer' );
get_template_part( 'template-parts/age', 'gate' );
wp_footer();
?>
</body>
</html>

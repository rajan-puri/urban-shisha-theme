<?php
/** Resolve organized page templates through the native WordPress hierarchy. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_page_template_hierarchy( $templates ) {
	$organized = array();
	foreach ( $templates as $template ) {
		// Root/child-theme overrides retain their native priority.
		$organized[] = $template;
		if ( basename( $template ) === $template && is_file( get_theme_file_path( 'pages/' . $template ) ) ) {
			$organized[] = 'pages/' . $template;
		}
	}
	return $organized;
}
add_filter( 'page_template_hierarchy', 'urban_shisha_page_template_hierarchy' );

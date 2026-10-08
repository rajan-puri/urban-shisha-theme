<?php
/** Shared document head and approved navigation shell. */
defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#6C2BFF">
<?php if ( ! has_site_icon() ) : ?>
<link rel="icon" href="<?php echo esc_url( get_theme_file_uri( 'assets/favicon.svg' ) ); ?>" type="image/svg+xml">
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'urban-shisha-foundation' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/site', 'header' ); ?>

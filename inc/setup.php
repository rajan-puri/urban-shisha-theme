<?php
/** Theme features; setup does not change store options or create content. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_setup() {
	load_theme_textdomain( 'urban-shisha', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 320, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'woocommerce' );
	register_nav_menus(
		array(
			'primary'        => __( 'Main navigation', 'urban-shisha' ),
			'footer-shop'    => __( 'Footer: Shop', 'urban-shisha' ),
			'footer-discover'=> __( 'Footer: Discover', 'urban-shisha' ),
			'footer-care'    => __( 'Footer: Customer care', 'urban-shisha' ),
			'footer-account' => __( 'Footer: Account & policies', 'urban-shisha' ),
		)
	);
}
add_action( 'after_setup_theme', 'urban_shisha_setup' );

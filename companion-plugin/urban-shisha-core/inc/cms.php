<?php
/** SCF admin screens and version-controlled field definitions. No store-setting writes. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_core_register_cms() {
    if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }
    acf_add_options_page( array(
        'page_title' => 'Urban Shisha — Global settings',
        'menu_title' => 'Urban Shisha',
        'menu_slug' => 'urban-shisha-settings',
        'capability' => 'manage_options',
        'redirect' => false,
        'icon_url' => 'dashicons-store',
        'position' => 58,
    ) );
    foreach ( glob( dirname( __DIR__ ) . '/fields/group-us-*.json' ) as $file ) {
        $group = json_decode( file_get_contents( $file ), true );
        if ( is_array( $group ) && isset( $group['key'], $group['fields'] ) ) {
            // Generic editorial fields do not belong on WooCommerce system pages.
            if ( 'group_us_pages' === $group['key'] && function_exists( 'wc_get_page_id' ) ) {
                foreach ( array( 'shop', 'cart', 'checkout', 'myaccount' ) as $page ) {
                    $page_id = wc_get_page_id( $page );
                    if ( $page_id > 0 ) {
                        $group['location'][0][] = array( 'param' => 'post', 'operator' => '!=', 'value' => (string) $page_id );
                    }
                }
            }
            acf_add_local_field_group( $group );
        }
    }
}
add_action( 'acf/init', 'urban_shisha_core_register_cms' );

function urban_shisha_core_dependency_notice() {
    if ( ! function_exists( 'acf_add_local_field_group' ) && current_user_can( 'manage_options' ) ) {
        echo '<div class="notice notice-error"><p>Urban Shisha content settings require Secure Custom Fields to be active.</p></div>';
    }
}
add_action( 'admin_notices', 'urban_shisha_core_dependency_notice' );

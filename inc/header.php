<?php
/** Header menus, shared category-card data and WooCommerce cart fragments. */
defined( 'ABSPATH' ) || exit;
add_action( 'after_setup_theme', function () {
    register_nav_menu( 'header-shortcuts', __( 'Header: Mega menu shortcuts', 'urban-shisha' ) );
} );

function urban_shisha_header_cards() {
    $cards = array();
    foreach ( (array) urban_shisha_get_option( 'mega_cards', array() ) as $row ) {
        $term = ! empty( $row['mega_card_category'] ) ? get_term( (int) $row['mega_card_category'], 'product_cat' ) : null;
        $link = urban_shisha_parse_link( $row['mega_card_link'] ?? null );
        if ( $term && ! is_wp_error( $term ) ) {
            $url = get_term_link( $term );
            if ( is_wp_error( $url ) ) {
                continue;
            }
            $link = array( 'url' => $url, 'title' => $term->name, 'target' => '' );
        }
        $label = trim( $row['mega_card_label'] ?? '' );
        $label = $label ?: ( $term && ! is_wp_error( $term ) ? $term->name : '' );
        if ( ! $label || ! $link ) {
            continue;
        }
        $image = absint( $row['mega_card_image'] ?? 0 );
        if ( ! $image && $term && ! is_wp_error( $term ) ) {
            $image = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
        }
        $cards[] = array( 'label' => $label, 'url' => $link['url'], 'target' => $link['target'], 'image' => $image );
    }
    return $cards;
}

function urban_shisha_header_link_attrs( $item ) {
    $attributes = ' href="' . esc_url( $item->url ) . '"';
    if ( '_blank' === $item->target ) {
        $attributes .= ' target="_blank" rel="noopener noreferrer"';
    }
    if ( in_array( 'current-menu-item', (array) $item->classes, true ) ) {
        $attributes .= ' aria-current="page"';
    }
    if ( $item->attr_title ) {
        $attributes .= ' title="' . esc_attr( $item->attr_title ) . '"';
    }
    return $attributes;
}

/** Same native menu tree on desktop/mobile; preserve hierarchy and edited URLs. */
function urban_shisha_render_header_menu( $mobile = false, $location = 'primary' ) {
    $menu = urban_shisha_get_nav_menu_object( $location );
    $items = $menu ? wp_get_nav_menu_items( $menu ) : array();
    if ( ! $items ) {
        return;
    }
    _wp_menu_item_classes_by_context( $items );
    $tree = array();
    $mega_id = 0;
    foreach ( $items as $item ) {
        if ( ! empty( $item->_invalid ) ) {
            continue;
        }
        $tree[ (int) $item->menu_item_parent ][] = $item;
        if ( ! $mega_id && ! $item->menu_item_parent && in_array( 'urban-mega-trigger', (array) $item->classes, true ) ) {
            $mega_id = (int) $item->ID;
        }
    }
    $has_cards = (bool) urban_shisha_header_cards();
    $render = function ( $parent, $depth = 0 ) use ( &$render, $tree, $mega_id, $has_cards, $mobile, $location ) {
        foreach ( $tree[ $parent ] ?? array() as $index => $item ) {
            $children = ! empty( $tree[ (int) $item->ID ] );
            $classes = array_map( 'sanitize_html_class', array_filter( (array) $item->classes ) );
            $classes[] = 'header-menu-item';
            if ( $children ) {
                $classes[] = 'menu-item-has-children';
            }
            if ( $mobile ) {
                $classes[] = 'mobile-menu-item';
            }
            echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
            echo '<a' . urban_shisha_header_link_attrs( $item ) . '>' . esc_html( $item->title );
            if ( $mobile && ! $depth && 'primary' === $location ) {
                echo ' <span>' . esc_html( sprintf( '%02d', $index + 1 ) ) . '</span>';
            }
            echo '</a>';
            if ( ! $mobile && ! $depth && (int) $item->ID === $mega_id && $has_cards ) {
                echo '<button type="button" class="mega-toggle" id="mega-toggle" aria-expanded="false" aria-controls="category-menu" aria-label="' . esc_attr( sprintf( __( 'Show %s categories', 'urban-shisha' ), $item->title ) ) . '"><svg aria-hidden="true"><use href="#i-chevron"/></svg></button>';
            }
            if ( $children ) {
                $id = 'header-submenu-' . ( $mobile ? 'mobile-' : 'desktop-' ) . $item->ID;
                echo '<button type="button" class="nav-submenu-toggle" aria-controls="' . esc_attr( $id ) . '" aria-expanded="false" aria-label="' . esc_attr( sprintf( __( 'Show links under %s', 'urban-shisha' ), $item->title ) ) . '"><svg aria-hidden="true"><use href="#i-chevron"/></svg></button>';
                echo '<div class="' . ( $mobile ? 'mobile-sub-menu' : 'sub-menu' ) . '" id="' . esc_attr( $id ) . '" hidden>';
                $render( (int) $item->ID, $depth + 1 );
                echo '</div>';
            }
            echo '</div>';
        }
    };
    $render( 0 );
}

function urban_shisha_render_desktop_nav() {
    urban_shisha_render_header_menu();
}

function urban_shisha_render_mobile_nav_links() {
    urban_shisha_render_header_menu( true );
}

function urban_shisha_cart_count_markup() {
    $count = urban_shisha_get_cart_count();
    return '<span class="cart-count" aria-live="polite" aria-atomic="true" aria-label="' . esc_attr( sprintf( _n( '%d item in bag', '%d items in bag', $count, 'urban-shisha' ), $count ) ) . '">' . esc_html( (string) $count ) . '</span>';
}

function urban_shisha_header_cart_fragments( $fragments ) {
    $fragments['.site-header .cart-count'] = urban_shisha_cart_count_markup();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'urban_shisha_header_cart_fragments' );

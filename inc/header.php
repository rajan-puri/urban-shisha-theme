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

function urban_shisha_get_drawer_cart_html() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
        ob_start();
        ?>
        <div class="empty-state">
            <h3><?php esc_html_e( 'A little room for character.', 'urban-shisha' ); ?></h3>
            <p><?php esc_html_e( 'Your bag is empty. Start with a hookah or find the finishing touches.', 'urban-shisha' ); ?></p>
            <a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>" data-browse>
                <?php esc_html_e( 'Explore the collection', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    ob_start();
    ?>
    <div class="cart-items">
        <?php
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                continue;
            }

            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
            $thumbnail         = $_product->get_image( 'woocommerce_thumbnail', array( 'alt' => esc_attr( $_product->get_name() ) ) );
            $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
        ?>
        <div class="cart-item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
            <a class="cart-thumb" href="<?php echo esc_url( $product_permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'urban-shisha' ), $_product->get_name() ) ); ?>">
                <?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
            <div>
                <h3><?php echo wp_kses_post( $_product->get_name() ); ?></h3>
                <p><?php echo $product_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                <div class="quantity-control">
                    <button type="button" class="qty-btn" data-quantity-key="<?php echo esc_attr( $cart_item_key ); ?>" data-delta="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'urban-shisha' ); ?>">−</button>
                    <span aria-label="<?php echo esc_attr( sprintf( __( 'Quantity %d', 'urban-shisha' ), $cart_item['quantity'] ) ); ?>"><?php echo esc_html( (string) $cart_item['quantity'] ); ?></span>
                    <button type="button" class="qty-btn" data-quantity-key="<?php echo esc_attr( $cart_item_key ); ?>" data-delta="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'urban-shisha' ); ?>">+</button>
                </div>
            </div>
            <button type="button" class="remove-item" data-remove-key="<?php echo esc_attr( $cart_item_key ); ?>"><?php esc_html_e( 'Remove', 'urban-shisha' ); ?></button>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="cart-summary">
        <span><?php esc_html_e( 'Subtotal', 'urban-shisha' ); ?></span>
        <strong><?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
    </div>
    <a class="view-full-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
        <?php esc_html_e( 'View full bag', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
    </a>
    <a class="button cart-checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
        <?php esc_html_e( 'Checkout', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
    </a>
    <?php
    return ob_get_clean();
}

function urban_shisha_get_drawer_wishlist_html( $product_ids = null ) {
    if ( null === $product_ids ) {
        if ( is_user_logged_in() && function_exists( 'urban_shisha_get_user_wishlist' ) ) {
            $product_ids = urban_shisha_get_user_wishlist();
        } else {
            $product_ids = array();
        }
    }

    $products = function_exists( 'urban_shisha_get_wishlist_products_data' ) ? urban_shisha_get_wishlist_products_data( $product_ids ) : array();

    if ( empty( $products ) ) {
        ob_start();
        ?>
        <div class="empty-state">
            <h3><?php esc_html_e( 'Keep an eye on your favourites.', 'urban-shisha' ); ?></h3>
            <p><?php esc_html_e( 'Tap the heart on a product to save it here for your next visit.', 'urban-shisha' ); ?></p>
            <a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>" data-browse>
                <?php esc_html_e( 'Discover your favourites', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    ob_start();
    ?>
    <div class="wishlist-products">
        <?php foreach ( $products as $item ) : ?>
        <article class="product-card" data-id="<?php echo esc_attr( $item['id'] ); ?>">
            <div class="product-image">
                <a class="product-picture" href="<?php echo esc_url( $item['permalink'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'urban-shisha' ), $item['name'] ) ); ?>">
                    <?php echo $item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
                <button type="button" class="save-product is-saved" data-save="<?php echo esc_attr( $item['id'] ); ?>" data-product-id="<?php echo esc_attr( $item['id'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from wishlist', 'urban-shisha' ), $item['name'] ) ); ?>" aria-pressed="true">
                    <svg aria-hidden="true"><use href="#i-heart"/></svg>
                </button>
            </div>
            <div class="product-info">
                <div>
                    <p class="product-type"><?php echo esc_html( $item['category'] ); ?></p>
                    <a class="product-name" href="<?php echo esc_url( $item['permalink'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
                    <p class="price"><?php echo $item['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

function urban_shisha_header_cart_fragments( $fragments ) {
    $fragments['.site-header .cart-count'] = urban_shisha_cart_count_markup();
    $fragments['div.drawer-cart-content'] = '<div class="drawer-cart-content">' . urban_shisha_get_drawer_cart_html() . '</div>';
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'urban_shisha_header_cart_fragments' );

function urban_shisha_ajax_update_cart_qty() {
    check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error( array( 'message' => __( 'Cart unavailable.', 'urban-shisha' ) ) );
    }

    $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
    $delta         = isset( $_POST['delta'] ) ? intval( $_POST['delta'] ) : 0;
    $cart          = WC()->cart->get_cart();

    if ( ! $cart_item_key || ! isset( $cart[ $cart_item_key ] ) ) {
        wp_send_json_error( array( 'message' => __( 'Item not found in cart.', 'urban-shisha' ) ) );
    }

    $current_qty = $cart[ $cart_item_key ]['quantity'];
    $new_qty     = max( 0, $current_qty + $delta );

    if ( $new_qty <= 0 ) {
        WC()->cart->remove_cart_item( $cart_item_key );
    } else {
        WC()->cart->set_quantity( $cart_item_key, $new_qty );
    }

    wp_send_json_success( array(
        'count'    => WC()->cart->get_cart_contents_count(),
        'html'     => urban_shisha_get_drawer_cart_html(),
        'subtotal' => WC()->cart->get_cart_subtotal(),
    ) );
}
add_action( 'wp_ajax_urban_shisha_update_cart_qty', 'urban_shisha_ajax_update_cart_qty' );
add_action( 'wp_ajax_nopriv_urban_shisha_update_cart_qty', 'urban_shisha_ajax_update_cart_qty' );

function urban_shisha_ajax_remove_cart_item() {
    check_ajax_referer( 'urban_shisha_cart_nonce', 'nonce' );
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error( array( 'message' => __( 'Cart unavailable.', 'urban-shisha' ) ) );
    }

    $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
    if ( ! $cart_item_key || ! WC()->cart->get_cart_item( $cart_item_key ) ) {
        wp_send_json_error( array( 'message' => __( 'Item not found in cart.', 'urban-shisha' ) ) );
    }

    WC()->cart->remove_cart_item( $cart_item_key );

    wp_send_json_success( array(
        'count'    => WC()->cart->get_cart_contents_count(),
        'html'     => urban_shisha_get_drawer_cart_html(),
        'subtotal' => WC()->cart->get_cart_subtotal(),
    ) );
}
add_action( 'wp_ajax_urban_shisha_remove_cart_item', 'urban_shisha_ajax_remove_cart_item' );
add_action( 'wp_ajax_nopriv_urban_shisha_remove_cart_item', 'urban_shisha_ajax_remove_cart_item' );

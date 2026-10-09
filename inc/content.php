<?php
/** Shared SCF settings, branding, link and native menu helpers. */
defined( 'ABSPATH' ) || exit;

/**
 * Retrieve a global setting with fallback when SCF is unavailable.
 * Preserves deliberately saved empty/false values.
 *
 * @param string $name Setting key.
 * @param mixed  $default Default value if unset in database.
 * @return mixed
 */
function urban_shisha_get_option( $name, $default = null ) {
    $missing = new stdClass();
    $raw = get_option( 'options_' . $name, $missing );
    if ( $raw === $missing ) {
        return $default;
    }
    return function_exists( 'get_field' ) ? get_field( $name, 'option' ) : maybe_unserialize( $raw );
}

/** Assigned native WordPress menu, with no hardcoded link fallback. */
function urban_shisha_get_nav_menu_object( $location ) {
    $locations = get_nav_menu_locations();
    return ! empty( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : false;
}

/**
 * Get brand name.
 *
 * @return string
 */
function urban_shisha_get_brand_name() {
	$name = urban_shisha_get_option( 'brand_name' );
	if ( null !== $name && '' !== trim( (string) $name ) ) {
		return sanitize_text_field( $name );
	}
	return 'Urban Shisha';
}

/**
 * Render brand logo or text wordmark safely.
 *
 * @param string $context 'header', 'footer', or 'dialog'.
 * @param bool   $is_dark Whether rendered over dark/primary background.
 */
function urban_shisha_render_brand_logo( $context = 'header', $is_dark = false ) {
	$brand_name = urban_shisha_get_brand_name();
	$logo_id = 0;
	if ( $is_dark ) {
		$logo_id = (int) urban_shisha_get_option( 'brand_logo_light', 0 );
	}
	if ( ! $logo_id ) {
		$logo_id = (int) urban_shisha_get_option( 'brand_logo', 0 );
	}

	if ( $logo_id ) {
		$img = wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			array(
				'class' => 'custom-logo custom-logo-' . esc_attr( $context ),
				'alt'   => esc_attr( $brand_name ),
			)
		);
		if ( $img ) {
			echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}
	}

	if ( 'Urban Shisha' === $brand_name ) {
		if ( 'header' === $context ) {
			echo 'URBAN<span>SHISHA</span><span class="brand-dot">®</span>';
		} else {
			echo 'URBAN<span>SHISHA</span>';
		}
	} else {
		echo esc_html( $brand_name );
	}
}

/**
 * Parse and validate SCF link array or string safely.
 *
 * @param mixed $link_field SCF link data.
 * @return array|false
 */
function urban_shisha_parse_link( $field ) {
    $data = is_array( $field ) ? $field : array( 'url' => $field );
    $url = isset( $data['url'] ) && is_string( $data['url'] ) ? esc_url_raw( trim( $data['url'] ) ) : '';
    if ( ! $url ) {
        return false;
    }
    return array(
        'url' => $url,
        'title' => sanitize_text_field( $data['title'] ?? '' ),
        'target' => '_blank' === ( $data['target'] ?? '' ) ? '_blank' : '',
    );
}

/**
 * Render a footer menu column with native menu binding.
 *
 * @param string $location Menu location slug.
 * @param string $column_id Element ID for accordion aria-controls.
 * @param string $default_title Default heading if menu object has no name.
 */
function urban_shisha_render_footer_column( $location, $column_id, $default_title ) {
	$menu = urban_shisha_get_nav_menu_object( $location );
	$heading = ( $menu && ! empty( $menu->name ) ) ? $menu->name : $default_title;
	$items = $menu ? wp_get_nav_menu_items( $menu ) : false;

	if ( empty( $items ) || ! is_array( $items ) ) {
		return;
	}

	?>
	<div class="footer-column">
		<button class="footer-toggle" aria-expanded="true" aria-controls="<?php echo esc_attr( $column_id ); ?>"><?php echo esc_html( $heading ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></button>
		<ul id="<?php echo esc_attr( $column_id ); ?>">
			<?php foreach ( $items as $item ) :
				$url = $item->url;

				// Resolve ?category= to canonical WooCommerce taxonomy URL if needed.
				if ( is_string( $url ) && ( false !== strpos( $url, '?category=' ) || false !== strpos( $url, '&category=' ) ) ) {
					$query_str = wp_parse_url( $url, PHP_URL_QUERY );
					if ( $query_str ) {
						parse_str( $query_str, $query_params );
						if ( ! empty( $query_params['category'] ) ) {
							$term = get_term_by( 'slug', sanitize_title( $query_params['category'] ), 'product_cat' );
							if ( $term && ! is_wp_error( $term ) ) {
								$canonical_link = get_term_link( $term, 'product_cat' );
								if ( ! is_wp_error( $canonical_link ) ) {
									$url = $canonical_link;
								}
							}
						}
					}
				}

				$raw_classes = ! empty( $item->classes ) && is_array( $item->classes ) ? array_filter( $item->classes ) : array();
				$is_wishlist = in_array( 'open-wishlist', $raw_classes, true )
					|| in_array( 'urban-wishlist-trigger', $raw_classes, true )
					|| in_array( 'menu-item-wishlist', $raw_classes, true )
					|| ( is_string( $item->url ) && false !== strpos( $item->url, 'tab=wishlist' ) && ( false !== stripos( $item->title, 'wishlist' ) || false !== stripos( $item->title, 'saved' ) ) );

				$classes = $raw_classes;
				if ( $is_wishlist && ! in_array( 'open-wishlist', $classes, true ) ) {
					$classes[] = 'open-wishlist';
				}

				$class_attr = ! empty( $classes ) ? ' class="' . esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ) . '"' : '';
				$data_attr  = $is_wishlist ? ' data-open="wishlist"' : '';
			?>
				<li<?php echo $class_attr; ?>><a href="<?php echo esc_url( $url ); ?>"<?php echo $class_attr; ?><?php echo $data_attr; ?>><?php echo esc_html( $item->title ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Get real WooCommerce cart count or 0.
 *
 * @return int
 */
function urban_shisha_get_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}
	return 0;
}

<?php
/** Shared SCF settings, branding, link and native menu helpers. */
defined( 'ABSPATH' ) || exit;

/**
 * Retrieve a global setting with fallback when SCF is unavailable.
 *
 * @param string $name Setting key.
 * @param mixed  $default Default value if unset or empty.
 * @return mixed
 */
function urban_shisha_get_option( $name, $default = null ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, 'option' );
		if ( null !== $value && '' !== $value ) {
			return $value;
		}
	}

	$raw = get_option( 'options_' . $name, null );
	if ( null === $raw ) {
		$raw = get_option( $name, null );
	}

	if ( null !== $raw && '' !== $raw ) {
		$unserialized = maybe_unserialize( $raw );
		if ( is_numeric( $unserialized ) && in_array( $name, array( 'mega_cards', 'owners', 'social_links' ), true ) ) {
			$count = (int) $unserialized;
			$items = array();
			$subfields = array(
				'mega_cards'   => array( 'mega_card_label', 'mega_card_image', 'mega_card_link' ),
				'owners'       => array( 'owner_name', 'owner_phone' ),
				'social_links' => array( 'social_label', 'social_url' ),
			);
			$fields = $subfields[ $name ];
			for ( $i = 0; $i < $count; $i++ ) {
				$row = array();
				foreach ( $fields as $subfield ) {
					$sub_val = get_option( "options_{$name}_{$i}_{$subfield}", null );
					$row[ $subfield ] = maybe_unserialize( $sub_val );
				}
				$items[] = $row;
			}
			return $items;
		}
		return $unserialized;
	}

	return $default;
}

/**
 * Filter nav menu locations to recognize inactive theme assignments stored in theme_mods.
 *
 * @param array $locations Registered nav menu locations.
 * @return array
 */
function urban_shisha_filter_nav_menu_locations( $locations ) {
	if ( empty( $locations ) || ! is_array( $locations ) ) {
		$theme_mods = get_option( 'theme_mods_urban-shisha-theme', array() );
		if ( ! empty( $theme_mods['nav_menu_locations'] ) && is_array( $theme_mods['nav_menu_locations'] ) ) {
			return $theme_mods['nav_menu_locations'];
		}
	}
	return $locations;
}
add_filter( 'theme_mod_nav_menu_locations', 'urban_shisha_filter_nav_menu_locations' );

/**
 * Get assigned menu object for a location.
 *
 * @param string $location Theme menu location.
 * @return WP_Term|false
 */
function urban_shisha_get_nav_menu_object( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations ) || ! is_array( $locations ) ) {
		$theme_mods = get_option( 'theme_mods_urban-shisha-theme', array() );
		if ( ! empty( $theme_mods['nav_menu_locations'] ) && is_array( $theme_mods['nav_menu_locations'] ) ) {
			$locations = $theme_mods['nav_menu_locations'];
		}
	}

	if ( ! empty( $locations[ $location ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $location ] );
		if ( $menu ) {
			return $menu;
		}
	}
	return false;
}

/**
 * Get brand name.
 *
 * @return string
 */
function urban_shisha_get_brand_name() {
	$name = urban_shisha_get_option( 'brand_name' );
	return ! empty( $name ) ? sanitize_text_field( $name ) : 'Urban Shisha';
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

	if ( 'header' === $context ) {
		if ( 'Urban Shisha' === $brand_name ) {
			echo 'URBAN<span>SHISHA</span><span class="brand-dot">®</span>';
		} else {
			echo esc_html( $brand_name );
		}
	} elseif ( 'footer' === $context ) {
		if ( 'Urban Shisha' === $brand_name ) {
			echo 'URBAN<span>SHISHA</span>';
		} else {
			echo esc_html( $brand_name );
		}
	} else {
		if ( 'Urban Shisha' === $brand_name ) {
			echo 'URBAN<span>SHISHA</span>';
		} else {
			echo esc_html( $brand_name );
		}
	}
}

/**
 * Parse SCF link array or string safely.
 *
 * @param mixed $link_field SCF link data.
 * @return array|false
 */
function urban_shisha_parse_link( $link_field ) {
	if ( empty( $link_field ) ) {
		return false;
	}
	if ( is_array( $link_field ) && ! empty( $link_field['url'] ) ) {
		return array(
			'url'    => esc_url( $link_field['url'] ),
			'title'  => ! empty( $link_field['title'] ) ? sanitize_text_field( $link_field['title'] ) : '',
			'target' => ! empty( $link_field['target'] ) ? sanitize_text_field( $link_field['target'] ) : '',
		);
	}
	if ( is_string( $link_field ) && '' !== trim( $link_field ) ) {
		return array(
			'url'    => esc_url( $link_field ),
			'title'  => '',
			'target' => '',
		);
	}
	return false;
}

/**
 * Render desktop primary navigation items.
 */
function urban_shisha_render_desktop_nav() {
	$menu = urban_shisha_get_nav_menu_object( 'primary' );
	$items = $menu ? wp_get_nav_menu_items( $menu ) : false;

	if ( ! empty( $items ) && is_array( $items ) ) {
		$top_level = array();
		$children  = array();
		foreach ( $items as $item ) {
			$parent_id = (int) $item->menu_item_parent;
			if ( 0 === $parent_id ) {
				$top_level[] = $item;
			} else {
				$children[ $parent_id ][] = $item;
			}
		}

		$has_explicit_trigger = false;
		foreach ( $top_level as $item ) {
			if ( in_array( 'urban-mega-trigger', (array) $item->classes, true ) ) {
				$has_explicit_trigger = true;
				break;
			}
		}

		foreach ( $top_level as $item ) {
			$is_trigger = in_array( 'urban-mega-trigger', (array) $item->classes, true );
			if ( ! $has_explicit_trigger && 'shop' === strtolower( trim( $item->title ) ) ) {
				$is_trigger = true;
			}

			if ( $is_trigger ) {
				echo '<button class="mega-toggle" id="mega-toggle" aria-expanded="false" aria-controls="category-menu">' . esc_html( $item->title ) . ' <svg aria-hidden="true"><use href="#i-chevron"/></svg></button>';
			} else {
				$has_children = ! empty( $children[ (int) $item->ID ] );
				if ( $has_children ) {
					echo '<div class="menu-item-has-children">';
					echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
					echo '<ul class="sub-menu">';
					foreach ( $children[ (int) $item->ID ] as $child ) {
						echo '<li><a href="' . esc_url( $child->url ) . '">' . esc_html( $child->title ) . '</a></li>';
					}
					echo '</ul>';
					echo '</div>';
				} else {
					echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';
				}
			}
		}
		return;
	}

	// Approved default links
	?>
	<button class="mega-toggle" id="mega-toggle" aria-expanded="false" aria-controls="category-menu">Shop <svg aria-hidden="true"><use href="#i-chevron"/></svg></button>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'shop', array( 'category' => 'hookahs' ) ) ); ?>">Hookahs</a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'shop', array( 'category' => 'accessories' ) ) ); ?>">Accessories</a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'wholesale' ) ); ?>">Bulk Orders</a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) . '#guides' ); ?>">The journal</a>
	<?php
}

/**
 * Render mobile primary navigation links.
 */
function urban_shisha_render_mobile_nav_links() {
	$menu = urban_shisha_get_nav_menu_object( 'primary' );
	$items = $menu ? wp_get_nav_menu_items( $menu ) : false;

	if ( ! empty( $items ) && is_array( $items ) ) {
		$counter = 1;
		foreach ( $items as $item ) {
			if ( 0 === (int) $item->menu_item_parent ) {
				$pill = sprintf( '%02d', $counter++ );
				echo '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . ' <span>' . esc_html( $pill ) . '</span></a>';
			}
		}
		return;
	}

	// Approved default links
	?>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'shop', array( 'category' => 'hookahs' ) ) ); ?>">Shop hookahs <span>01</span></a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'shop', array( 'category' => 'accessories' ) ) ); ?>">Accessories <span>02</span></a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) . '#arrivals' ); ?>">New arrivals <span>03</span></a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'wholesale' ) ); ?>">Bulk Orders <span>04</span></a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) . '#guides' ); ?>">The journal <span>05</span></a>
	<a href="<?php echo esc_url( urban_shisha_route_url( 'account' ) ); ?>">My account <span>06</span></a>
	<?php
}

/**
 * Render a footer menu column with native menu binding and fallback.
 *
 * @param string $location Menu location slug.
 * @param string $column_id Element ID for accordion aria-controls.
 * @param string $default_title Default heading.
 * @param array  $fallback_items Array of array('title' => ..., 'url' => ...).
 */
function urban_shisha_render_footer_column( $location, $column_id, $default_title, $fallback_items = array() ) {
	$menu = urban_shisha_get_nav_menu_object( $location );
	$heading = ( $menu && ! empty( $menu->name ) ) ? $menu->name : $default_title;
	$items = $menu ? wp_get_nav_menu_items( $menu ) : false;

	?>
	<div class="footer-column">
		<button class="footer-toggle" aria-expanded="true" aria-controls="<?php echo esc_attr( $column_id ); ?>"><?php echo esc_html( $heading ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></button>
		<ul id="<?php echo esc_attr( $column_id ); ?>">
			<?php
			if ( ! empty( $items ) && is_array( $items ) ) :
				foreach ( $items as $item ) :
					?>
					<li><a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a></li>
					<?php
				endforeach;
			else :
				foreach ( $fallback_items as $item ) :
					?>
					<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a></li>
					<?php
				endforeach;
			endif;
			?>
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

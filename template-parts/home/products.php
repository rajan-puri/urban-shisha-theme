<?php
/**
 * Homepage Products template part.
 * Dispatches hookah_rail, kit, accessories, and arrivals layouts.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$layout = $args['layout'] ?? 'hookah_rail';

if ( 'hookah_rail' === $layout ) :
	$eyebrow     = $args['home_hookah_rail_eyebrow'] ?? 'THE MAIN EVENT.';
	$heading     = $args['home_hookah_rail_heading'] ?? 'Hookahs worth discovering';
	$description = $args['home_hookah_rail_description'] ?? "Good looks. Considered details.<br>Find the one that feels like you.";
	$selected    = $args['home_hookah_rail_products'] ?? array();
	$limit       = ! empty( $args['home_hookah_rail_limit'] ) ? (int) $args['home_hookah_rail_limit'] : 100;

	$query_args = array(
		'limit'    => $limit,
		'category' => array( 'hookahs', 'portable-hookahs' ),
	);
	if ( ! empty( $selected ) ) {
		$query_args['post__in'] = $selected;
	}
	$hookahs = urban_shisha_query_products( $query_args );
	?>
	<section class="section collection wrap" id="collection" aria-labelledby="collection-title">
		<div class="section-heading reveal">
			<div>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 id="collection-title"><?php echo esc_html( $heading ); ?><span class="punct">.</span></h2>
			</div>
			<span class="section-note"><?php echo wp_kses_post( $description ); ?></span>
		</div>
		<div class="filter-row" role="group" aria-label="<?php esc_attr_e( 'Filter hookahs by budget', 'urban-shisha' ); ?>">
			<button type="button" class="filter-button is-active" data-budget="all" aria-pressed="true"><?php esc_html_e( 'All hookahs', 'urban-shisha' ); ?></button>
			<?php foreach ( urban_shisha_home_budget_ranges() as $key => $range ) : ?>
			<button type="button" class="filter-button" data-budget="<?php echo esc_attr( $key ); ?>" aria-pressed="false"><?php echo esc_html( $range['label'] ); ?></button>
			<?php endforeach; ?>
		</div>
		<div class="rail-controls">
			<span><?php esc_html_e( 'DRAG TO DISCOVER / SWIPE ON MOBILE', 'urban-shisha' ); ?></span>
			<div>
				<button type="button" class="rail-arrow" data-rail="-1" aria-label="<?php esc_attr_e( 'Previous hookahs', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
				<button type="button" class="rail-arrow" data-rail="1" aria-label="<?php esc_attr_e( 'Next hookahs', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
			</div>
		</div>
		<div class="product-grid hookah-grid hookah-rail" id="hookah-products" tabindex="0" aria-label="<?php esc_attr_e( 'Hookah collection, scroll horizontally', 'urban-shisha' ); ?>">
			<?php foreach ( $hookahs as $prod_data ) : ?>
				<?php get_template_part( 'template-parts/product-card', null, array( 'product_data' => $prod_data ) ); ?>
			<?php endforeach; ?>
		</div>
	</section>

<?php elseif ( 'kit' === $layout ) :
	$eyebrow     = $args['home_kit_eyebrow'] ?? 'THE EXTRAS MAKE IT YOURS.';
	$heading     = $args['home_kit_heading'] ?? 'COMPLETE YOUR KIT';
	$description = $args['home_kit_description'] ?? "Got the hookah? Now pick<br>the pieces that go with it.";
	$bottom_text = $args['home_kit_bottom_text'] ?? __( 'Want to put the whole thing together?', 'urban-shisha' );
	$bottom_cta  = $args['home_kit_bottom_cta'] ?? array( 'url' => '#builder', 'title' => __( 'Build your setup', 'urban-shisha' ) );
	?>
	<section class="section kit-section" id="details" aria-labelledby="detail-title">
		<div class="wrap">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<h2 id="detail-title"><?php echo esc_html( $heading ); ?><span class="punct">.</span></h2>
				</div>
				<p class="section-note"><?php echo wp_kses_post( $description ); ?></p>
			</div>
			<div class="kit-grid">
				<?php foreach ( (array) ( $args['home_kit_cards'] ?? array() ) as $card ) : ?>
				<a class="kit-card" href="<?php echo esc_url( $card['home_kit_card_link']['url'] ?: '#accessories' ); ?>" data-nav-category="<?php echo esc_attr( $card['home_kit_card_slug'] ?? '' ); ?>">
					<div class="kit-top"><span><?php echo esc_html( $card['home_kit_card_eyebrow'] ); ?></span><svg aria-hidden="true"><use href="#i-arrow"/></svg></div>
					<?php echo urban_shisha_home_art( (int) $card['home_kit_card_image'], '', 'kit-photo', 'div' ); ?>
					<div class="kit-copy"><h3><?php echo esc_html( $card['home_kit_card_title'] ); ?></h3><p><?php echo esc_html( $card['home_kit_card_copy'] ); ?></p><span><?php echo esc_html( $card['home_kit_card_cta'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span></div>
				</a>
				<?php endforeach; ?>
			</div>
			<div class="kit-bottom">
				<p><?php echo esc_html( $bottom_text ); ?></p>
				<a href="<?php echo esc_url( $bottom_cta['url'] ?? '#builder' ); ?>" class="text-link">
					<?php echo esc_html( $bottom_cta['title'] ?? __( 'Build your setup', 'urban-shisha' ) ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			</div>
		</div>
	</section>

<?php elseif ( 'accessories' === $layout ) :
	$eyebrow     = $args['home_accessories_eyebrow'] ?? 'SMALL DETAILS. BIG DIFFERENCE.';
	$heading     = $args['home_accessories_heading'] ?? 'Complete your setup';
	$shop_url    = wc_get_page_permalink( 'shop' ) ?: urban_shisha_route_url( 'shop' );

	$cta         = $args['home_accessories_cta'] ?? array();
	$cta_title   = ! empty( $cta['title'] ) ? $cta['title'] : __( 'Find your combination', 'urban-shisha' );
	$cta_url     = ! empty( $cta['url'] ) && '#builder' !== $cta['url'] ? $cta['url'] : $shop_url;

	$limit       = ! empty( $args['home_accessories_limit'] ) ? (int) $args['home_accessories_limit'] : 12;

	$tabs_config = array(
		'all'      => array(
			'label'      => __( 'All essentials', 'urban-shisha' ),
			'btn_label'  => __( 'View all accessories', 'urban-shisha' ),
			'categories' => array( 'accessories', 'bowls', 'heat-management', 'hoses-mouthpieces', 'tools-spares', 'charcoal', 'coal-burners', 'hookah-bags', 'tongs' ),
			'term_slug'  => 'accessories',
		),
		'bowls'    => array(
			'label'      => __( 'Bowls', 'urban-shisha' ),
			'btn_label'  => __( 'View all bowls', 'urban-shisha' ),
			'categories' => array( 'bowls' ),
			'term_slug'  => 'bowls',
		),
		'heat'     => array(
			'label'      => __( 'Heat management', 'urban-shisha' ),
			'btn_label'  => __( 'View all heat management', 'urban-shisha' ),
			'categories' => array( 'heat-management' ),
			'term_slug'  => 'heat-management',
		),
		'hoses'    => array(
			'label'      => __( 'Hoses & tips', 'urban-shisha' ),
			'btn_label'  => __( 'View all hoses & tips', 'urban-shisha' ),
			'categories' => array( 'hoses-mouthpieces' ),
			'term_slug'  => 'hoses-mouthpieces',
		),
		'care'     => array(
			'label'      => __( 'Tools & spares', 'urban-shisha' ),
			'btn_label'  => __( 'View all tools & spares', 'urban-shisha' ),
			'categories' => array( 'tools-spares', 'tongs' ),
			'term_slug'  => 'tools-spares',
		),
		'charcoal' => array(
			'label'      => __( 'Charcoal', 'urban-shisha' ),
			'btn_label'  => __( 'View all charcoal', 'urban-shisha' ),
			'categories' => array( 'charcoal' ),
			'term_slug'  => 'charcoal',
		),
	);

	$tab_product_map  = array();
	$all_tab_products = array();

	foreach ( $tabs_config as $tab_key => &$tab_data ) {
		$term      = get_term_by( 'slug', $tab_data['term_slug'], 'product_cat' );
		$term_link = ( $term && ! is_wp_error( $term ) ) ? get_term_link( $term ) : '';
		if ( is_wp_error( $term_link ) || empty( $term_link ) ) {
			$term_link = add_query_arg( 'category', $tab_data['term_slug'], $shop_url );
		}
		$tab_data['archive_url'] = $term_link;

		$prods = urban_shisha_query_products(
			array(
				'limit'    => $limit,
				'category' => $tab_data['categories'],
			)
		);

		foreach ( $prods as $p ) {
			$pid = $p['id'];
			if ( ! isset( $all_tab_products[ $pid ] ) ) {
				$all_tab_products[ $pid ] = $p;
			}
			$tab_product_map[ $pid ][] = $tab_key;
		}
	}
	unset( $tab_data );
	?>
	<section class="section accessories wrap" id="accessories" aria-labelledby="accessory-title">
		<div class="section-heading reveal">
			<div>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 id="accessory-title"><?php echo esc_html( $heading ); ?><span class="punct">.</span></h2>
			</div>
			<?php if ( ! empty( $cta_url ) ) : ?>
				<a class="text-link" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_title ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			<?php endif; ?>
		</div>
		<div class="accessory-filters" role="group" aria-label="<?php esc_attr_e( 'Accessory categories', 'urban-shisha' ); ?>">
			<?php foreach ( $tabs_config as $tab_key => $tab_info ) : ?>
				<button type="button" class="filter-button<?php echo 'all' === $tab_key ? ' is-active' : ''; ?>" data-accessory="<?php echo esc_attr( $tab_key ); ?>" data-archive-url="<?php echo esc_url( $tab_info['archive_url'] ); ?>" data-btn-label="<?php echo esc_attr( $tab_info['btn_label'] ); ?>" aria-pressed="<?php echo 'all' === $tab_key ? 'true' : 'false'; ?>">
					<?php echo esc_html( $tab_info['label'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<div class="product-grid accessory-grid" id="accessory-products">
			<?php foreach ( $all_tab_products as $pid => $prod_data ) :
				$tabs_for_product = $tab_product_map[ $pid ] ?? array();
				$is_in_all = in_array( 'all', $tabs_for_product, true );
			?>
				<?php
				get_template_part(
					'template-parts/product-card',
					null,
					array(
						'product_data'   => $prod_data,
						'accessory_tabs' => implode( ' ', $tabs_for_product ),
						'hidden'         => ! $is_in_all,
					)
				);
				?>
			<?php endforeach; ?>
		</div>
		<div class="accessory-footer">
			<a href="<?php echo esc_url( $tabs_config['all']['archive_url'] ); ?>" class="button button-outline accessory-view-all" id="accessory-view-all">
				<span class="view-all-label"><?php echo esc_html( $tabs_config['all']['btn_label'] ); ?></span>
				<svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
	</section>

<?php elseif ( 'arrivals' === $layout ) :
	$eyebrow     = $args['home_arrivals_eyebrow'] ?? 'A LITTLE SOMETHING NEW.';
	$heading     = $args['home_arrivals_heading'] ?? 'Fresh in the collection';
	$shop_url    = wc_get_page_permalink( 'shop' ) ?: urban_shisha_route_url( 'shop' );
	$cta         = $args['home_arrivals_cta'] ?? array();
	$cta_title   = ! empty( $cta['title'] ) ? $cta['title'] : __( 'Keep exploring', 'urban-shisha' );
	$cta_url     = ( ! empty( $cta['url'] ) && '#collection' !== $cta['url'] ) ? $cta['url'] : $shop_url;
	$selected    = $args['home_arrivals_products'] ?? array();
	$limit       = $args['home_arrivals_limit'] ?? 4;

	$arrivals = urban_shisha_query_products(
		array(
			'limit'    => $limit,
			'orderby'  => 'date',
			'order'    => 'DESC',
			'post__in' => $selected ?: null,
		)
	);
	?>
	<section class="section arrivals wrap" id="arrivals" aria-labelledby="arrival-title">
		<div class="section-heading reveal">
			<div>
				<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h2 id="arrival-title"><?php echo esc_html( $heading ); ?><span class="punct">.</span></h2>
			</div>
			<?php if ( ! empty( $cta_url ) ) : ?>
				<a class="text-link" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_title ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			<?php endif; ?>
		</div>
		<div class="product-grid" id="arrival-products">
			<?php foreach ( $arrivals as $prod_data ) : ?>
				<?php get_template_part( 'template-parts/product-card', null, array( 'product_data' => $prod_data ) ); ?>
			<?php endforeach; ?>
		</div>
	</section>

<?php endif; ?>

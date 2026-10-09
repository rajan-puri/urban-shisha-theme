<?php
/**
 * WooCommerce product archive and shop template for Urban Shisha.
 *
 * Faithfully matches the approved shop.html design while connecting
 * real WooCommerce catalog queries, taxonomy filters, and editable SCF fields.
 *
 * @package UrbanShishaTheme
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$fields     = urban_shisha_get_shop_fields();
$params     = urban_shisha_sanitize_shop_params();
$result     = urban_shisha_get_shop_products( $params );
$products   = $result['products'];
$categories = urban_shisha_get_shop_categories();
$brands     = urban_shisha_get_shop_brands();
$shop_url   = urban_shisha_route_url( 'shop' );
?>
<main id="main" class="shop-main">
	<div class="shop-intro">
		<div class="wrap">
			<nav class="shop-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
				<span aria-hidden="true">/</span>
				<?php if ( function_exists( 'is_product_category' ) && is_product_category() ) : ?>
					<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'urban-shisha' ); ?></a>
					<span aria-hidden="true">/</span>
					<span aria-current="page"><?php single_term_title(); ?></span>
				<?php elseif ( function_exists( 'is_tax' ) && is_tax( 'product_brand' ) ) : ?>
					<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'urban-shisha' ); ?></a>
					<span aria-hidden="true">/</span>
					<span aria-current="page"><?php single_term_title(); ?></span>
				<?php else : ?>
					<span aria-current="page"><?php esc_html_e( 'Shop', 'urban-shisha' ); ?></span>
				<?php endif; ?>
			</nav>
			<div class="shop-intro-copy">
				<div>
					<p class="eyebrow"><?php echo esc_html( $fields['hero_eyebrow'] ); ?></p>
					<h1><?php echo wp_kses_post( $fields['hero_heading'] ); ?></h1>
					<p><?php echo esc_html( $fields['hero_description'] ); ?></p>
				</div>
				<div class="shop-poster" aria-hidden="true">
					<span><?php echo wp_kses_post( $fields['hero_poster_word'] ); ?></span>
					<img src="<?php echo esc_url( $fields['hero_poster_image_url'] ); ?>" alt="" width="340" height="340">
					<span class="shop-poster-sticker"><?php echo wp_kses_post( $fields['hero_poster_sticker'] ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<section class="shop-catalog wrap" id="catalog" aria-labelledby="catalog-title">
		<div class="shop-category-tabs" aria-label="<?php esc_attr_e( 'Browse categories', 'urban-shisha' ); ?>">
			<a href="<?php echo esc_url( $shop_url ); ?>"<?php echo empty( $params['category'] ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All products', 'urban-shisha' ); ?></a>
			<?php foreach ( $categories as $slug => $cat ) : ?>
				<?php $is_active = in_array( $slug, $params['category'], true ); ?>
				<a href="<?php echo esc_url( add_query_arg( 'category', $slug, $shop_url ) ); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $cat['name'] ); ?>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="shop-layout">
			<aside class="shop-sidebar" aria-label="<?php esc_attr_e( 'Product filters', 'urban-shisha' ); ?>">
				<div id="filter-panel">
					<form id="shop-filters">
						<div class="filter-heading">
							<h2><?php echo esc_html( $fields['filter_heading'] ); ?></h2>
							<button type="button" class="filter-reset" data-clear><?php esc_html_e( 'Reset', 'urban-shisha' ); ?></button>
						</div>

						<details class="filter-group" open>
							<summary><?php esc_html_e( 'Category', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
							<div>
								<?php foreach ( $categories as $slug => $cat ) : ?>
									<label class="filter-option">
										<input type="checkbox" name="category" value="<?php echo esc_attr( $slug ); ?>"<?php checked( in_array( $slug, $params['category'], true ) ); ?>>
										<span><?php echo esc_html( $cat['name'] ); ?></span>
										<span class="filter-count" data-count-category="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( (string) $cat['count'] ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</details>

						<details class="filter-group" open>
							<summary><?php esc_html_e( 'Price range', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
							<div>
								<div class="price-inputs">
									<label>Min ₹<input id="price-min" name="min" type="number" min="0" max="20000" step="1" value="<?php echo esc_attr( (string) $params['min'] ); ?>" inputmode="numeric"></label>
									<span aria-hidden="true">—</span>
									<label>Max ₹<input id="price-max" name="max" type="number" min="0" max="20000" step="1" value="<?php echo esc_attr( (string) $params['max'] ); ?>" inputmode="numeric"></label>
								</div>
								<label class="sr-only" for="price-range"><?php esc_html_e( 'Maximum price', 'urban-shisha' ); ?></label>
								<input type="range" id="price-range" min="0" max="20000" step="100" value="<?php echo esc_attr( (string) $params['max'] ); ?>">
								<p class="filter-price-note" id="price-note">Up to ₹<?php echo esc_html( number_format( (float) $params['max'] ) ); ?></p>
							</div>
						</details>

						<details class="filter-group" open>
							<summary><?php esc_html_e( 'Brand', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
							<div>
								<?php foreach ( $brands as $brand ) : ?>
									<label class="filter-option">
										<input type="checkbox" name="brand" value="<?php echo esc_attr( $brand['id'] ); ?>"<?php checked( in_array( $brand['id'], $params['brand'], true ) ); ?>>
										<span><?php echo esc_html( $brand['name'] ); ?></span>
										<span class="filter-count" data-count-brand="<?php echo esc_attr( $brand['id'] ); ?>"><?php echo esc_html( (string) $brand['count'] ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</details>

						<details class="filter-group" open>
							<summary><?php esc_html_e( 'The edit', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
							<div>
								<label class="filter-option">
									<input type="checkbox" name="new" value="1"<?php checked( $params['new'] ); ?>>
									<span><?php esc_html_e( 'New in the collection', 'urban-shisha' ); ?></span>
								</label>
							</div>
						</details>

						<div class="filter-help">
							<span><?php echo esc_html( $fields['filter_help_eyebrow'] ); ?></span>
							<a href="<?php echo esc_url( $fields['filter_help_url'] ); ?>">
								<?php echo esc_html( $fields['filter_help_text'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
							</a>
						</div>
					</form>
				</div>
			</aside>

			<div class="shop-results">
				<div class="shop-toolbar">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'YOUR NEXT FAVOURITE IS HERE.', 'urban-shisha' ); ?></p>
						<h2 id="catalog-title"><?php esc_html_e( 'All products', 'urban-shisha' ); ?><span class="punct">.</span></h2>
						<p id="result-count" role="status" aria-live="polite">
							<?php
							printf(
								/* translators: %d: number of products */
								_n( '%d product in your edit', '%d products in your edit', count( $products ), 'urban-shisha' ),
								count( $products )
							);
							?>
						</p>
					</div>
					<label class="shop-sort"><?php esc_html_e( 'Sort by', 'urban-shisha' ); ?>
						<select id="shop-sort">
							<option value="featured"<?php selected( $params['sort'], 'featured' ); ?>><?php esc_html_e( 'Featured', 'urban-shisha' ); ?></option>
							<option value="price-low"<?php selected( $params['sort'], 'price-low' ); ?>><?php esc_html_e( 'Price: low to high', 'urban-shisha' ); ?></option>
							<option value="price-high"<?php selected( $params['sort'], 'price-high' ); ?>><?php esc_html_e( 'Price: high to low', 'urban-shisha' ); ?></option>
							<option value="name"<?php selected( $params['sort'], 'name' ); ?>><?php esc_html_e( 'Name: A–Z', 'urban-shisha' ); ?></option>
						</select>
					</label>
				</div>

				<div class="shop-search-row">
					<label class="shop-search">
						<svg aria-hidden="true"><use href="#i-search"/></svg>
						<span class="sr-only"><?php esc_html_e( 'Search products', 'urban-shisha' ); ?></span>
						<input id="shop-search" type="search" placeholder="<?php esc_attr_e( 'Search hookahs, bowls, brands…', 'urban-shisha' ); ?>" autocomplete="off" value="<?php echo esc_attr( $params['q'] ); ?>">
					</label>
					<button class="shop-filter-open" id="filter-open" aria-haspopup="dialog" aria-controls="filter-dialog">
						<?php esc_html_e( 'Filters', 'urban-shisha' ); ?> <span id="filter-total">0</span>
					</button>
				</div>

				<div class="active-filters" id="active-filters" role="group" aria-label="<?php esc_attr_e( 'Active filters', 'urban-shisha' ); ?>"></div>

				<div class="product-grid shop-product-grid" id="shop-products">
					<?php foreach ( $products as $item ) : ?>
						<?php get_template_part( 'template-parts/product-card', null, array( 'product_data' => $item ) ); ?>
					<?php endforeach; ?>
				</div>

				<div class="shop-empty" id="shop-empty"<?php echo ! empty( $products ) ? ' hidden' : ''; ?>>
					<span><?php echo esc_html( $fields['empty_eyebrow'] ); ?></span>
					<h3 id="empty-title"><?php echo esc_html( $fields['empty_heading'] ); ?></h3>
					<p id="empty-copy"><?php echo esc_html( $fields['empty_description'] ); ?></p>
					<button type="button" class="button" data-clear><?php esc_html_e( 'View all products', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
				</div>

				<div class="shop-end-note"<?php echo empty( $products ) ? ' hidden' : ''; ?>>
					<span><?php echo esc_html( $fields['end_note_eyebrow'] ); ?></span>
					<p><?php echo wp_kses_post( $fields['end_note_text'] ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="shop-build-band">
		<div class="wrap">
			<div>
				<p class="eyebrow"><?php echo esc_html( $fields['build_band_eyebrow'] ); ?></p>
				<h2><?php echo wp_kses_post( $fields['build_band_heading'] ); ?></h2>
			</div>
			<a class="button" href="<?php echo esc_url( $fields['build_band_button_url'] ); ?>"><?php echo esc_html( $fields['build_band_button_text'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
		</div>
	</section>
</main>

<dialog id="filter-dialog" class="shop-filter-dialog" aria-labelledby="filter-dialog-title">
	<div class="filter-dialog-header">
		<h2 id="filter-dialog-title"><?php esc_html_e( 'Your filters.', 'urban-shisha' ); ?></h2>
		<button class="icon-button close-dialog" aria-label="<?php esc_attr_e( 'Close filters', 'urban-shisha' ); ?>"><svg><use href="#i-close"/></svg></button>
	</div>
	<div id="mobile-filter-content"></div>
	<div class="filter-dialog-bottom">
		<button class="button" id="filter-apply"><?php esc_html_e( 'Show products', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
	</div>
</dialog>

<?php
get_footer();

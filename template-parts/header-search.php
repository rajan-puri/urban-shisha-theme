<?php
/** Native WooCommerce product search; no preview product catalogue. */
defined( 'ABSPATH' ) || exit;
?>
<dialog class="header-search-dialog" id="header-search" aria-labelledby="header-search-title">
<div class="header-search-heading"><h2 id="header-search-title"><?php esc_html_e( 'Find your next setup.', 'urban-shisha' ); ?></h2><button type="button" class="icon-button" data-close-search aria-label="<?php esc_attr_e( 'Close product search', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-close"/></svg></button></div>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-product-search">
<label for="header-search-input"><?php esc_html_e( 'Search products', 'urban-shisha' ); ?></label>
<div class="header-search-field"><input type="search" name="s" id="header-search-input" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Hookahs, bowls, accessories…', 'urban-shisha' ); ?>" required><button type="submit" class="button"><?php esc_html_e( 'Search', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button></div>
<input type="hidden" name="post_type" value="product">
</form>
</dialog>

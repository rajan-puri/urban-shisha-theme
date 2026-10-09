<?php
/**
 * Homepage Editorial Collections template part.
 * Links promotional banner cards to working shop destinations.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$shop_url      = wc_get_page_permalink( 'shop' ) ?: urban_shisha_route_url( 'shop' );
$charcoal_term = get_term_by( 'slug', 'charcoal', 'product_cat' );
$charcoal_url  = ( $charcoal_term && ! is_wp_error( $charcoal_term ) ) ? get_term_link( $charcoal_term ) : add_query_arg( 'category', 'charcoal', $shop_url );
?>
<section class="section editorial-collections wrap" aria-label="<?php esc_attr_e( 'Discover more essentials', 'urban-shisha' ); ?>">
	<?php
	foreach ( (array) ( $args['home_editorial_cards'] ?? array() ) as $i => $card ) :
		$saved_link = $card['home_editorial_card_link']['url'] ?? '';
		if ( ! empty( $saved_link ) ) {
			$link = $saved_link;
		} elseif ( 0 === $i ) {
			$link = add_query_arg( 'category', 'bowls,heat-management', $shop_url );
		} else {
			$link = $charcoal_url;
		}
	?>
		<a class="editorial-card<?php echo 1 === $i ? ' charcoal-card' : ''; ?>" href="<?php echo esc_url( $link ); ?>">
			<div class="editorial-copy">
				<p class="eyebrow"><?php echo esc_html( $card['home_editorial_card_eyebrow'] ?? '' ); ?></p>
				<h2><?php echo wp_kses_post( $card['home_editorial_card_title'] ?? '' ); ?></h2>
				<span class="text-link"><?php echo esc_html( $card['home_editorial_card_copy'] ?? '' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
			</div>
			<?php echo urban_shisha_home_art( (int) ( $card['home_editorial_card_image'] ?? 0 ), '', '', 'div' ); ?>
		</a>
	<?php endforeach; ?>
</section>

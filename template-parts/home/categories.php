<?php
defined( 'ABSPATH' ) || exit;
$cta = $args['home_categories_cta'] ?? array();
?>
<section class="section categories wrap" id="categories" aria-labelledby="category-title">
<div class="section-heading reveal"><div><p class="eyebrow"><?php echo esc_html( $args['home_categories_eyebrow'] ?? '' ); ?></p><h2 id="category-title"><?php echo wp_kses_post( $args['home_categories_heading'] ?? '' ); ?><span class="punct">.</span></h2></div>
<?php if ( ! empty( $cta['url'] ) ) : ?><a class="text-link" href="<?php echo esc_url( $cta['url'] ); ?>"><?php echo esc_html( $cta['title'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a><?php endif; ?></div>
<div class="category-grid">
<?php foreach ( (array) ( $args['home_categories_cards'] ?? array() ) as $card ) :
$link = $card['home_categories_card_link']['url'] ?? '';
$tag = $link ? 'a' : 'button';
?>
<<?php echo $tag; ?> class="category-card" <?php if ( $link ) : ?>href="<?php echo esc_url( $link ); ?>"<?php else : ?>type="button" data-category="<?php echo esc_attr( $card['home_categories_card_slug'] ?? '' ); ?>"<?php endif; ?>>
<span class="category-image"><?php echo urban_shisha_home_art( (int) $card['home_categories_card_image'], $card['home_categories_card_title'] ); ?></span>
<span class="category-name"><?php echo esc_html( $card['home_categories_card_title'] ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span><span class="category-caption"><?php echo esc_html( $card['home_categories_card_copy'] ); ?></span>
</<?php echo $tag; ?>>
<?php endforeach; ?>
</div></section>

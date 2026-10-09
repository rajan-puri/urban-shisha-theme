<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section journal wrap" id="guides" aria-labelledby="guide-title">
<div class="section-heading reveal"><div><p class="eyebrow"><?php echo esc_html( $args['home_guides_eyebrow'] ?? '' ); ?></p><h2 id="guide-title"><?php echo wp_kses_post( $args['home_guides_heading'] ?? '' ); ?><span class="punct">.</span></h2></div><span class="section-note"><?php echo wp_kses_post( $args['home_guides_description'] ?? '' ); ?></span></div>
<div class="guide-grid">
<?php foreach ( (array) ( $args['home_guides_cards'] ?? array() ) as $i => $card ) :
$link = $card['home_guides_card_link']['url'] ?? '';
$tag = $link ? 'a' : 'button';
?>
<<?php echo $tag; ?> class="guide-card" <?php if ( $link ) : ?>href="<?php echo esc_url( $link ); ?>"<?php else : ?>type="button" data-guide="<?php echo esc_attr( $card['home_guides_card_guide_key'] ); ?>"<?php endif; ?>>
<span class="guide-image"><?php echo urban_shisha_home_art( (int) $card['home_guides_card_image'] ); ?><span class="guide-number"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span></span>
<span class="guide-copy"><span class="eyebrow"><?php echo esc_html( $card['home_guides_card_eyebrow'] ); ?></span><h3><?php echo esc_html( $card['home_guides_card_title'] ); ?></h3><span class="text-link"><?php esc_html_e( 'Read the guide', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span></span>
<?php if ( ! $link ) : ?><template class="guide-dialog-template"><article class="article-content"><p class="eyebrow"><?php echo esc_html( $card['home_guides_card_eyebrow'] ); ?></p><h2 id="content-title"><?php echo esc_html( $card['home_guides_card_title'] ); ?></h2><?php echo wp_kses_post( $card['home_guides_card_modal_content'] ?? '' ); ?><button type="button" class="button" data-close-guide><?php esc_html_e( 'Explore the collection', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button></article></template><?php endif; ?>
</<?php echo $tag; ?>><?php endforeach; ?></div></section>

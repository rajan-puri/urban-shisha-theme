<?php
/** Theme-coloured platform icons; destinations remain editable in global Social links. */
defined( 'ABSPATH' ) || exit;
$platforms = array(
    'instagram' => array( 'label' => 'Instagram', 'icon' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2"/>' ),
    'facebook' => array( 'label' => 'Facebook', 'icon' => '<path d="M14 22v-9h3l.5-4H14V7c0-1.2.4-2 2-2h2V1.5c-.8-.1-1.8-.3-3-.3-3 0-5 1.9-5 5.4V9H7v4h3v9z"/>' ),
    'youtube' => array( 'label' => 'YouTube', 'icon' => '<path d="M21.5 6.3c-.3-1.1-1.1-1.8-2.2-2C17.4 4 12 4 12 4s-5.4 0-7.3.3c-1.1.2-1.9.9-2.2 2C2 8.3 2 12 2 12s0 3.7.5 5.7c.3 1.1 1.1 1.8 2.2 2 1.9.3 7.3.3 7.3.3s5.4 0 7.3-.3c1.1-.2 1.9-.9 2.2-2 .5-2 .5-5.7.5-5.7s0-3.7-.5-5.7z"/><path d="m10 8 6 4-6 4z" fill="var(--social-bg)"/>' ),
    'x' => array( 'label' => 'X', 'icon' => '<path d="M18.8 2h3l-6.6 7.6L23 22h-6.1l-4.8-6.3L6.6 22H3.5l7.2-8.3L1 2h6.3l4.3 5.7L18.8 2zM17.9 20h1.7L6.3 4H4.5z"/>' ),
    'pinterest' => array( 'label' => 'Pinterest', 'icon' => '<path d="M12 2a10 10 0 0 0-3.6 19.3c0-.8 0-1.7.2-2.5l1.3-5.5s-.3-.7-.3-1.7c0-1.6.9-2.8 2-2.8.9 0 1.4.7 1.4 1.5 0 .9-.6 2.3-.9 3.6-.3 1.1.6 2 1.7 2 2 0 3.4-2.1 3.4-5.1 0-2.6-1.8-4.3-4.5-4.3-3.1 0-4.9 2.3-4.9 4.7 0 .9.3 1.9.8 2.4l.2.6-.3 1c0 .3-.2.4-.5.3-1.4-.7-2.3-2.7-2.3-4.4 0-3.6 2.6-6.9 7.4-6.9 3.9 0 6.9 2.8 6.9 6.5 0 3.9-2.5 7.1-6 7.1-1.2 0-2.4-.6-2.8-1.3l-.8 3.1c-.3 1.1-1.1 2.3-1.6 3.1A10 10 0 1 0 12 2z"/>' ),
    'linkedin' => array( 'label' => 'LinkedIn', 'icon' => '<circle cx="5" cy="5" r="2"/><path d="M3 9h4v12H3zm6 0h4v1.7c.7-1.2 1.8-2 3.6-2 3.3 0 4.4 2.1 4.4 5.4V21h-4v-6.3c0-1.6-.3-2.8-1.8-2.8-1.6 0-2.2 1.1-2.2 2.8V21H9z"/>' ),
    'whatsapp' => array( 'label' => 'WhatsApp', 'icon' => '<path d="M20.5 3.5A11 11 0 0 0 3.2 16.8L2 22l5.3-1.3A11 11 0 0 0 20.5 3.5z" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8.2 6.9c-.4-.8-.8-.8-1.2-.8-.6 0-1.6.9-1.6 2.4 0 1.6 1.3 3.3 1.5 3.5.2.3 2.7 4.2 6.6 5.2 2 .6 2.6-.2 3.1-.7.4-.5.7-1.4.5-1.7l-2.5-1.2c-.3-.1-.5 0-.7.3l-.9 1c-.2.2-.4.2-.8 0-1.6-.7-2.8-1.8-3.6-3.1-.3-.4-.1-.6.1-.8l.6-.8c.2-.3.2-.5.1-.8z"/>' ),
);
$urls = array( 'instagram' => urban_shisha_get_option( 'instagram_url', '' ) );
$phone = preg_replace( '/\D/', '', (string) ( urban_shisha_get_option( 'primary_whatsapp', '' ) ?: '918700166924' ) );
$urls['whatsapp'] = $phone ? 'https://wa.me/' . $phone : '';
$extra = array();
foreach ( (array) urban_shisha_get_option( 'social_links', array() ) as $social ) {
    $label = trim( $social['social_label'] ?? '' );
    $url = $social['social_url'] ?? '';
    $key = strtolower( $label );
    $aliases = array( 'twitter' => 'x', 'twitter / x' => 'x', 'x (twitter)' => 'x', 'you tube' => 'youtube', 'whatsapp support' => 'whatsapp' );
    $key = $aliases[$key] ?? $key;
    if ( isset( $platforms[$key] ) ) { $urls[$key] = $url; }
    elseif ( $url && $label ) { $extra[] = array( 'label' => $label, 'url' => $url ); }
}
?>
<div class="social-links footer-social-links" aria-label="Social media">
<?php foreach ( $platforms as $key => $platform ) :
    $url = esc_url( $urls[$key] ?? '' );
    $label = $platform['label'];
?>
    <?php if ( $url ) : ?>
    <a class="footer-social" href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $label ); ?>" title="<?php echo esc_attr( $label ); ?>">
    <?php else : ?>
    <button class="footer-social" type="button" disabled aria-label="<?php echo esc_attr( $label . ' — link coming soon' ); ?>" title="<?php echo esc_attr( $label . ' — link coming soon' ); ?>">
    <?php endif; ?>
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><?php echo $platform['icon']; // Theme-owned SVG geometry. ?></svg>
    <?php echo $url ? '</a>' : '</button>'; ?>
<?php endforeach; ?>
<?php foreach ( $extra as $social ) : ?>
    <a class="social-link-custom" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $social['label'] ); ?></a>
<?php endforeach; ?>
</div>

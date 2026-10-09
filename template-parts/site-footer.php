<?php
/**
 * Shared site footer template part.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

$newsletter_eyebrow     = urban_shisha_get_option( 'newsletter_eyebrow', null );
$newsletter_heading     = urban_shisha_get_option( 'newsletter_heading', null );
$newsletter_description = urban_shisha_get_option( 'newsletter_description', null );
$newsletter_placeholder = urban_shisha_get_option( 'newsletter_placeholder', null );
$newsletter_note        = urban_shisha_get_option( 'newsletter_note', null );

if ( null === $newsletter_eyebrow ) {
	$newsletter_eyebrow = 'STAY IN THE KNOW.';
}
if ( null === $newsletter_heading ) {
	$newsletter_heading = 'Good things, in your inbox.';
}
if ( null === $newsletter_description ) {
	$newsletter_description = 'Fresh drops, restocks. Zero boring emails.';
}
if ( null === $newsletter_placeholder ) {
	$newsletter_placeholder = 'Your email address';
}
if ( null === $newsletter_note ) {
	$newsletter_note = 'Preview only. Subscriptions open with the store.';
}

$footer_description = urban_shisha_get_option( 'footer_description', null );
if ( null === $footer_description ) {
	$footer_description = "Hookahs with character.\nEssentials with purpose.\nA setup that feels like you.";
}

$business_location = urban_shisha_get_option( 'business_location', '' );
$owners            = urban_shisha_get_option( 'owners', array() );
$support_email     = urban_shisha_get_option( 'support_email', '' );
$primary_whatsapp  = urban_shisha_get_option( 'primary_whatsapp', '' );
$support_hours     = urban_shisha_get_option( 'support_hours', '' );
$instagram_url     = urban_shisha_get_option( 'instagram_url', '' );
$social_links      = urban_shisha_get_option( 'social_links', array() );

$footer_copyright = urban_shisha_get_option( 'footer_copyright', null );
$current_year     = wp_date( 'Y' );
$brand_name       = urban_shisha_get_brand_name();

$health_notice = urban_shisha_get_option( 'health_notice', null );
if ( null === $health_notice ) {
	$health_notice = 'For adults 18+. Smoking is injurious to health.';
}

$credit_label = urban_shisha_get_option( 'credit_label', null );
$credit_link  = urban_shisha_get_option( 'credit_link', null );
if ( null === $credit_label && null === $credit_link ) {
	$credit_label = 'Made by Mates Creation';
	$credit_link  = array(
		'url'    => 'https://matescreation.com/',
		'title'  => 'Made by Mates Creation',
		'target' => '_blank',
	);
} else {
	$credit_link = urban_shisha_parse_link( $credit_link );
}
?>
<footer class="site-footer" id="footer"><div class="wrap">
<div class="newsletter">
	<div>
		<?php if ( '' !== $newsletter_eyebrow && null !== $newsletter_eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $newsletter_eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== $newsletter_heading && null !== $newsletter_heading ) : ?>
			<h2><?php echo esc_html( $newsletter_heading ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $newsletter_description && null !== $newsletter_description ) : ?>
			<p><?php echo nl2br( esc_html( $newsletter_description ) ); ?></p>
		<?php endif; ?>
	</div>
	<form id="newsletter-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<input type="hidden" name="action" value="urban_shisha_newsletter">
		<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'urban_shisha_newsletter_nonce' ) ); ?>">
		<label class="sr-only" for="newsletter-email"><?php esc_html_e( 'Email address', 'urban-shisha' ); ?></label>
		<div class="email-field">
			<input id="newsletter-email" name="email" type="email" required placeholder="<?php echo esc_attr( ( '' !== $newsletter_placeholder && null !== $newsletter_placeholder ) ? $newsletter_placeholder : __( 'Your email address', 'urban-shisha' ) ); ?>" autocomplete="email">
			<button type="submit" aria-label="<?php esc_attr_e( 'Subscribe to updates', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
		</div>
		<p class="newsletter-note" id="newsletter-note" role="status" aria-live="polite"><?php echo esc_html( $newsletter_note ?: '' ); ?></p>
	</form>
</div>

<div class="footer-grid">
	<div class="footer-brand">
		<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>" class="wordmark" aria-label="<?php echo esc_attr( sprintf( __( '%s home', 'urban-shisha' ), $brand_name ) ); ?>">
			<?php urban_shisha_render_brand_logo( 'footer', true ); ?>
		</a>
		<?php if ( '' !== $footer_description && null !== $footer_description ) : ?>
			<p><?php echo nl2br( esc_html( $footer_description ) ); ?></p>
		<?php endif; ?>

		<a class="footer-concierge" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>"><?php esc_html_e( 'Talk to your concierge', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>

		<div class="social-links">
			<?php if ( $instagram_url ) : ?>
				<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-instagram"/></svg></a>
			<?php else : ?>
				<button type="button" data-info="social" aria-label="<?php esc_attr_e( 'Instagram', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-instagram"/></svg></button>
			<?php endif; ?>

			<?php if ( $primary_whatsapp ) :
				$clean_wa = preg_replace( '/[^\d]/', '', $primary_whatsapp );
			?>
				<a href="https://wa.me/<?php echo esc_attr( $clean_wa ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp support', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-chat"/></svg></a>
			<?php else : ?>
				<button type="button" data-info="contact" aria-label="<?php esc_attr_e( 'WhatsApp support', 'urban-shisha' ); ?>"><svg aria-hidden="true"><use href="#i-chat"/></svg></button>
			<?php endif; ?>

			<?php if ( ! empty( $social_links ) && is_array( $social_links ) ) : ?>
				<?php foreach ( $social_links as $social ) :
					$s_label = ! empty( $social['social_label'] ) ? sanitize_text_field( $social['social_label'] ) : '';
					$s_url   = ! empty( $social['social_url'] ) ? esc_url( $social['social_url'] ) : '';
					if ( $s_url ) :
				?>
					<a href="<?php echo esc_url( $s_url ); ?>" target="_blank" rel="noopener noreferrer" class="social-link-custom" aria-label="<?php echo esc_attr( $s_label ?: __( 'Social link', 'urban-shisha' ) ); ?>"><?php echo esc_html( $s_label ); ?></a>
				<?php endif; endforeach; ?>
			<?php endif; ?>
		</div>

		<?php if ( $business_location || ! empty( $owners ) || $support_email || $support_hours ) : ?>
			<div class="footer-contacts-block">
				<?php if ( $business_location ) : ?>
					<p class="footer-address">
						<svg class="footer-icon-pin" aria-hidden="true"><use href="#i-map-pin"/></svg>
						<span><?php echo nl2br( esc_html( $business_location ) ); ?></span>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $owners ) && is_array( $owners ) ) : ?>
					<div class="footer-contacts">
						<?php foreach ( $owners as $owner ) :
							$o_name  = ! empty( $owner['owner_name'] ) ? sanitize_text_field( $owner['owner_name'] ) : '';
							$o_phone = ! empty( $owner['owner_phone'] ) ? sanitize_text_field( $owner['owner_phone'] ) : '';
							if ( $o_phone ) :
								$clean_phone = preg_replace( '/[^\d+]/', '', $o_phone );
							?>
								<a href="tel:<?php echo esc_attr( $clean_phone ); ?>" class="footer-contact-link">
									<svg class="footer-contact-icon" aria-hidden="true"><use href="#i-chat"/></svg>
									<span><?php echo $o_name ? esc_html( $o_name . ': ' ) : ''; ?><?php echo esc_html( $o_phone ); ?></span>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $support_email ) : ?>
					<div class="footer-contacts footer-contacts-email">
						<a href="mailto:<?php echo esc_attr( $support_email ); ?>" class="footer-contact-link">
							<svg class="footer-contact-icon" aria-hidden="true"><use href="#i-mail"/></svg>
							<span><?php echo esc_html( $support_email ); ?></span>
						</a>
					</div>
				<?php endif; ?>

				<?php if ( $support_hours ) : ?>
					<p class="footer-hours"><?php echo esc_html( $support_hours ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php
	urban_shisha_render_footer_column( 'footer-shop', 'footer-shop', __( 'Shop', 'urban-shisha' ) );
	urban_shisha_render_footer_column( 'footer-discover', 'footer-discover', __( 'Discover', 'urban-shisha' ) );
	urban_shisha_render_footer_column( 'footer-care', 'footer-care', __( 'Customer care', 'urban-shisha' ) );
	urban_shisha_render_footer_column( 'footer-account', 'footer-account', __( 'Account & policies', 'urban-shisha' ) );
	?>
</div>

<a class="footer-wordmark" href="#top" aria-label="<?php echo esc_attr( sprintf( __( '%s, back to top', 'urban-shisha' ), $brand_name ) ); ?>">
	<span aria-hidden="true">
		<?php
		$letters = preg_split( '//u', mb_strtoupper( $brand_name, 'UTF-8' ), -1, PREG_SPLIT_NO_EMPTY );
		foreach ( (array) $letters as $letter ) {
			if ( ' ' === $letter ) {
				echo '<span class="footer-letter">&nbsp;</span>';
			} else {
				echo '<span class="footer-letter">' . esc_html( $letter ) . '</span>';
			}
		}
		?>
	</span>
	<svg aria-hidden="true"><use href="#i-arrow"/></svg>
</a>

<div class="footer-bottom">
	<span>
		<?php
		if ( null !== $footer_copyright && '' !== $footer_copyright ) {
			echo str_replace( array( '%YEAR%', '{year}' ), '<span id="year">' . esc_html( $current_year ) . '</span>', esc_html( $footer_copyright ) );
		} elseif ( null === $footer_copyright ) {
			echo '© <span id="year">' . esc_html( $current_year ) . '</span> ' . esc_html( $brand_name ) . '.';
		}
		?>
	</span>
	<?php if ( '' !== $health_notice && null !== $health_notice ) : ?>
		<span><?php echo esc_html( $health_notice ); ?></span>
	<?php endif; ?>
	<?php if ( $credit_link && ! empty( $credit_link['url'] ) ) : ?>
		<a class="footer-preview" href="<?php echo esc_url( $credit_link['url'] ); ?>"<?php echo ! empty( $credit_link['target'] ) ? ' target="' . esc_attr( $credit_link['target'] ) . '" rel="noopener noreferrer"' : ''; ?>><?php echo esc_html( $credit_label ?: ( $credit_link['title'] ?: $credit_link['url'] ) ); ?></a>
	<?php elseif ( '' !== $credit_label && null !== $credit_label ) : ?>
		<span class="footer-preview"><?php echo esc_html( $credit_label ); ?></span>
	<?php endif; ?>
</div>
</div></footer>

<?php
/**
 * Shared site footer template part.
 */
defined( 'ABSPATH' ) || exit;

$newsletter_eyebrow     = urban_shisha_get_option( 'newsletter_eyebrow', 'STAY IN THE KNOW.' );
$newsletter_heading     = urban_shisha_get_option( 'newsletter_heading', 'Good things, in your inbox.' );
$newsletter_description = urban_shisha_get_option( 'newsletter_description', 'Fresh drops, restocks. Zero boring emails.' );
$newsletter_placeholder = urban_shisha_get_option( 'newsletter_placeholder', 'Your email address' );
$newsletter_note        = urban_shisha_get_option( 'newsletter_note', 'Preview only. Subscriptions open with the store.' );

$footer_description = urban_shisha_get_option( 'footer_description', "Hookahs with character.\nEssentials with purpose.\nA setup that feels like you." );
$business_location  = urban_shisha_get_option( 'business_location' );
$owners             = urban_shisha_get_option( 'owners' );
$support_email      = urban_shisha_get_option( 'support_email' );
$primary_whatsapp   = urban_shisha_get_option( 'primary_whatsapp' );
$support_hours      = urban_shisha_get_option( 'support_hours' );
$instagram_url      = urban_shisha_get_option( 'instagram_url' );
$social_links       = urban_shisha_get_option( 'social_links' );

$footer_copyright = urban_shisha_get_option( 'footer_copyright' );
$current_year     = wp_date( 'Y' );
$brand_name       = urban_shisha_get_brand_name();

$health_notice = urban_shisha_get_option( 'health_notice', 'For adults 18+. Smoking is injurious to health.' );

$credit_label  = urban_shisha_get_option( 'credit_label', 'Made by Mates Creation' );
$credit_link   = urban_shisha_parse_link( urban_shisha_get_option( 'credit_link', array(
	'url'    => 'https://matescreation.com/',
	'title'  => 'Made by Mates Creation',
	'target' => '_blank',
) ) );
$credit_url    = $credit_link ? $credit_link['url'] : 'https://matescreation.com/';
$credit_target = ( $credit_link && ! empty( $credit_link['target'] ) ) ? $credit_link['target'] : '_blank';
?>
<footer class="site-footer" id="footer"><div class="wrap">
<div class="newsletter">
	<div>
		<?php if ( $newsletter_eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $newsletter_eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( $newsletter_heading ) : ?>
			<h2><?php echo esc_html( $newsletter_heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $newsletter_description ) : ?>
			<p><?php echo nl2br( esc_html( $newsletter_description ) ); ?></p>
		<?php endif; ?>
	</div>
	<form id="newsletter-form">
		<label class="sr-only" for="newsletter-email"><?php esc_html_e( 'Email address', 'urban-shisha' ); ?></label>
		<div class="email-field">
			<input id="newsletter-email" name="email" type="email" required placeholder="<?php echo esc_attr( $newsletter_placeholder ); ?>" autocomplete="email">
			<button type="submit" aria-label="<?php esc_attr_e( 'Subscribe to updates', 'urban-shisha' ); ?>"><svg><use href="#i-arrow"/></svg></button>
		</div>
		<p class="newsletter-note" id="newsletter-note"><?php echo esc_html( $newsletter_note ); ?></p>
	</form>
</div>

<div class="footer-grid">
	<div class="footer-brand">
		<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>" class="wordmark" aria-label="<?php echo esc_attr( $brand_name ); ?> home">
			<?php urban_shisha_render_brand_logo( 'footer', true ); ?>
		</a>
		<?php if ( $footer_description ) : ?>
			<p><?php echo nl2br( esc_html( $footer_description ) ); ?></p>
		<?php endif; ?>

		<?php if ( $business_location ) : ?>
			<div class="footer-location">
				<p class="footer-address">
					<svg aria-hidden="true" style="width:16px;height:16px;display:inline-block;vertical-align:-2px;margin-right:6px;"><use href="#i-map-pin"/></svg>
					<span><?php echo nl2br( esc_html( $business_location ) ); ?></span>
				</p>
			</div>
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
							<svg aria-hidden="true" style="width:14px;height:14px;"><use href="#i-chat"/></svg>
							<span><?php echo $o_name ? esc_html( $o_name . ': ' ) : ''; ?><?php echo esc_html( $o_phone ); ?></span>
						</a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $support_email ) : ?>
			<div class="footer-contacts" style="margin-top:6px;">
				<a href="mailto:<?php echo esc_attr( $support_email ); ?>" class="footer-contact-link">
					<svg aria-hidden="true" style="width:14px;height:14px;"><use href="#i-mail"/></svg>
					<span><?php echo esc_html( $support_email ); ?></span>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( $support_hours ) : ?>
			<p class="footer-hours" style="font-size:12px;opacity:.8;margin-top:8px;"><?php echo esc_html( $support_hours ); ?></p>
		<?php endif; ?>

		<button class="footer-concierge" data-info="contact"><?php esc_html_e( 'Talk to your concierge', 'urban-shisha' ); ?> <svg><use href="#i-arrow"/></svg></button>
		<div class="social-links">
			<?php if ( $instagram_url ) : ?>
				<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Instagram', 'urban-shisha' ); ?>"><svg><use href="#i-instagram"/></svg></a>
			<?php else : ?>
				<button data-info="social" aria-label="<?php esc_attr_e( 'Instagram', 'urban-shisha' ); ?>"><svg><use href="#i-instagram"/></svg></button>
			<?php endif; ?>

			<?php if ( $primary_whatsapp ) :
				$clean_wa = preg_replace( '/[^\d]/', '', $primary_whatsapp );
			?>
				<a href="https://wa.me/<?php echo esc_attr( $clean_wa ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp support', 'urban-shisha' ); ?>"><svg><use href="#i-chat"/></svg></a>
			<?php else : ?>
				<button data-info="contact" aria-label="<?php esc_attr_e( 'WhatsApp support', 'urban-shisha' ); ?>"><svg><use href="#i-chat"/></svg></button>
			<?php endif; ?>

			<?php if ( ! empty( $social_links ) && is_array( $social_links ) ) : ?>
				<?php foreach ( $social_links as $social ) :
					$s_label = ! empty( $social['social_label'] ) ? sanitize_text_field( $social['social_label'] ) : '';
					$s_url   = ! empty( $social['social_url'] ) ? esc_url( $social['social_url'] ) : '';
					if ( $s_url ) :
				?>
					<a href="<?php echo esc_url( $s_url ); ?>" target="_blank" rel="noopener noreferrer" class="social-link-custom" aria-label="<?php echo esc_attr( $s_label ?: 'Social link' ); ?>"><?php echo esc_html( $s_label ); ?></a>
				<?php endif; endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<?php
	// Shop column
	urban_shisha_render_footer_column(
		'footer-shop',
		'footer-shop',
		__( 'Shop', 'urban-shisha' ),
		array(
			array( 'title' => __( 'All products', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop' ) ),
			array( 'title' => __( 'Hookahs', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'hookahs' ) ) ),
			array( 'title' => __( 'Bowls', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'bowls' ) ) ),
			array( 'title' => __( 'Heat management', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'heat' ) ) ),
			array( 'title' => __( 'Charcoal', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'charcoal' ) ) ),
			array( 'title' => __( 'Hoses & mouthpieces', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'hoses' ) ) ),
			array( 'title' => __( 'Tools & spares', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'care' ) ) ),
			array( 'title' => __( 'All accessories', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'accessories' ) ) ),
		)
	);

	// Discover column
	urban_shisha_render_footer_column(
		'footer-discover',
		'footer-discover',
		__( 'Discover', 'urban-shisha' ),
		array(
			array( 'title' => __( 'About Urban Shisha', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'about' ) ),
			array( 'title' => __( 'Wholesale enquiries', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'wholesale' ) ),
			array( 'title' => __( 'New arrivals', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'home' ) . '#arrivals' ),
			array( 'title' => __( 'Featured hookahs', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop', array( 'category' => 'hookahs' ) ) ),
			array( 'title' => __( 'Build your setup', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'home' ) . '#builder' ),
			array( 'title' => __( 'Shop by budget', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shop' ) ),
			array( 'title' => __( 'The journal', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'home' ) . '#guides' ),
			array( 'title' => __( 'Your wishlist', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) ),
		)
	);

	// Customer care column
	urban_shisha_render_footer_column(
		'footer-care',
		'footer-care',
		__( 'Customer care', 'urban-shisha' ),
		array(
			array( 'title' => __( 'Contact us', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'contact' ) ),
			array( 'title' => __( 'Shipping & delivery', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'shipping' ) ),
			array( 'title' => __( 'Returns & refunds', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'returns' ) ),
			array( 'title' => __( 'Track your order', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) ),
		)
	);

	// Account & policies column
	urban_shisha_render_footer_column(
		'footer-account',
		'footer-account',
		__( 'Account & policies', 'urban-shisha' ),
		array(
			array( 'title' => __( 'My account', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'account' ) ),
			array( 'title' => __( 'My orders', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) ),
			array( 'title' => __( 'Saved products', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'account', array( 'tab' => 'wishlist' ) ) ),
			array( 'title' => __( 'Privacy policy', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'privacy' ) ),
			array( 'title' => __( 'Terms & conditions', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'terms' ) ),
			array( 'title' => __( '18+ policy', 'urban-shisha' ), 'url' => urban_shisha_route_url( 'age-policy' ) ),
		)
	);
	?>
</div>

<a class="footer-wordmark" href="#top" aria-label="<?php echo esc_attr( $brand_name ); ?>, back to top">
	<span aria-hidden="true">
		<span class="footer-letter">U</span><span class="footer-letter">R</span><span class="footer-letter">B</span><span class="footer-letter">A</span><span class="footer-letter">N</span><span class="footer-letter">&nbsp;</span><span class="footer-letter">S</span><span class="footer-letter">H</span><span class="footer-letter">I</span><span class="footer-letter">S</span><span class="footer-letter">H</span><span class="footer-letter">A</span>
	</span>
	<svg><use href="#i-arrow"/></svg>
</a>

<div class="footer-bottom">
	<span>
		<?php
		if ( $footer_copyright ) {
			echo str_replace( array( '%YEAR%', '{year}' ), '<span id="year">' . esc_html( $current_year ) . '</span>', esc_html( $footer_copyright ) );
		} else {
			echo '© <span id="year">' . esc_html( $current_year ) . '</span> ' . esc_html( $brand_name ) . '.';
		}
		?>
	</span>
	<span><?php echo esc_html( $health_notice ); ?></span>
	<a class="footer-preview" href="<?php echo esc_url( $credit_url ); ?>" target="<?php echo esc_attr( $credit_target ); ?>" rel="noopener noreferrer"><?php echo esc_html( $credit_label ); ?></a>
</div>
</div></footer>

<?php
/**
 * Contact Us page template.
 */
defined( 'ABSPATH' ) || exit;

get_header();

$raw_phone      = ( urban_shisha_get_option( 'primary_whatsapp', '' ) ?: '918700166924' );
$whatsapp_clean = preg_replace( '/\D/', '', (string) $raw_phone );
$store_email    = urban_shisha_get_option( 'support_email', get_option( 'admin_email' ) );
$store_address  = urban_shisha_get_option( 'store_address', '' );
$store_hours    = urban_shisha_get_option( 'support_hours', '' );
?>
<main id="main">
	<section class="contact-hero">
		<div class="wrap">
			<nav class="info-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>

			<div class="contact-hero-content">
				<p class="eyebrow"><?php esc_html_e( 'URBAN SHISHA / DIRECT CONCIERGE', 'urban-shisha' ); ?></p>
				<h1><?php esc_html_e( 'LET’S TALK.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'WE’RE HERE.', 'urban-shisha' ); ?></span></h1>
				<p><?php esc_html_e( 'Advice on sizing, bowl compatibility, dispatches or wholesale enquiries. We respond with care.', 'urban-shisha' ); ?></p>
			</div>

			<div class="contact-hero-shapes" aria-hidden="true">
				<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="100" cy="100" r="80" stroke="currentColor" stroke-width="2" stroke-dasharray="8 8"/>
					<circle cx="100" cy="100" r="45" fill="currentColor"/>
				</svg>
			</div>
		</div>
	</section>

	<div class="wrap contact-layout">
		<!-- Left Column: Contact Channels Card -->
		<aside class="contact-options-card" aria-label="<?php esc_attr_e( 'Contact channels', 'urban-shisha' ); ?>">
			<div class="contact-card-header">
				<p class="eyebrow"><?php esc_html_e( 'DIRECT ACCESS', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'Speak with us.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Choose the channel that best fits your enquiry.', 'urban-shisha' ); ?></p>
			</div>

			<div class="contact-channels-list">
				<!-- WhatsApp Channel -->
				<div class="channel-card" id="channel-whatsapp-card">
					<div class="channel-card-top">
						<div class="channel-icon-badge"><svg aria-hidden="true"><use href="#i-chat"/></svg></div>
						<div class="channel-title-wrap">
							<h3><?php esc_html_e( 'WhatsApp Concierge', 'urban-shisha' ); ?></h3>
							<p><?php esc_html_e( 'Direct chat for quick answers.', 'urban-shisha' ); ?></p>
						</div>
					</div>
					<div class="channel-content" id="channel-whatsapp-action">
						<?php if ( ! empty( $whatsapp_clean ) && strlen( $whatsapp_clean ) >= 7 ) : ?>
							<a class="channel-link" href="https://wa.me/<?php echo esc_attr( $whatsapp_clean ); ?>" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( 'Chat on WhatsApp', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
							</a>
						<?php else : ?>
							<p class="channel-status"><?php esc_html_e( 'WhatsApp concierge will be available when the store launches.', 'urban-shisha' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Email Channel -->
				<div class="channel-card" id="channel-email-card">
					<div class="channel-card-top">
						<div class="channel-icon-badge"><svg aria-hidden="true"><use href="#i-mail"/></svg></div>
						<div class="channel-title-wrap">
							<h3><?php esc_html_e( 'Email Support', 'urban-shisha' ); ?></h3>
							<p><?php esc_html_e( 'General enquiries & advice.', 'urban-shisha' ); ?></p>
						</div>
					</div>
					<div class="channel-content" id="channel-email-action">
						<?php if ( ! empty( $store_email ) ) : ?>
							<a class="channel-link" href="mailto:<?php echo esc_attr( $store_email ); ?>">
								<?php echo esc_html( $store_email ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
							</a>
						<?php else : ?>
							<p class="channel-status"><?php esc_html_e( 'Email support will open when the live store launches.', 'urban-shisha' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Order Support Channel -->
				<div class="channel-card" id="channel-order-card">
					<div class="channel-card-top">
						<div class="channel-icon-badge"><svg aria-hidden="true"><use href="#i-box"/></svg></div>
						<div class="channel-title-wrap">
							<h3><?php esc_html_e( 'Order Support', 'urban-shisha' ); ?></h3>
							<p><?php esc_html_e( 'Dispatches & invoices.', 'urban-shisha' ); ?></p>
						</div>
					</div>
					<div class="channel-content">
						<p><?php esc_html_e( 'Track your dispatches and review order history directly in your account.', 'urban-shisha' ); ?></p>
						<a class="channel-link" href="<?php echo esc_url( urban_shisha_route_url( 'account', array( 'tab' => 'orders' ) ) ); ?>">
							<?php esc_html_e( 'View your orders', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</a>
					</div>
				</div>

				<?php if ( ! empty( $store_address ) ) : ?>
				<!-- Studio / Location Card -->
				<div class="channel-card" id="channel-location-card">
					<div class="channel-card-top">
						<div class="channel-icon-badge"><svg aria-hidden="true"><use href="#i-map-pin"/></svg></div>
						<div class="channel-title-wrap">
							<h3><?php esc_html_e( 'Based in Delhi', 'urban-shisha' ); ?></h3>
							<p><?php esc_html_e( 'Online orders and delivery.', 'urban-shisha' ); ?></p>
						</div>
					</div>
					<div class="channel-content">
						<p id="channel-location-text">
							<?php echo esc_html( $store_address ); ?>
							<?php if ( ! empty( $store_hours ) ) : ?>
								<br><small><?php echo esc_html( $store_hours ); ?></small>
							<?php endif; ?>
						</p>
					</div>
				</div>
				<?php endif; ?>
			</div>

			<p class="contact-channels-note"><?php esc_html_e( 'Adults 18+ only. Enquiries are handled with discretion. Contact details are verified through store settings.', 'urban-shisha' ); ?></p>
		</aside>

		<!-- Right Column: Enquiry Form Card -->
		<section class="contact-form-card" aria-labelledby="form-heading">
			<div class="contact-card-header">
				<p class="eyebrow"><?php esc_html_e( 'SEND A MESSAGE', 'urban-shisha' ); ?></p>
				<h2 id="form-heading"><?php esc_html_e( 'Drop us a note.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Have a question regarding compatibility, pricing or availability? Send your details below.', 'urban-shisha' ); ?></p>
			</div>

			<?php
			if ( shortcode_exists( 'contact-form-7' ) ) {
				echo do_shortcode( '[contact-form-7 id="560" title="Contact Form"]' );
			} else {
				$custom_content = get_the_content();
				if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) {
					the_content();
				}
			}
			?>
		</section>
	</div>

	<!-- Helpful Answers: FAQ Accordion Section -->
	<div class="wrap">
		<section class="contact-faq-section" id="faq" aria-labelledby="faq-heading">
			<div class="contact-faq-header">
				<p class="eyebrow"><?php esc_html_e( 'FREQUENT QUESTIONS', 'urban-shisha' ); ?></p>
				<h2 id="faq-heading"><?php esc_html_e( 'Helpful answers.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Quick answers on setups, accessory sizing, delivery and orders.', 'urban-shisha' ); ?></p>
			</div>

			<div class="faq-list">
				<details class="faq-item" open>
					<summary class="faq-trigger">
						<span><?php esc_html_e( 'How do I choose the right hookah for my space?', 'urban-shisha' ); ?></span>
						<svg aria-hidden="true"><use href="#i-chevron"/></svg>
					</summary>
					<div class="faq-body">
						<p><?php
						printf(
							/* translators: 1: Hookahs shop link, 2: The Journal guide link */
							esc_html__( 'A considered setup starts with your environment and storage needs. Compact hookahs like the Dark Knight Slash offer portability and effortless cleaning, while larger statement pieces like the COCOYAYA Brando and Slims Sterling are tailored for dedicated tabletop lounge setups. Review our full collection in the %1$s or read %2$s for detailed dimensions.', 'urban-shisha' ),
							'<a href="' . esc_url( add_query_arg( 'category', 'hookahs', urban_shisha_route_url( 'shop' ) ) ) . '">' . esc_html__( 'Hookahs shop', 'urban-shisha' ) . '</a>',
							'<a href="' . esc_url( home_url( '/#guides' ) ) . '">' . esc_html__( 'The Journal guide', 'urban-shisha' ) . '</a>'
						);
						?></p>
					</div>
				</details>

				<details class="faq-item">
					<summary class="faq-trigger">
						<span><?php esc_html_e( 'How can I check if accessories and bowls will fit?', 'urban-shisha' ); ?></span>
						<svg aria-hidden="true"><use href="#i-chevron"/></svg>
					</summary>
					<div class="faq-body">
						<p><?php
						printf(
							/* translators: 1: Build your setup tool link */
							esc_html__( 'Do not assume all accessories fit every hookah universally. Always check bowl rim outer dimensions against your heat-management device (HMD), and verify silicone hose connector diameters and grommet seals before combining pieces. You can test four-piece combinations in our interactive %s tool.', 'urban-shisha' ),
							'<a href="' . esc_url( home_url( '/#builder' ) ) . '">' . esc_html__( 'Build your setup', 'urban-shisha' ) . '</a>'
						);
						?></p>
					</div>
				</details>

				<details class="faq-item">
					<summary class="faq-trigger">
						<span><?php esc_html_e( 'How does delivery and shipping work?', 'urban-shisha' ); ?></span>
						<svg aria-hidden="true"><use href="#i-chevron"/></svg>
					</summary>
					<div class="faq-body">
						<p><?php esc_html_e( 'All Urban Shisha orders are dispatched in discreet, robust protective packaging across serviceable PIN codes in India. Precise delivery timelines, courier options and shipping charges are confirmed during live store checkout. No delivery promises are made during this design preview.', 'urban-shisha' ); ?></p>
					</div>
				</details>

				<details class="faq-item">
					<summary class="faq-trigger">
						<span><?php esc_html_e( 'Where can I track an existing order?', 'urban-shisha' ); ?></span>
						<svg aria-hidden="true"><use href="#i-chevron"/></svg>
					</summary>
					<div class="faq-body">
						<p><?php
						printf(
							/* translators: 1: My Account & Orders page link */
							esc_html__( 'Once live purchasing opens, all order tracking numbers, shipment statuses and tax invoices will be accessible in your personal dashboard on the %s page.', 'urban-shisha' ),
							'<a href="' . esc_url( add_query_arg( 'tab', 'orders', urban_shisha_route_url( 'account' ) ) ) . '">' . esc_html__( 'My Account & Orders', 'urban-shisha' ) . '</a>'
						);
						?></p>
					</div>
				</details>
			</div>
		</section>
	</div>

	<section class="wrap contact-closing">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'EXPLORE THE CATALOGUE', 'urban-shisha' ); ?></p>
			<h2><?php esc_html_e( 'PREFER BROWSING FIRST?', 'urban-shisha' ); ?></h2>
			<p><?php esc_html_e( 'Explore hookahs, bowls, charcoal and precision accessories.', 'urban-shisha' ); ?></p>
		</div>
		<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
			<?php esc_html_e( 'Explore collection', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	</section>
</main>
<?php
get_footer();

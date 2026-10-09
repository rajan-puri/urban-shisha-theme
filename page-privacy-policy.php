<?php
/**
 * Privacy Policy page template.
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<section class="policy-hero">
		<div class="wrap">
			<nav class="info-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<div class="policy-hero-grid">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'URBAN SHISHA / PRIVACY POLICY', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'YOUR DETAILS.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'YOUR CHOICES.', 'urban-shisha' ); ?></span></h1>
				</div>
				<p class="policy-intro"><?php esc_html_e( 'What this preview stores, what stays in your browser and how to manage your saved choices.', 'urban-shisha' ); ?></p>
			</div>
		</div>
	</section>

	<div class="wrap policy-layout">
		<?php get_template_part( 'template-parts/policy', 'sidebar' ); ?>

		<article class="policy-article" aria-label="<?php esc_attr_e( 'Privacy policy', 'urban-shisha' ); ?>">
			<?php
			$custom_content = get_the_content();
			if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) :
				the_content();
			else :
			?>
			<div class="policy-draft">
				<span><?php esc_html_e( 'POLICY DRAFT · DETAILS UNDER REVIEW', 'urban-shisha' ); ?></span>
				<p><?php esc_html_e( 'This notice describes the current local design preview. The live store’s data handling, service providers, retention periods and privacy contact require a separate update before launch.', 'urban-shisha' ); ?></p>
				<small><?php esc_html_e( 'Prepared 8 October 2026. Effective date to be confirmed.', 'urban-shisha' ); ?></small>
			</div>

			<nav class="policy-contents" aria-label="<?php esc_attr_e( 'On this page', 'urban-shisha' ); ?>">
				<p class="eyebrow"><?php esc_html_e( 'ON THIS PAGE', 'urban-shisha' ); ?></p>
				<a href="#scope"><span>01</span><?php esc_html_e( 'What this notice covers', 'urban-shisha' ); ?></a>
				<a href="#storage"><span>02</span><?php esc_html_e( 'Saved choices in this browser', 'urban-shisha' ); ?></a>
				<a href="#forms"><span>03</span><?php esc_html_e( 'Forms & personal information', 'urban-shisha' ); ?></a>
				<a href="#sharing"><span>04</span><?php esc_html_e( 'When you choose an external channel', 'urban-shisha' ); ?></a>
				<a href="#technical"><span>05</span><?php esc_html_e( 'Cookies, analytics & technical data', 'urban-shisha' ); ?></a>
				<a href="#choices"><span>06</span><?php esc_html_e( 'Manage your saved choices', 'urban-shisha' ); ?></a>
				<a href="#live"><span>07</span><?php esc_html_e( 'Before the live store launches', 'urban-shisha' ); ?></a>
				<a href="#contact"><span>08</span><?php esc_html_e( 'Privacy questions', 'urban-shisha' ); ?></a>
			</nav>

			<section class="policy-section" id="scope" aria-labelledby="heading-scope">
				<span class="policy-number">01</span>
				<div>
					<h2 id="heading-scope"><?php esc_html_e( 'What this notice covers', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'This notice covers the Urban Shisha design preview. It does not describe an active WordPress checkout or a connected customer-account service. The merchant’s legal name, address and privacy contact will be added when confirmed.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="storage" aria-labelledby="heading-storage">
				<span class="policy-number">02</span>
				<div>
					<h2 id="heading-storage"><?php esc_html_e( 'Saved choices in this browser', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The preview uses browser local storage for the age confirmation, shopping bag, wishlist and bulk-enquiry product IDs and quantities. These choices stay available in the same browser until removed. They are separate from a live customer account.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="forms" aria-labelledby="heading-forms">
				<span class="policy-number">03</span>
				<div>
					<h2 id="heading-forms"><?php esc_html_e( 'Forms & personal information', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The checkout, login/register and contact forms currently validate information without creating accounts or sending orders or messages to a store backend. Bulk-enquiry contact details are used to prepare a message for your review and are not saved in local storage. Form values can remain in the current tab or in browser-managed autofill.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="sharing" aria-labelledby="heading-sharing">
				<span class="policy-number">04</span>
				<div>
					<h2 id="heading-sharing"><?php esc_html_e( 'When you choose an external channel', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Choosing a configured WhatsApp or email enquiry link puts the prepared information into that external application. You choose whether to send it. Those services have their own privacy terms. Ordinary links to social media, reference sites or other websites also leave this site.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="technical" aria-labelledby="heading-technical">
				<span class="policy-number">05</span>
				<div>
					<h2 id="heading-technical"><?php esc_html_e( 'Cookies, analytics & technical data', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The preview loads its fonts, images and scripts locally and does not add an analytics or advertising integration. Browser storage is used for the saved choices described above. Any hosting logs, payment services, analytics, cookies or live-store integrations must be reviewed and described when the store is deployed.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="choices" aria-labelledby="heading-choices">
				<span class="policy-number">06</span>
				<div>
					<h2 id="heading-choices"><?php esc_html_e( 'Manage your saved choices', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'You can clear the Urban Shisha preview’s saved product choices and age confirmation using the control below, or remove site data through your browser settings. Clearing them removes the local bag, wishlist and bulk list; it does not cancel an order. This preview has no server account or stored order record to delete.', 'urban-shisha' ); ?></p>
					<button class="button button-cream" id="clear-preview-data">
						<?php esc_html_e( 'Manage saved data', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
					</button>
					<p id="storage-feedback" class="policy-feedback" role="status" aria-live="polite"></p>
				</div>
			</section>

			<section class="policy-section" id="live" aria-labelledby="heading-live">
				<span class="policy-number">07</span>
				<div>
					<h2 id="heading-live"><?php esc_html_e( 'Before the live store launches', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The live policy must explain the personal information collected, its purposes, recipients, applicable choices and request process, retention, security arrangements and complaint contact. That policy must match the actual WooCommerce configuration and service providers.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="contact" aria-labelledby="heading-contact">
				<span class="policy-number">08</span>
				<div>
					<h2 id="heading-contact"><?php esc_html_e( 'Privacy questions', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Use the Contact page for privacy questions once its contact channels are connected. A direct privacy contact and merchant identification are still to be supplied. The 18+ policy explains who this store is intended for.', 'urban-shisha' ); ?></p>
				</div>
			</section>
			<?php endif; ?>

			<div class="policy-bottom">
				<p><?php esc_html_e( 'Need help with these details?', 'urban-shisha' ); ?></p>
				<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>">
					<?php esc_html_e( 'Talk to Urban Shisha', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
				<a href="<?php echo esc_url( urban_shisha_route_url( 'about' ) ); ?>" class="text-link">
					<?php esc_html_e( 'Meet the store', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			</div>
		</article>
	</div>
</main>

<dialog class="privacy-clear-dialog" id="clear-data-dialog" aria-labelledby="clear-data-title">
	<h2 id="clear-data-title"><?php esc_html_e( 'Clear your saved choices?', 'urban-shisha' ); ?></h2>
	<p><?php esc_html_e( 'This removes this browser’s Urban Shisha bag, wishlist, bulk product list and age confirmation.', 'urban-shisha' ); ?></p>
	<div>
		<button class="button" id="confirm-clear-data">
			<?php esc_html_e( 'Clear saved choices', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</button>
		<button class="text-link" id="cancel-clear-data"><?php esc_html_e( 'Keep my choices', 'urban-shisha' ); ?></button>
	</div>
</dialog>
<?php
get_footer();

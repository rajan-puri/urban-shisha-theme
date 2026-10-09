<?php
/**
 * Shipping & Delivery policy page template.
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
					<p class="eyebrow"><?php esc_html_e( 'URBAN SHISHA / SHIPPING & DELIVERY', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'THE JOURNEY.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'TO YOUR DOOR.', 'urban-shisha' ); ?></span></h1>
				</div>
				<p class="policy-intro"><?php esc_html_e( 'Delivery details, order tracking and what to check when your package arrives.', 'urban-shisha' ); ?></p>
			</div>
		</div>
	</section>

	<div class="wrap policy-layout">
		<?php get_template_part( 'template-parts/policy', 'sidebar' ); ?>

		<article class="policy-article" aria-label="<?php esc_attr_e( 'Shipping & delivery', 'urban-shisha' ); ?>">
			<?php
			$custom_content = get_the_content();
			if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) :
				the_content();
			else :
			?>
			<div class="policy-draft">
				<span><?php esc_html_e( 'POLICY DRAFT · DETAILS UNDER REVIEW', 'urban-shisha' ); ?></span>
				<p><?php esc_html_e( 'Delivery charges, serviceable locations and dispatch estimates need confirmation before the live store accepts orders.', 'urban-shisha' ); ?></p>
				<small><?php esc_html_e( 'Prepared 8 October 2026. Effective date to be confirmed.', 'urban-shisha' ); ?></small>
			</div>

			<nav class="policy-contents" aria-label="<?php esc_attr_e( 'On this page', 'urban-shisha' ); ?>">
				<p class="eyebrow"><?php esc_html_e( 'ON THIS PAGE', 'urban-shisha' ); ?></p>
				<a href="#delivery"><span>01</span><?php esc_html_e( 'Where we deliver', 'urban-shisha' ); ?></a>
				<a href="#charges"><span>02</span><?php esc_html_e( 'Shipping charges', 'urban-shisha' ); ?></a>
				<a href="#dispatch"><span>03</span><?php esc_html_e( 'Dispatch & delivery estimates', 'urban-shisha' ); ?></a>
				<a href="#tracking"><span>04</span><?php esc_html_e( 'Tracking your order', 'urban-shisha' ); ?></a>
				<a href="#arrival"><span>05</span><?php esc_html_e( 'When your order arrives', 'urban-shisha' ); ?></a>
				<a href="#address"><span>06</span><?php esc_html_e( 'Address changes & delivery issues', 'urban-shisha' ); ?></a>
				<a href="#bulk"><span>07</span><?php esc_html_e( 'Bulk enquiries', 'urban-shisha' ); ?></a>
			</nav>

			<section class="policy-section" id="delivery" aria-labelledby="heading-delivery">
				<span class="policy-number">01</span>
				<div>
					<h2 id="heading-delivery"><?php esc_html_e( 'Where we deliver', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Delivery availability depends on the delivery PIN code, the item and courier coverage. Check serviceability before placing an order. The store’s final delivery regions and any exclusions will be published here before launch.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="charges" aria-labelledby="heading-charges">
				<span class="policy-number">02</span>
				<div>
					<h2 id="heading-charges"><?php esc_html_e( 'Shipping charges', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Shipping charges must be shown before you confirm and pay for an order. Charges may depend on location, package size and order contents. No free-shipping threshold or fixed shipping rate has been confirmed for this preview.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="dispatch" aria-labelledby="heading-dispatch">
				<span class="policy-number">03</span>
				<div>
					<h2 id="heading-dispatch"><?php esc_html_e( 'Dispatch & delivery estimates', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The live store will show a dispatch estimate and an estimated delivery range before purchase. These timings are still being finalised. Courier delays, holidays and address issues can affect an estimate; contact the store if a confirmed delivery date is missed.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="tracking" aria-labelledby="heading-tracking">
				<span class="policy-number">04</span>
				<div>
					<h2 id="heading-tracking"><?php esc_html_e( 'Tracking your order', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Once a live order is dispatched, its shipment reference and tracking details should be available through your order confirmation or account. The current preview does not create shipments or live tracking links.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="arrival" aria-labelledby="heading-arrival">
				<span class="policy-number">05</span>
				<div>
					<h2 id="heading-arrival"><?php esc_html_e( 'When your order arrives', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Check the package and its contents when received. If an item is missing, incorrect or damaged, keep the packaging and contact the store with your order reference and a description. Photos can help explain the issue. A recording is not stated as a condition of support.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="address" aria-labelledby="heading-address">
				<span class="policy-number">06</span>
				<div>
					<h2 id="heading-address"><?php esc_html_e( 'Address changes & delivery issues', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'For an address correction, contact the store with the order reference as soon as possible. Changes depend on dispatch status. Any re-delivery charge or other arrangement must be explained before it is agreed.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="bulk" aria-labelledby="heading-bulk">
				<span class="policy-number">07</span>
				<div>
					<h2 id="heading-bulk"><?php esc_html_e( 'Bulk enquiries', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Bulk shipping arrangements, charges and lead times are confirmed in the quotation. A prepared enquiry is a request for information; it is not a paid order or a delivery booking.', 'urban-shisha' ); ?></p>
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
<?php
get_footer();

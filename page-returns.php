<?php
/**
 * Returns & Refunds policy page template.
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
					<p class="eyebrow"><?php esc_html_e( 'URBAN SHISHA / RETURNS & REFUNDS', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'SOMETHING WRONG?', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'LET’S TALK.', 'urban-shisha' ); ?></span></h1>
				</div>
				<p class="policy-intro"><?php esc_html_e( 'A clear place to start when an item needs attention, an order changes or a refund is due.', 'urban-shisha' ); ?></p>
			</div>
		</div>
	</section>

	<div class="wrap policy-layout">
		<?php get_template_part( 'template-parts/policy', 'sidebar' ); ?>

		<article class="policy-article" aria-label="<?php esc_attr_e( 'Returns & refunds', 'urban-shisha' ); ?>">
			<?php
			$custom_content = get_the_content();
			if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) :
				the_content();
			else :
			?>
			<div class="policy-draft">
				<span><?php esc_html_e( 'POLICY DRAFT · DETAILS UNDER REVIEW', 'urban-shisha' ); ?></span>
				<p><?php esc_html_e( 'Return windows, category conditions, return shipping costs and refund timings are still under review. This draft does not limit applicable consumer rights.', 'urban-shisha' ); ?></p>
				<small><?php esc_html_e( 'Prepared 8 October 2026. Effective date to be confirmed.', 'urban-shisha' ); ?></small>
			</div>

			<nav class="policy-contents" aria-label="<?php esc_attr_e( 'On this page', 'urban-shisha' ); ?>">
				<p class="eyebrow"><?php esc_html_e( 'ON THIS PAGE', 'urban-shisha' ); ?></p>
				<a href="#start"><span>01</span><?php esc_html_e( 'Start with your order details', 'urban-shisha' ); ?></a>
				<a href="#issues"><span>02</span><?php esc_html_e( 'Damaged, incorrect or defective items', 'urban-shisha' ); ?></a>
				<a href="#change"><span>03</span><?php esc_html_e( 'Change-of-mind requests', 'urban-shisha' ); ?></a>
				<a href="#cancellation"><span>04</span><?php esc_html_e( 'Order cancellations', 'urban-shisha' ); ?></a>
				<a href="#return"><span>05</span><?php esc_html_e( 'Sending a product back', 'urban-shisha' ); ?></a>
				<a href="#refund"><span>06</span><?php esc_html_e( 'Refunds & payment methods', 'urban-shisha' ); ?></a>
				<a href="#help"><span>07</span><?php esc_html_e( 'Help with an unresolved issue', 'urban-shisha' ); ?></a>
			</nav>

			<section class="policy-section" id="start" aria-labelledby="heading-start">
				<span class="policy-number">01</span>
				<div>
					<h2 id="heading-start"><?php esc_html_e( 'Start with your order details', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Contact the store with your order reference, product name and a description of the issue. For damage, a wrong item or a missing item, photos of the product and packaging can help. Keep relevant packaging while the issue is reviewed.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="issues" aria-labelledby="heading-issues">
				<span class="policy-number">02</span>
				<div>
					<h2 id="heading-issues"><?php esc_html_e( 'Damaged, incorrect or defective items', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Report an issue when you discover it. The store will review the circumstances and explain the available remedy, which may include replacement, repair or refund as appropriate. Final procedures will be published before launch; this draft does not exclude rights provided by applicable law.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="change" aria-labelledby="heading-change">
				<span class="policy-number">03</span>
				<div>
					<h2 id="heading-change"><?php esc_html_e( 'Change-of-mind requests', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Eligibility for a change-of-mind return, its time window and product-condition requirements have not yet been confirmed. Any category-specific conditions, including those for opened or used accessories, must be disclosed before purchase.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="cancellation" aria-labelledby="heading-cancellation">
				<span class="policy-number">04</span>
				<div>
					<h2 id="heading-cancellation"><?php esc_html_e( 'Order cancellations', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Contact the store as soon as possible if you want to cancel a live order. Whether dispatch can be stopped depends on the order status. The live store must explain any applicable cancellation terms before an order is placed.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="return" aria-labelledby="heading-return">
				<span class="policy-number">05</span>
				<div>
					<h2 id="heading-return"><?php esc_html_e( 'Sending a product back', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Contact the store before sending a package so the return address and handling instructions can be confirmed. Responsibility for return shipping costs depends on the reason and the applicable terms. No return address or automatic pickup service has been configured in this preview.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="refund" aria-labelledby="heading-refund">
				<span class="policy-number">06</span>
				<div>
					<h2 id="heading-refund"><?php esc_html_e( 'Refunds & payment methods', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'If a refund is approved, the store will confirm the amount, payment route and processing estimate. Bank or payment-provider processing may add time. Refund processing windows and payment providers are still being finalised; no refund is issued by this preview.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="help" aria-labelledby="heading-help">
				<span class="policy-number">07</span>
				<div>
					<h2 id="heading-help"><?php esc_html_e( 'Help with an unresolved issue', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Use the Contact page to explain the issue and include the order reference. The store’s legal entity, support contact and grievance-contact details must be completed before live orders begin. You can also refer to the Shipping policy for delivery issues.', 'urban-shisha' ); ?></p>
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

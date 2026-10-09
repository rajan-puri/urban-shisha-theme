<?php
/**
 * Terms & Conditions policy page template.
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
					<p class="eyebrow"><?php esc_html_e( 'URBAN SHISHA / TERMS & CONDITIONS', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'THE DETAILS.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'BEFORE YOU BUY.', 'urban-shisha' ); ?></span></h1>
				</div>
				<p class="policy-intro"><?php esc_html_e( 'Using the website, choosing products and understanding the difference between a preview and an order.', 'urban-shisha' ); ?></p>
			</div>
		</div>
	</section>

	<div class="wrap policy-layout">
		<?php get_template_part( 'template-parts/policy', 'sidebar' ); ?>

		<article class="policy-article" aria-label="<?php esc_attr_e( 'Terms & conditions', 'urban-shisha' ); ?>">
			<?php
			$custom_content = get_the_content();
			if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) :
				the_content();
			else :
			?>
			<div class="policy-draft">
				<span><?php esc_html_e( 'POLICY DRAFT · DETAILS UNDER REVIEW', 'urban-shisha' ); ?></span>
				<p><?php esc_html_e( 'These are review drafts. Merchant details, payment options and final purchase terms must be confirmed before the store takes live orders.', 'urban-shisha' ); ?></p>
				<small><?php esc_html_e( 'Prepared 8 October 2026. Effective date to be confirmed.', 'urban-shisha' ); ?></small>
			</div>

			<nav class="policy-contents" aria-label="<?php esc_attr_e( 'On this page', 'urban-shisha' ); ?>">
				<p class="eyebrow"><?php esc_html_e( 'ON THIS PAGE', 'urban-shisha' ); ?></p>
				<a href="#store"><span>01</span><?php esc_html_e( 'Who operates the store', 'urban-shisha' ); ?></a>
				<a href="#adults"><span>02</span><?php esc_html_e( 'Adults only & responsible use', 'urban-shisha' ); ?></a>
				<a href="#products"><span>03</span><?php esc_html_e( 'Product information & compatibility', 'urban-shisha' ); ?></a>
				<a href="#pricing"><span>04</span><?php esc_html_e( 'Prices, availability & payments', 'urban-shisha' ); ?></a>
				<a href="#orders"><span>05</span><?php esc_html_e( 'Order acceptance', 'urban-shisha' ); ?></a>
				<a href="#delivery"><span>06</span><?php esc_html_e( 'Delivery, cancellations & returns', 'urban-shisha' ); ?></a>
				<a href="#account"><span>07</span><?php esc_html_e( 'Accounts & website use', 'urban-shisha' ); ?></a>
				<a href="#bulk"><span>08</span><?php esc_html_e( 'Bulk orders & quotations', 'urban-shisha' ); ?></a>
				<a href="#questions"><span>09</span><?php esc_html_e( 'Questions & changes', 'urban-shisha' ); ?></a>
			</nav>

			<section class="policy-section" id="store" aria-labelledby="heading-store">
				<span class="policy-number">01</span>
				<div>
					<h2 id="heading-store"><?php esc_html_e( 'Who operates the store', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Urban Shisha is the store’s brand name. The legal merchant name, business address, support contact and applicable registration details have not yet been supplied. They must be published before live orders are accepted.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="adults" aria-labelledby="heading-adults">
				<span class="policy-number">02</span>
				<div>
					<h2 id="heading-adults"><?php esc_html_e( 'Adults only & responsible use', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The store is intended for adults aged 18 and over. Follow applicable local restrictions and product instructions. The website’s age confirmation is a user declaration, not a verified identity check. Smoking is injurious to health.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="products" aria-labelledby="heading-products">
				<span class="policy-number">03</span>
				<div>
					<h2 id="heading-products"><?php esc_html_e( 'Product information & compatibility', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Review each product’s description, dimensions, options and compatibility before buying. Colours can appear different on different screens. A photo showing accessories does not by itself confirm that they are included; the final product listing must state what comes in the box.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="pricing" aria-labelledby="heading-pricing">
				<span class="policy-number">04</span>
				<div>
					<h2 id="heading-pricing"><?php esc_html_e( 'Prices, availability & payments', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Some catalogue values are design-preview data or reference values. The live store must state current prices, applicable taxes, shipping charges, stock and accepted payment methods before purchase. The preview cannot charge a payment or guarantee availability.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="orders" aria-labelledby="heading-orders">
				<span class="policy-number">05</span>
				<div>
					<h2 id="heading-orders"><?php esc_html_e( 'Order acceptance', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'A saved bag, validated checkout form or prepared bulk enquiry is not an accepted order. The live store will explain when an order is accepted and provide a confirmation. Any inability to fulfil a paid order must be handled under the confirmed terms and applicable law.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="delivery" aria-labelledby="heading-delivery">
				<span class="policy-number">06</span>
				<div>
					<h2 id="heading-delivery"><?php esc_html_e( 'Delivery, cancellations & returns', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Read the Shipping and Returns pages for the current drafts. Final delivery estimates, cancellation procedures, return eligibility and refund arrangements must be disclosed before purchase. Nothing on these draft pages is intended to exclude statutory consumer rights.', 'urban-shisha' ); ?></p>
					<div class="policy-inline-links">
						<a href="<?php echo esc_url( urban_shisha_route_url( 'shipping' ) ); ?>"><?php esc_html_e( 'Shipping & delivery', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
						<a href="<?php echo esc_url( urban_shisha_route_url( 'returns' ) ); ?>"><?php esc_html_e( 'Returns & refunds', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
					</div>
				</div>
			</section>

			<section class="policy-section" id="account" aria-labelledby="heading-account">
				<span class="policy-number">07</span>
				<div>
					<h2 id="heading-account"><?php esc_html_e( 'Accounts & website use', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The current login/register interface is a preview; it does not create a live account. When accounts become available, keep credentials private and use accurate order details. Do not misuse the website, attempt unauthorised access or interfere with another customer’s use.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="bulk" aria-labelledby="heading-bulk">
				<span class="policy-number">08</span>
				<div>
					<h2 id="heading-bulk"><?php esc_html_e( 'Bulk orders & quotations', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Wholesale pricing, minimum quantities, availability and delivery are confirmed in a quotation. Submitting or sharing requirements does not commit either party to an order. Review the agreed quotation and final terms before paying.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="questions" aria-labelledby="heading-questions">
				<span class="policy-number">09</span>
				<div>
					<h2 id="heading-questions"><?php esc_html_e( 'Questions & changes', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Use the Contact page to ask about a term before purchase. Material updates will be dated when these policies are finalised. The live store must provide its support and grievance process alongside its merchant details.', 'urban-shisha' ); ?></p>
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

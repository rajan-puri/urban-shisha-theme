<?php
/**
 * 18+ Age Policy page template.
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
					<p class="eyebrow"><?php esc_html_e( 'URBAN SHISHA / 18+ POLICY', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'ADULTS ONLY.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'ALWAYS.', 'urban-shisha' ); ?></span></h1>
				</div>
				<p class="policy-intro"><?php esc_html_e( 'Urban Shisha is intended for adults aged 18 and over. Please read this before exploring the collection.', 'urban-shisha' ); ?></p>
			</div>
		</div>
	</section>

	<div class="wrap policy-layout">
		<?php get_template_part( 'template-parts/policy', 'sidebar' ); ?>

		<article class="policy-article" aria-label="<?php esc_attr_e( '18+ policy', 'urban-shisha' ); ?>">
			<?php
			$custom_content = get_the_content();
			if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) :
				the_content();
			else :
			?>
			<div class="policy-draft">
				<span><?php esc_html_e( 'POLICY DRAFT · DETAILS UNDER REVIEW', 'urban-shisha' ); ?></span>
				<p><?php esc_html_e( '18+ is the store’s access rule. It is not a claim that every product or use is permitted everywhere; applicable local restrictions still matter.', 'urban-shisha' ); ?></p>
				<small><?php esc_html_e( 'Prepared 8 October 2026. Effective date to be confirmed.', 'urban-shisha' ); ?></small>
			</div>

			<nav class="policy-contents" aria-label="<?php esc_attr_e( 'On this page', 'urban-shisha' ); ?>">
				<p class="eyebrow"><?php esc_html_e( 'ON THIS PAGE', 'urban-shisha' ); ?></p>
				<a href="#access"><span>01</span><?php esc_html_e( 'Who can use this store', 'urban-shisha' ); ?></a>
				<a href="#confirmation"><span>02</span><?php esc_html_e( 'How the age confirmation works', 'urban-shisha' ); ?></a>
				<a href="#orders"><span>03</span><?php esc_html_e( 'Age checks for live orders', 'urban-shisha' ); ?></a>
				<a href="#responsibility"><span>04</span><?php esc_html_e( 'Product use & local restrictions', 'urban-shisha' ); ?></a>
				<a href="#privacy"><span>05</span><?php esc_html_e( 'Age confirmation & privacy', 'urban-shisha' ); ?></a>
				<a href="#help"><span>06</span><?php esc_html_e( 'Questions about this policy', 'urban-shisha' ); ?></a>
			</nav>

			<section class="policy-section" id="access" aria-labelledby="heading-access">
				<span class="policy-number">01</span>
				<div>
					<h2 id="heading-access"><?php esc_html_e( 'Who can use this store', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'You must be 18 or older to explore the Urban Shisha store and to place an order when live checkout becomes available. The products and brand presentation are intended for adults.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="confirmation" aria-labelledby="heading-confirmation">
				<span class="policy-number">02</span>
				<div>
					<h2 id="heading-confirmation"><?php esc_html_e( 'How the age confirmation works', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The popup asks you to declare whether you are 18 or older. Accepting saves that choice in the same browser. It does not collect an identity document or independently verify your age. If you select “I’m under 18”, the store’s access prompt remains in place.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="orders" aria-labelledby="heading-orders">
				<span class="policy-number">03</span>
				<div>
					<h2 id="heading-orders"><?php esc_html_e( 'Age checks for live orders', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Any identity or delivery-age checks required for the live store must be explained before purchase, including what information is needed and how it is handled. No document upload or verified-age service is connected to this preview.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="responsibility" aria-labelledby="heading-responsibility">
				<span class="policy-number">04</span>
				<div>
					<h2 id="heading-responsibility"><?php esc_html_e( 'Product use & local restrictions', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Follow product instructions and applicable rules for purchase, possession and use in your location. Do not supply age-restricted products to minors. Smoking is injurious to health; no product description is a claim that smoking is safe.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="privacy" aria-labelledby="heading-privacy">
				<span class="policy-number">05</span>
				<div>
					<h2 id="heading-privacy"><?php esc_html_e( 'Age confirmation & privacy', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'The current confirmation stores a simple accepted flag in local storage. You can remove it through the Privacy page or your browser’s site-data controls. No date of birth or identity document is stored by this popup.', 'urban-shisha' ); ?></p>
				</div>
			</section>

			<section class="policy-section" id="help" aria-labelledby="heading-help">
				<span class="policy-number">06</span>
				<div>
					<h2 id="heading-help"><?php esc_html_e( 'Questions about this policy', 'urban-shisha' ); ?></h2>
					<p><?php esc_html_e( 'Contact the store before ordering if you have questions about age checks or a product restriction. The live age-verification and order-handling procedure must be confirmed before checkout is connected.', 'urban-shisha' ); ?></p>
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

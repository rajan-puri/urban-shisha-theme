<?php
/**
 * Bulk Orders / Wholesale page template.
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="ws-main">
	<section class="ws-hero" aria-labelledby="ws-title">
		<div class="wrap">
			<nav class="ws-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>

			<div class="ws-hero-layout">
				<div class="ws-hero-copy">
					<p class="eyebrow"><?php esc_html_e( 'FOR YOUR BUSINESS. IN YOUR QUANTITIES.', 'urban-shisha' ); ?></p>
					<h1 id="ws-title"><?php esc_html_e( 'BULK ORDERS.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'BUILT AROUND YOU.', 'urban-shisha' ); ?></span></h1>
					<p><?php esc_html_e( 'Hookahs, accessories and the essentials. Put together your requirements and let’s talk about your next order.', 'urban-shisha' ); ?></p>
					<div class="ws-hero-actions">
						<a class="button" href="#bulk-products">
							<?php esc_html_e( 'Request a quote', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</a>
						<a class="ws-contact-link" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>">
							<?php esc_html_e( 'Talk to us', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
						</a>
					</div>
					<span class="ws-hero-caption"><?php esc_html_e( 'WHOLESALE QUOTES ON REQUEST / 18+ ONLY', 'urban-shisha' ); ?></span>
				</div>

				<div class="ws-hero-art" aria-hidden="true" data-future-scene="wholesale-3d">
					<span class="ws-art-orbit"></span>
					<span class="ws-art-word"><?php esc_html_e( 'THE', 'urban-shisha' ); ?><br><?php esc_html_e( 'BULK', 'urban-shisha' ); ?><br><?php esc_html_e( 'EDIT.', 'urban-shisha' ); ?></span>
                    <?php foreach ( array( 'hookah' => 'hookahs', 'hose' => 'hoses-mouthpieces', 'coal' => 'charcoal' ) as $slot => $category ) :
                        $hero_products = urban_shisha_query_products( array( 'limit' => -1, 'category' => $category ) );
                        $hero_product = null;
                        foreach ( $hero_products as $candidate ) {
                            if ( 'publish' !== get_post_status( $candidate['id'] ) ) { continue; }
                            if ( ! $hero_product ) { $hero_product = $candidate; }
                            if ( false !== strpos( $candidate['cutout_url'], 'product-cutout-' ) ) { $hero_product = $candidate; break; }
                        }
                        if ( ! $hero_product ) { continue; }
                    ?>
                        <div class="ws-art-<?php echo esc_attr( $slot ); ?> product-art has-cutout"><?php echo $hero_product['image_html']; // Escaped by the shared product media renderer. ?></div>
                    <?php endforeach; ?>
					<span class="ws-art-label"><?php esc_html_e( 'YOUR NEXT', 'urban-shisha' ); ?><br><?php esc_html_e( 'BIG ORDER', 'urban-shisha' ); ?> <svg><use href="#i-arrow"/></svg></span>
				</div>
			</div>
		</div>
	</section>

	<section class="section wrap ws-product-section" id="bulk-products" aria-labelledby="ws-products-title">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'START WITH THE PIECES.', 'urban-shisha' ); ?></p>
				<h2 id="ws-products-title"><?php esc_html_e( 'MAKE IT', 'urban-shisha' ); ?><br><?php esc_html_e( 'A BULK EDIT', 'urban-shisha' ); ?><span class="punct">.</span></h2>
			</div>
			<p class="section-note"><?php esc_html_e( 'Pick products. Set quantities.', 'urban-shisha' ); ?><br><?php esc_html_e( 'We’ll quote your requirements.', 'urban-shisha' ); ?></p>
		</div>

		<div class="ws-catalog-layout">
			<div class="ws-catalog">
				<div class="ws-category-tabs" id="ws-categories" role="group" aria-label="<?php esc_attr_e( 'Filter bulk products', 'urban-shisha' ); ?>"></div>
				<label class="ws-search">
					<svg aria-hidden="true"><use href="#i-search"/></svg>
					<span class="sr-only"><?php esc_html_e( 'Search bulk products', 'urban-shisha' ); ?></span>
					<input id="ws-search" type="search" placeholder="<?php esc_attr_e( 'Find a hookah or accessory…', 'urban-shisha' ); ?>" autocomplete="off">
				</label>
				<p class="ws-results-count" id="ws-results-count" role="status" aria-live="polite"></p>
				<div class="ws-product-grid" id="ws-products"></div>
				<div class="ws-no-results" id="ws-no-results" hidden>
					<h3><?php esc_html_e( 'A different edit?', 'urban-shisha' ); ?></h3>
					<p><?php esc_html_e( 'Try another search or category.', 'urban-shisha' ); ?></p>
					<button class="text-link" id="ws-reset-search"><?php esc_html_e( 'Show all products', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
				</div>
			</div>

			<aside class="ws-enquiry" id="enquiry-list" aria-labelledby="ws-enquiry-title">
				<div class="ws-enquiry-heading">
					<h2 id="ws-enquiry-title" tabindex="-1"><?php esc_html_e( 'Your bulk list', 'urban-shisha' ); ?><span class="punct">.</span></h2>
					<button class="ws-clear" id="ws-clear-list" hidden><?php esc_html_e( 'Clear list', 'urban-shisha' ); ?></button>
				</div>
				<p class="ws-enquiry-count" id="ws-enquiry-count" role="status" aria-live="polite"><?php esc_html_e( 'Your next order starts here.', 'urban-shisha' ); ?></p>
				<div id="ws-enquiry-items"></div>
				<div class="ws-enquiry-empty" id="ws-enquiry-empty">
					<svg aria-hidden="true"><use href="#i-box"/></svg>
					<h3><?php esc_html_e( 'A little room', 'urban-shisha' ); ?><br><?php esc_html_e( 'for a bigger order.', 'urban-shisha' ); ?></h3>
					<p><?php esc_html_e( 'Add a product, then set how many pieces you need.', 'urban-shisha' ); ?></p>
				</div>
				<div class="ws-enquiry-bottom">
					<p><?php esc_html_e( 'Wholesale pricing, availability and delivery will be confirmed in your quotation.', 'urban-shisha' ); ?></p>
					<a class="button" href="#business-details"><?php esc_html_e( 'Add business details', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
					<span><?php esc_html_e( 'Enquiry list · separate from your retail bag.', 'urban-shisha' ); ?></span>
				</div>
			</aside>
		</div>
	</section>

	<section class="ws-business-section" id="business-details" aria-labelledby="ws-form-title">
		<div class="wrap ws-form-layout">
			<div class="ws-form-intro">
				<p class="eyebrow"><?php esc_html_e( 'LET’S PUT A NAME TO THE ORDER.', 'urban-shisha' ); ?></p>
				<h2 id="ws-form-title"><?php esc_html_e( 'YOUR BUSINESS.', 'urban-shisha' ); ?><br><?php esc_html_e( 'YOUR NEXT', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'BIG MOVE.', 'urban-shisha' ); ?></span></h2>
				<p><?php esc_html_e( 'Tell us where your order is headed and what you have in mind. Review your enquiry before sharing it with us.', 'urban-shisha' ); ?></p>
				<div class="ws-form-recap">
					<span><?php esc_html_e( 'YOUR REQUIREMENTS', 'urban-shisha' ); ?></span>
					<strong id="ws-form-count"><?php esc_html_e( 'No products selected yet.', 'urban-shisha' ); ?></strong>
					<a href="#enquiry-list"><?php esc_html_e( 'Review your list', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
				</div>
				<p class="ws-form-channel-note" id="ws-channel-note"><?php esc_html_e( 'Select products above, complete your business details and send your enquiry. We’ll confirm availability, wholesale pricing and delivery before any order is placed.', 'urban-shisha' ); ?></p>
			</div>

			<div class="ws-form-container">
				<p class="ws-selection-error" id="ws-selection-error" role="alert" hidden><?php esc_html_e( 'Add at least one product to your bulk list before sending your enquiry.', 'urban-shisha' ); ?></p>
				<?php
				if ( shortcode_exists( 'contact-form-7' ) ) {
					echo do_shortcode( '[contact-form-7 id="562" title="Bulk Order Enquiry"]' );
				} else {
					$custom_content = get_the_content();
					if ( ! empty( trim( wp_strip_all_tags( $custom_content ) ) ) ) {
						the_content();
					}
				}
				?>
			</div>
		</div>
	</section>

	<section class="section wrap ws-process" aria-labelledby="ws-process-title">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'FROM YOUR LIST TO OUR CONVERSATION.', 'urban-shisha' ); ?></p>
				<h2 id="ws-process-title"><?php esc_html_e( 'THREE STEPS.', 'urban-shisha' ); ?><br><?php esc_html_e( 'ONE BIGGER PICTURE', 'urban-shisha' ); ?><span class="punct">.</span></h2>
			</div>
		</div>
		<ol class="ws-process-grid">
			<li>
				<span>01</span>
				<h3><?php esc_html_e( 'Share your edit.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'Choose your products, quantities and business details.', 'urban-shisha' ); ?></p>
			</li>
			<li>
				<span>02</span>
				<h3><?php esc_html_e( 'Talk through the quote.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'Confirm pricing, availability, package contents and delivery with us.', 'urban-shisha' ); ?></p>
			</li>
			<li>
				<span>03</span>
				<h3><?php esc_html_e( 'Confirm your order.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'Proceed once the quotation and final terms work for your business.', 'urban-shisha' ); ?></p>
			</li>
		</ol>
	</section>

	<section class="wrap ws-faq" aria-labelledby="ws-faq-title">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'A FEW THINGS TO KNOW.', 'urban-shisha' ); ?></p>
			<h2 id="ws-faq-title"><?php esc_html_e( 'BEFORE YOU', 'urban-shisha' ); ?><br><?php esc_html_e( 'GO BIG', 'urban-shisha' ); ?><span class="punct">.</span></h2>
		</div>
		<div>
			<details open>
				<summary><?php esc_html_e( 'Can I mix products in one enquiry?', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
				<p><?php esc_html_e( 'Yes. Add different hookahs and accessories to your list and set a quantity for each. Availability and any order requirements are confirmed when we discuss your quote.', 'urban-shisha' ); ?></p>
			</details>
			<details>
				<summary><?php esc_html_e( 'What are the minimum quantities and rates?', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
				<p><?php esc_html_e( 'Minimum quantities and wholesale rates are confirmed per quotation. The list collects your requirements; it does not promise a price or reserve inventory.', 'urban-shisha' ); ?></p>
			</details>
			<details>
				<summary><?php esc_html_e( 'Can I request a preferred delivery date?', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chevron"/></svg></summary>
				<p><?php esc_html_e( 'Include your preferred date and delivery city in the enquiry. Delivery availability, charges and timelines are confirmed before an order is accepted.', 'urban-shisha' ); ?></p>
			</details>
		</div>
	</section>
</main>

<nav class="ws-mobile-list" aria-label="<?php esc_attr_e( 'Bulk enquiry shortcut', 'urban-shisha' ); ?>">
	<span id="ws-mobile-count"><?php esc_html_e( 'Your bulk list', 'urban-shisha' ); ?></span>
	<a class="button" href="#enquiry-list"><?php esc_html_e( 'Review list', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></a>
</nav>

<dialog class="ws-review-dialog" id="ws-review-dialog" aria-labelledby="ws-review-title">
	<div class="ws-review-header">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'REVIEW BEFORE YOU SHARE.', 'urban-shisha' ); ?></p>
			<h2 id="ws-review-title"><?php esc_html_e( 'YOUR ENQUIRY', 'urban-shisha' ); ?><span class="punct">.</span></h2>
		</div>
		<button class="icon-button" id="ws-review-close" aria-label="<?php esc_attr_e( 'Close enquiry review', 'urban-shisha' ); ?>">
			<svg aria-hidden="true"><use href="#i-close"/></svg>
		</button>
	</div>
	<label for="ws-prepared-message"><?php esc_html_e( 'Your prepared request', 'urban-shisha' ); ?></label>
	<textarea id="ws-prepared-message" readonly rows="12"></textarea>
	<p id="ws-review-note"><?php esc_html_e( 'Your request is ready. Copy it to share with the store when contact is available. Nothing has been sent.', 'urban-shisha' ); ?></p>
	<div class="ws-review-actions">
		<button class="button" id="ws-copy-message">
			<?php esc_html_e( 'Copy enquiry', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-copy"/></svg>
		</button>
		<a class="button button-cream" id="ws-send-whatsapp" hidden target="_blank" rel="noopener noreferrer">
			<?php esc_html_e( 'Review on WhatsApp', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-chat"/></svg>
		</a>
		<a class="text-link" id="ws-send-email" hidden>
			<?php esc_html_e( 'Email enquiry', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	</div>
</dialog>

<dialog class="content-dialog" id="ws-info-dialog" aria-labelledby="ws-info-title">
	<div class="content-dialog-header">
		<span class="wordmark"><?php echo esc_html( urban_shisha_get_brand_name() ); ?></span>
		<button class="icon-button" id="ws-info-close" aria-label="<?php esc_attr_e( 'Close information', 'urban-shisha' ); ?>">
			<svg><use href="#i-close"/></svg>
		</button>
	</div>
	<article class="article-content">
		<h2 id="ws-info-title"></h2>
		<p id="ws-info-copy"></p>
		<a class="text-link" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>">
			<?php esc_html_e( 'Contact us', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
		</a>
	</article>
</dialog>
<?php
get_footer();

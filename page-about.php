<?php
/**
 * About Us page template.
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<section class="about-hero">
		<div class="wrap">
			<nav class="info-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-shisha' ); ?>">
				<a href="<?php echo esc_url( urban_shisha_route_url( 'home' ) ); ?>"><?php esc_html_e( 'Home', 'urban-shisha' ); ?></a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<div class="about-hero-grid">
				<div class="about-hero-copy">
					<p class="eyebrow"><?php esc_html_e( 'THIS IS URBAN SHISHA.', 'urban-shisha' ); ?></p>
					<h1><?php esc_html_e( 'GOOD DESIGN.', 'urban-shisha' ); ?><br><span><?php esc_html_e( 'YOUR KIND', 'urban-shisha' ); ?><br><?php esc_html_e( 'OF SETUP.', 'urban-shisha' ); ?></span></h1>
					<p><?php esc_html_e( 'Hookahs with character. Accessories that bring it together. A collection for adults who appreciate the details.', 'urban-shisha' ); ?></p>
					<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
						<?php esc_html_e( 'Explore the collection', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
					</a>
				</div>
				<div class="about-cover" aria-hidden="true">
					<span class="about-cover-word"><?php esc_html_e( 'URBAN', 'urban-shisha' ); ?><br><?php esc_html_e( 'BY DESIGN.', 'urban-shisha' ); ?></span>
					<span class="about-cover-circle"></span>
					<span class="product-art art-0 about-cover-hookah" role="img" aria-label="Hookah">
						<svg class="catalog-photo" viewBox="251 0 767 1254" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
							<image href="<?php echo esc_url( get_theme_file_uri( 'assets/images/product-cutout-0.webp' ) ); ?>" width="1254" height="1254"/>
						</svg>
					</span>
					<span class="about-cover-label"><?php esc_html_e( 'THE DETAILS', 'urban-shisha' ); ?><br><?php esc_html_e( 'MAKE IT YOURS.', 'urban-shisha' ); ?></span>
					<span class="about-cover-caption"><?php esc_html_e( 'URBAN SHISHA / THE COLLECTION', 'urban-shisha' ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<section class="section wrap about-story" aria-labelledby="about-story-title">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'OUR POINT OF VIEW.', 'urban-shisha' ); ?></p>
			<h2 id="about-story-title"><?php esc_html_e( 'IT’S ALL', 'urban-shisha' ); ?><br><?php esc_html_e( 'IN THE', 'urban-shisha' ); ?> <span class="about-outline"><?php esc_html_e( 'DETAILS.', 'urban-shisha' ); ?></span></h2>
		</div>
		<div class="about-story-copy">
			<p class="about-lead"><?php esc_html_e( 'A setup is a collection of choices. We give each one room to stand out.', 'urban-shisha' ); ?></p>
			<p><?php esc_html_e( 'Urban Shisha brings hookahs and their accessories into one place. From a distinctive glass base to the bowl, hose and tools that complete it, the focus is on the pieces and how they come together.', 'urban-shisha' ); ?></p>
			<p><?php esc_html_e( 'Whether you’re exploring your first setup, choosing a replacement part or putting together an order for your business, start with the collection. If you need help with fit or product details, talk to us before you buy.', 'urban-shisha' ); ?></p>
			<a class="text-link" href="<?php echo esc_url( urban_shisha_route_url( 'contact' ) ); ?>">
				<?php esc_html_e( 'Let’s talk details', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
	</section>

	<section class="about-collection">
		<div class="section wrap">
			<div class="section-heading">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'THE PIECES. THE POSSIBILITIES.', 'urban-shisha' ); ?></p>
					<h2><?php esc_html_e( 'ONE STORE.', 'urban-shisha' ); ?><br><?php esc_html_e( 'YOUR WHOLE SETUP', 'urban-shisha' ); ?><span class="punct">.</span></h2>
				</div>
				<p class="section-note"><?php esc_html_e( 'Discover the statement piece.', 'urban-shisha' ); ?><br><?php esc_html_e( 'Then find its supporting cast.', 'urban-shisha' ); ?></p>
			</div>
			<div class="about-category-grid">
				<a class="about-category" href="<?php echo esc_url( urban_shisha_route_url( 'shop', array( 'category' => 'hookahs' ) ) ); ?>">
					<div class="about-category-photo">
						<span class="product-art art-2" aria-hidden="true">
							<svg class="catalog-photo" viewBox="0 71 1254 1088" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
								<image href="<?php echo esc_url( get_theme_file_uri( 'assets/images/product-cutout-2.webp' ) ); ?>" width="1254" height="1254"/>
							</svg>
						</span>
					</div>
					<div class="about-category-copy">
						<span><?php esc_html_e( '01 / THE CENTREPIECE', 'urban-shisha' ); ?></span>
						<h3><?php esc_html_e( 'Hookahs with character.', 'urban-shisha' ); ?></h3>
						<p><?php esc_html_e( 'Explore shapes, finishes and styles. Find the one that belongs in your setup.', 'urban-shisha' ); ?></p>
						<span class="text-link"><?php esc_html_e( 'Shop hookahs', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
					</div>
				</a>
				<a class="about-category" href="<?php echo esc_url( urban_shisha_route_url( 'shop', array( 'category' => 'accessories' ) ) ); ?>">
					<div class="about-category-photo">
						<span class="product-art art-4" aria-hidden="true">
							<svg class="catalog-photo" viewBox="7 116 1230 1084" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
								<image href="<?php echo esc_url( get_theme_file_uri( 'assets/images/product-cutout-4.webp' ) ); ?>" width="1254" height="1254"/>
							</svg>
						</span>
					</div>
					<div class="about-category-copy">
						<span><?php esc_html_e( '02 / THE FINISHING TOUCHES', 'urban-shisha' ); ?></span>
						<h3><?php esc_html_e( 'Every piece matters.', 'urban-shisha' ); ?></h3>
						<p><?php esc_html_e( 'Bowls, heat management, hoses and tools. Give the small details some attention.', 'urban-shisha' ); ?></p>
						<span class="text-link"><?php esc_html_e( 'Explore accessories', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
					</div>
				</a>
				<a class="about-category" href="<?php echo esc_url( urban_shisha_route_url( 'wholesale' ) ); ?>">
					<div class="about-category-photo">
						<span class="product-art art-10" aria-hidden="true">
							<svg class="catalog-photo" viewBox="100 47 1103 1182" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
								<image href="<?php echo esc_url( get_theme_file_uri( 'assets/images/product-cutout-10.webp' ) ); ?>" width="1254" height="1254"/>
							</svg>
						</span>
					</div>
					<div class="about-category-copy">
						<span><?php esc_html_e( '03 / FOR YOUR BUSINESS', 'urban-shisha' ); ?></span>
						<h3><?php esc_html_e( 'A bigger kind of order.', 'urban-shisha' ); ?></h3>
						<p><?php esc_html_e( 'Build a product list with your quantities and request a quotation for your business.', 'urban-shisha' ); ?></p>
						<span class="text-link"><?php esc_html_e( 'Explore bulk orders', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></span>
					</div>
				</a>
			</div>
		</div>
	</section>

	<section class="section wrap about-approach" aria-labelledby="about-approach-title">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'HOW WE LOOK AT IT.', 'urban-shisha' ); ?></p>
				<h2 id="about-approach-title"><?php esc_html_e( 'LESS GUESSWORK.', 'urban-shisha' ); ?><br><?php esc_html_e( 'MORE DETAIL', 'urban-shisha' ); ?><span class="punct">.</span></h2>
			</div>
		</div>
		<ol class="about-principles">
			<li>
				<span>01</span>
				<h3><?php esc_html_e( 'See the product.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'Large photographs and clear product information help you look closer before choosing.', 'urban-shisha' ); ?></p>
			</li>
			<li>
				<span>02</span>
				<h3><?php esc_html_e( 'Check the fit.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'Parts aren’t universal. Check dimensions and compatibility, or ask us about a specific combination.', 'urban-shisha' ); ?></p>
			</li>
			<li>
				<span>03</span>
				<h3><?php esc_html_e( 'Ask your questions.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'From a single accessory to a bulk enquiry, share what you’re looking for so we can discuss the details.', 'urban-shisha' ); ?></p>
			</li>
		</ol>
	</section>

	<section class="wrap about-adults">
		<p class="eyebrow"><?php esc_html_e( 'A COLLECTION FOR ADULTS.', 'urban-shisha' ); ?></p>
		<div>
			<h2><?php esc_html_e( '18+.', 'urban-shisha' ); ?><br><?php esc_html_e( 'ALWAYS.', 'urban-shisha' ); ?></h2>
			<p><?php esc_html_e( 'Urban Shisha is intended for adults aged 18 and over. Smoking is injurious to health. Please follow product instructions and applicable local rules.', 'urban-shisha' ); ?></p>
			<a class="text-link" href="<?php echo esc_url( urban_shisha_route_url( 'age-policy' ) ); ?>">
				<?php esc_html_e( 'Read our age policy', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
	</section>

	<section class="about-closing">
		<div class="wrap">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'YOUR NEXT FIND STARTS HERE.', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'MAKE IT', 'urban-shisha' ); ?><br><?php esc_html_e( 'YOUR OWN', 'urban-shisha' ); ?><span class="punct">.</span></h2>
			</div>
			<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
				<?php esc_html_e( 'Find your next piece', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
			</a>
		</div>
	</section>
</main>
<?php
get_footer();

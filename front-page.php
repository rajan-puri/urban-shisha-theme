<?php
/**
 * The dynamic front page template for Urban Shisha.
 *
 * Renders flexible homepage sections registered via SCF,
 * preserving the exact approved design, GSAP animations, and WooCommerce integration.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="page-loader" hidden aria-hidden="true">
	<div class="loader-wordmark">URBAN SHISHA<span>NEXT EDIT / 18+</span></div>
	<svg class="loader-puff" viewBox="0 0 180 120"><path d="M10 90C60 130 140 60 80 30S20 80 165 15"/></svg>
</div>
<div class="studio-cursor" hidden aria-hidden="true"><span>DRAG</span></div>

<main id="main">
<?php
urban_shisha_render_home_sections();
?>
</main>

<dialog class="content-dialog" id="content-dialog" aria-labelledby="content-title">
	<div class="content-dialog-header">
		<span class="wordmark"><?php urban_shisha_render_brand_logo( 'dialog' ); ?></span>
		<button type="button" class="icon-button close-dialog" aria-label="<?php esc_attr_e( 'Close details', 'urban-shisha' ); ?>">
			<svg aria-hidden="true"><use href="#i-close"/></svg>
		</button>
	</div>
	<div id="content-body"></div>
</dialog>

<?php
get_footer();

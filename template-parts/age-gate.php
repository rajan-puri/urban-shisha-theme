<?php
/**
 * Shared age confirmation dialog and toast element.
 */
defined( 'ABSPATH' ) || exit;

$age_eyebrow          = urban_shisha_get_option( 'age_eyebrow', 'A CONSIDERED COLLECTION. FOR ADULTS ONLY.' );
$age_heading          = urban_shisha_get_option( 'age_heading', "GOOD TO\nSEE YOU." );
$age_description      = urban_shisha_get_option( 'age_description', 'You must be 18 or older to explore Urban Shisha.' );
$age_accept_label     = urban_shisha_get_option( 'age_accept_label', 'I’m 18 or older' );
$age_decline_label    = urban_shisha_get_option( 'age_decline_label', 'I’m under 18' );
$age_declined_message = urban_shisha_get_option( 'age_declined_message', 'This store is for adults aged 18 and over.' );
$health_notice        = urban_shisha_get_option( 'health_notice', 'Smoking is injurious to health.' );
?>
<!-- Age Confirmation Gate -->
<dialog class="age-dialog" id="age-dialog" aria-labelledby="age-title">
	<div class="age-panel">
		<span class="wordmark"><?php urban_shisha_render_brand_logo( 'dialog' ); ?></span>
		<?php if ( $age_eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $age_eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( $age_heading ) : ?>
			<h2 id="age-title"><?php echo nl2br( esc_html( $age_heading ) ); ?></h2>
		<?php endif; ?>
		<?php if ( $age_description ) : ?>
			<p><?php echo nl2br( esc_html( $age_description ) ); ?></p>
		<?php endif; ?>
		<div class="age-actions">
			<button type="button" class="button close-dialog" id="age-accept"><?php echo esc_html( $age_accept_label ); ?> <svg><use href="#i-arrow"/></svg></button>
			<button type="button" class="age-decline" id="age-decline"><?php echo esc_html( $age_decline_label ); ?></button>
		</div>
		<p class="age-declined" id="age-declined" hidden><?php echo nl2br( esc_html( $age_declined_message ) ); ?></p>
		<?php if ( $health_notice ) : ?>
			<small><?php echo esc_html( $health_notice ); ?></small>
		<?php endif; ?>
	</div>
</dialog>

<!-- Toast notification element -->
<div class="toast" id="toast" role="status" aria-live="polite" aria-atomic="true"></div>

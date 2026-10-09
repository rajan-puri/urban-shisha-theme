<?php
/**
 * Shared age confirmation dialog and toast element.
 */
defined( 'ABSPATH' ) || exit;

$age_eyebrow          = urban_shisha_get_option( 'age_eyebrow', '' );
$age_heading          = urban_shisha_get_option( 'age_heading', '' );
$age_description      = urban_shisha_get_option( 'age_description', '' );
$age_accept_label     = urban_shisha_get_option( 'age_accept_label', '' );
$age_decline_label    = urban_shisha_get_option( 'age_decline_label', '' );
$age_declined_message = urban_shisha_get_option( 'age_declined_message', '' );
$health_notice        = urban_shisha_get_option( 'health_notice', '' );

// Functional defaults for required interactive/safety labels
$age_title_text    = $age_heading ? nl2br( esc_html( $age_heading ) ) : esc_html__( 'Age confirmation', 'urban-shisha' );
$age_accept_text   = $age_accept_label ?: __( 'I’m 18 or older', 'urban-shisha' );
$age_decline_text  = $age_decline_label ?: __( 'I’m under 18', 'urban-shisha' );
$age_declined_text = $age_declined_message ?: __( 'This store is for adults aged 18 and over.', 'urban-shisha' );
?>
<!-- Age Confirmation Gate -->
<dialog class="age-dialog" id="age-dialog" aria-labelledby="age-title">
	<div class="age-panel">
		<span class="wordmark"><?php urban_shisha_render_brand_logo( 'dialog' ); ?></span>
		<?php if ( $age_eyebrow ) : ?>
			<p class="eyebrow"><?php echo esc_html( $age_eyebrow ); ?></p>
		<?php endif; ?>
		<h2 id="age-title"><?php echo $age_title_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
		<?php if ( $age_description ) : ?>
			<p><?php echo nl2br( esc_html( $age_description ) ); ?></p>
		<?php endif; ?>
		<div class="age-actions">
			<button type="button" class="button close-dialog" id="age-accept"><?php echo esc_html( $age_accept_text ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg></button>
			<button type="button" class="age-decline" id="age-decline"><?php echo esc_html( $age_decline_text ); ?></button>
		</div>
		<p class="age-declined" id="age-declined" hidden><?php echo nl2br( esc_html( $age_declined_text ) ); ?></p>
		<?php if ( $health_notice ) : ?>
			<small><?php echo esc_html( $health_notice ); ?></small>
		<?php endif; ?>
	</div>
</dialog>

<!-- Toast notification element -->
<div class="toast" id="toast" role="status" aria-live="polite" aria-atomic="true"></div>

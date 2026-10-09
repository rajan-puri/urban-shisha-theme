<?php
/** Native variation fields with approved purchase controls. @version 10.5.2 */
defined( 'ABSPATH' ) || exit;
global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
<?php get_template_part( 'template-parts/product/purchase', null, array( 'product' => $product ) ); ?>
<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
<input type="hidden" name="variation_id" class="variation_id" value="0">
<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</div>

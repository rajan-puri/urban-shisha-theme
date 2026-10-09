<?php
defined( 'ABSPATH' ) || exit;
$product = $args['product'];
$available = 'publish' === $product->get_status() && $product->is_in_stock() && $product->is_purchasable();
?>
<div class="pdp-buy-controls">
<?php if ( ! $product->is_sold_individually() ) : ?>
<?php do_action( 'woocommerce_before_add_to_cart_quantity' ); ?><div class="pdp-quantity"><button type="button" data-quantity="-1" aria-label="Decrease quantity">−</button><?php woocommerce_quantity_input( array( 'min_value' => $product->get_min_purchase_quantity(), 'max_value' => $product->get_max_purchase_quantity() ), $product ); ?><button type="button" data-quantity="1" aria-label="Increase quantity">+</button></div><?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>
<?php else : ?><input type="hidden" name="quantity" value="1"><?php endif; ?>
<button type="submit" class="single_add_to_cart_button button pdp-add" <?php disabled( ! $available ); ?>><?php echo $available ? 'Add to bag' : ( 'publish' !== $product->get_status() ? 'Preview only' : 'Out of stock' ); ?></button>
<button type="button" class="pdp-save" data-save="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="Save product" aria-pressed="false"><svg aria-hidden="true"><use href="#i-heart"/></svg></button>
</div>

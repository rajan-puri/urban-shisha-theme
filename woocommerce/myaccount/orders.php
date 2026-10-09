<?php
/**
 * My Account Orders List Template.
 *
 * Matches approved account.html orders section and empty state.
 *
 * @package UrbanShishaTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>
<div class="account-section" id="section-orders">
	<div class="section-card">
		<div class="section-header">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'ORDER HISTORY', 'urban-shisha' ); ?></p>
				<h2><?php esc_html_e( 'Orders & tracking.', 'urban-shisha' ); ?></h2>
				<p><?php esc_html_e( 'Review past purchases, download invoices and check active shipments.', 'urban-shisha' ); ?></p>
			</div>
		</div>

		<?php if ( $has_orders ) : ?>
			<div class="orders-table-wrapper" style="overflow-x: auto;">
				<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive account-orders-table" style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">
					<thead>
						<tr style="text-align: left; font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: .06em;">
							<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
								<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $column_id ); ?>" style="padding: 12px 16px; border-bottom: 2px solid var(--ink); text-transform: uppercase;">
									<span><?php echo esc_html( $column_name ); ?></span>
								</th>
							<?php endforeach; ?>
						</tr>
					</thead>

					<tbody>
						<?php
						foreach ( $customer_orders->orders as $customer_order ) :
							$order      = wc_get_order( $customer_order );
							$item_count = $order->get_item_count();
							?>
							<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> order" style="background: var(--bg); border: 1.5px solid var(--ink); box-shadow: 2px 2px 0 var(--ink); border-radius: 12px;">
								<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
									<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>" style="padding: 16px; border-top: 1px solid rgba(20,18,31,0.08); vertical-align: middle;">
										<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
											<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>
										<?php elseif ( 'order-number' === $column_id ) : ?>
											<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" style="font-weight: 700; color: var(--ink); text-decoration: underline;">
												#<?php echo esc_html( $order->get_order_number() ); ?>
											</a>
										<?php elseif ( 'order-date' === $column_id ) : ?>
											<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
												<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
											</time>
										<?php elseif ( 'order-status' === $column_id ) : ?>
											<span class="status-badge" style="display: inline-block; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--ink); font-size: 11px; font-weight: 700; background: var(--soft);">
												<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
											</span>
										<?php elseif ( 'order-total' === $column_id ) : ?>
											<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
											<span style="font-size: 12px; color: var(--muted); display: block;">
												<?php printf( esc_html( _n( 'for %s item', 'for %s items', $item_count, 'urban-shisha' ) ), esc_html( (string) $item_count ) ); ?>
											</span>
										<?php elseif ( 'order-actions' === $column_id ) : ?>
											<?php
											$actions = wc_get_account_orders_actions( $order );
											if ( ! empty( $actions ) ) {
												foreach ( $actions as $key => $action ) {
													echo '<a href="' . esc_url( $action['url'] ) . '" class="button button-small ' . sanitize_html_class( $key ) . '" style="padding: 6px 14px; font-size: 12px;">' . esc_html( $action['name'] ) . '</a>';
												}
											}
											?>
										<?php endif; ?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

			<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
				<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination" style="margin-top: 24px; display: flex; justify-content: space-between;">
					<?php if ( 1 !== $current_page ) : ?>
						<a class="woocommerce-button button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'urban-shisha' ); ?></a>
					<?php endif; ?>

					<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
						<a class="woocommerce-button button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'urban-shisha' ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<div class="empty-state-box" id="orders-empty">
				<div class="empty-state-icon"><svg aria-hidden="true"><use href="#i-box"/></svg></div>
				<h3><?php esc_html_e( 'No orders yet.', 'urban-shisha' ); ?></h3>
				<p><?php esc_html_e( 'You haven’t placed any orders yet. When you complete checkout, your order tracking details and invoices will appear here.', 'urban-shisha' ); ?></p>
				<a class="button" href="<?php echo esc_url( urban_shisha_route_url( 'shop' ) ); ?>">
					<?php esc_html_e( 'Explore the shop', 'urban-shisha' ); ?> <svg aria-hidden="true"><use href="#i-arrow"/></svg>
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
do_action( 'woocommerce_after_account_orders', $has_orders );

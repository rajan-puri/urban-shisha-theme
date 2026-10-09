<?php
/** Server-side Telegram order notifications. Credentials belong in private server config. */
defined( 'ABSPATH' ) || exit;

function urban_shisha_queue_telegram_order( $order ): void {
	$order = $order instanceof WC_Order ? $order : wc_get_order( $order );
	if ( ! $order || $order->get_meta( '_tg_notified' ) || ! defined( 'TG_BOT_TOKEN' ) || ! defined( 'TG_CHAT_ID' ) ) { return; }
	$args = array( $order->get_id(), 0 );
	if ( function_exists( 'as_enqueue_async_action' ) ) {
		as_enqueue_async_action( 'urban_shisha_telegram_order', $args, 'urban-shisha', true );
	} elseif ( ! wp_next_scheduled( 'urban_shisha_telegram_order', $args ) ) {
		wp_schedule_single_event( time() + 1, 'urban_shisha_telegram_order', $args );
	}
}
add_action( 'woocommerce_checkout_order_processed', 'urban_shisha_queue_telegram_order', 20, 1 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'urban_shisha_queue_telegram_order', 20, 1 );

function urban_shisha_telegram_plain_text( $text ): string {
	$text = preg_replace( '/<br\s*\/?\s*>/i', ', ', (string) $text );
	return html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' );
}

function urban_shisha_send_telegram_order( $order_id, $attempt = 0 ): void {
	if ( ! defined( 'TG_BOT_TOKEN' ) || ! defined( 'TG_CHAT_ID' ) || ! TG_BOT_TOKEN || ! TG_CHAT_ID ) { return; }
	$order = wc_get_order( absint( $order_id ) );
	if ( ! $order || $order->get_meta( '_tg_notified' ) ) { return; }
	// Atomic database lock covers concurrent Classic/Block hooks and queue runners.
	$lock = 'urban_shisha_tg_lock_' . $order->get_id();
	if ( ! add_option( $lock, time(), '', false ) ) { return; }
	try {
		$order->read_meta_data( true );
		if ( $order->get_meta( '_tg_notified' ) ) { return; }
		$text = '🛒 New Order #' . $order->get_order_number() . "\n";
		$text .= 'Status: ' . wc_get_order_status_name( $order->get_status() ) . "\n\n";
		$text .= '👤 ' . urban_shisha_telegram_plain_text( $order->get_formatted_billing_full_name() ) . "\n";
		$text .= '📞 ' . $order->get_billing_phone() . "\n";
		$text .= '💳 ' . $order->get_payment_method_title() . "\n";
		$text .= '💰 Total: ' . urban_shisha_telegram_plain_text( $order->get_formatted_order_total() ) . "\n\nItems:\n";
		foreach ( $order->get_items() as $item ) {
			$text .= '• ' . urban_shisha_telegram_plain_text( $item->get_name() ) . ' × ' . $item->get_quantity() . "\n";
		}
		$text .= "\n📍 " . urban_shisha_telegram_plain_text( $order->get_formatted_shipping_address() ?: $order->get_formatted_billing_address() );
		// Telegram caps message length. Keep one message per order and a conservative Unicode limit.
		if ( function_exists( 'mb_substr' ) ) { $text = mb_substr( $text, 0, 4000, 'UTF-8' ); }
		else { $text = wp_html_excerpt( $text, 4000, '' ); }
		$response = wp_remote_post( 'https://api.telegram.org/bot' . TG_BOT_TOKEN . '/sendMessage', array(
			'timeout' => 15,
			'redirection' => 0,
			'body' => array( 'chat_id' => TG_CHAT_ID, 'text' => $text ),
		) );
		$payload = is_wp_error( $response ) ? array() : json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) && ! empty( $payload['ok'] ) ) {
			$order->update_meta_data( '_tg_notified', 1 );
			$order->update_meta_data( '_tg_message_id', absint( $payload['result']['message_id'] ?? 0 ) );
			$order->delete_meta_data( '_tg_delivery_error' );
			$order->save();
			return;
		}
		// Never store a raw HTTP error/URL: it can contain the bot token.
		$order->update_meta_data( '_tg_delivery_error', 'Telegram delivery failed; attempt ' . ( absint( $attempt ) + 1 ) );
		$order->save();
		if ( $attempt < 2 ) {
			$delay = max( 60 * ( $attempt + 1 ), absint( $payload['parameters']['retry_after'] ?? 0 ) );
			$args = array( $order->get_id(), absint( $attempt ) + 1 );
			if ( function_exists( 'as_schedule_single_action' ) ) { as_schedule_single_action( time() + $delay, 'urban_shisha_telegram_order', $args, 'urban-shisha', true ); }
			else { wp_schedule_single_event( time() + $delay, 'urban_shisha_telegram_order', $args ); }
		}
	} finally { delete_option( $lock ); }
}
add_action( 'urban_shisha_telegram_order', 'urban_shisha_send_telegram_order', 10, 2 );

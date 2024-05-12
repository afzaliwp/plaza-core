<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

defined( 'ABSPATH' ) || die();

class Checkout {
	public function __construct() {
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'handle_plaza_suggests' ], 10, 1 );
		add_action( 'woocommerce_cart_calculate_fees', [ $this, 'add_extra_fee' ] );
	}

	public function handle_plaza_suggests( $order_id ) {
		if ( !isset( $_COOKIE[ 'plazaSuggests' ] ) ) {
			return;
		}

		$plaza_suggest = json_decode( stripslashes( $_COOKIE[ 'plazaSuggests' ] ), ARRAY_A );

		$text = '';
		foreach ( $plaza_suggest as $product_id => $cookie ) {
			if ( is_integer( $product_id ) ) {
				$text .= 'محصول: ' . $cookie[ 'product_title' ] . PHP_EOL;

				foreach ( $cookie[ 'description' ] as $desc ) {
					$text .= $desc . PHP_EOL;
				}

				$text .= PHP_EOL;
			}
		}

		$order = wc_get_order( $order_id );

		$order_comment = $order->get_customer_note();
		$order_comment .= PHP_EOL . PHP_EOL . $text;

		$order->set_customer_note( $order_comment );
		$order->save();

		unset( $_COOKIE[ 'plazaSuggests' ] );
		setcookie( 'plazaSuggests', '', time() - 3600, '/' );
	}

	public function add_extra_fee() {
		if ( !isset( $_COOKIE[ 'plazaSuggests' ] ) ) {
			return;
		}

		$plaza_suggest = json_decode( stripslashes( $_COOKIE[ 'plazaSuggests' ] ), ARRAY_A );

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$product_id = $cart_item[ 'product_id' ];

			if ( isset( $plaza_suggest[ $product_id ] ) && in_array( 'فعال‌سازی سنسورهای اکسیژن خون و ECG', $plaza_suggest[ $product_id ][ 'description' ] ) ) {
				WC()->cart->add_fee( 'فعال‌سازی ECG ساعت هوشمند ' . $product_id, 2500000 );
			}
		}
	}
}
<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

defined( 'ABSPATH' ) || die();

class Checkout {
	public function __construct() {
		add_action( 'woocommerce_thankyou', [ $this, 'handle_plaza_suggests' ], 10, 1 );
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
}
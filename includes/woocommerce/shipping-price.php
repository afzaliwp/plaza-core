<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

defined( 'ABSPATH' ) || die();

class Shipping_Price {
	public function __construct() {
		$current_timestamp = current_time( 'timestamp' );
		update_option( '_transient_shipping-transient-version', $current_timestamp );
		update_option( '_transient_timeout_wc_shipping_method_count', $current_timestamp );
		add_filter( 'woocommerce_package_rates', [ $this, 'modify_shipping_methods' ], 99, 2 );
	}

	public function modify_shipping_methods( $rates, $package ) {
		if ( ! isset( $rates[ 'flat_rate:24' ] ) ) {
			return $rates;
		}

		global $woocommerce;
		$cart_product_categories = [];
		$cart_total = $woocommerce->cart->get_cart_contents_total();
		$product_categories = [ 'printer', 'monitor', 'all-in-one', 'assembled-cases' ];

		if ( ! $woocommerce->cart->get_cart() ) {
			return $rates;
		}

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values[ 'data' ];
			$product_id = $_product->get_id();

			if ( 'variation' === $_product->get_type() ) {
				$product_id = $_product->get_parent_id();
			}

			$terms = get_the_terms( $product_id, 'product_cat' );

			if ( $terms ) {
				foreach ( $terms as $term ) {
					$cart_product_categories[] = $term->slug;
				}
			}
		}

		$intersection = array_intersect( array_unique( $cart_product_categories ), $product_categories );

		if ( count( $intersection ) >= 2 ) {
			$rates[ 'flat_rate:24' ]->set_label( 'هزینه ارسال پس از ثبت سفارش محاسبه خواهد شد.' );
			$rates[ 'flat_rate:24' ]->set_cost( 0 );
			return $rates;
		}

		if ( in_array( 'assembled-cases', $cart_product_categories ) ) {
			$rates[ 'flat_rate:24' ]->set_cost( 180000 );
			return $rates;
		}

		mylog($cart_product_categories, '$cart_product_categories');
		if (
			in_array( 'monitor', $cart_product_categories ) ||
			in_array( 'all-in-one', $cart_product_categories ) ||
			in_array( 'printer', $cart_product_categories )
		) {
			$rates[ 'flat_rate:24' ]->set_cost( 200000 + ( intval( $cart_total / 10000000 ) * 50000 ) );
			return $rates;
		}

		if ( $cart_total <= 5000000 ) {
			$rates[ 'flat_rate:24' ]->set_cost( 40000 );
			return $rates;
		}

		if ( $cart_total <= 10000000 ) {
			$rates[ 'flat_rate:24' ]->set_cost( 50000 );
			return $rates;
		}

		if ( $cart_total <= 15000000 ) {
			$rates[ 'flat_rate:24' ]->set_cost( 80000 );
			return $rates;
		}

		if ( $cart_total <= 20000000 ) {
			$rates[ 'flat_rate:24' ]->set_cost( 100000 );
			return $rates;
		}

		$rates[ 'flat_rate:24' ]->set_cost( floatval( 100000 + ( intval( $cart_total / 5000000 ) * 20000 ) ) );
		return $rates;
	}
}

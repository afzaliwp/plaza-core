<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

defined( 'ABSPATH' ) || die();

class Shipping_Price {
	public function __construct() {
		add_filter( 'woocommerce_package_rates', [ $this, 'modify_shipping_methods' ], 10, 2 );
	}

	public function modify_shipping_methods( $rates, $package ) {
		global $woocommerce;
		$cart_total = $woocommerce->cart->get_cart_contents_total();
		$shipping_cost = 0;
		$product_categories = [ 'printer', 'monitor', 'all-in-one', 'assembled-cases' ];
		$cart_product_categories = [];

		foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values[ 'data' ];
			$terms = get_the_terms( $_product->id, 'product_cat' );

			foreach ( $terms as $term ) {
				$cart_product_categories[] = $term->name;
			}
		}

		$intersect = array_intersect( $product_categories, $cart_product_categories );

		if ( empty( $intersect ) ) {
			// Rule 3
			if ( $cart_total > 20000000 ) {
				$shipping_cost = 100000 + floor( ( $cart_total - 20000000 ) / 5000000 ) * 20000;
			} else {
				$shipping_cost = 40000 + floor( $cart_total / 5000000 ) * 10000;
			}
		} else if ( in_array( 'assembled-cases', $intersect ) && count( $intersect ) == 1 ) {
			// Rule 5
			$shipping_cost = 180000;
		} else if ( in_array( 'printer', $intersect ) || in_array( 'monitor', $intersect ) || in_array( 'all-in-one', $intersect ) ) {
			// Rule 4
			if ( $cart_total > 30000000 ) {
				$shipping_cost = 200000 + floor( ( $cart_total - 10000000 ) / 10000000 ) * 50000;
			} else {
				$shipping_cost = 200000 + floor( $cart_total / 10000000 ) * 50000;
			}
		} else {
			// Rule 6
			$shipping_cost = 0;
			$rates[ 'flat_rate:24' ]->label = "Shipping with post (the shipping price will be calculated after the payment is done)";
		}

		if ( isset( $rates[ 'flat_rate:24' ] ) ) {
			$rates[ 'flat_rate:24' ]->cost = $shipping_cost;
		}

		return $rates;
	}
}

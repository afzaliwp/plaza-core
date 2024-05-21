<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders;

defined( 'ABSPATH' ) || die();

class Order_List {
	public function __construct() {
		add_action( 'restrict_manage_posts', [ $this, 'filter_orders_by_order_category' ], 20 );
		add_filter( 'request', [ $this, 'filter_orders_by_order_category_query' ] );
	}

	public function filter_orders_by_order_category() {
		global $typenow;
		if ( 'shop_order' !== $typenow ) {
			return;
		}

		$order_categories = [
			'لپتاپ',
			'موبایل و تبلت',
			'آل این وان مانیتور',
			'کنسول',
			'پرینتر',
			'قطعات',
			'جانبی',
		];

		echo '<select name="plaza-order-category">';
		echo '<option value="">' . __( 'Filter by Order Category', 'woocommerce' ) . '</option>';
		foreach ( $order_categories as $order_category ) {
			echo '<option ' . selected( $_GET[ 'plaza-order-category' ], $order_category ) . ' value="' . $order_category . '">' . $order_category . '</option>';
		}
		echo '</select>';
	}

	public function filter_orders_by_order_category_query( $vars ) {
		global $typenow;
		if ( 'shop_order' === $typenow && isset( $_GET[ 'plaza-order-category' ] ) ) {
			$vars[ 'meta_key' ] = 'plaza-order-category';
			$vars[ 'meta_value' ] = $_GET[ 'plaza-order-category' ];
		}
		return $vars;
	}

}
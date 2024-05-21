<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders;

defined( 'ABSPATH' ) || die();

class Order_List {
	public function __construct() {
		add_action( 'restrict_manage_posts', [ $this, 'filter_orders_by_order_category' ], 20 );
		add_filter( 'request', [ $this, 'filter_orders_by_order_category_query' ] );

		add_action( 'wp', [ $this, 'setup_order_expiry_cron_job' ] );
		add_action( 'check_pending_orders', [ $this, 'cancel_pending_orders' ] );
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
		if ( 'shop_order' === $typenow && !empty( $_GET[ 'plaza-order-category' ] ) ) {
			$vars[ 'meta_key' ] = 'plaza-order-category';
			$vars[ 'meta_value' ] = $_GET[ 'plaza-order-category' ];
		}
		return $vars;
	}

	public function setup_order_expiry_cron_job() {
		if ( !wp_next_scheduled( 'check_pending_orders' ) ) {
			wp_schedule_event( time(), 'hourly', 'check_pending_orders' );
		}
	}

	public function cancel_pending_orders() {
		$hours_delay = 10;
		$args = array(
			'status' => 'wc-pending',
			'date_modified' => '<=' . ( time() - ( $hours_delay * 3600 ) ),
			'return' => 'ids',
		);
		$orders = wc_get_orders( $args );
		if ( !empty( $orders ) ) {
			foreach ( $orders as $order_id ) {
				$order = wc_get_order( $order_id );
				$order->update_status( 'wc-cancelled', 'این سفارش بیش از ' . $hours_delay . ' ساعت در حالت در انتظار پرداخت بود و لغو شد.' );
			}
		}
	}
}
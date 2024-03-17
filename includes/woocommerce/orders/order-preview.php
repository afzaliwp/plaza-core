<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders;

use AfzaliWP\PlazaDigital\Includes\SMS\KaveNegar;

defined( 'ABSPATH' ) || die();

class Order_Preview {

	private $pattern = 'sendzpallink';
	public function __construct() {
		add_action( 'wp_ajax_plaza_admin_send_zpal_link_sms', [ $this, 'handle_sms_delivery' ] );
		add_action( 'woocommerce_admin_order_preview_start', [ $this, 'order_preview_data' ] );
	}

	public function handle_sms_delivery() {
		$order = wc_get_order( $_POST[ 'post_id' ] );
		$phone_number = $order->get_billing_phone();

		// Send SMS
		$sms = new KaveNegar();
		$response = $sms->lookup( $phone_number, $this->pattern, $_POST['post_id'] );
		$sms_status = $response[ 'status' ] ? 'SMS sent successfully: ' . $response[ 'message' ] : 'Failed to send SMS: ' . $response[ 'message' ];

		$sms_status .= ' - ' . date( 'Y-m-d H:i:s' ); // Append the current date and time

		wp_send_json_success( $sms_status );
	}

	public function order_preview_data() {
		global $post;

		?>
		<div class="send-zpal-link-order-preview">
            <h4>جهت ارسال پیامک لینک زرین پال برای پرداخت بیعانه، روی دکمه زیر کلیک کنید.</h4>
			<button
                    data-order-id="<?php echo $post->ID; ?>"
                    class="button button-primary send-zpal-link-button">
                ارسال پیامک لینک زرین پال
            </button>

            <div class="results"></div>
		</div>
		<?php
	}
}
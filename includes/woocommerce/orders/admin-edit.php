<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders;

use AfzaliWP\PlazaDigital\Includes\SMS\KaveNegar;

defined( 'ABSPATH' ) || die();

class Admin_Edit {

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_custom_meta_box' ] );
		add_action( 'wp_ajax_plaza_admin_send_sms', [ $this, 'handle_sms_delivery' ] );
	}

	public function add_custom_meta_box() {
		add_meta_box(
			'custom_order_meta_box', // ID of meta box
            'ارسال پیامک به کاربر', // Title of meta box
			[ $this, 'show_custom_meta_box' ], // Callback function
			'shop_order', // Post type
			'side', // Context
			'default' // Priority
		);
	}

	public function show_custom_meta_box( $post ) {
		?>
        <div class="send-sms-to-user-meta-box loading">
            <input type="hidden" name="post_id" value="<?php echo $post->ID; ?>">
            <p>
                <label for="pattern">الگوی ارسال</label>
                <select name="pattern" id="pattern">
                    <option value="trackingcode">ارسال کد رهگیری</option>
                </select>
            </p>
            <p>
                <label for="shipping_company">شرکت حمل و نقل</label>
                <select name="shipping_company" id="shipping_company">
                    <option value="اداره پست">اداره پست</option>
                    <option value="تیپاکس">تیپاکس</option>
                </select>
            </p>

            <p>
                <label for="tracking_code">کد رهگیری</label>
                <input type="text" name="tracking_code" value="" placeholder="Tracking Code">
            </p>

            <button class="button button-primary send-button">ارسال پیامک</button>

            <p class="result"></p>
        </div>
		<?php
	}

	public function handle_sms_delivery() {
		$order = wc_get_order( $_POST[ 'post_id' ] );
		$phone_number = $order->get_billing_phone();

		// Send SMS
		$sms = new KaveNegar();
		$response = $sms->lookup( $phone_number, $_POST[ 'pattern' ], $_POST['post_id'], str_replace( ' ', '‌', $_POST[ 'shipping_company' ] ), $_POST[ 'tracking_code' ] );
		$sms_status = $response[ 'status' ] ? 'SMS sent successfully: ' . $response[ 'message' ] : 'Failed to send SMS: ' . $response[ 'message' ];

		$sms_status .= ' - ' . date( 'Y-m-d H:i:s' ); // Append the current date and time

		update_post_meta(
			$_POST['post_id'],
			'_plaza_tracking_code',
			[
				'code' => $_POST[ 'tracking_code' ],
				'sender' => $_POST[ 'shipping_company' ],
			]
		);

		wp_send_json_success( $sms_status );
	}
}

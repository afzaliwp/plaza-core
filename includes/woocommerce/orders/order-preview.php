<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders;

use AfzaliWP\PlazaDigital\Includes\SMS\KaveNegar;

defined( 'ABSPATH' ) || die();

class Order_Preview {

	private $pattern = [
		'zpal_link' => 'sendzpallink',
		'refund_info' => 'askinforefund'
	];

	private $order_categories = [
		'لپتاپ',
		'موبایل و تبلت',
		'آل این وان مانیتور',
		'کنسول',
		'پرینتر',
		'قطعات',
		'جانبی',
	];

	public function __construct() {
		add_action( 'wp_ajax_plaza_admin_send_zpal_link_sms', [ $this, 'handle_zpal_link_sms_delivery' ] );
		add_action( 'wp_ajax_plaza_admin_send_refund_info_sms', [ $this, 'handle_refund_info_sms_delivery' ] );
		add_action( 'wp_ajax_plaza_admin_set_order_category', [ $this, 'handle_order_category' ] );
		add_action( 'woocommerce_admin_order_preview_start', [ $this, 'order_preview_data' ] );
	}

	public function handle_zpal_link_sms_delivery() {
		$order = wc_get_order( $_POST[ 'post_id' ] );
		$phone_number = $order->get_billing_phone();

		// Send SMS
		$sms = new KaveNegar();
		$response = $sms->lookup( $phone_number, $this->pattern[ 'zpal_link' ], $_POST[ 'post_id' ] );
		$sms_status = $response[ 'status' ] ? 'SMS sent successfully: ' . $response[ 'message' ] : 'Failed to send SMS: ' . $response[ 'message' ];

		$sms_status .= ' - ' . date( 'Y-m-d H:i:s' ); // Append the current date and time

		wp_send_json_success( $sms_status );
	}

	public function handle_refund_info_sms_delivery() {
		$order = wc_get_order( $_POST[ 'post_id' ] );
		$phone_number = $order->get_billing_phone();

		// Send SMS
		$sms = new KaveNegar();
		$response = $sms->lookup( $phone_number, $this->pattern[ 'refund_info' ], $_POST[ 'post_id' ] );
		$sms_status = $response[ 'status' ] ? 'SMS sent successfully: ' . $response[ 'message' ] : 'Failed to send SMS: ' . $response[ 'message' ];

		$sms_status .= ' - ' . date( 'Y-m-d H:i:s' ); // Append the current date and time

		wp_send_json_success( $sms_status );
	}

	public function handle_order_category() {
        $res = update_post_meta( $_POST[ 'post_id' ], 'plaza-order-category', $_POST['category'] );

        if ( is_int( $res ) ) {
			wp_send_json_success( 'با موفقیت ثبت شد.' );
        } else {
			wp_send_json_success( 'خطایی رخ داده است. ثبت نشد.' );
        }

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
        <div class="send-refund-info-order-preview">
            <h4>جهت ارسال پیامک درخواست تکمیل فرم عودت وجه، روی دکمه زیر کلیک کنید.</h4>
            <button
                    data-order-id="<?php echo $post->ID; ?>"
                    class="button button-primary send-refund-info-button">
                ارسال پیامک تکمیل فرم عودت وجه
            </button>

            <div class="results"></div>
        </div>

        <div class="set-order-category-preview">
			<?php $current_category = get_post_meta( $post->ID, 'plaza-order-category', true ); ?>
            <h4>دسته‌بندی این سفارش را انتخاب کنید.</h4>
            <h4>دسته‌بندی فعلی:
				<?php
                echo $current_category ? $current_category : 'انتخاب نشده است';
                ?>
            </h4>
            <form action="" method="post" id="plaza-set-order-categories-form">
                <label for="plaza-order-categories">دسته‌بندی سفارش:</label>
                <select name="plaza-order-categories" id="plaza-order-categories">
					<?php foreach ( $this->order_categories as $category ) {
						?>
                        <option <?php echo selected( $current_category, $category ) ?> value="<?php echo $category; ?>">
                            <?php echo $category; ?>
                        </option>
					<? } ?>
                </select>
                <input type="hidden" id="order-categories-order-id" name="order-categories-order-id"
                       value="<?php echo $post->ID; ?>">
                <button id="plaza-order-categories-submit" type="submit" class="button button-primary primary">
                    ثبت دسته
                </button>
                <div class="results"></div>
            </form>
        </div>
		<?php
	}
}
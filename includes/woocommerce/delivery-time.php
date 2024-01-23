<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

use AfzaliWP\PlazaDigital\Includes\Helper\Jalali_Date;
use DateInterval;
use DateTime;
use DateTimeZone;

defined( 'ABSPATH' ) || die();

class Delivery_Time {
	private $jdate;

	public function __construct() {
		add_action( 'woocommerce_checkout_after_customer_details', [ $this, 'render' ] );
		add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'display_delivery_time_in_admin' ] );
		add_filter( 'woocommerce_admin_order_preview_get_order_details', [
			$this,
			'display_delivery_time_in_preview',
		], 99, 2 );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_delivery_time_to_order_meta' ] );

		$this->jdate = new Jalali_Date();
	}

	public function render() {
		$user_id = get_current_user_id();
		$default_billing_city = get_user_meta( $user_id, 'billing_city', true ); // Get default billing city

		?>
        <div id="cart-delivery-time-plaza" class="plz-card my-4">
            <p class="checkout-fields-title mb-3">زمان ارسال کالا</p>
            <div class="content">
                <div class="only-tehran time-and-day-container <?php echo 'تهران' === $default_billing_city || 'تهرا' === $default_billing_city ? 'show' : '' ?>">
                    <div class="w-100">
                        <label for="plaza-day-select">روز ارسال</label>
                        <select class="form-select w-100" aria-label="انتخاب روز ارسال" name="plaza-day-select"
                                id="plaza-day-select">
							<?php
							$tehran_days = $this->get_tehran_delivery_days();
							foreach ( $tehran_days[ 1 ] as $day ) {
								echo sprintf( '<option value="%1$s">%1$s</option>', $day );
							}
							?>
                        </select>
                    </div>
                    <div class="w-100">
                        <label for="plaza-time-select">ساعت ارسال</label>
                        <select class="form-select w-100" aria-label="انتخاب ساعت ارسال" name="plaza-time-select"
                                id="plaza-time-select">
							<?php
							$tehran_times = $this->get_tehran_delivery_times();
							if ( [] === $tehran_times ) {
								$tehran_times = $tehran_days[ 0 ][ 0 ];
							}
							foreach ( $tehran_times as $key => $time ) {
								echo sprintf( '<option value="%1$s">%2$s</option>', $key, $time );
							}
							?>
                        </select>
                    </div>
                </div>
                <div class="out-of-tehran time-and-day-container <?php echo 'تهران' !== $default_billing_city || 'تهرا' !== $default_billing_city ? 'show' : '' ?>">
                    <div class="w-100">
                        <label for="plaza-day-select">روز ارسال</label>
                        <select class="form-select w-100" aria-label="انتخاب روز ارسال" name="plaza-day-select"
                                id="plaza-day-select">
							<?php
							foreach ( $this->get_out_tehran_delivery_days() as $day ) {
								echo sprintf( '<option value="%1$s">%1$s</option>', $day );
							}
							?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	private function get_tehran_delivery_days() {
		$today = new DateTime();

		// Get the time options for today
		$today_times = $this->get_tehran_delivery_times();

		// If there are no time options left for today, skip to the next day
		if ( empty( $today_times ) ) {
			$today->add( new DateInterval( 'P1D' ) );
			// Reset the time options for the new day
			$today_times = $this->get_tehran_delivery_times( true );
		}

		$today_is_friday = $today->format( 'w' ) == 5;
		if ( $today_is_friday ) {
			$today->add( new DateInterval( 'P1D' ) );
		}

		// Create a new DateTime object for tomorrow
		$tomorrow = clone $today;
		$tomorrow->add( new DateInterval( 'P1D' ) );
		$tomorrow_is_friday = $tomorrow->format( 'w' ) == 5;

		// If tomorrow is Friday (Jomeh), go to next day
		if ( $tomorrow_is_friday ) {
			$tomorrow->add( new DateInterval( 'P1D' ) );
		}

		// Convert to Shamsi date
		$today_shamsi = $this->jdate->jdate( 'j F - l', $today->getTimestamp() );
		$tomorrow_shamsi = $this->jdate->jdate( 'j F - l', $tomorrow->getTimestamp() );

		return [ [ $today_times ], [ $today_shamsi, $tomorrow_shamsi ] ];
	}

	private function get_out_tehran_delivery_days() {
		// Get current time in Tehran
		$tehran_time = new DateTime( 'now', new DateTimeZone( 'Asia/Tehran' ) );
		$hour = $tehran_time->format( 'H' );

		$today = new DateTime();
		$tomorrow = new DateTime();
		$tomorrow->add( new DateInterval( 'P1D' ) );

		$today_is_friday = $today->format( 'w' ) == 5;
		$tomorrow_is_friday = $tomorrow->format( 'w' ) == 5;

		$skip_today = false;
		// If current hour is past 15 or today is Friday (Jomeh), go to next day
		if ( $hour >= 15 || $today_is_friday ) {
			$today->add( new DateInterval( 'P1D' ) );
			// Skip today's delivery option
			$skip_today = true;
		}

		// If tomorrow is Friday (Jomeh), go to next day
		if ( $tomorrow_is_friday ) {
			$tomorrow->add( new DateInterval( 'P1D' ) );
		}

		// Convert to Shamsi date and get day of week
		$today_shamsi = $this->jdate->jdate( 'j F - l', $today->getTimestamp() );
		$tomorrow_shamsi = $this->jdate->jdate( 'j F - l', $tomorrow->getTimestamp() );

		// Prepare delivery options
		$options = [];
		if ( !$skip_today ) {
			$options[] = 'ارسال امروز - ' . $today_shamsi;
		}

		if ( !$tomorrow_is_friday ) {
			$options[] = 'ارسال فردا - ' . $tomorrow_shamsi;
		} else {
			$options[] = 'ارسال ' . $tomorrow_shamsi;
		}

		return $options;
	}

	public function display_delivery_time_in_admin( $order ) {
		$delivery_day = get_post_meta( $order->get_id(), 'delivery_day', true );
		$delivery_time = get_post_meta( $order->get_id(), 'delivery_time', true );

		if ( $delivery_day ) {
			echo '<p><strong>روز ارسال:</strong> ' . $delivery_day . '</p>';
		}

		if ( $delivery_time ) {
			echo '<p><strong>ساعت ارسال:</strong> ' . $delivery_time . '</p>';
		}
	}

	public function display_delivery_time_in_preview( $data, $order ) {
		$delivery_day = get_post_meta( $order->get_id(), 'delivery_day', true );
		$delivery_time = get_post_meta( $order->get_id(), 'delivery_time', true );

		if ( $delivery_day ) {
			$data[ 'formatted_billing_address' ] .= '<br><p><strong>روز ارسال:</strong> ' . $delivery_day . '</p>';
		}

		if ( $delivery_time ) {
			$data[ 'formatted_billing_address' ] .= '<br><p><strong>ساعت ارسال:</strong> ' . $delivery_time . '</p>';
		}

		return $data;
	}

	public function save_delivery_time_to_order_meta( $order_id ) {
		if ( !empty( $_POST[ 'plaza-day-select' ] ) ) {
			update_post_meta( $order_id, 'delivery_day', sanitize_text_field( $_POST[ 'plaza-day-select' ] ) );
		}

		if ( !empty( $_POST[ 'plaza-time-select' ] ) ) {
			update_post_meta( $order_id, 'delivery_time', sanitize_text_field( $_POST[ 'plaza-time-select' ] ) );
		}
	}

	private function get_tehran_delivery_times( $reset = false ) {
		// If reset is true, return all time options
		if ( $reset ) {
			return [
				'11-15' => '11 الی 15',
				'15-19' => '15 الی 19',
			];
		}

		// Get current time in Tehran
		$tehran_time = new DateTime( 'now', new DateTimeZone( 'Asia/Tehran' ) );
		$hour = (int) $tehran_time->format( 'H' );
		$minute = (int) $tehran_time->format( 'i' );

		$times = [];

//		if ( $hour < 13 || ( $hour === 13 && $minute === 0 ) ) {
//			$times[ '11-15' ] = '11 الی 15';
//		}

		if ( $hour < 16 || ( $hour === 16 && $minute === 0 ) ) {
			$times[ '15-19' ] = '15 الی 19';
		}

		return $times;
	}

}
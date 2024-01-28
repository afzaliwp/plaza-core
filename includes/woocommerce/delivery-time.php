<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

use AfzaliWP\PlazaDigital\Includes\Helper\Jalali_Date;
use DateInterval;
use DateTime;
use DateTimeZone;

defined( 'ABSPATH' ) || die();

class Delivery_Time {
	private $jdate;

	private $exclude_delivery_days = [
		'بهمن' => [ '۱۹', '۲۲' ],
		'اسفند' => [ '۶', '۲۹' ],
	];

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

	private function get_out_tehran_delivery_days() {
		$tehran_time = new DateTime( 'now', new DateTimeZone( 'Asia/Tehran' ) );
		$hour = $tehran_time->format( 'H' );

		$today = new DateTime();
		$tomorrow = new DateTime();
		$tomorrow->add( new DateInterval( 'P1D' ) );

		$skip_today = $this->should_skip_day( $today, $hour );
		$skip_tomorrow = $this->should_skip_day( $tomorrow );

		$options = [];
		if ( !$skip_today ) {
			$options[] = 'ارسال امروز - ' . $this->jdate->jdate( 'j F - l', $today->getTimestamp() );
		}

		if ( !$skip_tomorrow ) {
			$options[] = 'ارسال فردا - ' . $this->jdate->jdate( 'j F - l', $tomorrow->getTimestamp() );
		}

		return $options;
	}


	private function get_tehran_delivery_days() {
		$today = new DateTime();
		$tomorrow = clone $today;
		$tomorrow->add( new DateInterval( 'P1D' ) );

		// Get the time options for today
		$today_times = $this->get_tehran_delivery_times();

		// If there are no time options left for today, skip to the next day
		if ( empty( $today_times ) ) {
			$today = $this->get_next_valid_day( $today );
			// Reset the time options for the new day
			$today_times = $this->get_tehran_delivery_times( true );
		}

		$today_shamsi = $this->jdate->jdate( 'j F - l', $today->getTimestamp() );
		$tomorrow_shamsi = $this->jdate->jdate( 'j F - l', $tomorrow->getTimestamp() );

        if ( $tomorrow_shamsi === $today_shamsi ) {
            $tomorrow = $this->get_next_valid_day( $tomorrow );
			$tomorrow_shamsi = $this->jdate->jdate( 'j F - l', $tomorrow->getTimestamp() );
		}
		return [ [ $today_times ], [ $today_shamsi, $tomorrow_shamsi ] ];
	}

	private function get_next_valid_day( $date ) {
		do {
			$date->add( new DateInterval( 'P1D' ) );
		} while ( $this->should_skip_day_tehran( $date ) );

		return $date;
	}

	private function should_skip_day_tehran( $date ) {
		$is_friday = $date->format( 'w' ) == 5;
		$is_excluded = $this->is_excluded( $this->jdate->jdate( 'j F - l', $date->getTimestamp() ) );

		return $is_friday || $is_excluded;
	}

	private function should_skip_day( $date, $hour = null ) {
		$is_friday = $date->format( 'w' ) == 5;
		$is_past_cutoff = isset( $hour ) && $hour >= 15;
		$is_excluded = $this->is_excluded( $this->jdate->jdate( 'j F - l', $date->getTimestamp() ) );

		while ( $is_excluded || $is_friday || $is_past_cutoff ) {
			$date->add( new DateInterval( 'P1D' ) );
			$is_friday = $date->format( 'w' ) == 5;
			$is_excluded = $this->is_excluded( $this->jdate->jdate( 'j F - l', $date->getTimestamp() ) );
			$is_past_cutoff = false;  // We only check the cutoff time for "today"
		}

		return $is_excluded || $is_friday || $is_past_cutoff;
	}

	private function is_excluded( $shamsi_day ) {
		// Split the $today string into day and month
		[ $day, $month ] = explode( ' ', $shamsi_day );

		// Check if the month is in the $exclude_delivery_days array
		if ( array_key_exists( $month, $this->exclude_delivery_days ) ) {
			// If it is, check if the day is in the array of excluded days for that month
			if ( in_array( $day, $this->exclude_delivery_days[ $month ] ) ) {
				return true;
			}
		}

		return false;
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
				'12-17' => '۱۲ الی ۱۷',
				'17-20' => '۱۷ الی ۲۰',
			];
		}

		// Get current time in Tehran
		$tehran_time = new DateTime( 'now', new DateTimeZone( 'Asia/Tehran' ) );
		$hour = (int) $tehran_time->format( 'H' );
		$minute = (int) $tehran_time->format( 'i' );

		$times = [];

		if ( $hour < 11 || ( $hour === 11 && $minute === 0 ) ) {
			$times[ '12-17' ] = '۱۲ الی ۱۷';
		}

		if ( $hour < 15 || ( $hour === 15 && $minute === 0 ) ) {
			$times[ '17-20' ] = '۱۷ الی ۲۰';
		}

		return $times;
	}

}
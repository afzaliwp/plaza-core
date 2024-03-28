<?php

namespace AfzaliWP\PlazaDigital\Includes\Third_Parties;

use AfzaliWP\Plaza_Theme\Includes\Helper;

defined( 'ABSPATH' ) || die();

class Gravity_Forms {

	public $modifiable_form_ids = [];

	public function __construct() {
		add_action( 'admin_notices', [ $this, 'order_edit_page_notice' ] );
		add_action( 'woocommerce_admin_order_preview_start', [ $this, 'order_preview_data' ] );

		$this->modifiable_form_ids = $this->get_forms();
		foreach ( $this->modifiable_form_ids as $form_id ) {
			add_action( 'gform_after_submission_' . $form_id, [ $this, 'gform_after_submission' ], 10, 2 );
		}
	}

	public function gform_after_submission( $entry, $form ) {
		$entries = $this->get_entries( $entry, $form );

		$this->handle_new_receiver_req( $entries );
		$this->handle_refund_req( $entries );
	}

	private function get_entries( $entry, $form ) {
		$new_entry = [];

		foreach ( $form[ 'fields' ] as $field ) {
			$input_id = (string) $field->id;
			$input_name = $field->inputName;
			if ( isset( $entry[ $input_id ] ) ) {
				$new_entry[ $input_name ] = $entry[ $input_id ];
			}
		}

		return $new_entry;
	}

	public function order_edit_page_notice() {
		global $pagenow;
		global $post;

		if ( !$post ) {
			return;
		}

		$this->new_receiver_order_edit_page_notice( $post, $pagenow );
		$this->refund_req_order_edit_page_notice( $post, $pagenow );
	}

	public function order_preview_data() {
		global $post;

		$this->new_receiver_order_preview_data( $post );
		$this->refund_req_order_preview_data( $post );
	}

	private function handle_new_receiver_req( $entries ) {
		if ( !isset( $entries[ 'new-receive-order-id' ] ) ) {
			return;
		}

		$_POST[ 'new-receive-order-id' ] = $entries[ 'new-receive-order-id' ];
		update_post_meta( $entries[ 'new-receive-order-id' ], 'plaza-send-order-to-other-person', $entries );
	}

	private function handle_refund_req( $entries ) {
		if ( !isset( $entries[ 'refund-order-id' ] ) ) {
			return;
		}
		$_POST[ 'refund-order-id' ] = $entries[ 'refund-order-id' ];
		update_post_meta( $entries[ 'refund-order-id' ], 'plaza-refund-request', $entries );
	}

	private function new_receiver_order_edit_page_notice( $post, $pagenow ) {
		$meta = get_post_meta( $post->ID, 'plaza-send-order-to-other-person', true );
		if ( $pagenow == 'post.php' && $post->post_type == 'shop_order' && $meta ) {
			?>
            <div class="notice notice-warning is-dismissible">
                <p>این سفارش به آدرس یا فرد دیگری ارسال شود.</p>
                <p>نام: </p>
                <p><?php echo $meta[ 'new-receive-name' ]; ?></p>
                <p>کدملی: </p>
                <p><?php echo $meta[ 'new-receive-nat-code' ]; ?></p>
                <p>آدرس: </p>
                <p><?php echo $meta[ 'new-receive-address' ]; ?></p>
                <p>شماره: </p>
                <p><?php echo $meta[ 'new-receive-phone' ]; ?></p>
                <p>کد پستی: </p>
                <p><?php echo $meta[ 'new-receive-post-code' ]; ?></p>
                <p>دلیل: </p>
                <p><?php echo $meta[ 'new-receive-reason' ]; ?></p>
            </div>
			<?php
		}
	}

	private function refund_req_order_edit_page_notice( $post, $pagenow ) {
		$meta = get_post_meta( $post->ID, 'plaza-refund-request', true );
		if ( $pagenow == 'post.php' && $post->post_type == 'shop_order' && $meta ) {
			?>
            <div class="notice notice-warning is-dismissible">
                <p>درخواست عودت وجه برای این سفارش</p>
                <p>نام: </p>
                <p><?php echo $meta[ 'refund-full-name' ]; ?></p>
                <p>بانک: </p>
                <p><?php echo $meta[ 'refund-bank' ]; ?></p>
                <p>شماره کارت: </p>
                <p><?php echo $meta[ 'refund-card-number' ]; ?></p>
                <p>شماره شبا: </p>
                <p><?php echo $meta[ 'refund-sheba-number' ]; ?></p>
            </div>
			<?php
		}
	}

	private function new_receiver_order_preview_data( $post ) {
		$meta = get_post_meta( $post->ID, 'plaza-send-order-to-other-person', true );
		if ( $meta ) {
			?>
            <div
                    style="display: flex; flex-wrap: wrap; gap: 10px;"
                    class="notice notice-warning is-dismissible quick-view-new-receive-order">
                <p style="width: 100%">این سفارش به آدرس یا فرد دیگری ارسال شود.</p>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">نام: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'name' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">کدملی: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'nat-code' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">آدرس: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'address' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">شماره: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'phone' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">کد پستی: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'post-code' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">دلیل: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'reason' ]; ?></p>
                </div>
            </div>
			<?php
		}
	}

	private function refund_req_order_preview_data( $post ) {
		$meta = get_post_meta( $post->ID, 'plaza-refund-request', true );
		if ( $meta ) {
			?>
            <div
                    style="display: flex; flex-wrap: wrap; gap: 10px;"
                    class="notice notice-warning is-dismissible quick-view-new-receive-order">
                <p style="width: 100%">درخواست عودت وجه برای این سفارش</p>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">نام: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'full-name' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">بانک: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'bank' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">شماره کارت: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'card-number' ]; ?></p>
                </div>
                <div style="width: 40%">
                    <p style="margin: 0; padding: 0;">شماره شبا: </p>
                    <p style="margin: 0; padding: 0;"><?php echo $meta[ 'sheba-number' ]; ?></p>
                </div>
            </div>
			<?php
		}
	}

	private function get_forms() {
		return [
			Helper::get_field( 'plaza-send-order-to-other-person-form-id', 'option' ),
			Helper::get_field( 'plaza-refund-order-req-form-id', 'option' ),
		];
	}

	public static function get_forms_ids() {
		return [
			'send-order-to-other' => Helper::get_field( 'plaza-send-order-to-other-person-form-id', 'option' ),
			'refund-order-req' => Helper::get_field( 'plaza-refund-order-req-form-id', 'option' ),
		];
	}
}
<?php

namespace AfzaliWP\PlazaDigital\Includes\WooCommerce;

defined( 'ABSPATH' ) || die();

class Tracking_Code {

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'order_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_tracking_code' ] );
	}

	public function order_meta_box() {
		add_meta_box(
			'plaza-tracking-code',
			'کد رهگیری مرسوله',
			[ $this, 'order_meta_box_callback' ],
			'shop_order',
			'side'
		);
	}

	function order_meta_box_callback( $post ) {
		$field_value = get_post_meta( $post->ID, '_plaza_tracking_code', true );

		echo '<input type="text" id="plaza_tracking_code" name="plaza_tracking_code" value="' . esc_attr( $field_value ) . '" />';
	}

	public function save_tracking_code( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST[ 'plaza_tracking_code' ] ) ) {
			return;
		}

		$tracking_code = sanitize_text_field( $_POST[ 'plaza_tracking_code' ] );

		update_post_meta( $post_id, '_plaza_tracking_code', $tracking_code );
	}
}
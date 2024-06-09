<?php

namespace AfzaliWP\PlazaDigital\Includes\SMS;

defined( 'ABSPATH' ) || die();

/**
 * Implementation of MeliPayamak API for sms.
 */
class MeliPayamak {

	private $url;

	public function __construct() {
		$this->url = 'https://console.melipayamak.com/api/send/shared/d710f3562b354ed38ebb7d5999065f35';
	}

	public function send_sms( $phone, $body_id, $args = [] ) {
		$data        = [
			'bodyId' => $body_id,
			'to'     => $phone,
			'args'   => $args
		];
		$data_string = json_encode( $data );

		$request = wp_remote_post( $this->url, [
			'method'    => 'POST',
			'headers'   => [ 'Content-Type' => 'application/json' ],
			'body'      => $data_string,
			'sslverify' => false  // This makes the request insecure, similar to your original code
		] );

		if ( is_wp_error( $request ) ) {
			$error_message = $request->get_error_message();

			return [ 'status' => false, 'message' => $error_message ];
		}

		$request = json_decode( wp_remote_retrieve_body( $request ) );

		return [
			'status'  => $request->return->status == 200,
			'message' => 'پنل پیامک: ' . $request->return->message
		];
	}
}
<?php

namespace AfzaliWP\PlazaDigital\Includes\SMS;

defined( 'ABSPATH' ) || die();

/**
 * Implementation of KaveNegar API for sms.
 */
class KaveNegar {

	private $apikey;

	public function __construct() {
		$this->apikey = '4E53422B7830515A59495777796A7045314A537A306F795A7475494F6D336C6E6358464E41686A344E31513D';
	}

	public function lookup($phone, $template, $token, $token2 = '', $token3 = '', $type = 'sms'){
		$request = wp_remote_get("https://api.kavenegar.com/v1/{$this->apikey}/verify/lookup.json?receptor={$phone}&template={$template}&token={$token}&token2={$token2}&token3={$token3}&type={$type}",[
			'timeout'	=> 30,
		]);
		if( is_wp_error( $request ) ) {
			return ['status' => false, 'message' => 'پنل پیامک: خطای ناشناخته رخ داده است'];
		} else {
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			return [
				'status'	=> ( $request->return->status == 200 ? true : false),
				'message'	=> 'پنل پیامک: '.$request->return->message
			];
		}
	}
}
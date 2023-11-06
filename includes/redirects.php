<?php

namespace AfzaliWP\PlazaDigital\Includes;

defined( 'ABSPATH' ) || die();

class Redirects {
	public function __construct() {
		// when not logged in, go back to cart instead of checkout
		add_action( 'template_redirect', [ $this, 'redirect_to_cart_if_not_logged_in' ] );
	}

	public function redirect_to_cart_if_not_logged_in() {
		if ( is_checkout() && ! is_user_logged_in() ) {
			wp_redirect( wc_get_cart_url() );
			exit;
		}
	}
}
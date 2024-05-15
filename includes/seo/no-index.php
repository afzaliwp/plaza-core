<?php

namespace AfzaliWP\PlazaDigital\Includes\SEO;

defined( 'ABSPATH' ) || die();

class No_Index {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'add_no_index_meta_to_header' ] );
	}

	public function add_no_index_meta_to_header() {
		if ( str_contains( $_SERVER[ 'HTTP_HOST' ], 'plazashop' ) ) {
			echo '<meta name="robots" content="noindex,follow">';
		}
	}

}
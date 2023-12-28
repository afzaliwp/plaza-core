<?php

namespace AfzaliWP\PlazaDigital\Includes\SEO;

defined( 'ABSPATH' ) || die();

class Canonical {
	public function __construct() {
		add_filter( 'rank_math/frontend/canonical', [ $this, 'set_canonical_url_paged_urls' ], 1, 1 );
	}

	public function set_canonical_url_paged_urls( $canonical ) {
		if ( is_paged() ) {
			return preg_replace( '#/page/[0-9]+/?$#', '/', $canonical );
		}

		return $canonical;
	}
}
<?php

namespace AfzaliWP\PlazaDigital\Includes\SEO;

defined( 'ABSPATH' ) || die();
class Canonical {
	public function __construct() {
		add_action('wp_head', [ 'set_canonical_url_paged_urls' ]);
	}

	public function set_canonical_url_paged_urls() {
		if (is_paged()) {
			$url = get_pagenum_link(1);
			echo '<link rel="canonical" href="' . esc_url($url) . '" />';
		}
	}
}
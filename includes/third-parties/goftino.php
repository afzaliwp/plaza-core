<?php

namespace AfzaliWP\PlazaDigital\Includes\Third_Parties;

defined( 'ABSPATH' ) || die();

class Goftino {
	public function __construct() {
		add_action( 'wp_footer', [ $this, 'add_goftino_script_to_footer' ] );
	}

	public function add_goftino_script_to_footer() {
		?>
		<!---start GOFTINO code--->
		<script type="text/javascript">
			jQuery(document).ready(function () {
				!function () {
					var i = 'FP7pjk', a = window, d = document;

					function g() {
						var g = d.createElement('script'), s = 'https://www.goftino.com/widget/' + i,
							l = localStorage.getItem('goftino_' + i);
						g.async = !0, g.src = l ? s + '?o=' + l : s;
						d.getElementsByTagName('head')[0].appendChild(g);
					}

					'complete' === d.readyState ? g() : a.attachEvent ? a.attachEvent('onload', g) : a.addEventListener('load', g, !1);
				}();
			});
		</script>
		<!---end GOFTINO code--->
		<?php
	}
}
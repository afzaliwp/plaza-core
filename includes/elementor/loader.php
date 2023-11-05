<?php
namespace AfzaliWP\PlazaDigital\Includes\Elementor;

use AfzaliWP\PlazaDigital\Includes\Elementor\Widgets\Slides;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Loader {
	/**
	 * Constructor
	 */
	public function __construct() {
		// Hook into Elementor widgets registered action
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}

	/**
	 * Include Widgets files
	 */
	private function include_widgets_files() {
		 require_once( __DIR__ . '/widgets/slides.php' );
	}

	/**
	 * Register Widgets
	 */
	public function register_widgets() {
		// Include Widget files
		$this->include_widgets_files();

		// Register widgets
		 Plugin::instance()->widgets_manager->register( new Slides() );
	}
}

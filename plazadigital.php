<?php
/**
 * Plugin Name:       PlazaDigital Core
 * Plugin URI:        https://afzaliwp.com
 * Description:       Adds more functionality to the plaza digital website.
 * Version:           2.2.3
 * Author:            Mohammad Afzali
 * Author URI:        https://afzaliwp.com
 */

namespace AfzaliWP;

use AfzaliWP\PlazaDigital\Includes\Redirects;
use AfzaliWP\PlazaDigital\Includes\SEO\Canonical;
use AfzaliWP\PlazaDigital\Includes\SEO\No_Index;
use AfzaliWP\PlazaDigital\Includes\SEO\Schema;
use AfzaliWP\PlazaDigital\Includes\Third_Parties\Gravity_Forms;
use AfzaliWP\PlazaDigital\Includes\WooCommerce\Checkout;
use AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders\Admin_Edit;
use AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders\Order_List;
use AfzaliWP\PlazaDigital\Includes\WooCommerce\Orders\Order_Preview;
use AfzaliWP\PlazaDigital\Includes\WooCommerce\Shipping_Price;
use AfzaliWP\PlazaDigital\Includes\WooCommerce\Tracking_Code;
use Exception;

defined( 'ABSPATH' ) || die();

require 'functions.php';

final class PlazaDigital {

	private static $instances = [];

	protected function __construct() {
		spl_autoload_register( 'afzaliwp_plazadigital_autoload' );

		$this->define_constants();

		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles_and_scripts' ], 90 );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_styles_and_scripts' ], 90 );

		add_action( 'init', function () {
			$this->init_hook();
			$this->woocommerce_related();
		} );
		$this->other_functionalities();
	}

	protected function __clone() {}

	/**
	 * @throws Exception
	 */
	public function __wakeup() {
		throw new Exception( "Cannot unserialize a singleton." );
	}

	public static function get_instance() {
		$cls = PlazaDigital::class;

		if ( !isset( self::$instances[ $cls ] ) ) {
			self::$instances[ $cls ] = new PlazaDigital();
		}

		return self::$instances[ $cls ];
	}

	public function register_styles_and_scripts() {
		wp_enqueue_style(
			'afzaliwp-pd-style',
			AFZALIWP_PD_ASSETS_URL . 'frontend.min.css',
			'',
			AFZALIWP_PD_ASSETS_VERSION
		);

		wp_enqueue_script(
			'afzaliwp-pd-script',
			AFZALIWP_PD_ASSETS_URL . 'frontend.min.js',
			[ 'jquery' ],
			AFZALIWP_PD_ASSETS_VERSION,
			true
		);

		wp_localize_script(
			'afzaliwp-pd-script',
			'PlazaObj',
			[
				'homeUrl' => get_bloginfo( 'url' ),
				// 'checkoutUrl' => wc_get_checkout_url(), //If WooCommerce is included in your works and need to use checkout url in ajax.
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'afzaliwp-pd-nonce' ),
			]
		);
	}

	public function register_admin_styles_and_scripts() {
		wp_enqueue_style(
			'afzaliwp-pd-admin-style',
			AFZALIWP_PD_ASSETS_URL . 'admin.min.css',
			'',
			AFZALIWP_PD_ASSETS_VERSION
		);

		wp_enqueue_script(
			'afzaliwp-pd-admin-script',
			AFZALIWP_PD_ASSETS_URL . 'admin.min.js',
			[ 'jquery' ],
			AFZALIWP_PD_ASSETS_VERSION,
			true
		);

		wp_localize_script(
			'afzaliwp-pd-admin-script',
			'PlazaObj',
			[
				'homeUrl' => get_bloginfo( 'url' ),
				// 'checkoutUrl' => wc_get_checkout_url(), //If WooCommerce is included in your works and need to use checkout url in ajax.
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'afzaliwp-pd-nonce' ),
			]
		);
	}

	public function woocommerce_related() {
		new Tracking_Code();
		new Order_Preview();
		new Order_List();
		new Admin_Edit();
	}

	public function define_constants() {
		define( 'AFZALIWP_PD_DEVELOPMENT', true );
		define( 'AFZALIWP_PD_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'AFZALIWP_PD_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'AFZALIWP_PD_TPL_DIR', trailingslashit( AFZALIWP_PD_DIR . 'templates' ) );
		define( 'AFZALIWP_PD_WC_TPL_DIR', trailingslashit( AFZALIWP_PD_DIR . 'woocommerce' ) );
		define( 'AFZALIWP_PD_INC_DIR', trailingslashit( AFZALIWP_PD_DIR . 'includes' ) );
		define( 'AFZALIWP_PD_ASSETS_URL', trailingslashit( AFZALIWP_PD_URL . 'assets/dist' ) );
		define( 'AFZALIWP_PD_IMAGES', trailingslashit( AFZALIWP_PD_URL . 'assets/images' ) );
		define( 'AFZALIWP_PD_JSON', trailingslashit( AFZALIWP_PD_ASSETS_URL . 'json' ) );

		if ( str_contains( get_bloginfo( 'wpurl' ), 'localhost' ) ) {
			define( 'AFZALIWP_PD_IS_LOCAL', true );
			define( 'AFZALIWP_PD_ASSETS_VERSION', time() );
		} else {
			define( 'AFZALIWP_PD_IS_LOCAL', false );
			define( 'AFZALIWP_PD_ASSETS_VERSION', '2.2.3' );
		}
	}

	public function init_hook() {
		new Redirects();
		new Shipping_Price();
	}

	public function other_functionalities() {
		add_action( 'after_setup_theme', function () {
			new Gravity_Forms();
		} );

		new Checkout();
		new Schema();
		new Canonical();
		new No_Index();
	}
}

PlazaDigital::get_instance();

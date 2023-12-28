<?php

namespace AfzaliWP\PlazaDigital\Includes\Helper;

defined( 'ABSPATH' ) || die();

class Price_Helper {

	private $variation_slugs = [
		'18-ماه-سریر،سازگار،پلازا-دیجیتال',
		'18-ماه-آوات_امرتات_سریر_ایران-نوتبوک_پلا',
		'18-ماهه-پلازا-سرویس-آوات-امرتات-سازگار-س',
		'18-ماهه-پلازا-سرویس',
		'18ماه-پلازا-سرویس',
	];

	private $variation_24_month_guaranti = '24-month-plaza-guaranty';

	public function __constructor() {
	}

	public function handle_sheet_price_update( $product_id, $price, $sale_price, $qty = 0, $product_obj = null ) {
		if ( !is_object( $product_obj ) ) {
			$product_obj = wc_get_product( $product_id );
		}

		if ( $product_obj->is_type( 'variable' ) ) {
			// Get the variations
			$variations = $product_obj->get_available_variations();
			foreach ( $variations as $variation ) {
				// Check if the variation slug is in the list
				$decoded_attribute = urldecode( urldecode( $variation[ 'attributes' ][ 'attribute_pa_garanti' ] ) );

				if ( $decoded_attribute === $this->variation_24_month_guaranti ) {
					$variation_obj = new \WC_Product_Variation( $variation[ 'variation_id' ] );

					if ( 0 === intval( $price ) || 0 === intval( $qty ) ) {
						$this->force_product_out_of_stock( $variation_obj );
						$this->force_product_out_of_stock( $product_obj );
						return;
					} else {
						$variation_obj->set_price( intval( $price ) * 1.01 );
						if ( intval( $sale_price ) ) {
							$variation_obj->set_sale_price( intval( $sale_price ) * 1.01 );
						} else {
							$variation_obj->set_sale_price( '' );
						}

						$variation_obj->set_stock_quantity( $qty );

						$variation_obj->save();
					}
				}
			}
		}
	}

	private function force_product_out_of_stock( $product ) {
		$product->set_price( '' );
		$product->set_regular_price( '' );
		$product->set_sale_price( '' );
		$product->set_date_on_sale_from();
		$product->set_date_on_sale_to();
		update_post_meta( $product->get_id(), '_partner_price', '' );
//		$this->set_product_stock_in_redis( $product->get_id(), 0 );
		update_post_meta( $product->get_id(), '_stock', '' );
		update_post_meta( $product->get_id(), '_stock_status', 'outofstock' );
		$product->save();
	}
}
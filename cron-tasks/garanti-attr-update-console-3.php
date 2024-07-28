<?php
// Include WordPress
require( 'wp-load.php' );

// Define the variation slugs to search for
$variation_slugs = [
	'گارانتی-18-ماهه-آواژنگ،مادیران،شیلدسر',
	'گارانتی-18-ماهه-شرکتی',
	'گارانتی-18-ماهه-معتبرشیلد-سرویس،-صدرا،',
	'گارانتی-18-ماه-اصلی',
	'گارانتی-18-ماهه-معتبرشیلد-سرویس،-صدرا',
];

// Open a log file
$log_file = fopen( 'log.txt', 'w' );

$args = array(
    'limit' => -1,
	'category' => ['sony-playstation', 'microsoft-consule']
);

$products = wc_get_products($args);

//$products = wc_get_products( [
//	'status' => 'publish',
//	'limit' => 1,
//	'include' => [99424]
//] );

foreach ( $products as $product ) {
    var_dump('$product_id: ' . $product->get_id());
    echo PHP_EOL;
	if ( $product->is_type( 'variable' ) ) {
		// Get the variations
		$variations = $product->get_available_variations();
        $price = 0;
		foreach ( $variations as $variation ) {
			if( '12-month-plaza-service-console-guaranty' !== $variation['attributes']['attribute_pa_garanti']) {
                $price = $variation['display_price'];
                $quantity = get_post_meta($variation['variation_id'], '_stock', true);
            }						
		}

        foreach ( $variations as $variation ) {
			if( '12-month-plaza-service-console-guaranty' === $variation['attributes']['attribute_pa_garanti']) {
                $variation_obj = new \WC_Product_Variation( $variation[ 'variation_id' ] );

                if(0 === intval($price)) {
                    $variation_obj->set_price('');
                    $variation_obj->set_stock(0);
                    $variation_obj->set_stock_status('outofstock');
                    // update_post_meta($variation[ 'variation_id' ], '_price', '');
                    // update_post_meta($variation[ 'variation_id' ], '_sale_price', '');
                    // update_post_meta($variation[ 'variation_id' ], '_stock', 0);
                    $variation_obj->save();

                    $product->set_stock(0);
                    $product->set_stock_status('outofstock');
                    $product->save();
                } else {
                    update_post_meta($variation[ 'variation_id' ], '_price', intval( $price ) + 1000000 );
                    update_post_meta($variation[ 'variation_id' ], '_regular_price', intval( $price ) + 1000000 );
                    update_post_meta($variation[ 'variation_id' ], '_sale_price', '');
                    $variation_obj->set_price( intval( $price ) + 1000000 );
                    update_post_meta($variation[ 'variation_id' ], '_stock', $quantity);
                    $variation_obj->save();
                }
                // update_post_meta($variation[ 'variation_id' ], '_price', intval( $price ) * 1.01);
                // update_post_meta($variation[ 'variation_id' ], '_sale_price', '');
            }						
		}
	}
}

// Close the log file
fclose( $log_file );

?>

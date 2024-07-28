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

// Loop through each product
foreach ( $products as $product ) {
	if ( $product->is_type( 'variable' ) ) {
		// Get the variations
		$variations = $product->get_available_variations();

		foreach ( $variations as $variation ) {
			// Check if the variation slug is in the list
			$decoded_attribute = urldecode( urldecode( $variation[ 'attributes' ][ 'attribute_pa_garanti' ] ) );

			var_dump($decoded_attribute);

				try {
					// Create a new variation
					$new_variation = new WC_Product_Variation();
					$new_variation->set_parent_id( $product->get_id() );

					// Get the existing attributes and modify the 'pa_garanti' attribute
					$attributes = $variation[ 'attributes' ];
					$attributes[ 'attribute_pa_garanti' ] = '12-month-plaza-service-console-guaranty';

					// Set the modified attributes
					$new_variation->set_attributes( $attributes );

					// Set the price 1% higher
					$new_variation->set_regular_price( $variation[ 'display_price' ] + 1000000 );
					$new_variation->set_sale_price( $variation[ 'display_price' ] + 1000000 );

					if ( $variation[ 'display_regular_price' ] ) {
						$new_variation->set_regular_price( $variation[ 'display_regular_price' ] + 1000000 );
					}

					// Set the stock quantity if manage_stock is enabled
					if ( $variation[ 'is_in_stock' ] ) {
						$new_variation->set_stock_status();
						$new_variation->set_manage_stock( true );
						$new_variation->set_stock_quantity( get_post_meta( $variation['variation_id'], '_stock', true ) );
					}

					$new_variation->save();

					// Log success
					fwrite( $log_file, "Successfully created new variation for product {$product->get_id()}\n" );
				} catch ( Exception $e ) {
					// Log error
					fwrite( $log_file, "Error creating new variation for product {$product->get_id()}: {$e->getMessage()}\n" );
				}
			
		}
	}
}

// Close the log file
fclose( $log_file );

?>

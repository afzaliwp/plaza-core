<?php
// Include WordPress
require( 'wp-load.php' );

// Define the variation slugs to search for
$variation_slugs = [
	'18-ماه-سریر،سازگار،پلازا-دیجیتال',
	'18-ماه-آوات_امرتات_سریر_ایران-نوتبوک_پلا',
	'18-ماهه-پلازا-سرویس-آوات-امرتات-سازگار-س',
	'18-ماهه-پلازا-سرویس',
	'18ماه-پلازا-سرویس',
];

// Get all published products
$products = wc_get_products( [ 
    'status' => 'publish', 
    'limit' => -1,
    'category' => ['laptop']
] );


// Loop through each product
foreach ( $products as $product ) {
	if ( $product->is_type( 'variable' ) ) {
		// Get the variations
		$variations = $product->get_available_variations();
		foreach ( $variations as $variation ) {
			// Check if the variation slug is in the list
			$decoded_attribute = urldecode( urldecode( $variation[ 'attributes' ][ 'attribute_pa_garanti' ] ) );
			if ( in_array( $decoded_attribute, $variation_slugs ) ) {
				// Add the new garanti to the product attributes
				echo $product->get_id() . PHP_EOL;
				wp_set_object_terms($product->get_id(), '24-month-plaza-guaranty', 'pa_garanti', true);
			}
		}
	}
}


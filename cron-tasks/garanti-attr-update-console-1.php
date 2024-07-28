<?php
// Include WordPress
require( 'wp-load.php' );

// Get all published products
$products = wc_get_products( [
	'status' => 'publish',
	'limit' => -1,
	'category' => ['sony-playstation', 'microsoft-consule']
] );
//$products = wc_get_products( [
//	'status' => 'publish',
//	'limit' => 1,
//	'include' => [99424]
//] );

// Loop through each product
foreach ( $products as $product ) {
	if ( $product->is_type( 'variable' ) ) {
		wp_set_object_terms($product->get_id(), '12-month-plaza-service-console-guaranty', 'pa_garanti', true);
	}
}


<?php
// Include WordPress
require( 'wp-load.php' );

// Get all published products
$products = wc_get_products( [
	'status' => 'publish',
	'limit' => -1,
	'category' => ['apple-watch']
] );
//$products = wc_get_products( [
//	'status' => 'publish',
//	'limit' => 1,
//	'include' => [99424]
//] );

// Loop through each product
foreach ( $products as $product ) {
	if ( $product->is_type( 'variable' ) ) {
		wp_set_object_terms($product->get_id(), 'apple-watch-enable-extra-features', 'pa_garanti', true);
	}
}


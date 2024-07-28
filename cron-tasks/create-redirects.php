<?php
// Load WordPress
require_once( 'wp-load.php' );

// Path to CSV file
$csvFile = 'laptops - vaio-all_before.csv';

function convert_url($url) {
	$parsed_url = parse_url($url);
	$path = $parsed_url['path'];
	return ltrim($path, '/');
}


function remove_trailing_slash($string) {
	return rtrim($string, '/');
}


// Open the CSV file
if ( ( $handle = fopen( $csvFile, "r" ) ) !== false ) {
	// Skip the first line (header)
	fgetcsv( $handle );
	wp_mail( 'afzaliwp@gmail.com', 'start update ' . $csvFile, '' );

	// Loop through the CSV rows
	while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== false ) {
		$product_id   = $data[ 0 ];
		$old_link     = $data[ 1 ];
//		$english_name = $data[ 2 ];
//		$rank_title   = $data[ 3 ];
//		$rank_desc    = $data[ 4 ];

		// Get the old permalink
		$current_link = get_permalink( $product_id );

		var_dump( remove_trailing_slash( convert_url( $old_link ) ) );
		var_dump( str_replace( 'plaza.local', 'plazadigital.ir', trailingslashit($current_link) ));
		continue;
		// If the permalink has changed, add a 301 redirect
		if ( $old_link !== $current_link ) {
			global $wpdb;
			$sources = serialize( [
				[
					'ignore'     => '',
					'pattern'    => remove_trailing_slash( convert_url( $old_link ) ),
					'comparison' => 'exact',
				]
			] );
			$wpdb->insert( $wpdb->prefix . 'rank_math_redirections', [
				'sources'     => $sources,
				'url_to'      => trailingslashit($current_link),
				'header_code' => 301,
			] );
		}
	}

	// Close the CSV file
	fclose( $handle );
}

wp_mail( 'afzaliwp@gmail.com', 'finish update ' . $csvFile, '' );
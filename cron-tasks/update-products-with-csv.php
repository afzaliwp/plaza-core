<?php
// Load WordPress
require_once( 'wp-load.php' );

// Path to CSV file
$csvFile = 'laptops - microsoft-laptop_after.csv';

// Open the CSV file
if ( ( $handle = fopen( $csvFile, "r" ) ) !== false ) {
	// Skip the first line (header)
	fgetcsv( $handle );
	wp_mail( 'afzaliwp@gmail.com', 'start update ' . $csvFile, '' );

	// Loop through the CSV rows
	while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== false ) {
		$product_id   = $data[ 0 ];
		$final_title  = $data[ 1 ];
		$english_name = $data[ 2 ];
		$rank_title   = $data[ 3 ];
		$rank_desc    = $data[ 4 ];

		// Get the old permalink
		$old_permalink = get_permalink( $product_id );

		// Generate the new permalink
		$permalink = sanitize_title( $final_title );

		// Update the post
		wp_update_post( [
			'ID'         => $product_id,
			'post_name'  => $permalink,
			'post_title' => $final_title,
		] );

		// Get the new permalink
		$new_permalink = get_permalink( $product_id );

		// If the permalink has changed, add a 301 redirect
		if ( $old_permalink !== $new_permalink ) {
			global $wpdb;
			$sources = serialize( [
				[
					'ignore'     => '',
					'pattern'    => str_replace( get_bloginfo('url') . '/', '', $old_permalink ),
					'comparison' => 'exact',
				]
			] );
			$wpdb->insert( $wpdb->prefix . 'rank_math_redirections', [
				'sources'     => $sources,
				'url_to'      => $new_permalink,
				'header_code' => 301,
			] );
		}

		// Update the post meta
		update_post_meta( $product_id, 'rank_math_title', $rank_title );
		update_post_meta( $product_id, 'rank_math_description', $rank_desc );
		update_post_meta( $product_id, 'english_title', $english_name );

		// Output the updated post
		echo '<p><a href="' . $new_permalink . '">' . $final_title . '</a></p>';
	}

	// Close the CSV file
	fclose( $handle );
}

wp_mail( 'afzaliwp@gmail.com', 'finish update ' . $csvFile, '' );
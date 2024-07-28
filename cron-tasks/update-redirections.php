<?php
// Load WordPress environment
require( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

global $wpdb;
// Open the CSV file
if (($handle = fopen("laptops - lenovo-all_after (1).csv", "r")) !== FALSE) {
	// Loop through each row in the CSV
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$old_link = $data[5]; // Get the old link
		$new_link = $data[6]; // Get the new link

		var_dump( $old_link, $new_link );
		// Prepare the SQL query
		$sql = $wpdb->prepare(
			"UPDATE plza_rank_math_redirections SET url_to = %s WHERE url_to = %s",
			$new_link,
			$old_link
		);

		// Execute the SQL query
		$res = $wpdb->query($sql);
		var_dump( $res );
	}

	fclose($handle);
}
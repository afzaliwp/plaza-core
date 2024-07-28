<?php
// Load WordPress environment
require( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

// Open the CSV file
if (($handle = fopen("laptops - asus-all_after (1).csv", "r")) !== FALSE) {
	$new_rows = []; // Array to hold new data

	// Loop through each row in the CSV
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		var_dump($data);
		$old_link = $data[5]; // Get the old link
		$final_title = $data[1]; // Get the final title

		// Sanitize the final title using WordPress function
		$sanitized_title = sanitize_title($final_title);

		// Create the new link
		$new_link = $old_link . $sanitized_title;

		// Add the new link to the end of the row
		$data[] = $new_link;

		// Add the new row to the new_rows array
		$new_rows[] = $data;
	}

	fclose($handle);

	// Open the CSV file for writing
	if (($handle = fopen("laptops - asus-all_after (1).csv", "w")) !== FALSE) {
		// Write each row in the new_rows array to the CSV file
		foreach ($new_rows as $row) {
			fputcsv($handle, $row);
		}

		fclose($handle);
	}
}

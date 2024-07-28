<?php

use AfzaliWP\Plaza_Theme\Includes\Helper;

require_once __DIR__ . '/wp-load.php';
function generate_product_strings() {
	$args = [
		'post_type'      => 'product',
		'posts_per_page' => -1, // Adjust based on your needs
//		'paged'          => 1, // Adjust based on your needs
		'tax_query'      => [
			[
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => 'microsoft-laptop'
			]
		],
	];

	$products = get_posts( $args );

	$array = [];
	foreach ( $products as $product ) {
		$product                   = wc_get_product( $product );
		$attributes                = $product->get_attributes();
		$display_size              = '';
		$brand                     = 'مایکروسافت';
		$brand_eng                     = 'Microsoft';
		$series                    = ''; // Fixed series
		$model_serial              = '';
		$cpu                       = '';
		$cpu_series                = '';
		$ram_capacity              = '';
		$internal_storage_capacity = '';
		$gpu_model                 = '';

		// Extracting model serial from the title using regex
		preg_match( '/مدل\s(\w+)/', $product->get_title(), $matches );
		if ( ! empty( $matches ) ) {
			$model_serial = $matches[ 1 ];
		}

//		preg_match( '/\s(\w+) مدل/', $product->get_title(), $matches );
//		if ( ! empty( $matches ) ) {
//			$seri = $matches[ 1 ];
//			var_dump( $seri );
//		}

		// Extracting other attributes using wc_get_product_terms
		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				switch ( $attribute->get_name() ) {
					case 'pa_display':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$display_size = $attribute_label . 'ی';
						break;
					case 'pa_cpu':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$cpu = str_replace( [ 'core ', 'Core ' ], '', $attribute_label );
						break;
					case 'pa_cpu-series':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$cpu_series = $attribute_label;
						break;
					case 'pa_ram':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$ram_capacity = $attribute_label;
						break;
					case 'pa_ssd':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$internal_storage_capacity = $attribute_label . ' ';
						$ssd                       = $attribute_label;
						break;
					case 'pa_hdd':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$internal_storage_capacity .= $attribute_label . ' ';
						$hdd                       = $attribute_label;
						break;
					case 'pa_gpu':
						$term = get_term_by( 'id', $attribute->get_options()[ 0 ], $attribute->get_name() );

						if ( $term && ! is_wp_error( $term ) ) {
							// Get the attribute label
							$attribute_label = $term->name;
						} else {
							$attribute_label = ''; // Set default or handle error
						}

						$gpu_model = $attribute_label;
						break;
				}
			}
		}

		// Generate the string
//		$string = "لپ تاپ " . $brand . " " . $display_size . " " . $series . " " . $model_serial . " " . $cpu . " " . $cpu_series . " " . $ram_capacity . " " . $internal_storage_capacity . " " . $gpu_model;

//		$string2 = 'قیمت و خرید ' . str_replace( 'ندارد', '', $string ) . ' مشخصات';
//		$string3 = 'خرید اینترنتی ' . str_replace( 'ندارد', '', $string ) . ' در انواع رنگ بندی، مقایسه، بررسی، نظرات، لیست بروز قیمت امروز در پلازا دیجیتال + امکان ارتقای رم و هارد با حفظ گارانتی + نصب ویندوز و برنامه به صورت رایگان. ';

		$product_title = str_replace( ' گیمینگ', '', $product->get_title() );
		if ( $ssd && 'ندارد' !== $ssd ) {
			$ssd = $ssd . ' SSD ';
		} else {
			$ssd = '';
		}

		if ( $hdd && 'ندارد' !== $hdd ) {
			$hdd = $hdd . ' HDD ';
		} else {
			$hdd = '';
		}
//		$english_name = 'Asus ROG ' . $model_serial . ' ' . $display_size . ' ' . $cpu . " " . $cpu_series . " " . $ram_capacity . " RAM " . $ssd . $hdd . $gpu_model;

//		$english_name = str_replace( [ 'ندارد', ' اینچی' ], [ '', '"' ], $english_name );

//		$pattern     = "/(لپ تاپ) (\d+\.\d+) (اینچی) (\S+) (.+)/u";
//		$replacement = "$1 $4 $2 $3 مدل $5";

		$pattern     = "/(لپ تاپ) (\d+(\.\d+)? اینچی) (\S+) (.+)/u";
		$replacement = "$1 $4 $2 مدل $5";

		preg_match( $pattern, $product_title, $matches );
		$seri_model = str_replace( ' مدل', '', $matches[ 5 ] ); // This captures "ROG Strix G15"

		$transformed_title = $matches[ 1 ] . ' ' . $matches[ 4 ] . ' ' . $matches[ 2 ] . ' ' . $matches[ 5 ];

		$transformed_title = str_replace( $matches[ 5 ], $seri_model, $transformed_title );

		$final_title = $transformed_title . " " . $cpu . " " . $cpu_series . " " . $ram_capacity . " " . $ssd . $hdd . $gpu_model;

//		$english_name = preg_replace( '/(\bGeForce\s)?(RTX\d{4}(T?i?)|GT\d{4}(T?i?))\s?(OC\sEdition)?\s?\d*GB\s?(GDDR6X|GDDR6|DDR5|DDR3)?/i', '$2', $english_name );
		$final_title = preg_replace( '/(\bGeForce\s)?(RTX\d{4}(T?i?)|GT\d{4}(T?i?))\s?(OC\sEdition)?\s?\d*GB\s?(GDDR6X|GDDR6|DDR5|DDR3)?/i', '$2', $final_title );
//		$rank_title  = preg_replace( '/(\bGeForce\s)?(RTX\d{4}(T?i?)|GT\d{4}(T?i?))\s?(OC\sEdition)?\s?\d*GB\s?(GDDR6X|GDDR6|DDR5|DDR3)?/i', '$2', $rank_title );
//		$rank_desc   = preg_replace( '/(\bGeForce\s)?(RTX\d{4}(T?i?)|GT\d{4}(T?i?))\s?(OC\sEdition)?\s?\d*GB\s?(GDDR6X|GDDR6|DDR5|DDR3)?/i', '$2', $rank_desc );

//		$english_name = str_replace( 'Geforce ', '', $english_name );

		$final_title = str_replace( [ 'Geforce ', 'GeForce', 'مدل ' ], '', $final_title );
		$final_title = str_replace( 'اینچی', 'اینچی مدل', $final_title );

		$permalink = sanitize_title(  $final_title );

		preg_match_all( '/مدل (.*)/', $final_title, $matches );
		$english_name = $brand_eng . ' ' . implode( ' ', $matches[ 1 ] );

		$rank_title = 'قیمت و خرید ' . $final_title . ' مشخصات';
		$rank_desc = 'خرید اینترنتی ' . $final_title . ' در انواع رنگ بندی، مقایسه، بررسی، نظرات، لیست بروز قیمت امروز در پلازا دیجیتال. ';

		$product_id = $product->get_id();
		$array[]    = [
			'before' => [
				'ID'                    => $product_id,
				'permalink'             => $product->get_permalink(),
				'final_title'           => $product->get_title(),
				'english_title'         => Helper::get_field( 'english_title', $product_id ),
				'rank_math_title'       => get_post_meta( $product_id, 'rank_math_title', true ),
				'rank_math_description' => get_post_meta( $product_id, 'rank_math_description', true ),
			],
			'after'  => [
				'ID'                    => $product->get_id(),
				'permalink'             => $permalink,
				'final_title'           => $final_title,
				'english_title'         => $english_name,
				'rank_math_title'       => str_replace( [ '   ', '  ' ], ' ', $rank_title ),
				'rank_math_description' => str_replace( [ '   ', '  ' ], ' ', $rank_desc ),
			],
		];

//		wp_update_post( [
//			'ID'         => $product->get_id(),
//			'post_name'  => $permalink,
//			'post_title' => $final_title,
//		] );
//		echo '<p><a href="' . $product->get_permalink() . '">' . $final_title . '</a></p>';
//		update_post_meta( $product->get_id(), 'rank_math_title', $rank_title );
//		update_post_meta( $product->get_id(), 'rank_math_description', $rank_desc );
//		update_post_meta( $product->get_id(), 'english_title', $english_name );
	}

	var_dump( $array );
	return $array;
}

$array = generate_product_strings();
//var_dump( $array );

function export_array_as_csv( $array, $filename_prefix ) {
	// Generate CSV headers
	$csv_content = implode( ',', [
			'ID',
			'permalink',
			'final_title',
			'english_title',
			'rank_math_title',
			'rank_math_description'
		] ) . "\n";

	$before = $csv_content;
	$after  = $csv_content;
	// Correctly iterate over the nested arrays

	$before_row = [];
	$after_row  = [];
	foreach ( $array as $item ) {
		foreach ( $item as $key => $value ) {
			if ( 'before' === $key ) {
				$before_row[] = array_values( $value );
			}
			if ( 'after' === $key ) {
				$after_row[] = array_values( $value );
			}
		}
	}

	foreach ( $before_row as $before_row_item ) {
		$before .= implode( ',', $before_row_item ) . "\n";
	}
	foreach ( $after_row as $after_row_item ) {
		$after .= implode( ',', $after_row_item ) . "\n";
	}

	// Create CSV file paths
	$before_csv_path = $filename_prefix . '_before.csv';
	$after_csv_path  = $filename_prefix . '_after.csv';

	// Write CSV content to files
	file_put_contents( $before_csv_path, $before );
	file_put_contents( $after_csv_path, $after );
	// Initiate downloads
//	header('Content-Type: application/csv');
//	header('Content-Disposition: attachment; filename="'. basename($before_csv_path). '"');
//	readfile($before_csv_path);
//	exit;
//
//	header('Content-Type: application/csv');
//	header('Content-Disposition: attachment; filename="'. basename($after_csv_path). '"');
//	readfile($after_csv_path);
//	exit;
}

export_array_as_csv($array, 'microsoft-laptop');

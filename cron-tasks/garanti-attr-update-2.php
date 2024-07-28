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

// Open a log file
$log_file = fopen( 'log.txt', 'w' );


$numbers = array(
    115915, 112377, 112368, 111463, 111449, 111438, 111420, 111378, 111336, 111328, 111273, 111227, 111106, 111062, 109938, 109921, 109884, 109773, 108850, 108821, 108786, 108760, 108639, 108178, 108158, 108139, 107573, 107548, 107452, 107435, 107413, 107394, 107353, 107321, 106661, 106634, 105507, 105489, 105459, 104611, 104571, 104498, 104456, 104381, 103864, 103799, 103200, 103166, 103145, 103131, 103086, 102144, 102142, 102126, 102109, 102090, 102052, 102030, 101991, 101581, 101570, 101429, 101407, 101354, 99734, 99703, 99513, 99488, 99421, 96888, 96862, 96748, 95737, 95640, 95628, 95615, 95200, 95002, 94697, 94660, 94647, 94629, 94580, 94551, 94484, 94469, 94448, 93332, 93317, 92563, 92552, 92543, 92525, 92177, 90428, 90418, 90245, 90235, 89630, 89609, 89595, 88299, 87442, 87349, 87286, 87068, 87039, 87006, 86886, 86570, 86526, 86265, 85501, 85075, 85014, 83971, 82785, 82684, 82663, 82266, 82214, 82132, 82101, 82034, 81949, 81937, 81915, 81856, 81812, 81789, 81585, 81510, 81456, 80626, 75546, 75378, 75137, 75110, 75073, 75036, 75011, 71220, 71214, 71209, 71204, 71195, 70643, 69555, 69431, 69420, 69408, 69397, 69384, 69364, 68577, 68401, 68379, 67985, 65212, 65186, 64915, 63675, 62580, 62574, 62414, 62401, 62352, 62342, 60058, 60046, 59899, 58377, 58366, 57947, 57936, 57658, 57483, 57461, 57448, 57438, 57399, 57380, 57029, 57008, 56994, 56983, 56951, 56941, 56922
);

$additional_numbers = array(
    56906, 56840, 56828, 56458, 54013, 53515, 52707, 52370, 51993, 51246, 50907, 50133, 47365, 46854, 42982, 42226, 42216, 42211, 42207, 42205, 42170, 42163, 42157, 42147, 42139, 40333, 38584, 38534, 38062, 37829, 37827, 37824, 37649, 37639, 37629, 37178, 35213, 35204, 35181, 34999, 34535, 34528, 34475, 32515, 29754, 28807, 28713, 27600, 26522, 26520, 25797, 25795, 24784, 20755, 18566, 18119, 18118, 18116, 18113, 18112, 11296, 11263, 11246, 111479, 111147, 103879, 103840, 103774, 99230, 75748, 70065, 63532, 52012, 51135, 46122, 40852, 37687, 34717, 29545, 28649, 28647, 116612, 115976, 114844, 114833, 114697, 114773, 114807, 114818, 116612, 115995, 116299, 114451, 118463, 118481, 118516, 118824, 118840, 118870, 118853, 118888, 118902, 118916, 118929, 86065, 87957, 45541, 39904, 126589, 128564, 130328, 131386, 131441, 131427, 131414, 131608, 131829, 131873, 126575, 126505, 122744, 122744, 122188, 122197, 122212, 122224, 122234, 122177, 122097, 122064, 121235, 120696, 120680, 120658, 120620, 120600, 120583, 119493, 119510, 119868, 119903, 119914, 119963, 119987, 120006, 120017, 120045, 120111, 120167, 120190, 132178, 132081,132061,132053,132040,132010,133764,133732,133746,133720,133707,133683,133207,132498,132489,133780,133838,133863,133891,134884,136561,136530,136615,137769,137812,137885,137873,137864,137853,137840,138923,138957,138976,139014,139047, 139220
);

$args = array(
    'type' => 'variable',
    'limit' => -1,
    'include' => array_merge($numbers, $additional_numbers),
);


$args2 = array(
    'type' => 'variable',
    'limit' => -1,
    'include' => [89609],
);

// $products = wc_get_products($args);
$products = wc_get_products($args);

var_dump('Count All Ids: ' . count(array_merge($numbers, $additional_numbers)));
var_dump('Count: ' . count($products));
echo PHP_EOL;
echo PHP_EOL;
// Loop through each product
foreach ( $products as $product ) {
    var_dump('$product_id: ' . $product->get_id());
    echo PHP_EOL;
	if ( $product->is_type( 'variable' ) ) {
		// Get the variations
		$variations = $product->get_available_variations();
        $price = 0;
		foreach ( $variations as $variation ) {
			if( '24-month-plaza-guaranty' !== $variation['attributes']['attribute_pa_garanti']) {
                $price = $variation['display_price'];
                $quantity = get_post_meta($variation['variation_id'], '_stock', true);
            }						
		}

        foreach ( $variations as $variation ) {
			if( '24-month-plaza-guaranty' === $variation['attributes']['attribute_pa_garanti']) {
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
                    update_post_meta($variation[ 'variation_id' ], '_price', intval( $price ) * 1.01);
                    update_post_meta($variation[ 'variation_id' ], '_regular_price', intval( $price ) * 1.01);
                    update_post_meta($variation[ 'variation_id' ], '_sale_price', '');
                    $variation_obj->set_price( intval( $price ) * 1.01 );
                    var_dump(intval( $price ) * 1.01);
                    update_post_meta($variation[ 'variation_id' ], '_stock', $quantity);
                    var_dump($quantity);
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

var_dump('done');

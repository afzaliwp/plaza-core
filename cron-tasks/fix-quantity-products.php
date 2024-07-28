<?php
$start = time();
require_once('wp-load.php');
global $wpdb, $table_prefix;
// SQL query to find products with empty price
$query = "
    SELECT p.ID
    FROM ${table_prefix}posts p
    INNER JOIN ${table_prefix}postmeta pm ON p.ID = pm.post_id
    WHERE p.post_type = 'product'
    AND pm.meta_key = '_stock_status'
    AND pm.meta_value = 'instock'
    AND NOT EXISTS (
        SELECT 1
        FROM ${table_prefix}posts v
        INNER JOIN ${table_prefix}postmeta vpm ON v.ID = vpm.post_id
        WHERE v.post_parent = p.ID
        AND v.post_type = 'product_variation'
        AND vpm.meta_key = '_price'
        AND vpm.meta_value != ''
    )
";

// Get the product IDs
$product_ids = $wpdb->get_col($query);

var_dump($product_ids);
foreach ($product_ids as $product_id) {
	// Set quantity to 0
	update_post_meta($product_id, '_stock', 0);

	// Set stock status to 'outofstock'
	update_post_meta($product_id, '_stock_status', 'outofstock');
}

?>

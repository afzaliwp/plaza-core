<?php
// Load WordPress
require( dirname(__FILE__) . '/wp-load.php' );

function update_special_offers() {
	// Get the 'special-offers' term
	$term = get_term_by('slug', 'special-offers', 'product_cat');

	// Get the current on sale products
	$on_sale_products = wc_get_product_ids_on_sale();

	// Get the previous on sale products from the options
	$previous_on_sale_products = get_option('special_offers_products', []);

	// Find the products to add and remove from the 'special-offers' category
	$products_to_remove = array_diff($previous_on_sale_products, $on_sale_products);

	// Add the new on sale products to the 'special-offers' category
	foreach ($on_sale_products as $product_id) {
		wp_set_object_terms($product_id, $term->term_id, 'product_cat', true);
	}

	// Remove the no longer on sale products from the 'special-offers' category
	foreach ($products_to_remove as $product_id) {
		wp_remove_object_terms($product_id, $term->term_id, 'product_cat');
	}

	// Update the 'special_offers_products' option with the current on sale products
	update_option('special_offers_products', $on_sale_products);
}

update_special_offers();

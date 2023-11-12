<?php

namespace AfzaliWP\PlazaDigital\Includes\Third_Parties;

defined( 'ABSPATH' ) || die();

class Schema {
	public function __construct() {
		add_action( 'woocommerce_after_single_product', [ $this, 'add_product_schema' ] );
	}

	public function add_product_schema() {
		global $product;
		$schema = [
			"@context"        => "https://schema.org/",
			"@type"           => "Product",
			"name"            => $product->get_name(),
			"image"           => wp_get_attachment_url( $product->get_image_id() ),
			"description"     => $product->get_description(),
			"mpn"             => $product->get_sku(),
			"review"          => [
				"@type"        => "Review",
				"reviewRating" => [
					"@type"       => "Rating",
					"ratingValue" => $product->get_average_rating(),
					"bestRating"  => "5",
				],
				"author"       => [
					"@type" => "Person",
					"name"  => get_the_author_meta( 'display_name', $product->post->post_author ),
				],
			],
			"aggregateRating" => [
				"@type"       => "AggregateRating",
				"ratingValue" => $product->get_average_rating(),
				"reviewCount" => $product->get_review_count(),
			],
			"offers"          => [
				"@type"           => "Offer",
				"url"             => get_permalink(),
				"priceCurrency"   => get_woocommerce_currency(),
				"price"           => $product->get_price(),
				"priceValidUntil" => date( 'Y-m-d', strtotime( '+1 day' ) ),
				"itemCondition"   => "https://schema.org/Product",
				"availability"    => $product->is_in_stock() ? "In Stock" : "Out of Stock",
			],
		];

		// Get the brand from product attribute
		$brand = $product->get_attribute( 'brand' );
		if ( ! empty( $brand ) ) {
			$schema[ "brand" ] = [
				"@type" => "Brand",
				"name"  => $brand,
			];
		}

		echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';
	}
}
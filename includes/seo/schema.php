<?php

namespace AfzaliWP\PlazaDigital\Includes\SEO;

defined( 'ABSPATH' ) || die();

class Schema {
	public function __construct() {
		add_action( 'woocommerce_after_single_product', [ $this, 'add_product_schema' ] );
	}

	public function add_product_schema() {
		global $product;
		$schema = [
			"@context" => "https://schema.org/",
			"@type" => "Product",
			"name" => $product->get_name(),
			"image" => array_merge(
				[ wp_get_attachment_url( $product->get_image_id() ) ],
				array_map( function ( $image_id ) {
					return wp_get_attachment_url( $image_id );
				}, $product->get_gallery_image_ids() )
			),
			"description" => $product->get_description(),
			"mpn" => $product->get_sku(),
			"aggregateRating" => [
				"@type" => "AggregateRating",
				"ratingValue" => 0 == $product->get_average_rating() ? 5 : $product->get_average_rating(),
				"reviewCount" => 0 == $product->get_review_count() ? 1 : $product->get_review_count(),
			],
			"offers" => [
				"@type" => "Offer",
				"url" => get_permalink(),
				"priceCurrency" => 'IRR',
				"price" => intval( $product->get_price() ) * 10,
				"priceValidUntil" => date( 'Y-m-d', strtotime( '+1 day' ) ),
				"itemCondition" => "https://schema.org/NewCondition",
				"availability" => $product->is_in_stock() ? "In Stock" : "Out of Stock",
			],
		];

		if ( $product->get_id() === 94577 ) {
			$schema = [
				"@context" => "https://schema.org/",
				"@type" => "Product",
				"name" => $product->get_name(),
				"image" => array_merge(
					[ wp_get_attachment_url( $product->get_image_id() ) ],
					array_map( function ( $image_id ) {
						return wp_get_attachment_url( $image_id );
					}, $product->get_gallery_image_ids() )
				),
				"description" => $product->get_description(),
				"mpn" => $product->get_sku(),
				"aggregateRating" => [
					"@type" => "AggregateRating",
					"ratingValue" => 0 == $product->get_average_rating() ? 5 : $product->get_average_rating(),
					"reviewCount" => 0 == $product->get_review_count() ? 1 : $product->get_review_count(),
				],
				"offers" => [
					"@type" => "Offer",
					"url" => get_permalink(),
					"priceCurrency" => 'IRR',
					"price" => intval( $product->get_price() ) * 10,
					"priceValidUntil" => date( 'Y-m-d', strtotime( '+1 day' ) ),
					"itemCondition" => "https://schema.org/NewCondition",
					"availability" => $product->is_in_stock() ? "In Stock" : "Out of Stock",
					"hasMerchantReturnPolicy" => true,
					"returnPolicy" => [
						"daysToReturn" => 15,
						"returnInstructions" => "لطفاً با پشتیبانی تماس بگیرید تا روند بازگشت کالا را شروع کنید."
					],
					"shippingDetails" => [
						"shippingMethod" => "پست سفارشی",
						"deliveryTime" => "1-2 روز کاری",
						"shippingLocations" => [ "تهران", "اصفهان", "شیراز" ]
					]
				],
			];
		}

		// Get the brand from product attribute
		$brand = $product->get_attribute( 'brand' );
		if ( !empty( $brand ) ) {
			$schema[ "brand" ] = $brand;
		}

		if ( empty( $brand ) ) {
			$brand = $product->get_attribute( 'berand' );
			if ( !empty( $brand ) ) {
				$schema[ "brand" ] = $brand;
			}
		}

		echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';
	}
}
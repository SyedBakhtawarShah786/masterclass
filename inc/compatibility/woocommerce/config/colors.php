<?php

class Tophive_WC_Colors {
	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ), 100 );
	}

	function config( $configs ) {
		$section = 'global_styling';

		$configs[] = array(
			'name'    => "{$section}_shop_colors_heading",
			'type'    => 'heading',
			'section' => $section,
			'title'   => esc_html__( 'Shop Colors', 'masterclass' ),
		);

		$configs[] = array(
			'name'        => "{$section}_shop_primary",
			'type'        => 'color',
			'section'     => $section,
			'title'       => esc_html__( 'Shop Buttons', 'masterclass' ),
			'placeholder' => '#c3512f',
			'description' => esc_html__( 'Color for add to cart, checkout buttons. Default is Secondary Color.', 'masterclass' ),
			'css_format'  => apply_filters(
				'tophive/styling/shop-buttons',
				'
					.woocommerce .button.add_to_cart_button, 
					.woocommerce .button.alt,
					.woocommerce .button.added_to_cart, 
					.woocommerce .button.checkout, 
					.woocommerce .button.product_type_variable,
					.item--wc_cart .cart-icon .cart-qty .tophive-wc-total-qty
					{
					    background-color: {{value}};
					}'
			),
			'selector'    => 'format',
		);

		$configs[] = array(
			'name'        => "{$section}_shop_rating_stars",
			'type'        => 'color',
			'section'     => $section,
			'title'       => esc_html__( 'Rating Stars', 'masterclass' ),
			'description' => esc_html__( 'Color for rating stars, default is Secondary Color.', 'masterclass' ),
			'placeholder' => '#c3512f',
			'css_format'  => apply_filters(
				'tophive/styling/shop-rating-stars',
				'
					.comment-form-rating a, 
					.star-rating,
					.comment-form-rating a:hover, 
					.comment-form-rating a:focus, 
					.star-rating:hover, 
					.star-rating:focus
					{
					    color: {{value}};
					}'
			),
			'selector'    => 'format',
		);

		$configs[] = array(
			'name'        => "{$section}_shop_onsale",
			'type'        => 'color',
			'section'     => $section,
			'title'       => esc_html__( 'On Sale', 'masterclass' ),
			'placeholder' => '#77a464',
			'css_format'  => apply_filters(
				'tophive/styling/shop-onsale',
				'
					span.onsale
					{
					    background-color: {{value}};
					}'
			),
			'selector'    => 'format',
		);

		return $configs;
	}
}

new Tophive_WC_Colors();

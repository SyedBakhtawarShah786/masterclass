<?php

class Tophive_WC_Products {
	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ), 100 );
	}

	function config( $configs ) {
		$section = 'woocommerce_product_catalog';

		$configs[] = array(
			'name'    => 'woocommerce_catalog_tablet_columns',
			'type'    => 'text',
			'section' => $section,
			'label'   => esc_html__( 'Products per row on tablet', 'masterclass' ),
		);
		$configs[] = array(
			'name'    => 'woocommerce_catalog_mobile_columns',
			'type'    => 'text',
			'section' => $section,
			'default' => 1,
			'label'   => esc_html__( 'Products per row on mobile', 'masterclass' ),
		);

		return $configs;
	}
}

new Tophive_WC_Products();

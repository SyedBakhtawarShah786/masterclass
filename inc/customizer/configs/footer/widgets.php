<?php

class Tophive_Builder_Item_Footer_Widget_1 {
	public $id = 'footer-1';

	function item() {
		return array(
			'name'    => esc_html__( 'Footer Sidebar 1', 'masterclass' ),
			'id'      => 'footer-1',
			'width'   => '3',
			'section' => 'sidebar-widgets-footer-1',
		);
	}

	function customize() {
		return tophive_footer_layout_settings( 'footer-1', 'sidebar-widgets-footer-1' );
	}
}

class Tophive_Builder_Item_Footer_Widget_2 { //phpcs:ignore
	public $id = 'footer-2';

	function item() {
		return array(
			'name'    => esc_html__( 'Footer Sidebar 2', 'masterclass' ),
			'id'      => 'footer-2',
			'width'   => '3',
			'section' => 'sidebar-widgets-footer-2',
		);
	}

	function customize() {
		return tophive_footer_layout_settings( 'footer-2', 'sidebar-widgets-footer-2' );
	}
}

class Tophive_Builder_Item_Footer_Widget_3 { //phpcs:ignore
	public $id = 'footer-3';

	function item() {
		return array(
			'name'    => esc_html__( 'Footer Sidebar 3', 'masterclass' ),
			'id'      => 'footer-3',
			'width'   => '3',
			'section' => 'sidebar-widgets-footer-3',
		);
	}

	function customize() {
		return tophive_footer_layout_settings( 'footer-3', 'sidebar-widgets-footer-3' );
	}
}

class Tophive_Builder_Item_Footer_Widget_4 { //phpcs:ignore
	public $id = 'footer-4';

	function item() {
		return array(
			'name'    => esc_html__( 'Footer Sidebar 4', 'masterclass' ),
			'id'      => 'footer-4',
			'width'   => '3',
			'section' => 'sidebar-widgets-footer-4',
		);
	}

	function customize() {
		return tophive_footer_layout_settings( 'footer-4', 'sidebar-widgets-footer-4' );
	}
}

class Tophive_Builder_Item_Footer_Widget_5 { //phpcs:ignore
	public $id = 'footer-5';

	function item() {
		return array(
			'name'    => esc_html__( 'Footer Sidebar 5', 'masterclass' ),
			'id'      => 'footer-5',
			'width'   => '3',
			'section' => 'sidebar-widgets-footer-5',
		);
	}

	function customize() {
		return tophive_footer_layout_settings( 'footer-5', 'sidebar-widgets-footer-5' );
	}
}

class Tophive_Builder_Item_Footer_Widget_6 { //phpcs:ignore
	public $id = 'footer-6';

	function item() {
		return array(
			'name'    => esc_html__( 'Footer Sidebar 6', 'masterclass' ),
			'id'      => 'footer-6',
			'width'   => '3',
			'section' => 'sidebar-widgets-footer-6',
		);
	}

	function customize() {
		return tophive_footer_layout_settings( 'footer-6', 'sidebar-widgets-footer-6' );
	}
}


function tophive_change_footer_widgets_location( $wp_customize ) {
	for ( $i = 1; $i <= 6; $i ++ ) {
		if ( $wp_customize->get_section( 'sidebar-widgets-footer-' . $i ) ) {
			$wp_customize->get_section( 'sidebar-widgets-footer-' . $i )->panel = 'footer_settings';
		}
	}

}

add_action( 'customize_register', 'tophive_change_footer_widgets_location', 999 );

/**
 * Always show footer widgets for customize builder
 *
 * @param bool   $active
 * @param string $section
 *
 * @return bool
 */
function tophive_customize_footer_widgets_show( $active, $section ) {
	if ( strpos( $section->id, 'widgets-footer-' ) ) {
		$active = true;
	}

	return $active;
}

add_filter( 'customize_section_active', 'tophive_customize_footer_widgets_show', 15, 2 );


/**
 * Display Footer widget
 *
 * @param string $footer_id
 */
function tophive_builder_footer_widget_item( $footer_id = 'footer-1' ) {
	$show = false;
	if ( is_active_sidebar( $footer_id ) ) {
		echo '<div class="widget-area">';
		dynamic_sidebar( $footer_id );
		$show = true;
		echo '</div>';
	}

}

function tophive_builder_footer_1_item() {
	tophive_builder_footer_widget_item( 'footer-1' );
}

function tophive_builder_footer_2_item() {
	tophive_builder_footer_widget_item( 'footer-2' );
}

function tophive_builder_footer_3_item() {
	tophive_builder_footer_widget_item( 'footer-3' );
}

function tophive_builder_footer_4_item() {
	tophive_builder_footer_widget_item( 'footer-4' );
}

function tophive_builder_footer_5_item() {
	tophive_builder_footer_widget_item( 'footer-5' );
}

function tophive_builder_footer_6_item() {
	tophive_builder_footer_widget_item( 'footer-6' );
}

Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_Widget_1() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_Widget_2() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_Widget_3() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_Widget_4() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_Widget_5() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_Widget_6() );

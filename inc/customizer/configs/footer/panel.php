<?php

class Tophive_Builder_Footer extends Tophive_Customize_Builder_Panel {
	public $id = 'footer';

	function get_config() {
		return array(
			'id'         => $this->id,
			'title'      => esc_html__( 'Footer Builder', 'masterclass' ),
			'control_id' => 'footer_builder_panel',
			'panel'      => 'footer_settings',
			'section'    => 'footer_builder_panel',
			'devices'    => array(
				'desktop' => esc_html__( 'Footer Layout', 'masterclass' ),
			),
		);
	}

	function get_rows_config() {
		return array(
			'main'   => esc_html__( 'Footer Main', 'masterclass' ),
			'bottom' => esc_html__( 'Footer Bottom', 'masterclass' ),
		);
	}

	function customize() {
		$fn     = 'tophive_customize_render_footer';
		$config = array(
			array(
				'name'     => 'footer_settings',
				'type'     => 'panel',
				'priority' => 98,
				'title'    => esc_html__( 'Footer', 'masterclass' ),
			),

			array(
				'name'  => 'footer_builder_panel',
				'type'  => 'section',
				'panel' => 'footer_settings',
				'title' => esc_html__( 'Footer Builder', 'masterclass' ),
			),

			array(
				'name'                => 'footer_builder_panel',
				'type'                => 'js_raw',
				'section'             => 'footer_builder_panel',
				'theme_supports'      => '',
				'title'               => esc_html__( 'Footer Builder', 'masterclass' ),
				'selector'            => '#site-footer',
				'render_callback'     => $fn,
				'container_inclusive' => true,
			),

		);

		return $config;
	}

	function row_config( $section = false, $section_name = false ) {

		if ( ! $section ) {
			$section = 'footer_top';
		}
		if ( ! $section_name ) {
			$section_name = esc_html__( 'Footer Top', 'masterclass' );
		}

		// Text skin.
		$color_mode = 'dark-mode';
		if ( 'footer_top' == $section ) {
			$color_mode = 'light-mode';
		}

		$selector           = '#cb-row--' . str_replace( '_', '-', $section );
		$skin_mode_selector = '.footer--row-inner.' . str_replace( '_', '-', $section ) . '-inner';

		$fn = 'tophive_customize_render_footer';

		$config = array(
			array(
				'name'           => $section,
				'type'           => 'section',
				'panel'          => 'footer_settings',
				'theme_supports' => '',
				'title'          => $section_name,
			),

			array(
				'name'            => $section . '_layout',
				'type'            => 'select',
				'section'         => $section,
				'title'           => esc_html__( 'Layout', 'masterclass' ),
				'selector'        => $selector,
				'render_callback' => $fn,
				'css_format'      => 'html_class',
				'default'         => 'layout-full-contained',
				'choices'         => array(
					'layout-full-contained' => esc_html__( 'Full width - Contained', 'masterclass' ),
					'layout-fullwidth'      => esc_html__( 'Full Width', 'masterclass' ),
					'layout-contained'      => esc_html__( 'Contained', 'masterclass' ),
				),
			),

			array(
				'name'        => $section . '_noti_layout',
				'type'        => 'custom_html',
				'section'     => $section,
				'title'       => '',
				'description' => __( "Layout <code>Full width - Contained</code> and <code>Full Width</code> will not fit browser width because you've selected <a class='focus-control' data-id='site_layout' href='#'>Site Layout</a> as <code>Boxed</code> or <code>Framed</code>", 'masterclass' ),
				'required'    => array(
					array( 'site_layout', '=', array( 'site-boxed', 'site-framed' ) ),
				),
			),

			array(
				'name'       => $section . '_text_mode',
				'type'       => 'image_select',
				'section'    => $section,
				'selector'   => $skin_mode_selector,
				'css_format' => 'html_class',
				'title'      => esc_html__( 'Skin Mode', 'masterclass' ),
				'default'    => $color_mode,
				'choices'    => array(
					'dark-mode'  => array(
						'img'   => esc_url( get_template_directory_uri() ) . '/assets/images/customizer/text_mode_light.svg',
						'label' => 'Dark',
					),
					'light-mode' => array(
						'img'   => esc_url( get_template_directory_uri() ) . '/assets/images/customizer/text_mode_dark.svg',
						'label' => 'Light',
					),
				),
			),

			array(
				'name'       => "{$section}_background_color",
				'type'       => 'color',
				'section'    => $section,
				'title'      => esc_html__( 'Background Color', 'masterclass' ),
				'selector'   => "{$selector} .footer--row-inner",
				'css_format' => 'background-color: {{value}}',
			),
		);
		$config = apply_filters( 'tophive/builder/' . $this->id . '/rows/section_configs', $config, $section, $section_name );
		return $config;
	}
}

function tophive_footer_layout_settings( $item_id, $section ) {

	global $wp_customize;

	if ( is_object( $wp_customize ) ) {
		global $wp_registered_sidebars;
		$name = $section;
		if ( is_array( $wp_registered_sidebars ) ) {
			if ( isset( $wp_registered_sidebars[ $item_id ] ) ) {
				$name = $wp_registered_sidebars[ $item_id ]['name'];
			}
		}
		$wp_customize->add_section(
			$section,
			array(
				'title' => $name,
			)
		);
	}

	if ( function_exists( 'tophive_header_layout_settings' ) ) {
		return tophive_header_layout_settings( $item_id, $section, 'tophive_customize_render_footer', 'footer_' );
	}

	return false;
}

Tophive_Customize_Layout_Builder()->register_builder( 'footer', new Tophive_Builder_Footer() );




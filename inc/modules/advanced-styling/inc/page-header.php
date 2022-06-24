<?php

class TophiveCore_Advanced_Styling_Page_Header extends TophiveCoreModulesBasics {
	private $section = 'page_header';

	function __construct() {
		add_filter( 'tophive/titlebar/config', array( $this, 'titlebar_config' ), 15, 2 );
		add_filter( 'tophive/cover/config', array( $this, 'cover_config' ), 15, 2 );
	}

	function titlebar_config( $configs, $class = null ) {

		$section  = 'page_header';
		$selector = '#page-titlebar';
		$name     = 'titlebar';
		$config   = array(

			array(
				'name'       => $name . '_typo',
				'type'       => 'typography',
				'section'    => $section,
				'title'      => __( 'Title Typography', 'masterclass' ),
				'selector'   => "{$selector} .titlebar-title",
				'css_format' => 'typography',
			),

			array(
				'name'       => $name . '_typo_desc',
				'type'       => 'typography',
				'section'    => $section,
				'title'      => __( 'Tagline Typography', 'masterclass' ),
				'selector'   => "{$selector} .titlebar-tagline",
				'css_format' => 'typography',
			),

			array(
				'name'       => $name . '_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Titlebar Styling', 'masterclass' ),
				'selector'   => array(
					'normal'            => "{$selector}",
					'normal_text_color' => "{$selector} .titlebar-title, {$selector} .titlebar-tagline",
					'normal_padding'    => "{$selector}",
				),
				'css_format' => 'styling', // styling.
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false, // disable for special field.
						'bg_image'   => false,
						'bg_cover'   => false,
						'bg_repeat'  => false,
						'margin'     => false,
					),
					'hover_fields'  => false,
				),
			),

			array(
				'name'       => $name . 'title_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Titlebar Title Styling', 'masterclass' ),
				'selector'   => array(
					'normal' => "{$selector} .titlebar-title",
				),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image'   => false,
						'bg_cover'   => false,
						'bg_repeat'  => false,
						'box_shadow' => false,
					),
					'hover_fields'  => false,
				),
			),

			array(
				'name'       => $name . 'tagline_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Titlebar Tagline Styling', 'masterclass' ),
				'selector'   => array(
					'normal' => "{$selector} .titlebar-tagline",
				),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image'   => false,
						'bg_cover'   => false,
						'bg_repeat'  => false,
						'box_shadow' => false,
					),
					'hover_fields'  => false,
				),
			),

		);

		return array_merge( $configs, $config );

	}

	function cover_config( $configs, $class = null ) {

		$section              = 'page_header';
		$selector             = '#page-cover';
		$transparent_selector = '.is-header-transparent #page-cover';
		$name                 = 'header_cover';
		$config               = array(

			array(
				'name'            => "{$name}_padding_top",
				'type'            => 'slider',
				'section'         => $section,
				'device_settings' => true,
				'title'           => __( 'Cover Margin Top', 'masterclass' ),
				'description'     => __( 'Only apply for pages with transparent header enable, value should equal to current header height.', 'masterclass' ),
				'selector'        => "{$transparent_selector}",
				'css_format'      => 'padding-top: {{value}};',
			),

			array(
				'name'       => $name . '_title_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Cover Title Styling', 'masterclass' ),
				'selector'   => array(
					'normal'            => "{$selector} .page-cover-title",
					'normal_link_color' => "{$selector} a",
					'hover_link_color'  => "{$selector} a:hover",
				),
				'css_format' => 'styling', // styling
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false, // disable for special field.
						'bg_image'   => false,
						'bg_cover'   => false,
						'bg_repeat'  => false,
						'box_shadow' => false,
					),
					'hover_fields'  => false,
				),
			),

			array(
				'name'       => $name . '_tagline_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Cover Tagline Styling', 'masterclass' ),
				'selector'   => array(
					'normal'            => "{$selector} .page-cover-tagline",
					'normal_link_color' => "{$selector} a",
					'hover_link_color'  => "{$selector} a:hover",
				),
				'css_format' => 'styling', // styling
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false, // disable for special field.
						'bg_image'   => false,
						'bg_cover'   => false,
						'bg_repeat'  => false,
						'box_shadow' => false,
					),
					'hover_fields'  => false,
				),
			),

			array(
				'name'       => "{$name}_title_typo",
				'type'       => 'typography',
				'css_format' => 'typography',
				'section'    => $section,
				'selector'   => "{$selector} .page-cover-title",
				'title'      => __( 'Cover Title Typography', 'masterclass' ),
			),

			array(
				'name'       => "{$name}_tagline_typo",
				'type'       => 'typography',
				'css_format' => 'typography',
				'section'    => $section,
				'selector'   => "{$selector} .page-cover-tagline",
				'title'      => __( 'Cover Tagline Typography', 'masterclass' ),
			),

		);

		return array_merge( $configs, $config );
	}
}

new TophiveCore_Advanced_Styling_Page_Header();

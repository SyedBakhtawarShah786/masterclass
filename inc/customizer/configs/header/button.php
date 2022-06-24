<?php

class Tophive_Builder_Item_Button {
	public $id = 'button';

	function __construct() {
		add_filter( 'tophive/icon_used', array( $this, 'used_icon' ) );
	}

	function used_icon( $list = array() ) {
		$list[ $this->id ] = 1;

		return $list;
	}

	function item() {
		return array(
			'name'    => esc_html__( 'Button', 'masterclass' ),
			'id'      => 'button',
			'col'     => 0,
			'width'   => '4',
			'section' => 'header_button', // Customizer section to focus when click settings.
		);
	}

	function customize() {
		$section  = 'header_button';
		$prefix   = 'header_button';
		$fn       = array( $this, 'render' );
		$selector = 'a.item--' . $this->id;
		$config   = array(
			array(
				'name'  => $section,
				'type'  => 'section',
				'panel' => 'header_settings',
				'title' => esc_html__( 'Button', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_text',
				'type'            => 'text',
				'section'         => $section,
				'theme_supports'  => '',
				'selector'        => $selector,
				'render_callback' => $fn,
				'title'           => esc_html__( 'Text', 'masterclass' ),
				'default'         => esc_html__( 'Button', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_icon',
				'type'            => 'icon',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'theme_supports'  => '',
				'title'           => esc_html__( 'Icon', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_position',
				'type'            => 'radio_group',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'default'         => 'before',
				'title'           => esc_html__( 'Icon Position', 'masterclass' ),
				'choices'         => array(
					'before' => esc_html__( 'Before', 'masterclass' ),
					'after'  => esc_html__( 'After', 'masterclass' ),
				),
			),

			array(
				'name'            => $prefix . '_link',
				'type'            => 'text',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'title'           => esc_html__( 'Link', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_target',
				'type'            => 'checkbox',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'checkbox_label'  => esc_html__( 'Open link in a new tab.', 'masterclass' ),
			),

			array(
				'name'        => $prefix . '_typography',
				'type'        => 'typography',
				'section'     => $section,
				'title'       => esc_html__( 'Typography', 'masterclass' ),
				'description' => esc_html__( 'Advanced typography for button', 'masterclass' ),
				'selector'    => $selector,
				'css_format'  => 'typography',
				'default'     => array(),
			),

			array(
				'name'        => $prefix . '_styling',
				'type'        => 'styling',
				'section'     => $section,
				'title'       => esc_html__( 'Styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced styling for button', 'masterclass' ),
				'selector'    => array(
					'normal' => $selector,
					'hover'  => $selector . ':hover',
				),
				'css_format'  => 'styling',
				'default'     => array(),
				'fields'      => array(
					'normal_fields' => array(
						'link_color'    => false, // disable for special field.
						'margin'        => false,
						'bg_image'      => false,
						'bg_cover'      => false,
						'bg_position'   => false,
						'bg_repeat'     => false,
						'bg_attachment' => false,
					),
					'hover_fields'  => array(
						'link_color' => false, // disable for special field.
					),
				),
			),

		);

		// Item Layout.
		return array_merge( $config, tophive_header_layout_settings( $this->id, $section ) );
	}


	function render() {
		$text          = top_hive()->get_setting( 'header_button_text' );
		$icon          = top_hive()->get_setting( 'header_button_icon' );
		$new_window    = top_hive()->get_setting( 'header_button_target' );
		$link          = top_hive()->get_setting( 'header_button_link' );
		$icon_position = top_hive()->get_setting( 'header_button_position' );
		$classes       = array( 'item--' . $this->id, 'tophive-btn tophive-builder-btn' );

		$icon = wp_parse_args(
			$icon,
			array(
				'type' => '',
				'icon' => '',
			)
		);

		$target = '';
		if ( 1 == $new_window ) {
			$target = ' target="_blank" ';
		}

		$icon_html = '';
		if ( $icon['icon'] ) {
			$icon_html = '<i class="' . esc_attr( $icon['icon'] ) . '"></i> ';
		}
		$classes[] = 'is-icon-' . $icon_position;
		if ( ! $text ) {
			$text = esc_html__( 'Button', 'masterclass' );
		}

		echo '<a' . $target . ' href="' . esc_url( $link ) . '" class="' . esc_attr( join( ' ', $classes ) ) . '">';
		if ( 'after' != $icon_position ) {
			echo tophive_sanitize_filter($icon_html) . esc_html( $text );
		} else {
			echo esc_html( $text ) . $icon_html;
		}
		echo '</a>';
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'header', new Tophive_Builder_Item_Button() );



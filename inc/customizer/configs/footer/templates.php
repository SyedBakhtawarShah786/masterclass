<?php

class Tophive_Builder_Footer_Templates {
	public $id = 'footer_templates';

	function customize() {
		$section = 'footer_templates';
		$prefix  = 'footer_templates_';

		$id          = 'footer';
		$theme_name  = wp_get_theme()->get( 'Name' );
		$option_name = "{$theme_name}_{$id}_saved_templates";

		$saved_templates = get_option( $option_name );
		if ( ! is_array( $saved_templates ) ) {
			$saved_templates = array();
		}

		$saved_templates = array_reverse( $saved_templates );

		$n = count( $saved_templates );

		$html = '';
		$html .= '<span class="customize-control-title">' . esc_html__( 'Saved Templates', 'masterclass' ) . '</span>';
		$html .= '<ul class="list-saved-templates list-boxed ' . ( $n > 0 ? 'has-templates' : 'no-templates' ) . '">';
		if ( count( $saved_templates ) > 0 ) {
			foreach ( $saved_templates as $key => $tpl ) {
				$tpl = wp_parse_args(
					$tpl,
					array(
						'name' => '',
						'data' => '',
					)
				);
				if ( ! $tpl['name'] ) {
					$name = esc_html__( 'Untitled', 'masterclass' );
				} else {
					$name = $tpl['name'];
				}
				$html .= '<li class="saved_template li-boxed" data-control-id="' . esc_attr( $prefix . 'save' ) . '" data-id="' . esc_attr( $key ) . '" data-data="' . esc_attr( json_encode( $tpl['data'] ) ) . '">' . esc_html( $name ) . ' <a href="#" class="load-tpl">' . esc_html__( 'Load', 'masterclass' ) . '</a><a href="#" class="remove-tpl">' . esc_html__( 'Remove', 'masterclass' ) . '</a></li>'; // phpcs:ignore
			}
		}

		$html .= '<li class="no_template">' . esc_html__( 'No saved templates.', 'masterclass' ) . '</li>';

		$html .= '</ul>';
		$html .= '</div>';

		return array(
			array(
				'name'     => $section,
				'type'     => 'section',
				'panel'    => 'footer_settings',
				'priority' => 0,
				'title'    => esc_html__( 'Templates', 'masterclass' ),
			),

			array(
				'name'           => $prefix . 'save',
				'type'           => 'custom_html',
				'section'        => $section,
				'theme_supports' => '',
				'title'          => esc_html__( 'Save Template', 'masterclass' ),
				'description'    => '<div class="save-template-form"><input type="text" data-builder-id="footer" data-control-id="' . esc_attr( $prefix . 'save' ) . '" class="template-input-name change-by-js"><button class="button button-secondary save-builder-template" type="button">' . esc_html__( 'Save', 'masterclass' ) . '</button></div>' . $html,
			),
		);
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'footer', 'Tophive_Builder_Footer_Templates' );

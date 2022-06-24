<?php

class Tophive_Builder_Footer_Item_Copyright {
	public $id = 'footer_copyright';
	public $section = 'footer_copyright';
	public $name = 'footer_copyright';
	public $label = '';

	/**
	 * Optional construct
	 */
	function __construct() {
		$this->label = esc_html__( 'Copyright', 'masterclass' );
	}

	/**
	 * Register Builder item
	 *
	 * @return array
	 */
	function item() {
		return array(
			'name'    => esc_html__( 'Copyright', 'masterclass' ),
			'id'      => $this->id,
			'col'     => 0,
			'width'   => '6',
			'section' => $this->section, // Customizer section to focus when click settings.
		);
	}

	/**
	 * Optional, Register customize section and panel.
	 *
	 * @return array
	 */
	function customize() {
		$fn = array( $this, 'render' );

		$config = array(
			array(
				'name'  => $this->section,
				'type'  => 'section',
				'panel' => 'footer_settings',
				'title' => $this->label,
			),

			array(
				'name'            => $this->name,
				'type'            => 'textarea',
				'section'         => $this->section,
				'selector'        => '.builder-footer-copyright-item',
				'render_callback' => $fn,
				'theme_supports'  => '',
				'default'         => esc_html__( 'Copyright &copy; {current_year} {site_title} - Powered by {theme_author}.', 'masterclass' ),
				'title'           => esc_html__( 'Copyright Text', 'masterclass' ),
				'description'     => esc_html__( 'Arbitrary HTML code or shortcode. Available tags: {current_year}, {site_title}, {theme_author}', 'masterclass' ),
			),

			array(
				'name'       => $this->name . '_typography',
				'type'       => 'typography',
				'section'    => $this->section,
				'title'      => esc_html__( 'Copyright Text Typography', 'masterclass' ),
				'selector'   => '.builder-item--footer_copyright, .builder-item--footer_copyright p',
				'css_format' => 'typography',
				'default'    => array(),
			),

			array(
				'name'       => $this->name . '_styling',
				'type'       => 'styling',
				'section'    => $this->section,
				'title'      => __( 'Styling', 'tophive-pro' ),
				'selector'   => array(
					'normal_text_color' => "body .builder-item--footer_copyright p",
					'hover_text_color' => "body .builder-item--footer_copyright, .builder-item--footer_copyright p:hover",
					'normal_link_color' => "body .builder-item--footer_copyright a",
					'hover_link_color' => "body .builder-item--footer_copyright a:hover",
				),
				'css_format' => 'styling', // styling.
				'fields'     => array(
					'normal_fields' => array(
						'margin'            => false,
						'border_heading'    => false,
						'border_width'      => false,
						'border_color'      => false,
						'border_radius'     => false,
						'border_style'      => false,
						'box_shadow'        => false,
						'bg_heading'        => false,
						'bg_cover'          => false,
						'bg_repeat'         => false,
						'bg_color'          => false,
						'bg_image'          => false,
					),
					'hover_fields'  => array(
						'margin'            => false,
						'bg_heading'        => false,
						'bg_cover'          => false,
						'bg_repeat'         => false,
						'bg_color'          => false,
						'bg_image'          => false,
						'border_heading'    => false,
						'border_width'      => false,
						'border_color'      => false,
						'border_radius'     => false,
						'border_style'      => false,
						'box_shadow'        => false,
					),
				),
			),
		);

		return array_merge( $config, tophive_footer_layout_settings( $this->id, $this->section ) );
	}

	/**
	 * Optional. Render item content
	 */
	function render() {
		$tags = array(
			'current_year' => date_i18n( 'Y' ),
			'site_title'   => get_bloginfo( 'name' ),
			'theme_author' => sprintf( '<a href="https://themeforest.net/user/tophive">%1$s</a>', 'masterclass' ), // Brand name.
		);

		$content = top_hive()->get_setting( $this->name );

		foreach ( $tags as $k => $v ) {
			$content = str_replace( '{' . $k . '}', $v, $content );
		}

		echo '<div class="builder-footer-copyright-item footer-copyright">';
		echo apply_filters( 'tophive_the_content', wp_kses_post( balanceTags( tophive_sanitize_filter($content), true ) ) ); // WPCS: XSS OK.
		echo '</div>';
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Footer_Item_Copyright() );

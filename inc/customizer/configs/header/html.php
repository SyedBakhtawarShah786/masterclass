<?php

class Tophive_Builder_Item_HTML {
	/**
	 * @var string Required
	 */
	public $id = 'html';
	/**
	 * @var string Optional
	 */
	public $section = 'header_html';
	/**
	 * @var string Optional
	 */
	public $name = 'header_html';
	/**
	 * @var string Optional
	 */
	public $label = '';
	public $priority = 200;
	public $panel = 'header_settings';

	/**
	 * Optional construct
	 *
	 * Tophive_Builder_Item_HTML constructor.
	 */
	function __construct() {
		$this->label = esc_html__( 'HTML 1', 'masterclass' );
	}

	/**
	 * Register Builder item
	 *
	 * @return array
	 */
	function item() {
		return array(
			'name'    => $this->label,
			'id'      => $this->id,
			'col'     => 0,
			'width'   => '4',
			'section' => $this->section, // Customizer section to focus when click settings.
		);
	}

	/**
	 * Optional, Register customize section and panel.
	 *
	 * @return array
	 */
	function customize() {
		// Render callback function.
		$fn     = array( $this, 'render' );
		$config = array(
			array(
				'name'     => $this->section,
				'type'     => 'section',
				'panel'    => $this->panel,
				'priority' => $this->priority,
				'title'    => $this->label,
			),

			array(
				'name'            => $this->name,
				'type'            => 'textarea',
				'section'         => $this->section,
				'selector'        => '.builder-header-' . $this->id . '-item',
				'render_callback' => $fn,
				'theme_supports'  => '',
				'title'           => esc_html__( 'HTML', 'masterclass' ),
				'description'     => esc_html__( 'Arbitrary HTML code.', 'masterclass' ),
			),

			array(
				'name'       => $this->name . '_typo',
				'type'       => 'typography',
				'section'    => $this->section,
				'selector'   => '.builder-header-' . $this->id . '-item.item--html p, .builder-header-' . $this->id . '-item.item--html',
				'css_format' => 'typography',
				'title'      => esc_html__( 'Typography Setting', 'masterclass' ),
			),

		);

		// Item Layout.
		return array_merge( $config, tophive_header_layout_settings( $this->id, $this->section ) );
	}

	/**
	 * Optional. Render item content
	 */
	function render() {
		$content = top_hive()->get_setting( $this->name );
		echo '<div class="builder-header-' . esc_attr( $this->id ) . '-item item--html">';
		echo apply_filters( 'tophive_the_content', wp_kses_post( balanceTags( tophive_sanitize_filter($content), true ) ) );
		echo '</div>';
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'header', new Tophive_Builder_Item_HTML() );

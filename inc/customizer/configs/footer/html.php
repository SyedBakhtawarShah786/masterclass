<?php
class Tophive_Builder_Footer_Item_HTML {
	public $id = 'html'; // Required
	public $section = 'footer_html'; // Optional
	public $name = 'footer_html'; // Optional
	public $label = ''; // Optional

	/**
	 * Optional construct
	 *
	 * Tophive_Builder_Item_HTML constructor.
	 */
	function __construct() {
		$this->label = __( 'HTML 1', 'masterclass' );
	}

	/**
	 * Register Builder item
	 *
	 * @return array
	 */
	function item() {
		return array(
			'name' => $this->label,
			'id' => $this->id,
			'col' => 0,
			'width' => '6',
			'section' => $this->section, // Customizer section to focus when click settings
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
				'name' => $this->section,
				'type' => 'section',
				'panel' => 'footer_settings',
				'title' => $this->label,
			),

			array(
				'name' => $this->name,
				'type' => 'textarea',
				'section' => $this->section,
				'selector' => '.builder-footer-html-item',
				'render_callback' => $fn,
				'theme_supports' => '',
				'title' => __( 'HTML', 'masterclass' ),
				'description' => __( 'Arbitrary HTML code.', 'masterclass' ),
			),

			array(
				'name' => $this->name . '_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.builder-footer-' . $this->id . '-item.item-footer--html p, .builder-footer-' . $this->id . '-item.item-footer--html',
				'css_format' => 'typography',
				'title' => __( 'Typography Setting', 'masterclass' ),
			),

			array(
				'name' => $this->name . '_text_align',
				'type' => 'text_align',
				'section' => $this->section,
				'selector' => '.site-footer .builder-first--html',
				'css_format' => 'text-align: {{value}};',
				'title'   => __( 'Align', 'masterclass' ),
				'device_settings' => true,
			),
		);

		// Item Layout
		return array_merge( $config, tophive_footer_layout_settings( $this->id, $this->section ) );
	}

	/**
	 * Optional. Render item content
	 */
	function render() {
		$content = top_hive()->get_setting( $this->name );
		echo '<div class="builder-footer-' . $this->id . '-item item-footer--html">';
		echo apply_filters( 'tophive_the_content', wp_kses_post( balanceTags( tophive_sanitize_filter($content), true ) ) );
		echo '</div>';
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Footer_Item_HTML() );

class TophiveCore_Module_HTML_2 extends Tophive_Builder_Footer_Item_HTML {

	public $name = null;
	public $description = null;
	function __construct() {
		$this->name = __( 'HTML 2', 'masterclass' );
	}
}


class Tophive_Builder_Item_HTML_2 extends  Tophive_Builder_Item_HTML {
	public $id = 'html_2'; // Required
	public $section = 'header_html_2'; // Optional
	public $name = 'header_html_2'; // Optional
	public $label = ''; // Optional
	function __construct() {
		$this->label = __( 'HTML 2', 'masterclass' );
	}
}
class Tophive_Builder_Item_HTML_3 extends  Tophive_Builder_Item_HTML {
	public $id = 'html_3'; // Required
	public $section = 'header_html_3'; // Optional
	public $name = 'header_html_3'; // Optional
	public $label = ''; // Optional
	function __construct() {
		$this->label = __( 'HTML 3', 'masterclass' );
	}
}

class Tophive_Builder_Item_Footer_HTML_2 extends  Tophive_Builder_Footer_Item_HTML {
	public $id = 'footer_html_2'; // Required
	public $section = 'footer_html_2'; // Optional
	public $name = 'footer_html_2';
	public $label;
	public $panel;

	function __construct() {
		$this->label = __( 'HTML 2', 'masterclass' );
		$this->panel = 'footer_settings';
		$this->section = 'footer_html_2';
	}
}

class Tophive_Builder_Item_Footer_HTML_3 extends  Tophive_Builder_Footer_Item_HTML {
	public $id = 'footer_html_3';
	public $section;
	public $name = 'footer_html_3';
	public $label = '';
	public $panel = '';
	function __construct() {
		$this->label = __( 'HTML 3', 'masterclass' );
		$this->panel = 'footer_settings';
		$this->section = 'footer_html_3';
	}
}
Tophive_Customize_Layout_Builder()->register_item( 'header', new Tophive_Builder_Item_HTML_2() );
Tophive_Customize_Layout_Builder()->register_item( 'header', new Tophive_Builder_Item_HTML_3() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_HTML_2() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Footer_HTML_3() );

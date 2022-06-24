<?php
class Tophive_Builder_Item_Contact {
	public $id;
	public $label;
	public $prefix;
	public $selector;
	public $section;
	public $panel = 'header_settings';

	/**
	 * Optional construct
	 */
	function __construct() {
		$this->id = 'header_contact';
		$this->label = __( 'Contact Info', 'masterclass' );
		$this->prefix = 'header_contact';
		$this->selector = '.header--row:not(.header--transparent) #header_contact';
		$this->section = 'header_contact';
		add_filter( 'tophive/icon_used', array( $this, 'used_icon' ) );
	}

	function used_icon( $list = array() ) {
		$list[ $this->id ] = 1;
		return $list;
	}

	function item() {
		return array(
			'name' => $this->label,
			'id' => $this->id,
			'width' => '2',
			'section' => $this->section, // Customizer section to focus when click settings.
		);
	}

	function customize() {
		$section = $this->section;
		$fn = array( $this, 'render' );
		$config = array(
			array(
				'name' => $section,
				'type' => 'section',
				'panel' => $this->panel,
				'theme_supports' => '',
				'title' => $this->label,
			),

			array(
				'name'             => $this->prefix . '_items',
				'type'             => 'repeater',
				'section'          => $section,
				'selector'         => $this->selector,
				'render_callback'  => $fn,
				'title'            => __( 'Contact Information', 'masterclass' ),
				'live_title_field' => 'title',
				'default'          => array(
					array(
						'title' => _x( '123 Street City', 'Default info', 'masterclass' ),
						'icon' => array(
							'type' => 'font-awesome',
							'icon' => 'fa fa-map-marker',
						),
						'type' => 'text',
					),
					array(
						'title' => '1.800.123.4567',
						'icon' => array(
							'type' => 'font-awesome',
							'icon' => 'fa fa-phone',
						),
						'type' => 'phone',
					),
					array(
						'title' => 'email@example.com',
						'icon' => array(
							'icon' => 'fa fa-envelope-o',
							'type' => 'font-awesome',
						),
						'type' => 'email',
					),
				),
				'fields'           => array(
					array(
						'name'  => 'title',
						'type'  => 'text',
						'label' => __( 'Title', 'masterclass' ),
					),
					array(
						'name'  => 'icon',
						'type'  => 'icon',
						'label' => __( 'Icon', 'masterclass' ),
					),
					array(
						'name'  => 'type',
						'type'  => 'select',
						'default'  => 'text',
						'label' => __( 'Field Type', 'masterclass' ),
						'choices' => array(
							'text' => __( 'Text', 'masterclass' ),
							'phone' => __( 'Phone', 'masterclass' ),
							'email' => __( 'Email', 'masterclass' ),
							'url'   => __( 'URL', 'masterclass' ),
						),
					),
					array(
						'name'  => 'url',
						'type'  => 'text',
						'label' => __( 'Link', 'masterclass' ),
						'required' => array( 'type', '=', 'url' ),
					),
				),
			),

			array(
				'name'            => $this->prefix . '_typo',
				'type'            => 'typography',
				'section'         => $section,
				'label'           => __( 'Item Typography', 'masterclass' ),
				'selector'        => "{$this->selector} li",
				'css_format'      => 'typography',
			),

			array(
				'name'       => $this->prefix . '_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Item Styling', 'masterclass' ),
				'selector'   => array(
					'normal'            => "{$this->selector} li",
					'normal_margin'     => "{$this->selector} li",
					'normal_text_color' => "{$this->selector} li, {$this->selector} li a",
					'hover'             => "{$this->selector} li:hover, {$this->selector} li a:hover",
					'hover_text_color'  => "{$this->selector} {$this->selector} li:hover, {$this->selector} li a:hover",
				),
				'css_format' => 'styling',
				'fields'     => array(
					'tabs'          => array(
						'normal' => __( 'Normal', 'masterclass' ),
						'hover'  => __( 'Hover', 'masterclass' ),
					),
					'normal_fields' => array(
						// 'padding' => false // disable for special field.
						'link_color'    => false,
						'bg_cover'      => false,
						'bg_image'      => false,
						'bg_repeat'     => false,
						'bg_attachment' => false,
						'bg_position'   => false,
					),
					'hover_fields'  => array(
						'link_color'    => false,
						'bg_cover'      => false,
						'bg_image'      => false,
						'bg_repeat'     => false,
						'bg_attachment' => false,
						'bg_position'   => false,
					), // disable hover tab and all fields inside.
				),
			),

			array(
				'name'            => $this->prefix . '_icon_size',
				'type'            => 'slider',
				'device_settings' => true,
				'section'         => $section,
				'min'             => 10,
				'step'            => 1,
				'max'             => 100,
				// 'selector'        => "$selector li a",
				'selector'        => 'format',
				'css_format'      => "{$this->selector} li .c-icon { font-size: {{value}}; }",
				'label'           => __( 'Icon Size', 'masterclass' ),
			),

			array(
				'name'            => $this->prefix . '_padding',
				'type'            => 'slider',
				'device_settings' => true,
				'section'         => $section,
				'min'             => .1,
				'step'            => .1,
				'max'             => 5,
				'selector'        => "{$this->selector} li .c-icon",
				'unit'            => 'em',
				'css_format'      => 'padding: {{value_no_unit}}em;',
				'label'           => __( 'Icon Padding', 'masterclass' ),
			),

			array(
				'name'       => $this->prefix . '_icon_styling',
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Item Icon Styling', 'masterclass' ),
				'selector'   => array(
					'normal'            => "{$this->selector} li .c-icon",
					'hover'             => "{$this->selector} li:hover .c-icon, {$this->selector} li a:hover .c-icon",
				),
				'css_format' => 'styling',
				'fields'     => array(
					'tabs'          => array(
						'normal' => __( 'Normal', 'masterclass' ),
						'hover'  => __( 'Hover', 'masterclass' ),
					),
					'normal_fields' => array(
						// 'padding' => false // disable for special field.
						'link_color'    => false,
						'padding'       => false,
						'bg_cover'      => false,
						'bg_image'      => false,
						'bg_repeat'     => false,
						'bg_attachment' => false,
						'bg_position'   => false,
						'box_shadow'   => false,
					),
					'hover_fields'  => array(
						'link_color'    => false,
						'bg_cover'      => false,
						'bg_image'      => false,
						'bg_repeat'     => false,
						'bg_attachment' => false,
						'bg_position'   => false,
						'box_shadow'   => false,
					), // disable hover tab and all fields inside.
				),
			),

			array(
				'name'            => $this->prefix . '_display_align',
				'type'            => 'select',
				'device_settings' => true,
				'section'         => $section,
				'devices'         => array( 'desktop', 'mobile' ),
				'selector'        => "{$this->selector}",
				'render_callback'  => $fn,
				'label'           => __( 'Display Align', 'masterclass' ),
				'choices'           => array(
					'horizontal' => __( 'Horizontal', 'masterclass' ),
					'vertical' => __( 'Vertical', 'masterclass' ),
				),
			),

		);

		if ( $this->id == 'header_contact' ) {
			$config[] = array(
				'name' => $this->prefix . '_vertical_on_sidebar',
				'type' => 'checkbox',
				'section' => $section,
				'selector' => "{$this->selector}",
				'render_callback' => $fn,
				'checkbox_label' => __( 'Display as vertical in menu sidebar', 'masterclass' ),
			);
		}

		// Item Layout
		return array_merge( $config, tophive_header_layout_settings( $this->id, $section ) );
	}

	/**
	 * @see https://polylang.wordpress.com/documentation/documentation-for-developers/functions-reference/
	 */
	function render() {
		$classes = array();
		$classes[] = 'builder-contact--item';

		$items = top_hive()->get_setting( $this->prefix . '_items' );
		$align_desktop = top_hive()->get_setting( $this->prefix . '_display_align', 'desktop' );
		$align_mobile = top_hive()->get_setting( $this->prefix . '_display_align', 'mobile' );
		$v_in_sidebar = top_hive()->get_setting( $this->prefix . '_vertical_on_sidebar' );

		if ( $v_in_sidebar ) {
			$classes[] = 'hca-sidebar-vertical';
		}
		$classes[] = 'cont-desktop-' . $align_desktop;
		$classes[] = 'cont-mobile-' . $align_mobile;

		echo '<ul id="' . esc_attr( $this->id ) . '" class="' . esc_attr( join( ' ', $classes ) ) . '">';
		foreach ( (array) $items as $item ) {
			$item = wp_parse_args(
				$item,
				array(
					'title' => '',
					'icon' => '',
					'url' => '',
					'type' => '',
					'_visibility' => '',
				)
			);
			$item['icon'] = wp_parse_args(
				$item['icon'],
				array(
					'icon' => '',
				)
			);

			if ( $item['_visibility'] != 'hidden' ) {
				$icon_html = '';
				if ( $item['icon']['icon'] ) {
					$icon_html = '<i class="c-icon ' . esc_attr( $item['icon']['icon'] ) . '"></i>';
				}

				if ( $item['type'] == 'email' ) {
					$item['title'] = antispambot( $item['title'] );
				}

				if ( is_rtl() ) {
					$text = $item['title'] . $icon_html;
				} else {
					$text = $icon_html . $item['title'];
				};
				echo '<li class="c-type-' . esc_attr( $item['type'] ) . '">';

				switch ( $item['type'] ) {
					case 'url':
						printf( '<a href="%1$s">%2$s</a>', esc_url( $item['url'] ), $text );
						break;
					case 'email':
						$email_link = sprintf( 'mailto:%s', $item['title'] );
						 printf( '<a href="%1$s">%2$s</a>', esc_url( $email_link, array( 'mailto' ) ), $text );
						break;
					case 'phone':
						printf( '<a href="%1$s">%2$s</a>', esc_attr( 'tel:' . $item['title'], array( 'tel' ) ), $text );
						break;
					default:
						echo '<span>' . $text . '</span>';
				}
				echo '</li>';
			}
		}
		echo '</ul>';

	}

}

class Tophive_Builder_Footer_Item_Contact extends Tophive_Builder_Item_Contact {
	public $id;
	public $label;
	public $prefix;
	public $selector;
	public $section;
	public $panel;
	/**
	 * Optional construct
	 */
	function __construct() {
		parent::__construct();
		$this->id = 'footer_contact';
		$this->prefix = 'footer_contact';
		$this->selector = '#footer_contact';
		$this->section = 'footer_contact';
		$this->panel = 'footer_settings';
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'header', new Tophive_Builder_Item_Contact() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Footer_Item_Contact() );
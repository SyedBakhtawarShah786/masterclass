<?php

class Tophive_Builder_Item_Link_List {
	public $id = 'link-lists';
	public $section = 'link_list_widget';
	public $class = 'link-list-widget';
	public $selector = 'th-link-lists';
	public $panel = 'footer_settings';
	public $label = '';

	function __construct() {
		$this->label = esc_html__( 'link lists', 'masterclass' );
		$this->selector = '.' . $this->class;
	}

	function item() {
		return array(
			'name'    => $this->label,
			'id'      => $this->id,
			'col'     => 0,
			'width'   => '4',
			'section' => $this->section, // Customizer section to focus when click settings.
		);
	}

	function customize() {
		$section  = $this->section;
		$prefix   = $this->section;
		$fn       = array( $this, 'render' );
		$selector = "{$this->selector}.tophive-builder-link-list";
		$config   = array(
			array(
				'name'           => $section,
				'type'           => 'section',
				'panel'          => $this->panel,
				'theme_supports' => '',
				'title'          => esc_html__( 'Link lists', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_heading',
				'type'            => 'text',
				'section'         => $section,
				'selector'        => "{$this->selector} h5.link-list-heading",
				'label'  => esc_html__( 'Heading', 'masterclass' ),
			),
			array(
				'name'             => $prefix . '_items',
				'type'             => 'repeater',
				'section'          => $section,
				'selector'         => $this->selector,
				'render_callback'  => $fn,
				'title'            => esc_html__( 'Link items', 'masterclass' ),
				'live_title_field' => 'title',
				'default'          => array(),
				'fields'           => array(
					array(
						'name'  => 'title',
						'type'  => 'text',
						'label' => esc_html__( 'Title', 'masterclass' ),
					),

					array(
						'name'  => 'url',
						'type'  => 'text',
						'label' => esc_html__( 'URL', 'masterclass' ),
					),

				),
			),

			array(
				'name'            => $prefix . '_target',
				'type'            => 'checkbox',
				'section'         => $section,
				'selector'        => $this->selector,
				'render_callback' => $fn,
				'default'         => 1,
				'checkbox_label'  => esc_html__( 'Open link in a new tab.', 'masterclass' ),
			),
			array(
				'name'            => $prefix . '_nofollow',
				'type'            => 'checkbox',
				'section'         => $section,
				'render_callback' => $fn,
				'default'         => 1,
				'checkbox_label'  => esc_html__( 'Adding rel="nofollow" for social links.', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_size',
				'type'            => 'slider',
				'device_settings' => true,
				'section'         => $section,
				'min'             => 10,
				'step'            => 1,
				'max'             => 100,
				'selector'        => 'format',
				'css_format'      => "{$this->selector} li a { font-size: {{value}}; }",
				'label'           => esc_html__( 'Font Size', 'masterclass' ),
			),

			array(
				'name'            => $prefix . '_padding',
				'type'            => 'slider',
				'device_settings' => true,
				'section'         => $section,
				'min'             => .1,
				'step'            => .1,
				'max'             => 5,
				'selector'        => "{$this->selector} li a",
				'unit'            => 'em',
				'css_format'      => 'padding: {{value_no_unit}}em;',
				'label'           => esc_html__( 'Padding', 'masterclass' ),
			),
			array(
				'name'       => $prefix . '_row_alignment',
				'type'       => 'text_align_no_justify',
				'section'    => $section,
				'title'      => __( 'Alignment', 'masterclass' ),
				'device_settings' => true,
				'selector'   =>  ".builder-item--link-lists-v",
				'css_format' => 'text-align: {{value}};',
			),
			array(
				'name'       => "{$section}_heading_styling",
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Heading Styling', 'masterclass' ),
				'selector'   => "h5.link-list-heading",
				'css_format' => 'styling', // styling.
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => true,
						'link_color' => false,
						'padding'     => true,
						'margin'     => true,
						'border_heading' => true,
						'border_width' => true,
						'border_color' => true,
						'border_radius' => true,
						'box_shadow' => false,
						'border_style'  => true,
					),
					'hover_fields'  => true,
				),
			),
			array(
				'name'            => "{$section}_heading_typo",
				'type'            => 'typography',
				'section'         => $section,
				'label'           => __( 'Heading Typography', 'masterclass' ),
				'selector'        => "h5.link-list-heading",
				'css_format'      => 'typography',
			),
			array(
				'name'       => "{$section}_box_styling",
				'type'       => 'styling',
				'section'    => $section,
				'title'      => __( 'Links Styling', 'masterclass' ),
				'selector'   => "{$this->selector} li.links a",
				'css_format' => 'styling', // styling.
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => true,
						'link_color' => false,
						'padding'     => true,
						'margin'     => true,
						'border_heading' => false,
						'border_width' => false,
						'border_color' => false,
						'border_radius' => true,
						'box_shadow' => true,
						'border_style'  => false,
					),
					'hover_fields'  => true,
				),
			),
			array(
				'name'            => "{$section}_links_typo",
				'type'            => 'typography',
				'section'         => $section,
				'label'           => __( 'Links Typography', 'masterclass' ),
				'selector'        => "{$this->selector} li.links a",
				'css_format'      => 'typography',
			),

			array(
				'name'        => $prefix . '_border',
				'type'        => 'modal',
				'section'     => $section,
				'selector'    => "{$this->selector} li a",
				'css_format'  => 'styling',
				'title'       => esc_html__( 'Border', 'masterclass' ),
				'description' => esc_html__( 'Border & border radius', 'masterclass' ),
				'fields'      => array(
					'tabs'           => array(
						'default' => '_',
					),
					'default_fields' => array(
						array(
							'name'       => 'border_style',
							'type'       => 'select',
							'class'      => 'clear',
							'label'      => esc_html__( 'Border Style', 'masterclass' ),
							'default'    => 'none',
							'choices'    => array(
								''       => esc_html__( 'Default', 'masterclass' ),
								'none'   => esc_html__( 'None', 'masterclass' ),
								'solid'  => esc_html__( 'Solid', 'masterclass' ),
								'dotted' => esc_html__( 'Dotted', 'masterclass' ),
								'dashed' => esc_html__( 'Dashed', 'masterclass' ),
								'double' => esc_html__( 'Double', 'masterclass' ),
								'ridge'  => esc_html__( 'Ridge', 'masterclass' ),
								'inset'  => esc_html__( 'Inset', 'masterclass' ),
								'outset' => esc_html__( 'Outset', 'masterclass' ),
							),
							'css_format' => 'border-style: {{value}};',
							'selector'   => "{$this->selector} li a",
						),

						array(
							'name'       => 'border_width',
							'type'       => 'css_ruler',
							'label'      => esc_html__( 'Border Width', 'masterclass' ),
							'required'   => array( 'border_style', '!=', 'none' ),
							'selector'   => "{$this->selector} li a",
							'css_format' => array(
								'top'    => 'border-top-width: {{value}};',
								'right'  => 'border-right-width: {{value}};',
								'bottom' => 'border-bottom-width: {{value}};',
								'left'   => 'border-left-width: {{value}};',
							),
						),
						array(
							'name'       => 'border_color',
							'type'       => 'color',
							'label'      => esc_html__( 'Border Color', 'masterclass' ),
							'required'   => array( 'border_style', '!=', 'none' ),
							'selector'   => "{$this->selector} li a",
							'css_format' => 'border-color: {{value}};',
						),

						array(
							'name'       => 'border_radius',
							'type'       => 'slider',
							'label'      => esc_html__( 'Border Radius', 'masterclass' ),
							'selector'   => "{$this->selector} li a",
							'css_format' => 'border-radius: {{value}};',
						),
					),
				),
			),

		);

		// Item Layout.
		return array_merge( $config, tophive_header_layout_settings( $this->id, $section ) );
	}

	function render( $item_config = array() ) {

		// $shape        = top_hive()->get_setting( $this->section . '_shape', 'all' );
		$heading   	  = top_hive()->get_setting( $this->section . '_heading' );
		$color_type   = top_hive()->get_setting( $this->section . '_color_type' );
		$items        = top_hive()->get_setting( $this->section . '_items' );
		$nofollow     = top_hive()->get_setting( $this->section . '_nofollow' );
		$target_blank = top_hive()->get_setting( $this->section . '_target' );

		$rel = '';
		if ( 1 == $nofollow ) {
			$rel = 'rel="nofollow" ';
		}

		$target = '_self';
		if ( 1 == $target_blank ) {
			$target = '_blank';
		}

		if ( ! empty( $items ) ) {
			$classes   = array();
			$classes[] = $this->class;
			$classes[] = 'tophive-builder-link-lists';
			// // if ( $shape ) {
			// // 	$shape = ' shape-' . sanitize_text_field( $shape );
			// // }
			// if ( $color_type ) {
			// 	$classes[] = 'color-' . sanitize_text_field( $color_type );
			// }
			if( !empty($heading) ){
				echo '<h5 class="link-list-heading">' . $heading . '</h5>';
			}
			echo '<ul class="' . esc_attr( join( ' ', $classes ) ) . '">';
			foreach ( (array) $items as $index => $item ) {
				$item = wp_parse_args(
					$item,
					array(
						'title'       => '',
						'url'         => '',
						'_visibility' => '',
					)
				);

				if ( 'hidden' !== $item['_visibility'] ) {
					echo '<li class="links">';
					if ( ! $item['url'] ) {
						$item['url'] = '#';
					}

					if ( $item['url'] ) {
						echo '<a ' . $rel . 'target="' . esc_attr( $target ) . '" href="' . esc_url( $item['url'] ) . '">';
					}
					echo tophive_sanitize_filter($item['title']);

					if ( $item['url'] ) {
						echo '</a>';
					}
					echo '</li>';
				}
			}

			echo '</ul>';
		}

	}

}


class Tophive_Builder_Item_Link_List_2 extends Tophive_Builder_Item_Link_List{
	public $id = 'link-lists-2';
	public $section = 'link_list_widget_2';
	public $class = 'link-list-widget-2';
	public $selector = 'th-link-lists-2';
	public $panel = 'footer_settings';
	public $label = '';

	function __construct() {
		$this->label = esc_html__( 'link lists 2', 'masterclass' );
		$this->selector = '.' . $this->class;
	}
}

class Tophive_Builder_Item_Link_List_3 extends Tophive_Builder_Item_Link_List{
	public $id = 'link-lists-3';
	public $section = 'link_list_widget_3';
	public $class = 'link-list-widget-3';
	public $selector = 'th-link-lists-3';
	public $panel = 'footer_settings';
	public $label = '';

	function __construct() {
		$this->label = esc_html__( 'link lists 3', 'masterclass' );
		$this->selector = '.' . $this->class;
	}
}

class Tophive_Builder_Item_Link_List_4 extends Tophive_Builder_Item_Link_List{
	public $id = 'link-lists-4';
	public $section = 'link_list_widget_4';
	public $class = 'link-list-widget-4';
	public $selector = 'th-link-lists-4';
	public $panel = 'footer_settings';
	public $label = '';

	function __construct() {
		$this->label = esc_html__( 'link lists 4', 'masterclass' );
		$this->selector = '.' . $this->class;
	}
}
class Tophive_Builder_Item_Link_List_5 extends Tophive_Builder_Item_Link_List{
	public $id = 'link-lists-v';
	public $section = 'link_list_widget_5';
	public $class = 'link-list-widget-v';
	public $selector = 'th-link-lists-v';
	public $panel = 'footer_settings';
	public $label = '';

	function __construct() {
		$this->label = esc_html__( 'link lists vertical', 'masterclass' );
		$this->selector = '.' . $this->class;
	}
}

Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Link_List() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Link_List_2() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Link_List_3() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Link_List_4() );
Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Item_Link_List_5() );

<?php

class Tophive_Builder_Item_Nav_Icon {
	public $id = 'nav-icon';
	public $section = 'header_menu_icon';

	function item() {
		return array(
			'name'    => esc_html__( 'Menu Icon', 'masterclass' ),
			'id'      => $this->id,
			'width'   => '3',
			'section' => $this->section, // Customizer section to focus when click settings.
		);
	}

	function customize() {
		$section  = $this->section;
		$fn       = array( $this, 'render' );
		$selector = '.menu-mobile-toggle';
		$config   = array(
			array(
				'name'           => $section,
				'type'           => 'section',
				'panel'          => 'header_settings',
				'theme_supports' => '',
				'title'          => esc_html__( 'Menu Icon', 'masterclass' ),
			),

			array(
				'name'            => 'nav_icon_text',
				'type'            => 'text',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'default'         => esc_html__( 'Menu', 'masterclass' ),
				'title'           => esc_html__( 'Label', 'masterclass' ),
			),

			array(
				'name'            => 'nav_icon_show_text',
				'type'            => 'checkbox',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'title'           => esc_html__( 'Label Settings', 'masterclass' ),
				'device_settings' => true,
				'default'         => array(
					'desktop' => 1,
					'tablet'  => 0,
					'mobile'  => 0,
				),
				'checkbox_label'  => esc_html__( 'Show Label', 'masterclass' ),
			),

			array(
				'name'            => 'nav_icon_size',
				'type'            => 'radio_group',
				'section'         => $section,
				'selector'        => $selector,
				'render_callback' => $fn,
				'title'           => esc_html__( 'Icon Size', 'masterclass' ),
				'default'         => array(
					'desktop' => 'medium',
					'tablet'  => 'medium',
					'mobile'  => 'medium',
				),
				'device_settings' => true,
				'choices'         => array(
					'small'  => esc_html__( 'Small', 'masterclass' ),
					'medium' => esc_html__( 'Medium', 'masterclass' ),
					'large'  => esc_html__( 'Large', 'masterclass' ),
				),
			),

			array(
				'name'       => 'nav_icon_item_color',
				'type'       => 'color',
				'section'    => $section,
				'title'      => esc_html__( 'Color', 'masterclass' ),
				'css_format' => 'color: {{value}};',
				'selector'   => ".header--row:not(.header--transparent) {$selector}",

			),

			array(
				'name'       => 'nav_icon_item_color_hover',
				'type'       => 'color',
				'section'    => $section,
				'css_format' => 'color: {{value}};',
				'selector'   => ".header--row:not(.header--transparent) {$selector}:hover",
				'title'      => esc_html__( 'Color Hover', 'masterclass' ),
			),
		);

		// Item Layout.
		return array_merge( $config, tophive_header_layout_settings( $this->id, $section ) );
	}

	function render() {
		$label      = sanitize_text_field( top_hive()->get_setting( 'nav_icon_text' ) );
		$show_label = top_hive()->get_setting( 'nav_icon_show_text', 'all' );
		$style      = sanitize_text_field( top_hive()->get_setting( 'nav_icon_style' ) );
		$sizes      = top_hive()->get_setting( 'nav_icon_size', 'all' );

		$classes       = array( 'menu-mobile-toggle item-button' );
		$label_classes = array( 'nav-icon--label' );
		if ( is_array( $show_label ) ) {
			foreach ( $show_label as $d => $v ) {
				if ( $v ) { // phpcs:ignore

				} else {
					$label_classes[] = 'hide-on-' . $d;
				}
			}
		}

		if ( empty( $sizes ) ) {
			$sizes = 'is-size-' . $sizes;
		}

		if ( is_string( $sizes ) ) {
			$classes[] = $sizes;
		} else {
			foreach ( $sizes as $d => $s ) {
				if ( ! is_string( $s ) ) {
					$s = 'is-size-medium';
				}

				$classes[] = 'is-size-' . $d . '-' . $s;
			}
		}

		if ( $style ) {
			$classes[] = $style;
		}
		?>
		<a class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">
			<span class="hamburger hamburger--squeeze">
				<span class="hamburger-box">
					<span class="hamburger-inner"></span>
				</span>
			</span>
			<?php
			if ( $show_label ) {
				echo '<span class="' . esc_attr( join( ' ', $label_classes ) ) . '">' . $label . '</span>';
			}
			?></a>
		<?php
	}

}

Tophive_Customize_Layout_Builder()->register_item( 'header', new Tophive_Builder_Item_Nav_Icon() );


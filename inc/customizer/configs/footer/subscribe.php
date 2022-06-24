<?php

class Tophive_Builder_Footer_Item_Subscribe {
	public $id = 'footer_subscribe';
	public $section = 'footer_subscribe';
	public $name = 'footer_subscribe';
	public $label = '';

	/**
	 * Optional construct
	 */
	function __construct() {
		$this->label = esc_html__( 'Subscribe Form', 'masterclass' );
	}

	/**
	 * Register Builder item
	 *
	 * @return array
	 */
	function item() {
		return array(
			'name'    => esc_html__( 'Subscribe Form', 'masterclass' ),
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
				'name'            => $this->name . '_styling',
				'type'            => 'styling',
				'section'         => $this->section,
				'selector'        => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget input',
				'css_format' 	  => 'styling',
				'title'           => esc_html__( 'Mail input styling', 'masterclass' ),
				'description'     => esc_html__( 'Styling for subscribe input form', 'masterclass' ),
				'fields'     => array(
					'normal_fields' => array(
						'link_color' 	=> false,
						'bg_image' 		=> false,
						'bg_cover' 		=> false,
						'bg_position' 	=> false,
						'bg_repeat' 	=> false,
						'bg_attachment' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name'       => $this->name . '_typography',
				'type'       => 'typography',
				'section'    => $this->section,
				'selector'        => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget input',
				'title'      => esc_html__( 'Subscribe form typography', 'masterclass' ),
				'css_format' => 'typography'
			),

			array(
				'name'            => $this->name . '_submit_styling',
				'type'            => 'styling',
				'section'         => $this->section,
				'selector'        => array(
					'normal' => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget a.newsletter-submit',
					'hover' => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget a.newsletter-submit:hover',
				),
				'css_format' 	  => 'styling',
				'title'           => esc_html__( 'Form submit styling', 'masterclass' ),
				'description'     => esc_html__( 'Styling for subscribe input form', 'masterclass' ),
				'fields'     => array(
					'normal_fields' => array(
						'link_color' 	=> false,
						'bg_image' 		=> false,
						'bg_cover' 		=> false,
						'bg_position' 	=> false,
						'bg_repeat' 	=> false,
						'bg_attachment' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' 	=> false,
						'bg_image' 		=> false,
						'bg_cover' 		=> false,
						'bg_position' 	=> false,
						'bg_repeat' 	=> false,
						'bg_attachment' => false,
						'bg_attachment' => false,
					),
				),
			),

			array(
				'name'       => $this->name . '_submit_typography',
				'type'       => 'typography',
				'section'    => $this->section,
				'selector'        => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget a.newsletter-submit',
				'title'      => esc_html__( 'Subscribe submit typography', 'masterclass' ),
				'css_format' => 'typography'
			),


			array(
				'name'            => $this->name . '_desc_styling',
				'type'            => 'color',
				'section'         => $this->section,
				'selector'        => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget .widget-description',
				'css_format' 	  => 'color:{{value}}',
				'title'           => esc_html__( 'Description Color', 'masterclass' ),
			),

			array(
				'name'            => $this->name . '_desc_typography',
				'type'            => 'typography',
				'section'         => $this->section,
				'selector'        => 'body .footer--row-inner .tophive-mc-mchimp-subs-widget .widget-description',
				'css_format' 	  => 'typography',
				'title'           => esc_html__( 'Description Typography', 'masterclass' ),
			),
		);

		return array_merge( $config, tophive_footer_layout_settings( $this->id, $this->section ) );
	}

}

Tophive_Customize_Layout_Builder()->register_item( 'footer', new Tophive_Builder_Footer_Item_Subscribe() );

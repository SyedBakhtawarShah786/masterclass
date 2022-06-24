<?php
class TophiveCore_Advanced_Styling_Background extends TophiveCoreModulesBasics {


	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ), 100 );
	}

	function config( $configs = array() ) {

		$config       = array(

			array(
				'name'       => 'content_background',
				'type'       => 'styling',
				'section'    => 'background',
				'title'      => __( 'Site Content Background', 'masterclass' ),
				'selector'   => array(
					'normal'            => '.site-content',
				),
				'css_format' => 'styling', // styling.
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => false,
						'link_color' => false,
						'padding'     => false,
						'margin'     => false,
						'border_heading' => false,
						'border_width' => false,
						'border_color' => false,
						'border_radius' => false,
						'box_shadow' => false,
						'border_style'  => false,
					),
					'hover_fields'  => false,
				),
			),
		);

		return array_merge( $configs, $config );

	}

}

new TophiveCore_Advanced_Styling_Background();

<?php
if ( ! function_exists( 'tophive_customizer_search_config' ) ) {
	function tophive_customizer_search_config( $configs = array() ) {

		$args = array(
			'name'     => esc_html__( 'Search Results', 'masterclass' ),
			'id'       => 'search_results',
			'selector' => '',
			'cb'       => '',
		);

		$top_panel     = 'blog_panel';
		$level_2_panel = 'section_' . $args['id'];

		$config = array(

			array(
				'name'  => $level_2_panel,
				'type'  => 'section',
				'panel' => $top_panel,
				'title' => $args['name'],
			),

			array(
				'name'            => $args['id'] . '_excerpt_type',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'default'         => 'excerpt',
				'choices'         => array(
					'custom'   => esc_html__( 'Custom', 'masterclass' ),
					'excerpt'  => esc_html__( 'Use excerpt metabox', 'masterclass' ),
					'more_tag' => esc_html__( 'Strip excerpt by more tag', 'masterclass' ),
					'content'  => esc_html__( 'Full content', 'masterclass' ),
				),
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Excerpt Type', 'masterclass' ),
			),

			array(
				'name'            => $args['id'] . '_excerpt_length',
				'type'            => 'number',
				'section'         => $level_2_panel,
				'default'         => 150,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Excerpt Length', 'masterclass' ),
				'required'        => array( $args['id'] . '_excerpt_type', '=', 'custom' ),
			),
			array(
				'name'            => $args['id'] . '_excerpt_more',
				'type'            => 'text',
				'section'         => $level_2_panel,
				'default'         => '',
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Excerpt More', 'masterclass' ),
			),

		);

		return array_merge( $configs, $config );

	}
}

add_filter( 'tophive/customizer/config', 'tophive_customizer_search_config' );

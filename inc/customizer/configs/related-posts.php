<?php
if ( ! function_exists( 'tophive_customizer_single_related_posts_config' ) ) {
	function tophive_customizer_single_related_posts_config( $configs = array() ) {

		$args = array(
			'name'     => esc_html__( 'Single Blog Post', 'masterclass' ),
			'id'       => 'single_blog_post',
			'selector' => '.entry.entry-single',
			'cb'       => 'tophive_single_post',
		);

		$top_panel     = 'blog_panel';
		$level_2_panel = 'section_' . $args['id'];

		$config = array(

			array(
				'name'    => $level_2_panel . '_h_related',
				'type'    => 'heading',
				'section' => $level_2_panel,
				'title'   => esc_html__( 'Related Posts', 'masterclass' ),
			),

			array(
				'name'            => $args['id'] . '_related_title',
				'type'            => 'text',
				'section'         => $level_2_panel,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'default'         => esc_html__( 'Related Posts', 'masterclass' ),
				'label'           => esc_html__( 'Title', 'masterclass' ),
			),

			array(
				'name'            => $args['id'] . '_related_by',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'default'         => 'tag',
				'max'             => 150,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Related posts by', 'masterclass' ),
				'choices'         => array(
					'cat' => esc_html__( 'By categories', 'masterclass' ),
					'tag' => esc_html__( 'By tags', 'masterclass' ),
				),
			),

			array(
				'name'            => $args['id'] . '_related_col',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'default'         => array(
					'desktop' => 3,
					'tablet'  => 3,
					'mobile'  => 1,
				),
				'max'             => 150,
				'device_settings' => true,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Number columns to show', 'masterclass' ),
				'choices'         => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				),
			),

			array(
				'name'            => $args['id'] . '_related_number',
				'type'            => 'text',
				'section'         => $level_2_panel,
				'selector'        => $args['selector'],
				'default'         => 3,
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Number posts to show', 'masterclass' ),
				'description'     => esc_html__( 'Enter 0 to disable related posts.', 'masterclass' ),
			),

			array(
				'name'            => $args['id'] . '_related_img_pos',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'default'         => 'top',
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Image Position', 'masterclass' ),
				'choices'         => array(
					'left'  => esc_html__( 'Left', 'masterclass' ),
					'right' => esc_html__( 'Right', 'masterclass' ),
					'top'   => esc_html__( 'Top', 'masterclass' ),
				),
			),

			array(
				'name'            => $args['id'] . '_related_thumbnail_size',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'default'         => 'medium',
				'label'           => esc_html__( 'Thumbnail Size', 'masterclass' ),
				'choices'         => tophive_get_all_image_sizes(),
			),

			array(
				'name'            => $args['id'] . '_related_thumbnail_height',
				'type'            => 'slider',
				'section'         => $level_2_panel,
				'selector'        => 'format',
				'unit'            => '%',
				'max'             => '200',
				'default'         => '',
				'label'           => esc_html__( 'Custom Thumbnail Height', 'masterclass' ),
				'device_settings' => true,
				'devices'         => array( 'desktop', 'tablet' ),
				'css_format'      => '.related-post .related-thumbnail a { padding-top: {{value_no_unit}}%; } .related-post .related-thumbnail img { width: 100%;position: absolute; top: 0px; right: 0px; display: block; height: 100%; object-fit: cover; }',
			),

			array(
				'name'            => $args['id'] . '_related_thumbnail_width',
				'type'            => 'slider',
				'section'         => $level_2_panel,
				'selector'        => 'format',
				'unit'            => '%',
				'max'             => '100',
				'default'         => '',
				'label'           => esc_html__( 'Custom Thumbnail Width', 'masterclass' ),
				'device_settings' => true,
				'devices'         => array( 'desktop', 'tablet' ),
				'css_format'      => '.img-pos-left .related-thumbnail, .img-pos-right .related-thumbnail { flex-basis: {{value_no_unit}}%; } .img-pos-left .related-body, .img-pos-right .related-body { flex-basis: calc( 100% - {{value_no_unit}}% ); }',
				'required'        => array( $args['id'] . '_related_img_pos', 'in', array( 'left', 'right' ) ),
			),

			array(
				'name'            => $args['id'] . '_related_orderby',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Order by', 'masterclass' ),
				'default'         => 'date',
				'choices'         => array(
					'date'          => esc_html__( 'Date', 'masterclass' ),
					'title'         => esc_html__( 'Title', 'masterclass' ),
					'menu_order'    => esc_html__( 'Post order', 'masterclass' ),
					'rand'          => esc_html__( 'Random', 'masterclass' ),
					'comment_count' => esc_html__( 'Comment count', 'masterclass' ),
				),
			),

			array(
				'name'            => $args['id'] . '_related_order',
				'type'            => 'select',
				'section'         => $level_2_panel,
				'selector'        => $args['selector'],
				'render_callback' => $args['cb'],
				'default'         => 'desc',
				'label'           => esc_html__( 'Order', 'masterclass' ),
				'choices'         => array(
					'desc' => esc_html__( 'Desc', 'masterclass' ),
					'asc'  => esc_html__( 'Asc', 'masterclass' ),
				),
			),

			array(
				'name'             => $args['id'] . '_related_meta',
				'section'          => $level_2_panel,
				'type'             => 'repeater',
				'label'            => esc_html__( 'Related post meta', 'masterclass' ),
				'live_title_field' => 'title',
				'limit'            => 4,
				'addable'          => false,
				'title_only'       => true,
				'selector'         => $args['selector'],
				'render_callback'  => $args['cb'],
				'default'          => array(
					array(
						'_key'        => 'author',
						'title'       => esc_html__( 'Author', 'masterclass' ),
						'_visibility' => 'hidden',
					),
					array(
						'_key'  => 'date',
						'title' => esc_html__( 'Date', 'masterclass' ),
					),
					array(
						'_key'        => 'categories',
						'title'       => esc_html__( 'Categories', 'masterclass' ),
						'_visibility' => 'hidden',
					),
					array(
						'_key'        => 'comment',
						'title'       => esc_html__( 'Comment', 'masterclass' ),
						'_visibility' => 'hidden',
					),

				),
				'fields'           => array(
					array(
						'name' => '_key',
						'type' => 'hidden',
					),
					array(
						'name'  => 'title',
						'type'  => 'hidden',
						'label' => esc_html__( 'Title', 'masterclass' ),
					),
				),
			),

			array(
				'name'            => $args['id'] . '_related_excerpt_length',
				'type'            => 'text',
				'section'         => $level_2_panel,
				'selector'        => $args['selector'],
				'default'         => 0,
				'render_callback' => $args['cb'],
				'label'           => esc_html__( 'Excerpt length', 'masterclass' ),
				'description'     => esc_html__( 'Custom excerpt length. Enter 0 to hide the excerpt.', 'masterclass' ),
			),

		);

		return array_merge( $configs, $config );

	}
}

add_filter( 'tophive/customizer/config', 'tophive_customizer_single_related_posts_config', 399 );


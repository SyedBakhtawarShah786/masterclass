<?php

class Tophive_Page_Header {
	public $name = null;
	public $description = null;
	static $is_transparent = null;
	static $_instance = null;
	static $_settings = null;

	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ) );
		if ( ! is_admin() ) {
			add_action( 'tophive_is_post_title_display', array( $this, 'display_page_title' ), 35 );
			add_action( 'tophive/breadcrumb-start', array( $this, 'render' ), 35 );
			add_action( 'wp', array( $this, 'wp' ), 85 );
		}
		self::$_instance = $this;
	}

	function wp() {
		$this->get_settings();
	}

	static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	function config( $configs = array() ) {
		$section      = 'page_header';
		$name         = 'page_header';
		$choices      = array(
			'default'  => esc_html__( 'Default', 'masterclass' ),
			'cover'    => esc_html__( 'Cover Header', 'masterclass' ),
			'titlebar' => esc_html__( 'Titlebar', 'masterclass' ),
			'none'     => esc_html__( 'Hide', 'masterclass' ),
		);
		$render_cb_el = array( $this, 'render' );

		$display_fields = array(
			array(
				'name'        => 'page',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on single page', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing single page', 'masterclass' ),
				'default'     => 'titlebar',
				'choices'     => $choices,
			),
			array(
				'name'        => 'post',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on single post', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing single post', 'masterclass' ),
				'default'     => '',
				'choices'     => $choices,
			),

			array(
				'name'        => 'category',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on categories', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing a category page', 'masterclass' ),
				'default'     => '',
				'choices'     => $choices,
			),
			array(
				'name'        => 'index',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on index', 'masterclass' ),
				'description' => esc_html__( 'Apply when your homepage displays as latest posts', 'masterclass' ),
				'default'     => '',
				'choices'     => $choices,
			),
			array(
				'name'        => 'search',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on search', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing search results page', 'masterclass' ),
				'default'     => '',
				'choices'     => $choices,
			),
			array(
				'name'        => 'archive',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on archive', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing archive pages, e.g. Tag, Author, Date, Custom Post Type or Custom Taxonomy', 'masterclass' ),
				'default'     => '',
				'choices'     => $choices,
			),
			array(
				'name'        => 'page_404',
				'type'        => 'select',
				'label'       => esc_html__( 'Display on 404 page', 'masterclass' ),
				'description' => esc_html__( 'Apply when the page not found', 'masterclass' ),
				'default'     => '',
				'choices'     => $choices,
			),

		);

		$title_fields = array(
			array(
				'name'        => 'index',
				'type'        => 'text',
				'label'       => esc_html__( 'Title for index page', 'masterclass' ),
				'description' => esc_html__( 'Apply when your homepage displays as latest posts', 'masterclass' ),
				'default'     => '',
			),
			array(
				'name'        => 'post',
				'type'        => 'text',
				'label'       => esc_html__( 'Title for single post', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing single post', 'masterclass' ),
				'default'     => '',
			),
			array(
				'name'        => 'page_404',
				'type'        => 'text',
				'label'       => esc_html__( 'Title for 404 page', 'masterclass' ),
				'description' => esc_html__( 'Apply when the page not found', 'masterclass' ),
				'default'     => '',
			),
		);

		$tagline_fields = array(
			array(
				'name'        => 'index',
				'type'        => 'textarea',
				'label'       => esc_html__( 'Tagline for index page', 'masterclass' ),
				'description' => esc_html__( 'Apply when your homepage displays as latest posts', 'masterclass' ),
				'default'     => '',
			),
			array(
				'name'        => 'post',
				'type'        => 'textarea',
				'label'       => esc_html__( 'Tagline for single post', 'masterclass' ),
				'description' => esc_html__( 'Apply when viewing single post', 'masterclass' ),
				'default'     => '',
			),
			array(
				'name'        => 'page_404',
				'type'        => 'textarea',
				'label'       => esc_html__( 'Tagline for 404 page', 'masterclass' ),
				'description' => esc_html__( 'Apply when the page not found', 'masterclass' ),
				'default'     => '',
			),
		);

		$post_types = top_hive()->get_post_types( false );
		if ( count( $post_types ) > 0 ) {
			foreach ( $post_types as $pt => $label ) {
				$display_fields[] = array(
					'name'        => $pt,
					'type'        => 'select',
					'label'       => sprintf( esc_html__( 'Display on %s page', 'masterclass' ), $label['singular_name'] ),
					'description' => sprintf( esc_html__( 'Apply when viewing single %s', 'masterclass' ), $label['singular_name'] ),
					'default'     => '',
					'choices'     => $choices,
				);

				$taxonomy_filter_args = [
					'show_in_nav_menus' => true,
				];

				$taxonomy_filter_args['object_type'] = [ $pt ];
				$taxonomies                          = get_taxonomies( $taxonomy_filter_args, 'objects' );
				$options                             = array();

				foreach ( $taxonomies as $taxonomy => $object ) {
					$options[ $taxonomy ] = $object->label;
					$display_fields[]     = array(
						'name'        => $taxonomy,
						'type'        => 'select',
						'label'       => sprintf( esc_html__( 'Display on %1$s %2$s', 'masterclass' ), $label['singular_name'], $object->labels->singular_name ),
						'description' => sprintf( esc_html__( 'Apply when viewing %1$s %2$s', 'masterclass' ), $label['singular_name'], $object->labels->singular_name ),
						'default'     => '',
						'choices'     => $choices,
					);
				}

				$title_fields[] = array(
					'name'        => $pt,
					'type'        => 'text',
					'label'       => sprintf( esc_html__( 'Title for %s', 'masterclass' ), $label['singular_name'] ),
					'description' => sprintf( esc_html__( 'Apply when viewing single %s', 'masterclass' ), $label['singular_name'] ),
					'default'     => '',
				);

				$tagline_fields[] = array(
					'name'        => $pt,
					'type'        => 'textarea',
					'label'       => sprintf( esc_html__( 'Tagline for %s', 'masterclass' ), $label['singular_name'] ),
					'description' => sprintf( esc_html__( 'Apply when viewing single %s', 'masterclass' ), $label['singular_name'] ),
					'default'     => '',
				);
			}
		}

		$config = array(
			array(
				'name'  => $section,
				'type'  => 'section',
				'panel' => 'layout_panel',
				'title' => esc_html__( 'Page Header', 'masterclass' ),
			),

			array(
				'name'       => $section . '_layout',
				'type'       => 'select',
				'section'    => $section,
				'title'      => esc_html__( 'Layout', 'masterclass' ),
				'selector'   => '.page-header--item',
				'css_format' => 'html_class',
				'default'    => '',
				'choices'    => array(
					''                      => esc_html__( 'Default', 'masterclass' ),
					'layout-full-contained' => esc_html__( 'Full width - Contained', 'masterclass' ),
					'layout-fullwidth'      => esc_html__( 'Full Width', 'masterclass' ),
					'layout-contained'      => esc_html__( 'Contained', 'masterclass' ),
				),
			),

			array(
				'name'    => "{$name}_display_h",
				'type'    => 'heading',
				'section' => $section,
				'title'   => esc_html__( 'Display Settings', 'masterclass' ),
			),

			array(
				'name'        => "{$name}_display",
				'type'        => 'modal',
				'section'     => $section,
				'label'       => esc_html__( 'Display', 'masterclass' ),
				'description' => esc_html__( 'Settings display for special pages.', 'masterclass' ),
				'default'     => array(
					'display' => array(
						'page'     => 'titlebar',
						'archive'  => 'titlebar',
						'category' => 'titlebar',
					),
				),
				'fields'      => array(
					'tabs'            => array(
						'display'  => esc_html__( 'Display', 'masterclass' ),
						'advanced' => esc_html__( 'Advanced', 'masterclass' ),
					),
					'display_fields'  => $display_fields,
					'advanced_fields' => array(
						array(
							'name'        => 'post_bg',
							'type'        => 'select',
							'label'       => esc_html__( 'Post Header Background Cover', 'masterclass' ),
							'description' => esc_html__( 'Apply when viewing single post and page header setting displays as cover.', 'masterclass' ),
							'default'     => '',
							'choices'     => array(
								'default'   => esc_html__( 'Default', 'masterclass' ),
								'blog_page' => esc_html__( 'Use featured image from blog page', 'masterclass' ),
								'featured'  => esc_html__( 'Use featured image of current post', 'masterclass' ),
							),
						),
						array(
							'name'    => 'post_title_tagline',
							'type'    => 'select',
							'label'   => esc_html__( 'Single Post Title & Tagline', 'masterclass' ),
							'default' => '',
							'choices' => array(
								'default'   => esc_html__( 'Default', 'masterclass' ),
								'blog_page' => esc_html__( 'Use title & tagline from blog page', 'masterclass' ),
								'current'   => esc_html__( 'Use title & tagline of current post', 'masterclass' ),
							),
						),
					),
				),
			),

			array(
				'name'            => "{$name}_title_tagline",
				'type'            => 'modal',
				'section'         => $section,
				'label'           => esc_html__( 'Title & Tagline', 'masterclass' ),
				'description'     => esc_html__( 'Title & tagline for special pages.', 'masterclass' ),
				'default'         => array(),
				'fields'          => array(
					'tabs'            => array(
						'titles'   => esc_html__( 'Title', 'masterclass' ),
						'taglines' => esc_html__( 'Tagline', 'masterclass' ),
					),
					'titles_fields'   => $title_fields,
					'taglines_fields' => $tagline_fields,
				),
				'selector'        => '#page-titlebar, #page-cover',
				'render_callback' => $render_cb_el,
			),

			array(
				'name'            => $name . '_show_archive_prefix',
				'type'            => 'checkbox',
				'section'         => $section,
				'title'           => esc_html__( 'Archive Prefix', 'masterclass' ),
				'description'     => esc_html__( 'Enable or disable archive prefix on category, date, tag page.', 'masterclass' ),
				'checkbox_label'  => esc_html__( 'Enable', 'masterclass' ),
				'default'         => 1,
				'selector'        => '#page-titlebar, #page-cover',
				'render_callback' => $render_cb_el,
			),

		);

		$configs = array_merge( $configs, $config );
		$configs = array_merge( $configs, $this->config_cover() );
		$configs = array_merge( $configs, $this->config_titlebar() );

		return $configs;
	}

	function config_titlebar() {

		$section      = 'page_header';
		$render_cb_el = array( $this, 'render' );
		$selector     = '#page-titlebar';
		$name         = 'titlebar';
		$config       = array(

			array(
				'name'    => "{$name}_styling_h_tb",
				'type'    => 'heading',
				'section' => 'page_header',
				'title'   => esc_html__( 'Titlebar Settings', 'masterclass' ),
			),

			array(
				'name'           => $name . '_show_title',
				'type'           => 'checkbox',
				'section'        => $section,
				'label'          => esc_html__( 'Show Title', 'masterclass' ),
				'description'    => esc_html__( 'Title is pull from post title, archive title.', 'masterclass' ),
				'checkbox_label' => esc_html__( 'Enable', 'masterclass' ),
				'default'        => 1,
			),

			array(
				'name'           => $name . '_show_tagline',
				'type'           => 'checkbox',
				'section'        => $section,
				'label'          => esc_html__( 'Show Tagline', 'masterclass' ),
				'description'    => esc_html__( 'Tagline is pull from post excerpt, archive description.', 'masterclass' ),
				'checkbox_label' => esc_html__( 'Enable', 'masterclass' ),
				'default'        => 1,
			),
			array(
				'name'            => "{$name}_align",
				'type'            => 'text_align_no_justify',
				'section'         => $section,
				'device_settings' => true,
				'selector'        => "$selector",
				'css_format'      => 'text-align: {{value}};',
				'title'           => esc_html__( 'Text Align', 'masterclass' ),
			),

		);

		$config = apply_filters( 'tophive/titlebar/config', $config, $this );

		return $config;
	}

	function config_cover() {

		$section      = 'page_header';
		$render_cb_el = array( $this, 'render' );
		$selector     = '#page-cover';
		$name         = 'header_cover';
		$config       = array(

			array(
				'name'    => "{$name}_settings_h",
				'type'    => 'heading',
				'section' => $section,
				'title'   => esc_html__( 'Cover Settings', 'masterclass' ),
			),

			array(
				'name'           => $name . '_show_title',
				'type'           => 'checkbox',
				'section'        => $section,
				'label'          => esc_html__( 'Show Title', 'masterclass' ),
				'description'    => esc_html__( 'Title is pull from post title, archive title.', 'masterclass' ),
				'checkbox_label' => esc_html__( 'Enable', 'masterclass' ),
				'default'        => 1,
			),

			array(
				'name'           => $name . '_show_tagline',
				'type'           => 'checkbox',
				'section'        => $section,
				'label'          => esc_html__( 'Show Tagline', 'masterclass' ),
				'description'    => esc_html__( 'Tagline is pull from post excerpt, archive description.', 'masterclass' ),
				'checkbox_label' => esc_html__( 'Enable', 'masterclass' ),
				'default'        => 1,
			),

			array(
				'name'       => $name . '_bg',
				'type'       => 'modal',
				'section'    => $section,
				'title'      => esc_html__( 'Cover Background', 'masterclass' ),
				'selector'   => $selector,
				'css_format' => 'styling', // Styling.
				'default'    => array(
					'normal' => array(
						'bg_image' => array(
							'id'  => '',
							'url' => esc_url( get_template_directory_uri() ) . '/assets/images/default-cover.jpg',
						),
					),
				),
				'fields'     => array(
					'tabs'          => array(
						'normal' => '_',
					),
					'normal_fields' => array(
						array(
							'name'       => 'bg_image',
							'type'       => 'image',
							'label'      => esc_html__( 'Background Image', 'masterclass' ),
							'selector'   => "$selector",
							'css_format' => 'background-image: url("{{value}}");',
						),
						array(
							'name'       => 'bg_cover',
							'type'       => 'select',
							'choices'    => array(
								''        => esc_html__( 'Default', 'masterclass' ),
								'auto'    => esc_html__( 'Auto', 'masterclass' ),
								'cover'   => esc_html__( 'Cover', 'masterclass' ),
								'contain' => esc_html__( 'Contain', 'masterclass' ),
							),
							'required'   => array( 'bg_image', 'not_empty', '' ),
							'label'      => esc_html__( 'Size', 'masterclass' ),
							'class'      => 'field-half-left',
							'selector'   => "$selector",
							'css_format' => '-webkit-background-size: {{value}}; -moz-background-size: {{value}}; -o-background-size: {{value}}; background-size: {{value}};',
						),
						array(
							'name'       => 'bg_position',
							'type'       => 'select',
							'label'      => esc_html__( 'Position', 'masterclass' ),
							'required'   => array( 'bg_image', 'not_empty', '' ),
							'class'      => 'field-half-right',
							'choices'    => array(
								''              => esc_html__( 'Default', 'masterclass' ),
								'center'        => esc_html__( 'Center', 'masterclass' ),
								'top left'      => esc_html__( 'Top Left', 'masterclass' ),
								'top right'     => esc_html__( 'Top Right', 'masterclass' ),
								'top center'    => esc_html__( 'Top Center', 'masterclass' ),
								'bottom left'   => esc_html__( 'Bottom Left', 'masterclass' ),
								'bottom center' => esc_html__( 'Bottom Center', 'masterclass' ),
								'bottom right'  => esc_html__( 'Bottom Right', 'masterclass' ),
							),
							'selector'   => "$selector",
							'css_format' => 'background-position: {{value}};',
						),
						array(
							'name'       => 'bg_repeat',
							'type'       => 'select',
							'label'      => esc_html__( 'Repeat', 'masterclass' ),
							'class'      => 'field-half-left',
							'required'   => array(
								array( 'bg_image', 'not_empty', '' ),
							),
							'choices'    => array(
								'repeat'    => esc_html__( 'Default', 'masterclass' ),
								'no-repeat' => esc_html__( 'No repeat', 'masterclass' ),
								'repeat-x'  => esc_html__( 'Repeat horizontal', 'masterclass' ),
								'repeat-y'  => esc_html__( 'Repeat vertical', 'masterclass' ),
							),
							'selector'   => "$selector",
							'css_format' => 'background-repeat: {{value}};',
						),

						array(
							'name'       => 'bg_attachment',
							'type'       => 'select',
							'label'      => esc_html__( 'Attachment', 'masterclass' ),
							'class'      => 'field-half-right',
							'required'   => array(
								array( 'bg_image', 'not_empty', '' ),
							),
							'choices'    => array(
								''       => esc_html__( 'Default', 'masterclass' ),
								'scroll' => esc_html__( 'Scroll', 'masterclass' ),
								'fixed'  => esc_html__( 'Fixed', 'masterclass' ),
							),
							'selector'   => "$selector",
							'css_format' => 'background-attachment: {{value}};',
						),

						array(
							'name'            => 'overlay',
							'type'            => 'color',
							'section'         => $section,
							'class'           => 'tophive--clear',
							'device_settings' => false,
							'selector'        => "$selector:before",
							'label'           => esc_html__( 'Cover Overlay', 'masterclass' ),
							'css_format'      => 'background-color: {{value}};',
						),

					),
					'hover_fields'  => false,
				),
			),

			array(
				'name'            => "{$name}_align",
				'type'            => 'text_align_no_justify',
				'section'         => $section,
				'device_settings' => true,
				'selector'        => "$selector",
				'css_format'      => 'text-align: {{value}};',
				'title'           => esc_html__( 'Cover Text Align', 'masterclass' ),
			),
			array(
				'name'           => $name . '_show_breadcrumb',
				'type'           => 'checkbox',
				'section'        => $section,
				'label'          => esc_html__( 'Show Breadcrumb', 'masterclass' ),
				'checkbox_label' => esc_html__( 'Enable', 'masterclass' ),
				'default'        => 1,
			),

			array(
				'name'            => "{$name}_height",
				'type'            => 'slider',
				'section'         => $section,
				'device_settings' => true,
				'max'             => 1000,
				'title'           => esc_html__( 'Cover Height', 'masterclass' ),
				'selector'        => "{$selector} .page-cover-inner",
				'css_format'      => 'min-height: {{value}};',
				'default'         => array(
					'desktop' => array(
						'value' => '300',
					),
					'tablet'  => array(
						'value' => '250',
					),
					'mobile'  => array(
						'value' => '200',
					),
				),
			),

			array(
				'name'            => "{$name}_align",
				'type'            => 'text_align_no_justify',
				'section'         => $section,
				'device_settings' => true,
				'selector'        => "$selector",
				'css_format'      => 'text-align: {{value}};',
				'title'           => esc_html__( 'Cover Text Align', 'masterclass' ),
			),

		);
		$config       = apply_filters( 'tophive/cover/config', $config, $this );

		return $config;
	}

	function get_settings() {

		if ( ! is_null( self::$_settings ) ) {
			return self::$_settings;
		}

		$args = array(
			'_page'                      => 'index',
			'display'                    => 'default',
			'title'                      => '',
			'tagline'                    => '',
			'image'                      => '',
			'title_tag'                  => 'h1',
			'force_display_single_title' => '', // Show || or hide.
			'show_title'                 => false, // force show post title.
			'shortcode'                  => false, // force show post title.
			'cover_tagline'              => 1, // Display tagline in cover.
			'cover_breadcrumb'           => 1, // Display Breadcrumb in cover.
			'titlebar_tagline'           => 1, // Display tagline in titlbar.
		);
		$name = 'page_header';

		$display  = top_hive()->get_setting_tab( $name . '_display', 'display' );
		$advanced = top_hive()->get_setting_tab( $name . '_display', 'advanced' );

		$titles   = top_hive()->get_setting_tab( $name . '_title_tagline', 'titles' );
		$taglines = top_hive()->get_setting_tab( $name . '_title_tagline', 'taglines' );

		$args['cover_tagline']    = top_hive()->get_setting( 'header_cover_show_tagline' );
		$args['titlebar_tagline'] = top_hive()->get_setting( 'titlebar_show_tagline' );

		$display = wp_parse_args(
			$display,
			array(
				'index'       => '',
				'category'    => '',
				'search'      => '',
				'archive'     => '',
				'page'        => '',
				'post'        => '',
				'singular'    => '',
				'product'     => '',
				'product_cat' => '',
				'product_tag' => '',
				'page_404'    => '',
			)
		);

		$advanced = wp_parse_args(
			$advanced,
			array(
				'post_bg'            => '',
				'post_title_tagline' => '',
			)
		);

		$titles = wp_parse_args(
			$titles,
			array(
				'index'    => '',
				'post'     => '',
				'product'  => '',
				'page_404' => '',
			)
		);

		$taglines = wp_parse_args(
			$taglines,
			array(
				'index'    => '',
				'post'     => '',
				'product'  => '',
				'page_404' => '',
			)
		);

		$post_thumbnail_id = false;

		$post_id = 0;
		if ( is_front_page() && is_home() ) { // index page.
			// Default homepage.
			$args['display'] = $display['index'];
			$args['title']   = $titles['index'];
			$args['tagline'] = $taglines['index'];
			$args['_page']   = 'index';
		} elseif ( is_front_page() ) {
			// static homepage.
			$args['display'] = $display['page'];
			$post_id         = get_the_ID();
			$args['_page']   = 'page';
		} elseif ( is_home() ) {
			// blog page.
			$args['display'] = $display['page'];
			$post_id         = get_option( 'page_for_posts' );
			$args['_page']   = 'page';
		} elseif ( is_category() ) {
			// category.
			$args['display'] = $display['category'];
			$args['title']   = get_the_archive_title();
			$args['tagline'] = get_the_archive_description();
			$args['_page']   = 'category';
			$post_id         = 0;
		} elseif ( is_page() ) {
			// single page.
			$args['display'] = $display['page'];
			$post_id         = get_the_ID();
			$args['_page']   = 'page';
		} elseif ( is_singular( 'post' ) ) {
			// single post.
			$args['display']   = $display['post'];
			$args['title_tag'] = 'h2';

			// Setup single post bg for cover.
			if ( 'blog_page' == $advanced['post_bg'] ) {
				$post_id           = get_option( 'page_for_posts' );
				$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			} elseif ( 'featured' == $advanced['post_bg'] ) {
				$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
			} else {
				$post_id = get_option( 'page_for_posts' );
				if ( $post_id ) {
					$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
				}
			}

			if ( 'none' != $args['display'] ) {
				if ( 'blog_page' == $advanced['post_title_tagline'] ) {
					$post_id                            = get_option( 'page_for_posts' );
					$args['force_display_single_title'] = 'show';
				} elseif ( 'current' == $advanced['post_title_tagline'] ) {
					$post_id = get_the_ID();
					if ( 'default' != $args['display'] ) {
						$args['force_display_single_title'] = 'hide';
					} else {
						$args['force_display_single_title'] = 'show';
					}
					$args['title_tag'] = 'h1';
				} else {
					$post_id                            = get_option( 'page_for_posts' );
					$args['force_display_single_title'] = 'show';
					if ( ! $post_id ) {
						$args['force_display_single_title'] = 'show';
						if ( $titles['post'] || $taglines['post'] ) {
							$args['title']   = $titles['post'];
							$args['tagline'] = $taglines['post'];
						}
					}
				}
			}

			$args['_page'] = 'post';
		} elseif ( is_singular() ) {
			// single custom post type.
			$post_id   = get_the_ID();
			$post_type = get_post_type();
			if ( isset( $display[ $post_type ] ) ) {
				$args['display'] = $display[ $post_type ];
				$args['_page']   = 'singular_' . $post_type;
			} elseif ( isset( $display['singular'] ) ) {
				$args['display'] = $display['singular'];
				$args['_page']   = 'singular';
			}
		} elseif ( is_404() ) {
			// page not found.
			$args['display'] = $display['page_404'];
			$args['_page']   = '404';
			$args['title']   = $titles['page_404'];
			$args['tagline'] = $taglines['page_404'];
			if ( ! $args['title'] ) {
				$args['title'] = esc_html__( "Oops! That page can't be found.", 'masterclass' );
			}
		} elseif ( is_search() ) {
			// Search result.
			$args['display'] = $display['search'];
			$args['title']   = sprintf( // WPCS: XSS ok.
				/* translators: 1: Search query name */
				__( 'Search Results for: %s', 'masterclass' ),
				'<span>' . get_search_query() . '</span>'
			);
			$args['tagline'] = '';
			$args['_page']   = 'search';
			$post_id         = 0;
		} elseif ( is_archive() ) {
			$args['display'] = $display['archive'];
			$args['title']   = get_the_archive_title();
			$args['tagline'] = get_the_archive_description();
			$args['_page']   = 'archive';
			$post_id         = 0;
		}

		if ( is_tax() ) {
			$queried_object = get_queried_object();
			if ( isset( $display[ $queried_object->taxonomy ] ) ) {
				$args['display'] = $display['product_tag'];
			}
			if ( isset( $titles[ $queried_object->taxonomy ] ) ) {
				$args['display'] = $titles[ $queried_object->taxonomy ];
			}
			if ( isset( $taglines[ $queried_object->taxonomy ] ) ) {
				$args['tagline'] = $taglines[ $queried_object->taxonomy ];
			}
			$args['_page'] = 'tax_' . $queried_object->taxonomy;
		}

		// WooCommerce Settings.
		if ( top_hive()->is_woocommerce_active() ) {
			if ( is_product() ) {
				$post_id         = wc_get_page_id( 'shop' );
				$args['display'] = $display['product'];
				$args['title']   = $titles['product'];
				$args['tagline'] = $taglines['product'];
				$args['_page']   = 'product';
				if ( $args['title'] || $args['tagline'] ) {
					$post_id = 0;
				}
			} elseif ( is_product_category() ) {
				$post_id         = 0;
				$args['display'] = $display['product_cat'];
				$args['title']   = get_the_archive_title();
				$args['tagline'] = get_the_archive_description();
				$args['_page']   = 'product_cat';
			} elseif ( is_product_tag() ) {
				$post_id         = 0;
				$args['display'] = $display['product_tag'];
				$args['title']   = get_the_archive_title();
				$args['tagline'] = get_the_archive_description();
				$args['_page']   = 'product_tag';
			} elseif ( is_shop() && ! is_search() ) {
				$args['display'] = $display['page'];
				$post_id         = wc_get_page_id( 'shop' );
				$args['_page']   = 'shop';
				$args['tagline'] = '';
			}
		}

		if ( $post_id > 0 ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$args['title'] = get_the_title( $post_id );
				if ( $post->post_excerpt ) {
					$args['tagline'] = get_the_excerpt( $post );
				}
				if ( ! $post_thumbnail_id ) {
					$post_thumbnail_id = get_post_thumbnail_id( $post_id );
				}
			}
		}

		if ( ! $args['image'] && $post_thumbnail_id ) {
			$_i = top_hive()->get_media( $post_thumbnail_id );
			if ( $_i ) {
				$args['image'] = $_i;
			}
		}

		if ( top_hive()->is_using_post() ) {
			$post_id = top_hive()->get_current_post_id();

			// If Disable page title.
			$disable = get_post_meta( $post_id, '_tophive_disable_page_title', true );
			if ( $disable ) {
				$args['force_display_single_title'] = 'hide';
			}

			// If has custom field custom title.
			$post_display = get_post_meta( $post_id, '_tophive_page_header_display', true );
			if ( $post_display && 'default' != $post_display ) {
				if ( 'normal' == $post_display ) {
					$args['display'] = 'default';
				} else {
					$args['display'] = $post_display;
				}
			}

			// If has custom field custom title.
			$title = get_post_meta( $post_id, '_tophive_page_header_title', true );
			if ( $title ) {
				$args['title'] = $title;
			}

			// If has custom field custom tagline.
			$tagline = trim( get_post_meta( $post_id, '_tophive_page_header_tagline', true ) );
			if ( $tagline ) {
				$args['tagline'] = $tagline;
			}

			// If has custom field header media.
			$media = get_post_meta( $post_id, '_tophive_page_header_image', true );
			if ( ! empty( $media ) ) {
				$image = top_hive()->get_media( $media );
				if ( $image ) {
					$args['image'] = $image;
				}
			}

			// Has custom shortcode.
			$args['shortcode'] = trim( get_post_meta( $post_id, '_tophive_page_header_shortcode', true ) );
			if ( $args['shortcode'] ) {
				$args['display'] = 'shortcode';
			}
		}

		if ( ! $args['display'] ) {
			$args['display'] = 'default';
		}

		self::$_settings = apply_filters( 'tophive/page-header/get-settings', $args );

		return $args;
	}

	function display_page_title( $show ) {
		$args = $this->get_settings();
		if ( ! $args['display'] || 'default' == $args['display'] ) {
			$show = true;
		} elseif ( 'cover' == $args['display'] || 'titlebar' == $args['display'] || 'none' == $args['display'] ) {
			$show = false;
		}
		if ( 'hide' == $args['force_display_single_title'] ) {
			$show = false;
		} elseif ( 'show' == $args['force_display_single_title'] ) {
			$show = true;
		}

		return $show;
	}

	function render_cover( $args = array() ) {
		$args = $this->get_settings();
		extract( $args, EXTR_SKIP ); // phpcs:ignore

		$style = '';
		if ( $args['image'] ) {
			$style = ' style="background-image: url(\'' . esc_url( $args['image'] ) . '\')" ';
		}

		if ( ! $args['title_tag'] ) {
			$args['title_tag'] = 'h2';
		}

		$layout    = top_hive()->get_setting_tab( 'page_header_layout' );
		$classes   = array( 'page-header--item page-cover' );
		$classes[] = $layout;
		$show_cover = top_hive()->get_setting( 'single_blog_post_page_cover' );
		$show_cover_woo = top_hive()->get_setting( 'single_product_page_cover' );
		if(!$show_cover && is_singular( $post_types = 'post' )){
			return;
		}
		if(!$show_cover_woo && is_singular( $post_types = 'product' )){
			return;
		}
		?>
		<div id="page-cover" class="<?php echo esc_attr( join( ' ', $classes ) ); ?>"<?php echo tophive_sanitize_filter($style); ?>>
			<div class="page-cover-inner tophive-container">
				<?php
				do_action( 'tophive/page-cover/before' );

				if ( top_hive()->get_setting( 'header_cover_show_title' ) ) {
					if ( $args['title'] ) {
						// WPCS: XSS ok.
						echo '<' . $args['title_tag'] . ' class="page-cover-title">' . apply_filters( 'tophive_the_title', wp_kses_post( $args['title'] ) ) . '</' . $args['title_tag'] . '>';
					}
				}
				if ( $args['cover_tagline'] ) {
					if ( $args['tagline'] ) {
						// WPCS: XSS ok.
						echo '<div class="page-cover-tagline-wrapper"><div class="page-cover-tagline">' . apply_filters( 'tophive_the_title', wp_kses_post( $args['tagline'] ) ) . '</div></div>';
					}
				}

				if( top_hive()->get_setting( 'header_cover_show_breadcrumb' ) ){
					echo tophive_sanitize_filter($this->render_breadcrumbs());
				}

				do_action( 'tophive/page-cover/after' );
				?>
			</div>
		</div>
		<?php
	}
	public function render_breadcrumbs()
	{   
	    // Set variables for later use
	    $here_text        = esc_html__( 'You are here', 'masterclass' );
	    $home_link        = home_url('/');
	    $home_text        = esc_html__( 'Home', 'masterclass' );
	    $link_before      = '<span>';
	    $link_after       = '</span>';
	    $link_attr        = '';
	    $link             = $link_before . '<a' . $link_attr . ' href="%1$s">%2$s</a>' . $link_after;
	    $delimiter        = ' &raquo; ';              // Delimiter between crumbs
	    $before           = '<span class="current">'; // Tag before the current crumb
	    $after            = '</span>';                // Tag after the current crumb
	    $page_addon       = '';                       // Adds the page number if the query is paged
	    $breadcrumb_trail = '';
	    $category_links   = '';

	    /** 
	     * Set our own $wp_the_query variable. Do not use the global variable version due to 
	     * reliability
	     */
	    $wp_the_query   = $GLOBALS['wp_the_query'];
	    $queried_object = $wp_the_query->get_queried_object();

	    // Handle single post requests which includes single pages, posts and attatchments
	    if ( is_singular() ) 
	    {
	        /** 
	         * Set our own $post variable. Do not use the global variable version due to 
	         * reliability. We will set $post_object variable to $GLOBALS['wp_the_query']
	         */
	        $post_object = sanitize_post( $queried_object );

	        // Set variables 
	        $title          = apply_filters( 'the_title', $post_object->post_title );
	        $parent         = $post_object->post_parent;
	        $post_type      = $post_object->post_type;
	        $post_id        = $post_object->ID;
	        $post_link      = $before . $title . $after;
	        $parent_string  = '';
	        $post_type_link = '';

	        if ( 'post' === $post_type ) 
	        {
	            // Get the post categories
	            $categories = get_the_category( $post_id );
	            if ( $categories ) {
	                // Lets grab the first category
	                $category  = $categories[0];

	                $category_links = get_category_parents( $category, true, $delimiter );
	                $category_links = str_replace( '<a',   $link_before . '<a' , $category_links );
	                $category_links = str_replace( '</a>', '</a>' . $link_after,             $category_links );
	            }
	        }

	        if ( !in_array( $post_type, ['post', 'page', 'attachment'] ) )
	        {
	            $post_type_object = get_post_type_object( $post_type );
	            $archive_link     = esc_url( get_post_type_archive_link( $post_type ) );

	            $post_type_link   = sprintf( $link, $archive_link, $post_type_object->labels->singular_name );
	        }

	        // Get post parents if $parent !== 0
	        if ( 0 !== $parent ) 
	        {
	            $parent_links = [];
	            while ( $parent ) {
	                $post_parent = get_post( $parent );

	                $parent_links[] = sprintf( $link, esc_url( get_permalink( $post_parent->ID ) ), get_the_title( $post_parent->ID ) );

	                $parent = $post_parent->post_parent;
	            }

	            $parent_links = array_reverse( $parent_links );

	            $parent_string = implode( $delimiter, $parent_links );
	        }

	        // Lets build the breadcrumb trail
	        if ( $parent_string ) {
	            $breadcrumb_trail = $parent_string . $delimiter . $post_link;
	        } else {
	            $breadcrumb_trail = $post_link;
	        }

	        if ( $post_type_link )
	            $breadcrumb_trail = $post_type_link . $delimiter . $breadcrumb_trail;

	        if ( $category_links )
	            $breadcrumb_trail = $category_links . $breadcrumb_trail;
	    }

	    // Handle archives which includes category-, tag-, taxonomy-, date-, custom post type archives and author archives
	    if( is_archive() )
	    {
	        if (    is_category()
	             || is_tag()
	             || is_tax()
	        ) {
	            // Set the variables for this section
	            $term_object        = get_term( $queried_object );
	            $taxonomy           = $term_object->taxonomy;
	            $term_id            = $term_object->term_id;
	            $term_name          = $term_object->name;
	            $term_parent        = $term_object->parent;
	            $taxonomy_object    = get_taxonomy( $taxonomy );
	            $current_term_link  = $before . $taxonomy_object->labels->singular_name . ': ' . $term_name . $after;
	            $parent_term_string = '';

	            if ( 0 !== $term_parent )
	            {
	                // Get all the current term ancestors
	                $parent_term_links = [];
	                while ( $term_parent ) {
	                    $term = get_term( $term_parent, $taxonomy );

	                    $parent_term_links[] = sprintf( $link, esc_url( get_term_link( $term ) ), $term->name );

	                    $term_parent = $term->parent;
	                }

	                $parent_term_links  = array_reverse( $parent_term_links );
	                $parent_term_string = implode( $delimiter, $parent_term_links );
	            }

	            if ( $parent_term_string ) {
	                $breadcrumb_trail = $parent_term_string . $delimiter . $current_term_link;
	            } else {
	                $breadcrumb_trail = $current_term_link;
	            }

	        } elseif ( is_author() ) {

	            $breadcrumb_trail = esc_html__( 'Author archive for ', 'masterclass') .  $before . $queried_object->data->display_name . $after;

	        } elseif ( is_date() ) {
	            // Set default variables
	            $year     = $wp_the_query->query_vars['year'];
	            $monthnum = $wp_the_query->query_vars['monthnum'];
	            $day      = $wp_the_query->query_vars['day'];

	            // Get the month name if $monthnum has a value
	            if ( $monthnum ) {
	                $date_time  = DateTime::createFromFormat( '!m', $monthnum );
	                $month_name = $date_time->format( 'F' );
	            }

	            if ( is_year() ) {

	                $breadcrumb_trail = $before . $year . $after;

	            } elseif( is_month() ) {

	                $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ), $year );

	                $breadcrumb_trail = $year_link . $delimiter . $before . $month_name . $after;

	            } elseif( is_day() ) {

	                $year_link        = sprintf( $link, esc_url( get_year_link( $year ) ),             $year       );
	                $month_link       = sprintf( $link, esc_url( get_month_link( $year, $monthnum ) ), $month_name );

	                $breadcrumb_trail = $year_link . $delimiter . $month_link . $delimiter . $before . $day . $after;
	            }

	        } elseif ( is_post_type_archive() ) {

	            $post_type        = $wp_the_query->query_vars['post_type'];
	            $post_type_object = get_post_type_object( $post_type );

	            $breadcrumb_trail = $before . $post_type_object->labels->singular_name . $after;

	        }
	    }   

	    // Handle the search page
	    if ( is_search() ) {
	        $breadcrumb_trail = esc_html__( 'Search result for: ', 'masterclass' ) . $before . get_search_query() . $after;
	    }

	    // Handle 404's
	    if ( is_404() ) {
	        $breadcrumb_trail = $before . esc_html__( 'Error 404', 'masterclass' ) . $after;
	    }

	    // Handle paged pages
	    if ( is_paged() ) {
	        $current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
	        $page_addon   = $before . sprintf( esc_html__( ' ( Page %s )', 'masterclass' ), number_format_i18n( $current_page ) ) . $after;
	    }

	    $breadcrumb_output_link  = '';

	    // $breadcrumb_output_link .= '<h3>' . $title . '<h3>';
	    $breadcrumb_output_link .= '<div class="tophive-breadcrumbs">';
	    if (    is_home()
	         || is_front_page()
	    ) {
	        if ( is_paged() ) {
	            $breadcrumb_output_link .= $here_text . $delimiter;
	            $breadcrumb_output_link .= '<a href="' . $home_link . '">' . $home_text . '</a>';
	            $breadcrumb_output_link .= $page_addon;
	        }
	    } else {
	        $breadcrumb_output_link .= $here_text . $delimiter;
	        $breadcrumb_output_link .= '<a href="' . $home_link . '">' . $home_text . '</a>';
	        $breadcrumb_output_link .= $delimiter . $breadcrumb_trail;
	        $breadcrumb_output_link .= $page_addon;
	    }
	    $breadcrumb_output_link .= '</div>';

	    return $breadcrumb_output_link;
	}

	function render_titlebar( $args = array() ) {

		$classes   = array( 'page-header--item page-titlebar' );
		$layout    = top_hive()->get_setting_tab( 'page_header_layout' );
		$classes[] = $layout;
		?>
		<div id="page-titlebar" class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">
			<div class="page-titlebar-inner tophive-container">
				<?php
				/**
				 * Hook titlebar before
				 */
				do_action( 'tophive/titlebar/before' );

				// WPCS: XSS ok.
				if ( top_hive()->get_setting( 'titlebar_show_title' ) ) {
					if ( $args['title'] ) {
						echo '<' . $args['title_tag'] . ' class="titlebar-title h4">' . apply_filters( 'tophive_the_title', wp_kses_post( $args['title'] ) ) . '</' . $args['title_tag'] . '>';
					}
				}
				if ( $args['titlebar_tagline'] ) {
					if ( $args['tagline'] ) {
						// WPCS: XSS ok.
						echo '<div class="titlebar-tagline">' . apply_filters( 'tophive_the_title', wp_kses_post( $args['tagline'] ) ) . '</div>';
					}
				}
				/**
				 * Hook titlebar after
				 */
				do_action( 'tophive/titlebar/after' );
				?>
			</div>
		</div>
		<?php
	}

	function render() {
		$args = $this->get_settings();
		if ( 'none' == $args['display'] ) {
			return '';
		}

		switch ( $args['display'] ) {
			case 'cover':
				$this->render_cover( $args );
				break;
			case 'titlebar':
				$this->render_titlebar( $args );
				break;
			case 'shortcode':
				echo '<div class="page-header-shortcode">' . apply_filters( 'tophive_the_content', $args['shortcode'] ) . '</div>';
				break;
		}

	}

}

Tophive_Page_Header::get_instance();

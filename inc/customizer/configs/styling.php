<?php
if ( ! function_exists( 'tophive_customizer_styling_config' ) ) {
	function tophive_customizer_styling_config( $configs ) {

		$section = 'global_styling';

		$config = array(

			// Styling panel.
			array(
				'name'     => 'styling_panel',
				'type'     => 'panel',
				'priority' => 22,
				'title'    => esc_html__( 'Styling', 'masterclass' ),
			),

			// Styling Global Section.
			array(
				'name'     => "{$section}",
				'type'     => 'section',
				'panel'    => 'styling_panel',
				'title'    => esc_html__( 'Global Colors', 'masterclass' ),
				'priority' => 10,
			),

			array(
				'name'    => "{$section}_color_theme_heading",
				'type'    => 'heading',
				'section' => $section,
				'title'   => esc_html__( 'Theme Colors', 'masterclass' ),
			),

			array(
				'name'        => "{$section}_color_primary",
				'type'        => 'color',
				'section'     => $section,
				'placeholder' => '#81d742',
				'title'       => esc_html__( 'Primary Color', 'masterclass' ),
				'css_format'  => apply_filters(
					'tophive/styling/primary-color',
					'
					.header-top .header--row-inner,
					.button,
					button,
					button.button,
					input[type="button"],
					input[type="reset"],
					input[type="submit"],
					.button:not(.components-button):not(.customize-partial-edit-shortcut-button), 
					input[type="button"]:not(.components-button):not(.customize-partial-edit-shortcut-button),
					input[type="reset"]:not(.components-button):not(.customize-partial-edit-shortcut-button), 
					input[type="submit"]:not(.components-button):not(.customize-partial-edit-shortcut-button),
					.pagination .nav-links > *:hover,
					.pagination .nav-links span,
					.nav-menu-desktop.style-full-height .primary-menu-ul > li.current-menu-item > a, 
					.nav-menu-desktop.style-full-height .primary-menu-ul > li.current-menu-ancestor > a,
					.hover-info-wishlist.course-single-wishlist a.on,
					.hover-info-wishlist a.on,
					.nav-menu-desktop.style-full-height .primary-menu-ul > li > a:hover,
					.posts-layout .readmore-button:hover,
					.tophive-lp-content ul.learn-press-nav-tabs .course-nav.active a:after, 
					.tophive-lp-content ul.learn-press-nav-tabs .course-nav:hover a:after,
					.woocommerce-tabs ul.tabs li.reviews_tab a span
					{
					    background-color: {{value}};
					}
					.posts-layout .readmore-button,
					body .theme-primary-color,
					.theme-primary-color-head-hover:hover h1,
					.theme-primary-color-head-hover:hover h2,
					.theme-primary-color-head-hover:hover h3,
					.theme-primary-color-head-hover:hover h4,
					.theme-primary-color-head-hover:hover h5,
					.theme-primary-color-head-hover:hover h6,
					.hover-info-wishlist.course-single-wishlist a:not(.on),
					.hover-info-wishlist a:not(.on),
					.tophive-lp-content ul.learn-press-nav-tabs li a,
					.woocommerce-tabs ul.tabs li.active a,
					li.active a, li a.active,
					li.current a, li a.current{
						color: {{value}};
					}
					.pagination .nav-links > *:hover,
					.pagination .nav-links span,
					.entry-single .tags-links a:hover, 
					.entry-single .cat-links a:hover,
					.posts-layout .readmore-button,
					.hover-info-wishlist.course-single-wishlist a,
					.hover-info-wishlist a,
					.posts-layout .readmore-button:hover,
					li.active a, li a.active,
					li.current a, li a.current
					{
					    border-color: {{value}};
					}'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_secondary",
				'type'        => 'color',
				'section'     => $section,
				'placeholder' => '#c3512f',
				'title'       => esc_html__( 'Secondary Color', 'masterclass' ),
				'css_format'  => apply_filters(
					'tophive/styling/secondary-color',
					'
				
					.tophive-builder-btn
					{
					    background-color: {{value}};
					}'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_text",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Text Color', 'masterclass' ),
				'placeholder' => '#686868',
				'css_format'  => apply_filters(
					'tophive/styling/text-color',
					'
					body
					{
					    color: {{value}};
					}
					abbr, acronym {
					    border-bottom-color: {{value}};
					}'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_link",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Link Color', 'masterclass' ),
				'placeholder' => '#1e4b75',
				'css_format'  => apply_filters(
					'tophive/styling/link-color',
					'a{color: {{value}};}'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_link_hover",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Link Hover Color', 'masterclass' ),
				'placeholder' => '#111111',
				'css_format'  => apply_filters(
					'tophive/styling/link-color-hover',
					'
					a:hover, 
					a:focus,
					.widget-area li:hover a,
					.widget-area li:hover:before,
					.posts-layout .readmore-button:hover,
					.link-meta:hover, .link-meta a:hover
					{
					    color: {{value}};
					}'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_border",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Border Color', 'masterclass' ),
				'placeholder' => '#eaecee',
				'css_format'  => apply_filters(
					'tophive/styling/color-border',
					'
h2 + h3, 
.comments-area h2 + .comments-title, 
.h2 + h3, 
.comments-area .h2 + .comments-title, 
.page-breadcrumb {
    border-top-color: {{value}};
}
blockquote,
.site-content .widget-area .menu li.current-menu-item > a:before
{
    border-left-color: {{value}};
}

@media screen and (min-width: 64em) {
    .comment-list .children li.comment {
        border-left-color: {{value}};
    }
    .comment-list .children li.comment:after {
        background-color: {{value}};
    }
}

.page-titlebar, .page-breadcrumb,
.posts-layout .entry-inner {
    border-bottom-color: {{value}};
}

.header-search-form .search-field,
.entry-content .page-links a,
.header-search-modal,
.pagination .nav-links > *,
.entry-footer .tags-links a, .entry-footer .cat-links a,
.search .content-area article,
.site-content .widget-area .menu li.current-menu-item > a,
.posts-layout .entry-inner,
.post-navigation .nav-links,
article.comment .comment-meta,
.widget-area .widget_pages li a, .widget-area .widget_categories li a, .widget-area .widget_archive li a, .widget-area .widget_meta li a, .widget-area .widget_nav_menu li a, .widget-area .widget_product_categories li a, .widget-area .widget_recent_entries li a, .widget-area .widget_rss li a,
.widget-area .widget_recent_comments li
{
    border-color: {{value}};
}

.header-search-modal::before {
    border-top-color: {{value}};
    border-left-color: {{value}};
}

@media screen and (min-width: 48em) {
    .content-sidebar.sidebar_vertical_border .content-area {
        border-right-color: {{value}};
    }
    .sidebar-content.sidebar_vertical_border .content-area {
        border-left-color: {{value}};
    }
    .sidebar-sidebar-content.sidebar_vertical_border .sidebar-primary {
        border-right-color: {{value}};
    }
    .sidebar-sidebar-content.sidebar_vertical_border .sidebar-secondary {
        border-right-color: {{value}};
    }
    .content-sidebar-sidebar.sidebar_vertical_border .sidebar-primary {
        border-left-color: {{value}};
    }
    .content-sidebar-sidebar.sidebar_vertical_border .sidebar-secondary {
        border-left-color: {{value}};
    }
    .sidebar-content-sidebar.sidebar_vertical_border .content-area {
        border-left-color: {{value}};
        border-right-color: {{value}};
    }
    .sidebar-content-sidebar.sidebar_vertical_border .content-area {
        border-left-color: {{value}};
        border-right-color: {{value}};
    }
}
'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_meta",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Meta Color', 'masterclass' ),
				'placeholder' => '#6d6d6d',
				'css_format'  => apply_filters(
					'tophive/styling/color-meta',
					'
					.pagination .nav-links > *,
					.link-meta, 
					.link-meta a,
					.color-meta,
					.entry-single .tags-links:before, 
					.entry-single .cats-links:before
					{
					    color: {{value}};
					}'
				),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_heading",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Heading Color', 'masterclass' ),
				'placeholder' => '#2b2b2b',
				'css_format'  => apply_filters( 'tophive/styling/color-heading', 'h1, h2, h3, h4, h5, h6 { color: {{value}};}' ),
				'selector'    => 'format',
			),

			array(
				'name'        => "{$section}_color_w_title",
				'type'        => 'color',
				'section'     => $section,
				'title'       => esc_html__( 'Widget Title Color', 'masterclass' ),
				'placeholder' => '#444444',
				'css_format'  => '.site-content .widget-title { color: {{value}};}',
				'selector'    => 'format',
			),

		);

		return array_merge( $configs, $config );
	}
}

add_filter( 'tophive/customizer/config', 'tophive_customizer_styling_config' );

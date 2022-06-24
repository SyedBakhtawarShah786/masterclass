<?php
/**
 * Functions which enhance the theme by hooking into WordPerss and itself (huh?).
 *
 * @package tophive
 */

if ( ! function_exists( 'tophive_body_classes' ) ) {
	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 0.0.1
	 * @since 0.2.6
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	function tophive_body_classes( $classes ) {

		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		$layout = tophive_get_layout();
		if ( '' != $layout ) {
			$classes[] = $layout;
			/**
			 * Add more layout classs
			 *
			 * @since 0.2.6
			 */
			$classes[] = 'main-layout-' . $layout;
		}

		$sidebar_vertical_border = top_hive()->get_setting( 'sidebar_vertical_border' );
		if ( 'sidebar_vertical_border' == $sidebar_vertical_border ) {
			$classes[] = 'sidebar_vertical_border';
		}

		if ( is_customize_preview() ) {
			$classes[] = 'customize-previewing';
		}

		// Site layout mode.
		$site_layout = sanitize_text_field( top_hive()->get_setting( 'site_layout' ) );
		if ( $site_layout ) {
			$classes[] = $site_layout;
		}

		$animate = top_hive()->get_setting( 'header_sidebar_animate' );
		if ( ! $animate ) {
			$animate = 'menu_sidebar_slide_left';
		}
		$classes[] = $animate;

		return $classes;
	}
}
add_filter( 'body_class', 'tophive_body_classes' );


if ( ! function_exists( 'tophive_site_classes' ) ) {
	function tophive_site_classes() {
		$classes    = array();
		$classes[]  = 'site';
		$box_shadow = top_hive()->get_setting( 'site_box_shadow' );
		$footer_layout = top_hive()->get_setting( 'site_footer_layout' );
		if ( $box_shadow ) {
			$classes[] = esc_attr( $box_shadow );
		}
		if ( $footer_layout ) {
			$classes[] = esc_attr( $footer_layout );
		}

		$classes = apply_filters( 'tophive_site_classes', $classes );

		echo 'class="' . join( ' ', $classes ) . '"';
	}
}

if ( ! function_exists( 'tophive_site_content_classes' ) ) {
	/**
	 * Adds custom classes to the array of site content classes.
	 *
	 * @param array $classes Classes for the site content element.
	 *
	 * @return array
	 */
	function tophive_site_content_classes( $classes ) {
		$classes[] = 'site-content';

		return $classes;
	}
}

add_filter( 'tophive_site_content_class', 'tophive_site_content_classes' );


if ( ! function_exists( 'tophive_sidebar_primary_classes' ) ) {
	/**
	 * Adds custom classes to the array of primary sidebar classes.
	 *
	 * @param array $classes Classes for the primary sidebar element.
	 *
	 * @return array
	 */
	function tophive_sidebar_primary_classes( $classes ) {

		$classes[] = 'sidebar-primary';
		$layout    = tophive_get_layout();
		
		if ( 'sidebar-sidebar-content' == $layout ) {
			$classes[] = 'tophive-col-3_sm-12';
		}

		if ( 'sidebar-content-sidebar' == $layout ) {
			$classes[] = 'tophive-col-3_sm-12';
		}

		if ( 'content-sidebar-sidebar' == $layout ) {
			$classes[] = 'tophive-col-3_sm-12';
		}

		if ( 'sidebar-content' == $layout ) {
			$classes[] = 'tophive-col-3_sm-12';
		}

		if ( 'content-sidebar' == $layout ) {
			$classes[] = 'tophive-col-3_sm-12';
		}

		return $classes;
	}
}
add_filter( 'tophive_sidebar_primary_class', 'tophive_sidebar_primary_classes' );

if ( ! function_exists( 'tophive_sidebar_secondary_classes' ) ) {
	/**
	 * Adds custom classes to the array of secondary sidebar classes.
	 *
	 * @param array $classes Classes for the secondary sidebar element.
	 *
	 * @return array
	 */
	function tophive_sidebar_secondary_classes( $classes ) {

		$classes[] = 'sidebar-secondary';
		$layout    = tophive_get_layout();

		if ( 'sidebar-sidebar-content' == $layout ) {
			$classes[] = 'tophive-col-3_md-0_sm-12';
		}

		if ( 'sidebar-content-sidebar' == $layout ) {
			$classes[] = 'tophive-col-3_md-0_sm-12-first'; // Not move to bottom on mobile, ueh?
		}

		if ( 'content-sidebar-sidebar' == $layout ) {
			$classes[] = 'tophive-col-3_md-0_sm-12';
		}

		return $classes;
	}
}
add_filter( 'tophive_sidebar_secondary_class', 'tophive_sidebar_secondary_classes' );

if ( ! function_exists( 'tophive_main_content_classes' ) ) {
	/**
	 * Adds custom classes to the array of main content classes.
	 *
	 * @param array $classes Classes for the main content element.
	 *
	 * @return array
	 */
	function tophive_main_content_classes( $classes ) {

		$classes[] = 'content-area';
		$layout    = tophive_get_layout();
		if ( 'sidebar-sidebar-content' == $layout ) {
			$classes[] = 'tophive-col-6_md-9_sm-12-last_sm-first';
		}

		if ( 'sidebar-content-sidebar' == $layout ) {
			$classes[] = 'tophive-col-6_md-9_sm-12';
		}

		if ( 'content-sidebar-sidebar' == $layout ) {
			$classes[] = 'tophive-col-6_md-9_sm-12-first';
		}

		if ( 'sidebar-content' == $layout ) {
			$classes[] = 'tophive-col-9_sm-12-last_sm-first';
		}

		if ( 'content-sidebar' == $layout ) {
			$classes[] = 'tophive-col-9_sm-12';
		}

		if ( 'content' == $layout ) {
			$classes[] = 'tophive-col-12';
		}
		return $classes;
	}
}
add_filter( 'tophive_main_content_class', 'tophive_main_content_classes' );

if ( ! function_exists( 'tophive_site_content_grid_classes' ) ) {
	/**
	 * Adds custom classes to the array of site content grid classes.
	 *
	 * @param array $classes Classes for the main content element.
	 *
	 * @return array
	 */
	function tophive_site_content_grid_classes( $classes ) {

		$classes[] = 'tophive-grid';

		return $classes;
	}
}
add_filter( 'tophive_site_content_grid_class', 'tophive_site_content_grid_classes' );

if ( ! function_exists( 'tophive_site_content_container_classes' ) ) {
	/**
	 * Adds custom classes to the array of site content container classes.
	 *
	 * @param array $classes Classes for the main content element.
	 *
	 * @return array
	 */
	function tophive_site_content_container_classes( $classes ) {

		$classes[] = 'tophive-container';

		return $classes;
	}
}
add_filter( 'tophive_site_content_container_class', 'tophive_site_content_container_classes' );

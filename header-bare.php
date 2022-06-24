<?php
/**
 * The header for bare pages
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tophive
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=10.0, user-scalable=yes">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" <?php tophive_site_classes(); ?>>
	<a class="skip-link screen-reader-text" href="#site-content"><?php esc_html_e( 'Skip to content', 'masterclass' ); ?></a>
	<?php
	do_action( 'tophive/site-start/before' );
	if ( ! tophive_is_e_theme_location( 'header' ) ) {
		/**
		 * Site start
		 *
		 * Hooked
		 *
		 * @see tophive_customize_render_header - 10
		 * @see Tophive_Page_Header::render - 35
		 */
		do_action( 'tophive/site-start' );
	}
	do_action( 'tophive/site-start/after' );

	/**
	 * Hook before main content
	 *
	 * @since 0.2.1
	 */
	do_action( 'tophive/before-site-content' );
	?>
	<div id="site-content" <?php tophive_site_content_class(); ?>>
		<div <?php tophive_site_content_container_class(); ?>>
			<div <?php tophive_site_content_grid_class(); ?>>
				<main id="main" class="tophive-col-12">

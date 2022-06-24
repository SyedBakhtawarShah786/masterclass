<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package tophive
 */

get_header(); ?>
	<div class="content-inner">
		<?php
		do_action( 'tophive/content/before' );
		tophive_blog_posts_heading();
		tophive_blog_posts(
			array(
				'_overwrite' => array(
					'media_hide'     => 1,
					'excerpt_type'   => top_hive()->get_setting( 'search_results_excerpt_type' ),
					'excerpt_length' => top_hive()->get_setting( 'search_results_excerpt_length' ),
					'excerpt_more'   => top_hive()->get_setting( 'search_results_excerpt_more' ),
				),
			)
		);
		do_action( 'tophive/content/after' );
		?>
	</div><!-- #.content-inner -->
<?php
get_footer();

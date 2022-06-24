<?php
/**
 * The main template file
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package tophive
 */

get_header(); ?>
	<div class="content-inner">
		<?php
		do_action( 'tophive/content/before' );
		if ( is_singular() ) {
			if ( ! tophive_is_e_theme_location( 'single' ) ) {
				tophive_blog_posts_heading();
				tophive_blog_posts();
			}
		} elseif ( is_archive() || is_home() || is_search() ) {
			if ( ! tophive_is_e_theme_location( 'archive' ) ) {
				tophive_blog_posts_heading();
				tophive_blog_posts();
			}
		} else {
			if ( ! tophive_is_e_theme_location( 'single' ) ) {
				get_template_part( 'template-parts/404' );
			}
		}
		do_action( 'tophive/content/after' );
		?>
	</div><!-- #.content-inner -->
<?php
get_footer();

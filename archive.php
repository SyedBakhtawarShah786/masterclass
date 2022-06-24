<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package tophive
 */

get_header(); ?>
<div class="content-inner">
	<?php
	do_action( 'tophive/content/before' );
	if ( ! tophive_is_e_theme_location( 'archive' ) ) {
		tophive_blog_posts_heading();
		tophive_blog_posts();
	}
	do_action( 'tophive/content/after' );
	?>
</div><!-- #.content-inner -->
<?php
get_footer();

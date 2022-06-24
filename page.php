<?php
/**
 * The templates for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package tophive
 */

get_header(); ?>
	<div class="content-inner">
		<?php
		do_action( 'tophive/content/before' );

		if ( ! tophive_is_e_theme_location( 'single' ) ) {
			while ( have_posts() ) :
				the_post();
				$post_type = get_post_type();
				$current_taxonomy = get_query_var( 'taxonomy' );
				if( $post_type == 'lp_course' ){
					get_template_part( 'template-parts/content', 'learnpress' );
				}
				else{
					if( $current_taxonomy == 'course_category' ){
						get_template_part( 'template-parts/content', 'learnpress-category' );
					}
					else{
						get_template_part( 'template-parts/content', 'page' );
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					}
				}

			endwhile; // End of the loop.
		}
		do_action( 'tophive/content/after' );
		?>
	</div><!-- #.content-inner -->
<?php
get_footer();

<?php
/**
 * The template for displaying Buddypress content
 *
 *
 * @link https://codex.buddypress.org/themes/theme-compatibility-1-7/template-hierarchy/
 *
 * @package MasterClass
 */

get_header(); ?>
	<div class="content-inner">
		<?php
		do_action( 'tophive/content/before' );
			if ( ! tophive_is_e_theme_location( 'single' ) ) {
				while ( have_posts() ) :
					the_post();
					
					get_template_part( 'template-parts/content', 'buddypress' );

				endwhile; // End of the loop.
			}
		do_action( 'tophive/content/after' );
		?>
	</div><!-- #.content-inner -->
<?php
get_footer();

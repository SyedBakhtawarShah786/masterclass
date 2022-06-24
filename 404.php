<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package tophive
 */

get_header(); ?>

<div class="content-inner">
	<?php
	if ( ! tophive_is_e_theme_location( 'single' ) ) {
		?>
		<section class="error-404 not-found ec-p-md-5 ec-p-1 ec-text-center ec-mx-md-5 ec-mx-sm-3 ec-mx-1">
			<img class="ec-mb-1" width="500px" src="<?php echo get_template_directory_uri() . '/assets/images/error.png' ?>" alt="Not-Found">
			<?php if ( tophive_is_post_title_display() ) { ?>
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Ooooops! That page can&rsquo;t be found.', 'masterclass' ); ?></h1>
				</header><!-- .page-header -->
			<?php } ?>
			<div class="page-content widget-area">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'masterclass' ); ?></p>
				<div class="widget ec-px-md-5 ec-px-1 ec-mx-md-5 ec-mx-1">
					<?php get_search_form(); ?>
				</div>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->
		<?php
	}
	?>
</div><!-- #.content-inner -->
<?php
get_footer();

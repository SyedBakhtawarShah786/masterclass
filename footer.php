<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tophive
 */

?>              <?php do_action( 'tophive/main/after' ); ?>
			</main><!-- #main -->
			<?php do_action( 'tophive/sidebars' ); ?>
		</div><!-- #.tophive-grid -->
	</div><!-- #.tophive-container -->
</div><!-- #content -->
<?php
/**
 * Hook before site content
 *
 * @since 0.2.2
 */
do_action( 'tophive/after-site-content' );

do_action( 'tophive/site-end/before' );
if ( ! tophive_is_e_theme_location( 'footer' ) ) {
	/**
	 * Site end
	 *
	 * @hooked tophive_customize_render_footer - 10
	 *
	 * @see tophive_customize_render_footer
	 */
	do_action( 'tophive/site-end' );
}
do_action( 'tophive/site-end/after' );

?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

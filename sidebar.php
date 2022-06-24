<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tophive
 */

$sidebar_id = apply_filters( 'tophive/sidebar-id', 'sidebar-1', 'primary' );
if ( ! is_active_sidebar( $sidebar_id ) ) {
	return;
}
?>
<aside id="sidebar-primary" <?php tophive_sidebar_primary_class(); ?>>
	<div class="sidebar-primary-inner sidebar-inner widget-area">
		<?php
		do_action( 'tophive/sidebar-primary/before' );
		dynamic_sidebar( $sidebar_id );
		do_action( 'tophive/sidebar-primary/after' );
		?>
	</div>
</aside><!-- #sidebar-primary -->

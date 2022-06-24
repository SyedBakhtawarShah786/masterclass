<?php
/**
 * The secondary sidebar for 3 columns layout.
 *
 * @link    https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package tophive
 */

$sidebar_id = apply_filters( 'tophive/sidebar-id', 'sidebar-2', 'secondary' );
if ( ! is_active_sidebar( $sidebar_id ) ) {
	return;
}
?>
<aside id="sidebar-secondary" <?php tophive_sidebar_secondary_class(); ?>>
	<div class="sidebar-secondary-inner sidebar-inner widget-area">
		<?php
		do_action( 'tophive/sidebar-secondary/before' );
		dynamic_sidebar( $sidebar_id );
		do_action( 'tophive/sidebar-secondary/after' );
		?>
	</div>
</aside><!-- #sidebar-secondary -->

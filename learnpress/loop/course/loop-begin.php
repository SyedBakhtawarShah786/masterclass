<?php
/**
 * Template for displaying wrap start of archive course within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/loop/course/loop-begin.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
remove_action( 'learn-press/before-main-content', 'learn_press_search_form', 20);

?>
<div class="ec-row ec-mb-3">
	<?php 
		do_action( 'tophive/learnpress/archive/courses/before' );
	?>
</div>
<div class="ec-row">
	<div class="ec-col-md-3">
		<?php  
			do_action('tophive/learnpress/archive/courses/sidebar');
		?>
	</div>
<div class="learn-press-courses ec-col-md-9">
<div class="ec-row tophive-advanced-filter-wrapper" data-grid="one">
	<span class="filter-loader"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="137px" height="137px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
					<path d="M35 50A15 15 0 0 0 65 50A15 16.3 0 0 1 35 50" fill="#292664" stroke="none" transform="rotate(177.696 50 50.65)">
					  <animateTransform attributeName="transform" type="rotate" dur="0.5025125628140703s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50.65;360 50 50.65"></animateTransform>
					</path></svg></span>

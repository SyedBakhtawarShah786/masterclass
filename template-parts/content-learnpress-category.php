<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package tophive
 */
$args = array(
    'post_type' => 'lp_course',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'tax_query' => array(
        array(
            'taxonomy' 	=> get_query_var( 'taxonomy' ),
            'field' 	=> 'slug',
            'terms'    	=> get_query_var( 'term' )
        )
    )
);
$query = new WP_Query( $args );

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="tophive-lp-headbar">
		<div class="tophive-lp-heading">
			<?php
				if(function_exists('learn_press_breadcrumb')){
					echo learn_press_breadcrumb();
				}
			?>
			<div class="ec-row">
				<div class="ec-col-md-6">
					<h2 class="font-weight-bold mb-0"><?php the_title(); ?> <small class="ec-badge badge-pill ec-badge-primary"><?php echo esc_attr($query->found_posts) ?> courses</small></h2>
				</div>
				<div class="ec-col-md-6">
					<?php do_action( 'tophive/learnpress/category/widgets/top' ); ?>
				</div>
			</div>
		</div>
	</div>

		
	<?php 
		
		if($query->have_posts()){
			?>
			<div class="ec-row">
				<?php 
					if( is_rtl() ){
						?>
							<div class="ec-col-sm-3 ec-pl-4">
						<?php
					}else{
						?>
							<div class="ec-col-sm-3 ec-pr-4">
						<?php
					}
				?>
					<?php
						do_action( 'tophive/learnpress/category/sidebar' );
					?>
				</div>
				
				<div class="ec-col-sm-9 tophive-advanced-filter-wrapper" data-grid="three">
					<?php  
						while ($query->have_posts()) {
					    	$query->the_post();
					    	$course = \LP_Global::course();
				    		echo apply_filters( 'tophive/learnpress/default/single/course/grid-three', $course );
						}
					?>
				</div>	
			</div>
			<?php
		}
	?>

</article><!-- #post-<?php the_ID(); ?> -->

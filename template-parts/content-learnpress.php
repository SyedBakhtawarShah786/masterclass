<?php
/**
 * Template part for displaying Learnpres single course
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package tophive
 */
	global $course, $wp;
	$user = LP_Global::user();
				
	$paged = ! empty( $_REQUEST['paged'] ) ? intval( $_REQUEST['paged'] ) : 1;

    $instructor_id = get_post_field( 'post_author', get_the_ID() );
	$instructor_slug = get_the_author_meta( 'user_nicename', $instructor_id );
	$pages = get_pages(array(
	    'meta_key' => '_wp_page_template',
	    'meta_value' => 'page-instructor.php'
	));
	$instructor_url = esc_url( trailingslashit(site_url()) ) . get_post($pages[0]->ID)->post_name . '/' . $instructor_slug;

	do_action( 'tophive/learnpress/single/before-content', get_the_ID() );

	if( top_hive()->is_learnpress_active() ){
		$courses_page_id = learn_press_get_page_id('courses');
		if( !empty($courses_page_id) ){
			update_post_meta( $courses_page_id, '_wp_page_template', 'courses.php' );
		}
	}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<div class="tophive-lp-headbar">
		<span class="head-bg"></span>
		<div class="ec-row">
			<div class="ec-col-md-7">
				<div class="tophive-lp-heading">
					<?php
						if(function_exists('learn_press_breadcrumb')){
							echo learn_press_breadcrumb();
						}
					?>
					<h2 class="font-weight-bold"><?php the_title(); ?></h2>	
					<?php 

						the_excerpt();

                        do_action( 'tophive/learnpress/single/header/metadata', get_the_ID() );
					?>
				</div>
			</div>	
			<div class="ec-col-md-5">
				<div class="tophive-lp-sidebar ec-mb-md-5 ec-ml-md-3 ec-mt-5">
					
					<div class="course-price">
						<?php do_action( 'tophive/learnpress/single/header/price', get_the_ID() ) ?>
					
					</div>
				    <div class="ec-d-flex ec-align-items-center">

					    <?php 
					    	do_action( 'tophive/learnpress/single/header/purchase-button', $course, $user );
					    	
					    	do_action( 'tophive/learnpress/single/header/wishlist-button', get_the_ID() );
					    ?>
				    </div>

				</div>
			</div>
		</div>
	</div>
	<div class="tophive-lp-content ec-mt-n5">

			<?php
				
				$url = home_url( $wp->request );
				if ( strpos($url,'quizzes') || strpos($url,'lessons') ) {
					the_content( $more_link_text = null, $strip_teaser = false );
				}
			 ?>
		<div class="ec-row">
			<?php  
				if( is_rtl() ){
					?>
						<div class="ec-col-lg-5 ec-pl-md-3 ec-pl-lg-5">
					<?php
				}else{
					?>
						<div class="ec-col-lg-5 ec-pr-md-3 ec-pr-lg-5">
					<?php
				}
			?>
				<div class="course-curriculum" id="learn-press-course-curriculum">
					<?php do_action( 'tophive/learnpress/single/enrolled/progress', $course, $user ); ?>
				    <div class="curriculum-scrollable">

						<?php
							do_action( 'learn-press/before-single-course-curriculum' );
								if( $course->get_curriculum() ){
									?>
										<h3 class="ec-mb-4"><?php esc_html_e( 'Course curriculum', 'masterclass' ); ?></h3>
									<?php
									apply_filters( 'tophive/learnpress/single/lessons/popup', $course, $user );
								}
						    	do_action( 'tophive/learnpress/single/curriculum', get_the_ID() , $course, $user );

							do_action( 'learn-press/after-single-course-curriculum' );
						?>

				    </div>

				</div>

				<div class="course-author">

				    <h3 class="ec-mb-4"><?php esc_html_e( 'Instructor', 'masterclass' ); ?></h3>

					<div class="ec-d-flex ec-align-items-center">				
						<?php 
							if( is_rtl() ){
								?>
					    			<div class="ec-ml-3">
								<?php
							}else{
								?>
					    			<div class="ec-mr-3">
								<?php
							}
						?>
							<?php echo get_avatar( $instructor_id, 60 ); ?>
					    </div>
					    <div>
							<h6 class="ec-mb-0"><?php echo get_the_author_meta( 'display_name', $instructor_id ); ?><small class="ec-ml-2"><a class="view-profile" href="<?php echo esc_url($instructor_url); ?>"><?php esc_html_e( 'View Profile', 'masterclass' ); ?></a></small></h6>
							<p class="ec-mb-0"><?php echo get_the_author_meta( 'designation', $instructor_id ); ?></p>
					    </div>
					</div>
				    <div class="ec-mt-3">
						<p class="description"><?php echo esc_attr($course->get_author()->get_description()); ?></p>
				    </div>

				</div>
			</div>
			<div class="ec-col-lg-7 ec-pl-md-3 ec-pl-lg-4">
					<?php
						$featured_video = get_post_meta( get_the_ID(), 'tophive_lp_featured_video', true );
						if( !empty($featured_video) ){
							$video_url = apply_filters( 'tophive/learnpress/single/video/embed-url', $featured_video );
							?>
								<div class="single-course-video ec-mb-4">
									<?php do_action( 'tophive/learnpress/single/video/html', $video_url ); ?>
								</div>
							<?php
						}
						if( empty($featured_video) && !empty(get_the_post_thumbnail()) ){
							?>
								<div class="single-course-thumb ec-mb-4">
									<?php echo get_the_post_thumbnail();?>
								</div>
							<?php
						}
					?>
				<?php 
					$points = get_post_meta( get_the_ID(), 'customdata_group', true );
					if( !empty($points) ):?>
					<div class="course-learning-points">
						<h5 class="ec-mb-4"><?php esc_html_e( 'What you will learn', 'masterclass' ); ?></h5>
						<div class="ec-row">
							<?php
							foreach ($points as $value) {
								$icon_margin = is_rtl() ? 'ec-ml-2' : 'ec-mr-2';
								echo '<div class="ec-col-sm-6 ec-d-flex">
									<span class="'. $icon_margin .' list-icon">
										<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										  	<path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									</span>
									<span class="main-text">' . $value['TitleItem'] . '</span>
							 	</div>';
							}
							?>							
						</div>
					</div>
				<?php endif; ?>
				<div class="course-description" id="learn-press-course-description">
					<?php  
						if( strlen(get_the_content()) > 1300 ){
							echo substr_replace(get_the_content(), '<div class="tophive-course-desc-hidden ec-d-none">', 1500, 0);
							echo '</div>';
							echo '<div class="tophive-course-desc-readmore">'. esc_html__( 'Show More', 'masterclass' ) .'<i class="ti-angle-down ec-pl-2 ec-mt-1"></i></div>';
						}else {
							echo get_the_content();
						}
					?>

				</div>
				<?php 
					do_action( 'tophive/learnpress/single/feedback', $user, get_the_ID() );

					do_action( 'tophive/learnpress/single/reviews', $paged, get_the_ID() );
				?>				
			</div>
		</div>

		<div class="ec-row">
			<div class="tophive-lp-related-courese ec-col-12">
			<?php 
				$query = new WP_Query(
				    array(
				    	'posts_per_page' => 4,
				    	'post_type' => 'lp_course',
				        'category__in'   => wp_get_post_categories( get_the_ID() ),
				        'post__not_in'   => array( get_the_ID() )
				    )
				);
				if( $query->found_posts > 0 ){
					?>
					<h3 class="ec-mt-5 ec-mb-4"><?php esc_html_e( 'Related courses', 'masterclass' ) ?></h3>	
					<div class="ec-row">
					<?php
					if( $query->have_posts() ) { 
					    while( $query->have_posts() ) { 
					        $query->the_post();
					    	$course = \LP_Global::course();

							echo apply_filters( 
								'tophive/learnpress/default/single/course/grid-one',
								$course,
								'ec-col-md-3'
							);
					    }
					}?>
					</div>
					<?php
				}
			?>
		</div>
		</div>
	</div>

</article>

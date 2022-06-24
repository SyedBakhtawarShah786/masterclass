<?php  
	
class Tophive_LP_Single_Course{
	static $_instance;
	public $lesson_id;
	static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	function is_active() {
		return top_hive()->is_learnpress_active();
	}

	function __construct() {
		if( $this->is_active() ){
			add_action( 'tophive/learnpress/single/before-content', array($this, 'tophive_before_single_content'), 10 , 1 );
			add_action( 'tophive/learnpress/single/header/metadata', array($this, 'tophive_render_single_header_meta'), 10 , 1 );
			add_action( 'tophive/learnpress/single/header/price', array($this, 'tophive_render_single_header_price'), 10 , 1 );
			add_action( 'tophive/learnpress/single/header/purchase-button', array($this, 'tophive_render_single_header_purchase_button'), 10 , 2 );
			add_action( 'tophive/learnpress/single/header/wishlist-button', array($this, 'tophive_render_single_header_wishlist_button'), 10 , 1 );
			add_action( 'tophive/learnpress/single/curriculum', array($this, 'tophive_render_single_curriculum'), 10 , 3 );

			add_action( 'tophive/learnpress/single/video/html', array($this, 'featured_video_embed_code'), 10 , 1 );

	        add_action( 'tophive/learnpress/single/video/embed-url', array($this, 'getEmbedUrl'), 10, 1);
	        add_action( 'tophive/learnpress/single/review/timelapse', array($this, 'tophive_review_timelapse'), 10 , 1 );
	        add_action( 'tophive/learnpress/single/rating/number', array($this, 'tophive_rating_number'), 10 , 1 );
	        
			add_action( 'tophive/learnpress/single/enrolled/progress', array($this, 'tophive_render_single_enrolled_progress'), 10 , 3 );
			add_action( 'tophive/learnpress/single/feedback', array($this, 'tophive_render_single_feedback'), 10 , 2 );
			add_action( 'tophive/learnpress/single/reviews', array($this, 'tophive_render_single_reviews'), 10 , 2 );
			add_action( 'wp_ajax_tophive_quizz', array( $this, 'tophive_lp_quiz_question' ) );
			add_action( 'wp_ajax_nopriv_tophive_quizz', array( $this, 'tophive_lp_quiz_question' ) );
		}
	}
	function featured_video_embed_code( $url ){
		$videotype = $this->videoType($url);
		if( $videotype == 'selfhosted' ){
			?>
				<video width="100%" height="100%" src="<?php echo esc_url($url); ?>" controls></video>
			<?php
				echo '<div class="embed-video-overlay">' . get_the_post_thumbnail() . '</div>';
				echo '<div class="embed-video-play-button video-embed-play"><i class="ti-control-play"></i></div>';
				echo '<span class="embed-video-info">
						<p>'. esc_html__( 'Preview this course', 'masterclass' ) .'</p>
						<i>i</i>
				</span>';
		}else{

		?>
			<iframe src="<?php echo esc_url($url); ?>" frameborder="0" class="tophive-course-featured-video" height="100%" width="100%" allowfullscreen></iframe>
		<?php
			echo '<div class="embed-video-overlay">' . get_the_post_thumbnail() . '</div>';
			echo '<div class="embed-video-play-button video-iframe-play"><i class="ti-control-play"></i></div>';
			echo '<span class="embed-video-info">
						<p>'. esc_html__( 'Preview this course', 'masterclass' ) .'</p>
						<i>i</i>
				</span>';
		}
	}
	function getEmbedUrl($url){
		$type = $this->videoType($url);
		switch ($type) {
			case 'youtube':
				$embed = $this->getYoutubeEmbedUrl( $url );
				break;
			case 'vimeo':
				$embed = $this->getVimeoEmbedUrl( $url );
				break;
			
			default:
				$embed = $url;
				break;
		}
		return $embed;
	}
	function getYoutubeEmbedUrl($url){
	    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
	    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';

	    if (preg_match($longUrlRegex, $url, $matches)) {
	        $youtube_id = $matches[count($matches) - 1];
	    }

	    if (preg_match($shortUrlRegex, $url, $matches)) {
	        $youtube_id = $matches[count($matches) - 1];
	    }
	    return 'https://www.youtube.com/embed/' . $youtube_id ;
	}
	function getVimeoEmbedUrl($url){
	    $longUrlRegex = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';

	    if (preg_match($longUrlRegex, $url, $matches)) {
	        $vimeo_id = $matches[count($matches) - 1];
	    }
	    return 'https://player.vimeo.com/video/' . $vimeo_id ;
	}
	function videoType($url) {
		$host = parse_url(get_site_url())['host'];
	    if (strpos($url, 'youtube') > 0) {
	        return 'youtube';
	    } elseif (strpos($url, 'vimeo') > 0) {
	        return 'vimeo';
	    } elseif (strpos($url, $host) > 0) {
	        return 'selfhosted';
	    } else {
	        return 'unknown';
	    }
	}
	function tophive_rating_number($rating){
		return sprintf('%1.1f', $rating);
	}
	function tophive_review_timelapse( $date ){
		$timelapse = $this->tophive_time_elapsed_string( $date );
		echo '<small class="ec-ml-1 small review-time-ago">'. $timelapse .'</small>';
	}
	function tophive_time_elapsed_string($datetime, $full = false) {
	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	function tophive_before_single_content( $id ){
		$views = get_post_meta( $id, '_lp_count_course_view', true );

		$new_views = (int) $views + 1;
		return update_post_meta( $id, '_lp_count_course_view', $new_views, '' );
	}
	function tophive_render_single_reviews( $paged, $course_id ){
		if(function_exists('learn_press_get_course_review')){
			?>
			<div class="ec-mt-5 tophive-lp-reviews">
				<?php
					$course_review = learn_press_get_course_review( $course_id, $paged );
					if ( $course_review['total'] ) {
						$reviews = $course_review['reviews']; ?>
						<h3 class="ec-mb-4"><?php esc_html_e( 'Reviews', 'masterclass' ) ?></h3>
					    <div id="course-reviews">
					        <ul class="course-reviews-list">
								<?php foreach ( $reviews as $review ) { ?>
									<li class="ec-d-flex">
									    <div class="review-author ec-w-25">
											<?php echo get_avatar( $review->ID, 80, $default = '', $alt = '', $args = null ); ?>
									    </div>
									    <div class="review-text">
									        <h6 class="user-name ec-mb-3">
												<?php echo esc_attr($review->display_name); ?>
												<?php 

												$comment = get_comment( $review->comment_id );
												do_action( 'tophive/learnpress/single/review/timelapse', $comment->comment_date );
												?>
									        </h6>								    	
											<?php learn_press_course_review_template( 'rating-stars.php', array( 'rated' => $review->rate ) ); ?>
									        <div class="review-content">
												<p class="ec-mb-0 ec-pr-4"><?php echo esc_attr($review->content); ?></p>
									        </div>
									    </div>
									</li>
								<?php } ?>

								<?php if ( empty( $course_review['finish'] ) ) { ?>
					                <li class="loading"><?php esc_html_e( 'Loading...', 'masterclass' ); ?></li>
								<?php } ?>
					        </ul>
							<?php if ( empty( $course_review['finish'] ) ) { ?>
					            <button class="button course-review-load-more" id="course-review-load-more"
					                    data-paged="<?php echo tophive_sanitize_filter($course_review['paged']); ?>"><?php esc_html_e( 'Load More', 'masterclass' ); ?></button>
							<?php } ?>
					    </div>
					<?php }?>
				</div>
			<?php
		}
	}
	function tophive_render_single_feedback( $user, $course_id ){
		if(function_exists('learn_press_get_course_rate')){
			$course_rate_res = learn_press_get_course_rate( $course_id, false );
			$course_rate     = $course_rate_res['rated'];
			$total           = $course_rate_res['total']; 
	        
	        $percent = ( ! $course_rate ) ? 0 : min( 100, ( round( $course_rate * 2 ) / 2 ) * 20 );
			?>
			<div class="ec-mt-5 tophive-lp-feedback">
				<div class="ec-d-flex ec-justify-content-between ec-align-items-center">
					<h3 class="ec-mb-0"><?php esc_html_e( 'Students reviews', 'masterclass' ); ?></h3>
					<?php
						if ( $user->has_course_status( $course_id, array( 'enrolled', 'completed', 'finished' ) ) ) {
							if( function_exists('learn_press_get_user_rate') ){
								$is_reviewed = learn_press_get_user_rate( $course_id, get_current_user_id() );
								if(empty($is_reviewed->ID)){
									?>
										<button class="write-a-review button"><?php esc_html_e( 'Write a review', 'masterclass' ); ?></button>
										<div class="course-review-wrapper" id="course-review">
										    <div class="review-overlay"></div>
										    <div class="review-form" id="review-form">
										        <div class="form-overlay-review"></div>
										        <form>
										        	<a href="" class="ec-float-right close dashicons dashicons-no-alt"></a>
										            <h3>
														<?php esc_html_e( 'Write a review', 'masterclass' ); ?>
										            </h3>
										            <ul class="review-fields">
														<?php do_action( 'learn_press_before_review_fields' ); ?>
										                <li>
										                    <ul class="review-stars">
																<?php for ( $i = 1; $i <= 5; $i ++ ) { ?>
										                            <li class="review-title" title="<?php echo esc_attr($i); ?>">
										                                <span class="dashicons dashicons-star-empty"></span></li>
																<?php } ?>
										                    </ul>
										                </li>
										                <li>
										                    <label><?php esc_html__( 'Title', 'masterclass' ); ?></label>
										                    <input type="hidden" name="review_title" value="<?php esc_html_e( 'Review title', 'masterclass' ) ?>"/>
										                </li>
										                <li class="ec-mt-4">
										                    <textarea placeholder="<?php esc_html_e( 'Describe your personal experience about taking this course.it will help others', 'masterclass' ); ?>" name="review_content"></textarea>
										                </li>
										                
														<?php do_action( 'learn_press_after_review_fields' ); ?>
										                <li class="review-actions">
										                    <button type="button" class="submit-review button"
										                            data-id="<?php echo esc_attr($course_id); ?>"><?php esc_html_e( 'Add review', 'masterclass' ); ?></button>
										                    <span class="ajaxload"></span>
										                    <span class="error"></span>
															<?php wp_nonce_field( 'learn_press_course_review_' . $course_id, 'review-nonce' ); ?>
										                    <input type="hidden" name="rating" value="0">
										                    <input type="hidden" name="lp-ajax" value="add_review">
										                    <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($course_id); ?>">
										                </li>
										            </ul>
										        </form>
										    </div>
										</div>
									<?php
								}else{
									if( !$is_reviewed->comment_approved ){
										?>
											<button class="disabled button ec-btn-sm"><?php esc_html_e( 'Review pending approval', 'masterclass' ); ?></button>

										<?php

									}else{
										?>
											<button class="disabled button ec-btn-sm"><?php esc_html_e( 'Reviewed', 'masterclass' ); ?></button>
										<?php
									}
									?>
									<?php
								}
							}
						}
					?>
				</div>

				<div class="course-rate ec-row">
				    <div class="ec-col-xl-3 ec-text-center course-rate-avg">
						<h1 class="theme-primary-color ec-mb-2 ec-mt-2"><?php echo apply_filters( 'tophive/learnpress/single/rating/number', $course_rate ); ?></h1>
						<div class="review-stars-rated th-lp-cr-single">
	                        <div class="review-stars empty theme-primary-color"></div>
	                        <div class="review-stars filled" style="width:<?php echo esc_attr($percent); ?>%;"></div>
	                    </div>
						<h6><?php echo esc_html($total) . esc_html__( ' Ratings', 'masterclass' ); ?></h6>
				    </div>
				    <div class="ec-col-xl-9 ec-text-center">
						<?php
						if ( isset( $course_rate_res['items'] ) && ! empty( $course_rate_res['items'] ) ):
							foreach ( $course_rate_res['items'] as $item ):
								?>
				                <div class="course-rate-individual">
				                    <div class="review-stars-rated th-lp-cr-single">
		                                <div class="review-stars empty theme-primary-color"></div>
		                                <div class="review-stars filled theme-primary-color" style="width:<?php echo esc_attr($item['rated'] * 20) ?>%;"></div>
		                            </div>
				                    <div class="review-bar">
				                        <div class="rating" style="width:<?php echo esc_attr($item['percent']); ?>% "></div>
				                    </div>
				                    <span class="review-percent"><?php echo esc_html( $item['percent'] ); ?>%</span>
				                </div>
							<?php
							endforeach;
						endif;
						?>
				    </div>
				</div>
			</div>
			<?php
		}
	}
	function tophive_render_single_enrolled_progress($course, $user){
		$course_data       = $user->get_course_data( $course->get_id() );
		$course_results    = $course_data->get_results( false );

		$passing_condition = $course->get_passing_condition();


		if( !$user->has_enrolled_course( $course->get_id() ) ){
			return;
		}

		?>
			<div class="learn-press-course-results-progress">
			    <div class="course-progress">

					<?php if ( false !== ( $heading = apply_filters( 'learn-press/course/result-heading', esc_html__( 'Course results', 'masterclass' ) ) ) ) { ?>
			            <h4 class="lp-course-progress-heading">
							<?php echo esc_html( $heading ); ?>
			            </h4>
					<?php } ?>

			        <div class="lp-course-status">
			            <span class="number"><?php echo round( $course_results['result'], 2 ); ?><span
			                        class="percentage-sign">%</span></span>
						<?php if ( $grade = $course_results['grade'] ) { ?>
			                <span class="lp-label grade <?php echo esc_attr( $grade ); ?>">
							<?php learn_press_course_grade_html( $grade ); ?>
							</span>
						<?php } ?>
			        </div>

			        <div class="learn-press-progress lp-course-progress <?php echo esc_attr($course_data->is_passed()) ? ' passed' : ''; ?>"
			             data-value="<?php echo esc_attr( $course_results['result'] ); ?>"
			             data-passing-condition="<?php echo esc_attr($passing_condition); ?>">
			            <div class="progress-bg lp-progress-bar">
			                <div class="progress-active lp-progress-value" style="left: <?php echo esc_attr($course_results['result']); ?>%;">
			                </div>
			            </div>
			            <div class="lp-passing-conditional"
			                 data-content="<?php printf( esc_html__( 'Passing condition: %s%%', 'masterclass' ), $passing_condition ); ?>"
			                 style="left: <?php echo esc_attr($passing_condition); ?>%;">
			            </div>
			        </div>
			    </div>

			</div>
		<?php
	}
	function tophive_render_single_header_price( $id ){
		$price = apply_filters( 'tophive/learnpress/course-meta/price', $id );
		
    	echo tophive_sanitize_filter($price['full_float']);
	}
	function tophive_render_single_header_purchase_button($course, $user){
		$id = $course->get_id();
		$price = apply_filters( 'tophive/learnpress/course-meta/price', $id );
		if( !$user->has_enrolled_course( $course->get_id() ) ){
			?>
				<form name="purchase-course" class="purchase-course ec-mt-3" method="post" enctype="multipart/form-data">
					<?php do_action( 'learn-press/before-purchase-button' ); ?>

			        <input type="hidden" name="purchase-course" value="<?php echo esc_attr( $course->get_id() ); ?>"/>
			        <input type="hidden" name="purchase-course-nonce"
			               value="<?php echo esc_attr( LP_Nonce_Helper::create_course( 'purchase' ) ); ?>"/>
		            <?php 
		            	if(!empty(get_post_meta( $id, '_lp_external_link_buy_course', true ))){
	            			?>
	            				<a target="_blank" href="<?php echo get_post_meta( $id, '_lp_external_link_buy_course', true ) ?>" class="button lp-button button-purchase-course">
									<?php esc_html_e( 'Proceed To Payment', 'masterclass' ); ?>
						        </a>
	            			<?php
		            	}else{
		            		?>
		            			<button class="lp-button button-purchase-course">

									<?php 
										if( $price['free'] ){
											?>
												<svg width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-arrow-right-circle-fill ec-mr-1 ec-align-top" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-11.5.5a.5.5 0 0 1 0-1h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5z"/>
												</svg>
											<?php
											esc_html_e( 'Enroll this course', 'masterclass' ); 
										}else{
											?>
												<svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-basket2-fill ec-mr-1 ec-align-top" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												  	<path fill-rule="evenodd" d="M11.314 1.036a.5.5 0 0 1 .65.278l2 5a.5.5 0 1 1-.928.372l-2-5a.5.5 0 0 1 .278-.65zm-6.628 0a.5.5 0 0 0-.65.278l-2 5a.5.5 0 1 0 .928.372l2-5a.5.5 0 0 0-.278-.65z"/>
												  	<path fill-rule="evenodd" d="M1.5 7a.5.5 0 0 0-.489.605l1.5 7A.5.5 0 0 0 3 15h10a.5.5 0 0 0 .489-.395l1.5-7A.5.5 0 0 0 14.5 7h-13zM4 10a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0v-2zm3 0a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0v-2zm4-1a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0v-2a1 1 0 0 0-1-1z"/>
												  	<path d="M0 6.5A.5.5 0 0 1 .5 6h15a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-1z"/>
												</svg>
											<?php
											esc_html_e( 'Buy this course', 'masterclass' ); 
										}
									?>
						        </button>
		            		<?php
		            	}
		            ?>

					<?php do_action( 'learn-press/after-purchase-button' ); ?>

			    </form>
			<?php
		}else{
			?>
				<form name="purchase-course" class="purchase-course ec-mt-3" method="post" enctype="multipart/form-data">
					<?php do_action( 'learn-press/before-purchase-button' ); ?>

			        <input type="hidden" name="purchase-course" value="<?php echo esc_attr( $course->get_id() ); ?>"/>
			        <input type="hidden" name="purchase-course-nonce"
			               value="<?php echo esc_attr( LP_Nonce_Helper::create_course( 'purchase' ) ); ?>"/>

			        <button class="lp-button button-purchase-course">
						<?php esc_html_e( 'You Already Enrolled', 'masterclass' ) ?>
			        </button>

					<?php do_action( 'learn-press/after-purchase-button' ); ?>

			    </form>
			<?php
		}
	}
	function tophive_render_single_curriculum( $id, $course, $user){
		$curriculum = $course->get_curriculum();
		if ( $course->get_curriculum() ) {
			?>
            <ul class="curriculum-sections">
				<?php foreach ( $curriculum as $section ) {

					$title = $section->get_title();
					$user_course = $user->get_course_data( $id );
					?>

						<li<?php $section->main_class(); ?> id="section-<?php echo esc_attr($section->get_slug()); ?>"
                                    data-id="<?php echo esc_attr($section->get_slug()); ?>"
                                    data-section-id="<?php echo esc_attr($section->get_id()); ?>">
							<div class="section-header">

							    <div class="section-left">

									<?php if ( $title ) { ?>
							            <h5 class="section-title"><?php echo esc_attr($title); ?></h5>
									<?php } ?>

									<?php if ( $description = $section->get_description() ) { ?>
							            <p class="section-desc"><?php echo esc_attr($description); ?></p>
									<?php } ?>

							    </div>

								<?php if ( $user->has_enrolled_course( $section->get_course_id() ) ) { ?>

									<?php $percent = $user_course->get_percent_completed_items( '', $section->get_id() ); ?>

							        <div class="section-meta">
							            <div class="learn-press-progress section-progress" title="<?php echo intval( $percent ); ?>%">
							                <div class="progress-bg">
							                    <div class="progress-active primary-background-color" style="left: <?php echo esc_attr($percent); ?>%;"></div>
							                </div>
							            </div>
							            <span class="step"><?php printf( esc_html__( '%d/%d', 'masterclass' ), $user_course->get_completed_items( '', false, $section->get_id() ), $section->count_items( '', false ) ); ?></span>
							            <span class="collapse"></span>
							        </div>

								<?php } ?>

							</div>
							<?php if ( $items = $section->get_items() ) { ?>

							    <ul class="section-content">
									<?php foreach ( $items as $item ) { ?>

							            <li class="<?php echo join( ' ', $item->get_class() ); ?>">
											<?php
												if ( $item->is_visible() ) {
													do_action( 'learn-press/begin-section-loop-item', $item );
													?>
								                        <a 
								                        	class="section-item-link section-item-popup ec-d-flex ec-align-items-center" 
								                        	data-lesson-id="<?php echo esc_attr($item->get_id()); ?>" 
								                        	data-course-id="<?php echo esc_attr($section->get_course_id()); ?>"
								                        	href="#"
								                        	>
								                        	<span class="ec-d-inline-block">
								                        	<?php 
								                        		if( 'lp_lesson' == $item->get_item_type() ){
								                        			?>
								                        				<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-text" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
																		  <path fill-rule="evenodd" d="M4 1h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H4z"/>
																		  <path fill-rule="evenodd" d="M4.5 10.5A.5.5 0 0 1 5 10h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0-2A.5.5 0 0 1 5 4h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z"/>
																		</svg>
								                        			<?php
								                        		}elseif( 'lp_quiz' == $item->get_item_type() ){
								                        			?>
								                        				<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-lightning" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
																		  <path fill-rule="evenodd" d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09zM4.157 8.5H7a.5.5 0 0 1 .478.647L6.11 13.59l5.732-6.09H9a.5.5 0 0 1-.478-.647L9.89 2.41 4.157 8.5z"/>
																		</svg>
								                        			<?php
								                        		}
								                        	?>
								                        	</span>
															<span class="item-name"><?php echo esc_attr($item->get_title( 'display' )); ?></span>
															<div class="course-item-meta ec-ml-auto">
																<?php do_action( 'learn-press/course-section-item/before-' . $item->get_item_type() . '-meta', $item ); ?>
																<?php if ( $item->is_preview() ) { ?>
																	<?php $course_id = $section->get_course_id(); ?>
																	<?php if ( get_post_meta( $course_id, '_lp_required_enroll', true ) == 'yes' ) { ?>
															            <i class="item-meta course-item-status"
															               data-preview="<?php esc_html_e( 'Preview', 'masterclass' ); ?>"></i>
																	<?php } ?>
																<?php } else { ?>
															        <svg class="ec-ml-1" enable-background="new 0 0 551.13 551.13" height="14" viewBox="0 0 551.13 551.13" width="14" xmlns="http://www.w3.org/2000/svg"><path d="m465.016 206.674h-17.223v-34.446c0-94.961-77.267-172.228-172.228-172.228s-172.228 77.267-172.228 172.228v34.446h-17.223c-9.52 0-17.223 7.703-17.223 17.223v310.011c0 9.52 7.703 17.223 17.223 17.223h378.902c9.52 0 17.223-7.703 17.223-17.223v-310.011c0-9.52-7.703-17.223-17.223-17.223zm-327.233-34.446c0-75.972 61.81-137.783 137.783-137.783s137.783 61.81 137.783 137.783v34.446h-275.566zm310.01 344.457h-344.456v-275.566h344.456z"/><path d="m258.342 373.623v74.17h34.446v-74.17c10.11-6 17.223-16.556 17.223-29.167 0-19.025-15.421-34.446-34.446-34.446s-34.446 15.421-34.446 34.446c0 12.61 7.113 23.167 17.223 29.167z"/></svg>
																<?php } ?>

																<?php do_action( 'learn-press/course-section-item/after-' . $item->get_item_type() . '-meta', $item ); ?>
															</div>

								                        </a>

													<?php
													do_action( 'learn-press/end-section-loop-item', $item );
												}
											?>

							            </li>

									<?php } ?>

							    </ul>

							<?php } else { ?>

								<?php learn_press_display_message( esc_html__( 'No items in this section', 'masterclass' ) ); ?>

							<?php } ?>

						</li>
					<?php
				} ?>
            </ul>

		<?php } else {

			echo apply_filters( 'learn_press_course_curriculum_empty', esc_html__( 'Curriculum is empty', 'masterclass' ) );
		}
	}
	function tophive_render_single_header_wishlist_button($id){
		if (is_user_logged_in()) {
				global $current_user;
					$added_to_wishlist = get_user_meta($current_user->ID, '_lpr_wish_list', true);
					if( !empty($added_to_wishlist) ){
						$wishlist = in_array($id, $added_to_wishlist) ? 'on ' : '';
				    	$wishlist_text = in_array($id, $added_to_wishlist) ? esc_html__('Remove from wishlist', 'masterclass') : esc_html__('Add to wishlist', 'masterclass');

					}else{
						$wishlist = '';
				    	$wishlist_text = esc_html__('Add to wishlist', 'masterclass');
					}
			    ?>
			    <div 
			    	class="hover-info-wishlist course-single-wishlist<?php echo !is_rtl() ? ' ec-ml-2' : '' ?>">
			    	<a class="<?php echo esc_attr($wishlist); ?>hover-wishlist-<?php echo esc_attr($id); ?> th-tooltip" data-id="<?php echo esc_attr($id);?>" data-nonce="<?php echo wp_create_nonce( 'course-toggle-wishlist' ); ?>" href="#">
			    		<i class="ti-heart"></i>
			    		<span class="th-tooltip-text"><?php echo tophive_sanitize_attr($wishlist_text); ?></span>
			    	</a>
			    </div>
			<?php
		}
	}
	function tophive_render_single_header_meta($id){
		$course_meta = apply_filters( 'tophive/learnpress/course-meta', $id );

		$html = '<ul class="tophive_lp_heading_meta">';
			if( !empty($course_meta['tag']) ){
	        	$html .= '<li class="no-dot">'. $course_meta['tag']['html'] .'</li>';
			}
			if(function_exists('learn_press_get_course_rate')){
		        $html .= '<li>'. $course_meta['rating']['html2'] .'</li>';
		        $html .= '<li>'. $course_meta['rating']['raw']['reviews'] . esc_html__( ' Reviews', 'masterclass' ) . '</li>';
	        }
	        $html .= '<li>'. $course_meta['student']['html'] . '</li>';
        $html .= '</ul>';


        $html .= '<ul class="tophive_lp_heading_meta">';
	        $html .= '<li class="ec-text-info course-date">'. $course_meta['updated']['html'] .'</li>';
	        $html .= '<li>'. $course_meta['category']['html'] .'</li>';
	        $html .= '<li>'. esc_html__( 'Course by: ', 'masterclass' ) . $course_meta['instructor_url']['html'] . '</li>';
	        $html .= '<li>'. $course_meta['lessons']['html'] . '</li>';
	        $html .= '<li>'. $course_meta['duration']['html'] . '</li>';
        $html .= '</ul>';
		echo tophive_sanitize_filter($html);
	}
	
}
function Tophive_LP_Single_Course() {
	return Tophive_LP_Single_Course::get_instance();
}

if ( top_hive()->is_learnpress_active() ) {
	Tophive_LP_Single_Course();
}

?>
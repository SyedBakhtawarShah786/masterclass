<?php  
	
class Tophive_LP_Quiz{
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
			add_action( 'tophive/learnpress/single/quiz/main', array($this, 'tophive_render_quiz_lite'), 10 , 2 );
			add_action( 'wp_ajax_tophive_quizz', array( $this, 'tophive_lp_quiz_question' ) );
			add_action( 'wp_ajax_nopriv_tophive_quizz', array( $this, 'tophive_lp_quiz_question' ) );
		}
	}
	function tophive_lp_quiz_question(){
		$data = $_REQUEST['data'];
		$hook 		= $data['hook'];
		
		switch ($hook) {
			case 'quiz_question_nav':
				$html = $this->quiz_question_nav_ajax( $data );
				break;
			case 'quiz_question_complete':
				$html = $this->quiz_question_complete_ajax( $data );
				break;
			
			default:
				// code...
				break;
		}
		wp_send_json($html);
	}
	function quiz_question_complete_ajax( $data ){
		$quiz_id = $data['quiz_id'];
		$html = $this->get_quiz_result_html($quiz_id);
		return $html;
	}
	function get_quiz_result_html($quiz_id){
		$marks = $this->quiz_result_marks( $quiz_id );
		// $percent = $this->quiz_result_percent( $quiz_id );

		$html = '<div class="quiz-result-section ec-text-center">';
		$html .= '<h6>'. esc_html__( 'Your Got Total : ', 'masterclass' ) . $marks . '<h6>';
		$html .= '</div>';
		return $html;
	}
	function quiz_result_marks( $quiz_id ){
		$marks = 0;
		$quiz = LP_Quiz::get_quiz($quiz_id);
		$question_ids = $quiz->get_questions();
		foreach ($question_ids as $question_id) {
			$marks += $this->count_quiz_mark( $question_id ); 
		}
		return $marks;
	}
	function count_quiz_mark( $question_id ){
		$question    = LP_Question::get_question( $question_id );
		$get_total_mark = !empty(get_post_meta( $question_id, '_lp_mark', true )) ? (int)get_post_meta( $question_id, '_lp_mark', true ) : 1;

		$correct_answers = [];
		foreach ($question->get_data('answer_options') as $value) {
			if( $value['is_true'] === 'yes' ){
				array_push($correct_answers, $value['value']);
			}
		}
		$given_answer = get_post_meta( $question_id, '_current_answer_' . get_current_user_id(), false )[0];
		return $correct_answers === $given_answer ? $get_total_mark : 0;
	}
	function get_question_answer( $question_id ){
		$current_id 	= $data['current_id'];
		$answer = get_post_meta( $question_id, '_current_answer_' . $current_id, false );
		return $answer[0];
	}
	function get_question_correct_answer( $question_id ){}
	function check_question_answer( $question_id ){}
	function quiz_question_nav_ajax($data){
		$question_id = $data['id'];
		$lesson_id 	= $data['lesson_id'];
		$answer 	= $data['answer'];
		$current_id 	= $data['current_id'];
		$current_user = get_current_user_id();

		update_post_meta( $current_id, '_current_answer_' . $current_user, $answer );

		$html = '<div class="tophive-quiz-containers">';
		$quiz = LP_Quiz::get_quiz( $lesson_id );
		
		$html .= $this->get_question_html( $question_id );
		
		$html .= '</div>';
		$html .= $this->get_quiz_footer( $quiz, $question_id );
		return $html;
	}
	function tophive_render_quiz_lite( $lesson_id, $course_id ){
		$html = '<div class="tophive-quiz-containers">';

		$html .= $this->get_quiz_lite_html( $lesson_id, $course_id );
		
		$html .= '</div>';
		return $html;
	}
	function get_quiz_lite_html( $lesson_id, $course_id ){
		$quiz = LP_Quiz::get_quiz( $lesson_id );
		$question_ids = $quiz->get_questions();
		
		$question_count = count($question_ids);
		$duration = $quiz->get_duration();

		$quiz_link = get_the_permalink( $course_id );
		$site_url = wp_parse_url(esc_url(site_url()))['path'];
		$url = wp_parse_url(get_the_permalink( $lesson_id ))['path'];
		$url = str_replace($site_url, '', $url);

		$html = '<div class="ec-text-center">';
			$html .= '<h5 class="ec-mb-4">'. esc_html__( 'Quiz On: ', 'masterclass' ) . get_the_title( $lesson_id ) .'</h5>';
				$html .= '<p class="ec-mb-2">'. esc_html__( 'Total Questions : ', 'masterclass' ) . $question_count .'</p>';
				$html .= '<p class="ec-mb-2">'. esc_html__( 'Passing grade : ', 'masterclass' ) . get_post_meta( $lesson_id, '_lp_passing_grade', true ) .'%</p>';
				$html .= '<p class="ec-mb-5">'. esc_html__( 'Total Time : ', 'masterclass' ) . $duration->get_minutes() . esc_html__( ' Minutes', 'masterclass' ) . '</p>';
			if( current_user_can( 'administrator' ) || $is_author ){
				$html .= '<a href="'. $quiz_link . $url .'" target="_blank" class="button">'. esc_html__( 'Take this quizz', 'masterclass' ) .'</a>';
			}
		$html .= '</div>';
		return $html;
	}
	function tophive_render_quiz( $lesson_id ){
		$html = '<div class="tophive-quiz-containers">';
		$quiz = LP_Quiz::get_quiz( $lesson_id );
		$question_id = $quiz->get_question_at( 0 );

		$html .= $this->get_question_html( $question_id );
		
		$html .= '</div>';
		$html .= $this->get_quiz_footer( $quiz, $question_id );
		return $html;
	}
	function get_quiz_footer( $quiz, $question_id ){
		$html ='<div class="tophive-quizz-footer-content">';
			$html .= '<div class="ec-row">';
				$html .= '<div class="ec-col-md-6">';
					$html .= $this->get_quiz_nav( $quiz, $question_id );
					$html .= '<a href="" class="button ec-btn-sm tophive-finish-quiz" data-quiz-id="'. $quiz->get_id() .'">'. esc_html__( 'Finish', 'masterclass' ) .'</a>';
				$html .= '</div>';
				$html .= '<div class="ec-col-md-6">';
				$html .= '</div>';
			$html .= '</div>';
		return $html .= '</div>';
	}
	
	function get_quiz_nav( $quiz, $question_id ){
		$html;

		$prev_id = $quiz->get_prev_question($question_id);
		$next_id = $quiz->get_next_question($question_id);

		if( $prev_id ){
			$html .= '<a href="#" class="button ec-btn-sm tophive-quiz-nav" data-question-id="'. $question_id .'" data-id="'. $prev_id .'" data-lesson-id="'. $quiz->get_id() .'">'. esc_html__( 'Prev', 'masterclass' ) .'</a>';
		}
		if( $next_id ){
			$html .= '<a href="#" class="button ec-btn-sm tophive-quiz-nav" data-question-id="'. $question_id .'" data-id="'. $next_id .'" data-lesson-id="'. $quiz->get_id() .'">'. esc_html__( 'Next', 'masterclass' ) .'</a>';
		}
		return $html;
	}
	function get_question_html( $question_id ){
		$current_user = get_current_user_id();
		$did_answer = get_post_meta( $question_id, '_current_answer_' . $current_user, false );
		$question = LP_Question::get_question( $question_id );
		$answers = $question->get_answers();
		$type = $question->get_type();
		$html .= '<div class="tophive-quiz-question">';
		switch ($type) {
			case 'multi_choice':
				$html .= $this->multi_choice_question( $question_id, $answers, $did_answer );
				break;
			case 'single_choice':
				$html .= $this->single_choice_question( $question_id, $answers, $did_answer );
				break;
			case 'true_or_false':
				$html .= $this->true_false_question( $question_id, $answers, $did_answer );
				break;
			case 'fill_in_blank':
				$html .= $this->fill_blank_question( $question_id, $answers, $did_answer );
				break;
			
			default:
				// code...
				break;
		}
		return $html .= '</div>';
	}
	function multi_choice_question( $question_id, $answers, $did_answer ){


		$html = '<h6 class="question-title">' . get_the_title( $question_id ) . '</h6>';
		$html .= '<ul class="answer-options">';

			foreach ( $answers as $k => $answer ) {
				$checked = in_array( $answer->get_value(), $did_answer[0]) ? 'checked' : '';
		        $html .= '<li class="answer-option">
		        	<input type="checkbox" class="option-check" name="learn-press-question-'. $question_id .'"
		                   value="' . $answer->get_value() . '" '. $checked .'/>
		            <div class="option-title">
		                <div class="option-title-content">' . $answer->get_title( 'display' ) . '</div>
		            </div>
		        </li>';

			}
		return $html .= '</ul>';
	}
	function single_choice_question( $question_id, $answers, $did_answer ){

		$html = '<h6 class="question-title">' . get_the_title( $question_id ) . '</h6>';
		$html .= '<ul class="answer-options">';

			foreach ( $answers as $k => $answer ) {
				$checked = in_array( $answer->get_value(), $did_answer[0]) ? 'checked' : '';
		        $html .= '<li class="answer-option">
		        	<input type="radio" class="option-check" name="learn-press-question-'. $question_id .'"
		                   value="' . $answer->get_value() . '" '. $checked .'/>
		            <div class="option-title">
		                <div class="option-title-content">' . $answer->get_title( 'display' ) . '</div>
		            </div>
		        </li>';

			}
		return $html .= '</ul>';
	}
	function true_false_question( $question_id, $answers, $did_answer ){

		$html = '<h6 class="question-title">' . get_the_title( $question_id ) . '</h6>';
		$html .= '<ul class="answer-options">';

			foreach ( $answers as $k => $answer ) {
				$checked = in_array( $answer->get_value(), $did_answer[0]) ? 'checked' : '';
		        $html .= '<li class="answer-option">
		        	<input type="radio" class="option-check" name="learn-press-question-'. $question_id .'"
		                   value="' . $answer->get_value() . '" '. $checked .'/>
		            <div class="option-title">
		                <div class="option-title-content">' . $answer->get_title( 'display' ) . '</div>
		            </div>
		        </li>';

			}
		return $html .= '</ul>';
	}
	function fill_blank_question( $question_id, $answers, $did_answer ){

		return '<h6>This Quiz type is not supported yet in Masterclass</h6>';
	}
	function lesson_empty_html($lesson_id, $is_author){
		$html = '<div class="tophive-learnpress-empty-lesson">';
			$html .= '<h5>'. esc_html__( 'Lesson content empty', 'masterclass' ) .'</h5>';
				$html .= '<p>'. esc_html__( 'The content is empty.You can move to next lesson or previous lesson from the curricilum on right side', 'masterclass' ) .'</p>';
			if( current_user_can( 'administrator' ) || $is_author ){
				$html .= '<a href="'. get_edit_post_link( $lesson_id ) .'" target="_blank" class="button ec-btn-sm">'. esc_html__( '+ Add Content', 'masterclass' ) .'</a>';
			}
		$html .= '</div>';
		return $html;
	}
	function lesson_protected_html( $course_id ){
		$html = '<div class="tophive-learnpress-empty-lesson">';
			$html .= '<h2><i class="ti-lock"></i></h2>';
			$html .= '<h5>'. esc_html__( 'The content is locked', 'masterclass' ) .'</h5>';
				$html .= '<p>'. esc_html__( 'The content is locked.You need to login and enroll or enroll this course to get the preview of this section', 'masterclass' ) .'</p>';
			
				$html .= '<form name="purchase-course" class="purchase-course ec-mt-3" method="post" enctype="multipart/form-data">
					        <input type="hidden" name="purchase-course" value="'. esc_attr( $course_id ) .'"/>
					        <input type="hidden" name="purchase-course-nonce"
					               value="' . esc_attr( LP_Nonce_Helper::create_course( 'purchase' ) ) .'"/>

					        <button class="button lp-button button-purchase-course">
								<i class="eicon-basket-solid"></i>'. esc_html( apply_filters( 'learn-press/purchase-course-button-text', esc_html__( ' Enroll this course', 'masterclass' ) ) ) . '
					        </button>

					    </form>';
		$html .= '</div>';
		return $html;
	}
	function tophive_learnpress_content_popup( $course_id, $user, $curriculum, $title ){
		?>
			<div class="tophive-learpress-content-popup-container">
				<div class="tophive-learpress-content-popup ec-d-md-flex">
					<div class="tophive-learnpress-content-header">
						<div class="ec-row ec-align-items-center">
							<div class="ec-col-7">
								<h6><?php esc_html_e( 'Loading...', 'masterclass' ) ?></h6>
								<p class="lead small"><?php esc_html_e( 'Course: ' . $title, 'masterclass' ); ?></p>
							</div>
							<div class="ec-col-5 ec-text-right">
								<?php if( $user->is_instructor() || $user->is_admin() ){ ?>
									<span class="tophive-lesson-edit">
																		
									</span>
								<?php } ?>
								<?php if( $user->has_enrolled_course($course_id) ){ ?>
									<span class="tophive-lesson-complete">
										<a class="button">
											Complete
										</a>								
									</span>
								<?php } ?>
								<span class="tophive-lesson-close">
									<a class="button tophive-lp-close-content">
										<i class="ti-close ec-mt-1"></i>
									</a>
								</span>
							</div>
						</div>
					</div>
					<div class="tophive-learnpress-content-main">
						<div class="item-loader">
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="137px" height="137px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
								<path d="M35 50A15 15 0 0 0 65 50A15 16.3 0 0 1 35 50" fill="#292664" stroke="none" transform="rotate(177.696 50 50.65)">
								  <animateTransform attributeName="transform" type="rotate" dur="0.5025125628140703s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50.65;360 50 50.65"></animateTransform>
								</path>
							</svg>
						</div>
						<div class="tophive-lesson-inner"></div>
					</div>
					<div class="tophive-learnpress-content-sidebar">
						<ul class="curriculum-sections">
							<?php foreach ( $curriculum as $section ) {
								$user = LP_Global::user();
								$title = $section->get_title();
								$user_course = $user->get_course_data( get_the_ID() );
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
										            <p class="section-desc"><?php echo tophive_sanitize_filter($description); ?></p>
												<?php } ?>

										    </div>

											<?php if ( $user->has_enrolled_course( $section->get_course_id() ) ) { ?>

												<?php $percent = $user_course->get_percent_completed_items( '', $section->get_id() ); ?>

										        <div class="section-meta">
										            <div class="learn-press-progress section-progress" title="<?php echo intval( $percent ); ?>%">
										                <div class="progress-bg">
										                    <div class="progress-active primary-background-color" style="left: <?php echo  esc_attr($percent); ?>%;"></div>
										                </div>
										            </div>
										            <span class="step"><?php printf( __( '%d/%d', 'masterclass' ), $user_course->get_completed_items( '', false, $section->get_id() ), $section->count_items( '', false ) ); ?></span>
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
											                        	class="section-item-link" 
											                        	data-lesson-id="<?php echo esc_attr($item->get_id()); ?>" 
											                        	data-course-id="<?php echo esc_attr($section->get_course_id()); ?>"
											                        	href="#"
											                        	>
																		<?php learn_press_get_template( 'single-course/section/content-item.php', array(
																			'item'    => $item,
																			'section' => $section
																		) ); ?>
											                        </a>

																<?php
																do_action( 'learn-press/end-section-loop-item', $item );
															}
														?>

										            </li>

												<?php } ?>

										    </ul>

										<?php } else { ?>

											<?php learn_press_display_message( __( 'No items in this section', 'masterclass' ) ); ?>

										<?php } ?>

									</li>
								<?php
							} ?>
			            </ul>
					</div>
				</div>
			</div>
		<?php
	}
}
function Tophive_LP_Quiz() {
	return Tophive_LP_Quiz::get_instance();
}

if ( top_hive()->is_learnpress_active() ) {
	Tophive_LP_Quiz();
}

?>
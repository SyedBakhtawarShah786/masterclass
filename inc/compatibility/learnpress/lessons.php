<?php  
	
class Tophive_LP_Lessons{
	static $_instance;

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
			require_once get_template_directory() . '/inc/compatibility/learnpress/quiz.php';
			
	        add_action( 'tophive/learnpress/single/lessons/popup', array( $this, 'tophive_learnpress_content_popup' ), 10, 4 );

	        add_action( 'wp_ajax_pull_lesson_content', array($this, 'lesson_content_server') );
	        add_action( 'wp_ajax_nopriv_pull_lesson_content', array($this, 'lesson_content_server') );
		}
	}
	function lesson_content_server(){
		global $wp_embed;
		$lesson_id = $_REQUEST['lesson_id'];

		$post_type = get_post_type( $lesson_id );

		$course_id = $_REQUEST['course_id'];
		$author_id = get_post_field( 'post_author', $lesson_id );

		$loggedin = is_user_logged_in();

		if( !$loggedin ){
			$enrolled = false;
		}else{
			$enrolled = learn_press_is_enrolled_course( $course_id, get_current_user_id() );
		}
		$title = get_the_title( $lesson_id );
		
		$content = get_the_content( null, false, $lesson_id ); 
		$is_author = $author_id == get_current_user_id() ? true : false;

		$is_preview = 'yes' == get_post_meta( $lesson_id, '_lp_preview', true ) ? true : false;

		if( $enrolled || current_user_can( 'administrator' ) || $is_author || $is_preview ){
			if( 'lp_lesson' == $post_type ){
				if( !empty($content) ){
					if( has_shortcode( $content, 'embed' ) ){
						$html = $wp_embed->run_shortcode($content);
					}else{
						$html = do_shortcode( $content );
					}
				}else{
					$html = $this->lesson_empty_html($lesson_id, $is_author);
				}
			}elseif( 'lp_quiz' == $post_type ){
				$html = apply_filters( 'tophive/learnpress/single/quiz/main', $lesson_id, $course_id );
			}
		}
		else{
			$html = $this->lesson_protected_html($course_id);
		}

		$edit_btn = '';
		if( current_user_can('administrator') || $is_author ){
			$url = get_edit_post_link( $lesson_id );
			$edit_btn = '<a target="_blank" href="'. $url .'" class="button">'. esc_html__( 'Edit', 'masterclass' ) .'</a>';
		}
		$link_btn = '';
		if( 'lp_lesson' == get_post_type( $lesson_id ) ){
			if( $enrolled || current_user_can('administrator') || $is_author ){
				$course_link = get_the_permalink($course_id);
				$site_url = wp_parse_url(esc_url(site_url()))['path'];
				$url = wp_parse_url(get_the_permalink( $lesson_id ))['path'];
				$url = str_replace($site_url, '', $url);
				$edit_btn = '<a target="_blank" href="'. $course_link . $url .'" class="button">'. esc_html__( 'Go to Lesson', 'masterclass' ) .'</a>';
			}
		}
		
		wp_send_json( 
			array(
				'title' => $title, 
				'html' => $html,
				'edit' => $edit_btn,
				'link' => $link_btn
			) 
		);
	}
	function lesson_empty_html($lesson_id, $is_author){
		$html = '<div class="tophive-learnpress-empty-lesson">';
			$html .= '<h5>'. esc_html__( 'Lesson content empty', 'masterclass' ) .'</h5>';
				$html .= '<p>'. esc_html__( 'The lesson content is empty. You can move to next lesson or previous lesson from the curricilum on right side', 'masterclass' ) .'</p>';
			if( current_user_can( 'administrator' ) || $is_author ){
				$html .= '<a href="'. get_edit_post_link( $lesson_id ) .'" target="_blank" class="button">'. esc_html__( '+ Add Content', 'masterclass' ) .'</a>';
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
	function tophive_learnpress_content_popup( $course, $user ){
		$id = $course->get_id();
		$curriculum = $course->get_curriculum(); 
		$title = get_the_title($id);
		?>
			<div class="tophive-learpress-content-popup-container">
				<div class="tophive-learpress-content-popup ec-d-md-flex">
					<div class="tophive-learnpress-content-header">
						<div class="ec-row ec-align-items-center">
							<div class="ec-col-12 ec-col-sm-7 ec-col-md-7 ec-col-md-9">
								<h6><?php esc_html_e( 'Loading...', 'masterclass' ) ?></h6>
								<p class="lead small"><?php esc_html_e( 'Course: ' . $title, 'masterclass' ); ?></p>
							</div>
							<div class="ec-col-12 ec-text-right ec-col-sm-5 ec-col-md-5 ec-col-lg-3">
								<?php if( $user->is_instructor() || $user->is_admin() ){ ?>
									<span class="tophive-lesson-edit"></span>
								<?php } ?>
								<?php if( $user->has_enrolled_course($id) ){ ?>
									<span class="tophive-lesson-complete"></span>
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
						<?php do_action( 'tophive/learnpress/single/curriculum', $id, $course, $user ); ?>
					</div>
				</div>
			</div>
		<?php
	}
}
function Tophive_LP_Lessons() {
	return Tophive_LP_Lessons::get_instance();
}

if ( top_hive()->is_learnpress_active() ) {
	Tophive_LP_Lessons();
}

?>
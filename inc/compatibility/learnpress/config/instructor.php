<?php

class Tophive_LP_Instructor {
	private $panel = 'learpress_panel';
	private $section = 'learpress_panel_instructor';
	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ) );
		add_filter( 'tophive/learnpress/page/instructor/courses', array($this, 'tophive_lp_instructor_courses'), 10, 4 );
		add_action( 'wp_ajax_pull_courses', array($this, 'learnpress_post_loader') );
		add_action( 'wp_ajax_nopriv_pull_courses', array($this, 'learnpress_post_loader') );
		if ( is_admin() || is_customize_preview() ) {
			add_filter( 'Tophive_Control_Args', array( $this, 'add_instructor_url' ), 35 );
		}
	}
	function learnpress_post_loader(){
		if(!check_ajax_referer( 'th_mc_nonce', 'security' )){
			return wp_send_json( 'invalid secirity' );
		}
		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$userid = $_REQUEST['userid'];

		$args = array(
			'author' => $userid,
			'post_type' => 'lp_course',
			'post_status' => 'publish',
			'posts_per_page' => 4,
			'offset' => $page,
		);
		$query = new WP_Query($args);
		$course_html = '';
		if($query->have_posts()){
			while ($query->have_posts()) {
		    	$query->the_post();
		    	$course = \LP_Global::course();

				$course_html .= apply_filters( 
					'tophive/learnpress/default/single/course/grid-one',
					$course,
					'ec-col-md-3'
				);
			}
		}
		wp_send_json( $course_html );
	}
	function get_single_course($price_html){
		// single course block
		$columns = top_hive()->get_setting('instructor_content_columns'); 
		$icons_url = esc_url(get_template_directory_uri()) . '/assets/images/svg-icons';
		$course_html = '<div class="'. $columns .' ec-mb-4">';
			$course_html .= '<div class="th-default-course-block">';
				$course_html .= '<a class="course-block-title" href="'. get_the_permalink() .'">';
            		$course_html .= '<div class="th-course-thumb">'. get_the_post_thumbnail(get_the_ID() , 'large' ) .'</div>';
					$course_html .= '<div class="th-course-details">';
            			$course_html .= '<div class="price-section"><span class="th-sale-price">' . $price_html . '</span></div>';
						$course_html .= '<div class="row no-gutters ec-no-gutters">';
						$course_html .= '<div class="col-6"><p class="ec-mb-2 tophive-lp-course-meta-img"><img src="'. $icons_url . '/user-graduate.svg' .'" alt="sidebaricons"/>' . intval(get_post_meta( get_the_ID(), 'count_enrolled_users', true )) . esc_html__( ' Student', 'masterclass' ) . '</p></div>';
						$course_html .= '</div>';
						$course_html .= '<h5>' . get_the_title() . '</h5>';
						$rated = learn_press_get_course_rate(get_the_ID(), false )['rated'];    
	                    $item = learn_press_get_course_rate(get_the_ID(), false )['total'];    
	                    
	                    $percent = ( ! $rated ) ? 0 : min( 100, ( round( $rated * 2 ) / 2 ) * 20 );
	                    $course_html .= '<div class="review-stars-rated"><div class="review-stars empty"></div><div class="review-stars filled" style="width:' . $percent . '%;"></div><div class="review-count">('. $item .')</div>
	                    </div>';
					$course_html .= '</div>';
				$course_html .= '</a>';
			$course_html .= '</div>';
		return $course_html .= '</div>';
		// end single course block
	}
	function tophive_lp_instructor_courses($query, $userid, $icons_url, $per_page){
		?>
		<div class="ec-row">
			<div class="ec-col-md-12 ec-mt-5">
				<?php 
					if($query->found_posts > 0 ){
						echo '<h3 class="ec-mb-4">' . esc_html__( 'Courses by ', 'masterclass' ) . get_the_author_meta('display_name', $userid) . '</h3>';
					}else{
						echo '<div class="text-center">';
						echo '<h4>' . get_the_author_meta('display_name', $userid) . esc_html__( ' has not published any course yet!', 'masterclass' ) . '</h4>';
						echo '</div>';
					}
				?>
			</div>
		</div>
		<?php
		$course_html = '<div class="ec-row ajax_post_loader">';
		if($query->have_posts()){
			while ($query->have_posts()) {
		    	$query->the_post();
		    	$course = \LP_Global::course();

				$course_html .= apply_filters( 
					'tophive/learnpress/default/single/course/grid-one',
					$course,
					'ec-col-md-3'
				);
			}
		}
		$course_html .= '</div>';	
		echo tophive_sanitize_filter($course_html);
		if( $query->found_posts > $per_page ){
			?>
				<div class="ec-row">
					<div class="ec-col ec-text-center ec-my-5 <?php echo tophive_sanitize_filter($this->section); ?>">
						<button type="button" data-nonce="<?php echo wp_create_nonce( 'th_mc_nonce' ); ?>" data-paged="<?php echo esc_attr($per_page); ?>" data-total="<?php echo esc_attr($query->found_posts); ?>" data-userid="<?php echo esc_attr($userid); ?>" class="th-button-load-more tophive-infinity-button button">
							Load more
						</button>
					</div>
				</div>
			<?php
		}
	}
	function config( $configs ) {
		$config = array(
			// Instructor section
			array(
				'name'     => $this->section,
				'type'     => 'section',
				'panel'    => $this->panel,
				'theme_supports' => '',
				'title'    => esc_html__( 'Instructor', 'masterclass' ),
			),

			// Instructor settings
			array(
				'name' => $this->section . '_instructor_course_section_heading',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Block', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_instructor_course_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.page-template-page-instructor .th-default-course-block',
					'hover' => '.page-template-page-instructor .th-default-course-block:hover',
				),
				'label' => esc_html__( 'Course block styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced styling for course block', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => false,
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'text_color' => false,
						'padding' => false,
						'margin' => false,
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
				),
			),
			array(
				'title'        => esc_html__( 'Courses Columns', 'masterclass' ),
				'name'         => 'instructor_content_columns',
				'type'         => 'select',
				'section' => $this->section,
				'choices'      => array(
					'ec-col-md-12'  => esc_html__( 'One column', 'masterclass' ),
					'ec-col-md-6'   => esc_html__( 'Two columns', 'masterclass' ),
					'ec-col-md-4 ec-col-sm-6'   => esc_html__( 'Three columns', 'masterclass' ),
					'ec-col-md-3 ec-col-sm-6'   => esc_html__( 'Four columns', 'masterclass' ),
				),
				'default' => 'ec-col-md-4 ec-col-sm-6',

			),
			array(
				'name' => $this->section . '_instructor_course_img_height',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => '.page-template-page-instructor .th-default-course-block .th-course-thumb',
				'label' => esc_html__( 'Image Height', 'masterclass' ),
				'css_format' => 'height: {{value}}',
				'min' => 30,
				'max' => 600,
			),
			array(
				'name' => $this->section . '_instructor_course_section_content_heading',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Content', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_instructor_course_content_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.page-template-page-instructor .th-default-course-block .th-course-details',
				),
				'label' => esc_html__( 'Course Content styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced styling for course content', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => false,
						'link_color' => false,
						'bg_image' => false,
						'margin' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_instructor_course_title_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.page-template-page-instructor .th-default-course-block .th-course-details h5',
				'label' => esc_html__( 'Title typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_instructor_course_title_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.page-template-page-instructor .th-default-course-block .th-course-details h5',
					'hover' => '.page-template-page-instructor .th-default-course-block:hover .th-course-details h5',
				),
				'label' => esc_html__( 'Course title styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced styling for course title', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'padding' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'bg_attachment' => false,
						'border_heading' => false,
						'border_width' => false,
						'border_color' => false,
						'border_radius' => false,
						'box_shadow' => false,
						'border_style'  => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'margin' => false,
						'padding' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'border_heading' => false,
						'border_width' => false,
						'border_color' => false,
						'border_radius' => false,
						'box_shadow' => false,
						'border_style'  => false,
					)
				),
			),
			array(
				'name' => $this->section . '_instructor_course_more_content_heading',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Load More Button', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_instructor_course_more_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body .tophive-lp-user-profile button.th-button-load-more',
				'label' => esc_html__( 'Button typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_instructor_course_more_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.page-template-page-instructor .tophive-lp-user-profile button.th-button-load-more',
					'hover' => 'body.page-template-page-instructor .tophive-lp-user-profile button.th-button-load-more:hover'
				),
				'label' => esc_html__( 'Button styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'margin' => false,
						'padding' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					)
				),
			),
		);
		return array_merge($configs, $config);
	}
	function add_instructor_url( $args ) {
		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => 'page-instructor.php'
		));

		$instructor_id = get_current_user_id();
		$instructor_slug = get_the_author_meta( 'user_nicename', $instructor_id );

		if( count($pages) ){
			$args['section_urls'][$this->section] = esc_url( trailingslashit(site_url()) ) . get_post($pages[0]->ID)->post_name . '/' . $instructor_slug;
		}

		return $args;
	}
}

new Tophive_LP_Instructor();
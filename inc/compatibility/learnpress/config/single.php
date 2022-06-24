<?php

class Tophive_LP_Single {
	private $panel = 'learpress_panel';
	private $section = 'learpress_panel_course_single';
	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ) );
		if ( is_admin() || is_customize_preview() ) {
			add_filter( 'Tophive_Control_Args', array( $this, 'add_section_url' ), 35 );
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
			'posts_per_page' => 9,
			'offset' => $page,
		);
		$query = new WP_Query($args);
		$course_html = '';
		if($query->have_posts()){
			while ($query->have_posts()) {
		    	$query->the_post();
		    	$price = intval(get_post_meta( get_the_ID(), '_lp_price', $single = true ));
		    	$sale_price = intval(get_post_meta( get_the_ID(), '_lp_sale_price', $single = true ));
		    	$price_html = '';
		    	if( $sale_price > 0 ){
		    		$price_html = '<span class="pr-1"><small><strike>'. learn_press_get_currency_symbol() . $price .'</strike></small></span><span>'. learn_press_get_currency_symbol() . $sale_price .'</span>';
		    	}else{
		    		$price_html = '<span>'. learn_press_get_currency_symbol() . $price .'</span>';
		    	}

				// single course block
				$course_html .= $this->get_single_course($price_html);
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
		$course_html = '<div class="ec-row mx-n3 ajax_post_loader">';
		if($query->have_posts()){
			while ($query->have_posts()) {
		    	$query->the_post();
		    	$price = intval(get_post_meta( get_the_ID(), '_lp_price', $single = true ));
		    	$sale_price = intval(get_post_meta( get_the_ID(), '_lp_sale_price', $single = true ));
		    	$price_html = '';
		    	if( $sale_price > 0 ){
		    		$price_html = '<span class="pr-1"><small><strike>'. learn_press_get_currency_symbol() . $price .'</strike></small></span><span>'. learn_press_get_currency_symbol() . $sale_price .'</span>';
		    	}else{
		    		$price_html = '<span>'. learn_press_get_currency_symbol() . $price .'</span>';
		    	}

				$course_html .= $this->get_single_course($price_html);
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
				'title'    => esc_html__( 'Course Single', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_spacing',
				'type' => 'css_ruler',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-container #main',
				'label' => esc_html__( 'Layout Spacing', 'masterclass' ),
				'css_format' => array(
					'top'    => 'padding-top: {{value}};',
					'right'  => 'padding-right: {{value}};',
					'bottom' => 'padding-bottom: {{value}};',
					'left'   => 'padding-left: {{value}};',
				),
			),
			array(
				'name' => $this->section . '_course_single_title_heading',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Header', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_head_bg_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar span.head-bg',
				'label' => esc_html__( 'Heading background color', 'masterclass' ),
				'css_format' => 'background-color:{{value}} !important',
			),
			array(
				'name' => $this->section . '_course_single_title_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar h2',
				'label' => esc_html__( 'Title typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_title_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.single-lp_course.learnpress .tophive-lp-headbar h2'
				),
				'label' => esc_html__( 'Title styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => false,
						'link_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_color' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'border_heading' => false,
						'border_width' => false,
						'border_color' => false,
						'border_radius' => false,
						'box_shadow' => false,
						'border_style'  => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_course_single_tags_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar .course-tags',
				'label' => esc_html__( 'Tags typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_tags_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.single-lp_course.learnpress .tophive-lp-headbar .course-tags'
				),
				'label' => esc_html__( 'Tags styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_color' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'border_style' => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_course_head_rating_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar .review-stars-rated .review-stars.empty, .single-lp_course.learnpress .tophive-lp-headbar .review-stars-rated .review-stars.filled',
				'label' => esc_html__( 'Rating Color', 'masterclass' ),
				'css_format' => 'color:{{value}}',
			),
			array(
				'name' => $this->section . '_course_head_date_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar .course-date',
				'label' => esc_html__( 'Date Color', 'masterclass' ),
				'css_format' => 'color:{{value}} !important',
			),
			array(
				'name' => $this->section . '_course_pricing_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar .tophive-lp-sidebar .course-price',
				'label' => esc_html__( 'Pricing typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_purchase_button_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.single-lp_course.learnpress .tophive-lp-headbar .tophive-lp-sidebar  .lp-button.button-purchase-course',
				'label' => esc_html__( 'Purchase button typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_cart_button',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.single-lp_course.learnpress .tophive-lp-headbar .tophive-lp-sidebar .lp-button.button-purchase-course',
					'hover' => '.single-lp_course.learnpress .tophive-lp-headbar .tophive-lp-sidebar .lp-button.button-purchase-course:hover',
				),
				'label' => esc_html__( 'Purchase Button Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
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
				'name' => $this->section . '_course_single_wishlist_button',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.single-lp_course.learnpress .tophive-lp-headbar .tophive-lp-sidebar .hover-info-wishlist.course-single-wishlist a',
					'hover' => '.single-lp_course.learnpress .tophive-lp-headbar .tophive-lp-sidebar .hover-info-wishlist.course-single-wishlist a:hover',
				),
				'label' => esc_html__( 'Wishlist Button Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
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
				'name' => $this->section . '_course_single_curricumum_head',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Curriculum', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_curriculum_section_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section',
					'hover' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section:hover',
				),
				'label' => esc_html__( 'Section Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'text_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'text_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
				),
			),
			array(
				'name' => $this->section . '_course_curriculum_section_heading_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-header',
					'hover' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-header:hover',
				),
				'label' => esc_html__( 'Section Header Styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced Section Header Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'text_color' => false,
						'bg_heading' => false,
						'bg_image' 	=> false,
						'bg_cover' 	=> false,
						'bg_position' => false,
						'bg_repeat' 	=> false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'text_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
				),
			),
			array(
				'name' => $this->section . '_course_curriculum_section_heading_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-header h5',
					'hover' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-header:hover h5',
				),
				'label' => esc_html__( 'Section Heading Text Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_heading' => false,
						'bg_image' 	=> false,
						'bg_color' 	=> false,
						'bg_position' => false,
						'bg_repeat' 	=> false,
						'bg_attachment' => false,
						'border_heading' => false,
						'border_style' => false,
						'border_radius' => false,
						'box_shadow' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_color' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'border_heading' => false,
						'border_style' => false,
						'border_radius' => false,
						'box_shadow' => false,
					),
				),
			),
			array(
				'name' => $this->section . '_course_curriculum_section_heading_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-header .section-title',
				'label' => esc_html__( 'Section Heading typography', 'masterclass' ),
				'css_format' => 'typography',
			),

			array(
				'name' => $this->section . '_course_curriculum_section_list_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-content li',
					'hover' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-content li:hover',
				),
				'label' => esc_html__( 'Section Content List Design', 'masterclass' ),
				'description' => esc_html__( 'Section Items Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'text_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
				),
			),
			array(
				'name' => $this->section . '_course_curriculum_section_list_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .course-curriculum ul.curriculum-sections li.section .section-content li a',
				'label' => esc_html__( 'Section List typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			
			array(
				'name' => $this->section . '_course_single_instructor_section',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Instructor', 'masterclass' ),
			),

			array(
				'name' => $this->section . '_course_instructor_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-content .course-author'
				),
				'label' => esc_html__( 'Instructor Section Design', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'text_color' => false,
						'link_color' => false,
						'bg_heading' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_course_instructor_img_br',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .course-author img',
				'label' => esc_html__( 'Instructor Image Border Radius', 'masterclass' ),
				'css_format' => 'border-radius: {{value}}',
				'min' => 0,
				'max' => 100,
			),
			array(
				'name' => $this->section . '_course_instructor_desc_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .course-author p.description',
				'label' => esc_html__( 'Description Typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_content_styling',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Content', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_single_content_preview',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .single-course-video',
				'label' => esc_html__( 'Course Preview Height', 'masterclass' ),
				'css_format' => 'height: {{value}}',
				'min' => 0,
				'max' => 600,
			),
			array(
				'name' => $this->section . '_course_single_content_preview_br',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .single-course-video',
				'label' => esc_html__( 'Course Preview Border Radius', 'masterclass' ),
				'css_format' => 'border-radius: {{value}}',
				'min' => 0,
				'max' => 100,
			),
			array(
				'name' => $this->section . '_course_single_content_learning_points',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-content .course-learning-points'
				),
				'label' => esc_html__( 'Learning Points styling', 'masterclass' ),
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
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_course_single_content_learning_points_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .course-learning-points .main-text',
				'label' => esc_html__( 'Learning points typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_student_rating',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Rating Section', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_single_student_rating_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate',
				),
				'label' => esc_html__( 'Rating Section styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_heading' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_course_single_student_rating_avg_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-avg',
				),
				'label' => esc_html__( 'Rating Average styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_heading' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => false
				),
			),
			array(
				'name' => $this->section . '_course_single_student_rating_avg_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-avg',
				'label' => esc_html__( 'Rating Average typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_student_rating_avg_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-avg h1',
				'label' => esc_html__( 'Rating Average Number Color', 'masterclass' ),
				'css_format' => 'color : {{value}}',
			),
			array(
				'name' => $this->section . '_course_single_student_rating_avg_start_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-avg .review-stars-rated .review-stars.empty, body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .review-stars-rated .review-stars.filled',
				'label' => esc_html__( 'Rating Average Star Color', 'masterclass' ),
				'css_format' => 'color : {{value}}',
			),
			array(
				'name' => $this->section . '_course_single_student_rating_star_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-individual .review-stars-rated .review-stars.empty, body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-individual .review-stars-rated .review-stars.filled',
				'label' => esc_html__( 'Rating Star Color', 'masterclass' ),
				'css_format' => 'color : {{value}}',
			),
			array(
				'name' => $this->section . '_course_single_student_rating_bar_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-individual .review-stars-rated .review-stars.empty, body.single-lp_course.learnpress .tophive-lp-feedback .course-rate .course-rate-individual .review-stars-rated .review-stars.filled',
				'label' => esc_html__( 'Rating Bar Color', 'masterclass' ),
				'css_format' => 'background-color : {{value}}',
			),

			array(
				'name' => $this->section . '_course_single_reviews_section',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Reviews', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_single_reviews_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-reviews .course-reviews-list li',
					'hover' => 'body.single-lp_course.learnpress .tophive-lp-reviews .course-reviews-list li:hover',
				),
				'label' => esc_html__( 'Course Reviews styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'margin' => false,
						'padding' => false,
						'bg_heading' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					)
				),
			),
			array(
				'name' => $this->section . '_course_single_review_img_br',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-content .tophive-lp-reviews .course-reviews-list li .review-author img',
				'label' => esc_html__( 'Review Image Border Radius', 'masterclass' ),
				'css_format' => 'border-radius: {{value}}',
				'min' => 0,
				'max' => 100,
			),
			array(
				'name' => $this->section . '_course_single_related_course_section',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course Reviews', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_single_related_course_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.single-lp_course.learnpress .tophive-lp-related-courese .th-default-course-block',
					'hover' => 'body.single-lp_course.learnpress .tophive-lp-related-courese .th-default-course-block:hover',
				),
				'label' => esc_html__( 'Related Courses styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'margin' => false,
						'padding' => false,
						'bg_heading' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
					)
				),
			),
			array(
				'name' => $this->section . '_course_single_related_courses_heading_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-related-courese .th-default-course-block .th-course-details h5',
				'label' => esc_html__( 'Heading typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_single_related_course_thumb',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.single-lp_course.learnpress .tophive-lp-related-courese .th-default-course-block .th-course-thumb',
				'label' => esc_html__( 'Thumbnail Height', 'masterclass' ),
				'css_format' => 'height: {{value}}',
				'min' => 0,
				'max' => 500,
			),
		);
		return array_merge($configs, $config);
	}
	function add_section_url( $args ) {
		$post = get_posts( array( 'posts_per_page' => 1, 'post_type' => 'lp_course') );
		$args['section_urls'][$this->section] = get_permalink( $post[0] );
		

		return $args;
	}
}

new Tophive_LP_Single();
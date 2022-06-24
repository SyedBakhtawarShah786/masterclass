<?php

class Tophive_LP_Categories {
	private $panel = 'learpress_panel';
	private $section = 'learpress_panel_categories';
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
				'title'    => esc_html__( 'Category', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_heading_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.archive.category.learnpress .tophive-lp-headbar'
				),
				'label' => esc_html__( 'Heading styling', 'masterclass' ),
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
				'name' => $this->section . '_course_cat_title_heading',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Page Header', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_cat_title_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.archive.category.learnpress .tophive-lp-headbar h2',
				'label' => esc_html__( 'Title typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_title_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.archive.category.learnpress .tophive-lp-headbar h2'
				),
				'label' => esc_html__( 'Title styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced styling category heading', 'masterclass' ),
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
				'name' => $this->section . '_course_cat_badge_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.archive.category.learnpress .tophive-lp-headbar h2 .ec-badge-primary',
				'label' => esc_html__( 'Badge typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_badge_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => '.archive.category.learnpress .tophive-lp-headbar h2 .ec-badge-primary'
				),
				'label' => esc_html__( 'Badge styling', 'masterclass' ),
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
				'name' => $this->section . '_course_cat_bread_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => '.archive.category.learnpress .tophive-lp-headbar .tophive-lp-heading .learn-press-breadcrumb',
				'label' => esc_html__( 'Breadcrumb typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			// array(
			// 	'name'            => $this->section . '_course_cat_show_sort_filter',
			// 	'type'            => 'checkbox',
			// 	'section'         => $this->section,
			// 	'default'         => 1,
			// 	'selector' => '.archive.category.learnpress .tophive-lp-headbar .tophive-lp-heading .th-filter-wrapper',
			// 	'checkbox_label'  => esc_html__( 'Show Sort Filter', 'masterclass' ),
			// ),
			array(
				'name' => $this->section . '_course_cat_filter_design',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Filter Design', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_cat_filter_select_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-filter-wrapper button.th-exclusive-course-filter-toggle',
					'hover' => 'body.archive.category.learnpress .th-filter-wrapper button.th-exclusive-course-filter-toggle:hover',
				),
				'label' => esc_html__( 'Filter Select Button Styling', 'masterclass' ),
				'description' => esc_html__( 'Advanced styling for select menu', 'masterclass' ),
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
				'name' => $this->section . '_course_cat_filter_select_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-filter-wrapper button.th-exclusive-course-filter-toggle',
				'label' => esc_html__( 'Filter Select Button typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_filter_select_dd_design',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-filter-wrapper .th-exclusive-course-filter'
				),
				'label' => esc_html__( 'Filter Select Dropdown Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'text_color' => false,
						'margin' => false,
						'padding' => false,
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
				'name' => $this->section . '_course_cat_filter_select_dd_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-filter-wrapper .th-exclusive-course-filter li',
				'label' => esc_html__( 'Filter Select Dropdown typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_filter_search_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-filter-wrapper input[type=text]',
					'hover' => 'body.archive.category.learnpress .th-filter-wrapper input[type=text]:hover, body.archive.category.learnpress .th-filter-wrapper input[type=text]:focus',
				),
				'label' => esc_html__( 'Search Input Styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'margin' => false,
						'padding' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'text_color' => false,
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
				'name' => $this->section . '_course_cat_filter_search_height',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-filter-wrapper input[type=text]',
				'label' => esc_html__( 'Search input height', 'masterclass' ),
				'css_format' => 'height : {{value}}',
				'min' => 20, 
				'max' => 100
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses',
				'type' => 'heading',
				'section' => $this->section,
				'title' => esc_html__( 'Course styling', 'masterclass' ),
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-default-course-block',
					'hover' => 'body.archive.category.learnpress .th-default-course-block:hover',
				),
				'label' => esc_html__( 'Course block styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'text_color' => false,
						'bg_image' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_attachment' => false,
						'bg_attachment' => false,
					),
					'hover_fields' => array(
						'link_color' => false,
						'text_color' => false,
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

			array(
				'name' => $this->section . '_course_cat_thumb_height',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block.th-thumb-contained .th-course-thumb',
				'label' => esc_html__( 'Thumbnail height', 'masterclass' ),
				'css_format' => 'height : {{value}}',
				'min' => 20, 
				'max' => 400
			),
			array(
				'name' => $this->section . '_course_cat_thumb_br',
				'type' => 'slider',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block.th-thumb-contained .th-course-thumb',
				'label' => esc_html__( 'Border Radius', 'masterclass' ),
				'css_format' => 'border-radius : {{value}}',
				'min' => 0, 
				'max' => 100
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_title_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block h5',
				'label' => esc_html__( 'Course title typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_title_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-default-course-block .th-course-details h5',
					'hover' => 'body.archive.category.learnpress .th-default-course-block:hover .th-course-details h5',
				),
				'label' => esc_html__( 'Course title styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'padding' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_heading' => false,
						'bg_color' => false,
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
						'bg_heading' => false,
						'bg_color' => false,
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
				'name' => $this->section . '_course_cat_filter_courses_desc_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block p.post-excerpt',
				'label' => esc_html__( 'Course description typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_desc_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-default-course-block .th-course-details p.post-excerpt',
					'hover' => 'body.archive.category.learnpress .th-default-course-block:hover .th-course-details p.post-excerpt',
				),
				'label' => esc_html__( 'Course description styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' => false,
						'bg_image' => false,
						'padding' => false,
						'bg_cover' => false,
						'bg_position' => false,
						'bg_repeat' => false,
						'bg_heading' => false,
						'bg_color' => false,
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
						'bg_heading' => false,
						'bg_color' => false,
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
				'name' => $this->section . '_course_cat_filter_courses_rating_typo',
				'type' => 'color',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block .review-stars-rated .review-stars.empty,body.archive.category.learnpress .th-default-course-block  .review-stars-rated .review-stars.filled',
				'label' => esc_html__( 'Course rating color', 'masterclass' ),
				'css_format' => 'color:{{value}}',
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_meta_typo',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block .tophive-lp-course-meta-img',
				'label' => esc_html__( 'Course meta typography', 'masterclass' ),
				'css_format' => 'color:{{value}}',
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_meta_color',
				'type' => 'color',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block .tophive-lp-course-meta-img',
				'label' => esc_html__( 'Course meta color', 'masterclass' ),
				'css_format' => 'color:{{value}}',
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_pricing_color',
				'type' => 'typography',
				'section' => $this->section,
				'selector' => 'body.archive.category.learnpress .th-default-course-block  .th-sale-price',
				'label' => esc_html__( 'Course pricing typography', 'masterclass' ),
				'css_format' => 'typography',
			),
			array(
				'name' => $this->section . '_course_cat_filter_courses_pricing_styling',
				'type' => 'styling',
				'section' => $this->section,
				'selector' => array(
					'normal' => 'body.archive.category.learnpress .th-default-course-block .th-course-details .th-sale-price',
					'hover' => 'body.archive.category.learnpress .th-default-course-block:hover .th-course-details .th-sale-price',
				),
				'label' => esc_html__( 'Course pricing styling', 'masterclass' ),
				'css_format' => 'styling',
				'fields'     => array(
					'normal_fields' => array(
						'link_color' 	=> false,
						'bg_image' 		=> false,
						'padding' 		=> false,
						'bg_cover' 		=> false,
						'bg_position' 	=> false,
						'bg_repeat' 	=> false,
						'bg_heading' 	=> false,
						'bg_color' 		=> false,
						'bg_attachment' => false,
						'border_heading'=> false,
						'border_width' 	=> false,
						'border_color' 	=> false,
						'border_radius' => false,
						'box_shadow' 	=> false,
						'border_style'  => false,
					),
					'hover_fields' => array(
						'link_color' 	=> false,
						'bg_image' 		=> false,
						'margin' 		=> false,
						'padding' 		=> false,
						'bg_heading' 	=> false,
						'bg_color' 		=> false,
						'bg_cover' 		=> false,
						'bg_position' 	=> false,
						'bg_repeat' 	=> false,
						'bg_attachment' => false,
						'border_heading'=> false,
						'border_width' 	=> false,
						'border_color' 	=> false,
						'border_radius' => false,
						'box_shadow' 	=> false,
						'border_style'  => false,
					)
				),
			),
		);
		return array_merge($configs, $config);
	}
	function add_section_url( $args ) {
		$terms = get_terms( 'course_category' );

		if( count($terms) ){
			$term_id = $terms[0]->term_id;
			$args['section_urls'][$this->section] = get_category_link( $term_id );
		}

		return $args;
	}
}

new Tophive_LP_Categories();
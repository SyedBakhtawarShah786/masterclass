<?php  
	
class Tophive_LP{
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
			add_filter( 'init', array($this, 'setup_course_meta_data') );
			add_filter( 'tophive/customizer/config', array( $this, 'config' ) );
	        add_action( 'save_post',      	array( $this, 'tophive_learnpress_save_levels_tags' ) );
	        add_action( 'save_post', 		array( $this, 'tophive_learnpress_save_learning_objectives'));
	        add_action( 'save_post', 		array( $this, 'tophive_learnpress_save_featured_video'));
	        add_action( 'wp_enqueue_scripts', array($this, 'load_scripts') );

	        add_action( 'learn-press/review-order/after-order-total', array($this, 'tophive_learnpress_order_confirm_button') );

			add_action( 'course_category_add_form_fields', array( $this, 'tophive_learnpress_course_category_image'), 10, 2 );

			add_action( 'created_course_category', array($this, 'save_category_image'), 10, 2 );

			add_action( 'course_category_edit_form_fields', array($this, 'update_category_image'), 10, 2 );
			add_action( 'edited_course_category', array($this, 'updated_category_image'), 10, 2 );
			add_action( 'admin_enqueue_scripts', array($this, 'load_media') );
			add_action( 'admin_footer', array( $this, 'add_script') );


	        add_action( 'tophive/learnpress/filter/search', array($this, 'course_filter_text_search'), 10, 1);
        	
	        add_action( 'tophive/learnpress/category/sidebar', array($this, 'course_category_sidebar_left'));
	        add_action( 'tophive/learnpress/category/widgets/top', array($this, 'course_category_widgets_top'));
        	add_action( 'tophive/learnpress/archive/courses/before', array($this, 'learnpress_archive_before') );

	        add_action( 'tophive/learnpress/filter/hidden', array($this, 'course_hidden_filter'), 10, 1);
	        add_action( 'tophive/learnpress/filter/sort', array($this, 'course_filter_sort'), 10, 1);
	        add_action( 'tophive/learnpress/filter/price', array($this, 'course_filter_price'), 12, 1);
	        add_action( 'tophive/learnpress/filter/topic', array($this, 'course_filter_topic'), 14, 1);
	        add_action( 'tophive/learnpress/filter/category', array($this, 'course_filter_category'), 14, 1);
	        add_action( 'tophive/learnpress/filter/rating', array($this, 'course_filter_rating'), 10, 1);
	        add_action( 'tophive/learnpress/filter/level', array($this, 'course_filter_level'), 13, 1);
	        add_action( 'tophive/elementor/learnpress/course-block/after-title', array($this, 'tophive_course_tag') , 10, 2 );

	        add_action( 'tophive/learnpress/course-meta', array($this, 'get_course_meta'), 10 , 1 );

	        add_action( 'tophive/learnpress/course-meta/level', array($this, 'tophive_learnpress_course_level'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/lessons', array($this, 'tophive_learnpress_course_lessons'), 10 , 1 );

	        add_action( 'tophive/learnpress/course-meta/price', array($this, 'tophive_learnpress_course_price'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/duration', array($this, 'tophive_learnpress_course_duration'), 10 , 2 );
	        add_action( 'tophive/learnpress/course-meta/rating', array($this, 'tophive_learnpress_course_rating'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/updated', array($this, 'tophive_learnpress_course_update_date'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/category', array($this, 'tophive_learnpress_course_category'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/instructor-url', array($this, 'tophive_learnpress_course_instructor_url'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/instructor', array($this, 'tophive_learnpress_course_instructor'), 10 , 1 );
	        add_action( 'tophive/learnpress/course-meta/students', array($this, 'tophive_learnpress_enrolled_students'), 10 , 1 );
	        add_action( 'tophive/learnpress/archive/courses/sidebar', array($this, 'learnpress_archive_sidebar') );

	        add_action( 'tophive/learnpress/author/rating', array($this, 'tophive_learnpress_author_rating') , 10, 1);

	        add_action( 'wp_ajax_tophive_advanced_filter', array($this, 'tophive_advanced_filter') );
	        add_action( 'wp_ajax_nopriv_tophive_advanced_filter', array($this, 'tophive_advanced_filter') );

	        require_once get_template_directory() . '/inc/compatibility/learnpress/config/instructor.php';
			require_once get_template_directory() . '/inc/compatibility/learnpress/config/categories.php';
			require_once get_template_directory() . '/inc/compatibility/learnpress/config/single.php';
			require_once get_template_directory() . '/inc/compatibility/learnpress/lessons.php';
			require_once get_template_directory() . '/inc/compatibility/learnpress/course-single.php';
			require_once get_template_directory() . '/inc/compatibility/learnpress/course-templates.php';
		}
	}
	function tophive_learnpress_author_rating( $author_id ){
		$post_rating = [];
		$total_reviewers = [];
		$args = array(
			'post_type' => 'lp_course',
			'post_status' => 'publish',
			'author' => $author_id,
			'posts_per_page' => -1
		);
		$query = new WP_Query( $args );
		if( $query->have_posts() ) {
			while ( $query->have_posts() ) {
		    	$query->the_post();
		    	if( function_exists('learn_press_get_course_rate') ){
		    		$rated = learn_press_get_course_rate( get_the_ID(), false )['rated'];
	        		$reviewers = learn_press_get_course_rate( get_the_ID(), false )['total'];    

	        		if( $rated > 0 ){
		    			array_push( $post_rating, $rated );
	        		}
	        		if( $reviewers > 0 ){
		    			array_push( $total_reviewers, $reviewers );
	        		}
		    	}
			}
		}
		$total_rating_count = count( $post_rating );
		$total_rating_sum = array_sum( $post_rating );
		$total_reviewers_count = array_sum( $total_reviewers );
		if( $total_rating_sum > 0 && $total_rating_count > 0 ){
			$rating_avg = $total_rating_sum / $total_rating_count;
		}else{
			$rating_avg = 0;
		}
	        
        $percent = ( ! $rating_avg ) ? 0 : min( 100, ( round( $rating_avg * 2 ) / 2 ) * 20 );

		$rating_avg = apply_filters( 'tophive/learnpress/single/rating/number', $rating_avg );
		return '<div class="review-stars-rated">
			<span class="star-ratings-bp">
				<span class="review-stars empty"></span>
				<span class="review-stars filled" style="width:' . $percent . '%;"></span>
			</span>
			<span class="review-count"> '. $rating_avg .'</span>
			<div class="reviewers">('. $total_reviewers_count . esc_html__(' Reviews', 'masterclass')  .')</div>
        </div>';
	}	
	function tophive_advanced_filter(){
		$filter =  $_REQUEST["filter"];
		$grid = $_REQUEST["grid"];
		if(isset($_REQUEST["settings"])){
			$settings = $_REQUEST["settings"];
		}

		$args = array(
			'post_type' => 'lp_course',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			$args['meta_query'] = array(
				'relation' => 'AND'
			),
			$args['tax_query'] = array(
				'relation' => 'AND'
			),
		);
		if( !empty($filter['FilterCategories']) ){
			$args['tax_query'][] = array(
				'taxonomy' => 'course_category',
				'field' => 'slug',
				'terms' => $filter['FilterCategories']
			);
		}
		if( !empty($filter['FilterSkills']) ){
			$args['tax_query'][] = array(
				'taxonomy' => 'course_tag',
				'field' => 'slug',
				'terms' => $filter['FilterSkills']
			);
		}
		if( !empty($filter['FilterLevel']) ){
			$args['meta_query'][] = array(
				'key' => 'th_wp_student_level_meta_key',
				'value' => $filter['FilterLevel'],
				'compare' => 'IN'
			);
		}
		if( $filter['FilterPrice'] == 'free' ){
			$args['meta_query'][] = array(
				'key' => '_lp_price',
				'compare' => 'NOT EXISTS'
			);
		}
		if( $filter['FilterPrice'] == 'paid' ){
			$args['meta_query'][] = array(
				'key' => '_lp_price',
				'compare' => 'EXISTS'
			);
		}
		if( !empty($filter['FilterRating']) ){
			$args['meta_query'][] = array(
				'key' => '_lp_course_rating_avg',
				'value' => $filter['FilterRating'],
				'compare' => 'BETWEEN'
			);
		}
		if( !empty($filter['FilterSort']) ){
			if( $filter['FilterSort']['type'] == 'post' ){
				if( $filter['FilterSort']['order'] == 'popular' ){
					$args['orderby'] = 'meta_value_num';
					$args['order'] = 'DESC';
					$args['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'key' => 'count_enrolled_users',
							'compare' => 'NOT EXISTS'
						),
						array(
							'key' => 'count_enrolled_users',
							'compare' => 'EXISTS'
						)
					);
				}
				if( $filter['FilterSort']['order'] == 'latest' ){
					$args['orderby'] = 'date';
					$args['order'] = 'DESC';
				}
			}
			if( $filter['FilterSort']['type'] == 'price' ){
				if( $filter['FilterSort']['order'] == 'asc' ){
					$args['meta_key'] = '_lp_price';
					$args['orderby'] = 'meta_value_num';
					$args['order'] = 'ASC';
				}
				if( $filter['FilterSort']['order'] == 'desc' ){
					$args['meta_key'] = '_lp_price';
					$args['orderby'] = 'meta_value_num';
					$args['order'] = 'DESC';
				}
			}
		}

		$posts = new WP_Query( $args );

		$post_ids = wp_list_pluck( $posts->posts, 'ID' );

		if( !empty($filter['FilterSort']) ){
			if( $filter['FilterSort']['type'] == 'price' ){
				$price_ids = [];
				foreach ($post_ids as $id) {
					$course = learn_press_get_course( $id );
				 	$price = $course->get_price();
				 	$price_ids[$id] = $price;
				} 
				if( $filter['FilterSort']['order'] == 'asc' ){
					asort($price_ids);
				}
				if( $filter['FilterSort']['order'] == 'desc' ){
					arsort($price_ids);
				}
				$post_ids = array_keys($price_ids);
			}
		}
		
		$html = '';
		if( !empty($post_ids) ){
			$html .= '	<span class="filter-loader"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; background: none; display: block; shape-rendering: auto;" width="137px" height="137px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
					<path d="M35 50A15 15 0 0 0 65 50A15 16.3 0 0 1 35 50" fill="#292664" stroke="none" transform="rotate(177.696 50 50.65)">
					  <animateTransform attributeName="transform" type="rotate" dur="0.5025125628140703s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50.65;360 50 50.65"></animateTransform>
					</path></svg></span>';
			foreach ($post_ids as $id) {
				$course = learn_press_get_course( $id );
				if( $grid == 'two' ){
					$html .= apply_filters( 'tophive/learnpress/default/single/course/grid-' . $grid, $course, $settings );
				}else{
					$html .= apply_filters( 'tophive/learnpress/default/single/course/grid-' . $grid, $course, 'ec-col-md-4' );
				}
			}
		}else{
			$html .= '<div class="ec-text-center ec-py-4 ec-w-100">
				<img src="'. get_template_directory_uri() . '/assets/images/404.png' . '" alt="no-result" />
				<h6 class="ec-mt-4 ec-text-center ec-w-100">'. esc_html__( 'Sorry ! No Course Have Been Found', 'masterclass' ) .'<h6>
			</div>';
		}

		$count = count($post_ids);
		$count_html = sprintf(esc_html__( 'Found %s Courses', 'masterclass' ), $count );

		wp_send_json( array( 'html' => $html, 'count' => $count_html ) );
	}
	function tophive_learnpress_course_update_date( $id ){
		$date['raw'] = get_the_date('F j, Y', $id);
		$date['html'] = esc_html__( 'Last updated: ', 'masterclass' ) . '<b>' .  get_the_date('M j, Y', $id) . '</b>';
		return $date;
	}
	function tophive_learnpress_course_category( $id ){
		$terms = get_the_terms( $id , 'course_category' );
		$cat['raw'] = [];
		$cat['html'] = '';
		if( !empty($terms) ){
			$cat['html'] .= '<span class="th-course-tags-single-course">';
            $total = count($terms);
            $i = 1;
            foreach ( $terms as $term ) {    
                if( $i < $total ){
                        $cat['html'] .= '<a href="'. get_category_link( $term->term_id ) .'">' . $term->name . '</a>, ';
                    }else{
                        $cat['html'] .= '<a href="'. get_category_link( $term->term_id ) .'">' . $term->name . '</a>';
                    }
                $i++;
            }
            $cat['html'] .= '</span>';
		}
        if( !empty($terms) ){
        	foreach ( $terms as $term ) {    
	        	$cat['raw'][$term->term_id] = $term->term_name;
	        }
        }
        return $cat;
	}	
	function tophive_learnpress_course_instructor_url( $id, $img = false ){
		$instructor_id = get_post_field( 'post_author', $id );
		$instructor_slug = get_the_author_meta( 'user_nicename', $instructor_id );
	
		$instructor_img = '';

		if( $img ){
			$instructor_img = !get_avatar( $instructor_id, 2) ? '<img class="default-avatar" src="'. $icons_url . '/user-alt.svg' .'" alt="'. $instructor_slug .'"/>' : get_avatar( $instructor_id, 32, $default = '', $instructor_slug, $args = array( 'class' => 'rounded-circle' ) );
		}

		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => 'page-instructor.php'
		));
		$instructor_url['raw'] = esc_url( trailingslashit(site_url()) ) . get_post($pages[0]->ID)->post_name . '/' . $instructor_slug;
		$instructor_url['html'] = '<a class="view-instructor" href="'. esc_url( trailingslashit(site_url()) ) . get_post($pages[0]->ID)->post_name . '/' . $instructor_slug .'">'. $instructor_img . get_the_author_meta( 'display_name', $instructor_id ) .'</a>';
		return $instructor_url;
	}
	function tophive_learnpress_course_instructor($id){
		$instructor_id = get_post_field( 'post_author', $id );
		$instructor_name = get_the_author_meta( 'display_name', $instructor_id );
		$instructor['avatar'] = get_avatar( $instructor_id, 32 );
		$instructor['name'] = $instructor_name;
		return $instructor;
	}
	function tophive_learnpress_course_lessons( $id ){
		$course = learn_press_get_course($id);
		$lessons = $course->count_items( LP_LESSON_CPT );

		$l['raw'] = $lessons;

		$l['html'] = $lessons . esc_html__( ' lessons', 'masterclass' );
		return $l;
	}
	function tophive_learnpress_course_duration( $id ){
		$duration = get_post_meta( $id, $key = '_lp_duration', true );

		$d['raw'] = explode(' ', $duration)[0];

		$duration_num = explode(' ', $duration)[0];
		if( $duration_num >= 1 ){
			$d['html'] = $duration . 's';
		}else{
			$d['html'] = $duration;
		}
		return $d;
	}
	function tophive_learnpress_course_level($id){
		$icons_url = esc_url(get_template_directory_uri()) . '/assets/images/svg-icons'; 
		$level = get_post_meta( $id, 'th_wp_student_level_meta_key', true );
		$html['raw'] = $level;
		if( $level == 3 ){
			$html['html_icon'] = '<img src="'. $icons_url . '/advanced.svg' .'" alt="advanced-level" />' . esc_html__('Advanced', 'masterclass');
			$html['html'] = esc_html__('Advanced', 'masterclass');
		}elseif ( $level == 2 ) {
			$html['html_icon'] = '<img src="'. $icons_url . '/intermediate.svg' .'" alt="intermediate-level" />' . esc_html__('Intermediate', 'masterclass');
			$html['html'] = esc_html__('Intermediate', 'masterclass');
		}elseif ( $level == 1 ) {
			$html['html_icon'] = '<img src="'. $icons_url . '/beginner.svg' .'" alt="begineer-level" />' . esc_html__('Beginner', 'masterclass');
			$html['html'] = esc_html__('Beginner', 'masterclass');
		}else{
			$html['html_icon'] = esc_html__('All level', 'masterclass');
			$html['html'] = esc_html__('All level', 'masterclass');
		}
		return $html;
	}
	function tophive_learnpress_course_price( $id, $full_form = false ){
		$course = learn_press_get_course($id);
    	$symbol = learn_press_get_currency_symbol();

		$price = get_post_meta( $id, '_lp_price', true );
		$sale_price = get_post_meta( $id, '_lp_sale_price', true );
		$actual_price = $course->get_price();

    	$html['free'] = false;

    	if( $actual_price > 0 && $sale_price > 0 ){
    		$html['full'] = '<span class="pr-1"><small><strike>'. $symbol . $price .'</strike></small></span><span> '. $symbol . $actual_price .'</span>';
    		$html['full_float'] = '<span class="pr-1"><small><strike>'. $symbol . $price .'</strike></small></span><span> '. $symbol . sprintf('%1.2f', $actual_price) .'</span>';
    		$html['html'] = '<span>'. $symbol . $actual_price .'</span>';
    	}
    	elseif(  $actual_price > 0 && ( empty($sale_price) || $sale_price == 0 )  ){
    		$html['full'] = '<span> '. $symbol . $actual_price .'</span>';
    		$html['full_float'] = '<span> '. $symbol . sprintf('%1.2f', $actual_price) .'</span>';
    		$html['html'] = '<span>'. $symbol . $actual_price .'</span>';    		
    	}
    	else{
    		$html['html'] = '<span>'. esc_html__( 'Free', 'masterclass' ) .'</span>';
    		$html['full'] = '<span>'. esc_html__( 'Free', 'masterclass' ) .'</span>';
    		$html['full_float'] = '<span>'. esc_html__( 'Free', 'masterclass' ) .'</span>';
	    	$html['free'] = true;
    	}
		return $html;
	}
	function tophive_learnpress_course_rating( $id ){
		if( function_exists('learn_press_get_course_rate') ){
			$rated = learn_press_get_course_rate($id, false )['rated'];    
	        $item = learn_press_get_course_rate($id, false )['total'];    
	        $percent = ( ! $rated ) ? 0 : min( 100, ( round( $rated * 2 ) / 2 ) * 20 );
			$rating['raw']['rate'] = $rated;
			$rating['raw']['reviews'] = $item;

			$rating['html'] = '<div class="review-stars-rated">
				<span>
					<span class="review-stars empty"></span>
					<span class="review-stars filled" style="width:' . $percent . '%;"></span>
				</span>
				<div class="review-count">( '. apply_filters( 'tophive/learnpress/single/rating/number', $rated ) . ' ) '. $item .'</div>
			                    </div>';
			$rating['html2'] = '<div class="review-stars-rated th-lp-cr-single">
			                <div class="review-stars empty"></div>
			                <div class="review-stars filled" style="width:' . $percent . '%;"></div>
			            </div> ( '. apply_filters( 'tophive/learnpress/single/rating/number', $rated ) . ' ) '. $item;
	        return $rating;
		}
	}

	function tophive_learnpress_enrolled_students( $id ){
		$course = learn_press_get_course($id);
		$count = $course->get_users_enrolled();

		$student['raw'] = $count;

		if( $count == 0 ){
			$student['html'] = esc_html__( 'No Student', 'masterclass' );
		}elseif( $count == 1 ){
			$student['html'] = esc_html__( '1 Student', 'masterclass' );
		}elseif( $count > 1 ){
			$student['html'] = esc_html__( $count . ' Students', 'masterclass' );
		}else{
			$student['html'] = esc_html__( $count . ' Students', 'masterclass' );
		}
		return $student;
	}
	function get_course_meta( $id ){
		$course = learn_press_get_course($id);

		$course_meta = [];

		// Course Tag
		$course_tag = get_post_meta($id, 'th_wp_course_tag_meta_key', true );

		if( !empty($course_tag) ){
			$course_meta['tag']['raw'] = $course_tag;
			$course_meta['tag']['html'] = '<span class="course-tags '. $course_tag .'">'. ucfirst( $course_tag ) . '</span>';
		}else{
			$course_meta['tag'] = '';
		}
		// Price
		$course_meta['price'] = apply_filters( 'tophive/learnpress/course-meta/price', $id );

		// Updated
		$course_meta['updated'] = apply_filters( 'tophive/learnpress/course-meta/updated', $id );

		// Category
		$course_meta['category'] = apply_filters('tophive/learnpress/course-meta/category', $id);

		// Students
    	$course_meta['student'] = apply_filters('tophive/learnpress/course-meta/students', $id);

    	// Lessons
    	$course_meta['lessons'] = apply_filters( 'tophive/learnpress/course-meta/lessons', $id );

    	// Duration
		$course_meta['duration'] = apply_filters( 'tophive/learnpress/course-meta/duration', $id );

		// Instructor Url
		$course_meta['instructor_url'] = apply_filters( 'tophive/learnpress/course-meta/instructor-url', $id );
		// Instructor
		$course_meta['instructor'] = apply_filters( 'tophive/learnpress/course-meta/instructor', $id );

		// Rating
        $course_meta['rating'] = apply_filters( 'tophive/learnpress/course-meta/rating', $id );

        // Level
        $course_meta['level'] = apply_filters( 'tophive/learnpress/course-meta/level', $id );

		return $course_meta;
	}
	function course_category_widgets_top(){
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
		$query = new WP_Query($args);
		$num = $query->found_posts;
		$align = is_rtl() ? 'ec-text-left' : 'ec-text-right';
		?>
			<div class="ec-row">
				<div class="ec-col-md-6">
					<p class="advanced-filter-search-results <?php echo esc_attr($align); ?> ec-font-weight-bold ec-mt-2"><?php echo sprintf(esc_html__( 'Found %s courses', 'masterclass' ), $num ); ?></p>
				</div>
				<?php
					if( is_rtl() ){
						?>
							<div class="ec-col-md-6 ec-pr-4">
						<?php
					}else{
						?>
							<div class="ec-col-md-6 ec-pl-4">
						<?php
					}
				?>
					<?php 
						echo apply_filters( 'tophive/learnpress/filter/sort', '' );
					?>
				</div>
			</div>
		<?php
	}
	function learnpress_archive_sidebar(){
		$topics = get_terms( 'course_tag' );
		$categories = get_terms( 'course_category' );
			if( is_rtl() ){
				?>
					<div class="ec-pl-md-3 ec-pl-sm-3">
				<?php
				}else{
				?>
					<div class="ec-pr-md-3 ec-pr-sm-0">
				<?php
				}
				echo apply_filters( 'tophive/learnpress/filter/category', $categories );
				echo apply_filters( 'tophive/learnpress/filter/level', '' );
				echo apply_filters( 'tophive/learnpress/filter/topic', $topics );
				echo apply_filters( 'tophive/learnpress/filter/price', '' );
				echo apply_filters( 'tophive/learnpress/filter/rating', '' );
			?>
			</div>
		<?php
	}
	function course_category_sidebar_left(){
		$topics = get_terms( 'course_tag' );

		$hiddenfilter = array(
			'name' => 'th-filter-category',
			'value' => get_query_var( 'term' ), 
		);
		echo apply_filters( 'tophive/learnpress/filter/hidden', $hiddenfilter );
		echo apply_filters( 'tophive/learnpress/filter/level', '' );
		echo apply_filters( 'tophive/learnpress/filter/topic', $topics );
		echo apply_filters( 'tophive/learnpress/filter/price', '' );
		echo apply_filters( 'tophive/learnpress/filter/rating', '' );
	}
	function learnpress_archive_before(){
		$args = array(
			'post_type' => 'lp_course',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);	
		$query = new WP_Query($args);
		$num = $query->found_posts;
		?>
			<div class="ec-col-md-3 ec-mb-4">
				<?php  
					echo '<h2 class="ec-mt-1">' . esc_html__( 'All Courses', 'masterclass' ) . '</h2>';
				?>
			</div>
			<div class="ec-col-md-3">
			</div>
			<div class="ec-col-md-3 ec-align-items-center">
				<p class="advanced-filter-search-results <?php echo is_rtl() ? 'ec-text-left' : 'ec-text-right' ?> ec-font-weight-bold ec-mt-2"><?php echo sprintf(esc_html__( 'Found %s courses', 'masterclass' ), $num ); ?></p>
			</div>
			<div class="ec-col-md-3">
				<?php 
					if( is_rtl() ){
						?>
							<div class="ec-pr-md-3">
						<?php
					}else{
						?>
							<div class="ec-pl-md-3">
						<?php
					}
				?>
					<?php
						echo apply_filters( 'tophive/learnpress/filter/sort', '' );
					?>
				</div>
			</div>
		<?php
	}
	function setup_course_meta_data(){
		$viewargs = array(
			'post_type' => 'lp_course',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
		    	array(
			     	'key' => '_lp_count_course_view',
			     	'compare' => 'NOT EXISTS'
			    ),
			)
		);
		$viewposts = new WP_Query($viewargs);
		$view_post_ids = wp_list_pluck( $viewposts->posts, 'ID' );
		foreach ($view_post_ids as $id) {
			update_post_meta( $id, '_lp_count_course_view', 0, '' );
		}

		$ratingargs = array(
			'post_type' => 'lp_course',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
		    	array(
			     	'key' => '_lp_course_rating_avg',
			     	'compare' => 'NOT EXISTS'
			    ),
			)
		);

		$ratingposts = new WP_Query($ratingargs);
		$rating_post_ids = wp_list_pluck( $ratingposts->posts, 'ID' );
		foreach ($rating_post_ids as $id) {
			$rating = apply_filters( 'tophive/learnpress/course-meta/rating', $id );
			$rateavg = $rating['raw']['rate'];
			update_post_meta( $id, '_lp_course_rating_avg', $rateavg );
		}
	}
	
	function get_first_lesson_id( $id = 0 ){
		return $id;
	}
	
	function tophive_course_tag($id, $settings = ''){

		$course_tag = get_post_meta(get_the_ID(), 'th_wp_course_tag_meta_key', true );
		if( 'yes' == $settings['show_tags'] ){
			if( !empty($course_tag) ){
				return '<span class="course-tags '. $course_tag .'">'. ucfirst($course_tag) . '</span>';
			}else{
				return '';
			}
		}else{
			return '';
		}
	}
	function course_hidden_filter( $args ){
		echo '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-price ec-d-none">
			<ul class="th-exclusive-course-filter filter-overlap">
			    <li class="active" ';
		    		foreach ($args as $key => $value) {
		    			echo 'data-' . $key .'="'. $value .'" ';
		    		}
			echo '></li>
			</ul>
		</div>';
	}
	// using 'filter-overlap' class overlapse dropdown on content
	function course_filter_sort(){
		echo '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-price">
			<button class="th-exclusive-course-filter-toggle">'. esc_html__( 'Sort Courses by', 'masterclass' ) .' <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter filter-overlap">
			    <li
			         data-name="th-filter-sort"
			         data-group="tophive-advanced-filter-wrapper"
			         data-type="post"
			         data-order="popular">
			         '. esc_html__( 'Popularity', 'masterclass' ) .' 
			    </li>
			    <li
			         data-name="th-filter-sort"
			         data-group="tophive-advanced-filter-wrapper"
			         data-type="post"
			         data-order="latest">
			         '. esc_html__( 'latest', 'masterclass' ) .' 
			    </li>
			    <li
			         data-name="th-filter-sort"
			         data-group="tophive-advanced-filter-wrapper"
			         data-type="price"
			         data-order="asc">
			         '. esc_html__( 'Price: Low to High', 'masterclass' ) .' 
			    </li>
			    <li
			         data-name="th-filter-sort"
			         data-group="tophive-advanced-filter-wrapper"
			         data-type="price"
			         data-order="desc">
			         '. esc_html__( 'Price: High to Low', 'masterclass' ) .' 
			    </li>
			</ul>
		</div>';
	}
	function course_filter_duration($topics){
		echo '<div class="box th-filter-wrapper">
			<button class="th-exclusive-course-filter-toggle">Course Duration <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter">

			    <li
			         data-jplist-control="buttons-range-filter"
			         data-path=".filter-duration"
			         data-group="tophive-advanced-filter-wrapper"
			         data-name="name1"
			         data-from="0">
			         '. esc_html__( 'All', 'masterclass' ) .' 
			    </li>
			    <li
			         data-jplist-control="buttons-range-filter"
			         data-path=".filter-duration"
			         data-group="tophive-advanced-filter-wrapper"
			         data-name="name1"
			         data-from="0"
			         data-to="3">
			         '. esc_html__( '0 to 3 Hours', 'masterclass' ) .' 
			    </li>
			    <li
			         data-jplist-control="buttons-range-filter"
			         data-path=".filter-duration"
			         data-group="tophive-advanced-filter-wrapper"
			         data-name="name1"
			         data-from="4"
			         data-to="6">
			         '. esc_html__( '4 to 6 hours', 'masterclass' ) .' 
			    </li>
			    <li
			         data-jplist-control="buttons-range-filter"
			         data-path=".filter-duration"
			         data-group="tophive-advanced-filter-wrapper"
			         data-name="name1"
			         data-from="7"
			         data-to="9">
			         '. esc_html__( '7 to 9 hours', 'masterclass' ) .' 
			    </li>
			    <li
			         data-jplist-control="buttons-range-filter"
			         data-path=".filter-duration"
			         data-group="tophive-advanced-filter-wrapper"
			         data-name="name1"
			         data-from="10">
			         '. esc_html__( '10+ hours', 'masterclass' ) .' 
			    </li>
			    
			</ul>
		</div>';
	}
	function course_filter_rating(){
		echo '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-rating">
			<button class="th-exclusive-course-filter-toggle">'. esc_html__( 'Rating', 'masterclass' ) .' <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter">
			    <li
			        data-group="tophive-advanced-filter-wrapper"
			        data-name="th-filter-rating"
			        data-from="0"
			        data-to="5">
			        '. esc_html__( 'All Courses', 'masterclass' ) .'
			    </li>
			    <li
			        data-group="tophive-advanced-filter-wrapper"
			        data-name="th-filter-rating"
			        data-from="4.5"
			        data-to="5">
			        '. esc_html__( '4.5 & More', 'masterclass' ) .'
			    </li>
			    <li
			        data-group="tophive-advanced-filter-wrapper"
			        data-name="th-filter-rating"
			        data-from="4.0"
			        data-to="5">
			        '. esc_html__( '4.0 & More', 'masterclass' ) .'
			    </li>
			    <li
			        data-group="tophive-advanced-filter-wrapper"
			        data-name="th-filter-rating"
			        data-from="3.5"
			        data-to="5">
			        '. esc_html__( '3.5 & More', 'masterclass' ) .'
			    </li>
			    <li
			        data-group="tophive-advanced-filter-wrapper"
			        data-name="th-filter-rating"
			        data-from="3.0"
			        data-to="5">
			        '. esc_html__( '3.0 & More', 'masterclass' ) .'
			    </li>
			    
			</ul>
		</div>';
	}
	function course_filter_topic($topics){
		$html = '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-topic">
			<button class="th-exclusive-course-filter-toggle">'. esc_html__( 'Industry Skills', 'masterclass' ) .' <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter">';
		    	foreach ($topics as $topic) {
					$html .= '<li data-name="th-filter-skills" data-group="tophive-advanced-filter-wrapper" data-value="'. $topic->slug .'" class="filter-checkbox">
				            <span class="filter-checkmark"></span>
				        	'. $topic->name .'
			        	</li>';
			    }
			$html .= '</ul>
		</div>';
		echo tophive_sanitize_filter($html);
	}
	function course_filter_category($topics){
		$html = '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-topic">
			<button class="th-exclusive-course-filter-toggle">'. esc_html__( 'Categories', 'masterclass' ) .' <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter">';
		    	foreach ($topics as $topic) {
					$html .= '<li data-name="th-filter-category" data-group="tophive-advanced-filter-wrapper" class="filter-checkbox" data-value="'. $topic->slug .'">
				            <span class="filter-checkmark"></span>
				        	'. $topic->name .'
			        	</li>';
			    }
			$html .= '</ul>
		</div>';
		echo tophive_sanitize_filter($html);
	}
	function course_filter_price($val){
		echo '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-price">
			<button class="th-exclusive-course-filter-toggle">' . esc_html__( 'Price', 'masterclass' ) . ' <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter">

			    <li
			    	data-name="th-filter-price"
			        data-group="tophive-advanced-filter-wrapper"
			        data-value="all">
			    '. esc_html__( 'All', 'masterclass' ) .'
			    </li>
			    <li
			    	data-name="th-filter-price"
			        data-group="tophive-advanced-filter-wrapper"
			        data-value="free">
			    '. esc_html__( 'Free', 'masterclass' ) .'
			    </li>
			    <li
			    	data-name="th-filter-price"
			        data-group="tophive-advanced-filter-wrapper"
			        data-value="paid">
			    '. esc_html__( 'Paid', 'masterclass' ) .'
			    </li>
			    
			</ul>
		</div>';
	}
	function course_filter_level($val){
		echo '<div class="tophive-advanced-filter tophive-advanced-filter-dropdown tophive-advanced-filter-level">
			<button class="th-exclusive-course-filter-toggle">Learners level <i class="ti-angle-down"></i></button>
			<ul class="th-exclusive-course-filter">
			    <li data-name="th-filter-level" class="filter-checkbox" data-group="tophive-advanced-filter-wrapper" data-value="0">
		            <span class="filter-checkmark"></span>
		        	'. esc_html__( 'All Level', 'masterclass' ) .'
			    </li>
			    <li data-name="th-filter-level" class="filter-checkbox" data-group="tophive-advanced-filter-wrapper" data-value="1">
		            <span class="filter-checkmark"></span>
		        	'. esc_html__( 'Beginner', 'masterclass' ) .'
			    </li>
			    <li data-name="th-filter-level" class="filter-checkbox" data-group="tophive-advanced-filter-wrapper" data-value="2">
		            <span class="filter-checkmark"></span>
		        	'. esc_html__( 'Intermediate', 'masterclass' ) .'
			    </li>
			    <li data-name="th-filter-level" class="filter-checkbox" data-group="tophive-advanced-filter-wrapper" data-value="3">
		            <span class="filter-checkmark"></span>
		        	'. esc_html__( 'Advanced', 'masterclass' ) .'
			    </li>
			</ul>
		</div>';
	}
	function course_filter_text_search( $class ){
		echo '<div class="tophive-advanced-filter tophive-advanced-filter-search">
			<input data-group="tophive-advanced-filter-wrapper"  type="text" value="" placeholder="'. esc_html__( 'Search Courses', 'masterclass' ) .'"
			/>
		</div>';
	}
	/**
     * Adds the meta box container.
     */
    public function tophive_learnpress_save_learning_objectives($post_id) {
	    if ( ! isset( $_POST['tophive_learnpress_objective_nonce'] ) ||
	    ! wp_verify_nonce( $_POST['tophive_learnpress_objective_nonce'], 'tophive_learnpress_objective_nonce' ) )
	        return;

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
	        return;

	    if (!current_user_can('edit_post', $post_id))
	        return;

	    $old = get_post_meta($post_id, 'customdata_group', true);
	    $new = array();
	    $invoiceItems = $_POST['TitleItem'];
	    $count = count( $invoiceItems );
	     for ( $i = 0; $i < $count; $i++ ) {
	        if ( $invoiceItems[$i] != '' ) :
	            $new[$i]['TitleItem'] = stripslashes( strip_tags( $invoiceItems[$i] ) );
	        endif;
	    }
	    if ( !empty( $new ) && $new != $old )
	        update_post_meta( $post_id, 'customdata_group', $new );
	    elseif ( empty($new) && $old )
	        delete_post_meta( $post_id, 'customdata_group', $old );
	}
    public function tophive_learnpress_save_featured_video($post_id) {
	    if ( ! isset( $_POST['tophive_learnpress_save_featured_video_nonce'] ) ||
	    ! wp_verify_nonce( $_POST['tophive_learnpress_save_featured_video_nonce'], 'tophive_learnpress_save_featured_video_nonce' ) )
	        return;

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
	        return;

	    if (!current_user_can('edit_post', $post_id))
	        return;

	    $url = esc_url( $_POST['upload_video_url'] );
	    update_post_meta( $post_id, 'tophive_lp_featured_video', $url );
	}

    public function tophive_learnpress_save_levels_tags( $post_id ) {
        if ( ! isset( $_POST['tophive_learnpress_save_levels_tags_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['tophive_learnpress_save_levels_tags_nonce'];
        if ( ! wp_verify_nonce( $nonce, 'tophive_learnpress_save_levels_tags_nonce' ) ) {
            return $post_id;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        $std_level = sanitize_text_field( $_POST['th-student-level'] );
        $course_tag = sanitize_text_field( $_POST['th-course-level'] );

        update_post_meta( $post_id, 'th_wp_student_level_meta_key', $std_level );
        update_post_meta( $post_id, 'th_wp_course_tag_meta_key', $course_tag );
    }

    
	function config( $configs ){
		$configs[] = array(
			'name'           => 'learpress_panel',
			'type'           => 'panel',
			'priority'       => 100,
			'title'          => esc_html__( 'Learnpress', 'masterclass' ),
		);
		return $configs;
	}
	function load_scripts(){
		wp_enqueue_script( 'select-2', get_template_directory_uri() . '/assets/js/select2.min.js', array('jquery') );
		wp_enqueue_style( 'select-2', get_template_directory_uri() . '/assets/css/admin/select2.min.css', $deps = array(), $ver = false, $media = 'all' );
		wp_enqueue_style( 'th-learnpress', get_template_directory_uri() . '/assets/css/compatibility/learnpress.css', $deps = array(), $ver = false, $media = 'all' );
	}
	function tophive_learnpress_order_confirm_button(){
		echo apply_filters( 'learn_press_order_button_html',
			sprintf(
				'<button type="submit" class="lp-button button alt" name="learn_press_checkout_place_order" id="learn-press-checkout-place-order" data-processing-text="%s" data-value="%s">%s</button>',
				esc_html__( 'Processing', 'masterclass' ),
				esc_html__( 'Place order', 'masterclass' ),
				esc_html__( 'Place order', 'masterclass' )
			)
		);
	}
	function tophive_learnpress_course_category_image( $taxonomy ) { ?>
	   <div class="form-field term-group">
			<label for="category-image-id"><?php esc_html_e('Image', 'masterclass'); ?></label>
			<input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
			<div id="category-image-wrapper"></div>
			<p>
		       <input type="button" class="button button-secondary tophive_tax_media_button" id="tophive_tax_media_button" name="tophive_tax_media_button" value="<?php esc_html_e( 'Add Image', 'masterclass' ); ?>" />
		       <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php esc_html_e( 'Remove Image', 'masterclass' ); ?>" />
		    </p>
	   </div>
	 <?php
	}
	function save_category_image ( $term_id, $tt_id ) {
	   if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
	     $image = $_POST['category-image-id'];
	     add_term_meta( $term_id, 'category-image-id', $image, true );
	   }
	}
	function update_category_image ( $term, $taxonomy ) { ?>
		<tr class="form-field term-group-wrap">
		 	<th scope="row">
		   		<label for="category-image-id"><?php esc_html_e( 'Image', 'masterclass' ); ?></label>
		 	</th>
		 	<td>
		   		<?php $image_id = get_term_meta ( $term->term_id, 'category-image-id', true ); ?>
		   		<input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo esc_attr($image_id); ?>">
		   		<div id="category-image-wrapper" width="150" height="150">
				     <?php if ( $image_id ) { ?>
				       <?php
				       	 	$img_url = wp_get_attachment_image_url ( $image_id, 'thumbnail' ); 
				       	 	?>
				       	 		<img src="<?php echo esc_url($img_url); ?>" width="150" height="150" />
				       	 	<?php
			       	 	?>
				     <?php } ?>
		   		</div>
			   	<p>
			     	<input type="button" class="button button-secondary tophive_tax_media_button" id="tophive_tax_media_button" name="tophive_tax_media_button" value="<?php esc_html_e( 'Add Image', 'masterclass' ); ?>" />
			     	<input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php esc_html_e( 'Remove Image', 'masterclass' ); ?>" />
			   	</p>
		 	</td>
		</tr>
	 	<?php
	}
	function updated_category_image ( $term_id, $tt_id ) {
		if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
			$image = $_POST['category-image-id'];
			update_term_meta ( $term_id, 'category-image-id', $image );
		}else {
		 	update_term_meta ( $term_id, 'category-image-id', '' );
		}
	}
	function load_media() {
	 	wp_enqueue_media();
	}
	function add_script() { ?>
	   	<script>
	     	jQuery(document).ready( function($) {
	       		function tophive_media_upload(button_class) {
	         		var _custom_media = true,
	         		_orig_send_attachment = wp.media.editor.send.attachment;
	         		$('body').on('click', button_class, function(e) {
						var button_id = '#'+$(this).attr('id');
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(button_id);
						_custom_media = true;
		           		wp.media.editor.send.attachment = function(props, attachment){
			             	if ( _custom_media ) {
								$('#category-image-id').val(attachment.id);
								$('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
								$('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
			             	} else {
			               		return _orig_send_attachment.apply( button_id, [props, attachment] );
			             	}
			            }
				        wp.media.editor.open(button);
				        return false;
			       	});
	     		}
				tophive_media_upload('.tophive_tax_media_button.button'); 
				$('body').on('click','.ct_tax_media_remove',function(){
					$('#category-image-id').val('');
					$('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
				});
	     		$(document).ajaxComplete(function(event, xhr, settings) {
					var queryStringArr = settings.data.split('&');
					if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
					 	var xml = xhr.responseXML;
					 	$response = $(xml).find('term_id').text();
					 	if($response!=""){
					   		$('#category-image-wrapper').html('');
					 	}
					}
	     		});
	   		});
	 	</script>
 	<?php 
	}
}
function Tophive_LP() {
	return Tophive_LP::get_instance();
}

if ( top_hive()->is_learnpress_active() ) {
	Tophive_LP();
}

?>
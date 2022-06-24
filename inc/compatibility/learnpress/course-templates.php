<?php  
	
class Tophive_LP_Course_Templates{
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
			add_action( 'learn-press/courses-loop-item-title', array($this, 'learn_press_courses_loop_item_before_title'), 12 );
			add_action( 'learn-press/courses-loop-item-title', array($this, 'th_lp_course_footer'), 16);
	        add_action( 'learn-press/courses-loop-item-title', array($this, 'th_lp_course_end'), 17);
	        add_action( 'learn-press/courses-loop-item-title', array($this, 'th_lp_course_starts'),11);

	        add_action( 'tophive/learnpress/default/single/course/grid-three', array($this, 'tophive_default_single_grid_type_three'), 10 , 1 );
	        add_action( 'tophive/learnpress/default/single/course/grid-one', array($this, 'tophive_default_single_grid_type_one'), 10 , 2 );
	        add_action( 'tophive/learnpress/default/single/course/grid-two', array($this, 'tophive_default_single_grid_type_two'), 10 , 2 );
	        add_action( 'tophive/learnpress/elementor/course-block/thumbnail', array($this, 'tophive_elementor_course_thumb'), 10, 2 );
	        add_action( 'tophive/learnpress/elementor/course-block/body', array($this, 'tophive_elementor_course_body'), 10, 2 );
	        add_action( 'tophive/learnpress/elementor/course-block/category', array($this, 'tophive_elementor_course_category'), 10, 3 );
	        add_action( 'tophive/learnpress/elementor/course-block/hover-info', array($this, 'tophive_elementor_course_hover_info'), 10, 2 );
	        add_action( 'tophive/learnpress/elementor/course-block/footer', array($this, 'tophive_elementor_course_footer'), 10, 2 );
		}
	}
	public function randColors(){
		$colors = array(
			array(
				'rgba(90, 90, 204, 0.2)',
				'rgba(90, 90, 204, 1)'
			), 
			array(
				'rgba(140, 103, 198, 0.2)',
				'rgba(140, 103, 198, 1)',
			), 
			array(
				'rgba(216, 99, 216, 0.2)',
				'rgba(216, 99, 216, 1)',
			), 
			array(
				'rgba(108, 40, 161, 0.2)',
				'rgba(108, 40, 161, 1)',
			), 
			array(
				'rgba(248, 129, 45, 0.2)',
				'rgba(248, 129, 45, 1)',
			), 
			array(
				'rgba(150, 229, 16, 0.2)',
				'rgba(150, 229, 16, 1)',
			), 
			array(
				'rgba(36, 85, 206, 0.2)',
				'rgba(36, 85, 206, 1)',
			), 
			array(
				'rgba(248, 51, 78, 0.2)',
				'rgba(248, 51, 78, 1)',
			), 
			array(
				'rgba(244, 180, 93, 0.2)',
				'rgba(244, 180, 93, 1)',
			), 
			array(
				'rgba(242, 176, 164, 0.2)',
				'rgba(242, 176, 164, 1)',
			),
		);
		return $colors[array_rand($colors)];
	}
	public function tophive_elementor_course_footer( $settings, $id ){
		$levels = apply_filters( 'tophive/learnpress/course-meta/level', $id );

		$author = apply_filters( 'tophive/learnpress/course-meta/instructor-url', $id, true );

		$lessons = apply_filters( 'tophive/learnpress/course-meta/lessons', $id );

		$rating = apply_filters( 'tophive/learnpress/course-meta/rating', $id );

		$html = '<div class="th-course-footer ec-align-items-center ec-row ec-no-gutters ec-px-2 ec-py-3">';
    		// course author
			$html .= 'yes' == $settings['show_author'] ? '<div class="ec-col"><p class="ec-mb-0 course-author">' . $author['html'] . '</p></div>' : '';
			$html .= 'yes' == $settings['show_level'] ? '<div class="ec-col"><p class="ec-mb-0 course-level">' . $levels['html_icon'] . '</p></div>' : '';
			$html .= 'yes' == $settings['show_lessons'] ? '<div class="ec-col"><p class="ec-mb-0 course-lessons">' . $lessons['html'] . '</p></div>' : '';

			if( $settings['show_rating'] ){
	        	$html .= '<div class="ec-col">';
	        		$html .= $rating['html'];
	        	$html .= '</div>';
        	}
        return $html .= '</div>';
	}
	public function tophive_elementor_course_hover_info( $settings, $id ){
		global $current_user;
		$added_to_wishlist = get_user_meta($current_user->ID, '_lpr_wish_list', true);
		$html = '';

		$html .= '<div class="hover-section ec-d-none ec-d-md-block">';
	    	$course = learn_press_get_course( $id );
	    	$course_meta = apply_filters( 'tophive/learnpress/course-meta', $id );
    		$learning_points = get_post_meta( $id, 'customdata_group', true );
    		$learning_points = array_splice( $learning_points, 0, 3 );
	    	$desc_excerpt = isset( $settings['hi_desc_excerpt'] );

	    	if( 'yes' === $settings['show_hi_title'] ){
    			$html .= '<div class="hover-info-title mb-3"><a href="'. get_the_permalink( $id ) .'">' . get_the_title( $id ) . '</a></div>';
	    	}
			$html .= '<p class="hover-info-date ec-text-success ec-mb-1">' . $course_meta['updated']['html'] . '</p>';
	    	$html .= '<div class="hover-info-lessons-duration">';
	    		$html .= 'yes' == $settings['show_hi_level'] ? '<span class="hover-info-level">'. $course_meta['level']['html_icon'] .'</span>' : '';
	    		$html .= 'yes' == $settings['show_hi_lessons'] ? '<span class="hover-info-lessons">'. $course_meta['lessons']['html'] .'</span>' : '';
    			$html .= 'yes' == $settings['show_hi_course_duration'] ? '<span class="hover-info-duration">'. $course_meta['duration']['html'] .'</span>' : '';
	    	$html .= '</div>';

    		$html .= $settings['show_hi_course_details'] ? '<div class="hover-info-desc">' . wp_trim_words( get_the_excerpt( $id ), $settings['hi_desc_excerpt'] ) . '</div>' : '';
    		if( $settings['show_learning_points'] == 'yes' ){
	    		foreach ($learning_points as $point) {
	    			$html .= '<p class="learning-points"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						  <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
						</svg> '. $point['TitleItem'] .'</p>';
	    		}
    		}
	    	$html .= '<div class="th-display-flex">';
	    		if( $settings['show_hi_add_to_cart'] == 'yes' ){	
			    	$html .= '<form method="post" enctype="multipart/form-data">
						' . do_action( 'learn-press/before-purchase-button' ) . '
				        <input type="hidden" name="purchase-course" value="'. esc_attr( $course->get_id() ) .'"/>
				        <input type="hidden" name="purchase-course-nonce"
				               value="'. esc_attr( \LP_Nonce_Helper::create_course( 'purchase' ) ) .'"/>
				        <input type="submit" class="hover-info-add-cart" value="'. esc_html( apply_filters( 'learn-press/purchase-course-button-text', esc_html__( 'Purchase', 'masterclass' ) ) ) .'"
				        />
						'.do_action( 'learn-press/after-purchase-button' ).'
				    </form>';
	    		}

		    	if ( $settings['show_hi_wishlist'] ) {
		    		if(!empty($added_to_wishlist))
		    			{
		    				$wishlist = in_array($id, $added_to_wishlist) ? 'on ' : '';
							$wishlist_text = in_array($id, $added_to_wishlist) ? esc_html__('Remove From  wishlist', 'masterclass') : esc_html__('Add to wishlist', 'masterclass');
						}else{
							$wishlist = '';
							$wishlist_text = esc_html__('Add to wishlist', 'masterclass');
						}
		    		$html .= '<div class="hover-info-wishlist"><a class="'. $wishlist .'hover-wishlist-'. $id .' th-tooltip" data-id="'. $id .'" data-nonce="'. wp_create_nonce( 'course-toggle-wishlist' ) .'" href="#"><i class="'. $settings['wishlist_icon'] .'"></i><span class="th-tooltip-text">'. $wishlist_text .'</span></a></div>';
		    	}
			$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
	function tophive_elementor_course_body( $settings, $id ){
		$html = '<div class="th-course-details">';
			// return sale price
			$price = apply_filters( 'tophive/learnpress/course-meta/price', $id );

			// Pricing
			$html .= $settings['show_pricing'] ? '<div class="price-section"><p class="th-sale-price">' . $price['html'] . '</p></div>' : '';

        	$html .= apply_filters( 'tophive/learnpress/elementor/course-block/category', $settings['cat_display'], $settings['show_cat_bg'], $id );

            $html .= '<a href="'. get_the_permalink( $id ) .'"><h5 class="course-block-title">' . get_the_title( $id ) . '</h5></a>';
            $html .= apply_filters( 'tophive/elementor/learnpress/course-block/after-title', $id, $settings );

            $html .= $settings['show_desc'] ? '<div class="th-description">' . wp_trim_words( get_the_excerpt( $id ), $settings['word_count'] ) . '</div>' : '';
            if( 'yes' == $settings['show_hover_info'] ){
				$html .= apply_filters( 'tophive/learnpress/elementor/course-block/hover-info', $settings, $id );
            }

		return $html .= '</div>';
	}

	public function tophive_elementor_course_category( $show, $showbg, $id ){
		$color = $this->randColors();
		$bg = $showbg ? $color[0] : 'transparent';
		$textcolor = $color[1];

    	$terms = get_the_terms( $id , 'course_category' );
		return  $show ?
        		'<div class="th-course-category"><p class="course-category ec-d-inline-block" style="background:'. $bg .';color:'. $textcolor .'">'.$terms[0]->name.'</p></div>' 
        		: 
        		'';
	}
	function tophive_elementor_course_thumb( $show, $id ){
		return 'yes' === $show ? '<div class="th-course-thumb ec-mb-4 ec-mb-md-0">'. get_the_post_thumbnail($id , 'large' ) .'</div>' : '';
	}

	function tophive_default_single_grid_type_one( $course, $columns ){
		$id = $course->get_id();
		$columns = !empty($columns) ? $columns : 'ec-col-md-3';
		$course_meta = apply_filters( 'tophive/learnpress/course-meta', $id );

		$course_html = '<div class="'. $columns .' ec-mb-2">';
			$course_html .= '<div class="th-default-course-block theme-primary-color-head-hover">';
				$course_html .= '<a class="course-block-title" href="'. get_the_permalink( $id ) .'">';

					// course thumbnail
		    		$course_html .= '<div class="th-course-thumb">';  
		    			$course_html .= get_the_post_thumbnail($id , 'large' );
		    			$course_html .= '<div class="price-section"><span class="th-sale-price">' . $course_meta['price']['html'] . '</span></div>';
		    		$course_html .= '</div>';
		    		// course details
					$course_html .= '<div class="th-course-details">';
						$course_html .= '<div class="row no-gutters ec-no-gutters">';
						$course_html .= '</div>';
						$course_html .= '<h5>' . get_the_title($id) . '</h5>';

						//course reviews
		                    $course_html .= $course_meta['rating']['html'];
		            	//course reviews ends

						// course meta
		        		$course_html .= '<p class="course-meta">' . $course_meta['level']['html'] . ' • '.$course_meta['lessons']['html'] . ' • ' . $course_meta['duration']['html'] . '</p>';
		        	
						// author meta
						$course_html .= '<div class="default-course-author">';
							$course_html .= $course_meta['instructor']['avatar'] . $course_meta['instructor']['name'];
						$course_html .= '</div>';
						// author meta ends

					$course_html .= '</div>';
				$course_html .= '</a>';
			$course_html .= '</div>';
		return $course_html .= '</div>';
	}
	function tophive_default_single_grid_type_two( $course, $settings ){
		$id = $course->get_id();

		$delay = isset($settings['autoplay_delay']) ? $settings['autoplay_delay'] : '';
		$classes = [];

		$style = '';
		if( isset($settings['style']) ){
			$style = ' style="'. $settings['style'] .'"';
		}

		if(isset($settings['hover_info_position'])){
			$classes[] = $settings['hover_info_position'];	
		}
		if( isset($settings['use_slider']) ){
			$classes[] = 'swiper-slide';
		}
		if( isset($settings['select_layout']) ){
			if( $settings['select_layout'] === 'thumb-left' || $settings['select_layout'] === 'thumb-right' ){
					$classes[] = 'ec-d-md-flex';
				}
		}
		if( isset($settings['classes']) ){
			$classes = array_merge( $classes , $settings['classes'] );
		}
		$html ='';

		if( !isset($settings['use_slider']) ){
			$html .= '<div class="'. $settings['select_columns'] .'">';
		}
			// single course block
			$html .= '<div class="th-course-block ' . implode(' ', $classes) .'" data-swiper-autoplay="'. $delay .'"'. $style .'>';

				if( $settings['select_layout'] === 'thumb-left' ){
					//Get course Thumbnail
					$html .= '<div>';
	            		$html .= apply_filters( 'tophive/learnpress/elementor/course-block/thumbnail', $settings['thumbnail_show_hide'], $id);
					$html .= '</div>';

					// Course body
					$html .= '<div class="ec-w-100">';
						$html .= apply_filters( 'tophive/learnpress/elementor/course-block/body', $settings, $id );

						// Footer
						$html .= apply_filters( 'tophive/learnpress/elementor/course-block/footer', $settings, $id );
					$html .= '</div>';
				}elseif( $settings['select_layout'] === 'thumb-right' ){

					//Get course Thumbnail
					$html .= '<div>';
						$html .= apply_filters( 'tophive/learnpress/elementor/course-block/body', $settings, $id );
						// Course body ends

						// Footer
						$html .= apply_filters( 'tophive/learnpress/elementor/course-block/footer', $settings, $id );
					$html .= '</div>';

					$html .= '<div>';
	            		$html .= apply_filters( 'tophive/learnpress/elementor/course-block/thumbnail', $settings['thumbnail_show_hide'], $id);
					$html .= '</div>';
				}else{
	        		$html .= apply_filters( 'tophive/learnpress/elementor/course-block/thumbnail', $settings['thumbnail_show_hide'], $id);
					$html .= apply_filters( 'tophive/learnpress/elementor/course-block/body', $settings, $id );
					// Course body ends

					// Footer
					$html .= apply_filters( 'tophive/learnpress/elementor/course-block/footer', $settings, $id );
				}
	            // Footer ends
			$html .= '</div>';
			// end single course block
		if( !isset($settings['use_slider']) ){
			$html .= '</div>';
		}

		return $html;
	}
	function tophive_default_single_grid_type_three( $course ){
		$id = $course->get_id();
		
		$course_meta = apply_filters( 'tophive/learnpress/course-meta', $id );			
		
		$html = '<div class="th-course-block-three">';
			$html .= '<a class="course-block-title" href="'. get_the_permalink( $id ) .'">';
			$html .= '<div class="th-default-course-block th-thumb-contained ec-d-flex">';
				// Thumbnail Starts
        		$html .= '<div class="th-course-thumb ec-w-25">'. get_the_post_thumbnail($id , 'large' ) .'</div>';
				// Thumbnail Ends
				$html .= '<div class="th-course-details ec-d-flex ec-w-75">';
					$html .= '<div class="ec-w-75">';
						$html .= '<h5 class="filter-title">' . get_the_title($id) . '</h5>';

						$html .= '<p class="post-excerpt ec-mb-0">' . wp_trim_words( get_the_excerpt($id), 15 ) . '</p>';
						
						//course rating
	                    $html .= $course_meta['rating']['html'];
                    	// Course Meta
						$html .= '<p class="ec-mb-2 course-meta">' . $course_meta['student']['html'] . ' • ' . $course_meta['duration']['html'] . ' • ' . $course_meta['lessons']['html'] . '</p>';
						//course author 
						$html .= '<div class="default-course-author">';
							$html .= $course_meta['instructor']['avatar'] . $course_meta['instructor']['name'];
						$html .= '</div>';

					$html .= '</div>';
        			$html .= '<div class="ec-w-25 price-section ec-mt-2">
        				<p class="th-sale-price">' . $course_meta['price']['html'] . '</p>
        				<p class="ec-mt-3 student-level">' . $course_meta['level']['html_icon'] . '</p>
        				</div>';
					
				$html .= '</div>';
			$html .= '</div>';
			$html .= '</a>';
            // Footer ends
		$html .= '</div>';
		return $html;
	}
	function th_lp_course_starts(){
		?>
			<div class="th-course-details theme-primary-color-head-hover">
		<?php
	}
	function th_lp_course_end(){
		?>
			</div>
		<?php
	}

	function th_lp_course_footer(){
		$course = LP_Global::course();
		$rated = learn_press_get_course_rate(get_the_ID(), false )['rated'];    
        $item = learn_press_get_course_rate(get_the_ID(), false )['total'];    
    	$lessons = $course->count_items( LP_LESSON_CPT );    
	    $percent = ( ! $rated ) ? 0 : min( 100, ( round( $rated * 2 ) / 2 ) * 20 );

		?>
			<div class="course-footer ec-row">
				<div class="ec-col-md-6">
					<div class="review-stars-rated">
                        <div class="review-stars empty"></div>
                        <div class="review-stars filled" style="width:<?php echo esc_attr($percent); ?>%;"> </div> 
                        <span class="ec-ml-1"><?php echo ' ' ?>(<?php echo esc_attr(sprintf('%1.1f', $rated)); ?>) <?php echo esc_attr($item); ?></span>
                    </div> 
				</div>
				<div class="ec-col-md-6 ec-text-right">
					<p class="ec-mb-0 course-lessons"><?php echo esc_attr($lessons) . esc_html__( ' Lessons', 'masterclass' ); ?></p>
				</div>
			</div>
		<?php
	}
	function learn_press_courses_loop_item_before_title(){
		$course = LP_Global::course();
		$id = $course->get_id();
		?>
			<div class="ec-row">
				<div class="ec-col course-students-number">
					<?php echo apply_filters( 'tophive/learnpress/course-meta/students', $id )['html'] ?>
				</div>
				<div class="ec-col ec-text-right course-price">
					<h4><?php echo tophive_sanitize_filter($course->get_price_html()); ?></h4>
				</div>
			</div>
		<?php
	}

}
function Tophive_LP_Course_Templates() {
	return Tophive_LP_Course_Templates::get_instance();
}

if ( top_hive()->is_learnpress_active() ) {
	Tophive_LP_Course_Templates();
}

?>
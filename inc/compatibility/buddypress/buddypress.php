<?php 
/**
 * MasterClass Integration for BuddyBress, Buddypress-gammiperss, Buddypress-learnpress
 */
class Tophive_BP
{
    static $_instance;

	static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	function is_active() {
		return top_hive()->is_buddypress_active();
	}
	function __construct(){
		if( $this->is_active() ){
			add_action( 'tophive/sidebar-id', array($this, 'th_bp_sidebar_id'), 10, 2 );
	        add_action( 'wp_enqueue_scripts', array($this, 'load_scripts') );
			add_action( 'bp_core_setup_globals', array( $this, 'th_bp_set_default_component') );
			add_action( 'bp_actions', array( $this, 'th_bp_reorder_buddypress_profile_tabs'), 999 );
			add_action( 'learn-press/buddypress/profile-tabs', array( $this, 'tophive_reorder_buddypress_lp_tabs'), 10, 1 );
			// add_action( 'bp_before_member_header_meta', array( $this, 'th_bp_load_member_desc' ) );
			add_action( 'bp_member_header_actions', array( $this, 'th_bp_load_member_social_profiles' ) );
			add_action( 'bp_profile_header_meta', array( $this, 'tophive_bp_profile_header_meta' ) );
			add_action( 'bp_before_activity_post_form', array( $this, 'th_bp_before_activity_post_form' ) );
			// add_action( 'bp_activity_entry_meta', array( $this, 'th_bp_activity_entry_meta_likes' ) );
			add_action( 'bp_directory_members_actions', array( $this, 'tophive_bp_send_private_message_button'),10 );
			add_action( 'bp_get_add_friend_button', array( $this, 'tophive_bp_add_friend_button'), 10, 1 );
			add_action( 'bp_directory_members_item', array( $this, 'tophive_gamipress_bp_member_loop'), 10, 1 );
			add_action( 'bp_get_the_notification_action_links', array($this, 'th_bp_notification_action_links'), 10, 2 );
		}
	}
	function th_bp_notification_action_links( $retval, $r ){
		$r = wp_parse_args( $args, array(
			'before' => '',
			'after'  => '',
			'sep'    => '  ',
			'links'  => array(
				bp_get_the_notification_mark_link( $user_id ),
				bp_get_the_notification_delete_link( $user_id )
			)
		) );
		$retval = $r['before'] . implode( $r['sep'], $r['links'] ) . $r['after'];
		return $retval;
	}
	function tophive_bp_get_context_user_id( $user_id = 0 ) {
 
	    if ( bp_is_my_profile() || ! is_user_logged_in() ) {
	        return 0;
	    }
	    if ( ! $user_id ) {
	        $user_id = bp_get_member_user_id();
	    }
	    if ( ! $user_id && bp_is_user() ) {
	        $user_id = bp_displayed_user_id();
	    }
	 
	    return apply_filters( 'tophive/buddypress/user/id', $user_id );
	}
	function tophive_bp_get_send_private_message_url() {
 
	    $user_id = $this->tophive_bp_get_context_user_id();
	 
	    if ( ! $user_id || $user_id == bp_loggedin_user_id() ) {
	        return;
	    }
	    if ( bp_is_my_profile() || ! is_user_logged_in() ) {
	        return false;
	    }
	    return apply_filters( 'tophive/buddypress/user/message/url', wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) ) );
	}
	function tophive_bp_get_send_private_message_button() {
	    $user_id = $this->tophive_bp_get_context_user_id();
	    if ( ! $user_id || $user_id == bp_loggedin_user_id() ) {
	        return;
	    }
	    $defaults = array(
	        'id'                => 'private_message-' . $user_id,
	        'component'         => 'messages',
	        'must_be_logged_in' => true,
	        'block_self'        => false,
	        'parent_element'    => 'li',
	        'wrapper_id'        => 'send-private-message-' . $user_id,
	        'wrapper_class'     => 'send-private-message',
	        'link_href'         => $this->tophive_bp_get_send_private_message_url(),
	        'link_title'        => esc_html__( 'Send a private message to this user.', 'masterclass' ),
	        'link_text'         => '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chat-left-dots" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v11.586l2-2A2 2 0 0 1 4.414 11H14a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
				  <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
				</svg>',
	        'link_class'        => 'send-message',
	    );
	 
	    $btn = bp_get_button( $defaults );
	 
	    return apply_filters( 'tophive/buddypress/user/message/button', $btn );
	}
	function tophive_bp_memebers_add_content(){
		echo gamipress_bp_before_member_header();
	}
	function tophive_bp_send_private_message_button(){
		echo '' . $this->tophive_bp_get_send_private_message_button();
	}
	function tophive_bp_add_friend_button($button) {
		switch ( $button['id'] ) {
			case 'pending' :
				$button['link_text'] = '<svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10zM11 7.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
				</svg>';
			break;

			case 'is_friend' :
				$button['link_text'] = '<svg width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm5-.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5z"/>
				</svg>';
			break;

			default:
				$button['link_text'] = '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M8 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm6 5c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10zM13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
				</svg>';
		}
		return $button;
	}
	function th_bp_activity_entry_meta_likes(){
		echo '<div class="generic-button">Likes</div>';
	}
	function th_bp_before_activity_post_form(){
		if( !function_exists('bp_get_user_firstname') ){
			return;
		}
		echo '<p class="what-is-new-avatar-text">' . bp_get_user_firstname( bp_get_loggedin_user_fullname() ) . '</p>';
	}
	function th_bp_load_member_social_profiles(){
		$html = '';
		$socials = [];
		$facebook 	= get_the_author_meta( 'facebook', bp_displayed_user_id() );
		$twitter 	= get_the_author_meta( 'twitter', bp_displayed_user_id() );
		$linkedin 	= get_the_author_meta( 'linkedin', bp_displayed_user_id() );
		$youtube 	= get_the_author_meta( 'youtube', bp_displayed_user_id() );
		$slack 		= get_the_author_meta( 'slack', bp_displayed_user_id() );
		if( !empty($facebook) ){
			array_push($socials, array( 'name' => 'facebook', 'url' => $facebook ));
		}
		if( !empty($twitter) ){
			array_push($socials, array( 'name' => 'twitter', 'url' => $twitter ));
		}
		if( !empty($linkedin) ){
			array_push($socials, array( 'name' => 'linkedin', 'url' => $linkedin ));
		}
		if( !empty($youtube) ){
			array_push($socials, array( 'name' => 'youtube', 'url' => $youtube ));
		}
		if( !empty($slack) ){
			array_push($socials, array( 'name' => 'slack', 'url' => $slack ));
		}
		$html .= '<ul class="bp-socials-vertical">';
		foreach ($socials as $value) {
			$html .= '<li class="'. $value['name'] .'"><a href="'. $value['url'] .'"><i class="fa fa-'. $value['name'] .'"></i></a></li>';
		}
		$html .= '</ul>';
		echo tophive_sanitize_filter($html);
	}
	function tophive_bp_profile_header_meta(){
		global $bp;

		?>
			<p><small class="hide-badge">@<?php bp_displayed_user_mentionname(); ?></small> • <small>Joined : <?php echo date( "F j, Y", strtotime( $bp->displayed_user->userdata->user_registered ) ) ?> </small></p>
		<?php
	}
	function th_bp_load_member_desc(){
		echo '<p class="bb-author-bio">' . get_the_author_meta( 'description', bp_displayed_user_id() ) . '</p>';
	}
	function th_bp_set_default_component () {
        define ( 'BP_DEFAULT_COMPONENT', 'activity' );
	}
	function tophive_reorder_buddypress_lp_tabs() {
		return array(
			array(
				'name'                    => __( 'Courses', 'masterclass' ),
				'slug'                    => $this->get_tab_courses_slug(),
				'show_for_displayed_user' => true,
				'screen_function'         => array( $this, 'bp_tab_content' ),
				'default_subnav_slug'     => 'all',
				'position'                => 20
			),
			array(
				'name'                    => __( 'Quizzes', 'masterclass' ),
				'slug'                    => $this->get_tab_quizzes_slug(),
				'show_for_displayed_user' => true,
				'screen_function'         => array( $this, 'bp_tab_content' ),
				'default_subnav_slug'     => 'all',
				'position'                => 20
			)
		);
	}
	public function get_tab_quizzes_slug() {
		$slugs = LP()->settings->get( 'profile_endpoints' );
		$slug  = '';
		if ( isset( $slugs['profile-quizzes'] ) ) {
			$slug = $slugs['profile-quizzes'];
		}
		if ( ! $slug ) {
			$slug = 'quizzes';
		}

		return sanitize_title(apply_filters( 'learn_press_bp_tab_quizzes_slug', $slug ));
	}
	public function bp_tab_content() {
		global $bp;
		$type = '';
		$current_component = $bp->current_component;
		$slugs = LP()->settings->get( 'profile_endpoints' );
		$tab_slugs_default = array( 'profile-courses', 'profile-quizzes', 'profile-orders' );
		$tab_slugs = array_keys( $slugs, $current_component );
		$tab_slugs = wp_parse_args($tab_slugs, $tab_slugs_default);
		$tab_slug = array_shift( $tab_slugs );
		if ( in_array( $tab_slug, $tab_slugs_default ) ) {
			switch ( $current_component ) {
				case  $this->get_tab_courses_slug():
					$type = 'courses';
					break;
				case  $this->get_tab_quizzes_slug():
					$type = 'quizzes';
					break;
				case  $this->get_tab_orders_slug():
					$type = 'orders';
					break;
				default:
					break;
			}
			if ( $type ) {
				add_action( 'bp_template_content', array( $this, "bp_tab_{$type}_content" ) );
				bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
			}
		}
		do_action( 'learn-press/buddypress/bp-tab-content', $current_component );
	}
	public function bp_tab_courses_content() {
		$args = array( 'user' => learn_press_get_current_user() );
		learn_press_get_template( 'profile/courses.php', $args, learn_press_template_path() . '/addons/buddypress/', LP_ADDON_BUDDYPRESS_PATH . '/templates' );
	}
	public function bp_tab_quizzes_content() {
		$args = array( 'user' => learn_press_get_current_user() );
		learn_press_get_template( 'profile/tabs/quizzes.php', $args );
	}
	public function get_tab_courses_slug() {
		$slugs = LP()->settings->get( 'profile_endpoints' );
		$slug  = '';
		if ( isset( $slugs['profile-courses'] ) ) {
			$slug = $slugs['profile-courses'];
		}
		if ( ! $slug ) {
			$slug = 'courses';
		}

		return sanitize_title(apply_filters( 'learn_press_bp_tab_courses_slug', $slug ));
	}
	function th_bp_reorder_buddypress_profile_tabs() {
	    $nav = buddypress()->members->nav;

	    $nav_items = array(
	        'activity' => 10,
	        'friends'  => 40,
	        'messages' => 50,
	        'groups'   => 60,
	        'blogs'    => 70,
	        'profile'  => 80,
	        'settings' => 90,
	    );
	 
	    foreach ( $nav_items as $nav_item => $position ) {
	        $nav->edit_nav( array( 'position' => $position ), $nav_item );
	    }
	    return $nav;
	}
	function th_bp_sidebar_id( $id, $sidebar_type = null ){
		if( $this->is_active() ){
			if( bp_current_component() !== false ){
				return 'buddy-press-sidebar';
			}else{
				return $id;
			}
		}
		return $id;
	}
	function load_scripts(){
		wp_enqueue_style( 'th-buddypress', get_template_directory_uri() . '/assets/css/compatibility/buddypress.css', $deps = array(), $ver = false, $media = 'all' );
		wp_enqueue_script('th-buddyrpess', get_template_directory_uri() . '/assets/js/compatibility/buddypress.js', array('jquery'), false, false);
	}
	function tophive_gamipress_bp_member_loop() {

		if( !class_exists('GamiPress_BuddyPress') ){
			return;
		}

	    if ( bp_is_my_profile() || ! is_user_logged_in() ) {
	        return 0;
	    }
        $user_id = bp_get_member_user_id();
	    if ( ! $user_id && bp_is_user() ) {
	        $user_id = bp_displayed_user_id();
	    }

	    if ( ! is_user_logged_in() && ! $user_id ) {
	        return;
	    }
	    ?>
    	<div class="tophive-buddypress-gamipress">
	    <?php
	    /* -------------------------------
	     * Points
	       ------------------------------- */

	    $points_placement = gamipress_bp_get_option( 'points_placement', '' );

	    if( $points_placement === 'top' || $points_placement === 'both' ) {

	        // Setup points types vars
	        $points_types = gamipress_get_points_types();
	        $points_types_slugs = gamipress_get_points_types_slugs();

	        // Get points display settings
	        $points_types_to_show = gamipress_bp_members_get_points_types();
	        $points_types_thumbnail = (bool) gamipress_bp_get_option( 'members_points_types_top_thumbnail', false );
	        $points_types_thumbnail_size = (int) gamipress_bp_get_option( 'members_points_types_top_thumbnail_size', 25 );
	        $points_types_label = (bool) gamipress_bp_get_option( 'members_points_types_top_label', false );

	        // Parse thumbnail size
	        if( $points_types_thumbnail_size > 0 ) {
	            $points_types_thumbnail_size = array( $points_types_thumbnail_size, $points_types_thumbnail_size );
	        } else {
	            $points_types_thumbnail_size = 'gamipress-points';
	        }

	        if( ! empty( $points_types_to_show ) ) : ?>

	            <div class="gamipress-buddypress-points">

	                <?php foreach( $points_types_to_show as $points_type_to_show ) :

	                // If points type not registered, skip
	                if( ! in_array( $points_type_to_show, $points_types_slugs ) )
	                    continue;

	                $points_type = $points_types[$points_type_to_show];
	                $user_points = gamipress_get_user_points( $user_id, $points_type_to_show ); ?>

	                <div class="gamipress-buddypress-points-type gamipress-buddypress-<?php echo $points_type_to_show; ?>">
	                    <?php if( $points_types_thumbnail ) : ?>

	                        <span class="activity gamipress-buddypress-points-thumbnail gamipress-buddypress-<?php echo $points_type_to_show; ?>-thumbnail">
	                            <?php echo gamipress_get_points_type_thumbnail( $points_type_to_show, $points_types_thumbnail_size ); ?>
	                        </span>

	                    <?php endif; ?>

	                    <span class="activity gamipress-buddypress-user-points gamipress-buddypress-user-<?php echo $points_type_to_show; ?>">
	                        <?php echo $user_points; ?>
	                    </span>

	                    <?php // The points label ?>
	                    <?php if( $points_types_label ) : ?>

	                        <span class="activity gamipress-buddypress-points-label gamipress-buddypress-<?php echo $points_type_to_show; ?>-label">
	                            <?php echo _n( $points_type['singular_name'], $points_type['plural_name'], $user_points ); ?>
	                        </span>

	                    <?php endif; ?>

	                </div>

	                <?php endforeach; ?>
	            </div>
	        <?php endif;

	    }

	    /* -------------------------------
	     * Achievements
	       ------------------------------- */

	    $achievements_placement = gamipress_bp_get_option( 'achievements_placement', '' );

	    if( $achievements_placement === 'top' || $achievements_placement === 'both' ) {

	        // Setup achievement types vars
	        $achievement_types = gamipress_get_achievement_types();
	        $achievement_types_slugs = gamipress_get_achievement_types_slugs();

	        // Get achievements display settings
	        $achievement_types_to_show = gamipress_bp_members_get_achievements_types();
	        $achievement_types_thumbnail = (bool) gamipress_bp_get_option( 'members_achievements_top_thumbnail', false );
	        $achievement_types_thumbnail_size = (int) gamipress_bp_get_option( 'members_achievements_top_thumbnail_size', 25 );
	        $achievement_types_title = (bool) gamipress_bp_get_option( 'members_achievements_top_title', false );
	        $achievement_types_link = (bool) gamipress_bp_get_option( 'members_achievements_top_link', false );
	        $achievement_types_label = (bool) gamipress_bp_get_option( 'members_achievements_top_label', false );
	        $achievement_types_limit = (int) gamipress_bp_get_option( 'members_achievements_top_limit', 10 );

	        // Parse thumbnail size
	        if( $achievement_types_thumbnail_size > 0 ) {
	            $achievement_types_thumbnail_size = array( $achievement_types_thumbnail_size, $achievement_types_thumbnail_size );
	        } else {
	            $achievement_types_thumbnail_size = 'gamipress-achievement';
	        }

	        if( ! empty( $achievement_types_to_show ) ) : ?>

	            <div class="gamipress-buddypress-achievements">

	                <?php foreach( $achievement_types_to_show as $achievement_type_to_show ) :

	                    // If achievements type not registered, skip
	                    if( ! in_array( $achievement_type_to_show, $achievement_types_slugs ) )
	                        continue;

	                    $achievement_type = $achievement_types[$achievement_type_to_show];
	                    $user_achievements = gamipress_get_user_achievements( array(
	                        'user_id' => $user_id,
	                        'achievement_type' => $achievement_type_to_show,
	                        'groupby' => 'achievement_id',
	                        'limit' => $achievement_types_limit,
	                    ) );

	                    // If user has not earned any achievements of this type, skip
	                    if( empty( $user_achievements ) ) {
	                        continue;
	                    } ?>

	                    <div class="gamipress-buddypress-achievement gamipress-buddypress-<?php echo $achievement_type_to_show; ?>">

	                        <?php // The achievement type label ?>
	                        <?php if( $achievement_types_label ) : ?>
	                        <span class="activity gamipress-buddypress-achievement-type-label gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-label">
	                            <?php echo $achievement_type['plural_name']; ?>:
	                        </span>
	                        <?php endif; ?>

	                        <?php // Lets to get just the achievement thumbnail and title
	                        foreach( $user_achievements as $user_achievement ) : ?>

	                            <?php // The achievement thumbnail ?>
	                            <?php if( $achievement_types_thumbnail ) : ?>

	                                <?php // The achievement link ?>
	                                <?php if( $achievement_types_link ) : ?>

	                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="activity gamipress-buddypress-achievement-thumbnail gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-thumbnail">
	                                        <?php echo gamipress_get_achievement_post_thumbnail( $user_achievement->ID, $achievement_types_thumbnail_size ); ?>
	                                    </a>

	                                <?php else : ?>

	                                    <span title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="activity gamipress-buddypress-achievement-thumbnail gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-thumbnail">
	                                        <?php echo gamipress_get_achievement_post_thumbnail( $user_achievement->ID, $achievement_types_thumbnail_size ); ?>
	                                    </span>

	                                <?php endif; ?>

	                            <?php endif; ?>

	                            <?php // The achievement title ?>
	                            <?php if( $achievement_types_title ) : ?>

	                                <?php // The achievement link ?>
	                                <?php if( $achievement_types_link ) : ?>

	                                    <a href="<?php echo get_permalink( $user_achievement->ID ); ?>" title="<?php echo get_the_title( $user_achievement->ID ); ?>" class="gamipress-buddypress-achievement-title gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-title">
	                                        <?php echo get_the_title( $user_achievement->ID ); ?>
	                                    </a>

	                                <?php else : ?>

	                                    <span class="activity gamipress-buddypress-achievement-title gamipress-buddypress-<?php echo $achievement_type_to_show; ?>-title">
	                                        <?php echo get_the_title( $user_achievement->ID ); ?>
	                                    </span>

	                                <?php endif; ?>

	                            <?php endif; ?>

	                        <?php endforeach; ?>

	                    </div>

	                <?php endforeach; ?>

	            </div>

	        <?php endif;

	    }

	    /* -------------------------------
	     * Ranks
	       ------------------------------- */

	    $ranks_placement = gamipress_bp_get_option( 'ranks_placement', '' );

	    if( $ranks_placement === 'top' || $ranks_placement === 'both' ) {

	        // Setup rank types vars
	        $rank_types = gamipress_get_rank_types();
	        $rank_types_slugs = gamipress_get_rank_types_slugs();

	        // Get ranks display settings
	        $rank_types_to_show = gamipress_bp_members_get_ranks_types();
	        $rank_types_thumbnail = (bool) gamipress_bp_get_option( 'members_ranks_top_thumbnail', false );
	        $rank_types_thumbnail_size = (int) gamipress_bp_get_option( 'members_ranks_top_thumbnail_size', 25 );
	        $rank_types_title = (bool) gamipress_bp_get_option( 'members_ranks_top_title', false );
	        $rank_types_link = (bool) gamipress_bp_get_option( 'members_ranks_top_link', false );
	        $rank_types_label = (bool) gamipress_bp_get_option( 'members_ranks_top_label', false );

	        // Parse thumbnail size
	        if( $rank_types_thumbnail_size > 0 ) {
	            $rank_types_thumbnail_size = array( $rank_types_thumbnail_size, $rank_types_thumbnail_size );
	        } else {
	            $rank_types_thumbnail_size = 'gamipress-rank';
	        }

	        if( ! empty( $rank_types_to_show ) ) : ?>

	            <div class="gamipress-buddypress-ranks">

	                <?php foreach( $rank_types_to_show as $rank_type_to_show ) :

	                    // If rank type not registered, skip
	                    if( ! in_array( $rank_type_to_show, $rank_types_slugs ) )
	                        continue;

	                    $rank_type = $rank_types[$rank_type_to_show];
	                    $user_rank = gamipress_get_user_rank( $user_id, $rank_type_to_show ); ?>

	                    <div class="gamipress-buddypress-rank gamipress-buddypress-<?php echo $rank_type_to_show; ?>">

	                        <?php // The rank type label ?>
	                        <?php if( $rank_types_label ) : ?>
	                        <span class="activity gamipress-buddypress-rank-label gamipress-buddypress-<?php echo $rank_type_to_show; ?>-label">
	                            <?php echo $rank_type['singular_name']; ?>:
	                        </span>
	                        <?php endif; ?>

	                        <?php // The rank thumbnail ?>
	                        <?php if( $rank_types_thumbnail ) : ?>

	                            <?php // The rank link ?>
	                            <?php if( $rank_types_link ) : ?>

	                                <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="activity gamipress-buddypress-rank-thumbnail gamipress-buddypress-<?php echo $rank_type_to_show; ?>-thumbnail">
	                                    <?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, $rank_types_thumbnail_size ); ?>
	                                </a>

	                            <?php else : ?>

	                                <span title="<?php echo $user_rank->post_title; ?>" class="activity gamipress-buddypress-rank-thumbnail gamipress-buddypress-<?php echo $rank_type_to_show; ?>-thumbnail">
	                                <?php echo gamipress_get_rank_post_thumbnail( $user_rank->ID, $rank_types_thumbnail_size ); ?>
	                            </span>

	                            <?php endif; ?>

	                        <?php endif; ?>

	                        <?php // The rank title ?>
	                        <?php if( $rank_types_title ) : ?>

	                            <?php // The rank link ?>
	                            <?php if( $rank_types_link ) : ?>

	                                <a href="<?php echo get_permalink( $user_rank->ID ); ?>" title="<?php echo $user_rank->post_title; ?>" class="activity gamipress-buddypress-rank-title gamipress-buddypress-<?php echo $rank_type_to_show; ?>-title">
	                                    <?php echo $user_rank->post_title; ?>
	                                </a>

	                            <?php else : ?>

	                                <span class="activity gamipress-buddypress-rank-title gamipress-buddypress-<?php echo $rank_type_to_show; ?>-title">
	                                <?php echo $user_rank->post_title; ?>
	                            </span>

	                            <?php endif; ?>

	                        <?php endif; ?>

	                    </div>

	                <?php endforeach; ?>
	            </div>
	        <?php endif;

	    }
	    ?>
	    	</div>
	    <?php

	}
}
function Tophive_BP() {
	return Tophive_BP::get_instance();
}

if ( top_hive()->is_buddypress_active() ) {
	Tophive_BP();
}	
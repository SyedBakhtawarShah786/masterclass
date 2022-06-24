<?php
/*
Template Name: Profile Page
*/

get_header('bare');

$profile       = learn_press_get_profile();
$filter_status = LP_Request::get_string( 'filter-status' );
$query         = $profile->query_courses( 'own', array( 'status' => $filter_status, 'limit' => 1000000) );

$tabs    = $profile->get_tabs();
$current = $profile->get_current_tab();
$user = $profile->get_user();
if(!is_user_logged_in()){

$profile = LP_Global::profile();
$loginfields  = $profile->get_login_fields();
$signupfields  = $profile->get_register_fields();
?>

<div class="ec-row">
	<div class="ec-col-md-3"></div>
	<div class="ec-col-md-6">
		<div class="tophive-popup-content-wrapper" id="tophive-signin-signup">
			<div class="ec-d-block login-segment">
			    <h3 class="ec-text-center ec-mb-4"><?php esc_html_e( 'Login', 'masterclass' ); ?></h3>

			    <form name="th-modal-login" class="th-modal-login" method="post">
			    	<p class="ec-text-center login-notices"></p>
			        <ul class="form-fields">
						<li>
							<div class="th-form-field">
								<div class="th-form-field">
									<label for="username"><?php esc_html_e( 'Username or email', 'masterclass' ) ?>
									</label>
								</div>
								<div class="th-form-field">
									<input size="30" placeholder="<?php esc_html_e( 'Username or email', 'masterclass' ); ?>" type="text" required="required" id="username" class="" name="username">
								</div>
							</div>
						</li>
						<li class="form-field">
							<div class="th-form-field">
								<label for="password"><?php esc_html_e( 'Password', 'masterclass' ); ?></label>
							</div>	
							<div class="th-form-field">
								<input size="30" placeholder="<?php esc_html_e( 'Password', 'masterclass' ); ?>" type="password" required="required" id="password" class="th-form-field" name="password">
							</div>			                
						</li>
			        </ul>

					<p>
			            <label>
			                <input type="checkbox" name="rememberme"/>
							<?php esc_html_e( 'Remember me', 'masterclass' ); ?>
			            </label>
			            <a class="ec-float-right" href="<?php echo wp_lostpassword_url(); ?>"><?php esc_html_e( 'Lost your password?', 'masterclass' ); ?></a>
			        </p>
			        <p class="ec-mx-4">
			            <input type="hidden" name="th-modal-login-nonce"
			                   value="<?php echo wp_create_nonce( 'th-modal-login' ); ?>">
			            <button type="submit" class="components-button tophive-infinity-button"><?php esc_html_e( 'Login', 'masterclass' ); ?>
			            </button>
			        </p>
			        <p class="ec-mb-0 ec-text-center">
			        	<?php esc_html_e( 'Not Registered? ', 'masterclass' ) ?><a href="#" class="switch-register"><b><?php esc_html_e( 'Sign up', 'masterclass' ); ?></b></a>
			        </p>
			    </form>

			</div>
			<div class="ec-d-none signup-segment">
			    <h3 class="ec-text-center ec-mb-4"><?php esc_html_e( 'Sign Up', 'masterclass' ); ?></h3>

			    <form name="th-modal-register" class="th-modal-register" method="post">

			    	<p class="ec-text-center login-notices"></p>
			        <ul class="form-fields">
		                <li>
							<div class="th-form-field">
								<label for="reg_username"><?php esc_html_e( 'Username', 'masterclass' ); ?></label>
							</div>
							<div class="th-form-field">
								<input size="30" placeholder="<?php esc_html_e( 'Username', 'masterclass' ); ?>" type="text" required="required" id="reg_username" class="th-form-field" name="reg_username">
							</div>
						</li>
						<li>
							<div class="th-form-field">
								<label for="reg_email"><?php esc_html_e( 'Email', 'masterclass' ); ?></label>	
							</div>
							<div class="th-form-field">
								<input size="30" placeholder="<?php esc_html_e( 'Email', 'masterclass' ); ?>" type="email" required="" id="reg_email" class="th-form-field" name="reg_email">
							</div>
						</li>
						<li class="form-field">
							<div class="th-form-field">
								<label for="reg_password"><?php esc_html_e('Password', 'masterclass'); ?>
									
								</label>
							</div>
							<div class="th-form-field">
								<input size="30" placeholder="<?php esc_html_e( 'Password', 'masterclass' ); ?>" type="password" required="" id="reg_password" class="th-form-field" name="reg_password">
								<p id="reg_password-description" class="description">
									<?php esc_html_e( 'The password should be at least twelve characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; )', 'masterclass' ); ?>
								</p>
							</div>
						</li>
			        </ul>
					<p class="ec-mb-3 ec-text-center ">
			        	<?php esc_html_e( 'Already registered? ', 'masterclass' ) ?> <a href="#" class="ec-d-inline-block switch-login"><b><?php esc_html_e( 'Signin', 'masterclass' ) ?></b></a>
			        </p>
			        <p class="ec-mx-4">
						<?php wp_nonce_field( 'th-modal-register', 'th-modal-register-nonce' ); ?>
			            <button class="components-button tophive-infinity-button" type="submit"><?php esc_html_e( 'Register', 'masterclass' ); ?></button>
			        </p>

			    </form>

			</div>

		</div>
	</div>
	<div class="ec-col-md-3"></div>
</div>

<?php
}
else{if ( $profile->is_public() ) {
	?>

    <div class="tophive-lp-user-profile">
		
		
		<div class="tophive-lp-headbar">
			<div class="ec-container-fluid">
				<div class="tophive-lp-heading">
					<div class="ec-row">
						<div class="ec-col-md-3 ec-text-center">
							<?php echo get_avatar( get_current_user_id(), 250,'','', array('class'=> ''));?>
						</div>
						<div class="ec-col-md-9 ec-px-md-3">
							<h3 class="font-weight-bold"><?php echo esc_html($user->get_display_name()); ?></h3>
							<div class="ec-d-flex ec-mt-3">
							<?php 
							$students = 0;
							foreach ($query['items'] as $user_course) {
								$course = learn_press_get_course( $user_course );
								$students += intval(get_post_meta( $user_course, 'count_enrolled_users', true ));
							}
							$space = is_rtl() ? 'ec-pl-4' : 'ec-pr-4';
							echo '<h5 class="'. $space .'">' . $query['total'] . '<small>' . esc_html__(' Courses', 'masterclass') . '</small></h5>'; 
							echo '<h5>' . $students .'<small>'. esc_html__(' Students', 'masterclass') . '</small></h5>';
							?>
							</div>
							<p>
								<?php echo esc_attr($user->get_description()); ?>
							</p>
							
							<p class="ec-mb-1"><small><b>
							<?php 
								if(!empty(get_the_author_meta( 'user_email', get_current_user_id() ))){
									esc_html_e( 'Email : ', 'masterclass' );
									echo get_the_author_meta( 'user_email', get_current_user_id() );
								}
							?>
							</b></small></p>
							<p class="ec-mb-1">
								<small><b>
								<?php 
									if(!empty(get_the_author_meta( 'user_url', get_current_user_id() ))){
										esc_html_e( 'Website : ', 'masterclass' ); ?><?php echo '<a href="'. get_the_author_meta( 'user_url', get_current_user_id() ) .'">' . parse_url(get_the_author_meta( 'user_url', get_current_user_id() ))['host'] . '</a>';
									}
								?>
								</b></small>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="th-lp-profile-content">
			<div class="ec-row">
				<div class="ec-col-md-3">
					<ul class="th-lp-profile-nav">
						<?php
							foreach ( $profile->get_tabs()->tabs() as $tab_key => $tab_data ) {

					            /**
					             * @var $tab_data LP_Profile_Tab
					             */
								
								if ( $tab_data->is_hidden() || ! $tab_data->user_can_view() ) {
									continue;
								}
								if( $tab_key == 'dashboard' ){
									continue;
								}

								$slug        = $profile->get_slug( $tab_data, $tab_key );
								$link        = $profile->get_tab_link( $tab_key, true );
								$tab_classes = array( esc_attr( $tab_key ) );
								/**
								 * @var $tab_data LP_Profile_Tab
								 */
								$sections    = $tab_data->sections();

								if ( $sections && sizeof( $sections ) > 1 ) {
									$tab_classes[] = 'has-child';
								}

								if ( $profile->is_current_tab( $tab_key ) ) {
									$tab_classes[] = 'active';
								} 
								?>

					            <li class="<?php echo join( ' ', $tab_classes ) ?>" id="<?php echo esc_attr($tab_key); ?>">
					                <!--tabs-->
										
					                <a href="<?php echo esc_url( $link ); ?>" data-slug="<?php echo esc_attr( $link ); ?>">
										<?php 
											if( $tab_key == 'courses' ){
												?>
													<svg width="15px" height="15px" viewBox="0 0 16 16" class="bi bi-journal-album" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													  	<path d="M4 1h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1H2a2 2 0 0 1 2-2z"/>
													  	<path d="M2 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H2zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H2zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H2zm3-6.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-5z"/>
													  	<path fill-rule="evenodd" d="M6 11.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5z"/>
													</svg> 
												<?php
											}
											else if( $tab_key == 'quizzes' ){
												?>
													<svg width="15px" height="15px" viewBox="0 0 16 16" class="bi bi-question-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  														<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  														<path d="M5.25 6.033h1.32c0-.781.458-1.384 1.36-1.384.685 0 1.313.343 1.313 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.007.463h1.307v-.355c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.326 0-2.786.647-2.754 2.533zm1.562 5.516c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
													</svg>
												<?php
											}
											else if( $tab_key == 'wishlist' ){
												?>
													<svg width="15px" height="15px" viewBox="0 0 16 16" class="bi bi-heart" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														<path fill-rule="evenodd" d="M8 2.748l-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
													</svg>
												<?php
											}
											else if( $tab_key == 'orders' ){
												?>
													<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-basket3" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  														<path fill-rule="evenodd" d="M10.243 1.071a.5.5 0 0 1 .686.172l3 5a.5.5 0 1 1-.858.514l-3-5a.5.5 0 0 1 .172-.686zm-4.486 0a.5.5 0 0 0-.686.172l-3 5a.5.5 0 1 0 .858.514l3-5a.5.5 0 0 0-.172-.686z"/>
  														<path d="M0 6.5A.5.5 0 0 1 .5 6h15a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H.5a.5.5 0 0 1-.5-.5v-1zM.81 9c0 .035.004.07.011.105l1.201 5.604A1 1 0 0 0 3 15.5h10a1 1 0 0 0 .978-.79l1.2-5.605A.495.495 0 0 0 15.19 9h-1.011L13 14.5H3L1.821 9H.81z"/>
													</svg>
												<?php
											}
											else if( $tab_key == 'settings' ){
												?>
													<svg width="15px" height="15px" viewBox="0 0 16 16" class="bi bi-gear" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													  <path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>
													  <path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>
													</svg>
												<?php
											}
										echo apply_filters( 'learn_press_profile_' . $tab_key . '_tab_title', esc_html( $tab_data['title'] ), $tab_key ); ?>
					                </a>
					                <?php if ( $sections && sizeof( $sections ) > 1 ) { ?>

				                    <ul class="profile-tab-sections">
										<?php foreach ( $sections as $section_key => $section_data ) {

											$classes = array( esc_attr( $section_key ) );
											if ( $profile->is_current_section( $section_key, $section_key ) ) {
												$classes[] = 'active';
											}

											$section_slug = $profile->get_slug( $section_data, $section_key );
											$section_link = $profile->get_tab_link( $tab_key, $section_slug );
											?>

				                            <li class="<?php echo join( ' ', $classes ); ?>">
				                                <a href="<?php echo esc_url($section_link); ?>"><?php echo esc_attr($section_data['title']); ?></a>
				                            </li>

										<?php } ?>

				                    </ul>

								<?php } ?>

				            </li>
						<?php } ?>
					</ul>
				</div>
				<div class="ec-col-md-9">
					<div id="learn-press-profile-content" class="lp-profile-content">

						<?php foreach ( $tabs as $tab_key => $tab_data ) {
							
						if ( ! isset( $user ) ) {
							$user = learn_press_get_current_user();
						}
							if ( ! $profile->tab_is_visible_for_user( $tab_key ) ) {
								continue;
							}
							?>

					        <div id="profile-content-<?php echo esc_attr( $tab_key ); ?>">
								<?php
								// show profile sections
								do_action( 'learn-press/before-profile-content', $tab_key, $tab_data, $user ); ?>

								<?php if ( empty( $tab_data['sections'] ) ) {
									if ( is_callable( $tab_data['callback'] ) ) {
										echo call_user_func_array( $tab_data['callback'], array( $tab_key, $tab_data, $profile ) );
									} else {
										do_action( 'learn-press/profile-content', $tab_key, $tab_data, $user );
									}
								} else {
									foreach ( $tab_data['sections'] as $key => $section ) {
										if ( $profile->get_current_section( '', false, false ) === $section['slug'] ) {
											if ( isset( $section['callback'] ) && is_callable( $section['callback'] ) ) {
												echo call_user_func_array( $section['callback'], array( $key, $section, $user ) );
											} else {
												do_action( 'learn-press/profile-section-content', $key, $section, $user );
											}
										}
									}
								} ?>

								<?php do_action( 'learn-press/after-profile-content' ); ?>
					        </div>

						<?php } ?>

					</div>
				</div>
			</div>
		</div>
    </div>

<?php 
} else {
	esc_html_e( 'This user does not public their profile.', 'masterclass' );
}}

get_footer();
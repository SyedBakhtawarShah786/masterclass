<?php 
/*
Template Name: Instructor Page
*/
	get_header('bare');

	$icons_url = esc_url(get_template_directory_uri()) . '/assets/images/svg-icons'; 
	$slug = get_query_var( 'user_slug' );
	$user = get_user_by('slug', $slug);
	if( !empty($user) ){
		$userid = $user->ID;
	}else{
		$userid = get_current_user_id();
	}
	$per_page = 8;
	$args = array(
		'author' => $userid,
		'post_type' => 'lp_course',
		'posts_per_page' => $per_page
	);
	$query = new WP_Query($args);
?>
<div class="tophive-lp-user-profile">
	<div class="tophive-lp-headbar ec-mt-5">
		<div class="ec-container-fluid">
			<div class="tophive-lp-heading">
				<div class="ec-row">
					<div class="ec-col-md-3 ec-text-center">
						<?php echo get_avatar( $userid, 250,'','', array('class'=> ''));?>
					</div>
					<div class="ec-col-md-9 ec-px-3">
						<h3 class="font-weight-bold ec-mb-3"><?php echo get_the_author_meta('display_name', $userid); ?></h3>
						<div class="ec-d-flex ec-mt-3">
							<?php 
								$students = 0;
								foreach ($query->posts as $post) {
									$students += intval(get_post_meta( $post->ID, 'count_enrolled_users', true ));
								}
								echo '<h5 class="ec-pr-4">' . $query->found_posts . '<small>' . esc_html__(' Courses', 'masterclass') . '</small></h5>'; 
								echo '<h5>' . $students .'<small>'. esc_html__(' Students', 'masterclass') . '</small></h5>';
							?>
						</div>
						<p>
							<?php echo get_the_author_meta('description', $userid); ?>
						</p>
						<p class="ec-mb-1"><small><b>
						<?php 
							if(!empty(get_the_author_meta( 'user_email', $userid ))){
								esc_html_e( 'Email : ', 'masterclass' );
								echo get_the_author_meta( 'user_email' );
							}
						?>
						</b></small></p>
						<p class="ec-mb-1"><small><b>
						<?php 
							if(!empty(get_the_author_meta( 'user_url', $userid ))){
								esc_html_e( 'Website : ', 'masterclass' );
								echo '<a href="'. get_the_author_meta( 'user_url' ) .'">' . parse_url(get_the_author_meta( 'user_url', $userid ))['host'] . '</a>';
							}
						?>
						</b></small></p>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php apply_filters( 'tophive/learnpress/page/instructor/courses', $query , $userid, $icons_url, $per_page ); ?>
</div>

<?php
	get_footer();
?>
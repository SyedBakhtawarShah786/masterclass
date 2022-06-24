<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

?>

<?php

$verified_badge = '';

if( class_exists('BP_Verified_Member') ){
	global $bp_verified_member;
	$verified_badge = $bp_verified_member->get_verified_badge();
}else{
	$verified_badge = '';
}

do_action( 'bp_before_member_header' ); ?>

<div id="cover-image-container">
	<a id="header-cover-image" href="<?php bp_displayed_user_link(); ?>"></a>

	<div id="item-header-cover-image">
		<?php if( function_exists('bp_add_friend_button') ): ?>
			<div class="profile-friendship-button">
				<?php echo bp_add_friend_button(); ?>
			</div>
		<?php endif; ?>
		<div id="item-header-avatar">
			<?php bp_displayed_user_avatar( 'type=full' ); ?>
			<?php
				$authors_rating = apply_filters( 'tophive/learnpress/author/rating', bp_displayed_user_id() );
				echo tophive_sanitize_filter($authors_rating);
			?>
		</div>
		<div id="item-header-content">
			<h2 class="user-nicename"><?php echo get_the_author_meta( 'display_name', bp_displayed_user_id() ) . $verified_badge; ?></h2>
			<div id="item-buttons"><?php
				do_action( 'bp_member_header_actions' ); ?></div>
			<?php 
				if( get_user_meta( bp_displayed_user_id(), 'designation', true ) ){
					?>
						<p class="bp-user-designation"><small><strong><?php echo get_user_meta( bp_displayed_user_id(), 'designation', true ); ?></strong></small></p>
					<?php
				}
			?>

				<?php do_action( 'bp_before_member_header_meta' ); ?>
			<div id="item-meta">
				<?php do_action( 'bp_profile_header_meta' ); ?>
			</div>
		</div>
	</div>
</div>

<?php

do_action( 'bp_after_member_header' ); ?>

<div id="template-notices" role="alert" aria-atomic="true">
	<?php do_action( 'template_notices' ); ?>
</div>

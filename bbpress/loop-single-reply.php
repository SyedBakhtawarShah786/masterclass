<?php

/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage MasterClass
 */
?>

<div <?php bbp_reply_class(); ?>>
	<div class="bbp-reply-author">

		<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

			<?php bbp_reply_author_link( array( 'type' => 'avatar', 'size' => 40, 'show_role' => false ) ); ?>

		<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

	</div>

	<div class="bbp-reply-content">

		<?php do_action( 'bbp_theme_before_reply_content' ); ?>
		
		<?php bbp_reply_author_link( array( 'type' => 'name', 'show_role' => true ) ); ?>
		<div class="bbp-reply-content-meta">
			<span class="bbp-reply-post-date"><?php bbp_reply_post_date(0, true); ?></span>
			<?php bbp_reply_admin_links( array( 'sep' => '' ) ); ?>
		</div>
		<?php bbp_reply_content(); ?>


		<?php do_action( 'bbp_theme_after_reply_content' ); ?>

	</div>
</div>

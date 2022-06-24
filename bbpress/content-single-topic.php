<?php

/**
 * Single Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

?>

<div id="bbpress-forums" class="bbpress-wrapper">

	<?php bbp_breadcrumb(); ?>

	<?php bbp_topic_subscription_link(); ?>

	<?php bbp_topic_favorite_link(); ?>

	<div class="tophive-bbpress-topic-question-main">
		<h2 class="topic-title"><?php bbp_topic_title(); ?></h2>
		<div class="topic-content">
			<div class="bbp-reply-author">
				<?php echo bbp_get_topic_author_link( 
					array(
						'post_id' => bbp_get_topic_id(), 
						'type' => 'avatar', 
						'size' => 40, 
						'show_role' => false 
					) 
				); ?>
			</div>
			<div class="bbp-reply-content">
				<?php echo bbp_get_topic_author_link( 
					array(
						'post_id' => bbp_get_topic_id(), 
						'type' => 'name', 
						'size' => 40, 
						'show_role' => false 
					) 
				); ?>
				<?php the_content(); ?>
			</div>			
		</div>
	</div>	

	<?php do_action( 'bbp_template_before_single_topic' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php bbp_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php bbp_topic_tag_list(); ?>

		<?php if ( bbp_show_lead_topic() ) : ?>

			<?php bbp_get_template_part( 'content', 'single-topic-lead' ); ?>

		<?php endif; ?>

		<?php if ( bbp_has_replies() ) : ?>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

			<?php bbp_get_template_part( 'loop',       'replies' ); ?>

			<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

		<?php endif; ?>

		<?php bbp_get_template_part( 'form', 'reply' ); ?>

	<?php endif; ?>

	<?php bbp_get_template_part( 'alert', 'topic-lock' ); ?>

	<?php do_action( 'bbp_template_after_single_topic' ); ?>

</div>

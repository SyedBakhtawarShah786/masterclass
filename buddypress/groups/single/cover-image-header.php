<?php
/**
 * Tophive Theme - BuddyPress - Groups Cover Image Header.
 *
 * @since 3.0.0
 * @version 3.2.0
 */
?>

<div id="cover-image-container">
	<div id="header-cover-image"></div>

	<div id="item-header-cover-image">
		<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
			<div id="item-header-avatar">
				<a href="<?php echo esc_url( bp_get_group_permalink() ); ?>" title="<?php echo esc_attr( bp_get_group_name() ); ?>">

					<?php bp_group_avatar(); ?>

				</a>
				<?php bp_get_template_part( 'groups/single/parts/header-item-actions' ); ?>	
			</div><!-- #item-header-avatar -->
		<?php endif; ?>
		<?php	if ( ! bp_nouveau_groups_front_page_description() ) : ?>
				<div id="item-header-content">
					<h4 class="group-name">
						<?php echo bp_group_name(); ?>
					</h4>
					<p class="group-status">
						<?php echo esc_html( bp_nouveau_group_meta()->status ); ?>		
					</p>

					<?php echo bp_nouveau_group_meta()->group_type_list; ?>
					<?php bp_nouveau_group_hook( 'before', 'header_meta' ); ?>

					<?php if ( bp_nouveau_group_has_meta_extra() ) : ?>
						<div class="item-meta">

							<?php echo bp_nouveau_group_meta()->extra; ?>

						</div><!-- .item-meta -->
					<?php endif; ?>

					<?php bp_nouveau_group_header_buttons(); ?>
					<?php if ( ! bp_nouveau_groups_front_page_description() && bp_nouveau_group_has_meta( 'description' ) ) : ?>
						<div class="desc-wrap">
							<div class="group-description">
								<?php bp_group_description(); ?>
							</div><!-- //.group_description -->
						</div>
					<?php endif; ?>
				</div><!-- #item-header-content -->
		<?php endif; ?>

	</div><!-- #item-header-cover-image -->


</div><!-- #cover-image-container -->

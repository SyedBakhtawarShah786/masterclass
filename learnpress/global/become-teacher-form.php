

<?php
/**
 * Template for displaying the form let user fill out their information to become a teacher.
 * @version  3.0.0
 */

	defined( 'ABSPATH' ) || exit();
?>

<div class="learn-press-become-teacher-form become-teacher-form learn-press-form">

    <form name="become-teacher-form" method="post" enctype="multipart/form-data">

		<?php do_action( 'learn-press/before-become-teacher-form' ); ?>

		<?php do_action( 'learn-press/become-teacher-form' ); ?>

		<?php do_action( 'learn-press/after-become-teacher-form' ); ?>

    </form>

</div>

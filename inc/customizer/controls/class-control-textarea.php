<?php
class Tophive_Customizer_Control_Textarea extends Tophive_Customizer_Control_Base {
	static function field_template() {
		echo '<script type="text/html" id="tmpl-field-tophive-textarea">';
		self::before_field();
		?>
		<?php echo self::field_header(); ?>
		<div class="tophive-field-settings-inner">
			<textarea rows="10" class="tophive-input" data-name="{{ field.name }}">{{ field.value }}</textarea>
		</div>
		<?php
		self::after_field();
		echo '</script>';
	}
}

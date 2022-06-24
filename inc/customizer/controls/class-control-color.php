<?php
class Tophive_Customizer_Control_Color extends Tophive_Customizer_Control_Base {
	static function field_template() {
		?>
		<script type="text/html" id="tmpl-field-tophive-color">
		<?php
		self::before_field();
		?>
		<?php echo self::field_header(); ?>
		<div class="tophive-field-settings-inner">
			<div class="tophive-input-color" data-default="{{ field.default }}">
				<input type="hidden" class="tophive-input tophive-input--color" data-name="{{ field.name }}" value="{{ field.value }}">
				<input type="text" class="tophive--color-panel" placeholder="{{ field.placeholder }}" data-alpha="true" value="{{ field.value }}">
			</div>
		</div>
		<?php
		self::after_field();
		?>
		</script>
		<?php
	}
}

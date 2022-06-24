<?php
class Tophive_Customizer_Control_Hidden extends Tophive_Customizer_Control_Base {
	static function field_template() {
		?>
		<script type="text/html" id="tmpl-field-tophive-hidden">
		<?php
		self::before_field();
		?>
		<input type="hidden" class="tophive-input tophive-only" data-name="{{ field.name }}" value="{{ field.value }}">
		<?php
		self::after_field();
		?>
		</script>
		<?php
	}
}

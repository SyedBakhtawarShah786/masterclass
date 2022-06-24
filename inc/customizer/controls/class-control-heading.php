<?php
class Tophive_Customizer_Control_Heading extends Tophive_Customizer_Control_Base {
	static function field_template() {
		?>
		<script type="text/html" id="tmpl-field-tophive-heading">
		<?php
		self::before_field();
		?>
		<h3 class="tophive-field--heading">{{ field.label }}</h3>
		<?php
		self::after_field();
		?>
		</script>
		<?php
	}
}

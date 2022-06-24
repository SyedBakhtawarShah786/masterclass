<?php
class Tophive_Customizer_Control_Font extends Tophive_Customizer_Control_Base {
	static function field_template() {
		?>
		<script type="text/html" id="tmpl-field-tophive-css-ruler">
		<?php
		self::before_field();
		?>
		<?php echo self::field_header(); ?>
		<div class="tophive-field-settings-inner">
			<input type="hidden" class="tophive--font-type" data-name="{{ field.name }}-type" >
			<div class="tophive--font-families-wrapper">
				<select class="tophive--font-families" data-value="{{ JSON.stringify( field.value ) }}" data-name="{{ field.name }}-font"></select>
			</div>
			<div class="tophive--font-variants-wrapper">
				<label><?php _e( 'Variants', 'masterclass' ); ?></label>
				<select class="tophive--font-variants" data-name="{{ field.name }}-variant"></select>
			</div>
			<div class="tophive--font-subsets-wrapper">
				<label><?php _e( 'Languages', 'masterclass' ); ?></label>
				<div data-name="{{ field.name }}-subsets" class="list-subsets">
				</div>
			</div>
		</div>
		<?php
		self::after_field();
		?>
		</script>
		<?php
	}
}

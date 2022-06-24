<?php
class Tophive_Customizer_Control_Typography extends Tophive_Customizer_Control_Base {
	static function field_template() {
		echo '<script type="text/html" id="tmpl-field-tophive-typography">';
		self::before_field();
		?>
		<?php echo self::field_header(); ?>
		<div class="tophive-actions">
			<a href="#" class="action--reset" data-control="{{ field.name }}" title="<?php esc_attr_e( 'Reset to default', 'masterclass' ); ?>"><span class="dashicons dashicons-image-rotate"></span></a>
			<a href="#" class="action--edit" data-control="{{ field.name }}" title="<?php esc_attr_e( 'Toggle edit panel', 'masterclass' ); ?>"><span class="dashicons dashicons-edit"></span></a>
		</div>
		<div class="tophive-field-settings-inner">
			<input type="hidden" class="tophive-typography-input tophive-only" data-name="{{ field.name }}" value="{{ JSON.stringify( field.value ) }}" data-default="{{ JSON.stringify( field.default ) }}">
		</div>
		<?php
		self::after_field();
		echo '</script>';
		?>
		<div id="tophive-typography-panel" class="tophive-typography-panel">
			<div class="tophive-typography-panel--inner">
				<input type="hidden" id="tophive--font-type">
				<div id="tophive-typography-panel--fields"></div>
			</div>
		</div>
		<?php
	}
}

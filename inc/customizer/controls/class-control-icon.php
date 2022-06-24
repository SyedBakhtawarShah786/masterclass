<?php
class Tophive_Customizer_Control_Icon extends Tophive_Customizer_Control_Base {
	static function field_template() {
		echo '<script type="text/html" id="tmpl-field-tophive-icon">';
		self::before_field();
		?>
		<#
		if ( ! _.isObject( field.value ) ) {
			field.value = { };
		}
		#>
		<?php echo self::field_header(); ?>
		<div class="tophive-field-settings-inner">
			<div class="tophive--icon-picker">
				<div class="tophive--icon-preview">
					<input type="hidden" class="tophive-input tophive--input-icon-type" data-name="{{ field.name }}-type" value="{{ field.value.type }}">
					<div class="tophive--icon-preview-icon tophive--pick-icon">
						<# if ( field.value.icon ) {  #>
							<i class="{{ field.value.icon }}"></i>
						<# }  #>
					</div>
				</div>
				<input type="text" readonly class="tophive-input tophive--pick-icon tophive--input-icon-name" placeholder="<?php esc_attr_e( 'Pick an icon', 'masterclass' ); ?>" data-name="{{ field.name }}" value="{{ field.value.icon }}">
				<span class="tophive--icon-remove" title="<?php esc_attr_e( 'Remove', 'masterclass' ); ?>">
					<span class="dashicons dashicons-no-alt"></span>
					<span class="screen-reader-text">
					<?php _e( 'Remove', 'masterclass' ); ?></span>
				</span>
			</div>
		</div>
		<?php
		self::after_field();
		echo '</script>';
		?>
		<div id="tophive--sidebar-icons">
			<div class="tophive--sidebar-header">
				<a class="customize-controls-icon-close" href="#">
					<span class="screen-reader-text"><?php _e( 'Cancel', 'masterclass' ); ?></span>
				</a>
				<div class="tophive--icon-type-inner">
					<select id="tophive--sidebar-icon-type">
						<option value="all"><?php _e( 'All Icon Types', 'masterclass' ); ?></option>
					</select>
				</div>
			</div>
			<div class="tophive--sidebar-search">
				<input type="text" id="tophive--icon-search" placeholder="<?php esc_attr_e( 'Type icon name', 'masterclass' ); ?>">
			</div>
			<div id="tophive--icon-browser"></div>
		</div>
		<?php
	}
}

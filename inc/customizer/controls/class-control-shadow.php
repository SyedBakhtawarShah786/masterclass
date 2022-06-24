<?php
class Tophive_Customizer_Control_Shadow extends Tophive_Customizer_Control_Base {
	static function field_template() {
		echo '<script type="text/html" id="tmpl-field-tophive-shadow">';
		self::before_field();
		?>
		<#
			if ( ! _.isObject( field.value ) ) {
			field.value = { };
			}

			var uniqueID = field.name + ( new Date().getTime() );
		#>
			<?php echo self::field_header(); ?>
			<div class="tophive-field-settings-inner">

				<div class="tophive-input-color" data-default="{{ field.default }}">
					<input type="hidden" class="tophive-input tophive-input--color" data-name="{{ field.name }}-color" value="{{ field.value.color }}">
					<input type="text" class="tophive--color-panel" data-alpha="true" value="{{ field.value.color }}">
				</div>

				<div class="tophive--gr-inputs">
					<span>
						<input type="number" class="tophive-input tophive-input-css change-by-js"  data-name="{{ field.name }}-x" value="{{ field.value.x }}">
						<span class="tophive--small-label"><?php _e( 'X', 'masterclass' ); ?></span>
					</span>
					<span>
						<input type="number" class="tophive-input tophive-input-css change-by-js"  data-name="{{ field.name }}-y" value="{{ field.value.y }}">
						<span class="tophive--small-label"><?php _e( 'Y', 'masterclass' ); ?></span>
					</span>
					<span>
						<input type="number" class="tophive-input tophive-input-css change-by-js" data-name="{{ field.name }}-blur" value="{{ field.value.blur }}">
						<span class="tophive--small-label"><?php _e( 'Blur', 'masterclass' ); ?></span>
					</span>
					<span>
						<input type="number" class="tophive-input tophive-input-css change-by-js" data-name="{{ field.name }}-spread" value="{{ field.value.spread }}">
						<span class="tophive--small-label"><?php _e( 'Spread', 'masterclass' ); ?></span>
					</span>
					<span>
						<span class="input">
							<input type="checkbox" class="tophive-input tophive-input-css change-by-js" <# if ( field.value.inset == 1 ){ #> checked="checked" <# } #> data-name="{{ field.name }}-inset" value="{{ field.value.inset }}">
						</span>
						<span class="tophive--small-label"><?php _e( 'inset', 'masterclass' ); ?></span>
					</span>
				</div>
			</div>
			<?php
			self::after_field();
			echo '</script>';
	}
}

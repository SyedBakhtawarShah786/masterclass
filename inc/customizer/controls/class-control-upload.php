<?php
class Tophive_Customizer_Control_Upload extends Tophive_Customizer_Control_Media {
	static function field_template() {
		echo '<script type="text/html" id="tmpl-field-tophive-upload">';
		self::before_field();
		?>
		<#
		if ( ! _.isObject(field.value) ) {
			field.value = {};
		}
		var url = field.value.url;
		#>
		<?php echo self::field_header(); ?>
		<div class="tophive-field-settings-inner tophive-media-type-{{ field.type }}">
			<div class="tophive--media">
				<input type="hidden" class="attachment-id" value="{{ field.value.id }}" data-name="{{ field.name }}">
				<input type="hidden" class="attachment-url"  value="{{ field.value.url }}" data-name="{{ field.name }}-url">
				<input type="hidden" class="attachment-mime"  value="{{ field.value.mime }}" data-name="{{ field.name }}-mime">
				<div class="tophive-image-preview <# if ( url ) { #> tophive--has-file <# } #>" data-no-file-text="<?php esc_attr_e( 'No file selected', 'masterclass' ); ?>">
					<#

					if ( url ) {
						if ( url.indexOf('http://') > -1 || url.indexOf('https://') ){

						} else {
							url = Tophive_Control_Args.home_url + url;
						}

						if ( ! field.value.mime || field.value.mime.indexOf('image/') > -1 ) {
							#>
							<img src="{{ url }}" alt="upload">
						<# } else if ( field.value.mime.indexOf('video/' ) > -1 ) { #>
							<video width="100%" height="" controls><source src="{{ url }}" type="{{ field.value.mime }}">Your browser does not support the video tag.</video>
						<# } else {
						var basename = url.replace(/^.*[\\\/]/, '');
						#>
							<a href="{{ url }}" class="attachment-file" target="_blank">{{ basename }}</a>
						<# }
					}
					#>
				</div>
				<button type="button" class="button tophive--add <# if ( url ) { #> tophive--hide <# } #>"><?php _e( 'Add', 'masterclass' ); ?></button>
				<button type="button" class="button tophive--change <# if ( ! url ) { #> tophive--hide <# } #>"><?php _e( 'Change', 'masterclass' ); ?></button>
				<button type="button" class="button tophive--remove <# if ( ! url ) { #> tophive--hide <# } #>"><?php _e( 'Remove', 'masterclass' ); ?></button>
			</div>
		</div>

		<?php
		self::after_field();
		echo '</script>';
	}
}

<script type="text/html" id="tmpl-tophive--cb-panel">
	<div class="tophive--cp-rows">

		<# if ( ! _.isUndefined( data.rows.top ) ) { #>
		<div class="tophive--row-top tophive--cb-row" data-id="{{ data.id }}_top">
			<a class="tophive--cb-row-settings" title="{{ data.rows.top }}" data-id="top" href="#"></a>
			<div class="tophive--row-inner">
				<div class="row--grid">
					<?php
					for ( $i = 1; $i <= 12; $i ++ ) {
						echo '<div></div>';
					}
					?>
				</div>
				<div class="tophive--cb-items grid-stack gridster" data-id="top"></div>
			</div>
		</div>
		<# } #>

		<# if ( ! _.isUndefined( data.rows.main ) ) { #>
		<div class="tophive--row-main tophive--cb-row" data-id="{{ data.id }}_main">
			<a class="tophive--cb-row-settings" title="{{ data.rows.main }}" data-id="main" href="#"></a>

			<div class="tophive--row-inner">
				<div class="row--grid">
					<?php
					for ( $i = 1; $i <= 12; $i ++ ) {
						echo '<div></div>';
					}
					?>
				</div>
				<div class="tophive--cb-items grid-stack gridster" data-id="main"></div>
			</div>
		</div>
		<# } #>


		<# if ( ! _.isUndefined( data.rows.bottom ) ) { #>
		<div class="tophive--row-bottom tophive--cb-row" data-id="{{ data.id }}_bottom">
			<a class="tophive--cb-row-settings" title="{{ data.rows.bottom }}" data-id="bottom" href="#"></a>
			<div class="tophive--row-inner">
				<div class="row--grid">
					<?php
					for ( $i = 1; $i <= 12; $i ++ ) {
						echo '<div></div>';
					}
					?>
				</div>
				<div class="tophive--cb-items grid-stack gridster" data-id="bottom"></div>
			</div>
		</div>
		<# } #>
	</div>


	<# if ( data.device != 'desktop' ) { #>
		<# if ( ! _.isUndefined( data.rows.sidebar ) ) { #>
		<div class="tophive--cp-sidebar">
			<div class="tophive--row-bottom tophive--cb-row" data-id="{{ data.id }}_sidebar">
				<a class="tophive--cb-row-settings" title="{{ data.rows.sidebar }}" data-id="sidebar" href="#"></a>
				<div class="tophive--row-inner">
					<div class="tophive--cb-items tophive--sidebar-items" data-id="sidebar"></div>
				</div>
			</div>
			<div>
		<# } #>
	<# } #>

</script>

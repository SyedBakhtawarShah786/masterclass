<?php
if ( ! function_exists( 'tophive_customizer_compatibility_config' ) ) {
	/**
	 * Add compatibility panel.
	 *
	 * @param array $configs List customize settings.
	 *
	 * @return array
	 */
	function tophive_customizer_compatibility_config( $configs ) {

		$panel  = 'compatibility';
		$config = array(
			// Layout panel.
			array(
				'name'     => $panel . '_panel',
				'type'     => 'panel',
				'priority' => 100,
				'title'    => esc_html__( 'Compatibility', 'masterclass' ),
			)
		);

		return array_merge( $configs, $config );
	}
}

add_filter( 'tophive/customizer/config', 'tophive_customizer_compatibility_config' );

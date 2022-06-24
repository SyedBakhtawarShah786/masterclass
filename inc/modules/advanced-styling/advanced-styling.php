<?php
TophiveCore()->register_module(
	'TophiveCore_Module_Advanced_Styling',
	array(
		'name' => __( 'Advanced Styling', 'masterclass' ),
		'desc' => __( 'Control the layout and typography setting for page header title, page header cover and more to come soon.', 'masterclass' ),
		'doc_link' => '',
		'enable_default' => false,
	)
);

class TophiveCore_Module_Advanced_Styling extends TophiveCoreModulesBasics {

	function __construct() {
		require_once dirname( __FILE__ ) . '/inc/page-header.php';
		require_once dirname( __FILE__ ) . '/inc/background.php';
		require_once dirname( __FILE__ ) . '/inc/footer-row.php';
	}
}

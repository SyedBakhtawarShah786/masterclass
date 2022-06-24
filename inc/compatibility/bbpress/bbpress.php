<?php 
/**
 * MasterClass Integration for BuddyBress, Buddypress-gammiperss, Buddypress-learnpress
 */
class Tophive_BBP
{
    static $_instance;

	static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	function is_active() {
		return top_hive()->is_bbpress_active();
	}
	function __construct(){
		if( $this->is_active() ){
			add_action( 'wp_enqueue_scripts', array($this, 'load_scripts') );
		}
	}
	function load_scripts(){
		wp_enqueue_style( 'th-bbpress', get_template_directory_uri() . '/assets/css/compatibility/bbpress.css', $deps = array(), $ver = false, $media = 'all' );
	}
}
function Tophive_BBP() {
	return Tophive_BBP::get_instance();
}

if ( top_hive()->is_bbpress_active() ) {
	Tophive_BBP();
}	
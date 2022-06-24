<?php

class Tophive_Dashboard {
	static $_instance;
	public $title;
	public $config;
	public $selected_item;
	public $current_tab = '';
	public $url         = '';
	public $welcome_head         = '';
	public $logo_url         = 'https://i.ibb.co/HgyYwts/masterclass.png';
	public $url_params         = 'admin.php?page=masterclass';

	static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance      = new self();
			self::$_instance->url = admin_url( 'admin.php' );
			self::$_instance->url = add_query_arg(
				array( 'page' => 'masterclass', 'tab' => 'dashboard' ),
				self::$_instance->url
			);
			self::$_instance->welcome_head = esc_html__( 'Welcome to MasterClass by Tophive', 'masterclass' );
			self::$_instance->title = esc_html__( 'MasterClass', 'masterclass' );

			if( !class_exists('WP_Importer') ){
				require_once( ABSPATH . 'wp-admin/includes/class-wp-importer.php' );				
			}
			require_once('importer/tophive-demo-import.php');
			require_once('importer/helpers.php');
			require_once('importer/import-actions.php');
			require_once('importer/logger.php');
			require_once('importer/wxri-importer.php');
			require_once('importer/importer.php');

			add_action( 'admin_menu', array( self::$_instance, 'add_menu_page' ), 5 );
			add_action( 'admin_enqueue_scripts', array( self::$_instance, 'scripts' ) );
			// 
			add_action( 'wp_ajax_process_required_plugins', array(self::$_instance, 'process_required_plugins') );
			add_action( 'wp_ajax_process_demo_import_html', array(self::$_instance, 'process_demo_import_html') );
			add_action( 'wp_ajax_process_plugin_install', array(self::$_instance, 'process_plugin_install') );
			add_action( 'wp_ajax_process_demo_import', array(TophiveDemoImport::get_instance(), 'import_demo_data_ajax_callback') );

			add_action( 'wp_ajax_tophive_import_customizer_data', array( TophiveDemoImport::get_instance(), 'tophive_import_customizer_data' ) );
			add_action( 'wp_ajax_ocdi_after_import_data', array( TophiveDemoImport::get_instance(), 'tophive_after_all_import_data_ajax_callback' ) );

			add_action( 'tophive/demo-import/files', array( self::$_instance, 'tophive_demo_import_files' ), 10, 1 );
			add_action( 'wp_ajax_demos_plugins_actions', array(self::$_instance, 'demos_plugins_actions') );
			add_action( 'wp_ajax_demos_plugin_status_check', array(self::$_instance, 'demos_plugin_status_check') );
			add_action( 'wp_ajax_demos_plugin_activate', array(self::$_instance, 'demos_plugin_activate') );
			add_action( 'wp_ajax_demos_plugin_install', array(self::$_instance, 'demos_plugin_install') );
			add_action( 'wp_ajax_demos_plugin_check_next', array(self::$_instance, 'demos_plugin_check_next') );
			add_action( 'wp_ajax_plugins_ok_footer', array(self::$_instance, 'plugins_ok_footer') );
			add_action( 'wp_ajax_plugins_done_footer', array(self::$_instance, 'plugins_done_footer') );
			add_action( 'wp_ajax_theme_activation', array(self::$_instance, 'theme_activation') );

			add_action( 'tophive/admin/tabs', array( self::$_instance, 'admin_tabs' ) );
			add_action( 'tophive/admin/content', array( self::$_instance, 'tophive_admin_content' ) );

			add_action( 'tophive/admin/demo-importer/plugins/html', array( self::$_instance, 'demo_importer_plugin_html' ), 10, 2 );
			add_action( 'tophive/admin/demo-importer/import/html', array( self::$_instance, 'demo_importer_import_html' ), 10, 2 );
		}
		return self::$_instance;
	}
	function demos_plugin_check_next(){
		$plugin = $_POST['plugin'];
		if( !empty($plugin) ){
			$status = self::get_plugin_status( $plugin );
			$text = self::get_plugin_next_action_html( $plugin );
			$response['status'] = $status;
			$response['text'] = $text;
			$response['plugin'] = $plugin;
		}else{
			$response['status'] = 'invalid';
		}
		wp_send_json( $response , 200 );
	}
	function demos_plugin_install(){
		$response = [];

		$slug = isset($_POST['plugin']) ? $_POST['plugin'] : '';

		if( !empty($slug) && '' !== $slug ){
			$info = self::tophive_get_plugin_info( $slug );
			$plugin_url = $info['download_link'];
			$installed = self::tophive_install_plugin( $slug, $plugin_url );
			if( $installed ){
				$response['status'] = esc_html__( 'installed', 'masterclass' );
				$response['text'] = esc_html__( 'Activating...', 'masterclass' );
				$response['plugin'] = $slug;
			}else{
				$response['status'] = $installed;
			}
		}else{
			$response['status'] = esc_html__( 'invalid', 'masterclass' );
		}

		wp_send_json( $response, 200 );
	}
	function demos_plugin_activate(){
		$response = [];

		$trd = get_transient( 'tophive_installing_demo_plugins' );
		$plugins = $trd['plugins'];

		$action_plugins = [];

		$plugin = isset($_POST['plugin']) ? $_POST['plugin'] : '';
		if( '' !== $plugin ){
			$activated = self::tophive_activate_plugin($plugin);
			if( null == $activated ){
				$response['status'] = esc_html__( 'active', 'masterclass' );
				$response['text'] = esc_html__( 'Active', 'masterclass' );
				$response['plugin'] = $plugin;
			}else{
				$response['status'] = esc_html__( 'Activation failed', 'masterclass' );
			}
		}else{
			$response['status'] = esc_html__('invalid', 'masterclass');
		}
		foreach ($plugins as $slug) {
			$status = self::get_plugin_status( $slug );
			if( 'active' !== $status ){
				array_push($action_plugins, $slug);
			}
		}
		$trd['plugins'] = $action_plugins;
		set_transient( 'tophive_installing_demo_plugins', $trd, 1200 );
		$trd = get_transient( 'tophive_installing_demo_plugins' );
		if( !empty($trd['plugins']) ){
			$plugin = $trd['plugins'][0];
			$response['next_plugin'] = $plugin;
		}else{
			$response['next_plugin'] = 'done';
		}

		wp_send_json( $response, 200 );
	}
	function demos_plugin_status_check(){
		$transient_data = [];

		$demo_slug = isset($_POST['slug']) ? $_POST['slug'] : '';
		$demo_selected = isset($_POST['selected']) ? $_POST['selected'] : 0;
		$plugins = isset($_POST['plugins']) ? $_POST['plugins'] : '';

		$action_plugins = [];
		foreach ($plugins as $slug) {
			$status = self::get_plugin_status( $slug );
			if( 'active' !== $status ){
				array_push($action_plugins, $slug);
			}
		}

		$transient_data['demo-slug'] = $demo_slug;
		$transient_data['demo-selected'] = $demo_selected;
		$transient_data['plugins'] = $action_plugins;

		set_transient( 'tophive_installing_demo_plugins', $transient_data, 2000 );

		$trd = get_transient( 'tophive_installing_demo_plugins' );

		$plugin = $trd['plugins'][0];

		$response = [];
		if( !empty($plugin) ){
			$status = self::get_plugin_status( $plugin );
			$text = self::get_plugin_next_action_html( $plugin );
			$response['status'] = $status;
			$response['text'] = $text;
			$response['plugin'] = $plugin;
		}else{
			$response['status'] = 'invalid';
		}
		wp_send_json( $response , 200 );
	}
	function theme_activation(){
		$key = $_POST['key'];
		
		set_theme_mod( 'mc_activation_key', $key );

		$verify = self::verify_purchase_code($key);
		if( $verify == 'activated'){
			$response = 'activated';
		}else{
			$response = '<p class="tophive-error">'. esc_html__( 'Activation failed, please try again!', 'masterclass' ) .'<p>';
		}

		wp_send_json( $response, 200 );
	}
	function verify_purchase_code( $key ){
		$request = wp_remote_get( 'https://api.tophivetheme.com/themes/data.php?type=activation&key=' . $key );
		$body = wp_remote_retrieve_body( $request );
		return $body;
	}
	function is_theme_activated(){
		$key = get_theme_mod( 'mc_activation_key' );
		if( isset($key) && !empty($key) ){
			$verify = self::verify_purchase_code( get_theme_mod( 'mc_activation_key' ) );
			if( $verify == 'activated' ){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function process_plugin_install(){
		$plugins = $_POST['plugins'];

		$result = [];
		foreach( $plugins as $slug  ){
			$plugin_info = self::tophive_get_plugin_info($slug);
			$result[] = self::demo_plugins_action_creators( $slug, $plugin_info['download_link'] );
		}
		wp_send_json( $result, 200 );
	}
	function demos_plugins_actions(){
		$item = $_POST['slug'];
		$id = $_POST['selected'];
		$get_plugins = self::get_required_demo_plugins( $item );
		$result = [];
		if( !empty($get_plugins) ){
			if( self::tophive_get_inactive_demo_plugins_count($item) == 0 ){
				$result = esc_html__( 'All Plugins are installed', 'masterclass' );
			}else{
				foreach ($get_plugins as $plugin) {
					$plugin_info = self::tophive_get_plugin_info($plugin);
					$result[] = self::demo_plugins_action_creators( $plugin, $plugin_info['download_link'] );
				}
			}
		}
		$response = apply_filters( 'tophive/admin/demo-importer/plugins/html', $item, $id );

		echo apply_filters( 'tophivetophive/admin/demo-importer/plugins/after', $response);
		die();
	}
	function tophive_get_inactive_demo_plugins_count( $slug ){
		$get_plugins = self::get_required_demo_plugins( $slug );
		$inactive_plugins = [];
		if( !empty($get_plugins) ){
			foreach ($get_plugins as $plugin) {
				if( !$this->check_if_plugin_active( self::tophive_get_plugin_path_by_slug( $plugin ) ) ){
					array_push($inactive_plugins, $plugin);
				}
			}
		}
		return count($inactive_plugins);
	}
	function tophive_get_inactive_demo_plugins( $slug ){
		$get_plugins = self::get_required_demo_plugins( $slug );
		$inactive_plugins = [];
		if( !empty($get_plugins) ){
			foreach ($get_plugins as $plugin) {
				if( !$this->check_if_plugin_active( self::tophive_get_plugin_path_by_slug( $plugin ) ) ){
					$inactive_plugins[$plugin] = $plugin->url;
				}
			}
		}
		return $inactive_plugins;
	}
	function demo_plugins_action_creators( $slug, $plugin_url ){
		$get_status = self::get_plugin_status( $slug );
		
		if( $get_status == 'installed' ){
			return self::tophive_activate_plugin( $slug );
		}
		elseif( $get_status == 'uninstalled' ){
			self::tophive_install_plugin( $slug, $plugin_url );
			return self::tophive_activate_plugin( $slug );
		}
		elseif( $get_status == 'active' ){
			return true;
		}
	}
	function tophive_install_plugin($slug, $plugin_url){
		$path = $plugin_dir = ABSPATH . 'wp-content/plugins/';
		$downloader = new Downloader();
		$zip_link = $downloader->download_file( $plugin_url, $slug . '.zip' );

		$unzip =  unzip_file( $zip_link, $path );
		return $unzip;
	}
	function tophive_activate_plugin( $slug ){
		$path = self::tophive_get_plugin_path_by_slug( $slug );
		$activate = activate_plugin($path);
		return $activate;
	}
	function process_demo_import(){
		$slug = $_POST['slug'];

		$request = wp_remote_get( 'https://api.tophivetheme.com/themes/data.php?type=imports' );
		$body = (array)json_decode(wp_remote_retrieve_body( $request ));
		
		$url = $body[$slug]->download;

		$desc = esc_attr('file-description');

		$file_attachment_id = $this->tophive_sideload_file($url, 0, $desc);
		$file = get_attached_file( $file_attachment_id );

		if( class_exists('WP_Import') ){
			$import = new WP_Import();			
			$html = $import->import( $file );
		}else{
			$html = false;
		}
		wp_send_json( $html );
	}
	function process_required_plugins(){
		$item = $_POST['item'];
		$id = $_POST['selected'];
		
		$html = apply_filters( 'tophive/admin/demo-importer/plugins/html', $item, $id );
		wp_send_json( $html );
	}
	function process_demo_import_html(){
		$item = $_POST['item'];
		$id = $_POST['selected'];
		
		$html = apply_filters( 'tophive/admin/demo-importer/import/html', $item, $id );
		wp_send_json( $html );	
	}
	function demo_importer_import_html( $slug, $id ){
		$html = '<div class="import-inner-content text-center">
			<svg class="mb-40" width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-download" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			  <path fill-rule="evenodd" d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
			  <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
			</svg>
			<h3>'. esc_html__( 'Start importing', 'masterclass' ) .'</h3>
			<p>'. esc_html__( 'This will take a few minutes to complete.Please don not close this window while installing. For avoiding copyright issues we will not import demo pictures', 'masterclass' ) .'</p>
		</div>
		<div class="import-inner-footer">
			<a href="" data-id="'. $id .'" data-slug="'. $slug .'" class="tophive-admin-small-button start-demo-import">'. esc_html__( 'Start Importing', 'masterclass' ) .'</a>
			<a href="" class="tophive-admin-small-button ghost-button end-import-popup">'. esc_html__( 'Cancel', 'masterclass' ) .'</a>
			<div class="tophive-progress tophive-progress-striped active display-none w-80">
		      <div role="tophive-progressbar tophive-progress-striped" class="tophive-progress-bar"><span></span></div>
		    </div>
		</div>';
		return $html;
	}
	function plugins_ok_footer(){
		$trd = get_transient( 'tophive_installing_demo_plugins' );
		$id = $trd['demo-selected'];
		$slug = $trd['demo-slug'];
		$response = [];
		$response['status'] = esc_html__( 'next', 'masterclass' );
		$response['html'] = '<a data-id="'. $id .'" data-slug="'. $slug .'" href="" class="tophive-admin-small-button button-success plugins-ok"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			  <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
			</svg>'. esc_html__( 'Proceed Next', 'masterclass' ) .'</a>

		<a href="" class="tophive-admin-small-button ghost-button end-import-popup">'. esc_html__( 'Return to demos', 'masterclass' ) .'</a>';
		delete_transient( 'tophive_installing_demo_plugins' );
		wp_send_json( $response, 200 );
	}
	function plugins_done_footer(){
		$trd = get_transient( 'tophive_installing_demo_plugins' );
		$response = [];
		$response['status'] = esc_html__( 'next', 'masterclass' );
		$response['html'] = '
			<svg class="tophive-active" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
			  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"></path>
			</svg>
			'. esc_html__( ' All Plugins has been installed and activated', 'masterclass' );
		delete_transient( 'tophive_installing_demo_plugins' );
		wp_send_json( $response, 200 );
	}
	function demo_importer_plugin_html( $slug, $id ){
		$html = '';
		$get_plugins = self::get_required_demo_plugins( $slug );
			$html .= '<div class="import-inner-content">
				<h3>'. esc_html__( 'Required Plugin', 'masterclass' ) .'</h3>
				<p>'. esc_html__( 'This plugins are required to install this demo', 'masterclass' ).'</p>
				<div class="demo-import-dynamic-content-wrapper">
					'. self::get_plugins_list_html( $get_plugins ) .'
				</div>
			</div>';
			if( self::tophive_get_inactive_demo_plugins_count( $slug ) == 0 ){
			$html .= '<div class="import-inner-footer">
				<a data-id="'. $id .'" data-slug="'. $slug .'" href="" class="tophive-admin-small-button button-success plugins-ok"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					  <path fill-rule="evenodd" d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
					</svg>'. esc_html__( 'Proceed Next', 'masterclass' ) .'</a>

				<a href="" class="tophive-admin-small-button ghost-button end-import-popup">'. esc_html__( 'Return to demos', 'masterclass' ) .'</a>
				</div>';
			}else{
				$html .= '<div class="import-inner-footer">
					<a data-id="'. $id .'" href="" data-slug="'. $slug .'" class="tophive-admin-small-button tophive-demo-plugin-install">'. esc_html__( 'Install Plugins', 'masterclass' ) .'</a>

					<a href="" class="tophive-admin-small-button ghost-button end-import-popup">'. esc_html__( 'Return to demos', 'masterclass' ) .'</a>
					</div>';
			}
		return $html;
	}
	function tophive_get_plugin_info( $slug ){
		$url = 'http://api.wordpress.org/plugins/info/1.0/'. $slug .'.json';
		$request = wp_remote_get( $url );
		$body = (array)json_decode(wp_remote_retrieve_body( $request ));	

		if( !array_key_exists('error', $body) ){
			$res = [];
			$res['name'] = $body['name'];
			$res['version'] = $body['version'];
			$res['description'] = wp_trim_words( wp_strip_all_tags($body['sections']->description), 40);
			$res['thumb_url'] = 'https://ps.w.org/' . $slug . '/assets/icon-128x128.png';
			$res['download_link'] = $body['download_link'];
			$res['status'] = self::get_plugin_status($slug);
		}else{
			$url = 'https://api.tophivetheme.com/themes/data.php?type=plugin_info&slug=' . $slug;
			$request = wp_remote_get( $url );
			$res = (array)json_decode(wp_remote_retrieve_body( $request ));
			$res['status'] = self::get_plugin_status($slug);
		}
		return $res;
	}
	function get_required_demo_plugins( $item_id ){
		$request = wp_remote_get( 'https://api.tophivetheme.com/themes/data.php?type=imports' );
		$body = (array)json_decode(wp_remote_retrieve_body( $request ));
		return $body[$item_id]->plugins;
	}
	function get_plugins_list_html( $plugins ){
		$html = '<ul class="popup-plugins-list">';
		foreach ($plugins as $plugin) {
			$plugin_info = self::tophive_get_plugin_info( $plugin );
			$html .= '<li data-slug="'. $plugin .'" class="'. $plugin .'">';
				$html .= '<span>'. $plugin_info['name'] .'</span>';
				$html .= self::get_plugin_status_html($plugin);
			$html .= '</li>';
		}
		return $html .= '</ul>';
	}
	function get_plugin_status_html( $slug ){
		$status = self::get_plugin_status( $slug );
		if( $status == 'active' ){
			$html = '<span class="success">'. esc_html__( 'Active', 'masterclass' ) .'</span>';
		}elseif( $status == 'installed' ){
			$html = '<span class="warning">'. esc_html__( 'Waiting Activation', 'masterclass' ) .'</span>';
		}else{
			$html = '<span>'. esc_html__( 'Installation Required', 'masterclass' ) .'</span>';
		}
		return $html;
	}
	function get_plugin_next_action_html( $slug ){
		$status = self::get_plugin_status( $slug );
		if( $status == 'active' ){
			$html = esc_html__( 'Active', 'masterclass' );
		}elseif( $status == 'installed' ){
			$html = esc_html__( 'Activating...', 'masterclass' );
		}else{
			$html = esc_html__( 'Installing...', 'masterclass' );
		}
		return $html;
	}
	function tophive_get_plugin_path_by_slug( $slug ){
		$installed_plugins = get_plugins();
		$installed_plugins_root = [];

		foreach ($installed_plugins as $key => $value) {
			$installed_plugins_root[$key] = explode('/', $key)[0];
		}
		return array_search($slug, $installed_plugins_root);
	}
	function get_plugin_status( $slug ){

		$active_plugins = get_option('active_plugins');
		$active_plugins_root = [];

		$installed_plugins = get_plugins();
		$installed_plugins_root = [];

		foreach ($active_plugins as $key => $value) {
			$active_plugins_root[] = explode('/', $value)[0];
		}
		foreach ($installed_plugins as $key => $value) {
			$installed_plugins_root[] = explode('/', $key)[0];
		}

		$check = $slug;
		
		$status = '';

		$path = self::tophive_get_plugin_path_by_slug($slug);

		if( in_array($check, $installed_plugins_root) ){
			if( $this->check_if_plugin_active( $path ) ){
				$status = 'active';
			}else{
				$status = 'installed';
			}
		}else{
			$status = 'uninstalled';
		}
		return $status;
	}
	function check_if_plugin_active( $plugin ){
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || is_plugin_active_for_network( $plugin );
	}
	public function admin_links(){
		return array(
			array(
				'name' => esc_html__( 'Dashboard', 'masterclass' ),
				'url' => $this->url_params . '&tab=dashboard',
				'slug' => 'dashboard',
				'target' => '_self', 
			),
			array(
				'name' => esc_html__( 'Activation', 'masterclass' ),
				'url' => $this->url_params . '&tab=activation',
				'slug' => 'activation',
				'target' => '_self' 
			),
			array(
				'name' => esc_html__( 'Import Demo', 'masterclass' ),
				'url' => $this->url_params . '&tab=importer', 
				'slug' => 'importer', 
				'target' => '_self' 
			),
			array(
				'name' => esc_html__( 'Customization', 'masterclass' ),
				'url' => esc_url( admin_url('customize.php')), 
				'target' => '_blank',
				'slug' => '', 
			),
			array(
				'name' => esc_html__( 'Plugins', 'masterclass' ),
				'url' => $this->url_params . '&tab=plugins', 
				'slug' => 'plugins', 
				'target' => '_self' 
			),
			array(
				'name' => esc_html__( 'System Status', 'masterclass' ),
				'url' => esc_url( admin_url('site-health.php')), 
				'slug' => 'status', 
				'target' => '_blank' 
			),
			array(
				'name' => esc_html__( 'Update', 'masterclass' ),
				'url' => $this->url_params . '&tab=update', 
				'slug' => 'update', 
				'target' => '_self' 
			),
		);
	} 
	function add_url_args( $args = array() ) {
		return add_query_arg( $args, self::$_instance->url );
	}

	function add_menu_page() {
		$icon = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMCIgd2lkdGg9IjQwLjAwMDAwMHB0IiBoZWlnaHQ9IjQwLjAwMDAwMHB0IiB2aWV3Qm94PSIwIDAgNDAuMDAwMDAwIDQwLjAwMDAwMCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCI+CgkJCQkJPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsNDAuMDAwMDAwKSBzY2FsZSgwLjEwMDAwMCwtMC4xMDAwMDApIiBmaWxsPSIjMDAwMDAwIiBzdHJva2U9Im5vbmUiPgoJCQkJCTxwYXRoIGQ9Ik0xNTEgMzg4IGMtNjEgLTEzIC0xMjUgLTc5IC0xMzkgLTE0MiAtMTUgLTcyIDIgLTEzMCA1MyAtMTgxIDEzNyAtMTM3IDM2NCAtMTIgMzIzIDE3OCAtMjIgMTA0IC0xMjcgMTY4IC0yMzcgMTQ1eiBtMzQgLTEyOSBsMTkgLTQxIDE5IDQyIGMxOCAzNyAyMyA0MSA1MSAzOCAzMCAtMyAzMSAtNSA0MiAtNjggNyAtMzYgMTIgLTgwIDEzIC05OCAxIC0zMiAtMSAtMzMgLTMxIC0zMCAtMzEgMyAtMzMgNiAtMzggNDYgbC01IDQyIC0xOCAtNDUgYy0xMSAtMjUgLTI1IC00NSAtMzMgLTQ1IC04IDAgLTIzIDE5IC0zMyA0MyBsLTE4IDQyIC03IC00MiBjLTcgLTM5IC0xMCAtNDMgLTM3IC00MyAtMjcgMCAtMjkgMyAtMjkgMzYgMCAyMCA1IDY1IDEyIDEwMCAxMSA2MyAxMiA2NCA0MyA2NCAyOCAwIDM0IC01IDUwIC00MXoiLz4KCQkJCQk8L2c+CgkJCQkJPC9zdmc+';
		add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			'masterclass',
			array( $this, 'page' ),
			'data:image/svg+xml;base64,' . $icon,
			2
		);
	}

	function scripts( $id ) {
		$suffix = top_hive()->get_asset_suffix();
		wp_enqueue_style( 'tophive-admin', esc_url( get_template_directory_uri() ) . '/assets/css/admin/dashboard' . $suffix . '.css', false, Tophive::$version );

		wp_enqueue_script('tophive-admin-js', esc_url( get_template_directory_uri() ) . '/assets/js/admin/dashboard' . $suffix . '.js', array('jquery'), Tophive::$version, false);
		wp_localize_script( 'tophive-admin-js', 'TophiveAdminAjax', 
			array(
				'_nonce'          => wp_create_nonce( 'tophive_pro_module' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'tophive_mc_ajax_verification' ),
				'activating' => esc_html__( 'Activating...', 'masterclass' ),
				'activation_text' => esc_html__( 'Activate Masterclass', 'masterclass' ),
				'regenerate_done' => __( 'Regenerate assets completed', 'masterclass' ),
				'regenerate_url'  => add_query_arg(
					array(
						'regenerate_assets' => 1,
						'regenerate_nonce'  => wp_create_nonce( 'regenerate_nonce' ),
					),
					home_url( '/' )
				),
			)
		);
		if ( 'themes' != $id ) {
			wp_enqueue_style( 'plugin-install' );
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
			add_thickbox();
		}
	}

	function setup() {
		$theme        = wp_get_theme();
		if ( is_child_theme() ) {
			$theme = $theme->parent();
		}
		$this->config = array(
			'name'       => $theme->get( 'Name' ),
			'theme_uri'  => $theme->get( 'ThemeURI' ),
			'desc'       => $theme->get( 'Description' ),
			'author'     => $theme->get( 'Author' ),
			'author_uri' => $theme->get( 'AuthorURI' ),
			'version'    => $theme->get( 'Version' ),
		);

		$this->current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : ''; // phpcs:ignore
	}

	function page() {
		$this->setup();
		$this->page_header();
		echo '<div class="tophive-admin-main-content">';
			$cb = apply_filters( 'tophive/dashboard/content_cb', false );
			if ( ! is_callable( $cb ) ) {
				$cb = array( $this, 'page_inner' );
			}

			if ( is_callable( $cb ) ) {
				call_user_func_array( $cb, array( $this ) );
			}

		echo '</div>';
	}

	public function page_header() {
		?>
		<div class="tophive-admin-header">
			<div class="tophive-admin-header">
				<div class="tophive-admin-header-content">
					<a href="https://themeforest.net/user/tophive" target="_blank" class="tophive-branding">
						<img src="<?php echo esc_url( $this->logo_url ) ?>" alt="<?php esc_attr_e( 'logo', 'masterclass' ); ?>">
					</a>
					<h2><?php echo esc_attr($this->welcome_head); ?></h2>
					<span class="tophive-version"><?php esc_html_e( 'Version: ', 'masterclass' )?><?php echo esc_html( $this->config['version'] ); ?></span>
					
				</div>
			</div>
		</div>
		<?php
	}


	private function page_inner() {
		?>
		<div id="plugin-filter" class="cd-row metabox-holder">
			<hr class="wp-header-end">
			<?php

				do_action( 'tophive/admin/tabs' );
				do_action( 'tophive/admin/content' );

			?>
		</div>
		<?php
	}
	public function tophive_admin_content(){
		$tab = isset($_GET['tab']) ?  $_GET['tab'] : '';
		?>
			<div class="tophive-admin-content-wrapper-main">
				<?php 
					switch ($tab) {
						case 'activation':
								$this->tophive_activation_panel();
							break;
						case 'importer':
								$this->tophive_import_panel();
							break;
						case 'plugins':
								$this->tophive_plugins_panel();
							break;
						case 'tutorials':
								$this->tophive_tutorials_panel();
							break;
						case 'update':
								$this->tophive_update_panel();
							break;
						case 'dashboard':
								$this->tophive_dashboard_panel();
							break;
						case '':
								$this->tophive_dashboard_panel();
							break;
						default:
								$this->tophive_dashboard_panel();
							break;
					}
				?>
			</div>
		<?php
	}
	function tophive_update_panel(){
		?>
			<div class="tophive-admin-content-row">
				<div class="col-12">
					<div class="tophive-admin-section text-center">
						<?php 
							if( self::is_theme_activated() ){
								do_action( 'tophive_core_dynamic_update' );
							}else{
								?>
									<h3 class="tophive-section-heading"><?php esc_html_e( 'Activate Your Theme To Get Updates', 'masterclass' ); ?></h3>
									<a href="<?php echo esc_url( $this->url_params . '&tab=activation' ); ?>" class="tophive-admin-big-button"><?php esc_html_e( 'Activate MasterClass', 'masterclass' ); ?></a>
								<?php
							}
						?>
						
					</div>
				</div>
			</div>
		<?php
	}
	function tophive_activation_panel(){
		$key = get_theme_mod( 'mc_activation_key' );
		?>
			<div class="tophive-admin-content-row">
				<div class="col-12">
					<div class="tophive-admin-section text-center">
						<?php 
							if( self::is_theme_activated() ){
								?>
									<h1></h1>
									<svg width="8em" height="8em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="#4cd137" xmlns="http://www.w3.org/2000/svg">
										  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
									<h3 class="tophive-section-heading"><?php esc_html_e( 'Cheers!!Your theme is activated.', 'masterclass' ) ?></h3>
									<p class="tophive-section-sub-heading"><?php echo esc_attr($key); ?></p>	
								<?php
							}else{
								?>
									<h3 class="tophive-section-heading"><?php esc_html_e( 'Activate Your Theme To Get Started', 'masterclass' ); ?></h3>
									<div class="text-center">
										<input type="text" name="tophive-theme-purchase-key" placeholder="Your Theme Purchase Code" class="tophive-input-big" />
									</div>
									<a href="" class="tophive-admin-big-button tophive-activate-theme"><?php esc_html_e( 'Activate MasterClass', 'masterclass' ); ?></a>
									<span class="tophive-messages"></span>
									<div class="tophive-activation-success-message text-center">
										<svg width="10em" height="10em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="#4cd137" xmlns="http://www.w3.org/2000/svg">
										  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
										</svg>
										<h3 class="tophive-section-heading"><?php esc_html_e( 'Hoorayyy ! You Have Successfully Activated MasterClass', 'masterclass' ); ?></h3>
									</div>
								<?php
							}
						?>
						
					</div>
				</div>
			</div>
		<?php
	}
	function tophive_plugins_panel(){
		$request = wp_remote_get( 'https://api.tophivetheme.com/themes/data.php?type=plugins' );
		$plugins = (array)json_decode(wp_remote_retrieve_body( $request ));
		$all_plugins = [];
		foreach ($plugins as $value){
			foreach ($value->plugins as $slug) {
				array_push($all_plugins, $slug);
			}
		}
		$all_plugins = array_unique($all_plugins);
		?>
			<div class="tophive-admin-plugin-download">
				<div class="tophive-admin-content-row">
					<div class="col-12">
						<div class="tophive-admin-section">
							<h2 class="tophive-section-heading"><?php esc_html_e( 'Recommended plugins', 'masterclass' ); ?></h2>
							<p class="tophive-section-sub-heading mb-0"><?php esc_html_e( 'Here We have recommended to install some plugins. Here some plugins are must with essential tab.You can install and activate them here and deactivate or delete from wordpress plugin page', 'masterclass' ); ?></p>

						<div class="tophive-plugin-installation">
							<div class="tophive-admin-tabs-two">  <!-- begins the tabbed panels / wrapper-->
								<ul class="tabs">
									<li><a href="#panel1"><?php esc_html_e( 'All plugins', 'masterclass' ) ?></a></li>
									<?php 
										$i = 2;
										foreach ($plugins as $value) {
											echo '<li><a href="#panel'. $i .'">'. $value->section .'</a></li>';
											$i++;
										}
									?>
								</ul>

								<div class="tophive-admin-tabs-two-container">
									<div id="panel1" class="panel">
										<table class="tophive-admin-table">
											<thead>
												<tr>
													<th></th>
													<th></th>
													<th class="name"><?php esc_html_e( 'Name', 'masterclass' ); ?></th>
													<th><?php esc_html_e( 'Description', 'masterclass' ); ?></th>
													<th>
														<?php esc_html_e( 'Status', 'masterclass' ); ?>
													</th>
													<th>
														<?php esc_html_e( 'Version', 'masterclass' ); ?>
													</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													foreach ($all_plugins as $slug) {
														$plugin_info = self::tophive_get_plugin_info( $slug );
														$is_checkbox = $plugin_info['status'] == 'active' ? '<svg class="tophive-active" width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
														  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
														</svg>' : '<input name="tophive_selected_plugins" value="'. $slug .'" type="checkbox" />';
														echo '<tr class="'. $slug .'">';
														echo '<td class="action">'. $is_checkbox .'</td>';
														echo '<td class="table-thumb"><img src="'. $plugin_info['thumb_url'] .'" /></td>';
														echo '<th class="table-name">' . $plugin_info['name'] . '</th>';
														echo '<td>' . $plugin_info['description'] . '</td>';
														echo '<td class="plugin-status tophive-'. $plugin_info['status'] .'">' . ucfirst($plugin_info['status']) . '</td>';
														echo '<td>' . $plugin_info['version'] . '</td>';
														echo '</tr>';
													}
												?>
											</tbody>
										</table>
									</div>
									<?php 
										$i = 2;
										foreach ($plugins as $value){
											?>
												<div id="panel<?php echo esc_attr($i) ?>" class="panel">
													<table class="tophive-admin-table">
														<thead>
															<tr>
																<th></th>
																<th></th>
																<th class="name"><?php esc_html_e( 'Name', 'masterclass' ); ?></th>
																<th><?php esc_html_e( 'Description', 'masterclass' ); ?></th>
																<th>
																	<?php esc_html_e( 'Status', 'masterclass' ); ?>
																</th>
																<th>
																	<?php esc_html_e( 'Version', 'masterclass' ); ?>
																</th>
															</tr>
														</thead>
														<tbody>
															<?php 
																foreach ($value->plugins as $slug) {
																	$plugin_info = self::tophive_get_plugin_info( $slug );
																	$is_checkbox = $plugin_info['status'] == 'active' ? '<svg class="tophive-active" width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-check-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
																		  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
																		</svg>' : '<input name="tophive_selected_plugins" value="'. $slug .'" type="checkbox" />';
																	echo '<tr>';
																	echo '<td class="action">'. $is_checkbox .'</td>';
																	echo '<td class="table-thumb"><img src="'. $plugin_info['thumb_url'] .'" /></td>';
																	echo '<th class="table-name">' . $plugin_info['name'] . '</th>';
																	echo '<td>' . $plugin_info['description'] . '</td>';
																	echo '<td class="tophive-'. $plugin_info['status'] .'">' . ucfirst($plugin_info['status']) . '</td>';
																	echo '<td>' . $plugin_info['version'] . '</td>';
																	echo '</tr>';
																}
															?>
														</tbody>
													</table>    
												</div>
											<?php
											$i++;
										}
									?>
								</div>
							</div>
							<div class="tophive-plugins-action tophive-sticky-bottom deactive">
								<a href="" class="tophive-admin-small-button tophive-plugins-import"><?php esc_html_e( 'Install Plugins..', 'masterclass' ); ?></a>
							</div>
						</div>
						</div>
					</div>
				</div>
			</div>
		<?php
	}
	function tophive_sideload_file( $file, $post_id = 0, $desc = null ) {
		if( empty( $file ) ) {
			return new \WP_Error( 'error', 'File is empty' );
		}

		$file_array = array();

		// Get filename and store it into $file_array
		// Add more file types if necessary
		preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png|xml)\b/i', $file, $matches );
		$file_array['name'] = basename( $matches[0] );

		// Download file into temp location.
		$file_array['tmp_name'] = download_url( $file );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return new \WP_Error( 'error', 'Error while storing file temporarily' );
		}

		// Store and validate
		$id = media_handle_sideload( $file_array, $post_id, $desc );

		// Unlink if couldn't store permanently
		if ( is_wp_error( $id ) ) {
			unlink( $file_array['tmp_name'] );
			return new \WP_Error( 'error', "Couldn't store upload permanently" );
		}

		if ( empty( $id ) ) {
			return new \WP_Error( 'error', "Upload ID is empty" );
		}

		return $id;
	}
	public function tophive_demo_import_files(){
		$request = wp_remote_get( 'https://api.tophivetheme.com/themes/data.php?type=imports' );
		$body = (array)json_decode(wp_remote_retrieve_body( $request ));
		
		$demoImporter = [];
		foreach ($body as $value) {
			$newval = (array)$value;
			$newval['import_file_name'] = $newval['name'];
			unset( $newval['name'], $newval['slug'], $newval['thumb'], $newval['plugins'] );
			$demoImporter[] = $newval;
		}
		return $demoImporter;
	}
	public  function tophive_import_panel(){
		$request = wp_remote_get( 'https://api.tophivetheme.com/themes/data.php?type=imports' );
		$body = (array)json_decode(wp_remote_retrieve_body( $request ));

		?>
			<div class="tophive-demo-importer-wrapper">
				<div class="tophive-demo-importer-popup">
					<div class="tophive-demo-importer-popup-thumb">

					</div>
					<div class="tophive-demo-importer-popup-content">
						
					</div>
				</div>
			</div>
			<div class="tophive-admin-content-row">
				<?php 
					$i = 0;
					foreach ($body as $demo) {
						?>
							<div class="col-4">
								<div class="tophive-admin-section tophive-demo-import-section">
									<div class="tophive-demo-thumb">
										<img src="<?php echo esc_attr( $demo->thumb ); ?>">
									</div>
									<div class="tophive-demo-footer">
										<div>
											<p class="tophive-demo-import-title"><?php echo esc_attr( $demo->name ); ?></p>
										</div>
										<div class="button-group">										
											<a data-id="<?php echo esc_attr($i); ?>" data-thumb="<?php echo esc_attr( $demo->thumb ) ?>" data-slug="<?php echo esc_attr($demo->slug) ?>" href="" class="tophive-admin-small-button start-import-popup btn-svg">
												<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-download" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												  <path fill-rule="evenodd" d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
												  <path fill-rule="evenodd" d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
												</svg>
											</a>
											<a target="_blank" href="<?php echo esc_attr( $demo->preview_url ); ?>" class="tophive-admin-small-button ghost-button">
												<svg width="1.2em" height="1.2em" viewBox="0 0 16 16" class="bi bi-link" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											  	<path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
											  	<path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"/>
												</svg>
											</a>
										</div>
									</div>	
								</div>
							</div>
						<?php
						$i++;
					}
				?>	
			</div>
		<?php
	}
	public function tophive_dashboard_panel(){
			if( !self::is_theme_activated() ){
				?>
				<div class="tophive-admin-content-row">
					<div class="col-12">
						<div class="tophive-admin-section text-center">
							<h3 class="tophive-section-heading"><?php esc_html_e( 'Activate Your Theme To Get Started', 'masterclass' ); ?></h3>
							<a href="<?php echo esc_url( $this->url_params . '&tab=activation' ); ?>" class="tophive-admin-big-button"><?php esc_html_e( 'Activate MasterClass', 'masterclass' ); ?></a>
						</div>
					</div>
				</div>
				<?php
			}
		?>
			
			<div class="tophive-admin-content-row">
				<div class="col-6">
					<div class="tophive-admin-section section-smart-bg">
						<h3 class="tophive-section-heading-small"><?php esc_html_e( 'Import Demo Sites', 'masterclass' ); ?></h3>
						<p class="tophive-section-sub-heading mb-40"><?php esc_html_e( 'import from premade demos', 'masterclass' ); ?></p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=masterclass&tab=importer' ) ) ?>" class="tophive-admin-small-button"><?php esc_html_e( 'Start import', 'masterclass' ); ?></a>
						<img class="moto-image" src="https://i.ibb.co/DQbCqB5/Build.png">
					</div>
				</div>
				<div class="col-6">
					<div class="tophive-admin-section">
						<h3 class="tophive-section-heading-small"><?php esc_html_e( 'Customize Masterclass', 'masterclass' ); ?></h3>
						<p class="tophive-section-sub-heading mb-40"><?php esc_html_e( 'huge customization options', 'masterclass' ); ?></p>
						<a target="_blank" href="<?php echo esc_url( admin_url( 'customize.php' ) ) ?>" class="tophive-admin-small-button"><?php esc_html_e( 'Start customizing', 'masterclass' ); ?></a>
						<img class="moto-image" src="https://i.ibb.co/Xp2M1xm/Service.png">
					</div>
				</div>	
			</div>
			<div class="tophive-admin-content-row">
				<div class="col-6">
					<div class="tophive-admin-section">
						<h3 class="tophive-section-heading-small"><?php esc_html_e( 'Find a support', 'masterclass' ); ?></h3>
						<p class="tophive-section-sub-heading mb-40"><?php esc_html_e( 'support from professionals', 'masterclass' ); ?></p>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=masterclass&tab=importer' ) ) ?>" class="tophive-admin-small-button"><?php esc_html_e( 'Submit a ticket', 'masterclass' ); ?></a>
						<img class="moto-image" src="https://i.ibb.co/sgnb1Jb/Support.png">
					</div>
				</div>
				<div class="col-6">
					<div class="tophive-admin-section bg-green">
						<h3 class="tophive-section-heading-small white"><?php esc_html_e( 'Read Documentation', 'masterclass' ); ?></h3>
						<p class="tophive-section-sub-heading mb-40 white"><?php esc_html_e( 'Documentation with FAQs', 'masterclass' ); ?></p>
						<a target="_blank" href="https://masterclass.gitbook.io/masterclass-wordpress-lms-theme/" class="tophive-admin-small-button"><?php esc_html_e( 'Go to Documentation', 'masterclass' ); ?></a>
						<img class="moto-image" src="https://i.ibb.co/3MKtHqB/Customize.png">
					</div>
				</div>	
				<div class="col-12">
					<div class="tophive-admin-section">
						<h3 class="tophive-section-heading-small"><?php esc_html_e( 'Combine Assets', 'masterclass' ); ?></h3>
						<p class="tophive-section-sub-heading mb-40"><?php esc_html_e( 'helps site optimization and speed', 'masterclass' ); ?></p>
						<?php do_action( 'tophive/backend/admin/theme-page' ); ?>
					</div>
				</div>	
			</div>
			
		<?php
	}
	public function admin_tabs(){
		$admin_links = $this->admin_links();
		$tab = isset($_GET['tab']) ?  $_GET['tab'] : 'dashboard';
		?>
			<div class="tophive-admin-tabs">
				<ul>
					<?php
						foreach ($admin_links as $value) {
							$active = $tab == $value['slug'] ? 'active' : '';
							echo '<li class="'. $active . ' '. strtolower($value['name']) .'"><a href="'. $value['url'] .'" target="'. $value['target'] .'">'.esc_attr( $value['name'] ) .'</a></li>';
						}
					?>
				</ul>
			</div>
		<?php
	}

}

Tophive_Dashboard::get_instance();
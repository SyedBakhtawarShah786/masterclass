<?php
/**
 * Main Demo Import class/file. 
 * Code is mostly from Once Click Demo Import class https://wordpress.org/plugins/one-click-demo-import/
 *
 * @package MasterClass
 */
class TophiveDemoImport {

	private static $instance;

	public $importer;

	private $plugin_page;

	public $import_files;

	public $log_file_path;

	private $selected_index;

	private $selected_import_files;

	public $frontend_error_messages = array();

	private $before_import_executed = false;

  	private $plugin_page_setup = array();

	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup_plugin_with_filter_data' ) );
	}
	private function __clone() {}

	private function __wakeup() {}

	/**
	 * Main AJAX callback function for:
	 * 1). prepare import files (uploaded or predefined via filters)
	 * 2). execute 'before content import' actions (before import WP action)
	 * 3). import content
	 * 4). execute 'after content import' actions (before widget import WP action, widget import, customizer import, after import WP action)
	 */
	public function import_demo_data_ajax_callback() {
		ini_set( 'memory_limit', '750M' );
		Helpers::verify_ajax_call();

		$use_existing_importer_data = $this->use_existing_importer_data();

		if ( ! $use_existing_importer_data ) {
			Helpers::set_demo_import_start_time();

			$this->log_file_path = Helpers::get_log_path();

			$this->selected_index = empty( $_POST['selected'] ) ? 0 : absint( $_POST['selected'] );

			
			if ( ! empty( $_FILES ) ) { // Using manual file uploads?
				$this->selected_import_files = Helpers::process_uploaded_files( $_FILES, $this->log_file_path );

				$this->import_files[ $this->selected_index ]['import_file_name'] = esc_html__( 'Manually uploaded files', 'masterclass' );
			}
			elseif ( ! empty( $this->import_files[ $this->selected_index ] ) ) {
				$this->selected_import_files = Helpers::download_import_files( $this->import_files[ $this->selected_index ] );
				if ( is_wp_error( $this->selected_import_files ) ) {
					Helpers::log_error_and_send_ajax_response(
						$this->selected_import_files->get_error_message(),
						$this->log_file_path,
						esc_html__( 'Downloaded files', 'masterclass' )
					);
				}
				$log_added = Helpers::append_to_file(
					sprintf(
						esc_html__( 'The import files for: %s were successfully downloaded!', 'masterclass' ),
						$this->import_files[ $this->selected_index ]['import_file_name']
					) . Helpers::import_file_info( $this->selected_import_files ),
					$this->log_file_path,
					esc_html__( 'Downloaded files' , 'masterclass' )
				);
			}
			else {
				wp_send_json( esc_html__( 'No import files specified!', 'masterclass' ) );
			}
		}

		Helpers::set_tophive_import_data_transient( $this->get_current_importer_data() );

		if ( ! $this->before_import_executed ) {
			$this->before_import_executed = true;

			do_action( 'tophive/demo-import/content/execution/before', $this->selected_import_files, $this->import_files, $this->selected_index );
		}
		if ( ! empty( $this->selected_import_files['content'] ) ) {
			$this->append_to_frontend_error_messages( $this->importer->import_content( $this->selected_import_files['content'] ) );
		}
		do_action( 'tophive/demo-import/content/execution/after', $this->selected_import_files, $this->import_files, $this->selected_index );
		Helpers::set_tophive_import_data_transient( $this->get_current_importer_data() );
		if ( ! empty( $this->selected_import_files['customizer'] ) ) {
			wp_send_json( array( 'status' => 'customizerAJAX' ) );
		}

		if ( false !== has_action( 'tophive/demo-import/execution/all/after' ) ) {
			wp_send_json( array( 'status' => 'afterAllImportAJAX' ) );
		}

		$this->final_response();
	}

	public function tophive_import_customizer_data() {
		Helpers::verify_ajax_call();

		if ( $this->use_existing_importer_data() ) {
			do_action( 'tophive/demo-import/customizer/execution', $this->selected_import_files );
		}

		if ( false !== has_action( 'tophive/demo-import/execution/all/after' ) ) {
			wp_send_json( array( 'status' => 'afterAllImportAJAX' ) );
		}

		$this->final_response();
	}


	/**
	 * AJAX callback for the after all import action.
	 */
	public function tophive_after_all_import_data_ajax_callback() {
		Helpers::verify_ajax_call();

		if ( $this->use_existing_importer_data() ) {
			do_action( 'tophive/demo-import/execution/all/after', $this->selected_import_files, $this->import_files, $this->selected_index );
		}
		$this->final_response();
	}


	/**
	 * Send a JSON response with final report.
	 */
	private function final_response() {
		delete_transient( 'tophive_importer_data' );

		if ( empty( $this->frontend_error_messages ) ) {
			$response['message'] = '<div class="import-inner-content text-center">
				<svg width="5em" height="5em" viewBox="0 0 16 16" class="mb-40" fill="#4cd137" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
				</svg>
				<h3>'. esc_html__( 'Cheers !! Everything Done', 'masterclass' ) .'</h3>
				<p>'. esc_html__( 'Congratulations!!You have successfully completed importing the demo.', 'masterclass' ) .'</p>
			</div>
			<div class="import-inner-footer">
				<a href="" class="tophive-admin-small-button ghost-button end-import-popup">'. esc_html__( 'Close Importer', 'masterclass' ) .'</a>
			</div>';
		}
		else {
			$response['message'] = '<div class="import-inner-content text-center">
				<svg width="5em" height="5em" viewBox="0 0 16 16" class="mb-40" fill="#4cd137" xmlns="http://www.w3.org/2000/svg">
				  <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
				</svg>
				<h3>'. esc_html__( 'Cheers !! Everything Done', 'masterclass' ) .'</h3>
				<p>'. esc_html__( 'Congratulations!!You have successfully completed importing the demo.', 'masterclass' ) .'</p>
			</div>
			<div class="import-inner-footer">
				<a href="" class="tophive-admin-small-button ghost-button end-import-popup">'. esc_html__( 'Close Importer', 'masterclass' ) .'</a>
			</div>';
		}

		wp_send_json( $response );
	}


	/**
	 * Get content importer data, so we can continue the import with this new AJAX request.
	 *
	 * @return boolean
	 */
	private function use_existing_importer_data() {
		if ( $data = get_transient( 'tophive_importer_data' ) ) {
			$this->frontend_error_messages = empty( $data['frontend_error_messages'] ) ? array() : $data['frontend_error_messages'];
			$this->log_file_path           = empty( $data['log_file_path'] ) ? '' : $data['log_file_path'];
			$this->selected_index          = empty( $data['selected_index'] ) ? 0 : $data['selected_index'];
			$this->selected_import_files   = empty( $data['selected_import_files'] ) ? array() : $data['selected_import_files'];
			$this->import_files            = empty( $data['import_files'] ) ? array() : $data['import_files'];
			$this->before_import_executed  = empty( $data['before_import_executed'] ) ? false : $data['before_import_executed'];
			$this->importer->set_importer_data( $data );

			return true;
		}
		return false;
	}


	/**
	 * Get the current state of selected data.
	 *
	 * @return array
	 */
	public function get_current_importer_data() {
		return array(
			'frontend_error_messages' => $this->frontend_error_messages,
			'log_file_path'           => $this->log_file_path,
			'selected_index'          => $this->selected_index,
			'selected_import_files'   => $this->selected_import_files,
			'import_files'            => $this->import_files,
			'before_import_executed'  => $this->before_import_executed,
		);
	}


	/**
	 * Getter function to retrieve the private log_file_path value.
	 *
	 * @return string The log_file_path value.
	 */
	public function get_log_file_path() {
		return $this->log_file_path;
	}


	/**
	 * Setter function to append additional value to the private frontend_error_messages value.
	 *
	 * @param string $additional_value The additional value that will be appended to the existing frontend_error_messages.
	 */
	public function append_to_frontend_error_messages( $text ) {
		$lines = array();

		if ( ! empty( $text ) ) {
			$text = str_replace( '<br>', PHP_EOL, $text );
			$lines = explode( PHP_EOL, $text );
		}

		foreach ( $lines as $line ) {
			if ( ! empty( $line ) && ! in_array( $line , $this->frontend_error_messages ) ) {
				$this->frontend_error_messages[] = $line;
			}
		}
	}


	/**
	 * Display the frontend error messages.
	 *
	 * @return string Text with HTML markup.
	 */
	public function frontend_error_messages_display() {
		$output = '';

		if ( ! empty( $this->frontend_error_messages ) ) {
			foreach ( $this->frontend_error_messages as $line ) {
				$output .= esc_html( $line );
				$output .= '<br>';
			}
		}

		return $output;
	}

	/**
	 * Get data from filters, after the theme has loaded and instantiate the importer.
	 */
	public function setup_plugin_with_filter_data() {
		if ( ! ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) ) {
			return;
		}

		$this->import_files = Helpers::validate_import_file_info( apply_filters( 'tophive/demo-import/files', array() ) );

		$import_actions = new ImportActions();
		$import_actions->register_hooks();

		$importer_options = apply_filters( 'tophive/demo-importer/importer-options', array(
			'fetch_attachments' => true,
		) );

		$logger_options = apply_filters( 'tophive/demo-importer/logger-options', array(
			'logger_min_level' => 'warning',
		) );

		$logger            = new Logger();
		$logger->min_level = $logger_options['logger_min_level'];

		$this->importer = new Importer( $importer_options, $logger );
	}
}

<?php
/**
 * Class for declaring the content importer
 *
 * @package MasterClass
 */

class Importer {
	private $importer;

	private $microtime;

	public $logger;

	private $tophiveimporter;

	public function __construct( $importer_options = array(), $logger = null ) {
		$this->include_required_files();
		$this->importer = new WXRImporter( $importer_options );

		$this->logger = $logger;
		if ( ! empty( $this->logger ) ) {
			$this->set_logger( $this->logger );
		}

		$this->tophiveimporter = TophiveDemoImport::get_instance();
	}


	/**
	 * Include required files.
	 */
	private function include_required_files() {
		if ( ! class_exists( '\WP_Importer' ) ) {
			require ABSPATH . '/wp-admin/includes/class-wp-importer.php';
		}
	}

	/**
	 * Imports content from a WordPress export file.
	 *
	 * @param string $data_file path to xml file, file with WordPress export data.
	 */
	public function import( $data_file ) {
		$this->importer->import( $data_file );
	}

	/**
	 * Set the logger used in the import
	 *
	 * @param object $logger logger instance.
	 */
	public function set_logger( $logger ) {
		$this->importer->set_logger( $logger );
	}

	/**
	 * Get all protected variables from the WXR_Importer needed for continuing the import.
	 */
	public function get_importer_data() {
		return $this->importer->get_importer_data();
	}

	/**
	 * Sets all protected variables from the WXR_Importer needed for continuing the import.
	 *
	 * @param array $data with set variables.
	 */
	public function set_importer_data( $data ) {
		$this->importer->set_importer_data( $data );
	}

	/**
	 * Import content from an WP XML file.
	 *
	 * @param string $import_file_path Path to the import file.
	 */
	public function import_content( $import_file_path ) {
		$this->microtime = microtime( true );

		// Increase PHP max execution time. Just in case, even though the AJAX calls are only 25 sec long.
		if ( strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) === false ) {
			set_time_limit( apply_filters( 'tophive/demo-import/ajax-call/time-limit', 300 ) );
		}

		// Disable import of authors.
		add_filter( 'wxr_importer.pre_process.user', '__return_false' );

		// Check, if we need to send another AJAX request and set the importing author to the current user.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'new_ajax_request_maybe' ) );

		// Disables generation of multiple image sizes (thumbnails) in the content import step.
		if ( ! apply_filters( 'tophive/demo-import/content/thumbnail/generate', true ) ) {
			add_filter( 'intermediate_image_sizes_advanced', '__return_null' );
		}

		// Import content.
		if ( ! empty( $import_file_path ) ) {
			ob_start();
				$this->import( $import_file_path );
			$message = ob_get_clean();
		}

		// Return any error messages for the front page output (errors, critical, alert and emergency level messages only).
		return $this->logger->error_output;
	}


	/**
	 * Check if we need to create a new AJAX request, so that server does not timeout.
	 *
	 * @param array $data current post data.
	 * @return array
	 */
	public function new_ajax_request_maybe( $data ) {
		$time = microtime( true ) - $this->microtime;

		if ( $time > apply_filters( 'tophive/demo-import/ajax-call/time', 20 ) ) {
			$response = array(
				'status'  => 'newAJAX',
				'message' => 'Time for new AJAX request!: ' . $time,
			);

			$message = ob_get_clean();
			if ( ! empty( $message ) ) {
				$this->tophiveimporter->append_to_frontend_error_messages( $message );
			}
			$log_added = Helpers::append_to_file(
				esc_html__( 'New AJAX call!' , 'masterclass' ) . PHP_EOL . $message,
				$this->tophiveimporter->get_log_file_path(),
				''
			);

			$this->set_current_importer_data();
			wp_send_json( $response );
		}
		$current_user_obj    = wp_get_current_user();
		$data['post_author'] = $current_user_obj->user_login;

		return $data;
	}


	/**
	 * Set current state of the content importer, so we can continue the import with new AJAX request.
	 */
	private function set_current_importer_data() {
		$data = array_merge( $this->tophiveimporter->get_current_importer_data(), $this->get_importer_data() );

		Helpers::set_tophive_import_data_transient( $data );
	}
}

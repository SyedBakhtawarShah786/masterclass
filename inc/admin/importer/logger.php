<?php
/**
 * Logger class used in the theme
 *
 * @package MasterClass
 */

require_once 'awesomemotive/wp-content-importer-v2/WPImporterLoggerCLI.php';

class Logger extends WPImporterLoggerCLI {
	public $error_output = '';

	/**
	 * Overwritten log function from WP_Importer_Logger_CLI.
	 *
	 * Logs with an arbitrary level.
	 *
	 */
	public function log( $level, $message, array $context = array() ) {
		$this->error_output( $level, $message, $context = array() );

		if ( $this->level_to_numeric( $level ) < $this->level_to_numeric( $this->min_level ) ) {
			return;
		}

		printf(
			'[%s] %s' . PHP_EOL,
			strtoupper( $level ),
			$message
		);
	}

	/**
	 * Save messages for error output.
	 * Only the messages greater then Error.
	 *
	 */
	public function error_output( $level, $message, array $context = array() ) {
		if ( $this->level_to_numeric( $level ) < $this->level_to_numeric( 'error' ) ) {
			return;
		}

		$this->error_output .= sprintf(
			'[%s] %s<br>',
			strtoupper( $level ),
			$message
		);
	}
}

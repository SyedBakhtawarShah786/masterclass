<?php
/**
 * Class for the Redux importer
 *
 * @see https://wordpress.org/plugins/redux-framework/
 * @package MasterClass
 */

class ReduxImporter {
	public static function import( $import_data ) {
		$thimport          = TophiveDemoImport::get_instance();
		$log_file_path = $thimport->get_log_file_path();

		if ( ! class_exists( 'ReduxFramework' ) ) {
			$error_message = esc_html__( 'The Redux plugin is not activated, so the Redux import was skipped!', 'masterclass' );

			$thimport->append_to_frontend_error_messages( $error_message );

			Helpers::append_to_file(
				$error_message,
				$log_file_path,
				esc_html__( 'Importing Redux settings' , 'masterclass' )
			);
			return;
		}

		foreach ( $import_data as $redux_item ) {
			$redux_options_raw_data = Helpers::data_from_file( $redux_item['file_path'] );

			$redux_options_data = json_decode( $redux_options_raw_data, true );

			$redux_framework = \ReduxFrameworkInstances::get_instance( $redux_item['option_name'] );

			if ( isset( $redux_framework->args['opt_name'] ) ) {
				$redux_framework->set_options( $redux_options_data );
				$log_added = Helpers::append_to_file(
					sprintf( esc_html__( 'Redux settings import for: %s finished successfully!', 'masterclass' ), $redux_item['option_name'] ),
					$log_file_path,
					esc_html__( 'Importing Redux settings' , 'masterclass' )
				);
			}
			else {
				$error_message = sprintf( esc_html__( 'The Redux option name: %s, was not found in this WP site, so it was not imported!', 'masterclass' ), $redux_item['option_name'] );

				$thimport->append_to_frontend_error_messages( $error_message );

				Helpers::append_to_file(
					$error_message,
					$log_file_path,
					esc_html__( 'Importing Redux settings' , 'masterclass' )
				);
			}
		}
	}
}

<?php
/**
 * Class for the import actions
 *
 * @package MasterClass
 */
require_once 'widget-importer.php';
require_once 'customizer-import.php';

class ImportActions {
	/**
	 * Register all action hooks for this class.
	 */
	public function register_hooks() {
		// Before content import.
		add_action( 'tophive/demo-import/content/execution/before', array( $this, 'before_content_import_action' ), 10, 3 );

		// After content import.
		add_action( 'tophive/demo-import/content/execution/after', array( $this, 'before_widget_import_action' ), 10, 3 );
		add_action( 'tophive/demo-import/content/execution/after', array( $this, 'widgets_import' ), 20, 3 );
		add_action( 'tophive/demo-import/content/execution/after', array( $this, 'redux_import' ), 30, 3 );

		// Customizer import.
		add_action( 'tophive/demo-import/customizer/execution', array( $this, 'customizer_import' ), 10, 1 );

		// After full import action.
		add_action( 'tophive/demo-import/execution/all/after', array( $this, 'after_import_action' ), 10, 3 );

		// Special widget import cases.
		if ( apply_filters( 'tophive/demo-import/enable_custom_menu_widget_ids_fix', true ) ) {
			add_action( 'tophive/demo-import/widget_settings_array', array( $this, 'fix_custom_menu_widget_ids' ) );
		}
	}


	/**
	 * Change the menu IDs in the custom menu widgets in the widget import data.
	 * This solves the issue with custom menu widgets not having the correct (new) menu ID, because they
	 * have the old menu ID from the export site.
	 *
	 * @param array $widget The widget settings array.
	 */
	public function fix_custom_menu_widget_ids( $widget ) {
		if ( ! array_key_exists( 'nav_menu', $widget ) || empty( $widget['nav_menu'] ) || ! is_int( $widget['nav_menu'] ) ) {
			return $widget;
		}

		$importerclass       = TophiveDemoImport::get_instance();
		$content_import_data = $importerclass->importer->get_importer_data();
		$term_ids            = $content_import_data['mapping']['term_id'];

		$widget['nav_menu'] = $term_ids[ $widget['nav_menu'] ];

		return $widget;
	}


	/**
	 * Execute the widgets import.
	 */
	public function widgets_import( $selected_import_files, $import_files, $selected_index ) {
		if ( ! empty( $selected_import_files['widgets'] ) ) {
			WidgetImporter::import( $selected_import_files['widgets'] );
		}
	}


	/**
	 * Execute the Redux import.
	 *
	 */
	public function redux_import( $selected_import_files, $import_files, $selected_index ) {
		if ( ! empty( $selected_import_files['redux'] ) ) {
			ReduxImporter::import( $selected_import_files['redux'] );
		}
	}


	/**
	 * Execute the customizer import.
	 *
	 */
	public function customizer_import( $selected_import_files ) {
		if ( ! empty( $selected_import_files['customizer'] ) ) {
			CustomizerImporter::import( $selected_import_files['customizer'] );
		}
	}


	/**
	 * Execute the action: tophive/demo-import/content/before'.
	 *
	 */
	public function before_content_import_action( $selected_import_files, $import_files, $selected_index ) {
		$this->do_import_action( 'tophive/demo-import/content/before', $import_files[ $selected_index ] );
	}


	/**
	 * Execute the action: 'tophive/demo-import/widgets/before'.
	 *
	 */
	public function before_widget_import_action( $selected_import_files, $import_files, $selected_index ) {
		$this->do_import_action( 'tophive/demo-import/widgets/before', $import_files[ $selected_index ] );
	}


	/**
	 * Execute the action: 'tophive/demo-import/after'.
	 */
	public function after_import_action( $selected_import_files, $import_files, $selected_index ) {
		$this->do_import_action( 'tophive/demo-import/after', $import_files[ $selected_index ] );
	}


	/**
	 * Register the do_action hook, so users can hook to these during import.
	 *
	 */
	private function do_import_action( $action, $selected_import ) {
		if ( false !== has_action( $action ) ) {
			$importerclass = TophiveDemoImport::get_instance();
			$log_file_path = $importerclass->get_log_file_path();

			ob_start();
				do_action( $action, $selected_import );
			$message = ob_get_clean();

			$log_added = Helpers::append_to_file(
				$message,
				$log_file_path,
				$action
			);
		}
	}
}

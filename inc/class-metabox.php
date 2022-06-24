<?php

if ( is_admin() ) {
	// Calls the class on the post edit screen.
	add_action( 'load-post.php', array( 'Tophive_MetaBox', 'get_instance' ) );
	add_action( 'load-post-new.php', array( 'Tophive_MetaBox', 'get_instance' ) );
}

/**
 * The Metabox.
 */
class Tophive_MetaBox {

	public static $_instance = null;
	/**
	 * @see Tophive_Form_Fields
	 * @var Tophive_Form_Fields null
	 */
	public $field_builder = null;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			
			add_action( 'save_post', array( self::$_instance, 'save' ) );
			add_action( 'admin_enqueue_scripts', array( self::$_instance, 'scripts' ) );
			require_once get_template_directory() . '/inc/class-metabox-fields.php';
			self::$_instance->field_builder = new Tophive_Form_Fields();
			self::$_instance->fields_config();
			do_action( 'tophive/metabox/init', self::$_instance );

		}

		return self::$_instance;
	}

	/**
	 * Add metabox fields
	 *
	 * @since 0.2.2
	 */
	function fields_config() {

		$this->field_builder->add_tab(
			'layout',
			array(
				'title' => esc_html__( 'Layout', 'masterclass' ),
				'icon'  => 'dashicons dashicons-grid-view',
			)
		);

		$this->field_builder->add_tab(
			'page_header',
			array(
				'title' => esc_html__( 'Page Header', 'masterclass' ),
				'icon'  => 'dashicons dashicons-editor-kitchensink',
			)
		);

		$this->field_builder->add_field(
			array(
				'title'        => esc_html__( 'Content Layout', 'masterclass' ),
				'name'         => 'content_layout',
				'tab'          => 'layout',
				'type'         => 'select',
				'choices'      => array(
					'full-width'     => esc_html__( 'Full Width', 'masterclass' ),
					'full-stretched' => esc_html__( 'Full Width - Stretched', 'masterclass' ),
				),
				'show_default' => true,

			)
		);
		$this->field_builder->add_field(
			array(
				'title'           => esc_html__( 'Content spaing', 'masterclass' ),
				'name'            => 'content_spacing',
				'tab'          		=> 'layout',
				'type'            => 'select',
				'choices'      => array(
					'md-space' => esc_html__( 'Medium space', 'masterclass' ),
					'xm-space'     => esc_html__( 'Extra small space', 'masterclass' ),
					'sm-space' => esc_html__( 'Small space', 'masterclass' ),
					'lg-space' => esc_html__( 'Large space', 'masterclass' ),
					'xl-space' => esc_html__( 'Xtra Large space', 'masterclass' ),
					'no-gap'     => esc_html__( 'No gap', 'masterclass' ),
				),
				'default' => 'no-gap'
			)
		);
		$this->field_builder->add_field(
			array(
				'title'         => esc_html__( 'Sidebar Settings', 'masterclass' ),
				'name'          => 'sidebar',
				'tab'           => 'layout',
				'type'          => 'select',
				'choices'       => tophive_get_config_sidebar_layouts(),
				'show_default'  => true,
				'default_label' => esc_html__( 'Inherit from customize settings', 'masterclass' ),
			)
		);
		$disable_elements_choices = array(
			'disable_header'        => esc_html__( 'Disable Header', 'masterclass' ),
			'disable_page_title'    => esc_html__( 'Disable Title', 'masterclass' ),
		);
		if ( class_exists( 'Tophive_Pro' ) ) {
			$disable_elements_choices['disable_footer_top'] = esc_html__( 'Disable Footer Top', 'masterclass' );
		}
		$disable_elements_choices['disable_footer_main'] = esc_html__( 'Disable Footer Main', 'masterclass' );
		$disable_elements_choices['disable_footer_bottom'] = esc_html__( 'Disable Footer Bottom', 'masterclass' );
		$this->field_builder->add_field(
			array(
				'title'   => esc_html__( 'Disable Elements', 'masterclass' ),
				'name'    => 'disable_elements',
				'tab'     => 'layout',
				'type'    => 'multiple_checkbox',
				'choices' => $disable_elements_choices,
			)
		);

		$this->field_builder->add_field(
			array(
				'title'   => esc_html__( 'Display', 'masterclass' ),
				'name'    => 'page_header_display',
				'tab'     => 'page_header',
				'type'    => 'select',
				'choices' => array(
					'default'  => esc_html__( 'Inherit from customize settings', 'masterclass' ),
					'normal'   => esc_html__( 'Default', 'masterclass' ),
					'cover'    => esc_html__( 'Cover', 'masterclass' ),
					'titlebar' => esc_html__( 'Titlebar', 'masterclass' ),
					'none'     => esc_html__( 'Hide', 'masterclass' ),
				),
			)
		);

		if ( Tophive_Breadcrumb::get_instance()->support_plugins_active() ) {
			$this->field_builder->add_tab(
				'breadcrumb',
				array(
					'title' => esc_html__( 'Breadcrumb', 'masterclass' ),
					'icon'  => 'dashicons dashicons-admin-links',
				)
			);
			$this->field_builder->add_field(
				array(
					'title'   => esc_html__( 'Breadcrumb', 'masterclass' ),
					'tab'     => 'breadcrumb',
					'name'    => 'breadcrumb_display',
					'type'    => 'select',
					'choices' => array(
						'default' => esc_html__( 'Inherit from customize settings', 'masterclass' ),
						'hide'    => esc_html__( 'Hide', 'masterclass' ),
						'show'    => esc_html__( 'Show', 'masterclass' ),
					),
				)
			);
		}

	}

	public function scripts( $hook ) {
		if ( 'post.php' != $hook && 'post-new.php' != $hook ) {
			return;
		}
		$suffix = top_hive()->get_asset_suffix();
		wp_enqueue_script( 'tophive-metabox', esc_url( get_template_directory_uri() ) . '/assets/js/admin/metabox' . $suffix . '.js', array( 'jquery' ), Tophive::$version, true );
		wp_enqueue_style( 'tophive-metabox', esc_url( get_template_directory_uri() ) . '/assets/css/admin/metabox' . $suffix . '.css', false, Tophive::$version );
	}

	public function get_support_post_types() {
		$args = array(
			'public' => true,
		);

		$output     = 'names'; // Names or objects, note names is the default.
		$operator   = 'and'; // Can use 'and' or 'or'.
		$post_types = get_post_types( $args, $output, $operator );

		return array_values( $post_types );
	}



	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 * @return int|bool
	 */
	public function save( $post_id ) {

		/**
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		if ( ! isset( $_POST['tophive_page_settings_nonce'] ) ) { // Check if our nonce is set.
			return $post_id;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['tophive_page_settings_nonce'] ) );

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'tophive_page_settings' ) ) {
			return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == get_post_type( $post_id ) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		/**
		 * @since 0.2.2
		 */
		$settings = $this->field_builder->get_submitted_values();

		foreach ( $settings as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$value = wp_kses_post( $value );
			} else {
				$value = array_map( 'wp_kses_post', $value );
			}
			// Update the meta field.
			update_post_meta( $post_id, '_tophive_' . $key, $value );
		}

	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'tophive_page_settings', 'tophive_page_settings_nonce' );
		$values = array();
		foreach ( $this->field_builder->get_all_fields() as $key => $f ) {
			if ( 'multiple_checkbox' == $f['type'] ) {
				foreach ( (array) $f['choices'] as $_key => $label ) {
					$value           = get_post_meta( $post->ID, '_tophive_' . $_key, true );
					$values[ $_key ] = $value;
				}
			} elseif ( $f['name'] ) {
				$values[ $f['name'] ] = get_post_meta( $post->ID, '_tophive_' . $f['name'], true );
			}
		}

		$this->field_builder->set_values( $values );
		$this->field_builder->render();

	}
}

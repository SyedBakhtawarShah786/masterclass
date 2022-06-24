<?php

/**
 * Class Tophive_WC_Single_Product
 *
 * Single product settings
 */
class Tophive_WC_Single_Product {
	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ), 100 );
		if ( is_admin() || is_customize_preview() ) {
			add_filter( 'Tophive_Control_Args', array( $this, 'add_product_url' ), 35 );
		}

		add_action( 'wp', array( $this, 'single_product_hooks' ) );
	}

	/**
	 * Add more class if nav showing
	 *
	 * @param array $classes HTML classes.
	 *
	 * @return array
	 */
	function post_class( $classes ) {
		if ( top_hive()->get_setting( 'wc_single_product_nav_show' ) ) {
			$classes[] = 'nav-in-title';
		}
		return $classes;
	}

	/**
	 * Get adjacent product
	 *
	 * @param bool   $in_same_term In same term.
	 * @param string $excluded_terms Exlclude terms.
	 * @param bool   $previous Previous.
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return null|string|WP_Post
	 */
	public function get_adjacent_product( $in_same_term = false, $excluded_terms = '', $previous = true, $taxonomy = 'product_cat' ) {
		return get_adjacent_post( $in_same_term, $excluded_terms, $previous, $taxonomy );
	}

	/**
	 * Display prev - next button
	 */
	public function product_prev_next() {
		if ( ! top_hive()->get_setting( 'wc_single_product_nav_show' ) ) {
			return;
		}
		$prev_post = $this->get_adjacent_product();
		$next_post = $this->get_adjacent_product( false, '', false );
		if ( $prev_post || $next_post ) {
			?>
			<div class="wc-product-nav">
				<?php if ( $prev_post ) { ?>
					<a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>" title="<?php the_title_attribute( array( 'post' => $prev_post ) ); ?>" class="prev-link">
						<span class="nav-btn nav-next"><?php echo apply_filters( 'tophive_nav_prev_icon', '' ); ?></span>
						<?php if ( has_post_thumbnail( $prev_post ) ) { ?>
							<span class="nav-thumbnail">
								<?php
								echo get_the_post_thumbnail( $prev_post, 'woocommerce_thumbnail' );
								?>
							</span>
						<?php } ?>
					</a>
				<?php } ?>
				<?php if ( $next_post ) { ?>
					<a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" title="<?php the_title_attribute( array( 'post' => $next_post ) ); ?>" class="next-link">
						<span class="nav-btn nav-next">
						<?php echo apply_filters( 'tophive_nav_next_icon', '' ); ?>
						</span>
						<?php if ( has_post_thumbnail( $next_post ) ) { ?>
							<span class="nav-thumbnail">
								<?php
								echo get_the_post_thumbnail( $next_post, 'woocommerce_thumbnail' );
								?>
							</span>
						<?php } ?>
					</a>
				<?php } ?>
			</div>
			<?php
		}
	}

	/**
	 * Hooks for single product
	 */
	function single_product_hooks() {
		if ( ! is_product() ) {
			return;
		}

		add_action( 'wc_after_single_product_title', array( $this, 'product_prev_next' ), 2 );
		add_filter( 'post_class', array( $this, 'post_class' ) );

		if ( top_hive()->get_setting( 'wc_single_product_tab_hide_description' ) ) {
			add_filter( 'woocommerce_product_description_heading', '__return_false', 999 );
		}

		if ( top_hive()->get_setting( 'wc_single_product_tab_hide_attr_heading' ) ) {
			add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 999 );
		}

		$tab_type = top_hive()->get_setting( 'wc_single_product_tab' );

		if ( 'section' == $tab_type || 'toggle' == $tab_type ) {
			add_filter( 'woocommerce_product_description_heading', '__return_false', 999 );
			add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 999 );
		}

	}

	/**
	 * Add url to customize preview when section open
	 *
	 * @param array $args Args to add.
	 *
	 * @return mixed
	 */
	public function add_product_url( $args ) {

		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'posts_per_page' => 1,
				'orderby'        => 'rand',
			)
		);

		$products = $query->get_posts();
		if ( count( $products ) ) {
			$args['section_urls']['wc_single_product'] = get_permalink( $products[0] );
		}

		return $args;
	}

	/**
	 * Customize config
	 *
	 * @param array $configs Config args.
	 *
	 * @return array
	 */
	public function config( $configs ) {
		$section = 'wc_single_product';

		$configs[] = array(
			'name'     => $section,
			'type'     => 'section',
			'panel'    => 'woocommerce',
			'title'    => esc_html__( 'Single Product Page', 'masterclass' ),
			'priority' => 19,
		);

		$configs[] = array(
			'name'    => 'wc_single_layout_h',
			'type'    => 'heading',
			'section' => $section,
			'label'   => esc_html__( 'Layout', 'masterclass' ),
		);

		/*
		$configs[] = array(
			'name'    => 'wc_single_layout',
			'type'    => 'select',
			'section' => $section,
			'default' => 'default',
			'label'   => esc_html__( 'Layout', 'masterclass' ),
			'choices' => array(
				'default'    => esc_html__( 'Default', 'masterclass' ),
				'top-medium' => esc_html__( 'Top Gallery Boxed', 'masterclass' ),
				'top-full'   => esc_html__( 'Top Gallery Full Width', 'masterclass' ),
				'left-grid'  => esc_html__( 'Left Gallery Grid', 'masterclass' ),
			)
		);
		*/

		$configs[] = array(
			'name'             => 'wc_single_layout',
			'type'             => 'image_select',
			'section'          => $section,
			'title'            => esc_html__( 'Layout', 'masterclass' ),
			'default'          => 'default',
			'choices'          => array(
				'default'    => array(
					'img'   => esc_url( get_template_directory_uri() ) . '/assets/images/customizer/wc-layout-default.svg',
					'label' => esc_html__( 'Default', 'masterclass' ),
				),
				'top-medium' => array(
					'img'     => esc_url( get_template_directory_uri() ) . '/assets/images/customizer/wc-layout-top-medium.svg',
					'label'   => esc_html__( 'Top Gallery Boxed', 'masterclass' ),
				),
				'top-full'   => array(
					'img'     => esc_url( get_template_directory_uri() ) . '/assets/images/customizer/wc-layout-top-full.svg',
					'label'   => esc_html__( 'Top Gallery Full Width', 'masterclass' ),
				),
				'left-grid'  => array(
					'img'     => esc_url( get_template_directory_uri() ) . '/assets/images/customizer/wc-layout-left-grid.svg',
					'label'   => esc_html__( 'Left Gallery Grid', 'masterclass' ),

				),
			),
		);

		$configs[] = array(
			'name'     => "{$section}_nav_heading",
			'type'     => 'heading',
			'section'  => $section,
			'title'    => esc_html__( 'Product Navigation', 'masterclass' ),
			'priority' => 39,
		);

		$configs[] = array(
			'name'           => "{$section}_nav_show",
			'type'           => 'checkbox',
			'default'        => 1,
			'section'        => $section,
			'checkbox_label' => esc_html__( 'Show Product Navigation', 'masterclass' ),
			'priority'       => 39,
		);

		$configs[] = array(
			'name'     => "{$section}_tab_heading",
			'type'     => 'heading',
			'section'  => $section,
			'title'    => esc_html__( 'Product Tabs', 'masterclass' ),
			'priority' => 40,
		);

		$configs[] = array(
			'name'     => "{$section}_tab",
			'type'     => 'select',
			'default'  => 'horizontal',
			'section'  => $section,
			'label'    => esc_html__( 'Tab Layout', 'masterclass' ),
			'choices'  => array(
				'horizontal' => esc_html__( 'Horizontal', 'masterclass' ),
				'vertical'   => esc_html__( 'Vertical', 'masterclass' ),
				'toggle'     => esc_html__( 'Toggle', 'masterclass' ),
				'sections'   => esc_html__( 'Sections', 'masterclass' ),
			),
			'priority' => 45,
		);

		$configs[] = array(
			'name'           => "{$section}_tab_hide_description",
			'type'           => 'checkbox',
			'default'        => 1,
			'section'        => $section,
			'checkbox_label' => esc_html__( 'Hide product description heading', 'masterclass' ),
			'priority'       => 46,
		);

		$configs[] = array(
			'name'           => "{$section}_tab_hide_attr_heading",
			'type'           => 'checkbox',
			'default'        => 1,
			'section'        => $section,
			'checkbox_label' => esc_html__( 'Hide product additional information heading', 'masterclass' ),
			'priority'       => 47,
		);

		$configs[] = array(
			'name'           => "{$section}_tab_hide_review_heading",
			'type'           => 'checkbox',
			'default'        => 0,
			'section'        => $section,
			'checkbox_label' => esc_html__( 'Hide product review heading', 'masterclass' ),
			'selector'       => '.woocommerce-Reviews-title',
			'css_format'     => 'display: none;',
			'priority'       => 48,
		);

		$configs[] = array(
			'name'     => "{$section}_upsell_heading",
			'type'     => 'heading',
			'section'  => $section,
			'title'    => esc_html__( 'Upsell Products', 'masterclass' ),
			'priority' => 60,
		);

		$configs[] = array(
			'name'     => "{$section}_upsell_number",
			'type'     => 'text',
			'default'  => 3,
			'section'  => $section,
			'label'    => esc_html__( 'Number of upsell products', 'masterclass' ),
			'priority' => 65,
		);

		$configs[] = array(
			'name'            => "{$section}_upsell_columns",
			'type'            => 'text',
			'device_settings' => true,
			'section'         => $section,
			'label'           => esc_html__( 'Upsell products per row', 'masterclass' ),
			'priority'        => 66,
		);

		$configs[] = array(
			'name'     => "{$section}_related_heading",
			'type'     => 'heading',
			'section'  => $section,
			'title'    => esc_html__( 'Related Products', 'masterclass' ),
			'priority' => 70,
		);

		$configs[] = array(
			'name'     => "{$section}_related_number",
			'type'     => 'text',
			'default'  => 3,
			'section'  => $section,
			'label'    => esc_html__( 'Number of related products', 'masterclass' ),
			'priority' => 75,
		);

		$configs[] = array(
			'name'            => "{$section}_related_columns",
			'type'            => 'text',
			'device_settings' => true,
			'section'         => $section,
			'label'           => esc_html__( 'Related products per row', 'masterclass' ),
			'priority'        => 76,
		);

		$configs[] = array(
			'name'           => 'wc_single_layout_breadcrumb',
			'type'           => 'checkbox',
			'section'        => $section,
			'default'        => 1,
			'checkbox_label' => esc_html__( 'Show shop breadcrumb', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'single_product_page_cover',
			'type'            => 'checkbox',
			'section'         => $section,
			'default'         => 1,
			'selector'        => '.woocommerce.single-product #page-cover',
			'checkbox_label'  => esc_html__( 'Show page cover', 'masterclass' ),
		);

		return $configs;
	}
}

new Tophive_WC_Single_Product();

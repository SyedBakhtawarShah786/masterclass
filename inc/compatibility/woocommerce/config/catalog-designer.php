<?php

class Tophive_WC_Catalog_Designer {

	private $configs = array();

	function __construct() {
		add_filter( 'tophive/customizer/config', array( $this, 'config' ), 100 );
		if ( is_admin() || is_customize_preview() ) {
			add_filter( 'Tophive_Control_Args', array( $this, 'add_catalog_url' ), 35 );
		}

		// Loop.
		add_action( 'tophive_wc_product_loop', array( $this, 'render' ) );
	}

	/**
	 * Get callback function for item part
	 *
	 * @param string $item_id ID of builder item.
	 *
	 * @return string|object|boolean
	 */
	function callback( $item_id ) {
		$cb = apply_filters( 'tophive/product-designer/part', false, $item_id, $this );
		if ( ! is_callable( $cb ) ) {
			$cb = array( $this, 'product__' . $item_id );
		}
		if ( is_callable( $cb ) ) {
			return $cb;
		}

		return false;
	}

	function render() {

		$items = top_hive()->get_setting( 'wc_cd_positions' );

		$this->configs['excerpt_type']   = top_hive()->get_setting( 'wc_cd_excerpt_type' );
		$this->configs['excerpt_length'] = top_hive()->get_setting( 'wc_cd_excerpt_length' );

		$this->configs = apply_filters( 'tophive_wc_catalog_designer/configs', $this->configs );

		$cb = $this->callback( 'media' );
		if ( $cb ) {
			call_user_func( $cb, array( null, $this ) );
		}

		echo '<div class="wc-product-contents">';

		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 */
		do_action( 'woocommerce_before_shop_loop_item' );

		$html = '';

		/**
		 * Allow 3rg party to render items html
		 */
		$html = apply_filters( 'tophive/product-designer/render_html', $html, $items, $this );

		if ( ! $html ) {
			foreach ( (array) $items as $item ) {
				$item = wp_parse_args(
					$item,
					array(
						'_key'         => '',
						'_visibility'  => '',
						'show_in_grid' => 1,
						'show_in_list' => 1,
					)
				);
				if ( 'hidden' !== $item['_visibility'] ) {

					$cb = $this->callback( $item['_key'] );

					if ( is_callable( $cb ) ) {
						$classes   = array();
						$classes[] = 'wc-product__part';
						$classes[] = 'wc-product__' . $item['_key'];

						if ( $item['show_in_grid'] ) {
							$classes[] = 'show-in-grid';
						} else {
							$classes[] = 'hide-in-grid';
						}
						if ( $item['show_in_list'] ) {
							$classes[] = 'show-in-list';
						} else {
							$classes[] = 'hide-in-list';
						}

						$item_html = '';
						ob_start();
						call_user_func( $cb, array( $item, $this ) );
						$item_html = ob_get_contents();
						ob_end_clean();

						if ( trim( $item_html ) != '' ) {
							$html .= '<div class="' . esc_attr( join( ' ', $classes ) ) . '">';
							$html .= $item_html;
							$html .= '</div>';
						}
					}
				}
			}
		}

		echo tophive_sanitize_filter($html); // WPCS: XSS OK.

		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 */
		do_action( 'woocommerce_after_shop_loop_item' );

		echo '</div>'; // End .wc-product-contents.

	}

	/**
	 * Preview url when section open
	 *
	 * @param array $args The section urls config.
	 *
	 * @return array
	 */
	function add_catalog_url( $args ) {
		$args['section_urls']['wc_catalog_designer'] = get_permalink( wc_get_page_id( 'shop' ) );

		return $args;
	}

	/**
	 * Get Default builder items for product designer
	 *
	 * @since 2.0.5
	 *
	 * @return array
	 */
	function get_default_items() {
		$items = array(
			array(
				'_key'         => 'category',
				'_visibility'  => '',
				'show_in_grid' => 1,
				'show_in_list' => 1,
				'title'        => esc_html__( 'Category', 'masterclass' ),
			),
			array(
				'_visibility'  => '',
				'_key'         => 'title',
				'title'        => esc_html__( 'Title', 'masterclass' ),
				'show_in_grid' => 1,
				'show_in_list' => 1,
			),
			array(
				'_key'         => 'rating',
				'_visibility'  => '',
				'show_in_grid' => 1,
				'show_in_list' => 1,
				'title'        => esc_html__( 'Rating', 'masterclass' ),
			),

			array(
				'_key'         => 'price',
				'_visibility'  => '',
				'show_in_grid' => 1,
				'show_in_list' => 1,
				'title'        => esc_html__( 'Price', 'masterclass' ),
			),
			array(
				'_key'         => 'description',
				'_visibility'  => '',
				'show_in_grid' => 0,
				'show_in_list' => 1,
				'title'        => esc_html__( 'Short Description', 'masterclass' ),
			),
			array(
				'_key'         => 'add_to_cart',
				'_visibility'  => '',
				'show_in_grid' => 1,
				'show_in_list' => 1,
				'title'        => esc_html__( 'Add To Cart', 'masterclass' ),
			),
		);

		return apply_filters( 'tophive/product-designer/body-items', $items );
	}


	function config( $configs ) {

		$section = 'wc_catalog_designer';

		$configs[] = array(
			'name'     => $section,
			'type'     => 'section',
			'panel'    => 'woocommerce',
			'priority' => 10,
			'label'    => esc_html__( 'Product Catalog Designer', 'masterclass' ),
		);

		// Catalog header.
		$configs[] = array(
			'name'            => 'wc_cd_show_catalog_header',
			'type'            => 'checkbox',
			'section'         => $section,
			'default'         => 1,
			'priority'        => 10,
			'selector'        => '.wc-product-listing',
			'render_callback' => 'woocommerce_content',
			'label'           => esc_html__( 'Show Catalog Filtering Bar', 'masterclass' ),
		);

		// Show view mod.
		$configs[] = array(
			'name'            => 'wc_cd_show_view_mod',
			'type'            => 'checkbox',
			'section'         => $section,
			'default'         => 1,
			'selector'        => '.wc-product-listing',
			'render_callback' => 'woocommerce_content',
			'checkbox_label'  => esc_html__( 'Show Grid/List View Buttons', 'masterclass' ),
			'priority'        => 11,
		);

		$configs[] = array(
			'name'            => 'wc_cd_default_view',
			'type'            => 'select',
			'section'         => $section,
			'default'         => 'grid',
			'priority'        => 12,
			'choices'         => array(
				'grid' => esc_html__( 'Grid', 'masterclass' ),
				'list' => esc_html__( 'List', 'masterclass' ),
			),
			'selector'        => '.wc-product-listing',
			'render_callback' => 'woocommerce_content',
			'label'           => esc_html__( 'Default View Mod', 'masterclass' ),
		);

		$configs[] = array(
			'name'             => 'wc_cd_positions',
			'section'          => $section,
			'label'            => esc_html__( 'Outside Media Items & Positions', 'masterclass' ),
			'type'             => 'repeater',
			'live_title_field' => 'title',
			'addable'          => false,
			'priority'         => 15,
			'selector'         => '.wc-product-listing',
			'render_callback'  => 'woocommerce_content',
			'default'          => $this->get_default_items(),
			'fields'           => apply_filters(
				'tophive/product-designer/body-field-config',
				array(
					array(
						'name' => '_key',
						'type' => 'hidden',
					),
					array(
						'name'  => 'title',
						'type'  => 'hidden',
						'label' => esc_html__( 'Title', 'masterclass' ),
					),
					array(
						'name'           => 'show_in_grid',
						'type'           => 'checkbox',
						'checkbox_label' => esc_html__( 'Show in grid view', 'masterclass' ),
					),
					array(
						'name'           => 'show_in_list',
						'type'           => 'checkbox',
						'checkbox_label' => esc_html__( 'Show in list view', 'masterclass' ),
					),
				)
			),
		);

		$configs[] = array(
			'name'     => 'wc_cd_excerpt_type',
			'type'     => 'select',
			'section'  => $section,
			'priority' => 17,
			'title'    => esc_html__( 'List view excerpt type', 'masterclass' ),
			'choices'  => array(
				'excerpt' => esc_html__( 'Product short description', 'masterclass' ),
				'content' => esc_html__( 'Full content', 'masterclass' ),
				'more'    => esc_html__( 'Strip by more tag', 'masterclass' ),
				'custom'  => esc_html__( 'Custom', 'masterclass' ),
			),
		);

		$configs[] = array(
			'name'     => 'wc_cd_excerpt_length',
			'type'     => 'text',
			'section'  => $section,
			'priority' => 17,
			'title'    => esc_html__( 'Custom list view excerpt length', 'masterclass' ),
			'required' => array( 'wc_cd_excerpt_type', '=', 'custom' ),
		);

		// Product Media.
		$configs[] = array(
			'name'     => 'wc_cd_memdia_h',
			'type'     => 'heading',
			'section'  => $section,
			'priority' => 25,
			'label'    => esc_html__( 'Product Media & Alignment', 'masterclass' ),
		);

		$configs[] = array(
			'name'            => 'wc_cd_list_media_width',
			'type'            => 'slider',
			'section'         => $section,
			'unit'            => '%',
			'max'             => 100,
			'device_settings' => true,
			'priority'        => 26,
			'selector'        => 'format',
			'css_format'      => '.woocommerce-listing.wc-list-view .product.tophive-col:not(.product-category) .wc-product-inner .wc-product-media { flex-basis: {{value_no_unit}}%; } .woocommerce-listing.wc-list-view .product.tophive-col:not(.product-category) .wc-product-inner .wc-product-contents{ flex-basis: calc(100% - {{value_no_unit}}%); }',
			'title'           => esc_html__( 'List View Media Width', 'masterclass' ),
		);

		$configs[] = array(
			'name'            => 'wc_cd_media_secondary',
			'type'            => 'select',
			'choices'         => array(
				'first' => esc_html__( 'Use first image of product gallery', 'masterclass' ),
				'last'  => esc_html__( 'Use last image of product gallery', 'masterclass' ),
				'none'  => esc_html__( 'Disable', 'masterclass' ),
			),
			'section'         => $section,
			'default'         => 'first',
			'priority'        => 27,
			'selector'        => '.wc-product-listing',
			'render_callback' => 'woocommerce_content',
			'description'     => esc_html__( 'This setting adds a hover effect that will reveal a secondary product thumbnail to product images on your product listings. This is ideal for displaying front and back images of products.', 'masterclass' ),
			'title'           => esc_html__( 'Secondary Thumbnail', 'masterclass' ),
		);

		$configs[] = array(
			'name'            => 'wc_cd_item_grid_align',
			'type'            => 'text_align_no_justify',
			'section'         => $section,
			'device_settings' => true,
			'priority'        => 28,
			'selector'        => '.wc-grid-view .wc-product-contents',
			'css_format'      => 'text-align: {{value}};',
			'title'           => esc_html__( 'Grid View - Content Alignment', 'masterclass' ),
		);

		$configs[] = array(
			'name'            => 'wc_cd_item_list_align',
			'type'            => 'text_align_no_justify',
			'section'         => $section,
			'device_settings' => true,
			'priority'        => 28,
			'selector'        => '.wc-list-view .wc-product-contents',
			'css_format'      => 'text-align: {{value}};',
			'title'           => esc_html__( 'List View - Content Alignment', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'wc_cd_image_border_radius',
			'type'            => 'css_ruler',
			'section'         => $section,
			'selector'        => '.wc-product-listing .wc-product-media, .related.products .wc-product-media',
			'priority'        => 28,
			'css_format' => array(
				'top'    => 'border-top-left-radius: {{value}};',
				'right'  => 'border-top-right-radius: {{value}};',
				'bottom' => 'border-bottom-right-radius: {{value}};',
				'left'   => 'border-bottom-left-radius: {{value}};',
			),
			'title'           => esc_html__( 'Border radius', 'masterclass' ),
		);

		// Product Sale Bubble.
		$configs[] = array(
			'name'     => 'wc_cd_sale_bubble_h',
			'type'     => 'heading',
			'section'  => $section,
			'priority' => 30,
			'label'    => esc_html__( 'Product Onsale Bubble', 'masterclass' ),
		);

		$configs[] = array(
			'name'            => 'wc_cd_sale_bubble_type',
			'type'            => 'select',
			'default'         => 'text',
			'priority'        => 31,
			'choices'         => array(
				'text'    => esc_html__( 'Text', 'masterclass' ),
				'percent' => esc_html__( 'Discount percent', 'masterclass' ),
				'value'   => esc_html__( 'Discount value', 'masterclass' ),
			),
			'selector'        => '.wc-product-listing',
			'render_callback' => 'woocommerce_content',
			'section'         => $section,
			'label'           => esc_html__( 'Display Type', 'masterclass' ),
		);

		$configs[] = array(
			'name'        => 'wc_cd_sale_bubble_styling',
			'type'        => 'styling',
			'section'     => $section,
			'priority'    => 32,
			'title'       => esc_html__( 'Styling', 'masterclass' ),
			'description' => esc_html__( 'Advanced styling for onsale button', 'masterclass' ),
			'selector'    => array(
				'normal' => '.woocommerce span.onsale',
			),
			'css_format'  => 'styling',
			'default'     => array(),
			'fields'      => array(
				'normal_fields' => array(
					'link_color'    => false, // disable for special field.
					'bg_image'      => false,
					'bg_cover'      => false,
					'bg_position'   => false,
					'bg_repeat'     => false,
					'bg_attachment' => false,
				),
				'hover_fields'  => false,
			),
		);
		// Product typography
		$configs[] = array(
			'name'     => 'wc_cd_typography',
			'type'     => 'heading',
			'section'  => $section,
			'priority' => 32,
			'label'    => esc_html__( 'Typography & Colors', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'wc_cd_category_typo',
			'type'            => 'typography',
			'section'         => $section,
			'priority'        => 32,
			'selector'        => '.wc-product-listing .wc-product-inner .wc-product__category a, .related.products .wc-product-inner .wc-product__category a',
			'css_format'      => 'typography',
			'title'           => esc_html__( 'Category', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'wc_cd_title_typo',
			'type'            => 'typography',
			'section'         => $section,
			'priority'        => 32,
			'selector'        => '.wc-product-listing .wc-product-inner .wc-product__title h2 a, .related.products .wc-product-inner .wc-product__title h2 a',
			'css_format'      => 'typography',
			'title'           => esc_html__( 'Title', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'wc_cd_price_typo',
			'type'            => 'typography',
			'section'         => $section,
			'priority'        => 32,
			'selector'        => '.wc-product-listing .wc-product-inner .wc-product__price .price .woocommerce-Price-amount, .related.products .wc-product-inner .wc-product__price .price .woocommerce-Price-amount',
			'css_format'      => 'typography',
			'title'           => esc_html__( 'Price', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'wc_cd_price_now_color',
			'type'            => 'color',
			'section'         => $section,
			'priority'        => 32,
			'selector'        => 'format',
			'css_format'      => '.wc-product-listing .wc-product__price .price .woocommerce-Price-amount, .related.products .wc-product__price .price .woocommerce-Price-amount{color : {{value}}}',
			'title'           => esc_html__( 'Price Color', 'masterclass' ),
		);
		$configs[] = array(
			'name'            => 'wc_cd_desc_typo',
			'type'            => 'typography',
			'section'         => $section,
			'priority'        => 32,
			'selector'        => '.wc-product-listing .wc-product__description p, .related.products .wc-product__description p',
			'css_format'      => 'typography',
			'title'           => esc_html__( 'Description', 'masterclass' ),
		);

		return $configs;
	}

	function product__media() {
		echo '<div class="wc-product-media">';
		/**
		 * Hook: tophive/wc-product/before-media
		 * hooked: woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'tophive/wc-product/before-media' );
		woocommerce_show_product_loop_sale_flash();
		woocommerce_template_loop_product_thumbnail();
		tophive_wc_secondary_product_thumbnail();
		do_action( 'tophive_after_loop_product_media' );
		/**
		 * Hook: tophive/wc-product/after-media
		 * hooked: woocommerce_template_loop_product_link_close - 10
		 */
		do_action( 'tophive/wc-product/after-media' );
		echo '</div>';
	}

	function product__title() {

		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );

		/**
		 * @see    woocommerce_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );

	}

	/**
	 * Trim the excerpt with custom length.
	 *
	 * @see wp_trim_excerpt
	 *
	 * @param string  $text           Text to trim.
	 * @param integer $excerpt_length Number word to trim.
	 *
	 * @return mixed|string
	 */
	function trim_excerpt( $text, $excerpt_length = null ) {
		$text = strip_shortcodes( $text );
		/** This filter is documented in wp-includes/post-template.php */
		$text = apply_filters( 'the_content', $text );
		$text = str_replace( ']]>', ']]&gt;', $text );

		if ( ! $excerpt_length ) {
			/**
			 * Filters the number of words in an excerpt.
			 *
			 * @since 2.7.0
			 *
			 * @param int $number The number of words. Default 55.
			 */
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
		}
		$more_text    = ' &hellip;';
		$excerpt_more = apply_filters( 'excerpt_more', $more_text );

		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

		return $text;
	}

	function product__description() {
		echo '<div class="woocommerce-loop-product__desc">';

		if ( 'excerpt' == $this->configs['excerpt_type'] ) {
			the_excerpt();
		} elseif ( 'more' == $this->configs['excerpt_type'] ) {
			the_content( '', true );
		} elseif ( 'content' == $this->configs['excerpt_type'] ) {
			the_content( '', false );
		} else {
			$text = '';
			global $post;
			if ( $post ) {
				if ( $post->post_excerpt ) {
					$text = $post->post_excerpt;
				} else {
					$text = $post->post_content;
				}
			}
			$excerpt = $this->trim_excerpt( $text, $this->configs['excerpt_length'] );
			if ( $excerpt ) {
				// WPCS: XSS OK.
				echo apply_filters( 'the_excerpt', $excerpt );
			} else {
				the_excerpt();
			}
		}

		echo '</div>';

	}

	function product__price() {
		woocommerce_template_loop_price();
	}

	function product__rating() {
		woocommerce_template_loop_rating();
	}

	function product__category() {
		global $post;

		$tax = 'product_cat';
		$num = 1;

		$terms = get_the_terms( $post, $tax );

		if ( is_wp_error( $terms ) ) {
			return $terms;
		}

		if ( empty( $terms ) ) {
			return false;
		}

		$links = array();

		foreach ( $terms as $term ) {
			$link = get_term_link( $term, $tax );
			if ( is_wp_error( $link ) ) {
				return $link;
			}
			$links[] = '<a class="text-xsmall link-meta" href="' . esc_url( $link ) . '" rel="tag">' . esc_html( $term->name ) . '</a>';
		}

		$categories_list = array_slice( $links, 0, $num );

		echo join( ' ', $categories_list );
	}

	function product__add_to_cart() {
		woocommerce_template_loop_add_to_cart();
	}

}

new Tophive_WC_Catalog_Designer();

<?php
/**
 *
 * @Tophive megamenu builder loader
 * @package wordpress
 * @subpackage Masterclass
 **
 *
 *
 */
class TophiveMegaMenuLoader
{
	
	function __construct()
	{	
		add_action( 'admin_enqueue_scripts', array( $this, 'loadMegaMenuAssets') );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'loadMMBuilderTemplate' ) );

		add_action( 'customize_register', array( $this, 'tophive_mega_menu_settings' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @param string $handle Script name
	 * @param string $src Script url
	 * @param array $deps (optional) Array of script names on which this script depends
	 * @param string|bool $ver (optional) Script version (used for cache busting), set to null to disable
	 * @param bool $in_footer (optional) Whether to enqueue the script before </head> or before </body>
	 */
	public function loadMegaMenuAssets() {
		wp_enqueue_script( 
			'tophive-mm-scripts', 
			esc_url( get_template_directory_uri() ) . '/inc/customizer/megamenu/assets/scripts/scripts.js',
			array( 'jquery' ), 
			false,
			false 
		);
		wp_enqueue_script("jquery-ui-draggable");
		wp_enqueue_style( 
			'tophive-mm-style', 
			esc_url( get_template_directory_uri() ) . '/inc/customizer/megamenu/assets/styles/style.css' , 
			false, 
			'all' 
		);
		$temp_dir = array( 'template_directory_uri' => get_template_directory_uri() );
		wp_localize_script( 'tophive-mm-scripts', 'directory_uri', $temp_dir );
	}

	public function loadMMBuilderTemplate(){
		?>

			<div class="tophive--customize-mm-builder tophive-mm-open">

				<div class="tophive--mm-builder-inner">
					<div class="tophive-mm-builder-layout">
						<div class="tophive-layout-selector">
							<a href="">Select Layout</a>

							<div class="tophive-mm-layout-examples">
								<span data-col="1">1 column</span>
								<span data-col="2">2 column</span>
								<span data-col="3">3 column</span>
								<span data-col="4">4 column</span>
								<span data-col="5">5 column</span>
								<span data-col="6">6 column</span>
							</div>
						</div>
						<div class="tophive-available-widgets" id="tophive-available-widgets"><div class="tophive-mm-widget" data-widid="1" data-widname="Links"><img src="http://demo.tophivetheme.com/wordpress/masterclass/wp-content/themes/tophive-template/inc/customizer/megamenu/images/link.png">Link</div><div class="tophive-mm-widget" data-widid="2" data-widname="Paragraph"><img src="http://demo.tophivetheme.com/wordpress/masterclass/wp-content/themes/tophive-template/inc/customizer/megamenu/images/paragraph.png">Paragraph</div><div class="drag"></div></div>
					</div>
				</div>
			</div>
		
		<?php
	}
	public function tophive_mega_menu_settings( $wp_customize ) {
	    // add a custom section
	    // $wp_customize->add_section( 'tophive_mega_menu', array(
	    //     'title' => esc_html__( 'Activate Megamenu', 'masterclass' ),
	    //     'panel' => 'nav_menus'
	    // ) );

	    // add "menu primary flex" checkbox setting
	    foreach ($this->availablemenus() as $menuid) {
	    	$wp_customize->add_setting( 'mega_menu_open_btn_' . $menuid, array(
		        'capability'        => 'edit_theme_options',
		        // 'default'           => '1',
		        'sanitize_callback' => 'tophive_sanitize_checkbox',
		    ) );

		    // add 'menu primary flex' checkbox control
		    $wp_customize->add_control( 'mega_menu_open_btn_' . $menuid , array(
		        'type' 		=> 'button',
		        'settings' => array(),
			    'priority' 	=> 0,
			    'description' => esc_html__( 'Opens Megamenu For this menu', 'masterclass' ),
			    'section' 	=> $menuid,
			    'input_attrs' => array(
			        'value' => esc_html__( 'Activate Megamenu', 'masterclass' ),
			        'class' => 'button button-primary tophive-mm-section-open',
			    ),
		    ));
	    }
	}
	public function availablemenus(){

		$menuSection = 'nav_menu';

		$menuObjects = wp_get_nav_menus();
		$menuids = array();

		foreach ($menuObjects as $key => $value) {
			$singlemenu = $value->term_id;
			array_push( $menuids, $menuSection . '[' . $singlemenu . ']' );
		}
		return $menuids;
	}
}

new TophiveMegaMenuLoader();
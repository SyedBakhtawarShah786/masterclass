<?php

class TophiveCoreCustomFonts extends TophiveCoreModulesBasics {
    /**
     * Cache fonts Just load one time
     * @var null
     */
    private $fonts = null;
    function __construct()
    {
        add_action('init', array($this, 'post_type'), 1 );

        if (is_admin()) {
            // add_action('add_meta_boxes', array($this, 'metabox'));
            add_filter('upload_mimes', array($this, 'font_mime_types'), 1, 1);
            add_action('admin_enqueue_scripts', array($this, 'admin_style'));
            add_filter('enter_title_here', array($this, 'change_title_placeholder'), 15, 2);
            add_action('save_post', array($this, 'save_metabox'));
            add_action('admin_head', array($this, 'custom_admin_head'));
        }

        add_filter('tophive/list-fonts', array($this, 'add_custom_fonts'));
        add_filter('tophive/auto-css', array($this, 'maybe_load_custom_fonts'), 20, 2);
        add_action('wp_head', array($this, 'load_for_preview'));

    }

    /**
     * Add custom font face to header admin for preview
     *
     * @return bool|string
     */
    function custom_admin_head(){
        global $post;
        if ( ! is_object( $post ) ) {
            return false;
        }

        $css = '';
        $font_files = get_post_meta( $post->ID, '_font_files', true );
        if ( ! is_array( $font_files ) ) {
            return $css;
        }
        foreach ( $font_files as $variation ) {
            $css = $this->setup_font_face( $post->post_title, $variation );
        }
        echo "\r\n<style type='text/css' id='tophive_custom_fonts'>\r\n";
        echo tophive_sanitize_filter($css);
        echo "\r\n</style>\r\n";
    }

    function load_for_preview(){
        if ( ! is_customize_preview() ) {
            return ;
        }

        $all_fonts = $this->get_all_custom_fonts();

        if ( ! empty( $all_fonts ) ) {
            $css = '';
            foreach ( $all_fonts as $font ) {
                $font_files = get_post_meta($font['id'], '_font_files', true);
                if (is_array($font_files)) {
                    foreach ($font_files as $variation) {
                        $css .= $this->setup_font_face($font['family'], $variation);
                    }
                }
            }
            if ( $css ) {
                echo "\r\n<style type='text/css' id='tophive_custom_fonts'>\r\n";
                echo tophive_sanitize_filter($css);
                echo "\r\n</style>\r\n";
            }
        }
    }

    /**
     * Setup font face
     *
     * @param $name
     * @param $variation
     * @return bool|string
     */
    function setup_font_face( $name, $variation ){
        if ( ! $name ) {
            return false;
        }

        $variation = wp_parse_args( $variation, $this->default_font_file_args() );

        $code = " @font-face { font-family: '{$name}'; ";
        if ( $variation['weight'] !== 'normal' ) {
            $code .= "font-weight: {$variation['weight']}; ";
        }
        if ( $variation['style'] !== 'normal' ) {
            $code .= "font-style: {$variation['style']}; ";
        }

        $src = array();
        $eot = '';

        foreach ( $variation as $ext => $info ) {
            $url = $this->get_font_file_url( $info );
            if ( $url ) {
                switch ($ext) {
                    case  'eot':
                        $src[$ext] = "url('{$url}') format('embedded-opentype')";
                        $eot = "src: url('{$url}');";
                        break;
                    case  'woff':
                        $src[$ext] = "url('{$url}') format('woff')";
                        break;
                    case  'woff2':
                        $src[$ext] = "url('{$url}') format('woff2')";
                        break;
                    case  'ttf':
                        $src[$ext] = "url('{$url}') format('truetype')";
                        break;
                    case  'svg':
                        $src[$ext] = "url('{$url}#{$name}') format('svg')";
                        break;
                }
            }
        }

        $code .= $eot;
        if ( ! empty( $src ) ){
            $src = join(', ', $src ).';';
            $code .= 'src: '. $src;
        } else { // if not have any upload files
            return '';
        }

        $code .= ' }';

        return $code;
    }

    /**
     * Setup font face for custom font
     * Each font may have more than 1 font face
     *
     * @param $font
     * @return string
     */
    function setup_font_faces( $font ){
        $css = '';
        $font_files = get_post_meta( $font['id'], '_font_files', true );
        if ( ! is_array( $font_files ) ) {
           return $css;
        }
        foreach ( $font_files as $variation ) {
            $css .=  $this->setup_font_face( $font['family'], $variation );
        }
        return $css;
    }

    /**
     * Filter to load custom font
     *
     * @param $code
     * @param $class
     * @return string
     */
    function maybe_load_custom_fonts( $code, $class ){

        $all_fonts = $this->get_all_custom_fonts();
        $font_faces = '';
        // load all custom font on customize preview
        if ( is_customize_preview() ) {
            return $code; // already load in other function
        } else {
            // just load fonts used
            if ( ! empty( $class->fonts ) ){
                foreach ( $class->fonts as $k => $font_name ) {
                    if ( isset( $all_fonts[ $font_name ] ) ) {
                        $font_faces.= $this->setup_font_faces( $all_fonts[ $font_name ] );
                        if( is_array( $class->fonts ) ) {
                            unset( $class->fonts[ $k ] ); // do not load google font if have same name
                        }
                    }
                }
            }

            // Maybe default have custom font
            if ( ! empty( $class->custom_fonts ) ){
                foreach ( $class->custom_fonts as $k => $font_name ) {
                    if ( isset( $all_fonts[ $font_name ] ) ) {
                        $font_faces.= $this->setup_font_faces( $all_fonts[ $font_name ] );
                        if( is_array( $class->custom_fonts ) ) {
                            unset( $class->custom_fonts[ $k ] ); // do not load google font if have same name
                        }
                    }
                }
            }

        }

        if ( $font_faces ) {
            $code = "/*Custom Fonts*/\r\n{$font_faces}\r\n".$code;
        }

        return $code;
    }

    /**
     * Get all custom font from DB
     *
     * @return array|null
     */
    function get_all_custom_fonts(){
        if ( ! is_null( $this->fonts ) ) {
            return $this->fonts;
        }

        $args = array(
            'post_type'       => 'font',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'asc'
        );

        $posts = get_posts( $args );
        $custom_fonts = array();
        if ( ! empty( $posts ) ) {
            foreach ($posts as $p) {
                if ($p->post_title) {
                    $custom_fonts[$p->post_title] = array(
                        'family' => $p->post_title,
                        'category' => '',
                        'id' => $p->ID,
                    );
                }
            }
        }

        $this->fonts = $custom_fonts; // cache when possible

        return $custom_fonts;
    }

    function add_custom_fonts( $fonts ){
        $custom_fonts = $this->get_all_custom_fonts();
        if ( ! empty( $custom_fonts ) ) {

            $new_fonts['tophive_fonts'] = array(
                'title' => __( 'Custom Fonts', 'masterclass' ),
                'fonts' => $custom_fonts,
            );

            foreach ( $fonts as $k => $f ) {
                $new_fonts[ $k ] = $f;
            }

            return $new_fonts;
        } else {
            return $fonts;
        }

    }

    /**
     * Save Font settings
     *
     * @param $post_id
     */
    function save_metabox( $post_id ){
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['tophive_font_nonce'] ) ) {
            return $post_id;
        }

        $nonce = $_POST['tophive_font_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'tophive_font_nonce' ) ) {
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
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Sanitize the user input.
        $font_face = $_POST['font_face'];
        $data = array();
        if ( ! is_array( $font_face ) ) {
            return ;
        }

        if ( ! is_array( $font_face['font_weight'] ) ){
            return ;
        }

        foreach ( $font_face['font_weight'] as $i => $weight ) {
            $style = $font_face['font_style'][ $i ];

            $woff_id = $font_face['woff_id'][ $i ];
            $woff2_id = $font_face['woff2_id'][ $i ];
            $ttf_id = $font_face['ttf_id'][ $i ];
            $svg_id = $font_face['svg_id'][ $i ];
            $eot_id = $font_face['eot_id'][ $i ];

            $woff_url = $font_face['woff_url'][ $i ];
            $woff2_url = $font_face['woff2_url'][ $i ];
            $ttf_url = $font_face['ttf_url'][ $i ];
            $svg_url = $font_face['svg_url'][ $i ];
            $eot_url = $font_face['eot_url'][ $i ];

            $data[ $i ] = array(
                'weight'    => $weight,
                'style'     => $style,
                'category'     => '',
                'woff' => array(
                    'id' => $woff_id,
                    'url' => $woff_url,
                ),
                'woff2' => array(
                    'id' => $woff2_id,
                    'url' => $woff2_url,
                ),
                'ttf' => array(
                    'id' => $ttf_id,
                    'url' => $ttf_url,
                ),
                'svg' => array(
                    'id' => $svg_id,
                    'url' => $svg_url,
                ),
                'eot' => array(
                    'id' => $eot_id,
                    'url' => $eot_url,
                ),

            );
        }

        // Update the meta field.
        update_post_meta( $post_id, '_font_files', $data );
    }

    /**
     * Change enter title placeholder
     *
     * @param $title
     * @param $post
     * @return string
     */
    function change_title_placeholder( $title, $post ){
        if ( get_post_type( $post ) == 'font' ) {
            return __( 'Enter Font Family', 'masterclass' );
        }
        return $title;
    }

    /**
     * Load Scripts
     *
     * @param $hook
     */
    function admin_style($hook) {
        if( $hook != 'post-new.php'  && $hook != 'post.php' && $hook != 'edit.php'  ) {
            return;
        }
        wp_enqueue_style( 'tophive-custom-fonts', $this->get_url().'/assets/css/style.css' );
        wp_enqueue_media();
        wp_enqueue_script( 'tophive-custom-fonts', $this->get_url().'/assets/js/script.js', array( 'jquery' ) );
        wp_localize_script( 'tophive-custom-fonts', 'Tophive_Fonts_Settings', $this->get_font_exts() );
    }

    /**
     * Allow upload font files
     *
     * @param $mime_types
     * @return mixed
     */
    function font_mime_types($mime_types){

        foreach ( $this->get_font_exts() as $ext => $info ) {
            $mime_types[ $ext ] = $info['mime'];
        }
        return $mime_types;
    }

    /**
     * Register a font post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    function post_type() {

        $labels = array(
            'name'               => _x( 'Custom Fonts', 'post type general name', 'masterclass' ),
            'singular_name'      => _x( 'Font', 'post type singular name', 'masterclass' ),
            'menu_name'          => _x( 'Fonts', 'admin menu', 'masterclass' ),
            'name_admin_bar'     => _x( 'Font', 'add new on admin bar', 'masterclass' ),
            'add_new'            => _x( 'Add New', 'Font', 'masterclass' ),
            'add_new_item'       => __( 'Add New Font', 'masterclass' ),
            'new_item'           => __( 'New Font', 'masterclass' ),
            'edit_item'          => __( 'Edit Font', 'masterclass' ),
            'view_item'          => __( 'View Font', 'masterclass' ),
            'all_items'          => __( 'Custom Fonts', 'masterclass' ),
            'search_items'       => __( 'Search Fonts', 'masterclass' ),
            'parent_item_colon'  => __( 'Parent Font:', 'masterclass' ),
            'not_found'          => __( 'No Font found.', 'masterclass' ),
            'not_found_in_trash' => __( 'No Font found in trash.', 'masterclass' )
        );


        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => 'themes.php',
            'show_in_nav_menus'  => false,
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'exclude_from_search' => true,
            'menu_position'      => null,
            //'_builtin'           => true,
            'supports'           => array( 'title' )
        );

        // register_post_type( 'font', $args );
    }

    /**
     * Add Meta box
     */
    // function metabox(){
    //     add_meta_box( 'font-div', __( 'Font Files', 'masterclass' ), array( $this, 'metabox_cb' ), 'font' );
    //     add_meta_box( 'font-helps', __( 'Helps', 'masterclass' ), array( $this, 'metabox_helps' ), 'font', 'side', 'low' );
    //     remove_meta_box( 'slugdiv', array( 'font' ), 'normal' );
    // }

    function metabox_helps(){
        ?>
        <p class="description">
            <?php _e( 'After adding fonts, you can go to Customizer -> Typography and the new fonts will appear in the Fonts selection dropdown list.', 'masterclass' ); ?>
        </p>
        <?php
    }

    /**
     * Get support custom font file types
     *
     * @return array
     */
    function get_font_exts(){
        $exts = array(
            'woff' => array(
                'title' => __('WOFF File', 'masterclass'),
                'modal' => __('Upload .WOFF File', 'masterclass'),
                'placeholder' => __('The Web Open Font Format, Used by Modern Browsers', 'masterclass'),
                'mime' => 'application/woff',
            ),
            'woff2' => array(
                'title' => __('WOFF 2 File', 'masterclass'),
                'modal' => __('upload .WOFF2 File', 'masterclass'),
                'placeholder' => __('The Web Open Font Format 2, Used by Super Modern Browsers', 'masterclass'),
                'mime' => 'application/woff2',
            ),
            'ttf' => array(
                'title' => __('TTF File', 'masterclass'),
                'modal' => __('Upload .TTF File', 'masterclass'),
                'mime' => 'application/x-font-ttf',
                'placeholder' => __('TrueType Fonts, Used for better supporting Safari, Android, iOS', 'masterclass'),
            ),
            'svg' => array(
                'title' => __('SVG File', 'masterclass'),
                'modal' => __('Upload .SVG File', 'masterclass'),
                'placeholder' => __('SVG fonts allow SVG to be used as glyphs when displaying text, Used by Legacy iOS', 'masterclass'),
                'mime' => 'image/svg+xml',
            ),
            'eot' => array(
                'title' => __('EOT File', 'masterclass'),
                'modal' => __('Upload .EOT File', 'masterclass'),
                'placeholder' => __('Embedded OpenType, Used by IE6-IE9 Browsers', 'masterclass'),
                'mime' => 'application/vnd.ms-fontobject',
            ),
        );

        return $exts;
    }

    /**
     * Get font file url
     *
     * @param $args
     * @return array|bool|false|int|string
     */
    function get_font_file_url( $args ) {
        $url = false;
        if ( is_array( $args ) ) {
            $args = wp_parse_args( $args, array(
                'id' => '',
                'url' => ''
            ) );
            if( $args['id'] ) {
                $url = wp_get_attachment_url( $args['id'] );
            }

            if ( $url ) {
                $args['url'] = '';
            }

        } else if ( is_numeric( $args ) ) {
            $url = wp_get_attachment_url( $args );
        }

        if ( ! $url ) {
            $url = $args;
        }

        if ( ! is_string( $url ) ) {
            $url = false;
        }

        return $url;
    }

    function default_font_file_args( ){
        $args = array(
            'weight'    => '',
            'style'     => '',
            'category'     => '',
            'woff' => array(
                'id' => '',
                'url' => '',
            ),
            'woff2' => array(
                'id' => '',
                'url' => '',
            ),
            'ttf' => array(
                'id' => '',
                'url' => '',
            ),
            'svg' => array(
                'id' => '',
                'url' => '',
            ),
            'eot' => array(
                'id' => '',
                'url' => '',
            ),
        );
        return $args;
    }

    /**
     * Meta box display
     *
     * @param $post
     */
    function metabox_cb( $post ){

        $font_weights = array(
            'normal' => __( 'Normal' , 'masterclass' ),
            'bold' => __( 'Bold' , 'masterclass' ),
        );
        for( $i = 1; $i <= 9 ; $i ++ ) {
            $font_weights[ $i*100 ] = $i*100;
        }

        $font_styles = array(
            'normal' => __( 'Normal', 'masterclass' ),
            'italic' => __( 'Italic' , 'masterclass' ),
            'oblique' => __( 'Oblique', 'masterclass' ),
        );

        $mime_types = $this->font_mime_types( array() );

        wp_nonce_field( 'tophive_font_nonce', 'tophive_font_nonce' );

        $font_files = get_post_meta( $post->ID, '_font_files', true );
        $default_font_file_args = $this->default_font_file_args();

        if ( ! is_array( $font_files ) || empty( $font_files ) ) {
            $font_files = array();
            $font_files[0] = $default_font_file_args;
        }

        ?>
        <div class="list-fonts">

            <?php foreach ( $font_files as $index => $font ) {
                $font = wp_parse_args( $font, $default_font_file_args );
                if ( $post->post_title ) {
                    $style = "font-family: \"{$post->post_title}\"; font-weight: {$font['weight']}; font-style: {$font['style']};";
                } else {
                    $style = '';
                }

                ?>
            <div class="font-file-group <?php echo tophive_sanitize_filter($index) > 1 ? 'close' : '' ; ?>">
                <div class="font-file-header">
                    <label><?php _e( 'Font Weight', 'masterclass' ); ?></label>
                    <select name="font_face[font_weight][]" >
                        <?php
                        foreach ( $font_weights as $k=> $l ) {
                            echo "<option ".selected( $font['weight'], $k, false )." value='{$k}'>{$l}</option>";
                        }
                        ?>
                    </select>
                    <label><?php _e( 'Style', 'masterclass' ); ?></label>
                    <select name="font_face[font_style][]">
                        <?php
                        foreach ( $font_styles as $k=> $l ) {
                            echo "<option ".selected( $font['style'], $k, false )." value='{$k}'>{$l}</option>";
                        } ?>
                    </select>
                    <div class="preview-text" style="<?php echo esc_attr( $style ); ?>"><?php _ex( 'I watched the storm, so beautiful yet terrific.', 'custom font preview', 'masterclass' ); ?></div>
                    <div class="font-actions">
                        <a class="font-edit" href="#"><span class="dashicons dashicons-edit"></span></span><?php _e( 'Edit', 'masterclass' ); ?></a>
                        <a class="font-close" href="#"><span class="dashicons dashicons-no-alt"></span><?php _e( 'Close', 'masterclass' ); ?></a>
                        <a class="font-remove" href="#"><span class="dashicons dashicons-trash"></span><?php _e( 'Remove', 'masterclass' ); ?></a>
                    </div>
                </div>
                <div class="font-file-body">
                    <?php
                    foreach ( $this->get_font_exts() as $ext => $info ) {

                        $mime = isset( $mime_types[ $ext ] ) ? $mime_types[ $ext ] : '';
                        $value = isset( $font[ $ext ] ) ? $font[ $ext ] : array();
                        $value = wp_parse_args( $value, array(
                            'id' => '',
                            'url' => ''
                        ) );
                        $url = $this->get_font_file_url( $value );
                        ?>
                    <div class="font-file-field">
                        <div class="font-file-label"><?php echo tophive_sanitize_filter($info['title']); ?></div>
                        <div class="font-file-input">
                            <input type="text" placeholder="<?php echo esc_attr( $info['placeholder'] ); ?>" class="attachment-url" name="font_face[<?php echo esc_attr( $ext ); ?>_url][]" value="<?php echo esc_attr( $url ); ?>">
                            <input type="hidden" name="font_face[<?php echo esc_attr( $ext ); ?>_id][]" class="attachment-id" value="<?php echo esc_attr( $value['id'] ); ?>">
                        </div>
                        <div class="font-file-button">
                            <button data-mime="<?php echo esc_attr( $mime ); ?>" class="button button-secondary font-upload-btn" type="button"><?php _e( 'Upload', 'masterclass' ); ?></button>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div><!-- /.font-file-group -->
            <?php } ?>

        </div>

        <input type="button" class="button button-secondary font-add-variation" value="<?php esc_attr_e( 'Add Font Variation', 'masterclass' ); ?>">
        <?php
    }

}
new TophiveCoreCustomFonts();
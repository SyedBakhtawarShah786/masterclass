<?php

class TophiveCore_Blog_Posts_Layout extends Tophive_Posts_Layout {

    public $parts = array(
        'media',
        'header',
        'body',
        'footer',
    );

    public $entry_class = '';

    function set_args( $config = null ){
        parent::set_args( $config );
        if ( ! is_array( $config ) ) {
            $config = array();
        }
        foreach( $this->parts as $part ) {
            $key = $part.'_fields';
            $fields = top_hive()->get_setting( $this->customizer_args['prefix'].'_dnp_'.$part );
            $this->args[ $key ] =  is_array( $fields )  ? $fields : array();
        }

        $this->args['columns'] =  top_hive()->get_setting( $this->customizer_args['prefix'].'_columns', 'all' );

        Tophive_Post_Entry()->config['term_sep'] =  top_hive()->get_setting( $this->customizer_args['prefix'].'_term_sep' );
        Tophive_Post_Entry()->config['term_count'] = absint( top_hive()->get_setting( $this->customizer_args['prefix'].'_term_count' ) );
        Tophive_Post_Entry()->config['tax'] =  top_hive()->get_setting( $this->customizer_args['prefix'].'_taxonomy' );
        Tophive_Post_Entry()->config['title_tag'] =  top_hive()->get_setting( $this->customizer_args['prefix'].'_title_tag' );
        Tophive_Post_Entry()->config['title_link'] =  top_hive()->get_setting( $this->customizer_args['prefix'].'_title_link' );


        if ( empty( Tophive_Post_Entry()->config['tax'] ) ) {
            Tophive_Post_Entry()->config['tax'] = 'category';
        }
        if(  $this->args['layout'] == 'blog_lateral' ) {
            $this->args['columns'] = 1;
        }

        $this->entry_class = top_hive()->get_setting( $this->customizer_args['prefix'].'_media_ca' ); //

    }

    function layout( $post = null ){
        $media_fields = array(
            array(
                '_key' => 'thumbnail'
            ),
        );
        if ( $this->args['media_hide'] ) {
            $show_media = false;
        } else {
            $show_media = true;
            if ( ! has_post_thumbnail( $post ) ) {
                if ( $this->args['hide_thumb_if_empty'] ) {
                    $show_media = false;
                }
            }
        }

        switch( $this->args['layout'] ) {
            case 'blog_column':
            case 'blog_timeline':
            case 'blog_masonry':
                $this->item_part( 'header', $post );
                if ( $show_media ) {
                    ?>
                    <div class="entry-article-part entry-media">
                        <a class="entry-media-link " href="<?php echo esc_url( get_permalink( $post ) ); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"></a>
                        <?php
                        Tophive_Post_Entry()->build_fields( $media_fields, $post );
                        $this->item_part('media', $post, 'media-content-inner');
                        ?>
                    </div>
                <?php } ?>
                <div class="entry-content-data">
                    <?php
                    $this->item_part( 'body', $post );
                    $this->item_part( 'footer', $post );
                    ?>
                </div>
                <?php
            break;
            default:
                if ( $show_media ) {
                ?>
                <div class="entry-media">
                    <a class="entry-media-link " href="<?php echo esc_url( get_permalink( $post ) ); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"></a>
                    <?php
                    Tophive_Post_Entry()->build_fields( $media_fields, $post );
                    $this->item_part( 'media', $post, 'media-content-inner' );
                    ?>
                </div>
                    <?php } ?>
                <div class="entry-content-data">
                    <?php
                    $this->item_part( 'header', $post );
                    $this->item_part( 'body', $post );
                    $this->item_part( 'footer', $post );
                    ?>
                </div>
                <?php

        }

    }

}
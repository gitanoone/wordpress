<?php
Class custom_search extends WP_Widget {
    public function __construct() {
        $widget_ops = [
            'classname'   => 'widget_custom_search',
            'description' => esc_html__( "A search plugin search form for your site." )
        ];
        parent::__construct( 'custom_search', _x( 'Custom search', 'My custom search widget' ), $widget_ops );
    }
    public function widget( $args, $instance ) {
        wp_enqueue_script( 'ajax-search','/wp-content/plugins/searchplugin/js/ajax-search.js',['jquery'] );
        wp_localize_script('ajax-search', 'myajax', array(
            'url' => admin_url( 'admin-ajax.php' )
        ));
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        echo $args['before_widget'];
        if ( $title ) {
            ?>
            <label>Title:
                <input type="text" name="title" id="title" onkeyup="search()"/>
            </label>
            <label>Date for:
                <input type="date" name="date" id="date" onchange="search()"/>
            </label>
            <input type="hidden" value="<?php echo $instance['NumberOfPosts'] ?>" name="num" id="num"/>
            <div id="datafetch"></div>
            <?php
            echo $args['after_widget'];
        }
    }
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, ['title' => ''] );
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        $NumberOfPosts = ! empty( $instance['NumberOfPosts'] ) ? $instance['NumberOfPosts'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
            <label for="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:','text_domain' ); ?></label>
            <input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'title' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php  esc_attr_e( $title ); ?>">
        </p>
        <p>
            <label for="<?php esc_attr_e( $this->get_field_id( 'NumberOfPosts' ) ); ?>"><?php esc_attr_e( 'Number of posts:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'NumberOfPosts' ) ); ?>" name="<?php esc_attr_e( $this->get_field_name( 'NumberOfPosts' ) ); ?>" type="number" value="<?php esc_attr_e( $NumberOfPosts ); ?>">
        </p>
        <?php
    }
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args( (array) $new_instance, ['title' => ''] );
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['NumberOfPosts'] = ( !empty( $new_instance['NumberOfPosts'] ) ) ? sanitize_text_field( $new_instance['NumberOfPosts'] ) : '';
        return $instance;
    }
}
add_action( 'widgets_init','register_custom' );
function register_custom(){
    register_widget( 'custom_search' );
}
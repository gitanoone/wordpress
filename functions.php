<?php
/*
 * Plugin Name: Event
 * Author:      Romanov
 * Version:     1.0
 */
add_action('init', 'post_type_event');

function post_type_event(){
    $args=[
        'labels'=>[
                'name'              => _x('Events','post type general name'),
                'singular_name'     => _x('Event','post type singular name'),
        ],
        'hierarchical'  => true,
        'public'        => true,
        'has_archive'   => true,
        'supports'      => ['title','editor','thumbnail']
    ];
    register_post_type('Event',$args);
}

add_action( 'init', 'taxonomy_event' );

function taxonomy_event(){
    $labels = [
        'name'              => _x( 'Types', 'taxonomy general name' ),
        'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
    ];
    $args = [
        'labels'        => $labels,
        'show_ui'       => true,
        'query_var'     => true,
        'public'        => true,
        'hierarchical'  => true,
        'default_term'  => ['name' => 'Event']
    ];
    register_taxonomy('event-types', 'event', $args);
}

add_action( 'add_meta_boxes', 'wporg_add_custom_box' );

function wporg_add_custom_box() {
    add_meta_box(
             'wporg_box_id',
           'Invite',
         'wporg_custom_box_html',
          'event'
    );
}
function wporg_custom_box_html( $post ) {
    $value = get_post_meta( $post->ID, 'wporg_field_status', true );
    ?>
    <label for="wporg_field_status">Free or invite?</label>
    <div class="widefat">
        <label>Status:<select name = "wporg_field_status" required>
                <option name="wporg_field_free" value="wporg_field_free" <?php selected ( $value, 'wporg_field_free' )?>>Free</option>
                <option name="wporg_field_invite" value="wporg_field_invite" <?php selected ( $value, 'wporg_field_invite' )?>>Invite</option>
                      </select>
        </label>
        <p>
            <label for="wporg_field_date">Date event:</label>
            <input id="wporg_field_date" type="date" name="wporg_field_date" value = "<?php esc_attr_e( get_post_meta( get_the_ID(), 'wporg_field_date', true ) ); ?>">
        </p>
    </div>
    <?php
}

add_action( 'save_post', 'hcf_save_meta_box' );

function hcf_save_meta_box( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision( $post_id ) ) {
        $post_id = $parent_id;
    }
    $fields = [
        'wporg_field_status',
        'wporg_field_free',
        'wporg_field_invite',
        'wporg_field_date',
    ];
    foreach ( $fields as $field ) {
        if ( array_key_exists( $field, $_POST ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }
}

add_action( 'widgets_init', 'register_Event_widget' );

class Event_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'event_widget',
            esc_html__( 'About event', 'text_domain' ),
            [ 'description' => esc_html__( 'Widget about event', 'text_domain' ), ]
        );
    }
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['NumberOfEvents'] ) ) {
            $events = get_posts( [
                'posts_per_page'   => $instance['NumberOfEvents'],
                'category'         => 0,
                'orderby'          => 'meta_value',
                'order'            => 'ASC',
                'include'          => [],
                'exclude'          => [],
                'meta_key'         => 'wporg_field_date',
                'meta_value'       => $instance['wporg_field_date'],
                'post_type'        => 'event',
                'suppress_filters' => true,
            ] );
            foreach( $events as $event ){
                ?>
                <ul>
                    <li><strong><?php esc_attr_e( $event->post_title); ?></strong></li>
                    <li><strong>Date event:</strong><?php  esc_attr_e( get_post_meta ( $event->ID, 'wporg_field_date', true ) ); ?></li>
                </ul>
                <?php
            }
            wp_reset_postdata();
        }
        echo $args['after_widget'];
    }
    public function form( $instance ) {
        $status = ! empty( $instance['status'] ) ? $instance['status'] : esc_html__( '', 'text_domain' );
        $NumberOfEvents = ! empty( $instance['NumberOfEvents'] ) ? $instance['NumberOfEvents'] : esc_html__( '', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'status' ) ); ?>"><?php esc_attr_e( 'Status:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'status' ) ); ?>" name="<?php _e ( esc_attr( $this->get_field_name( 'status' ) ) ); ?>" type="text" value="<?php _e( esc_attr( $status ) ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'NumberOfEvents' ) ); ?>"><?php esc_attr_e( 'Number of events:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'NumberOfEvents' ) ); ?>" name="<?php _e ( esc_attr( $this->get_field_name( 'NumberOfEvents' ) ) ); ?>" type="number" value="<?php _e ( esc_attr( $NumberOfEvents ) ); ?>">
        </p>
        <?php
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['status'] = ( ! empty( $new_instance['status'] ) ) ? sanitize_text_field( $new_instance['status'] ) : '';
        $instance['NumberOfEvents'] = ( ! empty( $new_instance['NumberOfEvents'] ) ) ? sanitize_text_field( $new_instance['NumberOfEvents'] ) : '';
        return $instance;
    }
}
function register_Event_widget() {
    register_widget( 'Event_Widget' );
}

add_shortcode( 'event', 'event_shortcode' );

function event_shortcode( $args ) {
    $param = shortcode_atts( [
        'status'=> 'wporg_field_invite',
        'NumberOfEvents' => 5,
    ], $args );
    event_show( $param );
}
function event_show( $param ){
    $args =[
        'post_type'         => 'event',
        'posts_per_page'    => $param['NumberOfEvents'],
        'meta_query'        => [
       ['key'               => 'wporg_field_date',]
        ]
    ];
    $params=new WP_Query( $args );
    while ( $params->have_posts() ) {
        $params->the_post();
        _e('<ul>' . '<li>');
        the_title();
        _e('<br>' . esc_attr( get_post_meta( get_the_ID(), 'wporg_field_date', true ) ) . '<br>' . '</li>' . '</ul>');
        wp_reset_postdata();
    }
}
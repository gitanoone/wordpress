<?php
/**
* Plugin Name: Search
* Author:      Romanov
* Version:     1.0
*/
require (plugin_dir_path( __FILE__ ) . '/search-widget.php');
add_action( 'wp_ajax_data_fetch', 'data_fetch' );
add_action( 'wp_ajax_nopriv_data_fetch', 'data_fetch' );
function data_fetch(){
    $args_title=[
            'posts_per_page' => $_POST['num'],
            's'              => esc_attr( $_POST['title'] ),
            'post_type'      => 'post',
            'oderby'         => 'date',
            'order'          => 'DESC',
    ];
    $args_date=[
            'posts_per_page' => $_POST['num'],
            's'              => esc_attr( $_POST['title'] ),
            'post_type'      => 'post',
            'oderby'         => 'date',
            'order'          => 'DESC',
            'date_query'     => [
            [
                'after'      => $_POST['date'],
                'inclusive'  => true,
            ],
        ],
    ];
        $args=(!empty($_POST['date']))? $args_date : $args_title;
        $query = new WP_Query($args);
        if( $query->have_posts() ) :
            while( $query->have_posts() ): $query->the_post(); ?>
                <ul>
                    <li><a href="<?php esc_attr_e( get_permalink() ); ?>"><?php esc_attr_e( the_title() ); ?></a></li>
                    <li><p <?php esc_attr_e( get_permalink() )?>"><?php esc_attr_e( the_date() ); ?></p></li>
                </ul>
                <?php
        endwhile;
        wp_reset_postdata();
        else : ?><h4>Not found</h4>
        <?php
        endif;
        die();
}

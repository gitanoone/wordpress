<?php
/**
 * Plugin Name: Google map api
 * Author: Romanov
 * Version: Version 1.0
 */
add_action( 'init', 'cpt_location' );
function cpt_location() {
    $labels = array(
        'name'          => __( 'Shops', 'text_domain' ),
        'singular_name' => __( 'Shop', 'text_domain' ),
    );
    $args = array(
        'label'         => __( 'Shops', 'text_domain' ),
        'labels'        => $labels,
        'hierarchical'  => true,
        'public'        => true,
        'has_archive'   => true,
        'supports'      => ['title','editor','thumbnail']
    );
    register_post_type( "Shop", $args );
}
add_action( 'add_meta_boxes', 'add_embed_gmaps_meta_box' );
function add_embed_gmaps_meta_box() {
    add_meta_box(
        'show_map',
        'Address',
        'show_map_meta_box',
        'Shop',
        'normal',
        'high');
}
function curl($address){
    $apiKey = 'AIzaSyCNlHU-mVsIvYTw4cvzmYPAH4ozWHvqBWQ';
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address.';' . "&key=" . $apiKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);
    $result = curl_exec($ch);
    curl_close($ch);
    $geo = file_get_contents($url);
    $geo = json_decode($geo, true);
    wp_enqueue_script( 'custom','/wp-content/plugins/googlemaps/js/custom.js' );
    if ( $geo['status'] == 'OK' )
    $helper = [
        $geo['results'][0]['geometry']['location']['lat'],
        $geo['results'][0]['geometry']['location']['lng'],
        $address
        ];
    wp_localize_script( 'custom','helper',$helper );
    return json_encode($result, JSON_PRETTY_PRINT);
}
function show_map_meta_box() {
    ?>
    <div class="maparea" id="map-canvas"></div>
    <label for="position">
        <input type="text" name="position" id="position" placeholder="Enter location" value = "<?php esc_attr_e( get_post_meta( get_the_ID(), 'position', true ) );?>">
    </label>
    <?php
}
add_action( 'save_post', 'save_embed_gmap' );
function save_embed_gmap($post_id)
{
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( $parent_id = wp_is_post_revision($post_id) ) {
        $post_id = $parent_id;
    }
    $fields = [
        'position',
    ];
    foreach ( $fields as $field ) {
        if ( array_key_exists($field, $_POST) ) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action( "wp_enqueue_scripts", "do_scripts");
function do_scripts()
{
    global $post;
    $ID = $post->ID;
    wp_register_script('custom', '/wp-content/plugins/googlemaps/js/custom.js');
    $script_vars = [
        'title'    => get_the_title( $ID ),
        'post'     => get_post( $ID )
    ];
    wp_localize_script('custom', 'script_vars', $script_vars);
}
add_shortcode('shops','MapForShop');
function MapForShop(){
    global $post;
    $ID = $post->ID;
    $address=get_post_meta($ID,'position');
    curl($address[0]);
    do_scripts();
}
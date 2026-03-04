<?php
//Remove JQuery migrate
 function remove_jquery_migrate( $scripts ) {
       if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
            $script = $scripts->registered['jquery'];
       if ( $script->deps ) { 
    // Check whether the script has any dependencies

            $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
     }
    }
 }
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );


//Disable emoji
function mytheme_disable_wp_emojicons() {
    // Remove the emoji script.
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'mytheme_disable_wp_emojicons' );


// disable jquery
function mytheme_dequeue_jquery() {
    if ( ! is_admin() ) {
    //    wp_deregister_script( 'jquery' );
    //    wp_dequeue_script( 'jquery' );

        // Dequeue Gutenberg block library CSS.
   // wp_dequeue_style( 'wp-block-library' );

    // If you want to dequeue the Gutenberg theme styles.
    wp_dequeue_style( 'wp-block-library-theme' );
    }
}
add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_jquery', 100 );

//disable feeds
function mytheme_disable_feeds() {
    wp_die( __( 'No feed available. Please visit the homepage!', 'mytheme' ) );
}
add_action( 'do_feed', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_rdf', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_rss', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_rss2', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_atom', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_rss2_comments', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_atom_comments', 'mytheme_disable_feeds', 1 );

remove_action( 'wp_head', 'feed_links_extra', 3 ); // Remove comment feed link
add_action( 'do_feed_rss2_comments', 'mytheme_disable_feeds', 1 );
add_action( 'do_feed_atom_comments', 'mytheme_disable_feeds', 1 );


//Hide /wp-json/wp/v2/users
function mytheme_disable_users_endpoint_access( $result ) {
    // Check if the request is for the users endpoint
    if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp/v2/users' ) !== false ) {
        // Deny access for non-authenticated users
        if ( ! is_user_logged_in() ) {
            return new WP_Error( 'rest_forbidden', __( 'You cannot view this resource.', 'mytheme' ), array( 'status' => 403 ) );
        }
    }
    return $result;
}
add_filter( 'rest_authentication_errors', 'mytheme_disable_users_endpoint_access' );

// Disable the users endpoint for unauthenticated users
function mytheme_disable_users_endpoint() {
    register_rest_route( 'wp/v2', '/users', array(
        'methods'  => 'GET',
        'callback' => function() {
            return new WP_Error( 'rest_forbidden', __( 'You cannot view this resource.', 'mytheme' ), array( 'status' => 403 ) );
        },
    ));
}
add_action( 'rest_api_init', 'mytheme_disable_users_endpoint', 10 );

//Remove WP version from meta
remove_action('wp_head', 'wp_generator');

//Remove xmlrpc
add_filter('xmlrpc_enabled', '__return_false');





/**
 * Disable comments & WooCommerce reviews completely
 */

// 1. Disable comments everywhere
add_action('init', function () {

    // Remove comment support from all post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments & pings on frontend
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

// 2. Remove WooCommerce product reviews
add_filter('woocommerce_enable_reviews', '__return_false');
add_filter('woocommerce_product_tabs', function ($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}, 98);

// 3. Disable REST comments endpoint
add_filter('rest_endpoints', function ($endpoints) {
    unset($endpoints['/wp/v2/comments']);
    return $endpoints;
});

<?php
/*
Plugin Name: YG Present
Plugin URI: yg-present.com
Version: 1.0.0
Author: Zeter Lee
Description: YG Present
 */

//if ( !function_exists( 'add_artist_custom_post_type' ) ) {
//    function add_artist_custom_post_type() {
//        register_post_type( 'artist', array(  'label' => 'Artist', 'description' => 'YG Artist', 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'capability_type' => 'post', 'hierarchical' => false, 'rewrite' => array( 'slug' => '' ), 'query_var' => true, 'exclude_from_search' => false, 'supports' => array( 'title', 'editor', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'author', 'page-attributes', ), 'labels' => array (
//            'name' => 'Artist',
//            'singular_name' => 'Artist',
//            'menu_name' => 'Artist',
//            'add_new' => 'Add Artist',
//            'add_new_item' => 'Add New Artist',
//            'edit' => 'Edit',
//            'edit_item' => 'Edit Artist',
//            'new_item' => 'New Artist',
//            'view' => 'View Artist',
//            'view_item' => 'View Artist',
//            'search_items' => 'Search Artist',
//            'not_found' => 'No Artist Found',
//            'not_found_in_trash' => 'No Artist Found in Trash',
//            'parent' => 'Parent Artist',
//        ), ) );
//
//    }
//    add_action( 'init', 'add_artist_custom_post_type' );
//}
//
//
//if ( !function_exists( 'add_ticket_custom_post_type' ) ) {
//    function add_ticket_custom_post_type() {
//        register_post_type( 'ticket', array(  'label' => 'Ticket', 'description' => 'YG Ticket', 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'capability_type' => 'post', 'hierarchical' => false, 'rewrite' => array( 'slug' => '' ), 'query_var' => true, 'exclude_from_search' => false, 'supports' => array( 'title', 'editor', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'author', 'page-attributes', ), 'labels' => array (
//            'name' => 'Ticket',
//            'singular_name' => 'Ticket',
//            'menu_name' => 'Ticket',
//            'add_new' => 'Add Ticket',
//            'add_new_item' => 'Add New Ticket',
//            'edit' => 'Edit',
//            'edit_item' => 'Edit Ticket',
//            'new_item' => 'New Ticket',
//            'view' => 'View Ticket',
//            'view_item' => 'View Ticket',
//            'search_items' => 'Search Ticket',
//            'not_found' => 'No Ticket Found',
//            'not_found_in_trash' => 'No Ticket Found in Trash',
//            'parent' => 'Parent Ticket',
//        ), ) );
//
//    }
//    add_action( 'init', 'add_ticket_custom_post_type' );
//}
//
//
//if ( !function_exists( 'add_event_custom_post_type' ) ) {
//    function add_event_custom_post_type() {
//        register_post_type( 'event', array(  'label' => 'Event', 'description' => 'YG Event', 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'capability_type' => 'post', 'hierarchical' => false, 'rewrite' => array( 'slug' => '' ), 'query_var' => true, 'exclude_from_search' => false, 'supports' => array( 'title', 'editor', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'author', 'page-attributes', ), 'labels' => array (
//            'name' => 'Event',
//            'singular_name' => 'Event',
//            'menu_name' => 'Event',
//            'add_new' => 'Add Event',
//            'add_new_item' => 'Add New Event',
//            'edit' => 'Edit',
//            'edit_item' => 'Edit Event',
//            'new_item' => 'New Event',
//            'view' => 'View Event',
//            'view_item' => 'View Event',
//            'search_items' => 'Search Event',
//            'not_found' => 'No Ticket Found',
//            'not_found_in_trash' => 'No Ticket Found in Trash',
//            'parent' => 'Parent Event',
//        ), ) );
//    }
//    add_action( 'init', 'add_event_custom_post_type' );
//}
//
//
//
///**
// *
// * How to create custom post meta boxes in WORDPRESS
// *
// */
//
//add_action( 'load-post.php', 'tour_post_meta_boxes_setup' );
//add_action( 'load-post-new.php', 'tour_post_meta_boxes_setup' );
//
//
///* Meta box setup function. */
//function tour_post_meta_boxes_setup() {
//
//    /* Add meta boxes on the 'add_meta_boxes' hook. */
//    add_action( 'add_meta_boxes', 'tour_add_post_meta_boxes' );
//
//    /* Save post meta on the 'save_post' hook. */
//    add_action( 'save_post', 'tour_save_post_class_meta', 10, 2 );
//}
//
///* Create one or more meta boxes to be displayed on the post editor screen. */
//function tour_add_post_meta_boxes(){
//    add_meta_box(
//        'tour-guest', // Unique ID
//        esc_html('Guest' , 'example'),  // Title
//        'tour_post_meta_box',  //callback function
//        'ticket',  // Admin Page or post Type
//        'advanced', // Context
//        'default' // Priority
//    );
//}
//
///* Display the post meta box. */
//function tour_post_meta_box($object, $box){ ?>
<!---->
<!--    --><?php //wp_nonce_field( basename( __FILE__ ), 'tour_post_class_nonce' ); ?>
<!---->
<!--    <p>-->
<!--        <label for="tour-guest">--><?php //_e( "Add Guest Artist", 'example' ); ?><!--</label>-->
<!--        <br />-->
<!--        <input class="widefat" type="text" name="tour-guest" id="tour-guest" value="--><?php //echo esc_attr( get_post_meta( $object->ID, 'tour-guest', true ) ); ?><!--" size="30" />-->
<!---->
<!--        <input class="widefat" type="text" name="tour-guest-id" id="tour-guest-id" value="--><?php //echo esc_attr( get_post_meta( $object->ID, 'tour-guest-id', true ) ); ?><!--" size="30" />-->
<!--    </p>-->
<!--    --><?php
//}
//
///* Save the meta box's post metadata. */
//function tour_save_post_class_meta($post_id , $post){
//
//    /* Verify the nonce before proceeding. */
//    if ( !isset( $_POST['tour_post_class_nonce'] ) || !wp_verify_nonce( $_POST['tour_post_class_nonce'], basename( __FILE__ ) ) )
//        return $post_id;
//
//    /* Get the post type object. */
//    $post_type = get_post_type_object($post->post_type);
//
//    /* Check if the current user has permission to edit the post. */
//    if( !current_user_can($post_type->cap->edit_post, $post_id) )
//        return $post_id;
//
//    /* Get the posted data and sanitize it for use as an HTML class. */
//
//    $new_meta_value = (isset($_POST['tour-guest']) ? sanitize_html_class( $_POST['tour-guest'] ) : '' );
//
//    /* Get the meta key. */
//    $meta_key = 'tour-guest';
//
//
//    /* Get the meta value of the custom field key. */
//    $meta_value = get_post_meta($post_id, $meta_key, true);
//
//    error_log($new_meta_value);
//    error_log($meta_key);
//
//    /* If a new meta value was added and there was no previous value, add it. */
//    if ( $new_meta_value && '' == $meta_value )
//        add_post_meta( $post_id, $meta_key, $new_meta_value, true );
//
//    /* If the new meta value does not match the old value, update it. */
//    elseif ( $new_meta_value && $new_meta_value != $meta_value )
//        update_post_meta( $post_id, $meta_key, $new_meta_value );
//
//    /* If there is no new meta value but an old value exists, delete it. */
//    elseif ( '' == $new_meta_value && $meta_value )
//        delete_post_meta( $post_id, $meta_key, $meta_value );
//
//
//
//    $new_meta_value = (isset($_POST['tour-guest-id']) ? sanitize_html_class( $_POST['tour-guest-id'] ) : '' );
//
//    /* Get the meta key. */
//    $meta_key = 'tour-guest-id';
//
//
//    /* Get the meta value of the custom field key. */
//    $meta_value = get_post_meta($post_id, $meta_key, true);
//
//    error_log($new_meta_value);
//    error_log($meta_key);
//
//    /* If a new meta value was added and there was no previous value, add it. */
//    if ( $new_meta_value && '' == $meta_value )
//        add_post_meta( $post_id, $meta_key, $new_meta_value, true );
//
//    /* If the new meta value does not match the old value, update it. */
//    elseif ( $new_meta_value && $new_meta_value != $meta_value )
//        update_post_meta( $post_id, $meta_key, $new_meta_value );
//
//    /* If there is no new meta value but an old value exists, delete it. */
//    elseif ( '' == $new_meta_value && $meta_value )
//        delete_post_meta( $post_id, $meta_key, $meta_value );
//
//}
//
//
//
//
//
/////* Filter the post class hook with our custom post class function. */
////add_filter( 'post_class', 'smashing_post_class' );
////
////function smashing_post_class( $classes ) {
////
////    /* Get the current post ID. */
////    $post_id = get_the_ID();
////
////    /* If we have a post ID, proceed. */
////    if ( !empty( $post_id ) ) {
////
////        /* Get the custom post class. */
////        $post_class = get_post_meta( $post_id, 'smashing_post_class', true );
////
////        /* If a post class was input, sanitize it and add it to the post class array. */
////        if ( !empty( $post_class ) )
////            $classes[] = sanitize_html_class( $post_class );
////    }
////
////    return $classes;
////}

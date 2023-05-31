<?php
/**
 *
 * Plugin Name: Samadhan Post List
 * Plugin URI: http://www.samadhan.com
 * Description: Easily manage and view your post list.
 * Version: 2.0.7
 * Author: Samadhan Consultants
 * Author URI: http://www.samadhan.com
 * Text Domain: smdn_post_list
 * samadhan plugins test

 */

include_once('includes/model/PostModel.php');
include_once('includes/view/PostViewList.php');

include_once('post-import/model/PostImportModel.php');
include_once('post-import/view/PostImortView.php');


function samadhan_post_style_loaded(){

    wp_enqueue_style('bootstrap.min.css', plugins_url('/vandor/bootstrap.min.css',__FILE__) , array(), '1.0.0', 'all');
    wp_enqueue_style('post-list', plugins_url('/apps/css/post-list.css',__FILE__) , array(), '1.0.0', 'all');

   // wp_enqueue_script('jquery.slim.min', plugins_url('/vandor/jquery.slim.min.js',__FILE__) , array('jquery'), '1.0.0', true);
    //wp_enqueue_script('bootstrap.bundle.min', plugins_url('/vandor/bootstrap.bundle.min.js',__FILE__) , array('jquery.slim.min'), '1.0.0', true);


}

add_action( 'pre_get_posts', 'wpb_custom_query' );
//function to modify default WordPress query
function wpb_custom_query( $query ) {

    if( $query->is_main_query() && ! is_admin() && $query->is_home() ) {
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
}

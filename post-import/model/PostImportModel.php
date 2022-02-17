<?php

namespace Samadhan;

class PostImportModel
{
    public function __construct()
    {
    }
    public static function save_post_data($post_data){

       global  $wpdb;

        $insert_count=0;
        $error_count=0;
        $update_count=0;

      foreach ($post_data as $data){

            // read json file
//            $data = file_get_contents($_FILES['importfile']['tmp_name']);
//            $_SESSION['data'] = $data;

         $post_parent=$data['id'];
         $post_title=$data['name'];
         $post_content=$data['longDescription'];
         $post_excerpt=$data['shortDescription'];
         $post_date=$data['genericDate'];
         $post_author_id=$data['ownerId'];

         $profileMedia1Id=$data['profileMedia1Id'];
         $profileMedia1Type=$data['profileMedia1Type'];
         $profileMedia1Description=$data['profileMedia1Description'];
         $intValue5=$data['intValue5'];

         $userImage=$data['stringValue1'];
         $image_url=$data['stringValue2'];
         $ImageTypes=$data['stringValue3'];

      //  if(!post_exists( $post_title )){


            // start post image save
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            if ( ! function_exists( 'wp_crop_image' ) ) {
                include( ABSPATH . 'wp-admin/includes/image.php' );
            }
//            $user_id=get_current_user_id();
//            $userImage = $_FILES['photoUser'];
//            $userCvImage=$userImage['name'];
//            $userSignature = $_FILES['signatuerUser'];
//            $userSignatureImage= $userSignature['name'];
//
//          $file = array(
//              'name' => $userImage,
//              'type' => $ImageTypes,
//              //'tmp_name' => $image_url,
//              'error' => 0,
//              'size' => 33254
//          );





//
//            if(!empty($file)){
//                $move_image_url = wp_handle_upload( $file, array('test_form' => true) );
//                if ( $move_image_url && !isset($move_image_url['error']) ) {
//                    $wp_upload_dir = wp_upload_dir();
//                    $attachment = array(
//                        'guid' => $wp_upload_dir['url'] . '/' . basename($move_image_url['file']),
//                        'post_mime_type' => $move_image_url['type'],
//                        'post_title' => preg_replace( '/\.[^.]+$/', '', basename($move_image_url['file']) ),
//                        'post_content' => '',
//                        'post_status' => 'inherit'
//                    );
//                    $logo_attach_id = wp_insert_attachment($attachment, $move_image_url['file']);
//                    $image_attributes = wp_get_attachment_image_src( $logo_attach_id );
//
//                    $attachment_data = wp_generate_attachment_metadata( $logo_attach_id, $move_image_url['file'] );
//                    wp_update_attachment_metadata( $logo_attach_id, $attachment_data );
//
//                }
//            }
        //end  post image save

          $arr_query_args = array(
              'numberposts'  => -1,
              'post_type'    => 'post',
              'orderby'   => 'title',
              'order'     => 'ASC',
              'post_parent'  => $post_parent
          );

          $parent_posts = get_posts( $arr_query_args );

          if (empty($parent_posts)) {

            $post_data = array(
                'post_title'    => !empty($post_title)?$post_title:'',
                'post_content'  => !empty($post_content)? $post_content: '',
                'post_excerpt'  => !empty($post_excerpt)? $post_excerpt: '',
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_parent'   => !empty($post_parent)?$post_parent:0,
             //   'post_category' => array( 8,39 ),
                'post_date' => !empty($post_date)?$post_date:'',
                'post_type' => 'post',

            );

           // Insert the post into the database.
              $post_id=wp_insert_post( $post_data );
                 if($post_id){
                     $insert_count++;
                     add_post_meta($post_id,'site-sidebar-layout','no-sidebar');
                     add_post_meta($post_id,'site-content-layout','plain-container');
                    // add_post_meta($post_id,'_thumbnail_id',4149);
                 }else{
                    // echo $post_parent."<br/>";
                     $error_count++;
                 }
          }else{
              $update_count++;
          }


    }

      if($insert_count>0) {
            $message = '<h2 style="color:green">Post Imported Successfully  ( '.$insert_count.' )</h2>';
        }
        if($error_count>0) {
            $message .= '<h2 style="color:red">Post Imported Error!! (' .$error_count.' )</h2>';
        }
        if($update_count>0) {
            $message .= '<h2 style="color:#44f313">Post Import data already Updated (' .$update_count. ') </h2>';
        }

       return $message;
    }

    public static function  delete_post_all_data(){
        global $wpdb;
        $query="DELETE p,m FROM {$wpdb->prefix}posts as p INNER JOIN {$wpdb->prefix}postmeta as m on p.ID = m.post_id WHERE p.post_type='post'";
        $delete_data=$wpdb->query($query);
//        $allposts= get_posts( array('post_type'=>'post','numberposts'=>-1) );
//        foreach ($allposts as $eachpost) {
//            wp_delete_post( $eachpost->ID, true );
//        }
        if($delete_data) {
            $message = '<h2 style="color:green">Post Delete Successfully!! </h2>';
        }else{
            $message = '<h2 style="color:green">Error !! Post Delete.</h2>';
        }
        return $message;
   }
}

new PostImportModel();
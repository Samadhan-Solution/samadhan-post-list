<?php

namespace Samadhan;

use WP_Query;

class PostViewList
{
    public function __construct(){
        samadhan_post_style_loaded();
        add_shortcode('smdn_post_list',array($this,'smdn_get_post_list_view'));
    }
    public function smdn_get_post_list_view($atts){



        global $paged;
        $date = '';

        $atts = shortcode_atts(
            array(
                'per_page' => '10',
            ), $atts, 'smdn_post_list' );

        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;


                if(!empty($_POST['sort_by']) || !empty($_POST['search_name']) || !empty($_POST['category_id'])){

                    $sort_id=$_POST['sort_by'];
                    $title=!empty($_POST['search_name']) ? $_POST['search_name']: " ";
                    $category_id=$_POST['category_id'];

                    if(!empty($sort_id)){
                        $filters['sort_by']=$sort_id;
                    }
                    if(!empty($title)){
                        $filters['search_name']=$title;
                    }
                    if(!empty($category_id)){
                        $filters['category_id']=$category_id;
                    }


                } else{


                    //if(!empty($_GET['sort_by']) || !empty($_GET['search_name']) || !empty($_GET['category_id'])){
                    $sort_id=$_GET['sort_by'];
                    $title=$_GET['search_name'];
                    $category_id= !empty($_POST['category_id']) ? $_POST['category_id']:$_GET['category_id'];
                    if(!empty($sort_id)){
                        $filters['sort_by']=$sort_id;
                    }
                    if(!empty($title)){
                        $filters['search_name']=$title;
                    }
                    if(!empty($category_id)){
                        $filters['category_id']=$category_id;
                    }
                    //}

                    }



            if(isset($_POST['search-button']) && !empty($_POST['category_id']) && $_POST['category_id']>0 ){
                $psot_category_id= $_POST['category_id'];
                $category_data= array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => array($psot_category_id),
                );

            }else{


                if(!empty($_GET['category_id']) && $_GET['category_id']>0 ){
                    $get_category_id= $_GET['category_id'];
                    $category_data= array(
                        'taxonomy' => 'category',
                        'field' => 'term_id',
                        'terms' => array($get_category_id),
                    );


                }
        }

        if(isset($_POST['search-button']) && !empty($_POST['search_name'])){


            $title=$_POST['search_name'];
            $sort_id=$_POST['sort_by'];
            $paged=1;
            $menu_order='date';
            $category=$category_data;

        }else{

            if(isset($_POST['search-button']) && empty($_POST['search_name'])){
                $title=' ';
            }elseif ((!isset($_POST['search-button']) && !empty($_GET['search_name'])) || ((!isset($_POST['search-button']) && empty($_GET['search_name'])))){
                $title=$_GET['search_name'] ;
            }else{
                $title=' ';
            }

            if(!empty($_POST['sort_by'])){
                $sort_id=$_POST['sort_by'];
            }elseif (!empty($_GET['sort_by'])){
                $sort_id=$_GET['sort_by'] ;
            }else{
                $sort_id='desc';
            }
            $menu_order='date';
            $category=!empty($category_data)?$category_data :' ';

        }



        $args = array(
            'post_type'  => array('post'),
            "s" => $title,
            'posts_per_page' => $atts['per_page'],
            'paged' => $paged,
            'suppress_filters' => true,
            'orderby' => $menu_order,
            'order'  =>$sort_id,
            'tax_query' => array($category)

        );



        $post_sort_by=array(
            'DESC'=>"Most Recent",
            "ASC"=>'OLD',
        );

        $cat_args = array(
            'orderby'    => 'name',
            'order'      => 'asc',
            'hide_empty' => false,
        );



        $post_categories = get_terms( 'category', $cat_args );

        $sort_by_options=PostModel::samadhan_get_sort_by_options($post_sort_by,$sort_id);
        $category_options=PostModel::samadhan_get_post_category_options($post_categories,$category_id);


        $post_query = new WP_Query($args);
        $postslist=$post_query->posts;

        $content='';


        $total_pages = $post_query->max_num_pages;

        $content .= $this->get_filter_html($title,$sort_by_options,$category_options);


        $count=0;
        $adds_count=0;
        if(!empty($postslist)){

         $content .= $this->samadhan_get_post_latest_item($postslist);

        foreach ($postslist as $key=>$item){


            $posts_id=$item->ID;

            if($key==0){
                continue;
            }


            if($count==5){

                $adds= PostModel::get_adds_data();
                $total_adds=count($adds);
                if($adds_count==$total_adds){
                    $adds_count=0;
                }
                foreach($adds as $keys=>$value){
                    if($keys==$adds_count && $adds_count<$total_adds){
                        $show_adds= do_shortcode('[adrotate banner="'.$value.'"]');
                    }

                }
                $content .= $this->get_adds_show($show_adds);
                $count=0;
                $adds_count++;
            }
            $content .=$this->samadhan_get_post_list_loop($item);

            $count++;

        }

        if ($total_pages > 1){

            $current_page = max(1, get_query_var('paged'));


            $content .= PostModel::samadhan_get_pagination($current_page,$filters,$total_pages);

        }
        wp_reset_postdata();
        }else{
            $content .="<h2 style='text-align:center;padding: 20px;'>Data not found!!</h2>";
        }
        return $content;

    }

    public function get_filter_html($title,$sort_by_options,$category_options){

        $postList='<form method="post" action="" style="background-color: #F1EEF5;" class="smdn-form-control filter-form">
                    <div class="container post-filter-form" style="padding-top: 40px;padding-bottom: 5px;">
                      <div class="row ">
                         <div class="col-md-5">  
                             <div class="form-group row ">
                                    <label for="search" class="col-sm-3 col-form-label smdn-title">SEARCH </label>
                                    <div class="col-sm-9 form-inline">
                                         <input class="form-control mr-sm-2 smdn-search" type="text" placeholder="SEARCH" name="search_name" aria-label="Search" value="'.$title.'">
                                         <button class="btn btn-outline-success my-2 my-sm-0 smdn-icon-search" type="submit" name="search-button"><i class="fa fa-search"> </i></button>
                                    </div>
                             </div>
                         </div>
                        
                        <div class="col-md-3">  
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-4 col-form-label smdn-title">SORT BY</label>
                            <div class="col-sm-8">
                              <select name="sort_by" class="form-control" id="smdn-form-search">
                                  '.$sort_by_options.'
                                </select>
                            </div>
                           </div>
                          </div>
                        <div class="col-md-4">
                        
                          <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label smdn-title">TOPICS</label>
                                <div class="col-sm-9">
                                  <select name="category_id" class="form-control" id="smdn-form-search">
                                       '.$category_options.'
                                    </select>
                                </div>
                              </div>
                            </div>
                             
                      </div>
                    </div></form>';
        return $postList;
    }
    public function samadhan_get_post_latest_item($item){

        // var_dump($item);

        $image=wp_get_attachment_image_src(get_post_meta( $item[0]->ID,'_thumbnail_id',true),$size = 'full', $icon = false  );

        $post_content = substr($item[0]->post_content, 0, 275);
        $post_excerpt = substr($item[0]->post_excerpt, 0, 275);
        $article_post_excerpt=!empty($post_excerpt)?$post_excerpt :$post_content;
        $postList='';
        if(!empty($image[0])){
        $postList .='<div class="container post_first ">
                  <div class="row">
                    <div class="col-md-12" style="margin-bottom: 30px;margin-top: 30px;">
                    <h2><a href="'.get_the_permalink($item[0]->ID).'" class="smdn-title">'.$item[0]->post_title.'</a></h2>
                    <p class="smdn-description">'.strip_tags($article_post_excerpt).' ....</p>
                    <h5 class="smdn-display-name">'.date('d/m/Y',strtotime($item[0]->post_date)).'</h5>
                    <img src="'.$image[0].'" alt="Los Angeles" style="height: auto; max-width: 100%;">
                    </div>
                    </div>
                    <div style="border-top: 2px solid #E4DEEE;">
                       </div>
                    </div>';
        }else{
        $postList .='<div class="container post_first ">
                  <div class="row">
                    <div class="col-md-12" style="margin-bottom: 30px;margin-top: 30px;">
                    <h2><a href="'.get_the_permalink($item[0]->ID).'" class="smdn-title">'.$item[0]->post_title.'</a></h2>
                    <p class="smdn-description">'.strip_tags($article_post_excerpt).' ....</p>
                    <h5 class="smdn-display-name">'.date('d/m/Y',strtotime($item[0]->post_date)).'</h5>
                    </div>
                    </div>
                    <div style="border-top: 2px solid #E4DEEE;">
                       </div>
                    </div>';
        }

        return $postList;


    }
    public function samadhan_get_post_list_loop($item){

        $image=wp_get_attachment_image_src(get_post_meta( $item->ID,'_thumbnail_id',true),$size = 'full', $icon = false  );
        $post_content = substr($item->post_content, 0, 275);
        $post_excerpt = substr($item->post_excerpt, 0, 275);
        $article_data=!empty($post_excerpt)?$post_excerpt :$post_content;
        $postList='';
        if(!empty($image[0])){
        $postList .= '<div class="container post_list">
                       
                        <div class="row '.$item->ID.'" style="margin-top: 3%; margin-bottom: 3%;">
                            <div class="col-md-4">
                                 <img src="' .$image[0].'" alt="Los Angeles" style="height: auto; max-width: 100%;">
                            </div>
                            <div class="col-md-4">
                            <h3><a href="'.get_the_permalink($item->ID).'" class="smdn-title">'.$item->post_title.'</a></h3>
                            <h5 class="smdn-display-name">'.date('d/m/Y',strtotime($item->post_date)).' '.get_the_author_meta('display_name', $item->post_author).'</h5>
                            </div>
                            <div class="col-md-4">
                            <p class="smdn-description">'.strip_tags($article_data).' ....</p>
                            </div>
                        </div>
                        <div style="border-top: 2px solid #E4DEEE;">
                       </div>
                   </div>';
        }else{
        $postList .= '<div class="container post_list">
                       
                        <div class="row '.$item->ID.'" style="margin-top: 3%; margin-bottom: 3%;">
                         
                            <div class="col-md-6">
                            <h3><a href="'.get_the_permalink($item->ID).'" class="smdn-title">'.$item->post_title.'</a></h3>
                            <h5 class="smdn-display-name">'.date('d/m/Y',strtotime($item->post_date)).' '.get_the_author_meta('display_name', $item->post_author).'</h5>
                            </div>
                            <div class="col-md-6">
                            <p class="smdn-description">'.strip_tags($article_data).' ....</p>
                            </div>
                        </div>
                        <div style="border-top: 2px solid #E4DEEE;">
                       </div>
                   </div>';
        }
        return $postList;

    }



    public function get_adds_show($show_adds){
        return '<div class="container post_adds">
                    <div class="row" style="margin-top: 3%; margin-bottom: 3%;">
                        <div class="col-md-12 smdn-show-adds">
                        '.$show_adds.'
               </div></div></div>';
    }
}

new PostViewList();
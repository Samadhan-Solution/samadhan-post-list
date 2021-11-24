<?php

namespace Samadhan;

use WP_Query;

class PostViewList
{
    public function __construct(){
        add_shortcode('post_list',array($this,'get_post_list_view'));
    }
    public function get_post_list_view($atts){
        samadhan_post_style_loaded();


        global $paged;
        $date = '';

        $atts = shortcode_atts(
            array(
                'posts_per_page' => '10',
            ), $atts, 'bartag' );

            $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

        $sort_id='';
        $category_id='';



        if(isset($_POST['search']) && !empty($_POST['search'])){

            $title=$_POST['search'];
            $paged=1;
            $menu_order=' ';
        }else{
            $title='';
            $menu_order='menu_order';
        }


        $args = array(
            'post_type'  => array('post'),
            "s" => $title,
            'posts_per_page' => $atts['posts_per_page'],
            'paged' => $paged,
            'suppress_filters' => true,
            'orderby' => $menu_order,
            'order'  => 'DESC',

        );



        if(isset($_POST['category_id']) && !empty($_POST['category_id'])){
            if(!empty($_POST['category_id'])){
                $category_id= $_POST['category_id'];
                $sort_id=$_POST['sort_by'];

                $category= array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => array($category_id),
                );
                $paged=1;
            }else{
                $category='';
            }



            $args = array(
                'post_type'  => array('post'),
                'posts_per_page' => $atts['posts_per_page'],
                'paged' => $paged,
                'suppress_filters' => true,
                'order'            => 'DESC',
                'tax_query' => array($category)

            );



        }



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
        $postslists =$post_query->posts;

        $postslist = array_reverse($postslists);

        $total_pages = $post_query->max_num_pages;

        $content = $this->get_filter_html($title,$sort_by_options,$category_options);
        $content .= $this->samadhan_get_post_latest_item($postslist);





        foreach ($postslist as $item){

            $posts_id=$item->ID;

            $content .=$this->samadhan_get_post_list_loop($item);


        }


        if ($total_pages > 1){

            $current_page = max(1, get_query_var('paged'));
            $content .= PostModel::samadhan_get_pagination($current_page,$total_pages);

        }

        wp_reset_postdata();



        return $content;



    }

    public function get_filter_html($title,$sort_by_options,$category_options){
        $postList='<form method="post" action="" >
                  <div class="container">
                      <div class="row" style="background-color: powderblue; padding-top: 6%; padding-bottom: 6%;">
                  <div class="col-md-12">  
                 <h1 style="text-align: center; font-size: 40px; font-weight: 700; font-family: Gotham-ultra, sans-serif;">DIGITAL MARKETING NEWS</h1> 
                    </div>
                    </div>
                      <div class="row"  style="background-color: powderblue; padding-bottom: 3%;">
                         <div class="col-md-5">  
                         <div class="form-group row ">
                                <label for="search" class="col-sm-3 col-form-label"  style="color: #11003C;">SEARCH </label>
                                <div class="col-sm-9 form-inline">
                                     <input class="form-control mr-sm-2" type="text" placeholder="Search" name="search" aria-label="Search" value="'.$title.'">
                                     <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa fa-search"> </i></button>
                                </div>
                              </div>
                              </div>
                        
                        <div class="col-md-3">  
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-4 col-form-label">SORT BY</label>
                            <div class="col-sm-8">
                              <select name="sort_by" class="form-control">
                                  '.$sort_by_options.'
                                </select>
                            </div>
                           </div>
                          </div>
                        <div class="col-md-4">
                        
                          <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">TOPICS</label>
                                <div class="col-sm-9">
                                  <select name="category_id" class="form-control">
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

       $image=wp_get_attachment_image_src(get_post_meta( $item[0]->ID,'_thumbnail_id',true),$size = 'full', $icon = false  );

       $postList='<div class="container">
                  <div class="row">
                    <div class="col-md-12">
                    <h1 style="margin-top: 6%;">'.$item[0]->post_title.'</h1>
                    <h4>'.$item[0]->post_name.'</h4>
                    <h5>'.date('d-m-Y',strtotime($item[0]->post_date)).'</h5>
                    <img src="'.$image[0].'" alt="Los Angeles" style="height: auto; max-width: 100%;">
                    </div>
                    </div>
                    </div>';

       return $postList;


    }
   public function samadhan_get_post_list_loop($item){

       $image=wp_get_attachment_image_src(get_post_meta( $item->ID,'_thumbnail_id',true),$size = 'full', $icon = false  );

       $postList='<div class="container">

                <div class="row" style="margin-top: 3%; margin-bottom: 3% ">
                <div class="col-md-4">
                <img src="'.$image[0].'" alt="Los Angeles" style="height: auto; max-width: 100%;">
                </div>
                <div class="col-md-4">
                <h2>'.$item->post_title.'</h2>
                <h5>'.date('d-m-Y',strtotime($item->post_date)).' '.get_the_author_meta('display_name', $item->post_author).'</h5>
                </div>
                <div class="col-md-4">
                <p>'.$item->post_content.'</p>
                </div>
                </div>
                
                
                </div>';
       return $postList;


    }
}

new PostViewList();
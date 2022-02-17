<?php
namespace Samadhan;
class PostModel
{
    public function __construct(){

    }
    public static function samadhan_get_sort_by_options($product_categories,$sort_by_id){

        $delivery_options='';
        foreach ($product_categories as $key=> $product_category){

            if($key==$sort_by_id){
                $selected='selected="selected"';
            }else{
                $selected='';
            }
            $delivery_options .='<option value="'.$key.'" '.$selected.'>'.$product_category.'</option>';


        }
        return $delivery_options;
    }
    public static function samadhan_get_post_category_options($post_categories,$category_id){

        $category_options ='<option value="-1">All</option>';

        foreach ($post_categories as $post_category){
            // var_dump($product_category->parent);
            $parent_category = get_term_by( 'slug', '	profession', 'category' );

            // if($product_category->parent==$parent_category->term_id  ){
            if($post_category->term_id==$category_id){
                $selected='selected="selected"';
            }else{
                $selected='';
            }

            $category_options .='<option value="'.$post_category->term_id.'" '.$selected.'>'.$post_category->name.'</option>';
            //}

        }
        return $category_options;
    }

    public static function samadhan_get_pagination($current_page,$filters,$total_pages){


        if($current_page<=1 ){
            $button_name='View More »';
        }else{
            $button_name='Next »';
        }
        $big = 10000000;
        $content = "<div class='samadhan-pagination'>".paginate_links( array(
                'base' => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' =>$current_page,
                'total' => $total_pages,
                'mid_size' => 1,
                'prev_text' => __('« Previous'),
                'next_text' => __($button_name),
                'add_args'=>$filters,
            ) )."</div>";


        return $content;
    }
    public static  function get_adds_data(){

        global $wpdb;
        return $wpdb->get_col("SELECT * FROM {$wpdb->prefix}adrotate where type='active' ORDER BY RAND() DESC");

    }

}
new PostModel();
<?php

namespace Samadhan;

class PostImortView
{
public function __construct(){
  add_shortcode('post_import',array($this,'get_import_file_data_form'));
}
public function get_import_file_data_form(){
    if(isset($_POST['import_button']) && !empty($_FILES['importfile'])){
        $data = file_get_contents($_FILES['importfile']['tmp_name']);
        $post_data=json_decode($data,true);
        $message= PostImportModel::save_post_data($post_data);
    }
    if(isset($_POST['delete-post']) && !empty($_POST['delete-post'])){
        $message=PostImportModel::delete_post_all_data();
    }
    $outPut='<div >'.$message.'</div><form method="POST" enctype="multipart/form-data">
                <input type="file" name="importfile" id="importfile"  accept="application/json" /> 
                <button id="import" class="btn btn-info" type="submit" name="import_button">Import</button>
                <button id="delete-post" style="background: #d50000;"  class="btn btn-danger" type="submit" name="delete-post" value="delete-post" onclick="return confirm(\'Are you sure you want to delete all posts \');" >Delete All Post</button>
                <script>
                    // jQuery("#import").click(function(){ jQuery("#importfile").trigger("click"); });
                   </script>
                </form>';
    return $outPut;
}
}
new PostImortView();
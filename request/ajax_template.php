<?php
try{
    $str = htmlentities(filter_input(INPUT_POST,'category'),ENT_QUOTES);
    $id = filter_var($str, FILTER_SANITIZE_STRING);
    $response = [];
    include '../functions/Functions.php';
    $class = new Functions();
    $templates = $class->fetchAll('test_templates'," WHERE category_id = '$id'");
    foreach($templates as $template){
        $response[] = array('tid'=>$template->id,'name'=>$template->template_name,'body'=>html_entity_decode($template->body));
    }
      echo (json_encode(array('data'=>$response)));
  }
  catch(Exception $e){
    echo $e->getMessage();
  }

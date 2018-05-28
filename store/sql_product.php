<?php
session_start();
if(empty($_SESSION['backend']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
require_once "../swop/library/dba.php";
$dba=new dba();
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(E_ALL);
$login_fi_no = $_SESSION['backend']['login_fi_no'];
$login_store = $_SESSION['backend']['login_store'];
$login_username = $_SESSION['backend']['login_username'];

function tina_encode($arr){
    if( is_array($arr) ){
        $b_key5str = false;

        if((bool)count(array_filter(array_keys($arr), 'is_string')))
                $b_key5str = true;
        $json="";
        foreach( $arr as $key => $val ){

                if( $b_key5str){
                        $json .= '"'.$key.'":';
                }

                if( is_array($val) )
                        $json .= tina_encode($val).",";
                else if(is_string($val))
                        $json .= '"'.$val.'",';
                else if(is_numeric($val))
                        $json .= $val.',';

        }
        if($b_key5str)
                return "{".substr($json,0,-1)."}";
        else
                return "[".substr($json,0,-1)."]";
    }else{
        throw new exception("It's not an array!");
    }
}

function get_oc_control_time()
{
    $atime = explode(" ", microtime());
    $atime = explode(".", $atime[0]);
    return date('Y-m-d H:i:s').".".$atime[1];
}

$images_path = "../public/img/goods/";
$thumbnail_path = "../public/img/thumbnail/";
$minimize_path = "../public/img/minimize/";
$introduction_images_path = "../public/img/introduction/";

switch($_POST["query_type"])
{
    case "product_add":
    case "product_copy":
        $_POST['images_filename'] = explode("∵",$_POST['images_filename']);
        $_POST['introduction_images_filename'] = explode("∵",$_POST['introduction_images_filename']);
        if($_POST['introduction_images_filename'][0]=="")$_POST['introduction_images_filename'] = array();
        $_POST["volumetric_weight_key"] = explode("∵",$_POST["volumetric_weight_key"]);
        $_POST["volumetric_weight_value"] = explode("∵",$_POST["volumetric_weight_value"]);
        $_POST["specification"] = explode("∵",$_POST["specification"]);
        $_POST["specification_class"] = explode("@",$_POST["specification_class"]);
        foreach($_POST["specification_class"] as $k => $v)
            $_POST["specification_class"][$k] = explode("∵",$v);
        $_POST["inventory"] = explode("∵",$_POST["inventory"]);
        $_POST["relate_news"] = $_POST["relate_news"]==""?array():explode("∵",$_POST["relate_news"]);
        $_POST["relate_hots"] = $_POST["relate_hots"]==""?array():explode("∵",$_POST["relate_hots"]);
        $_POST["related"] = tina_encode(array(
            "news" => $_POST["relate_news"],
            "hots" => $_POST["relate_hots"]
        ));
        

        $len = empty($_FILES)?0:count($_FILES["file_images"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_images"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        $len = empty($_FILES)?0:count($_FILES["file_introduction"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_introduction"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        
        $_POST["volumetric_weight"] = tina_encode(array_combine($_POST["volumetric_weight_key"],$_POST["volumetric_weight_value"]));
        $_POST["specification"][0] = $_POST["specification"][0]==""?"default":$_POST["specification"][0];
        $_POST["specification_class"][0][0] = $_POST["specification_class"][0][0] ==""?"default":$_POST["specification_class"][0][0];
        $_POST["specification"] = tina_encode(array_combine($_POST["specification"], $_POST["specification_class"]));
        $_POST["inventory"] = tina_encode($_POST["inventory"]);
        if(!empty($_POST["combination"]))
        {
            $_POST["inventory"]='["0"]';
            $_POST["specification"]='{"default":["default"]}';
        }
        $query = "insert into goods_index(category,brand,attribute,combination,name,product,import,price,promotions,discount,other_price,direct,specifications,inventory,volumetric_weight,store,status_audit,status_shelves,free_gifts,free_shipping,supplier,`delete`)values(".$_POST['category'].",".$_POST['brand'].",'".$_POST['attribute']."','".$_POST['combination']."','".$_POST['name']."','".$_POST['product']."',".$_POST['import'].",".$_POST['price'].",".$_POST['promotions'].",".$_POST['discount'].",'".$_POST['other_price']."',".$_POST['direct'].",'".$_POST["specification"]."','".$_POST['inventory']."','".$_POST["volumetric_weight"]."',".$login_store.",0,".$_POST['status_shelves'].",".$_POST['free_gifts'].",".$_POST['free_shipping'].",".$_POST['supplier'].",0)";
        $dba->query($query);
        $insert_id = $dba->get_insert_id();
        $_POST['introduction'] = str_replace("@", $insert_id, $_POST['introduction']);
        
        if(($len = count($_POST['images_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['images_filename'][$i], "@")===false){
                    //for copy
                    $_POST['images_filename'][$i] = explode("/",$_POST['images_filename'][$i]);
                    $_POST['images_filename'][$i] = $_POST['images_filename'][$i][count($_POST['images_filename'][$i])-1];
                    $seg = explode("_",$_POST['images_filename'][$i]);
                    $ext = explode(".",$seg[count($seg)-1]);
                    $new_name = $seg[0]."_".$seg[1]."_".$seg[2]."_".$insert_id.".".$ext[1];
                    copy($images_path.$_POST['images_filename'][$i],$images_path.$new_name);
                    copy($thumbnail_path.$_POST['images_filename'][$i],$thumbnail_path.$new_name);
                    copy($minimize_path.$_POST['images_filename'][$i],$minimize_path.$new_name);
                    $_POST['images_filename'][$i] = $new_name;
                    continue;
                }
                $_POST['images_filename'][$i] = str_replace("@", $insert_id, $_POST['images_filename'][$i]);
                $old_name = $_POST['images_filename'][$i];
                $_POST['images_filename'][$i] = explode("_",$_POST['images_filename'][$i]);
                array_shift($_POST['images_filename'][$i]);
                $_POST['images_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['images_filename'][$i]);
                if(is_uploaded_file($_FILES['file_images']['tmp_name'][$fileIndex]))
                {
                    if(!move_uploaded_file($_FILES['file_images']['tmp_name'][$fileIndex], $images_path.$_POST['images_filename'][$i]))
                    {
                        echo "圖片儲存失敗";
                        return;
                    }
                    $file_ext = explode(".",$_POST['images_filename'][$i]);
                    $ext_len = count($file_ext);
                    $file_ext = $file_ext[$ext_len-1];
                    switch ($file_ext)
                    {
                        case "jpg":
                        case "jpeg":
                            $src = imagecreatefromjpeg($images_path.$_POST['images_filename'][$i]);
                            break;
                        case "png":
                            $src = imagecreatefrompng($images_path.$_POST['images_filename'][$i]);
                            break;
                        case "gif":
                            $src = imagecreatefromgif($images_path.$_POST['images_filename'][$i]);
                            break;
                    }
                    $src_w = imagesx($src);
                    $src_h = imagesy($src);
                    $new_w = 230;
                    $new_h = 230;
                    $thumb = imagecreatetruecolor($new_w, $new_h);
                    imagecopyresampled($thumb, $src, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
                    imagejpeg($thumb, $thumbnail_path.$_POST['images_filename'][$i]);
                    $new_w = 25;
                    $new_h = 25;
                    $minimize = imagecreatetruecolor($new_w, $new_h);
                    imagecopyresampled($minimize, $src, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
                    imagejpeg($minimize, $minimize_path.$_POST['images_filename'][$i]);
                    imagedestroy($minimize);
                    imagedestroy($thumb);
                    imagedestroy($src);
                }
                $fileIndex++;
            }
            $_POST['images_filename'] = json_encode($_POST['images_filename']);
        }
        else
        {
            $_POST['images_filename'] = json_encode($_POST['images_filename']);
        }  
        if(($len = count($_POST['introduction_images_filename']))>0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['introduction_images_filename'][$i], "@")===false){
                    //for copy
                    $_POST['introduction_images_filename'][$i] = explode("/",$_POST['introduction_images_filename'][$i]);
                    $_POST['introduction_images_filename'][$i] = $_POST['introduction_images_filename'][$i][count($_POST['introduction_images_filename'][$i])-1];
                    $seg = explode("_",$_POST['introduction_images_filename'][$i]);
                    $ext = explode(".",$seg[count($seg)-1]);
                    $new_name = $seg[0]."_".$seg[1]."_".$seg[2]."_".$insert_id.".".$ext[1];
                    $_POST['introduction'] = str_replace($_POST['introduction_images_filename'][$i], $new_name, $_POST['introduction']);
                    copy($introduction_images_path.$_POST['introduction_images_filename'][$i],$introduction_images_path.$new_name);
                    $_POST['introduction_images_filename'][$i] = $new_name;
                    continue;
                }
                $_POST['introduction_images_filename'][$i] = str_replace("@", $insert_id, $_POST['introduction_images_filename'][$i]);
                $old_name = $_POST['introduction_images_filename'][$i];
                $_POST['introduction_images_filename'][$i] = explode("_",$_POST['introduction_images_filename'][$i]);
                array_shift($_POST['introduction_images_filename'][$i]);
                $_POST['introduction_images_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['introduction_images_filename'][$i]);
                $_POST['introduction'] = str_replace($old_name, $_POST['introduction_images_filename'][$i], $_POST['introduction']);
                
                if(is_uploaded_file($_FILES['file_introduction']['tmp_name'][$fileIndex]))
                {
                    if(!move_uploaded_file($_FILES['file_introduction']['tmp_name'][$fileIndex], $introduction_images_path.$_POST['introduction_images_filename'][$i]))
                    {
                        echo "圖片儲存失敗";
                        return;
                    }
                }
                $fileIndex++;
            }
            $_POST['introduction_images_filename'] = json_encode($_POST['introduction_images_filename']);
        }
        else
        {
            $_POST['introduction_images_filename'] = json_encode($_POST['introduction_images_filename']);
        }    
        
        
        require_once "../swop/library/tidyhtml.php";
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['introduction']);
        $_POST['introduction'] = $tidy->tidyhtml_content;
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['instructions']);
        $_POST['instructions'] = $tidy->tidyhtml_content;
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['remark']);
        $_POST['remark'] = $tidy->tidyhtml_content;
        
        $query = "update goods_index set images='".$_POST['images_filename']."',oc_control='".get_oc_control_time()."' where fi_no=".$insert_id;
        $dba->query($query);
        $query = "insert into goods_info(fi_no,category,brand,attribute,combination,name,product,depiction,import,price,promotions,discount,other_price,direct,specifications,inventory,volumetric_weight,images,promotions_message,introduction,introduction_images,store,status_audit,status_shelves,free_gifts,free_shipping,`delete`,related,instructions,remark,supplier) values (".$insert_id.",".$_POST['category'].",".$_POST['brand'].",'".$_POST['attribute']."','".$_POST["combination"]."','".$_POST['name']."','".$_POST['product']."','".$_POST['depiction']."',".$_POST['import'].",".$_POST['price'].",".$_POST['promotions'].",".$_POST['discount'].",'".$_POST["other_price"]."',".$_POST['direct'].",'".$_POST["specification"]."','".$_POST['inventory']."','".$_POST["volumetric_weight"]."','".$_POST['images_filename']."','".$_POST["promotions_message"]."','".$_POST['introduction']."','".$_POST['introduction_images_filename']."',".$login_store.",0,".$_POST['status_shelves'].",".$_POST['free_gifts'].",".$_POST['free_shipping'].",0,'".$_POST['related']."','".$_POST["instructions"]."','".$_POST["remark"]."',".$_POST["supplier"].")";
        $dba->query($query);
        $query = "insert into goods_vevaluate(fi_no,store,`view`,respond) values (".$insert_id.",".$login_store.",0,0)";
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增產品 ".$_POST['name']."(編號):".$insert_id."',NOW())";
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $fuer->addgoods($insert_id, $_POST['name'], $_POST['brand'], '', $_POST["specification"], $_POST['attribute'], $_POST["volumetric_weight"], '', $_POST['direct'], $_POST["supplier"], $_POST['price'], $_POST['import'], $_POST['promotions'], $_POST['discount'], $_POST["category_a"], $_POST["category_b"], $_POST["category_c"], $_POST['images_filename'], $_POST['introduction'], '', $_POST["other_price"]);
        
        echo "儲存成功！";
        break;
    case "product_edit":
        $_POST['images_filename'] = explode("∵",$_POST['images_filename']);
        $_POST['introduction_images_filename'] = explode("∵",$_POST['introduction_images_filename']);
        if($_POST['introduction_images_filename'][0]=="")$_POST['introduction_images_filename'] = array();
        $_POST["volumetric_weight_key"] = explode("∵",$_POST["volumetric_weight_key"]);
        $_POST["volumetric_weight_value"] = explode("∵",$_POST["volumetric_weight_value"]);
        $_POST["specification"] = explode("∵",$_POST["specification"]);
        $_POST["specification_class"] = explode("@",$_POST["specification_class"]);
        foreach($_POST["specification_class"] as $k => $v)
            $_POST["specification_class"][$k] = explode("∵",$v); 
        $_POST["inventory"] = explode("∵",$_POST["inventory"]);
        $_POST["relate_news"] = $_POST["relate_news"]==""?array():explode("∵",$_POST["relate_news"]);
        $_POST["relate_hots"] = $_POST["relate_hots"]==""?array():explode("∵",$_POST["relate_hots"]);
        $_POST["related"] = tina_encode(array(
            "news" => $_POST["relate_news"],
            "hots" => $_POST["relate_hots"]
        ));
        
        $len = empty($_FILES)?0:count($_FILES["file_images"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_images"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        $len = empty($_FILES)?0:count($_FILES["file_introduction"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_introduction"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        $query = "select images,introduction_images from goods_info where fi_no=".$_POST["fi_no"];
        $data = $dba->query($query);
        $images = json_decode($data[0]["images"]);
        $introduction_images = json_decode($data[0]["introduction_images"]);
        $_POST['introduction'] = str_replace("@", $_POST["fi_no"], $_POST['introduction']);
        if(($len = count($_POST['images_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['images_filename'][$i],"@")!==false)
                {
                    $_POST['images_filename'][$i] = str_replace("@", $_POST["fi_no"], $_POST['images_filename'][$i]);
                    $old_name = $_POST['images_filename'][$i];
                    $_POST['images_filename'][$i] = explode("_",$_POST['images_filename'][$i]);
                    array_shift($_POST['images_filename'][$i]);
                    $_POST['images_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['images_filename'][$i]);

                    if(is_uploaded_file($_FILES['file_images']['tmp_name'][$fileIndex]))
                    {
                        if(!move_uploaded_file($_FILES['file_images']['tmp_name'][$fileIndex], $images_path.$_POST['images_filename'][$i]))
                        {
                            echo "圖片儲存失敗";
                            return;
                        }
                        $file_ext = explode(".",$_POST['images_filename'][$i]);
                        $ext_len = count($file_ext);
                        $file_ext = $file_ext[$ext_len-1];
                        switch ($file_ext)
                        {
                            case "jpg":
                            case "jpeg":
                                $src = imagecreatefromjpeg($images_path.$_POST['images_filename'][$i]);
                                break;
                            case "png":
                                $src = imagecreatefrompng($images_path.$_POST['images_filename'][$i]);
                                break;
                            case "gif":
                                $src = imagecreatefromgif($images_path.$_POST['images_filename'][$i]);
                                break;
                        }
                        $src_w = imagesx($src);
                        $src_h = imagesy($src);
                        $new_w = 230;
                        $new_h = 230;
                        $thumb = imagecreatetruecolor($new_w, $new_h);
                        imagecopyresampled($thumb, $src, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
                        imagejpeg($thumb, $thumbnail_path.$_POST['images_filename'][$i]);
                        $new_w = 25;
                        $new_h = 25;
                        $minimize = imagecreatetruecolor($new_w, $new_h);
                        imagecopyresampled($minimize, $src, 0, 0, 0, 0, $new_w, $new_h, $src_w, $src_h);
                        imagejpeg($minimize, $minimize_path.$_POST['images_filename'][$i]);
                        imagedestroy($minimize);
                        imagedestroy($thumb);
                        imagedestroy($src);
                    }
                    $fileIndex++;
                }
                else
                {
                    $_POST['images_filename'][$i] = explode("/", $_POST['images_filename'][$i]);
                    $_POST['images_filename'][$i] = $_POST['images_filename'][$i][count($_POST['images_filename'][$i])-1];
                    if(($k = array_search($_POST['images_filename'][$i], $images)) !== false)
                    {
                        unset($images[$k]);
                    }
                }
            }
            if(!empty($images))
            foreach($images as $v){
                unlink($images_path.$v);
                unlink($thumbnail_path.$v);
                unlink($minimize_path.$v);
            }
            $_POST['images_filename'] = json_encode($_POST['images_filename']);
        }
        
        if(($len = count($_POST['introduction_images_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['introduction_images_filename'][$i],"@")!==false)
                {
                    $_POST['introduction_images_filename'][$i] = str_replace("@", $_POST["fi_no"], $_POST['introduction_images_filename'][$i]);
                    $old_name = $_POST['introduction_images_filename'][$i];
                    $_POST['introduction_images_filename'][$i] = explode("_",$_POST['introduction_images_filename'][$i]);
                    array_shift($_POST['introduction_images_filename'][$i]);
                    $_POST['introduction_images_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['introduction_images_filename'][$i]);
                    $_POST['introduction'] = str_replace($old_name, $_POST['introduction_images_filename'][$i], $_POST['introduction']);

                    if(is_uploaded_file($_FILES['file_introduction']['tmp_name'][$fileIndex]))
                    {
                        if(!move_uploaded_file($_FILES['file_introduction']['tmp_name'][$fileIndex], $introduction_images_path.$_POST['introduction_images_filename'][$i]))
                        {
                            echo "圖片儲存失敗";
                            return;
                        }
                    }
                    $fileIndex++; 
                }
                else
                {
                    $_POST['introduction_images_filename'][$i] = explode("/", $_POST['introduction_images_filename'][$i]);
                    $_POST['introduction_images_filename'][$i] = $_POST['introduction_images_filename'][$i][count($_POST['introduction_images_filename'][$i])-1];
                    if(($k = array_search($_POST['introduction_images_filename'][$i], $introduction_images)) !== false)
                    {
                        unset($introduction_images[$k]);
                    }
                }
            }
            if(!empty($introduction_images))
            foreach($introduction_images as $v){
                unlink($introduction_images_path.$v);
            }
            $_POST['introduction_images_filename'] = json_encode($_POST['introduction_images_filename']);
        }
        else
        {
            foreach($introduction_images as $v){
                unlink($introduction_images_path.$v);
            }
            $_POST['introduction_images_filename'] = json_encode($_POST['introduction_images_filename']);
        }
        
        $_POST["volumetric_weight"] = tina_encode(array_combine($_POST["volumetric_weight_key"],$_POST["volumetric_weight_value"]));
        $_POST["specification"][0] = $_POST["specification"][0]==""?"default":$_POST["specification"][0];
        $_POST["specification_class"][0][0] = $_POST["specification_class"][0][0] ==""?"default":$_POST["specification_class"][0][0];
        $_POST["specification"] = tina_encode(array_combine($_POST["specification"], $_POST["specification_class"]));
        $_POST["inventory"] = tina_encode($_POST["inventory"]);
        
        if(!empty($_POST["combination"]))
        {
            $_POST["inventory"]='["0"]';
            $_POST["specification"]='{"default":["default"]}';
        }
        
        require_once "../swop/library/tidyhtml.php";
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['introduction']);
        $_POST['introduction'] = $tidy->tidyhtml_content;
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['instructions']);
        $_POST['instructions'] = $tidy->tidyhtml_content;
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['remark']);
        $_POST['remark'] = $tidy->tidyhtml_content;
        
        $query = "update `goods_index` set `category`='".$_POST["category"]."',`brand`='".$_POST["brand"]."',`attribute`='".$_POST["attribute"]."',`combination`='".$_POST["combination"]."',`name`='".$_POST["name"]."',`product`='".$_POST["product"]."',`import`='".$_POST["import"]."',`price`='".$_POST["price"]."',`promotions`='".$_POST["promotions"]."',`discount`='".$_POST["discount"]."',`other_price`='".$_POST["other_price"]."',`direct`='".$_POST["direct"]."',`specifications`='".$_POST["specification"]."',`inventory`='".$_POST["inventory"]."',`volumetric_weight`='".$_POST["volumetric_weight"]."',`images`='".$_POST['images_filename']."',`store`='".$login_store."',`status_shelves`='".$_POST['status_shelves']."',`free_gifts`='".$_POST["free_gifts"]."',`free_shipping`='".$_POST["free_shipping"]."',`oc_control`='".get_oc_control_time()."',`supplier`='".$_POST["supplier"]."' where `fi_no`='".$_POST["fi_no"]."'";
        $dba->query($query);
        $query = "update goods_info set category=".$_POST["category"].",brand=".$_POST["brand"].",attribute='".$_POST["attribute"]."',combination='".$_POST["combination"]."',name='".$_POST["name"]."',product='".$_POST["product"]."',depiction='".$_POST["depiction"]."',import=".$_POST["import"].",price=".$_POST["price"].",promotions=".$_POST["promotions"].",discount=".$_POST["discount"].",other_price='".$_POST["other_price"]."',direct=".$_POST["direct"].",specifications='".$_POST["specification"]."',inventory='".$_POST["inventory"]."',volumetric_weight='".$_POST["volumetric_weight"]."',images='".$_POST['images_filename']."',promotions_message='".$_POST["promotions_message"]."',introduction='".$_POST["introduction"]."',introduction_images='".$_POST['introduction_images_filename']."',store=".$login_store.",status_shelves=".$_POST["status_shelves"].",free_gifts=".$_POST["free_gifts"].",free_shipping=".$_POST["free_shipping"].",related='".$_POST["related"]."',instructions='".$_POST["instructions"]."',remark='".$_POST["remark"]."',supplier=".$_POST["supplier"]." where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 編輯產品 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $fuer->updategoods($_POST["fi_no"], $_POST['name'], $_POST['brand'], '', $_POST["specification"], $_POST['attribute'], $_POST["volumetric_weight"], '', $_POST['direct'], $_POST["supplier"], $_POST['price'], $_POST['import'], $_POST['promotions'], $_POST['discount'], $_POST["category_a"], $_POST["category_b"], $_POST["category_c"], $_POST['images_filename'], $_POST['introduction'], '',$_POST["other_price"]);

        echo "儲存成功！";
        break;
    case "product_del":
        $files = scandir($images_path);
        foreach($files as $k => $v)
        {
            $seg = explode("_",$v);
            $seg = $seg[count($seg)-1];
            $seg = explode(".",$seg);
            $seg = (int)$seg[0];
            if($seg==$_POST["fi_no"])
                unlink ($images_path.$v);
        }
        $files = scandir($thumbnail_path);
        foreach($files as $k => $v)
        {
            $seg = explode("_",$v);
            $seg = $seg[count($seg)-1];
            $seg = explode(".",$seg);
            $seg = (int)$seg[0];
            if($seg==$_POST["fi_no"])
                unlink ($thumbnail_path.$v);
        }
        $files = scandir($minimize_path);
        foreach($files as $k => $v)
        {
            $seg = explode("_",$v);
            $seg = $seg[count($seg)-1];
            $seg = explode(".",$seg);
            $seg = (int)$seg[0];
            if($seg==$_POST["fi_no"])
                unlink ($minimize_path.$v);
        }
        $files = scandir($introduction_images_path);
        foreach($files as $k => $v)
        {
            $seg = explode("_",$v);
            $seg = $seg[count($seg)-1];
            $seg = explode(".",$seg);
            $seg = (int)$seg[0];
            if($seg==$_POST["fi_no"])
                unlink ($introduction_images_path.$v);
        }
        
        $query = "update goods_index set `delete`=1,oc_control='".get_oc_control_time()."' where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "update goods_info set `delete`=1 where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除產品 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $fuer->deletegoods($_POST["fi_no"]);
        break;
    case "get_product_detail":
        $query = "select * from goods_info where fi_no=".$_POST["fi_no"];
        $return = $dba->query($query);
        $return = $return[0];
        $return['specifications'] = urldecode($return['specifications']);
        $return['inventory'] = urldecode($return['inventory']);
        $return['volumetric_weight'] = urldecode($return['volumetric_weight']);

        $return =   $return['fi_no'].'`'.
                    $return['category'].'`'.
                    $return['brand'].'`'.
                    $return['attribute'].'`'.
                    $return['name'].'`'.
                    $return['depiction'].'`'.
                    $return['price'].'`'.
                    $return['promotions'].'`'.
                    $return['specifications'].'`'.
                    $return['inventory'].'`'.
                    $return['volumetric_weight'].'`'.
                    $return['images'].'`'.
                    $return['promotions_message'].'`'.
                    $return['introduction'].'`'.
                    $return['introduction_images'].'`'.
                    $return['discount'].'`'.
                    $return['free_gifts'].'`'.
                    $return['free_shipping'].'`'.
                    $return['instructions'].'`'.
                    $return['remark'].'`'.
                    $return['status_shelves'].'`'.
                    $return['supplier'].'`'.
                    $return['related'].'`'.
                    $return['import'].'`'.
                    $return['direct'].'`'.
                    $return['product'].'`'.
                    $return['other_price'].'`'.
                    $return['combination'];
        echo $return;
        break;
    case "get_news":
        $not_in = "";
        if($_POST["not_in"]!="")
        {
            $not_in = " and fi_no not in(".$_POST["not_in"].") ";
        }
        $query = "select fi_no from goods_index where store=".$login_store." and category=".$_POST["category"]." and `delete`=0 ".$not_in." order by added_date desc limit 0,".$_POST["auto_count"];
        $fi_nos = $dba->query($query);
        $return = array();
        foreach ($fi_nos as $k => $v)
        {
            $return[] = $v["fi_no"];
        }
        $return = implode(",", $return);
        echo $return;
        break;
    case "get_hots":
        $not_in = "";
        if($_POST["not_in"]!="")
        {
            $not_in = " and fi_no not in(".$_POST["not_in"].") ";
        }
        $query = "select fi_no from goods_index where store=".$login_store." and category=".$_POST["category"]." and `delete`=0 ".$not_in." order by click desc limit 0,".$_POST["auto_count"];
        $fi_nos = $dba->query($query);
        $return = array();
        foreach ($fi_nos as $k => $v)
        {
            $return[] = $v["fi_no"];
        }
        $return = implode(",", $return);
        echo $return;
        break;
}
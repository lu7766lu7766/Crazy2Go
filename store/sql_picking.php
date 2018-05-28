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

$images_path = "../public/img/picking/";

switch($_POST["query_type"])
{
    case "picking_add":
    case "picking_copy":

        $_POST["header_filename"] = explode("∵",$_POST['header_filename']);
        $_POST["footer_filename"] = explode("∵",$_POST['footer_filename']);
        
        $len = empty($_FILES)?0:count($_FILES["file_header"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_header"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        $len = empty($_FILES)?0:count($_FILES["file_footer"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_footer"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        
        $query = "insert into store_picking(store,name)values(".$login_store.",'".$_POST['name']."')";
        $dba->query($query);
        $insert_id = $dba->get_insert_id();
        
        $_POST['header'] = str_replace("@", $insert_id, $_POST['header']);
        $_POST['footer'] = str_replace("@", $insert_id, $_POST['footer']);
        
        if(($len = count($_POST['header_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['header_filename'][$i], "@")===false){
                    //for copy
                    $_POST['header_filename'][$i] = explode("/",$_POST['header_filename'][$i]);
                    $_POST['header_filename'][$i] = $_POST['header_filename'][$i][count($_POST['header_filename'][$i])-1];
                    $seg = explode("_",$_POST['header_filename'][$i]);
                    $ext = explode(".",$seg[count($seg)-1]);
                    $new_name = $seg[0]."_".$seg[1]."_".$seg[2]."_".$insert_id.".".$ext[1];
                    $_POST['header'] = str_replace($_POST['header_filename'][$i], $new_name, $_POST['header']);
                    copy($images_path.$_POST['header_filename'][$i],$images_path.$new_name);
                    $_POST['header_filename'][$i] = $new_name;
                    continue;
                }
                $_POST['header_filename'][$i] = str_replace("@", $insert_id, $_POST['header_filename'][$i]);
                $old_name = $_POST['header_filename'][$i];
                $_POST['header_filename'][$i] = explode("_",$_POST['header_filename'][$i]);
                array_shift($_POST['header_filename'][$i]);
                $_POST['header_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['header_filename'][$i]);
                $_POST['header'] = str_replace($old_name, $_POST['header_filename'][$i], $_POST['header']);
                
                if(is_uploaded_file($_FILES['file_header']['tmp_name'][$fileIndex]))
                {
                    if(!move_uploaded_file($_FILES['file_header']['tmp_name'][$fileIndex], $images_path.$_POST['header_filename'][$i]))
                    {
                        echo "圖片儲存失敗";
                        return;
                    }
                }
                $fileIndex++;
            }
            $_POST['header_filename'] = json_encode($_POST['header_filename']);
        }
        else
        {
            $_POST['header_filename'] = json_encode($_POST['header_filename']);
        }

        if(($len = count($_POST['footer_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['footer_filename'][$i], "@")===false){
                    //for copy
                    $_POST['footer_filename'][$i] = explode("/",$_POST['footer_filename'][$i]);
                    $_POST['footer_filename'][$i] = $_POST['footer_filename'][$i][count($_POST['footer_filename'][$i])-1];
                    $seg = explode("_",$_POST['footer_filename'][$i]);
                    $ext = explode(".",$seg[count($seg)-1]);
                    $new_name = $seg[0]."_".$seg[1]."_".$seg[2]."_".$insert_id.".".$ext[1];
                    $_POST['footer'] = str_replace($_POST['footer_filename'][$i], $new_name, $_POST['footer']);
                    copy($images_path.$_POST['footer_filename'][$i],$images_path.$new_name);
                    $_POST['footer_filename'][$i] = $new_name;
                    continue;
                }
                $_POST['footer_filename'][$i] = str_replace("@", $insert_id, $_POST['footer_filename'][$i]);
                $old_name = $_POST['footer_filename'][$i];
                $_POST['footer_filename'][$i] = explode("_",$_POST['footer_filename'][$i]);
                array_shift($_POST['footer_filename'][$i]);
                $_POST['footer_filename'][$i] = date("ymdHis",time()+1)."_".implode("_",$_POST['footer_filename'][$i]);
                $_POST['footer'] = str_replace($old_name, $_POST['footer_filename'][$i], $_POST['footer']);
                
                if(is_uploaded_file($_FILES['file_footer']['tmp_name'][$fileIndex]))
                {
                    if(!move_uploaded_file($_FILES['file_footer']['tmp_name'][$fileIndex], $images_path.$_POST['footer_filename'][$i]))
                    {
                        echo "圖片儲存失敗";
                        return;
                    }
                }
                $fileIndex++;
            }
            $_POST['footer_filename'] = json_encode($_POST['footer_filename']);
        }
        else
        {
            $_POST['footer_filename'] = json_encode($_POST['footer_filename']);
        }
        
        require_once "../swop/library/tidyhtml.php";
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['header']);
        $_POST['header'] = $tidy->tidyhtml_content;
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['footer']);
        $_POST['footer'] = $tidy->tidyhtml_content;
        
        $query = "update store_picking set title_context='".$_POST['header']."',title_images='".$_POST['header_filename']."',ending_context='".$_POST['footer']."',ending_images='".$_POST['footer_filename']."' where fi_no=".$insert_id;
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增揀貨單 ".$_POST['name']."(編號):".$insert_id."',NOW())";
        $dba->query($query);
        echo "儲存成功！";
        break;
    case "picking_edit":
        $_POST["header_filename"] = explode("∵",$_POST['header_filename']);
        $_POST["footer_filename"] = explode("∵",$_POST['footer_filename']);
        
        $len = empty($_FILES)?0:count($_FILES["file_header"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_header"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        $len = empty($_FILES)?0:count($_FILES["file_footer"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_footer"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        
        $query = "select title_images,ending_images from store_picking where fi_no=".$_POST["fi_no"];
        $data = $dba->query($query);
        $header_images = json_decode($data[0]["title_images"]);
        $footer_images = json_decode($data[0]["ending_images"]);
        
        $_POST['header'] = str_replace("@", $_POST["fi_no"], $_POST['header']);
        $_POST['footer'] = str_replace("@", $_POST["fi_no"], $_POST['footer']);
        
        if(($len = count($_POST['header_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['header_filename'][$i],"@")!==false)
                {
                    $_POST['header_filename'][$i] = str_replace("@", $_POST["fi_no"], $_POST['header_filename'][$i]);
                    $old_name = $_POST['header_filename'][$i];
                    $_POST['header_filename'][$i] = explode("_",$_POST['header_filename'][$i]);
                    array_shift($_POST['header_filename'][$i]);
                    $_POST['header_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['header_filename'][$i]);
                    $_POST['header'] = str_replace($old_name, $_POST['header_filename'][$i], $_POST['header']);
                    if(is_uploaded_file($_FILES['file_header']['tmp_name'][$fileIndex]))
                    {
                        if(!move_uploaded_file($_FILES['file_header']['tmp_name'][$fileIndex], $images_path.$_POST['header_filename'][$i]))
                        {
                            echo "圖片儲存失敗";
                            return;
                        }
                    }
                    $fileIndex++; 
                }
                else
                {
                    $_POST['header_filename'][$i] = explode("/", $_POST['header_filename'][$i]);
                    $_POST['header_filename'][$i] = $_POST['header_filename'][$i][count($_POST['header_filename'][$i])-1];
                    if(($k = array_search($_POST['header_filename'][$i], $header_images)) !== false)
                    {
                        unset($header_images[$k]);
                    }
                }
            }
            if(!empty($header_images))
            foreach($header_images as $v){
                unlink($images_path.$v);
            }
            $_POST['header_filename'] = json_encode($_POST['header_filename']);
        }
        else
        {
            foreach($header_images as $v){
                unlink($images_path.$v);
            }
            $_POST['header_filename'] = json_encode($_POST['header_filename']);
        }
        
        if(($len = count($_POST['footer_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['footer_filename'][$i],"@")!==false)
                {
                    $_POST['footer_filename'][$i] = str_replace("@", $_POST["fi_no"], $_POST['footer_filename'][$i]);
                    $old_name = $_POST['footer_filename'][$i];
                    $_POST['footer_filename'][$i] = explode("_",$_POST['footer_filename'][$i]);
                    array_shift($_POST['footer_filename'][$i]);
                    $_POST['footer_filename'][$i] = date("ymdHis",time()+1)."_".implode("_",$_POST['footer_filename'][$i]);
                    $_POST['footer'] = str_replace($old_name, $_POST['footer_filename'][$i], $_POST['footer']);
                    if(is_uploaded_file($_FILES['file_footer']['tmp_name'][$fileIndex]))
                    {
                        if(!move_uploaded_file($_FILES['file_footer']['tmp_name'][$fileIndex], $images_path.$_POST['footer_filename'][$i]))
                        {
                            echo "圖片儲存失敗";
                            return;
                        }
                    }
                    $fileIndex++; 
                }
                else
                {
                    $_POST['footer_filename'][$i] = explode("/", $_POST['footer_filename'][$i]);
                    $_POST['footer_filename'][$i] = $_POST['footer_filename'][$i][count($_POST['footer_filename'][$i])-1];
                    if(($k = array_search($_POST['footer_filename'][$i], $footer_images)) !== false)
                    {
                        unset($footer_images[$k]);
                    }
                }
            }
            if(!empty($footer_images))
            foreach($footer_images as $v){
                unlink($images_path.$v);
            }
            $_POST['footer_filename'] = json_encode($_POST['footer_filename']);
        }
        else
        {
            foreach($footer_images as $v){
                unlink($images_path.$v);
            }
            $_POST['footer_filename'] = json_encode($_POST['footer_filename']);
        }
        
        require_once "../swop/library/tidyhtml.php";
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['header']);
        $_POST['header'] = $tidy->tidyhtml_content;
        $tidy = new Library_Tidyhtml();
        $tidy->purifier($_POST['footer']);
        $_POST['footer'] = $tidy->tidyhtml_content;
        
        $query = "update store_picking set name='".$_POST['name']."',title_context='".$_POST['header']."',title_images='".$_POST['header_filename']."',ending_context='".$_POST['footer']."',ending_images='".$_POST['footer_filename']."' where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 編輯產品 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        echo "儲存成功！";
        break;
    case "picking_del":
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
        $query = "delete from store_picking where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除產品 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        break;
    case "get_picking_detail":
        $query = "select * from store_picking where fi_no=".$_POST["fi_no"];
        $return = $dba->query($query);
        $return = $return[0];
        
        $return =   $return['name'].'`'.
                    $return['title_context'].'`'.
                    $return['title_images'].'`'.
                    $return['ending_context'].'`'.
                    $return['ending_images'];
        echo $return;
        break;
}
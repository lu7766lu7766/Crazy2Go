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

$images_path = "../public/img/shipping/";

switch($_POST["query_type"])
{
    case "logi_add":
    case "logi_copy":
        if(!empty($_FILES))
        if($_FILES["file_images"]["size"]>1024*1024*2)
        {
            echo "單檔大小不能超過 2 MB";
            return;
        }
        $query = "insert into store_shipping(store,name,item,images)values(".$login_store.",'".$_POST["name"]."','".$_POST["item"]."','".$_POST["images_filename"]."')";
        $dba->query($query);
        $insert_id = $dba->get_insert_id();
        
        if(strpos($_POST['images_filename'], "@")===false){
            //for copy
            $_POST['images_filename'] = explode("/",$_POST['images_filename']);
            $_POST['images_filename'] = $_POST['images_filename'][count($_POST['images_filename'])-1];
            $seg = explode("_",$_POST['images_filename']);
            $ext = explode(".",$seg[count($seg)-1]);
            $new_name = $seg[0]."_".$seg[1]."_".$seg[2]."_".$insert_id.".".$ext[1];
            copy($images_path.$_POST['images_filename'],$images_path.$new_name);
            $_POST['images_filename'] = $new_name;
        }
        else
        {
            $_POST['images_filename'] = str_replace("@", $insert_id, $_POST['images_filename']);
            $old_name = $_POST['images_filename'];
            $_POST['images_filename'] = explode("_",$_POST['images_filename']);
            array_shift($_POST['images_filename']);
            $_POST['images_filename'] = date("ymdHis",time())."_".implode("_",$_POST['images_filename']);
        }

        if(!empty($_FILES))
        if(is_uploaded_file($_FILES['file_images']['tmp_name']))
        {
            if(!move_uploaded_file($_FILES['file_images']['tmp_name'], $images_path.$_POST['images_filename']))
            {
                echo "圖片儲存失敗";
                return;
            }
        }
        
        $query = "update store_shipping set images='".$_POST['images_filename']."' where fi_no=".$insert_id;
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增物流模板 ".$_POST['name']."(編號):".$insert_id."',NOW())";
        $dba->query($query);
        echo "儲存成功！";
        break;
    case "logi_edit":
        if(!empty($_FILES))
        if($_FILES["file_images"]["size"]>1024*1024)
        {
            echo "單檔大小不能超過 1 MB";
            return;
        }
        
        $query = "select images from store_shipping where fi_no=".$_POST["fi_no"];
        $data = $dba->query($query);
        $images = $data[0]["images"];
        
        if(strpos($_POST['images_filename'],"@")!==false)
        {
            $_POST['images_filename'] = str_replace("@", $_POST["fi_no"], $_POST['images_filename']);
            $old_name = $_POST['images_filename'];
            $_POST['images_filename'] = explode("_",$_POST['images_filename']);
            array_shift($_POST['images_filename']);
            $_POST['images_filename'] = date("ymdHis",time())."_".implode("_",$_POST['images_filename']);
            
            if(!empty($_FILES))
            if(is_uploaded_file($_FILES['file_images']['tmp_name']))
            {
                if(!move_uploaded_file($_FILES['file_images']['tmp_name'], $images_path.$_POST['images_filename']))
                {
                    echo "圖片儲存失敗";
                    return;
                }
            }
            echo $images_path.$images;
            unlink($images_path.$images);
        }
        else
        {
            $_POST['images_filename'] = explode("/", $_POST['images_filename']);
            $_POST['images_filename'] = $_POST['images_filename'][count($_POST['images_filename'])-1];
        }
        
        $query = "update store_shipping set name='".$_POST["name"]."',images='".$_POST['images_filename']."',item='".$_POST['item']."' where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 編輯物流模板 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        echo "儲存成功！";
        break;
    case "logi_del":
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
        $query = "delete from store_shipping where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除物流模板 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        break;
    case "get_logi_detail":
        $query = "select * from store_shipping where fi_no=".$_POST["fi_no"];
        $return = $dba->query($query);
        $return = $return[0];
        $return =   $return['fi_no'].'`'.
                    $return['name'].'`'.
                    $return['item'].'`'.
                    $return['images'];
        echo $return;
        break;
}
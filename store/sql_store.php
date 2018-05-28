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

$images_path = "../public/img/store/";

switch($_POST["query_type"])
{
    case "update_store_introduction":
        $query = "update store set introduction='".$_POST["introduction"]."',edit_date=NOW() where fi_no=".$login_store;
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 更新店家說明', NOW())";
        $dba->query($query);
        break;
    case "update_store_image":
        $_POST['filename'] = str_replace("@", $login_store, $_POST['filename']);
        if($_FILES["file"]["error"]>0)
        {
            echo "上傳失敗!";
        }
        else
        {
            if($_FILES["file"]["size"]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
            if(is_uploaded_file($_FILES['file']['tmp_name']))
            {
                if(!move_uploaded_file($_FILES['file']['tmp_name'], $_POST["image_path"].$_POST["filename"]))
                {
                    echo "圖片儲存失敗";
                    return;
                }
                echo "圖片儲存成功！";
                $query = "update store set images='".$_POST['filename']."',edit_date=NOW() where fi_no=".$login_store;
                $dba->query($query);
                $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 置換店家圖示', NOW())";
                $dba->query($query);
            }
        }
        break;
    case "update_store_ad":
        $_POST['ads_filename'] = explode("∵",$_POST['ads_filename']);
        $_POST['ads_item'] = explode("∵",$_POST['ads_item']);
        $_POST['ads_type'] = explode("∵",$_POST['ads_type']);
        $_POST['ads_delete'] = explode("∵",$_POST['ads_delete']);
        $len = empty($_POST['ads_delete'])||empty($_POST['ads_delete'][0])?0:count($_POST['ads_delete']);
        for($i = 0; $i<$len; $i++)
        {
            $query = "select images from store_advertisement where fi_no=".$_POST['ads_delete'][$i];
            $del_img = $dba->query($query);
            $del_img = $del_img[0]["images"];
            if(file_exists($images_path.$del_img))
            unlink($images_path.$del_img);
            $query = "update store_advertisement set `delete`=1 where fi_no=".$_POST['ads_delete'][$i];
            $dba->query($query);
        }
        $len = empty($_FILES)?0:count($_FILES["file_images"]["name"]);
        for($i = 0; $i<$len; $i++)
        {
            if($_FILES["file_images"]["size"][$i]>1024*1024)
            {
                echo "單檔大小不能超過 1 MB";
                return;
            }
        }
        $ids = array();
        if(($len = count($_POST['ads_filename'])) > 0)
        {
            $fileIndex = 0;
            for($i=0; $i<$len;$i++)
            {
                if(strpos($_POST['ads_filename'][$i], "@")===false){
                    //for update
                    $_POST['ads_filename'][$i] = explode("/",$_POST['ads_filename'][$i]);
                    $_POST['ads_filename'][$i] = $_POST['ads_filename'][$i][count($_POST['ads_filename'][$i])-1];
                    $update_id = explode("_",$_POST['ads_filename'][$i]);
                    $update_id=array_pop($update_id);
                    $update_id=explode(".",$update_id);
                    $update_id=$update_id[0];
                    $ids[]=$update_id;
                    $query = "update store_advertisement set images='".$_POST['ads_filename'][$i]."',type=".$_POST['ads_type'][$i].",item='".$_POST['ads_item'][$i]."',weights=".($len-$i)." where fi_no=".$update_id;
                    $dba->query($query);
                    continue;
                }
                
                $query = "insert into store_advertisement(store,images,type,item,weights,`delete`,click)values(".$login_store.",'".$_POST['ads_filename'][$i]."',".$_POST['ads_type'][$i].",'".$_POST['ads_item'][$i]."',".($len-$i).",0,0)";
                $dba->query($query);
                $insert_id = $dba->get_insert_id();
                $ids[]=$insert_id;
                $_POST['ads_filename'][$i] = str_replace("@", $insert_id, $_POST['ads_filename'][$i]);
                $old_name = $_POST['ads_filename'][$i];
                $_POST['ads_filename'][$i] = explode("_",$_POST['ads_filename'][$i]);
                array_shift($_POST['ads_filename'][$i]);
                $_POST['ads_filename'][$i] = date("ymdHis",time())."_".implode("_",$_POST['ads_filename'][$i]);
                
                if(is_uploaded_file($_FILES['file_images']['tmp_name'][$fileIndex]))
                {
                    if(!move_uploaded_file($_FILES['file_images']['tmp_name'][$fileIndex], $images_path.$_POST['ads_filename'][$i]))
                    {
                        echo "圖片儲存失敗";
                        return;
                    }
                    
                }
                $query = "update store_advertisement set images='".$_POST['ads_filename'][$i]."' where fi_no=".$insert_id;
                $dba->query($query);
                $fileIndex++;
            }
        }
        
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增更新產品廣告 (編號):".implode(",",$ids)."',NOW())";
        $dba->query($query);
        echo "儲存成功！";
        break;
    case "get_store_page_ad":
        $type = "";
        switch($_POST["type"]){
            case "1and2":
                $type="1,2";
                break;
            case "3and4":
                $type="3,4";
                break;
        }
        $query = "select fi_no,images,type,item from store_advertisement where type in (".$type.") and store=".$login_store." and `delete`=0 order by weights desc";
        $return = $dba->query($query);
        $fi_no = array();
        $images = array();
        $type = array();
        $item = array();
        foreach($return as $v)
        {
            $fi_no[]=$v["fi_no"];
            $images[]=$v["images"];
            $type[]=$v["type"];
            $item[]=$v["item"];
        }
        $return = json_encode(array(
            "fi_no" => $fi_no,
            "images" => $images,
            "type" => $type,
            "item" => $item
        ));
        echo $return;
        break;
}

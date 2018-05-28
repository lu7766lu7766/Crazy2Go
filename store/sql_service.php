<?php
session_start();
if(empty($_SESSION['backend']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$login_fi_no = $_SESSION['backend']['login_fi_no'];
$login_store = $_SESSION['backend']['login_store'];
$login_username = $_SESSION['backend']['login_username'];

switch($_POST["query_type"])
{
    case "service_add":
        $query = "select user from service where user=".$_POST["account"];
        $data = $dba->query($query);
        if(empty($data)){
            $query = "insert into service (permissions,store,type,name,user,password,qq)values('".$_POST["permissions"]."',".$login_store.",".$_POST["type"].",'".$_POST["name"]."','".$_POST["account"]."','".md5(md5($_POST["password"]))."','".$_POST["qq"]."')";
            $dba->query($query);
            $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增 ".$_POST["account"]."(".$_POST["name"].") 客服', NOW())";
            $dba->query($query);
            echo "新增成功！";
        }else{
            echo "新增失敗，帳號已存在！";
        }
        break;
    case "service_del":
        $query = "delete from service where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除 ".$_POST["name"]." 客服', NOW())";
        $dba->query($query);
        break;
    case "service_edit":
        if(empty($_POST["password"])){
            $query = "update service set permissions='".$_POST["permissions"]."',qq='".$_POST["qq"]."',type=".$_POST["type"]." where fi_no=".$_POST["fi_no"];
            $dba->query($query);
            $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改 ".$_POST["name"]." 客服類型為 ".$_POST["type"]." ,權限為 ".$_POST["permissions"]."', NOW())";
            $dba->query($query);
        }else{
            $query = "update service set permissions='".$_POST["permissions"]."',qq='".$_POST["qq"]."',type=".$_POST["type"].",password='".md5(md5($_POST["password"]))."' where fi_no=".$_POST["fi_no"];
            $dba->query($query);
            $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改 ".$_POST["name"]." 密碼,客服類型為 ".$_POST["type"]." ,權限為 ".$_POST["permissions"]."', NOW())";
            $dba->query($query);
        }
        break;
}

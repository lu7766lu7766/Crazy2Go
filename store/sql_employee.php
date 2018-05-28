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
    case "employee_add":
        $query = "select user from store_account where user=".$_POST["account"];
        $data = $dba->query($query);
        if(empty($data)){
            $query = "insert into store_account (store,active,permissions,name,user,password)values(".$login_store.",".$_POST["active"].",'".$_POST["permissions"]."','".$_POST["name"]."','".$_POST["account"]."','".md5(md5($_POST["password"]))."')";
            $dba->query($query);
            $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增 ".$_POST["account"]."(".$_POST["name"].") 員工', NOW())";
            $dba->query($query);
            echo "新增成功！";
        }else{
            echo "新增失敗，帳號已存在！";
        }    
        break;
    case "employee_del":
        $query = "delete from store_account where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除 ".$_POST["name"]." 員工', NOW())";
        $dba->query($query);
        break;
    case "employee_edit":
        if(empty($_POST["password"])){
            $query = "update store_account set permissions='".$_POST["permissions"]."',active=".$_POST["active"]." where fi_no=".$_POST["fi_no"];
            $dba->query($query);
            $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改 ".$_POST["name"]." 員工啟用為 ".$_POST["active"]." ,權限為 ".$_POST["permissions"]."', NOW())";
            $dba->query($query);
        }else{
            $query = "update store_account set permissions='".$_POST["permissions"]."',active=".$_POST["active"].",password='".$_POST["password"]."' where fi_no=".$_POST["fi_no"];
            $dba->query($query);
            $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改 ".$_POST["name"]." 密碼,員工啟用為 ".$_POST["active"]." ,權限為 ".$_POST["permissions"]."', NOW())";
            $dba->query($query);
        }
        break;
}

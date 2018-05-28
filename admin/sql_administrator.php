<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no	= $_SESSION["admin"]["login_fi_no"];
$new_user	= $_POST["user"];
$new_name	= $_POST["name"];
switch($_POST["query_type"]){

	case "administrator_add":
		$new_pwd	= $_POST["password"];
		$result = $dba->query("insert into administrator (`user`,`password`,`name`) values('$new_user',md5(md5('$new_pwd')),'$new_name')");
        if($result)
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) values('$admin_no','$admin_id 新增 $new_user($name) 管理者', NOW())");
        if($result)
        	die("success");
    break;
    case "administrator_edit":
    	$fi_no			= $_POST["fi_no"];
		$new_pwd		= $_POST["password"];
		$new_permissions= $_POST["permissions"];
		$new_active		= $_POST["active"];
		$password_sql 	= $new_pwd==""? "":"password=md5(md5('$new_pwd')),";
		
        $result = $dba->query("update administrator set
        						$password_sql
        						active='$new_active',
        						permissions='$new_permissions'
        					 where fi_no='$fi_no'");
        if($result)
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) values('$admin_no','$admin_id 編輯 $new_user($new_name) 管理者', NOW())");
        if($result)
        	die("success");
    break;
    case "administrator_del":
    	$fi_no		= $_POST["fi_no"];
		
        $result = $dba->query("delete from administrator where fi_no='$fi_no'");
        if($result)
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) values('$admin_no','$admin_id 刪除 $new_user($new_name) 管理者', NOW())");
        if($result)
        	die("success");
    break;
}
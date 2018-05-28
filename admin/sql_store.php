<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
switch($_POST["query_type"]){

	case "store_add":
		$store_name		= $_POST["store_name"];
		$introduction 	= $_POST["store_introduction"];
		
		$new_user	= $_POST["user"];
		$new_pwd 	= $_POST["password"];
		$new_name	= $_POST["name"];
		$user_name	= "$new_user($new_name)"; 
		
		$result		= $dba->query("insert into store (`name`,`introduction`,`create_date`) values('$store_name','$introduction',NOW())");
		$store_id 	= $dba->get_insert_id();
		if($result)
			$result = $dba->query("insert into store_account (`store`		,`active`	,`permissions`	,`user`		,`password`				,`name`		)
										 		 	   values('$store_id'	,'1'		,'all'			,'$new_user',md5(md5('$new_pwd'))	,'$new_name')");
		if($result)
			$dba->query("insert into administrator_log (`administrator`,`action`,											`action_date`) 
												 values('$admin_no',	'$admin_id 新增 {$store_name} 商店, $user_name 使用者', NOW())");
		if($result)
        	die("success");
	break;
	case "store_edit":
		$store_no	= $_POST["fi_no"];
		$store_name	= $_POST["store_name"];
		$store_introduction	= $_POST["store_introduction"];
		$user_id			= $_POST["user"];
		$update["name"]		= $_POST["name"];
		$update["password"]	= $_POST["password"];
		$update["active"]	= $_POST["active"];
		$user_name	= $user_id."(".$update["neme"].")"; 
		
		$sql = $store_introduction==""?"":"update store set `introduction`='$store_introduction' where `fi_no`='$store_no'";
		$result	= $dba->query($sql);
		foreach($update as $key=>$val){
			if($val!="")
				if($key=="password")
					$update_val=$update_val."`".$key."`"."= md5(md5('".$val."')), ";
				else
					$update_val=$update_val."`".$key."`"."='".$val."', ";
		}
		if($update_val!=""){
			$update_val = substr($update_val,0,-2);
			$sql = "update store_account set $update_val where `user`='$user_id'";
			$result2= $dba->query($sql);
			if( is_numeric($update["active"])&&(int)$update["active"]===0 ){
				$dba->query("update store_account set `active`='".$update["active"]."' where `store`='$store_no'");
			}
		}
		
		if($result || $result2){
			$dba->query("insert into administrator_log (`administrator`,`action`,											`action_date`)
												 values('$admin_no',	'$admin_id 編輯 $store_name 商店, $user_name 使用者', 	NOW())");
			die("success");
		}
		
	break;
	case "store_del":
		$store_no		= $_POST["fi_no"];
		$store_name	= $_POST["store_name"];
        $result		= $dba->query("delete from `store` where `fi_no`='$store_no'");
        if($result)
        	$dba->query("delete from `store_account` where `store`='$store_no'");
        if($result)
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) values('$admin_no','$admin_id 刪除 $store_name 商店', NOW())");
        if($result){
        	die("success");
        }
	break;
}
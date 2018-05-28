<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$fi_no		= $_POST["fi_no"];
$user_name	= $_POST["id"]."(".$_POST["name"].")";

switch($_POST["query_type"]){

	case "member_edit":
		
		require_once "../backend/template.php";
		
		$update["name"]			= $update2["name"]		= $_POST["name"];
		$update["email"]		= $update2["email"]		= $_POST["email"];
		$update["phone"]		= $update2["phone"]		= $_POST["phone"];
		$update["password"]		= $update2["password"]	= $_POST["password"];
		$update["sex"]									= $_POST["sex"];
		$update["qq"]									= $_POST["qq"];
		$update["birthday"]								= $_POST["birthday"];
								$update2["verification"]= $_POST["verification"];
		
		$update_val=arr2sql($update);
		if($update_val!=""){
			$sql = "update member_info set $update_val where `fi_no`='$fi_no'";
			$result = $dba->query($sql);
		}
		
		$update_val=arr2sql($update2);
		if($update_val!=""){
			$sql = "update member_index set $update_val where `fi_no`='$fi_no'";
			$result2 = $dba->query($sql);
		}
		
		if($result && $result2){
			$dba->query("insert into administrator_log (`administrator`,`action`,						`action_date`)
												 values('$admin_no',	'$admin_id 編輯 $user_name 會員', NOW())");
			die("success");
		}else{
			echo "member_info:".$result."\n member_index:".$result2;
		}
		
	break;
	case "member_del":
		
		$sql = "delete from member_info  where `fi_no`='$fi_no'";
		$result = $dba->query($sql);
		$sql = "delete from member_index  where `fi_no`='$fi_no'";
		$result2 = $dba->query($sql);
		if($result && $result2){
			$dba->query("insert into administrator_log (`administrator`,`action`,						`action_date`)
												 values('$admin_no',	'$admin_id 編輯 $user_name 會員', NOW())");
        	die("success");
		}else{
			echo "member_info:".$result."\n member_index:".$result2;
		}
        	
	break;
	case "get_member_info":
		
		$sql = "select sex,qq,birthday from member_info  where `fi_no`='$fi_no'";
		$result = $dba->query($sql);
		$result = $result[0];
		//die(print_r($result));
		die( json_encode($result) );
	break;
}
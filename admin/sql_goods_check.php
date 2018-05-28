<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject = "goods_check";
$subject_cht = "商品審核";
//////////

switch($_POST["query_type"]){
	
	case "get_".$subject."_info":
		die();
	break;
	
	break;
	
	case $subject."_add"://審核通過
	
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["name"];
		/////////////////////////////////////////////////////////////////////////////////
		$sql = "update `goods_index` set
					   `status_audit` = '1'
				where `fi_no`='".${$subject."_no"}."'";
				
		$result = $dba->query($sql);
		$sql = "update `goods_info` set
					   `status_audit` = '1'
				where `fi_no`='".${$subject."_no"}."'";
				
		$result2 = $dba->query($sql);
		if( $result && $result2 ){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 商品 ".${$subject."_name"}." 審核通過', NOW())");
			die("success");
		}
		die($sql);
		
	break;
	
	case $subject."_edit":
	break;
	
	case $subject."_del"://審核失敗
	
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["name"];
		/////////////////////////////////////////////////////////////////////////////////
		$sql = "update `goods_index` set
					   `status_audit` = '2'
				where `fi_no`='".${$subject."_no"}."'";
				
		$result = $dba->query($sql);
		$sql = "update `goods_info` set
					   `status_audit` = '2'
				where `fi_no`='".${$subject."_no"}."'";
				
		$result2 = $dba->query($sql);
		if( $result && $result2 ){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 商品 ".${$subject."_name"}." 審核失敗', NOW())");
			die("success");
		}
		die($sql);
		
	break;
}

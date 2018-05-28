<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject = "appeal";
$subject_cht = "會員申訴";
$sub_subject = "";
//////////

switch($_POST["query_type"]){
	
	case "get_".$subject."_info":
		die();
	break;
	
	case "get_".$sub_subject."_info":
	
		die();
	break;
	
	case $subject."_add":
		
		die();
	break;
	case $subject."_edit":
		
		${$subject."_no"}	= $_POST["fi_no"];
		$reply_content		= $_POST["reply_content"];
		$member				= $_POST["member"];
		
		/////////////////////////////////////////////////////////////////////////////////
		$sql = "update `appeal_index` set
					    `reply_content`		= '$reply_content'
					   ,`reply_date`		= NOW()
					   ,`progress`			= '2'
				where `fi_no`='".${$subject."_no"}."'";
		//die($sql);
		$result = $dba->query($sql);
		$sql = "update `appeal_info` set
					    `reply_content`		= '$reply_content'
					   ,`reply_date`		= NOW()
					   ,`progress`			= '2'
				where `fi_no`='".${$subject."_no"}."' and `member`='$member'";
		//die($sql);
		$result2 = $dba->query($sql);
		if( $result && $result2 ){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 編輯 ".${$subject."_name"}." {$subject_cht}', NOW())");
			die("success");
		}
		die($sql);
		
	break;
	case $subject."_del":
		
		die();
	break;
}

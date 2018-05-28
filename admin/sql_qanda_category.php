<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
require_once "../backend/template.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject 	= "qanda_category";
$subject_cht = "幫助中心分類";
$sub_subject = "";
//////////
$img_path = "../public/img/logo/";
$file_size_limit = 1024*1024*1;//1M   unit:byte
switch($_POST["query_type"]){
	
	case "get_category":
		$index = $_POST["index"];
		die( get_category($index,"qanda") );
	break;
	case "get_category_back":
		$fi_no	= $_POST["fi_no"];
		die( get_category_back($fi_no,"qanda").get_category($fi_no,"qanda") );
	break;
	
	case "get_".$subject."_info":
		
		die();
	break;
	
	case "get_".$sub_subject."_info":
	
		die();
	break;
	
	case $subject."_add":
	
		${$subject."_name"} = $_POST["name"];
		$index			= $_POST["index"];
		$result = $dba->query("select max(weights) from `qanda` where `index`='$index';");
		$weights = $result[0]["max(weights)"]+1;
		
		$sql =	"insert into `qanda` (`name`,`index`,`weights`,`show`) values('".${$subject."_name"}."','$index','$weights','1');";
		$result	= $dba->query($sql);
		
		if($result){
			
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 新增 ".${$subject."_name"}." {$subject_cht}', NOW())");
        	die("success");
        	
		}else{
			
			die($sql);
			
		}
			
	break;
	
	case $subject."_edit":
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["name"];
		$weights			= $_POST["weights"];
		$index				= $_POST["index"];
		$show				= $_POST["show"];
		
		/////////////////////////////////////////////////////////////////////////////////
		$logo_name = $file_modify_name[0];
		$sql = "update `qanda` set 
						`name`		= '".${$subject."_name"}."'
					   ,`weights`	= '$weights'
					   ,`index`		= '$index'
					   ,`show`		= '$show'
				where `fi_no`='".${$subject."_no"}."'";
		$result = $dba->query($sql);
		
		if( $result ){
			
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 編輯 ".${$subject."_name"}." {$subject_cht}', NOW())");
			die("success");
			
		}
		
		die($sql);
		
	break;
	
	case $subject."_del":
	
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["name"];
		
		$sql = "delete from `qanda` where `fi_no`='".${$subject."_no"}."'";
		$result	= $dba->query($sql);
		
        if($result){
	        
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) 
        										 values('$admin_no','$admin_id 刪除 ".${$subject."_name"}." {$subject_cht}', NOW())");
        	die("success");
        	
        }else{
	        
			die($sql);
			
		}
		
	break;
	
}

<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
require_once "../backend/template.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject  = "brand";
$subject_cht = "品牌列表";
$sub_subject = "";
//////////
$img_path = "../public/img/logo/";
$file_size_limit = 1024*1024*1;//1M   unit:byte
switch($_POST["query_type"]){
	
	case "get_category":
		$index = $_POST["index"];
		die( get_category($index) );
	break;
	case "get_category_back":
		$fi_no	= $_POST["fi_no"];
		die( get_category_back($fi_no).get_category($fi_no) );
	break;
	
	case "get_".$subject."_info":
		
		die();
	break;
	
	case "get_".$sub_subject."_info":
	
		die();
	break;
	
	case $subject."_add":
		${$subject."_name"} = $_POST["name"];
		$category			= $_POST["category"];
		$result = $dba->query("select max(weights) from `brand`;");
		$weights = $result[0]["max(weights)"]+1;
		
		$sql =	"insert into `brand` (`category`,`name`,`weights`) values('$category','".${$subject."_name"}."','$weights');";
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
		$category			= $_POST["category"];
		
		$a_remove_file		= $_POST["a_remove_file"];
		$file_source_name	= array();
		//圖片驗證
		$len = count($_FILES['file']['name']);
		for($i=0; $i<$len; $i++) {
			if( $_FILES['file']['error'][$i] > 0 ) {
				switch($_FILES['file']['error'][$i]){
                    case 1:
                        $error .= $_FILES['file']['name'][$i]."上傳超過伺服器規定大小<br>";break;
                    case 2:
                        $error .= $_FILES['file']['name'][$i]."上傳超過前台表單規定大小<br>";break;
                    case 3:
                        $error .= $_FILES['file']['name'][$i]."文件上傳不完整<br>";break;
                }
			}else{
				if( $_FILES['file']['size'][$i] > $file_size_limit ){
					$error .= " ";
					break;
				}
			}
		}
		if($error!=""){die($error);}
		//圖片上傳+改名
		for($i=0; $i<$len; $i++) {
			$mtime = explode(" ", microtime());
			list($width,$height,,) = getimagesize($_FILES['file']['tmp_name'][$i]);
			$ext = end(explode('.', $_FILES['file']['name'][$i]));
			$file_name =  date("ymdHis",$mtime[1]).substr($mtime[0],2)."_{$i}_{$width}x{$height}_".${$subject."_no"}.".{$ext}";
			move_uploaded_file($_FILES['file']['tmp_name'][$i], $img_path.$file_name);
			chmod($img_path.$file_name,0777);
			//因圖片改名，存原檔名歸類用
			$file_source_name[] = $_FILES['file']['name'][$i];
			$file_modify_name[] = $file_name;
		}
		//圖片刪除
		if(is_array($a_remove_file))
			foreach($a_remove_file as $per_remove_file)
				@unlink($img_path . $per_remove_file["title"]);
				//echo $img_path . $per_remove_file["title"];
		/////////////////////////////////////////////////////////////////////////////////
		$logo_name = $file_modify_name[0];
		$sql = "update `brand` set 
						`name`		='".${$subject."_name"}."',
						`weights`	='$weights',
						`category`	='$category',
						`logo`		='$logo_name'
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
		
		$sql = "delete from `brand` where `fi_no`='".${$subject."_no"}."'";
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

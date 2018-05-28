<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
require_once "../backend/template.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
switch($_POST["query_type"]){
	
	case "get_category":
		$index = $_POST["index"];
		die( get_category($index) );
	break;
	case "get_category_back":
		$fi_no	= $_POST["fi_no"];
		die( get_category_back($fi_no).get_category($fi_no) );
	break;
	case "category_add":
		$category_name	= $_POST["name"];
		$subtitle		= $_POST["subtitle"];
		
		$result	= $dba->query("insert into category (`name`,`subtitle`,`index`,`show`,`delete`) values('$category_name','$subtitle','0','0','0')");
		if($result){
			$dba->query("insert into administrator_log (`administrator`,`action`,							`action_date`)
												 values('$admin_no',	'$admin_id 新增 $category_name 分類', NOW())");
        	die("success");
		}
			
	break;
	case "category_edit":
		$category_no	= $_POST["fi_no"];
		$category_name	= $_POST["name"];
		$subtitle		= $_POST["subtitle"];
		$index			= $_POST["index"];
		$weights		= $_POST["weights"];
		$show			= $_POST["show"];
		
		$img_path 		= $_POST["img_path"];
		$file_size_limit= 1*1024*1024*1024;
		$len = count($_FILES['file']['name']);
		for($i=0; $i<$len; $i++) {
			if( $_FILES['file']['error'][$i] > 0 ) {
				switch($_FILES['file']['error'][$i])
                {
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
		
		for($i=0; $i<$len; $i++) {
			list($width,$height,,) = getimagesize($_FILES['file']['tmp_name'][$i]);
			$ext = end(explode('.', $file_name));
			$file_name =  date("ymdHis",time())."_{$i}_{$width}x{$height}_{$category_no}.{$ext}";
			move_uploaded_file($_FILES['file']['tmp_name'][$i], $img_path.$file_name);
			chmod($img_path.$file_name,0777);
			$icon_update_sql = "`icon`='$file_name',";
		}
		
		/*$filename = str_replace("@", $category_no, $_POST['pic_filename']);
		if($filename){
			$result = $dba->query("select `icon` from `category` where `fi_no`='$category_no'");
		    $file_path = $_POST['img_path'].$result[0]["icon"];
	        @unlink($file_path);
			$base64_src = $_POST["base64_src"];
			$file_path = $_POST['img_path'].$filename;
		    file_put_contents($file_path, base64_decode(str_replace(' ', '+', $base64_src)), true);
		    chmod($file_path,0777);
	        $icon_update_sql = "`icon`='$filename',";
	        echo "\n ".base64_decode(str_replace(' ', '+', $base64_src))." \n cache:".$base64_src."\ntttt".$_POST["base64_src"];
		}else{
			$icon_update_sql = "";
		}*/
        
		$sql ="update category set 
				  `name`='$category_name',
				  $icon_update_sql
				  `subtitle`='$subtitle',
				  `index`='$index',
				  `weights`='$weights',
				  `show`='$show' where `fi_no`='$category_no'";
		$result	= $dba->query($sql);
		
		if( $result && $error=="" ){
			$dba->query("insert into administrator_log (`administrator`,`action`,							`action_date`)
												 values('$admin_no',	'$admin_id 編輯 $category_name 分類', NOW())");
			die("success");
		}
		
		
	break;
	case "category_del":
		$category_no	= $_POST["fi_no"];
		$category_name	= $_POST["name"];
		
		$result		= $dba->query("update `category` set `delete`='1' where `fi_no`='$category_no'");
        if($result){
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) values('$admin_no','$admin_id 刪除 $category_name 分類', NOW())");
        	die("success");
        }
	break;
}

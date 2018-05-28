<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
require_once "../backend/template.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject  = "floors";
$subject_cht = "樓層列表";
$sub_subject = "";
//////////
$img_path = "../public/img/template/";
$file_size_limit = 1024*1024*1;//1M   unit:byte
switch($_POST["query_type"]){
	
	case "get_".$subject."_info":
		
		die();
	break;
	
	case "get_".$sub_subject."_info":
	
		die();
	break;
	
	case $subject."_add":
		${$subject."_name"} = $_POST["name"];
		$result = $dba->query("select max(weights) from `floors`;");
		$weights = $result[0]["max(weights)"]+1;
		$sql =	"insert into `floors` (`name`,`weights`,`show`) values('".${$subject."_name"}."','$weights','0');";
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
		$type				= $_POST["type"];
		$weights			= $_POST["weights"];
		$show				= $_POST["show"];
		$color_light		= $_POST["color_light"];
		$color_deep			= $_POST["color_deep"];
		$a_remove_file		= $_POST["a_remove_file"];
		$left_top			= $_POST["left_top"];//pic
		$left_middle		= $_POST["left_middle"];//pic
		$left_bottom		= $_POST["left_bottom"];//text
		$right_bottom		= $_POST["right_bottom"];//pic
		$middle_middle		= $_POST["middle_middle"];//pic
		$middle_bottom		= $_POST["middle_bottom"];//pic
		$right_top			= $_POST["right_top"];//text
		$more_url			= $_POST["more_url"];
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
			//$mtime = explode(" ", microtime());
			list($width,$height,,) = getimagesize($_FILES['file']['tmp_name'][$i]);
			$ext = end(explode('.', $_FILES['file']['name'][$i]));
			//$file_name =  date("ymdHis",$mtime[1]).substr($mtime[0],2)."_{$i}_{$width}x{$height}_".${$subject."_no"}.".{$ext}";
			$file_name =  date("ymdHis",time())."_{$i}_{$width}x{$height}_".${$subject."_no"}.".{$ext}";
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
		
		$a_item_json = array();
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($left_top))
		foreach($left_top as $val){
			$title = $val["title"];
			$key = array_search($title,$file_source_name);
			if( $key!==false )
				$title = $file_modify_name[$key];
			$a_tmp_item[] = $title;
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($left_middle))
		foreach($left_middle as $val){
			$title = $val["title"];
			$key = array_search($title,$file_source_name);
			if( $key!==false )
				$title = $file_modify_name[$key];
			$a_tmp_item[] = $title;
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($left_bottom))
		foreach($left_bottom as $val){
			$a_tmp_item[] = $val["value"];
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($right_bottom))
		foreach($right_bottom as $val){
			$title = $val["title"];
			$key = array_search($title,$file_source_name);
			if( $key!==false )
				$title = $file_modify_name[$key];
			$a_tmp_item[] = $title;
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($middle_middle))
		foreach($middle_middle as $val){
			$title = $val["title"];
			$key = array_search($title,$file_source_name);
			if( $key!==false )
				$title = $file_modify_name[$key];
			$a_tmp_item[] = $title;
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($middle_bottom))
		foreach($middle_bottom as $val){
			$title = $val["title"];
			$key = array_search($title,$file_source_name);
			if( $key!==false )
				$title = $file_modify_name[$key];
			$a_tmp_item[] = $title;
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		
		$a_tmp_item = array();
		$a_tmp_url = array();
		if(is_array($right_top))
		foreach($right_top as $val){
			$a_tmp_item[] = $val["value"];
			$a_tmp_url[] = $val["url"];
		}
		$a_item_json[] = array("item"=>$a_tmp_item,"url"=>$a_tmp_url);
		
		$a_item_json[] = array("item"=>array(),"url"=>array($more_url));
		
		$j_item_json = tina_encode($a_item_json);
		//echo $j_item_json;
		
		$sql = "update `floors` set 
						`name`='".${$subject."_name"}."',
						`type`='$type',
						`weights`='$weights',
						`item`='$j_item_json',
						`show`='$show',
						`color_light`='$color_light',
						`color_deep`='$color_deep' 
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
		
		$sql = "delete from `floors` where `fi_no`='".${$subject."_no"}."'";
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
/*
[
	{"item":"baby.jpg","url":"#"},
	{"item":["","","","","","","","","",""],"url":["#","#","#","#","#","#","#","#","#","#"]},
	{"item":["米麵糧油","風味美食","飲料沖泡","保健食品"],"url":["#","#","#","#"]},
	{"item":["baby2.jpg","makeup01.jpg"],"url":["#","#"]},
	{"item":["","","",""],"url":["","","",""]},
	{"item":["","","",""],"url":["","","",""]},
	{"item":["地方特產","精選米麵","草本漢方","奶粉食品"],"url":["","","",""]}
]
*/

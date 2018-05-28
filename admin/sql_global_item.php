<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject = "global_item";
$subject_cht = "全域項目管理";
$sub_subject = "";
//////////
$img_path = "../public/img/template/";
$file_size_limit = 1024*1024*1;//1M
$pic_width_limit	= 0;
$pic_height_limit	= 0;//不限
$pic_width = 0;
$pic_height = 0;

switch($_POST["query_type"]){
	
	case "get_".$subject."_info":
		$type 		= $_POST["fi_no"];
		$permissions2del	= $_POST["permissions2del"];
		$permissions2edit	= $_POST["permissions2edit"];
		$sql = "select `fi_no`,`name`,`item`,`weights`,`show` from `global_item` 
								where `type`='$type' order by `weights` desc";
		$result = $dba->query($sql);
		
		echo "
		<table class='table-h' id='list_panel'>
            <tr>
            	<td>ID</td>";
        if($type=="1"||$type=="4"){
            echo "	
            	<td>名稱</td>";
        }
        else
	    {
		    echo "
		    	<td>名稱</td>
		    	<td>圖</td>";
	    }
        	echo "
            	<td>是否顯示</td>
            	<td>權重</td>
            	<td>修改</td>
            	<td>刪除</td>
            </tr>";
        if(is_array($result))
        {
	        foreach($result as $per_data)
			{
				$tmp_no			= $per_data["fi_no"];
				$tmp_name		= $per_data["name"];
	            $json_item		= $per_data["item"];
	            $tmp_item		= json_decode($json_item,true);
	            $tmp_icon		= $tmp_item["icon"][0];
	            $a_items		= $tmp_item["item"];
	            $a_urls			= $tmp_item["url"];
	            $tmp_show		= $per_data["show"];
	            $tmp_show_cht	= $tmp_show==0?"不顯示":"顯示";
	            $tmp_weights	= $per_data["weights"];
	            $tmp_url 		= $tmp_item["url"][0];
	            
				echo "  
				<tr>
					<td>{$tmp_no}</td>";
				if($type=="1"||$type=="4"){
					echo "	
		            <td>{$tmp_name}</td>";
	            }
	            else
	            {
		            echo "
		            <td>{$tmp_name}</td>
		            <td><img src='{$img_path}{$tmp_icon}' title='$tmp_images' style='max-height:100px;max-width:100px'/></td>";
	            }
	            echo "
	                <td>{$tmp_show_cht}</td>
	                <td>{$tmp_weights}</td>
	                <td>";
	            if($permissions2edit)
	                echo "
	                    <input type='button' now_no='$tmp_no' value='編輯' data-open-dialog='編輯{$subject_cht}'>";
	            echo "
	            	</td><td>";
	            if($permissions2del)
	                echo "
	                    <input type='button' now_no='$tmp_no' value='刪除' class='del_btn'>";
	            //img
                if( is_file($img_path.$tmp_icon) )
            	{
	            	list($width,$height,,) = getimagesize($img_path.$tmp_icon);
	            	echo "<div id='data_{$tmp_no}_edit_icon'>";
	            	echo "<img src='{$img_path}{$tmp_icon}' title='$tmp_icon' s_width='$width' s_height='$height' style='max-height:100px;max-width:100px' />";
					echo "</div>";
            	}
            	//items
            	echo "	<div class='edit_items' move now_no='$tmp_no'>";
            	
            	//$last_item = end($a_items);
            	foreach($a_items as $item_val)
            	{
	            	echo "<input type='text' value='$item_val'>";
            	}
            	echo "<input type='button' value='新增欄位' class='add_move_text' />";
            	echo "	</div>";
            	//urls
            	echo "	<div class='edit_urls' move now_no='$tmp_no'>";
            	//$last_url = end($a_urls);
            	foreach($a_urls as $url_val)
            	{
	            	echo "<input type='text' value='$url_val'>";
            	}
            	echo "<input type='button' value='新增欄位' class='add_move_text' />";
            	echo "	</div>";
	            //end img
	            echo "	<input type='hidden' id='data_{$tmp_no}' name='$tmp_name' type='$type' edit_name='$tmp_name' edit_url='$tmp_url' edit_show='$tmp_show' edit_weights='$tmp_weights'  edit_item='$json_item' >";
	            echo "
	                </td>
	            </tr>";
			}
        }
		die("</table>");
	break;
	
	case "get_".$sub_subject."_info":
	
		die();
	break;
	
	case $subject."_add":
		
		$type 	= $_POST["type"];
		$name 	= $_POST["name"];
		$show 	= 0;
		
		$result = $dba->query("select max(weights) from `global_item` where `type`='$type';");
		$weights = $result[0]["max(weights)"]+1;
		
		$sql =	"insert into `global_item` (`type`,`name`,`weights`,`show`) 
									  values ('$type','$name','$weights','0');";
		$result	= $dba->query($sql);
		if($result){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 新增 ".$name." {$subject_cht}', NOW())");
        	die("success");
		}else{
			die($sql);
		}
		
	break;
	case $subject."_edit":
		
		require_once "../backend/template.php";
		
		$file_source_name	= array();
		$file_modify_name	= array();
		
		${$subject."_no"}	= $_POST["fi_no"];
		$type				= $_POST["type"];
		${$subject."_name"}	= $name				= $_POST["name"];
		$url				= $_POST["url"];
		$items				= $_POST["items"];
		$json_item			= $_POST["json_item"];
		$a_item				= json_decode($json_item,true);
		$s_icon				= count($a_item["icon"])>0?$a_item["icon"][0]:"";//
		
		$urls				= $_POST["urls"];
		$show				= $_POST["show"];
		$weights			= $_POST["weights"];
		
		$a_remove_file		= $_POST["a_remove_file"];
		
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
		if( $type=="2"|| $type=='3' )
		if(is_array($_FILES['file']['name']))
		foreach( $_FILES['file']['name'] as $key => $cc )
		{
			$mtime = explode(" ", microtime());
			list($width,$height,,) = getimagesize($_FILES['file']['tmp_name'][$key]);
			
			$source_filename = $_FILES['file']['name'][$key];
			$ext = end(explode('.', $source_filename));
			$file_name =  date("ymdHis",$mtime[1]).substr($mtime[0],2)."_{$key}_{$width}x{$height}_".${$subject."_no"}.".{$ext}";
			move_uploaded_file($_FILES['file']['tmp_name'][$key], $img_path.$file_name);
			chmod($img_path.$file_name,0777);
			//因圖片改名，存原檔名歸類用
			$image = $file_name;
			
		}
		//圖片刪除
		if(is_array($a_remove_file))
			foreach($a_remove_file as $per_remove_file)
				@unlink($img_path . $per_remove_file);
		
		if( $image==""&&!is_array($a_remove_file) )
				$image = $s_icon;
		if( $type=="1"||$type=='4' )
		{
			$a_item = array("url"=>array($url));
			$item = tina_encode($a_item);
		}
		else if( $type=="2" )
		{
			$urls = is_array($urls)?$urls:array();
			$items = is_array($items)?$items:array();
			$a_item = array("icon"=>array($image),"item"=>$items,"url"=>$urls);
			$item = tina_encode($a_item);
		}
		else if( $type=="3" )
		{
			$a_item = array("icon"=>array($image),"url"=>array($url));
			$item = tina_encode($a_item);
		}
		
		//die(${$subject."_name"}."^^".$answers."^^".$weights."^^".$qanda_no."^^".$json_images);
		/////////////////////////////////////////////////////////////////////////////////
		$sql = "update `global_item` set
					   `name`		= '$name'
					   ,`item`		= '$item'
					   ,`weights`	= '$weights'
					   ,`show`		= '$show'
					where `fi_no`='".${$subject."_no"}."'";
		//die($sql);
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
		$type				= $_POST["type"];
		
		if($type!='1')
		{
			$result = $dba->query("select `images`,`item` from `global_item` where `fi_no`='".${$subject."_no"}."';");
			foreach( $result as $per_result )
			{
				@unlink($img_path . $per_result['icon'][0]);
			}
		}
		
		$sql = "delete from `global_item` where `fi_no`='".${$subject."_no"}."'";
		$result	= $dba->query($sql);
        if($result){
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) 
        										 values('$admin_no','$admin_id 刪除 ".$type." ".${$subject."_name"}." {$subject_cht}', NOW())");
        	die("success");
        }else{
			die($sql);
		}
		
	break;
}

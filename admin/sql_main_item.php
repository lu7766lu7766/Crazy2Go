<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject = "main_item";
$subject_cht = "首頁焦點項目";
$sub_subject = "";
//////////
$img_path = "../public/img/template/";
$file_size_limit = 1024*1024*1;//1M
$pic_width_limit	= 0;
$pic_height_limit	= 0;//不限
$pic_width = 0;
$pic_height = 100;

switch($_POST["query_type"]){
	
	case "get_".$subject."_info":
		$type 		= $_POST["fi_no"];
		$permissions2del	= $_POST["permissions2del"];
		$permissions2edit	= $_POST["permissions2edit"];
		$sql = "select `fi_no`,`images`,`url`,`weights`,`show`,`setting` from `main_item` 
								where `type`='$type' order by `weights` desc";
		
		$result = $dba->query($sql);
		
		echo "
		<table class='table-h' id='list_panel'>
            <tr>
            	<td>ID</td>
            	<td class='list_images'>圖</td>
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
	            $tmp_images		= $per_data["images"];
	            $tmp_url		= $per_data["url"];
	            $tmp_show		= $per_data["show"];
	            $tmp_show_cht	= $tmp_show==0?"不顯示":"顯示";
	            $tmp_weights	= $per_data["weights"];
	            $json_setting	= $per_data["setting"];
	            $tmp_setting	= json_decode($json_setting,true);
	            
	            $attr			= "";
				echo "  
				<tr class='flash'>
					<td>{$tmp_no}</td>
		            <td><img src='{$img_path}{$tmp_images}' title='$tmp_images' style='max-height:100px;max-width:600px'/></td>
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
	            if( $type == 1 )
				{
			        $attr = " d_color ";
					$d_color = $tmp_setting["color"];
				}
            	//brand index=0 type=1 name depiction price url
            	//brand index=0 type=2 icon color name
            	if( file_exists($img_path.$tmp_images)&&!is_dir($img_path.$tmp_images) )
            	{
	            	list($width,$height,,) = getimagesize($img_path.$tmp_images);
	            	echo "<div id='data_{$tmp_no}_edit_images'>";
	            	echo "<img src='{$img_path}{$tmp_images}' title='$tmp_images' s_width='$width' s_height='$height' style='max-height:100px;max-width:600px' $attr />";
					echo "</div>";
            	}
	            //end img
	            echo "	<input type='hidden' id='data_{$tmp_no}' title='$tmp_images' edit_type='$type' edit_url='$tmp_url' edit_show='$tmp_show' edit_weights='$tmp_weights' >";
	            echo "<script>
	            		$(document).ready(function(){
		            		$('#data_{$tmp_no}').data('d_color','$d_color');
	            		});
	            	</script>
	            	";
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
		$url 	= $_POST["url"];
		
		$result = $dba->query("select max(weights) from `main_item` where `type`='$type';");
		$weights = $result[0]["max(weights)"]+1;
		
		$sql =	"insert into `main_item` (`type`,`images`,`url`,`setting`,`weights`,`show`) 
									  values ('$type','@','$url','[]','$weights','0');";
		$result	= $dba->query($sql);
		if($result){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 新增 {$subject_cht}', NOW())");
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
		${$subject."_name"}	= $_POST["title"];
		$type				= $_POST["type"];
		$url				= $_POST["url"];
		$show				= $_POST["show"];
		$weights			= $_POST["weights"];
		
		$a_remove_file		= $_POST["a_remove_file"];
		$del_src			= $_POST["del_src"];
		
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
			$images = $file_name;
			
		}
		//圖片刪除
		if(is_array($a_remove_file))
			foreach($a_remove_file as $per_remove_file)
				@unlink($img_path . $per_remove_file);
		//圖片刪除
		if(is_array($del_src))
			foreach($del_src as $per_remove_file)
				@unlink($img_path . $per_remove_file);
				
		
			
		if($type==1)
		{
			$a_setting["color"] = $_POST["d_color"];
		}
		else
		{
			$a_setting = array();
		}
		$setting = tina_encode($a_setting);
		
		//die(${$subject."_name"}."^^".$answers."^^".$weights."^^".$qanda_no."^^".$json_images);
		/////////////////////////////////////////////////////////////////////////////////
		$sql = "update `main_item` set
					    `url`		= '$url'
					   ,`setting`	= '$setting'
					   ,`weights`	= '$weights'
					   ,`show`		= '$show'
				";
		$sql .= $images!=""?",`images`	='$images' ":"";
		$sql .= "where `fi_no`='".${$subject."_no"}."'";
		//die($sql);
		$result = $dba->query($sql);
		if( $result ){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 編輯 ".${$subject."_name"}." {$subject_cht}', NOW())");
			//echo $sql;
			die("success");
		}
		die($sql);
		
	break;
	case $subject."_del":
		
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["title"];
		$type				= $_POST["type"];
		
		$result = $dba->query("select `images` from `main_item` where `fi_no`='".${$subject."_no"}."';");
		foreach( $result as $per_result )
		{
			@unlink($img_path . $per_result['images']);
		}
		
		$sql = "delete from `main_item` where `fi_no`='".${$subject."_no"}."'";
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

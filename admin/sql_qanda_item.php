<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
require_once "../backend/template.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
//主功能
$subject  = "qanda_item";
//主功能中文
$subject_cht = "幫助中心項目";
//子功能
$sub_subject = "";
//////////
//圖片上傳資料夾
$img_path = "../public/img/qanda/";
//圖片大小限制
$file_size_limit = 1024*1024*1;//1M   unit:byte
//上傳圖片寬度限制
$pic_width_limit	= 800;
//上傳圖片高度限制
$pic_height_limit	= 0;//不限
//預覽顯示塗麵的寬
$pic_width 	= 100;
//預覽顯示圖片的高
$pic_height = 0;
//子屬性，雙擊會跳出網址設定視窗
$v_url 		= "";
//子屬性，雙擊會跳出大小調整視窗
$v_resize 	= "resize";
switch($_POST["query_type"]){
	//取得當下分類
	case "get_category":
		$index = $_POST["index"];
		die( get_category($index,"qanda") );
	break;
	//取得母分類
	case "get_category_back":
		$fi_no	= $_POST["fi_no"];
		die( get_category_back($fi_no,"qanda").get_category($fi_no,"qanda") );
	break;
	//切換分類取得不同資料
	case "get_".$subject."_info":
		$category_no 		= $_POST["fi_no"];
		$permissions2del	= $_POST["permissions2del"];
		$permissions2edit	= $_POST["permissions2edit"];
		$sql = "select `fi_no`,`qanda_no`,`issue`,`answers`,`weights`,`show`,images from qanda_item 
								where `qanda_no`='$category_no' order by `weights` desc";
		$result	= $dba->query($sql);
		echo "
		<table class='table-h' id='list_panel'>
            <tr>
            	<td>ID</td>
            	<td>問題</td>
            	<td>是否顯示</td>
            	<td>權重</td>
            	<td>修改</td>
            	<td>刪除</td>
            </tr>";
        if(is_array($result))
		foreach($result as $per_data){
			$tmp_no			= $per_data["fi_no"];
			$tmp_qanda_no	= $per_data["qanda_no"];
            $tmp_issue		= $per_data["issue"];
            $tmp_answers	= $per_data["answers"];
            $tmp_show		= $per_data["show"];
            $tmp_show_cht	= $tmp_show==0?"不顯示":"顯示";
            $tmp_weights	= $per_data["weights"];
            $json_images	= $per_data["images"];
            $tmp_images		= json_decode($json_images);
            $attr			= "";
			echo "  
			<tr class='flash'>
				<td>{$tmp_no}</td>
				<td>{$tmp_issue}</td>
                <td>{$tmp_show_cht}</td>
                <td>{$tmp_weights}</td>
                <td>";
            //編輯權限判斷
            if($permissions2edit)
                echo "
                    <input type='button' now_no='$tmp_no' value='編輯' data-open-dialog='編輯{$subject_cht}'>";
            echo "
            	</td><td>";
            //刪除權限判斷
            if($permissions2del)
                echo "
                    <input type='button' now_no='$tmp_no' value='刪除' class='del_btn'>";
            //判斷是否有圖片
            if( is_array($tmp_images) )
            {	
	            $attr .= !isset($pic_width)||$pic_width==0?"":" width='$pic_width' ";
            	$attr .= !isset($pic_height)||$pic_height==0?"":" width='$pic_height' ";
            	$attr .= !isset($v_url)?"":" $v_url ";
            	$attr .= !isset($v_resize)?"":" $v_resize ";
            	//以div包覆id以"data_{fi_no}_上傳按鈕名"命名，該物件會被存入data並在編輯時自動顯示於圖片預覽
	            echo "<div id='data_{$tmp_no}_edit_images'>";
	            foreach($tmp_images as $image)
	            {
		            list($width,$height,,) = getimagesize($img_path.$image);
		            echo "<img src='{$img_path}{$image}' s_width='$width' s_height='$height' title='$image' $attr />";
	            }
	            	
	            echo "</div>";
            }
            //data物件 ，與編輯物件同名，編輯時即會被帶入
            //textarea物件 now_no={fi_no} class與編輯物件同名，編輯時會被帶入
            echo "	<input type='hidden' id='data_{$tmp_no}' issue='$tmp_issue' edit_issue='$tmp_issue' edit_weights='$tmp_weights' edit_show='$tmp_show' edit_qanda_no='$tmp_qanda_no'>
                	<textarea now_no='$tmp_no' class='edit_answers' >$tmp_answers</textarea>
                </td>
            </tr>";
		}
		die("</table>");
	break;
	//取得子資料用，關聯兩個以上資料表才會用到
	case "get_".$sub_subject."_info":
	
		die();
	break;
	//資料新增
	case $subject."_add":
		${$subject."_name"} = $_POST["issue"];
		$qanda_no			= $_POST["qanda_no"];
		$result = $dba->query("select max(weights) from `qanda_item` where qanda_no='$qanda_no';");
		$weights = $result[0]["max(weights)"]+1;
		
		$sql =	"insert into `qanda_item` (`qanda_no`,`issue`,`weights`,`show`) values('$qanda_no','".${$subject."_name"}."','$weights','0');";
		$result	= $dba->query($sql);
		if($result){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 新增 ".${$subject."_name"}." {$subject_cht}', NOW())");
        	die("success");
		}else{
			die($sql);
		}
			
	break;
	//資料編輯
	case $subject."_edit":
	
		require_once "../backend/template.php";
		require_once "../swop/library/tidyhtml.php";
        $tidy = new Library_Tidyhtml();
        
        
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["issue"];
		$answers 			= $_POST["answers"];
		$qanda_no			= $_POST["qanda_no"];
		$weights			= $_POST["weights"];
		$show				= $_POST["show"];
		$images				= $_POST["images"];
		
		$a_remove_file		= $_POST["a_remove_file"];
		$file_source_name	= array();
		$file_modify_name	= array();
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
		for($i=0; $i<$len; $i++)
		{
			$mtime = explode(" ", microtime());
			list($width,$height,,) = getimagesize($_FILES['file']['tmp_name'][$i]);
			
			$source_filename = $_FILES['file']['name'][$i];
			$ext = end(explode('.', $source_filename));
			$file_name =  date("ymdHis",$mtime[1]).substr($mtime[0],2)."_{$i}_{$width}x{$height}_".${$subject."_no"}.".{$ext}";
			
			if( strpos($answers,$source_filename)!==false )
			{
				$pattern = "/title[\s]*=[\s]*['\"]{$source_filename}['\"]/";
				$replacement = "src = '{$img_path}{$file_name}'";
				$answers = preg_replace($pattern, $replacement, $answers);
			}
			
			move_uploaded_file($_FILES['file']['tmp_name'][$i], $img_path.$file_name);
			chmod($img_path.$file_name,0777);
			//因圖片改名，存原檔名歸類用
			$file_source_name[] = $source_filename;
			$file_modify_name[] = $file_name;
		}
		//html校正
		$tidy->purifier($answers);
        $answers = $tidy->tidyhtml_content;
        $answers = addslashes($answers);
		//圖片刪除
		if(is_array($a_remove_file))
			foreach($a_remove_file as $per_remove_file)
				@unlink($img_path . $per_remove_file["title"]);
				//echo $img_path . $per_remove_file["title"];
				
		//圖片json字串建制		
		$a_tmp_item = array();
		if(is_array($images))
		foreach($images as $val){
			$title = $val["title"];
			$key = array_search($title,$file_source_name);
			if( $key!==false )
				$title = $file_modify_name[$key];
			$a_tmp_item[] = $title;
		}
		$json_images = tina_encode($a_tmp_item);
		//die(${$subject."_name"}."^^".$answers."^^".$weights."^^".$qanda_no."^^".$json_images);
		/////////////////////////////////////////////////////////////////////////////////
		
		$sql = "update `qanda_item` set 
						`issue`		='".${$subject."_name"}."'
					   ,`answers`	='$answers'
					   ,`weights`	='$weights'
					   ,`qanda_no`	='$qanda_no'
					   ,`show`		='$show'
					   ,`images`	='$json_images'
				where `fi_no`='".${$subject."_no"}."'";
		$result = $dba->query($sql);
		if( $result ){
			$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`)
												 values('$admin_no',	'$admin_id 編輯 ".${$subject."_name"}." {$subject_cht}', NOW())");
			die("success");
		}
		die($sql);
		
	break;
	//資料刪除
	case $subject."_del":
		${$subject."_no"}	= $_POST["fi_no"];
		${$subject."_name"}	= $_POST["issue"];
		
		$sql = "delete from `qanda_item` where `fi_no`='".${$subject."_no"}."'";
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

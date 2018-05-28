<?php
session_start();
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
require_once "../swop/library/dba.php";
$dba=new dba();

$admin_id	= $_SESSION["admin"]["login_user"];
$admin_no  	= $_SESSION["admin"]["login_fi_no"];
$subject = "advertisement";
$subject_cht = "廣告";
$sub_subject = "";
//////////
$img_path = "../public/img/advertisement/";
$file_size_limit = 1024*1024*1;//1M
$pic_width_limit	= 0;
$pic_height_limit	= 0;//不限
$pic_width = 0;
$pic_height = 100;

switch($_POST["query_type"]){
	
	case "get_".$subject."_info":
		$page 		= $_POST["fi_no"];
		$permissions2del	= $_POST["permissions2del"];
		$permissions2edit	= $_POST["permissions2edit"];
		$sql = "select `fi_no`,`type`,`index`,`images`,`url`,`item`,`weights`,`show`,`start_date`,`end_date` from `advertisement` 
								where `page`='$page' order by `index`,`weights` desc";
		
		$category_data_not_finish = $dba->query($sql);
		if(is_array($category_data_not_finish))
		{
		    foreach($category_data_not_finish as $per_data)
		    {
		    	if($per_data["index"]=="0")
		    	{
		        	$per_data["jfloors"]=0;
			        $category_data[]=$per_data;
		        }else
		        {
		        	$b_found=false;
		        	$offset=0;
			        foreach($category_data as $key=>$val)
			        {
				        if( $val["fi_no"]==$per_data["index"] )
				        {
					        $offset=$key+1;
					        $self_floors=$val["jfloors"]+1;
					        $b_found=true;
				        }else if( $val["index"]==$per_data["index"] )
				        {
				        	$offset=$key+1;
				        	$self_floors=$val["jfloors"];
				        	
				        }else if($b_found)
				        {
					        break;
				        }
			        }
			        
			        $clear_len = count($category_data)-$offset;
			        $per_data["jfloors"] = $self_floors;
			        
			        if($clear_len==0)
			        {
						$category_data[] = $per_data;
			        }else
			        {
				        $tmp_arr = array_slice($category_data,$offset);
				        array_unshift($tmp_arr,$per_data);
				        array_splice( $category_data,$offset,$clear_len,$tmp_arr );
			        }
		        }
		    }
		}
		
		$result = $category_data;
		$flash_index = 0;
		echo "
		<table class='table-h' id='list_panel'>
            <tr>
            	<td>ID</td>";
        if($page=="search"){
            echo "	
            	<td class='list_item'>關鍵字</td>
            	<td>關鍵字分類</td>";
        }else if($page=="brand")
	    {
		    echo "
		    	<td class='list_images'>圖</td>
		    	<td>Index</td>
		    	<td>品牌分類</td>";
	    }
	    else
	    {
            echo "	
            	<td class='list_images'>圖</td>";
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
				$tmp_type		= $per_data["type"];
	            $tmp_index		= $per_data["index"];
	            $tmp_images		= $per_data["images"];
	            $tmp_url		= $per_data["url"];
	            $json_item		= $per_data["item"];
	            $tmp_item		= json_decode($json_item,true);
	            $tmp_show		= $per_data["show"];
	            $tmp_show_cht	= $tmp_show==0?"不顯示":"顯示";
	            $tmp_weights	= $per_data["weights"];
	            $tmp_start_date	= date("Y-m-d H:i",strtotime($per_data["start_date"]));
	            $tmp_end_date	= date("Y-m-d H:i",strtotime($per_data["end_date"]));
	            
	            $attr			= "";
	            
	            list($width,$height,,) = getimagesize($img_path.$tmp_images);
	            
				echo "  
				<tr class='flash'>
					<td>{$tmp_no}</td>";
				if($page=="search"){
					if( $tmp_type == 1 )
					{
						$type_cht = "產品";
					}
					else if( $tmp_type == 2 )
					{
						$type_cht = "品牌";
					}
		            echo "	
		            <td>$json_item</td>
		            <td>{$type_cht}</td>";
	            }
	            else if($page=="brand")
	            {
		            if( $tmp_type == 1 )
					{
						$type_cht = "新進品牌";
					}
					else if( $tmp_type == 2 )
					{
						$type_cht = "推薦品牌";
						if( $tmp_index != 0 )
						{
							$type_cht.="(分頁廣告)";
						}
					}
		            echo "
		            <td><img src='{$img_path}{$tmp_images}' title='$tmp_images' style='max-height:100px;max-width:600px'/></td>
					<td>{$tmp_index}</td>
					<td>{$type_cht}</td>";
	            }
	            else
	            {
		            echo "	
		            <td><img src='{$img_path}{$tmp_images}' title='$tmp_images' style='max-height:100px;max-width:600px'/></td>";
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
	            if($page=="main"){
		            $pic_height = 0;
		            $pic_width = 400;
	            }
                $attr .= !isset($pic_width)||$pic_width==0?"":" width='$pic_width' ";
            	$attr .= !isset($pic_height)||$pic_height==0?"":" height='$pic_height' ";
            	if( $page == "brand" )
				{
					if( $tmp_type == 1 )
					{
				        $attr = " d_name d_depiction d_price d_url ";
						$d_name = $tmp_item["name"][0];
						$d_depiction = $tmp_item["depiction"][0];
						$d_price = $tmp_item["price"][0];
						$d_url = $tmp_item["url"][0];
					}else if( $tmp_type == 2 )
					{
						if( $tmp_index == 0 )
						{
							$attr = " d_icon d_color d_name ";
							$d_icon = $tmp_item["icon"];
							$d_color = $tmp_item["color"];
							$d_name = $tmp_item["name"][0];
						}
					}
				}
            	//brand index=0 type=1 name depiction price url
            	//brand index=0 type=2 icon color name
            	if( file_exists($img_path.$tmp_images)&&!is_dir($img_path.$tmp_images) )
            	{
	            	echo "<div id='data_{$tmp_no}_edit_images'>";
	            	echo "<img src='{$img_path}{$tmp_images}' s_width='$width' s_height='$height' title='$tmp_images' style='max-height:100px;max-width:600px' $attr />";
					echo "</div>";
            	}
	            //end img
	            echo "	<input type='hidden' id='data_{$tmp_no}' title='$tmp_images' edit_type='$tmp_type' edit_index='$tmp_index' edit_url='$tmp_url' edit_show='$tmp_show' edit_weights='$tmp_weights' edit_start_date='$tmp_start_date' edit_end_date='$tmp_end_date' edit_item='$json_item'>";
	            echo "<script>
	            		$(document).ready(function(){
		            		$('#data_{$tmp_no}').data('srcd_icon',new Array());";
		        if( file_exists($img_path.$d_icon)&&!is_dir($img_path.$d_icon) )
            	{
	            	echo "	$('#data_{$tmp_no}').data('srcd_icon')[-1]='{$img_path}{$d_icon}';";
            	}
		        echo "		$('#data_{$tmp_no}').data('d_name','$d_name');
		            		$('#data_{$tmp_no}').data('d_depiction','$d_depiction');
		            		$('#data_{$tmp_no}').data('d_price','$d_price');
		            		$('#data_{$tmp_no}').data('d_url','$d_url');
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
		
		$page	= $_POST["page"];
		$type 	= $_POST["type"];
		$item 	= $_POST["item"];
		$index 	= $_POST["index"];
		$url 	= $_POST["url"];
		$show 	= 0;
		$start_date = $_POST["start_date"];
		$end_date 	= $_POST["end_date"];
		
		$result = $dba->query("select max(weights) from `advertisement` where `page`='$page' and `index`='$index';");
		$weights = $result[0]["max(weights)"]+1;
		
		$sql =	"insert into `advertisement` (`page`,`type`,`index`,`images`,`url`,`item`,`weights`,`show`,`start_date`,`end_date`,`click`) 
									  values ('$page','$type','$index','@','$url','$item','$weights','0','$start_date','$end_date','0');";
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
		
		require_once "../backend/template.php";
		
		$file_source_name	= array();
		$file_modify_name	= array();
		
		${$subject."_no"}	= $_POST["fi_no"];
		$type				= $_POST["type"];
		$item				= $_POST["item"];
		$index				= $_POST["index"];
		$url				= $_POST["url"];
		$show				= $_POST["show"];
		$weights			= $_POST["weights"];
		$start_date			= $_POST["start_date"];
		$end_date			= $_POST["end_date"];
		
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
			if($key==0){
				$images = $file_name;
			}else if( $key==1 ){
				$_POST["d_icon"] = $file_name;
			}
		}
		//圖片刪除
		if(is_array($a_remove_file))
			foreach($a_remove_file as $per_remove_file)
				@unlink($img_path . $per_remove_file);
		//圖片刪除
		if(is_array($del_src))
			foreach($del_src as $per_remove_file)
				@unlink($img_path . $per_remove_file);
				
		
		if( $_POST["page"]=="brand" )
		{
			if( $item!='' )
			{
				$a_item = (array)json_decode( stripslashes($item) );
				if( $type==1 )
				{
					@unlink($img_path . $a_item["icon"]);
				}
			}
			
			$a_item = array();
			
			if($type==1)
			{
				$a_item["name"] 	= array($_POST["d_name"]);
				$a_item["depiction"]= array($_POST["d_depiction"]);
				$a_item["price"] 	= array($_POST["d_price"]);
				$a_item["url"] 		= array($_POST["d_url"]);
			}
			else if($type==2)
			{
				if($index==0)
				{
					$a_item["icon"] = $_POST["d_icon"];
					$a_item["color"]= "#".strtr($_POST["d_color"],array("#"=>""));
					$a_item["name"] = array($_POST["d_name"]);
				}
			}
			$item = tina_encode($a_item);
		}else if($_POST["page"]=="search")
		{
			$item = $_POST["item"];
		}
		
		//die(${$subject."_name"}."^^".$answers."^^".$weights."^^".$qanda_no."^^".$json_images);
		/////////////////////////////////////////////////////////////////////////////////
		$sql = "update `advertisement` set
					    `url`		= '$url'
					   ,`type`		= '$type'
					   ,`index`		= '$index'
					   ,`item`		= '$item'
					   ,`weights`	= '$weights'
					   ,`show`		= '$show'
					   ,`start_date`= '$start_date'
					   ,`end_date`	= '$end_date' ";
		$sql .= $images!=""?",`images`	='$images' ":"";
		$sql .= "where `fi_no`='".${$subject."_no"}."'";
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
		${$subject."_name"}	= $_POST["title"];
		$page				= $_POST["page"];
		
		if($page!='search')
		{
			$result = $dba->query("select `images`,`item` from `advertisement` where `fi_no`='".${$subject."_no"}."';");
			foreach( $result as $per_result )
			{
				@unlink($img_path . $per_result['images']);
				if( $per_result['item']!='' )
				{
					$a_item = (array)json_decode( $per_result['item'] );
					@unlink($img_path . $a_item["icon"]);
				}
			}
		}
		
		$sql = "delete from `advertisement` where `fi_no`='".${$subject."_no"}."'";
		$result	= $dba->query($sql);
        if($result){
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) 
        										 values('$admin_no','$admin_id 刪除 ".$page." ".${$subject."_name"}." {$subject_cht}', NOW())");
        	die("success");
        }else{
			die($sql);
		}
		
	break;
}

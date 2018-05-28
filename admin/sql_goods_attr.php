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
	case "get_attr_info":
		$category_no 		= $_POST["fi_no"];
		$permissions2del	= $_POST["permissions2del"];
		$permissions2edit	= $_POST["permissions2edit"];
		$result	= $dba->query("select `fi_no`,`category`,`name`,`type`,`required` from attribute 
								where `delete`='0' and `category`='$category_no' order by `category`,`weights` desc");
		echo "
		<table class='table-h' id='list_panel'>
            <tr>
            	<td>屬性名稱</td>
            	<td>項目選擇設定</td>
            	<td>是否必填</td>
            	<td>修改</td>
            	<td>刪除</td>
            </tr>";
		foreach($result as $per_data){
			$tmp_no			= $per_data["fi_no"];
            $tmp_category	= $per_data["category"];
            $tmp_name		= $per_data["name"];
            $tmp_type		= $per_data["type"];
            $tmp_type_cht	= $tmp_type==0?"單選":"多選";
            $tmp_required	= $per_data["required"];
            $tmp_required_cht=$tmp_required==1?"必填":"非必填";
			echo "  
			<tr class='flash'>
				<td>{$tmp_name}</td>
                <td>{$tmp_type_cht}</td>
                <td>{$tmp_required_cht}</td>
                <td>";
            if($permissions2edit)
                echo "
                    <input type='button' now_no='$tmp_no' value='編輯' data-open-dialog='編輯商品屬性'>";
            echo "
                </td>
                <td>";
            if($permissions2del)
                echo "
                	<input type='button' now_no='{$tmp_no}' class='del_btn' value='刪除'>";
            echo "
                	<input type='hidden' id='data_{$tmp_no}' edit_name='$tmp_name' edit_type='$tmp_type' edit_required='$tmp_required'>
                </td>
            </tr>";
		}
		die("</table>");
	break;
	
	case "get_attr_item_info":
		$attr_no = $_POST["fi_no"];
		$result	= $dba->query("select `fi_no`,`item` from attribute_item where `delete`='0' and `attribute`='$attr_no' order by `attribute`,`weights` desc");
		echo "
			<div class='edit_attr_item' now_no='$attr_no' move>";
		foreach($result as $per_data){
			$tmp_no		= $per_data["fi_no"];
            $tmp_item	= $per_data["item"];
			echo "<input type='text' fi_no='$tmp_no' value='$tmp_item'>";
		}
		echo "	<input type='button' value='新增項目' class='add_move_text'>";
		die("</div>");
	break;
	
	case "goods_attr_add":
		$goods_attr_name= $_POST["name"];
		$category		= $_POST["category"];
		$type			= $_POST["type"];
		$required		= $_POST["required"];
		$result = $dba->query("select max(weights) from attribute where `category`='7' limit 1;");
		$weights = $result[0]["max(weights)"]+1;
		$sql =	"insert into attribute (`name`,`category`,`type`,`required`,`weights`,`delete`) 
						values('$goods_attr_name','$category','$type','$required','$weights','0');";
		$result	= $dba->query($sql);
		if($result){
			$dba->query("insert into administrator_log (`administrator`,`action`,								`action_date`)
												 values('$admin_no',	'$admin_id 新增 $goods_attr_name 商品屬性', NOW())");
        	die("success");
		}else{
			die($sql);
		}
			
	break;
	case "goods_attr_edit":
		$goods_attr_no	= $_POST["fi_no"];
		$goods_attr_name= $_POST["name"];
		$type			= $_POST["type"];
		$required		= $_POST["required"];
		$a_attr_item	= $_POST["a_attr_item"];
		
		$result = $dba->query("update attribute set name='$goods_attr_name',type='$type',required='$required' where fi_no = '$goods_attr_no'");
		foreach($a_attr_item as $key=>$per_item){
			$item = $per_item[0];
			if($per_item[2]){//update	
				$item_no  = $per_item[2];
				$weights  = $per_item[1];
				$result2 .= $dba->query("update attribute_item set item='$item',weights='$weights' where fi_no = '$item_no'");
			}else{//insert
				$result2 .= $dba->query("insert into attribute_item (attribute,item,weights) values('$goods_attr_no','$item','$weights')");
				$item_no  = $dba->get_insert_id();
			}
			$all_item_no .= $item_no.",";
		}
		if( $all_item_no!=""){
			$all_item_no = substr($all_item_no,0,-1);
			$dba->query("update attribute_item set `delete`='1' where attribute='$goods_attr_no' and fi_no not in($all_item_no)");
		}
		if( $result && $result2 ){
			$dba->query("insert into administrator_log (`administrator`,`action`,							`action_date`)
												 values('$admin_no',	'$admin_id 編輯 $goods_attr_name 商品屬性', NOW())");
			die("success");
		}
		
		
	break;
	case "goods_attr_del":
		$goods_attr_no	= $_POST["fi_no"];
		$goods_attr_name= $_POST["name"];
		
		$sql = "update `attribute` set `delete`='1' where `fi_no`='$goods_attr_no'";
		$result		= $dba->query($sql);
        if($result){
        	$dba->query("update attribute_item set `delete`='1' where attribute='$goods_attr_no'");
        	$dba->query("insert into administrator_log (`administrator`,`action`,`action_date`) values('$admin_no','$admin_id 刪除 $goods_attr_name 商品屬性', NOW())");
        	die("success");
        }else{
			die($sql);
		}
	break;
}


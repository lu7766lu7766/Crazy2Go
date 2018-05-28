<?php
$html_title = '宏廣亞太後台管理系統';
$html_copyright = '- Copyright © 2014 BigwayAsia -';
$html_resource =    '<link rel="stylesheet" type="text/css" href="../backend/css/template.css">'.
                    '<link rel="stylesheet" type="text/css" href="../backend/js/jquery-te/jquery-te-1.4.0.css">'.
                    '<link rel="shortcut icon" type="image/png" href="../backend/img/favicon.png"/>'.
                    '<script type="text/javascript" src="../backend/js/jquery-1.11.1.min.js"></script>'.
                    '<script type="text/javascript" src="../backend/js/jquery-ui.effect.min.js"></script>'.
                    '<script type="text/javascript" src="../backend/js/jquery-te/jquery-te-1.4.0.min.js"></script>'.
                    '<script type="text/javascript" src="../backend/js/template.js"></script>'.
                    '<script type="text/javascript" src="../public/js/jac.validate.js"></script>';
function tina_encode($arr)
{
    if( is_array($arr) )
    {
        $b_normalArr = true;
		$i = 0;
        foreach( $arr as $key => $val )
        {
	        if($i!==$key)
	        {
		        $b_normalArr = false;
		        break;
	        }
	        $i++;
        }

        foreach( $arr as $key => $val )
        {
                if( !$b_normalArr)
                {
                    $json .= '"'.$key.'":';
                }

                if( is_array($val) )
                    $json .= tina_encode($val).",";
                        
                else if( is_string($val) )
                    $json .= '"'.$val.'",';
                        
                else if( is_numeric($val) )
                    $json .= $val.',';

        }
        if( !$b_normalArr )
                return "{".substr($json,0,-1)."}";
                
        else
                return "[".substr($json,0,-1)."]";
                
    }else
    {
        throw new exception("It's not an array!");
    }
}

function arr2sql($arr)
{
	if(!is_array($arr))return"";
	$sql="";
	foreach($arr as $key=>$val)
	{
		if($key=="password")
		{
			if( $val!="" )
				$sql .= ", $key = md5(md5('$val')) ";
		}
		else
		{
			$sql .= ", $key = '$val' ";
		}
	}
	return substr($sql,1);
}

function get_category_selection($table_name="category",$sel=0){
	
	global $dba;
	
	if( $table_name == "category" )
		$category_data_not_finish = $dba->query("select `fi_no`,`name`,`index` from category where `delete`=0 order by `index`,`weights` desc");
	
	else if( $table_name == "qanda" )
		$category_data_not_finish = $dba->query("select `fi_no`,`name`,`index` from qanda order by `index`,`weights` desc");
        //算出每個陣列該有的位置
    if(is_array($category_data_not_finish))
    foreach($category_data_not_finish as $per_data){
    	if($per_data["index"]=="0"){
        	$per_data["jfloors"]=0;
	        $category_data[]=$per_data;
        }else{
        	$b_found=false;
        	$offset=0;
	        foreach($category_data as $key=>$val){
		        if( $val["fi_no"]==$per_data["index"] ){
			        $offset=$key+1;
			        $self_floors=$val["jfloors"]+1;
			        $b_found=true;
		        }else if( $val["index"]==$per_data["index"] ){
		        	$offset=$key+1;
		        	$self_floors=$val["jfloors"];
		        	
		        }else if($b_found){
			        break;
		        }
	        }
	        
	        $clear_len = count($category_data)-$offset;
	        $per_data["jfloors"] = $self_floors;
	        if($clear_len==0){
				$category_data[] = $per_data;
	        }else{
		        $tmp_arr = array_slice($category_data,$offset);
		        array_unshift($tmp_arr,$per_data);
		        array_splice( $category_data,$offset,$clear_len,$tmp_arr );
	        }
        }
    }
    $category_html = "";
    foreach($category_data as $per_data){
    	$tmp_no = $per_data["fi_no"];
    	$tmp_floors = $per_data["jfloors"];
		$tmp_name = $per_data["name"];
		$space = "";
		$selected = $tmp_no == $sel?"selected":"";
		while($tmp_floors-->0){
    		$space.="--";
		}
        $category_html.="<option value='$tmp_no' $selected >{$space}{$tmp_name}</option>";
    }
    return $category_html;
}

function get_category($index=0,$table_name="category"){
	include_once '../swop/library/dba.php';
	global $dba;
	if(!is_object($dba)){
		$dba = new dba();
	}
	if( $table_name == "category" )
		$category_data = $dba->query("select `fi_no`,`name`,`index` from $table_name where `delete`=0 and `index`='$index' order by `index`,`weights` desc");
	
	else if( $table_name == "qanda" )
		$category_data = $dba->query("select `fi_no`,`name`,`index` from $table_name where `index`='$index' order by `index`,`weights` desc");
	
	if(count($category_data)==0)return "";
	$category_html = "<select class='category_sel'>
						<option value=''></option>";
    foreach($category_data as $per_data){
    	$tmp_no = $per_data["fi_no"];
    	$tmp_floors = $per_data["jfloors"];
		$tmp_name = $per_data["name"];
        $category_html.="<option value='$tmp_no'>{$tmp_name}</option>";
    }
    $category_html.= "</select>";
    return $category_html;
}

//反推
function get_category_back($fi_no,$table_name="category"){
	if( $fi_no=="" )return "請給予索引值";
	include_once '../swop/library/dba.php';
	global $dba;
	if(!is_object($dba)){
		$dba = new dba();
	}
	if( $table_name == "category" )
	{
		$result = $dba->query("select `index` from `category` where `fi_no`='$fi_no' limit 1");
		$category_data = $dba->query("select `fi_no`,`name` from $table_name where `delete`=0 and `index`='".$result[0]["index"]."' order by `index`,`weights` desc");
	}
	else if( $table_name == "qanda" )
	{
		$result = $dba->query("select `index` from `qanda` where `fi_no`='$fi_no' limit 1");
		$category_data = $dba->query("select `fi_no`,`name` from $table_name where `index`='".$result[0]["index"]."' order by `index`,`weights` desc");
	}
	$index = $result[0]["index"];
	$category_html = "";
	if( $index!=0 )
	{
		$run_times++;
		$category_html = get_category_back($index,$table_name);
		$category_html.= "<select class='category_sel'>";
		$category_html.= "<option value=''></option>";
	}
	else
	{
		$category_html.= "<select class='category_sel'>";
		$category_html.= "<option value='0'>最上層</option>";
	}
	
    foreach($category_data as $per_data){
    	$tmp_no = $per_data["fi_no"];
		$tmp_name = $per_data["name"];
		$selected = $tmp_no==$fi_no?"selected":"";
        $category_html.="<option value='$tmp_no' $selected >{$tmp_name}</option>";
    }
    $category_html.= "</select>";
    
    return $category_html;
}
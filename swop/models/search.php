<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		
		
				
		//品牌
		if($_GET['brand'] != '' && is_numeric($_GET['brand'])) {
			$string_get_brand = " AND `brand` = '".$_GET['brand']."' ";
		}
		
		//分類
		if($_GET['category'] != '' && is_numeric($_GET['category'])) {
			$string_get_category = " AND `category` = '".$_GET['category']."' ";
		}
		
		
		
		//關鍵字
		$query = explode(" ", $_GET['keyword']);
		$string_goods_index = "`name` LIKE '%".$query[0]."%'";
		for($i=1; $i<count($query); $i++) {
			if($query[$i] != '') {
				$string_goods_index .= " AND `name` LIKE '%".$query[$i]."%' ";
			}
		}
		if($_GET['hot_key'] != '') {
			$string_goods_index .=  " AND `name` LIKE '%".$_GET['hot_key']."%' ";
		}
		$string_goods_index = " (".$string_goods_index.") ";
		
		
		
		if($_GET['attribute'] == '' || !is_numeric($_GET['attribute'])) {
			//母規格
			$attribute = $dba->query("select `fi_no`, `name` from attribute");
			$array_attribute = array();
			for($i=0; $i<count($attribute); $i++) {
				for($j=0; $j<count($query); $j++) {
					if($attribute[$i]['name'] == $query[$j]) {
						$array_attribute[] = $attribute[$i]['fi_no'];
					}
				}
			}
			$array_attribute = array_unique($array_attribute);
			if($array_attribute[0] != '') {
				$string_array_attribute = "`attribute` LIKE '%\"".$array_attribute[0]."\":%'";
				for($i=1; $i<count($array_attribute); $i++) {
					$string_array_attribute .= " OR `attribute` LIKE '%\"".$array_attribute[$i]."\":%'";
				}
				$string_array_attribute = " AND (".$string_array_attribute.") ";
			}
			
			//子規格
			$attribute_item = $dba->query("select `fi_no`, `item` from attribute_item");
			$array_attribute_item = array();
			for($i=0; $i<count($attribute_item); $i++) {
				for($j=0; $j<count($query); $j++) {
					if($attribute_item[$i]['item'] == $query[$j]) {
						$array_attribute_item[] = $attribute_item[$i]['fi_no'];
					}
				}
			}
			
			$array_attribute_item = array_unique($array_attribute_item);
			if($array_attribute_item[0] != '') {
				$string_array_attribute_item = "`attribute` LIKE '%\"".$array_attribute_item[0]."\",%'";
				for($i=1; $i<count($array_attribute_item); $i++) {
					$string_array_attribute_item .= " OR `attribute` LIKE '%\"".$array_attribute_item[$i]."\",%'";
				}
				$string_array_attribute_item = " AND (".$string_array_attribute_item.") ";
			}
			$this->string_aai = implode(",",$array_attribute)."|".implode(",",$array_attribute_item);
		}
		else {
			$string_array_attribute_item = " AND (`attribute` LIKE '%\"".$_GET['attribute']."\",%') ";
			$this->string_aai = "|".$_GET['attribute'];
		}
		
		switch($_GET['discount']) {
			case "0":
				$discount = "";
				break;
			case "1":
				$discount = " && discount=1 ";
				break;
			default:
				$discount = "";
		}
		
		
		
		$price_start = floor($_GET['price_start']);
		$price_end = floor($_GET['price_end']);
		if($price_start != '' && is_numeric($price_start) && $price_end != '' && is_numeric($price_end) && $price_end > $price_start) {
			$interval = " && promotions >= ".$price_start." && promotions <= ".$price_end." ";
		}
		else {
			$interval = "";
		}
		
		
		
		if($_GET['direct'] == '1') {
			$direct = " && (direct=3 || direct=4) ";
		}
		else {
			$direct = "";
		}
		
		if($_GET['keyword']!='' || $_GET['attribute']!='') {
			$string_gat = "( ".$string_goods_index." ".$string_array_attribute." ".$string_array_attribute_item." )";
		}
		else {
			$string_gat = "1=1";
		}
		
		//echo "select count(`fi_no`) as count from view_goods_index WHERE ".$string_gat." ".$string_get_brand." ".$string_get_category." ".$discount." ".$interval." ".$direct." && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ";
		$goods_count = $dba->query("select count(`fi_no`) as count from view_goods_index WHERE ".$string_gat." ".$string_get_brand." ".$string_get_category." ".$discount." ".$interval." ".$direct." && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ");
		
		$page_limit = 20;
		$page_end = ceil($goods_count[0]['count']/$page_limit);
		if($_GET['page'] == '' || $_GET['page'] <= 0) {
			$page_now = 1;
		}
		else if($_GET['page'] > $page_end) {
			$page_now = $page_end;
		}
		else {
			$page_now = $_GET['page'];
		}
		
		if($page_now > 0) {
			$page_start = ($page_now - 1) * $page_limit;
		}
		else {
			$page_start = 0;
		}
		
		
		
		if($_GET['sort'] != '' && $_GET['by'] != '') {
			
			switch($_GET['sort']) {
				case 'comprehensive':
					$sort_order = "comprehensive";
					break;
				case 'popularity':
					$sort_order = "click";
					break;
				case 'sales':
					$sort_order = "transaction_times";
					break;
				case 'news':
					$sort_order = "added_date";
					break;
				case 'collect':
					$sort_order = "collect_times";
					break;
				case 'credit':
					$sort_order = "evaluation";
					break;
				case 'price':
					$sort_order = "promotions";
					break;
				default:
					$sort_order = "comprehensive";
			}
			
			$unsort_array = array('comprehensive', 'popularity', 'news', 'collect', 'sales', 'credit');
			if(!in_array($_GET['sort'], $unsort_array)) { //綜合、人氣、新品、收藏、銷量、信用沒有遞增排序
				switch($_GET['by']) {
					case 'desc':
						$sort_by = "desc";
						break;
					case 'asc':
						$sort_by = "asc";
						break;
					default:
						$sort_by = "desc";
				}
			}
			else {
				$sort_by = "desc";
			}
			
			$orderby = " ORDER BY ".$sort_order." ".$sort_by." ";
		}
		
		//echo "select `fi_no`, `images`, `name`, `price`, `promotions`, `discount`, `free_gifts`, `free_shipping`, `transaction_times`, `evaluation`, `evaluation_number`, `store_name` from view_goods_index WHERE ( ".$string_goods_index." ".$string_array_attribute." ".$string_array_attribute_item." ) ".$string_get_brand." ".$string_get_category." ".$discount." ".$interval." ".$orderby." LIMIT ".$page_start.",".$page_limit." ";
		$goods_index = $dba->query("select `fi_no`, `category`, `images`, `store`, `name`, `price`, `promotions`, `discount`, `free_gifts`, `free_shipping`, `transaction_times`, `evaluation`, `evaluation_number`, `store_name` from view_goods_index WHERE ".$string_gat." ".$string_get_brand." ".$string_get_category." ".$discount." ".$interval." ".$direct." && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ".$orderby." LIMIT ".$page_start.",".$page_limit." ");
		
		$this->goods_index = $goods_index;
		$this->num = $goods_count[0]['count'];
		
		
		
		if($this->num > 0 && $_SESSION['keyword'] != $_GET['keyword']) {
			$_SESSION['keyword'] = $_GET['keyword'];
			$search_results = $dba->query("select * from search WHERE keyword = '".$_GET['keyword']."' ");
			$search_num = count($search_results);
			
			if($search_num == 1) {
				$search_update = $dba->query("UPDATE search SET frequency=frequency+1 WHERE fi_no = '".$search_results[0]['fi_no']."' ");
			}
			else {
				$search_insert = $dba->query("INSERT INTO search (`fi_no`, `keyword`, `frequency`, `show`) VALUES (NULL, '".$_GET['keyword']."', '1', '1') ");
			}
		}
		
		$search_category = array();
		for($i=0; $i<count($goods_index); $i++) {
			$search_category[] = $goods_index[$i]['category'];
		}
		
		$this->recommend = $dba->query("SELECT `fi_no`, `name`, `images`, `discount`, `promotions`, `transaction_times` FROM goods_index WHERE `category` IN (".implode(",", $search_category).") && `status_audit`='1' && `status_shelves`='1' order by `added_date` desc limit 0, 15");
		
		$category = $dba->query("SELECT * FROM category");
		
		$all_index = array();
		for($i=0; $i<count($category); $i++) {
			for($j=0; $j<count($search_category); $j++) {
				if($category[$i]['fi_no'] == $search_category[$j]) {
					$all_index[] = $category[$i]['index'];
				}
			}
		}
		
		$all_category = array();
		for($i=0; $i<count($category); $i++) {
			for($j=0; $j<count($all_index); $j++) {
				if($category[$i]['index'] == $all_index[$j]) {
					$all_category[] = $category[$i]['fi_no'];
				}
			}
		}
				
		$this->love = $dba->query("SELECT `fi_no`, `name`, `images`, `discount`, `promotions` FROM goods_index WHERE `category` IN (".implode(",", $all_category).") && `status_audit`='1' && `status_shelves`='1' order by `transaction_times` desc limit 0,18");
		
		return $this;
	}
	
	public function ajax_append() {		
		require_once "swop/library/dba.php";
		$dba=new dba();
		
		$query = explode(" ", $_POST['keyword']);
		
		
		
		//選購熱點
		if($_POST['keyword'] !='' && $_POST['hot_key'] == '') {
			$string_keyword = "keyword LIKE '%".$query[0]."%'";
			for($i=1; $i<count($query); $i++) {
				if($query[$i] != '') {
					$string_keyword .= " AND keyword LIKE '%".$query[$i]."%' ";
				}
			}
			$hot_key = array();
			//echo "SELECT keyword FROM search WHERE ".$string_keyword." order by `frequency` desc LIMIT 0, 10";
			$keyword = $dba->query("SELECT keyword FROM search WHERE ".$string_keyword." order by `frequency` desc LIMIT 0, 10");
			
			for($i=0; $i<count($keyword); $i++) {
				$key = explode(" ", $keyword[$i]['keyword']);
				for($j=0; $j<count($key); $j++) {
					$c=0;
					for($k=0; $k<count($query); $k++) {
						if($key[$j] == $query[$k]) {
							$c++;
						}
					}
					if($c==0) {
						$hot_key[$key[$j]] = $hot_key[$key[$j]]+1;
					}
				}
			}
			arsort($hot_key);
		}
		else {
			$hot_key = array();
		}
		
		
		
		//品牌
		if($_POST['brand'] != '' && is_numeric($_POST['brand'])) {
			$string_get_brand = " AND `brand` = '".$_POST['brand']."' ";
		}
		
		//分類
		if($_POST['category'] != '' && is_numeric($_POST['category'])) {
			$string_get_category = " AND `category` = '".$_POST['category']."' ";
		}
		
		
		
		//關鍵字
		$string_goods_index = "`name` LIKE '%".$query[0]."%'";
		for($i=1; $i<count($query); $i++) {
			if($query[$i] != '') {
				$string_goods_index .= " AND `name` LIKE '%".$query[$i]."%' ";
			}
		}
		if($_POST['hot_key'] != '') {
			$string_goods_index .=  " AND `name` LIKE '%".$_POST['hot_key']."%' ";
		}
		$string_goods_index = " (".$string_goods_index.") ";
		
		
		
		$array = explode("|", $_POST['attr']);
		
		//母規格
		$array_attribute = explode(",", $array[0]);
		if($array_attribute[0] != '') {
			$string_array_attribute = "`attribute` LIKE '%\"".$array_attribute[0]."\":%'";
			for($i=1; $i<count($array_attribute); $i++) {
				$string_array_attribute .= " OR `attribute` LIKE '%\"".$array_attribute[$i]."\":%'";
			}
			$string_array_attribute = " AND (".$string_array_attribute.") ";
		}
		
		//子規格
		$array_attribute_item = explode(",", $array[1]);
		if($array_attribute_item[0] != '') {
			$string_array_attribute_item = "`attribute` LIKE '%\"".$array_attribute_item[0]."\",%'";
			for($i=1; $i<count($array_attribute_item); $i++) {
				$string_array_attribute_item .= " OR `attribute` LIKE '%\"".$array_attribute_item[$i]."\",%'";
			}
			$string_array_attribute_item = " AND (".$string_array_attribute_item.") ";
		}
		
		switch($_POST['discount']) {
			case "0":
				$discount = "";
				break;
			case "1":
				$discount = " && discount=1 ";
				break;
			default:
				$discount = "";
		}
		
		
		
		$price_start = floor($_POST['price_start']);
		$price_end = floor($_POST['price_end']);
		if($price_start != '' && is_numeric($price_start) && $price_end != '' && is_numeric($price_end) && $price_end > $price_start) {
			$interval = " && promotions >= ".$price_start." && promotions <= ".$price_end;
		}
		else {
			$interval = "";
		}
		
		
		
		if($_POST['direct'] == '1') {
			$direct = " && (direct=3 || direct=4) ";
		}
		else {
			$direct = "";
		}
		
		
		
		if($_POST['keyword']!='' || $_POST['attr']!='|') {
			$string_gat = "( ".$string_goods_index." ".$string_array_attribute." ".$string_array_attribute_item." )";
		}
		else {
			$string_gat = "1=1";
		}
		
		
		
		//echo "select `category`, `brand`, `attribute`, `store` from view_goods_index WHERE ".$string_gat." ".$string_get_brand." ".$string_get_category." ".$discount." ".$interval." ".$direct." && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ";
		$goods_index = $dba->query("select `category`, `brand`, `attribute`, `store` from view_goods_index WHERE ".$string_gat." ".$string_get_brand." ".$string_get_category." ".$discount." ".$interval." ".$direct." && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ");
		//print_r($goods_index);
		
		$goods_store = array();
		for($i=0; $i<count($goods_index); $i++) {
			$goods_store[$goods_index[$i]['store']] = $goods_store[$goods_index[$i]['store']] + 1;
		}
		
		$category_select = array();
		$brand_select = array();
		$attribute_select = array();
		for($i=0; $i<count($goods_index); $i++) {
			$category_select[$goods_index[$i]['category']] = $category_select[$goods_index[$i]['category']] + 1;
			$brand_select[$goods_index[$i]['brand']] = $brand_select[$goods_index[$i]['brand']] + 1;
			$j_attribute = (json_decode($goods_index[$i]['attribute']));
			if(count($j_attribute) > 0) {
				foreach($j_attribute as $k => $v) {
					$attribute_select[$k] = $attribute_select[$k] + 1;
				}
			}
		}
		
		
		
		if($_POST['category'] == '') {
			$category = $dba->query("select * from category");
			$all_category = array();
			foreach($category_select as $k => $v) {
				$a = $k; //追母分類
				$b = ''; //記錄子分類名
				$c = 0; //確認記錄子分類名
				$tmp = ''; //記錄母分類名
				while(true) {
					for($i=0; $i<count($category); $i++) {
						if($category[$i]['fi_no'] == $a && $category[$i]['index'] == 0 ) {
							$tmp = $category[$i]['name'];
						}
						else if($category[$i]['fi_no'] == $a) {
							$a = $category[$i]['index'];
							if($c == 0) {
								$b = $category[$i]['name']."|".$category[$i]['fi_no'];
								$c = 1;
							}
						}
					}
					if($tmp != '') {
						break;
					}
				}
				if($b != '') { //強制排除母分類
					$all_category[$tmp][$b] = $v;
				}
			}
		}
		else if( is_numeric($_POST['category']) ) {
			$category = $dba->query("select * from category");
			$category_str = "";
			$category_tmp = $_POST['category'];
			$category_index = "";
			while(true) {
				for($i=0; $i<count($category); $i++) {
					if($category[$i]['fi_no'] == $category_tmp) {
						if($category_index == "") {
							$category_index = $category[$i]['index'];
						}
						$category_str = $category[$i]['name'].">".$category_str;
						if($category[$i]['index'] == 0) {
							$category_tmp = "break";
						}
						else {
							$category_tmp = $category[$i]['index'];
						}
					}
				}
				if($category_tmp == "break") {
					break;
				}
			}
			
			$cll_arr = array();
			if($_POST['cll']!="") {
				$cll = explode(".", $_POST['cll']);
				for($i=0; $i<count($category); $i++) {
					for($j=0; $j<count($cll); $j++) {
						if($category[$i]['fi_no'] == $cll[$j] && $category[$i]['index'] == $category_index /*&& $category[$i]['fi_no'] != $_POST['category']*/) {
							$cll_arr[] = $category[$i]['fi_no']."|".$category[$i]['name'];
						}
					}
				}
			}
		}
		else {
			$all_category =  array();
		}
		
		
		
		
		
		
		
		
		
		if($_POST['brand'] == '') {
			$brand = $dba->query("select * from brand_group");
			$all_brand = array();
			//print_r($brand_select);
			foreach($brand_select as $k => $v) {
				for($i=0; $i<count($brand); $i++) {
					if($brand[$i]['fi_no'] == $k) {
						$all_brand[$brand[$i]['name']."|".$brand[$i]['fi_no']] = $v;
					}
				}
			}
		}
		else if( is_numeric($_POST['brand']) ) {
			$brand = $dba->query("select * from brand_group");
			$brand_str = "";
			for($i=0; $i<count($brand); $i++) {
				if($brand[$i]['fi_no'] == $_POST['brand']) {
					$brand_str = $brand[$i]['name'];
				}
			}
		}
		else {
			$all_brand = array();
		}
		
		
		
		
		
		
		
		if($_POST['attribute'] == '') {
			$attribute = $dba->query("select * from attribute");
			$attribute_item = $dba->query("select * from attribute_item");
			$all_attribute = array();
			foreach($attribute_select as $k => $v) {
				for($i=0; $i<count($attribute); $i++) {
					if($attribute[$i]['fi_no'] == $k) {
						
						for($j=0; $j<count($attribute_item); $j++) {
							if($k == $attribute_item[$j]['attribute']) {
								$all_attribute[$attribute[$i]['name']][] = $attribute_item[$j]['item']."|".$attribute_item[$j]['fi_no'];
							}
						}
						
					}
				}
			}
		}
		else if( is_numeric($_POST['attribute']) ) {
			$attribute = $dba->query("select * from attribute");
			$attribute_item = $dba->query("select * from attribute_item");
			$attribute_str = "";
			$attribute_tmp = "";
			for($j=0; $j<count($attribute_item); $j++) {
				if($attribute_item[$j]['fi_no'] == $_POST['attribute']) {
					$attribute_str = $attribute_item[$j]['item'];
					$attribute_tmp = $attribute_item[$j]['attribute'];
				}
			}
			
			for($i=0; $i<count($attribute); $i++) {
				if($attribute[$i]['fi_no'] == $attribute_tmp) {
					$attribute_str .= ":".$attribute[$i]['name'];
				}
			}
		}
		else {
			$all_attribute = array();
		}
		
		$this->attr = array("hot_key"=>$hot_key, "all_category"=>$all_category, "all_brand"=>$all_brand, "all_attribute"=>$all_attribute, "category_str"=>$category_str, "cll"=>$cll_arr, "brand_str"=>$brand_str, "attribute_str"=>$attribute_str);
		//print_r($this->attr);
		
		return $this;
	}
	
	public function ajax() {
		$query = explode(" ", $_GET['keyword']);
		
		$string = "keyword LIKE '%".$query[0]."%'";
		for($i=1; $i<count($query); $i++) {
			if($query[$i] != '') {
				$string .= " AND keyword LIKE '%".$query[$i]."%' ";
			}
		}
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->rows = $dba->query("SELECT keyword FROM search WHERE ".$string." order by `frequency` desc LIMIT 0, 10");
		
		return $this;
	}
}
?>
<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		$error = array();
		if($_GET['keyword'] != '' || $_GET['category'] != '' || $_GET['direct'] != '') {
			$keyword = explode(" ", $_GET['keyword']);
			
			if(count($keyword) > 5) {
				array_push($error, "關鍵字組過多，不能超過5個");
			}
			
			$text_length = 0;
			for($i=0; $i<count($keyword); $i++) {
				if(mb_strlen($keyword[$i]) < 2 && mb_strlen($keyword[$i]) > 0) {
					$text_length++;
				}
			}
			
			if($text_length > 0) {
				array_push($error, "關鍵字過短，不得少於2個字");
			}
			
			require_once "swop/models/search.php";
			$swop = new Main_Models();
			$swop->home();
			
			for($i=0; $i<count($swop->goods_index); $i++) {
				$array_images = $this->json_url_arr($swop->goods_index[$i]['images']);
				
				if(is_array($array_images)) {
					$images->images_array[$i] = $array_images;
				}
				else {
					$images->images_array[$i][] = $swop->goods_index[$i]['images'];
				}
			}
			//print_r($images->images_array);
			
			$now_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			require_once "swop/library/pagination.php";
			$page = new Library_Pagination();
			$page->pagination($swop->num, 5, 20, $now_url, 0, 0, 0, $_GET['page']);
			
			require_once "swop/library/sortby.php";
			$sort = new Sortby_Pagination();
			$sort->sortby($now_url);
		}
		else {
			array_push($error, "未輸入任何關鍵字");
		}
		
		$this->error = $error;
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/search.php";
		include_once "swop/view/template/bottom.php";
	}
	
	public function ajax_append() {
		if($_POST['keyword'] != '' && $_POST['attr'] != '' || $_POST['direct'] != '') {
			require_once "swop/models/search.php";
			$swop = new Main_Models();
			$swop->ajax_append();
			
			if(is_array($swop->attr)) {
				print_r(json_encode($swop->attr));
			}
			else {
				print_r(json_encode(array()));
			}
		}
	}
	
	public function ajax() {
		if($_GET['keyword'] != '') {
			require_once "swop/models/search.php";
			$swop = new Main_Models();
			$swop->ajax();
			
			if(is_array($swop->rows)) {
				print_r(json_encode($swop->rows));
			}
			else {
				print_r(json_encode(array()));
			}
		}
	}
	
	public function arr_url_json($arr) {
	    if(is_array($arr)) {
			$b_key5str = false;
			
			if((bool)count(array_filter(array_keys($arr), 'is_string'))) {
				$b_key5str = true;
			}
			
			foreach($arr as $key => $val) {
				if($b_key5str) {
					$json .= '"'.$key.'":';
				}
				
				if(is_array($val)) {
					$json .= $this->arr_url_json($val).",";
				}
				else if(is_string($val)) {
					$json .= '"'.$val.'",';
				}
				else if(is_numeric($val)) {
					$json .= $val.',';
				}
			}
			
			if($b_key5str) {
				return "{".substr($json,0,-1)."}";
			}
			else {
				return "[".substr($json,0,-1)."]";
			}
		}
		else {
			throw new exception("It's not an array!");
		}
	}
	
	public function json_url_arr($ja) {
	    return json_decode($ja, true);
	}
}
?>
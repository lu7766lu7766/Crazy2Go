<?php
class Library_Pagination {

	public function __construct() {
	}

	public function pagination($count, $show, $number, $url='#', $unhref=0, $type=0, $untools=0, $info_page) { //總數, 顯示幾個分頁, 每頁顯示幾筆, 網址, 不是連結, 型態, 不顯示換頁工具, 目前頁數
		
		/*
		if($_GET['page']!="") {
			$info_page = $_GET['page'];
		}
		else {
			$info_page = $_POST['page'];
		}
		*/
		
		if($info_page != '') {
			$url = str_replace("&page=".$info_page, "", $url);
			$url = str_replace("page=".$info_page, "", $url);
		}
		
		$page_count = ceil($count / $number);
		
		$now = ($info_page==''||$info_page<=0||!is_numeric($info_page))?1:$info_page;
		if($now > $page_count) {$now = $page_count;}
		
		//$page_start = intval($now / $show) * $show + 1;
		//if(($now % $show) == 0) {$page_start = $page_start - $show;}
		//$page_range = ceil($now / $show) * $show;
		//if($page_range > $page_count) {$page_range = $page_count;}
		
		$range_a = ceil($show/2)+1;
		$range_b = ceil($show/2);
		
		if($now < $range_a) {
			$page_start = 1;
		}
		else if($now >= $range_a && $now <= ($page_count - $range_b)) {
			$page_start = $now - (ceil($show/2)-1);
		}
		else if($now > ($page_count - $range_b)) {
			$page_start = $page_count - $show + 1;
			if($page_start <= 0) {
				$page_start = 1;
			}
		}
		
		if($page_count > $show) {
			$page_range = $page_start + $show -1;
		}
		else {
			$page_range = $page_count;
		}
		
		$content = "";
		
		if($unhref == 0) {
			$tag_name = "a";
			$tag_added = " href";
			if(substr_count($url, '?') == 0) {
				$and_attr = "?";
			}
			if(substr($url, -1) == '?' || substr($and_attr, -1) == '?') {
				$and_attr .= "page=";
			}
			else {
				$and_attr .= "&page=";
			}
			
		}
		else {
			$tag_name = "span";
			$tag_added = " class='page' data-page";
			$url = "";
			$and_attr = "";
		}

		if($now != 1 && $count > 0) {
			$content .= "<".$tag_name." id='go_page_up'".$tag_added."='".$url.$and_attr.($now-1)."'><span style='margin:2px; padding-right:6px; border:#898989 solid 1px; width:70px; line-height:24px; display:inline-block; border-radius:5px; color:#717171;'>〈 上一頁</span></".$tag_name.">";
		}
		else {
			$content .= "<span style='margin:2px; padding-right:6px; border:#717171 solid 1px; width:70px; line-height:24px; display:inline-block; border-radius:5px; color:#C7C7C7;'>〈 上一頁</span>";
		}
		
			if($now > ceil($show/2)) {
				$content .= "<span style='margin:2px; border:#fff solid 1px; width:30px; line-height:24px; display:inline-block; border-radius:5px; color:#717171;'>⋯⋯</span>";
			}
		
		for($i=$page_start; $i<=$page_range; $i++) {
			if($i == $now) {
				$content .= "<span style='margin:2px; border:#fff solid 1px; width:30px; line-height:24px; display:inline-block; border-radius:5px; color:red;'>".$i."</span>";
			}
			else {
				$content .= "<".$tag_name.$tag_added."='".$url.$and_attr.$i."'><span style='margin:2px; border:#717171 solid 1px; width:30px; line-height:24px; display:inline-block; border-radius:5px; color:#717171;'>".$i."</span></".$tag_name.">";
			} 
		}
		
			if($now < ($page_count - (floor($show/2)))) {
				$content .= "<span style='margin:2px; border:#fff solid 1px; width:30px; line-height:24px; display:inline-block; border-radius:5px; color:#717171;'>⋯⋯</span>";
			}
		
		if($now != $page_count) {
			$content .= "<".$tag_name." id='go_page_down'".$tag_added."='".$url.$and_attr.($now+1)."'><span style='margin:2px; padding-left:6px; border:#717171 solid 1px; width:70px; line-height:24px; display:inline-block; border-radius:5px; color:#717171;'>下一頁 〉</span></".$tag_name.">";
		}
		else {
			$content .= "<span style='margin:2px; padding-left:6px; border:#717171 solid 1px; width:70px; line-height:24px; display:inline-block; border-radius:5px; color:#C7C7C7;'>下一頁 〉</span>";
		}
		
		if($untools == 0) {
			$content .= "<span style='color:#717171;'>　　共 <span id='page_count' style='color:red;'>".$page_count."</span> 頁　到第 <input type='text' id='jump_page' value='".$now."' style='width:30px; text-align:center; border:#898989 solid 1px;'> 頁　<input type='button' id='jump_buttom' value='確定' style='background:#E61726; color:#fff; border:0; border-radius:0; -moz-appearance:none; -webkit-appearance:none;'></span>";
		}
		
		$this->page_content = "<div style='text-align:center; font-size:8pt;'>".$content."</div>\r\n";
		
		return $this;
	}
}
?>
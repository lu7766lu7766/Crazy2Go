<?php
class Library_Quick {

	public function __construct() {
	}

	public function navigation_top() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		
		$global_item = $dba->query("select * from global_item where `show` = '1' order by `type`, `weights` DESC ");
		
		$top_arr = array();
		$sidebar_arr = array();
		for($i=0; $i<count($global_item); $i++) {
			if($global_item[$i]['type'] == 1) {
				$global_item[$i]['item'] = json_decode($global_item[$i]['item']);
				if($global_item[$i]['item']->url[0] != '') {
					$top_arr[] = "<a href='".$global_item[$i]['item']->url[0]."' style='color:#fff;'><div style='float:left; padding:0 15px 0 15px; font-size:12pt; font-weight:bold;'>".$global_item[$i]['name']."</div></a>";
				}
			}
		}
		$this->content['top'] = implode("<div style='float:left; color:#EF9495;'>|</div>", $top_arr);
				
		return $this;
	}
	
	public function navigation_bottom($url, $img_url) {
		require_once "swop/library/dba.php";
		$dba=new dba();
		
		$global_item = $dba->query("select * from global_item where `show` = '1' order by `type`, `weights` DESC ");
		
		$bottom_arr = array();
		$bottom_second_arr =array();
		for($i=0; $i<count($global_item); $i++) {
			if($global_item[$i]['type'] == 2) {
				$bottom_arr[$global_item[$i]['name']][] = json_decode($global_item[$i]['item']);
			}
			else if($global_item[$i]['type'] == 4) {
				$global_item[$i]['item'] = json_decode($global_item[$i]['item']);
				if($global_item[$i]['item']->url[0] != '') {
					$bottom_second_arr[] = "<a href='".$global_item[$i]['item']->url[0]."'>".$global_item[$i]['name']."</a>";
				}
			}
		}
		$w = 0;
		foreach($bottom_arr as $k => $v) {
			$ba=(array)$v[0];
			if(count($ba['item'])>0 && $w<4) {
				$bottom .= "<div style='float:left; width:189px;'>";
					$bottom .= "<div style='font-size:11pt; font-weight:bold; margin-bottom:6px;'><img src='".$img_url.$ba['icon'][0]."' style='position:relative; top:5px; width:20px; height:20px;'> ".$k."</div>";
					$bottom .= "<div style='margin-left:25px;'>";
					$bottom_str = array();
					
					for($i=0; $i<count($ba['item']); $i++) {
						$bottom_str[] = "<a href='".$url.$ba['url'][$i]."'>".$ba['item'][$i]."</a>";
					}//
					$bottom .= implode("<br />", $bottom_str);
					$bottom .= "</div>";
				$bottom .= "</div>";
			}
			$w++;
		}
		$this->content['bottom'] = $bottom;
		$this->content['bottom_second'] = implode(" ｜ ", $bottom_second_arr);
		
		return $this;
	}
}
?>
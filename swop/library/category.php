<?php
class Library_Category {

	public function __construct() {
	}

	public function menu($url, $img_url) {
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->results = $dba->query("select * from category where `show` = 1 && `delete` = 0 order by `weights` desc");
		
		
		
		
		$menu = "<div id='menu' style='position:relative; top:-10px; opacity:0; width:208px; z-index:50; display:none;'>";
		$content = "<div id='list' style='position:absolute; top:0px; z-index:49;'>";
		
		foreach($this->results as $i => $j) {
			if($j['index'] == 0) {
				
				$menu .= "<div id='menu_sub".$j['fi_no']."' class='menu_sub' data-color='".$j['color']."' style='position:relative; width:208px; height:64px; background-color:rgba(0, 0, 0, 0.85); color:#fff; border-bottom:#39393b solid 1px;'>";
					$menu .= "<div id='menu_span".$j['fi_no']."' style='position:relative; width:170px; margin-left:26px;'>";
						$menu .= "<div style='font-size:12pt; font-weight:bold; padding:9px 0 11px 0; width:170px;'><img src='".$img_url.$j['icon']."' style='width:20px; height:20px; margin-right:11px;'>".$j['name']."</div>";
						$menu .= "<div style='font-size:9pt; color:#c4c2c3; padding-left:28px;'>".$j['subtitle']."</div>";
					$menu .= "</div>";
					$menu .= "<div class='menu_reflect' id='menu_reflect".$j['fi_no']."' style='cursor:pointer; position:absolute; top:0px; left:0px; width:208px; height:64px;'></div>";
				$menu .= "</div>";
				
				$content .= "<div id='list_sub".$j['fi_no']."' class='list_sub' style='position:absolute; font-size:9pt; top:0px; left:198px; background-color:#fff; width:390px; /*display:none;*/ pointer-events:none; opacity:0; box-shadow:3px 3px 3px rgba(0,0,0,0.2);'>";
				foreach($this->results as $k => $l) {
					if($j['fi_no'] == $l['index']) {
						$content .= "<div id='list_sub".$j['fi_no']."' style='padding:12px 28px 12px 28px;'>";
							$content .= "<div id='list_sub".$j['fi_no']."' style='position:relative; height:25px; font-weight:bold;'>";
								$content .= "<div id='list_sub".$j['fi_no']."' style='position:absolute; top:0px; left:0px; border-bottom:#000 solid 1px; width:100%;'>　</div>";
								$content .= "<div id='list_sub".$j['fi_no']."' style='position:absolute; top:0px; left:0px; border-bottom:#fff solid 1px; '>".$l['name']."&nbsp;</div>";
							$content .= "</div>";
							$content .= "<div id='list_sub".$j['fi_no']."' style='color:#414141;'>";
							
						//$tab_lock = '';
						foreach($this->results as $m => $n) {
							if($l['fi_no'] == $n['index']) {
								$content .= "<a href='".$url."search?category=".$n['fi_no']."'><div id='list_sub".$j['fi_no']."' style='float:left;'>".$n['name']."<span class='list_border' data-no='".$j['fi_no']."' style='border-right:1px solid #000; position:relative; top:1px; height:10px; display:inline-block; margin:0 6px 0 6px;'></span></div></a>";
								//$content .= $tab_lock."<a href='".$url."search?category=".$n['fi_no']."'><div id='list_sub".$j['fi_no']."' style='float:left;'>".$n['name']."</div></a>";
								//$tab_lock = "<div id='list_sub".$j['fi_no']."' style='float:left;'> ｜ </div>";
							}
						}
						
						$content .= "<div style='clear:both;'></div></div></div>";
					}
				}
				$content .= "</div>";
				$category_num++;
			}
		}
		
		$menu .= "</div>";
		$content .= "</div>";
		
		
		
		
		$this->category_content = "<div style='position:absolute; height:245px;'>".$menu.$content."</div>\r\n";
		return $this;
	}
}
?>
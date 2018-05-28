<?php
class Library_Search {

	public function __construct() {
	}

	public function engine($img_url) {
		$aurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$search_str = "商品";
		if(strlen($_GET['keyword']) > 0) {
			if(substr_count($aurl, '/search') > 0) {
				$search_str = "商品";
			}
			else if(substr_count($aurl, '/brand') > 0) {
				$search_str = "品牌";
			}
		}
		
		$content = "<div style='/*text-align:center;*/'>";
			$content .= "<form id='search' action='http://www.crazy2go.com/".(($search_str == "商品")?"search":"brand")."' method='get' autocomplete='off'>";
			
				$content .= "<div style='float:left;'>";
					$content .= "<input type='text' id='keyword' name='keyword' value='".$_GET['keyword']."' style='width:424px; height:22px; border:0; border-radius:0; padding:5px;'>";
				$content .= "</div>";

				$content .= "<div style='float:left; width:1px; height:12px; border-left:#BABABA 1px solid; margin-top: 11px;'></div>";
				
				$content .= "<div style='float:left; position:relative; width:72px; height:32px; color:#030303;'>";
					$content .= "<div style='position:absolute; width:72px;'>";
						$content .= "<div id='search_type' style='line-height:32px; font-size: 10.5pt; padding-left:19px;'>".$search_str."</div>";
						$content .= "<div><img src='".$img_url."myaccount_1-3.png' style='position:absolute; left:51px; top:12px; -ms-transform:rotate(90deg); -moz-transform:rotate(90deg); -webkit-transform:rotate(90deg); -o-transform:rotate(90deg); transform:rotate(90deg);'></div>";
					$content .= "</div>";
					
					$content .= "<div style='position:absolute;'><select id='search_select' style='cursor:pointer; width:72px; height:32px; color:#030303; display:inline-block; background:transparent; opacity:0.0;'>";
						$content .= "<option value='1' ".(($search_str == "商品")?"selected":"").">商品</option>";
						$content .= "<option value='2' ".(($search_str == "品牌")?"selected":"").">品牌</option>";
						//$content .= "<!--option value='3'>店家</option-->";
					$content .= "</select></div>";
				$content .= "</div>";

				$content .= "<div style='float:left; background-color:#e61726;'>";
					$content .= "<input type='submit' value='搜索' style='cursor:pointer; width:75px; height:32px; font-size:10.5pt; color:#fff; background: transparent url(".$img_url."search.png) no-repeat 50px 10px; padding-right:15px; border:0; border-radius:0; -moz-appearance:none; -webkit-appearance:none;'>";
				$content .= "</div>";
				$content .= "<div style='clear:both;'></div>";
				
			$content .= "</form>";
			$content .= "<div id='association' style='position:absolute; border:#898989 solid 1px; display:none; z-index:20;'></div>";
		$content .= "</div>\r\n";
		
		$this->search_content = $content;
		return $this;
	}
}
?>
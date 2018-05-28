<?php
class Sortby_Pagination {

	public function __construct() {
	}

	public function sortby($now_url) {
		
		$url_comprehensive = $this->href("comprehensive", $now_url);
		//print_r($url_comprehensive);
		$url_popularity = $this->href("popularity", $now_url);
		//print_r($url_popularity);
		$url_news = $this->href("news", $now_url);
		$url_collect = $this->href("collect", $now_url);
		$url_sales = $this->href("sales", $now_url);
		$url_credit = $this->href("credit", $now_url);
		$url_price = $this->href("price", $now_url);
		
		if( substr_count($url_price['symbol'], "arrow_red") > 0 ) {
			if( substr_count($url_price['symbol'], "transform") > 0 ) {
				//echo "上紅 desc";
				$url_price['symbol'] = '<div style="width:12px; height:12px; text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_red2.png" style="margin-top: 3px;"></div>';
				$url_price['symbol'] .= '<a href="'.$url_price['url'].'" style="text-decoration:blink;"><div style="width:12px; height:11px; text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_deepgray.png" style="-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);"></div></a>';
			}
			else {
				//echo "下紅 asc";
				$url_price['symbol'] = '<a href="'.$url_price['url'].'" style="text-decoration:blink;"><div style="width:12px; height:12px; text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_deepgray.png" style="margin-top: 3px;"></div></a>';
				$url_price['symbol'] .= '<div style="width:12px; height:11px; text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_red2.png" style="-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);"></div>';
			}
		} else {
			//echo "無 desc";
			$url_price['symbol'] = '<a href="'.str_replace('by=desc', 'by=asc', $url_price['url']).'" style="text-decoration:blink;"><div style="width:12px; height:12px; text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_deepgray.png" style="margin-top: 3px;"></div></a>';
			$url_price['symbol'] .= '<a href="'.$url_price['url'].'" style="text-decoration:blink;"><div style="width:12px; height:11px; text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_deepgray.png" style="-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);"></div></a>';
		}
		
		if($_GET['discount']=="0") {
			$url_discount['url'] = str_replace('discount=0', 'discount=1', $now_url);
			$url_discount['checked'] = 'un';
		} else if($_GET['discount']=="1") {
			$url_discount['url'] = str_replace('discount=1', 'discount=0', $now_url);
			$url_discount['checked'] = '';
		}
		else {
			$url_discount['url'] = $now_url.'&discount=1';
			$url_discount['checked'] = 'un';
		}
		
		$content = "";
		
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px; border-style:solid;"><a href="'.$url_comprehensive['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="comprehensive" || $_GET['sort']=="")?" color:#EB313A;":" color:#848484;").'">綜合'.$url_comprehensive['symbol'].'</a></div>';
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;"><a href="'.$url_popularity['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="popularity")?" color:#EB313A;":" color:#848484;").'">人氣'.$url_popularity['symbol'].'</a></div>';
		
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;"><a href="'.$url_news['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="news")?" color:#EB313A;":" color:#848484;").'">新品'.$url_news['symbol'].'</a></div>';
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;"><a href="'.$url_collect['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="collect")?" color:#EB313A;":" color:#848484;").'">收藏'.$url_collect['symbol'].'</a></div>';
		
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;"><a href="'.$url_sales['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="sales")?" color:#EB313A;":" color:#848484;").'">銷量'.$url_sales['symbol'].'</a></div>';
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;"><a href="'.$url_credit['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="credit")?" color:#EB313A;":" color:#848484;").'">信用'.$url_credit['symbol'].'</a></div>';
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;">';
			$content .= '<a href="'.$url_price['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="price")?" color:#EB313A;":" color:#848484;").'"><div style="float:left; margin-left:15px;">價格</div></a>';
			$content .= '<div style="float:left; position:relative;">'.$url_price['symbol'].'</div>';
			$content .= '<div style="clear:both;"></div>';
		$content .= '</div>';
		//<a href="'.$url_price['url'].'" style="text-decoration:blink;'.(($_GET['sort']=="price")?" color:#EB313A;":" color:#848484;").'">
		//</a>
		$content .= '<div style="float:left; width:62px; line-height:23px; background-color:#ffffff; border-color:#d1cfd0; border-width:1px 1px 1px 0; border-style:solid;"><a href="'.$url_discount['url'].'" style="text-decoration:blink;'.(($url_discount['checked']=="")?" color:#EB313A;":" color:#848484;").'"><img src="http://www.crazy2go.com/public/img/goods/'.$url_discount['checked'].'check_button.png" style="position:relative; top:3px; margin-right:3px;">折扣</a></div>';
		$content .= '<div style="clear:both;"></div>';
		
		$this->sort_content = '<div style="text-align:center; font-weight:bold;">'.$content."</div>\r\n";
		
		return $this;
	}
	
	public function href($sort, $now_url) {
		$unsort_array = array('comprehensive', 'popularity', 'news', 'collect', 'sales', 'credit');
		
		if($_GET['sort'] == $sort && $_GET['by'] == 'desc') {
			if(!in_array($_GET['sort'], $unsort_array)) { //綜合、人氣、新品、收藏、銷量、信用沒有遞增排序
				$new['url'] = str_replace('desc', 'asc', $now_url);
			}
			$new['symbol'] = '<img src="http://www.crazy2go.com/public/img/goods/arrow_red.png" style="margin-left:3px; position:relative; top:1px;">';
		}
		else if($_GET['sort'] == $sort && $_GET['by'] == 'asc') {
			$new['url'] = str_replace('asc', 'desc', $now_url);
			if(!in_array($_GET['sort'], $unsort_array)) { //綜合、人氣、新品、收藏、銷量、信用沒有遞增排序
				$new['symbol'] = '<img src="http://www.crazy2go.com/public/img/goods/arrow_red.png" style="margin-left:3px; position:relative; top:1px; -ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);">';
			}
			else {
				$new['symbol'] = '<img src="http://www.crazy2go.com/public/img/goods/arrow_red.png" style="margin-left:3px; position:relative; top:1px;">';
			}
		}
		else if($_GET['sort'] == $sort && $_GET['by'] == '') {
			$new['url'] = $now_url.'&by=asc';
			$new['symbol'] = '<img src="http://www.crazy2go.com/public/img/goods/arrow_red.png" style="margin-left:3px; position:relative; top:1px;">';
		}
		else if($_GET['sort'] != $sort) {
			$new['url'] = $now_url;
			
			if($_GET['sort'] != '') {
				$new['url'] = str_replace($_GET['sort'], $sort, $new['url']);
			}
			else {
				$new['url'] .= '&sort='.$sort;
			}
			
			if($_GET['by'] != '') {
				$new['url'] = str_replace('asc', 'desc', $new['url']);
			}
			else {
				if($_GET['sort'] == '' && $sort == 'comprehensive') {
					$new['url'] .= '&by=asc';
				}
				else {
					$new['url'] .= '&by=desc';
				}
			}
			
			if($_GET['sort'] == '' && $sort == 'comprehensive') {
				$new['symbol'] = '<img src="http://www.crazy2go.com/public/img/goods/arrow_red.png" style="margin-left:3px; position:relative; top:1px;">';
			}
			else {
				$new['symbol'] = '<img src="http://www.crazy2go.com/public/img/goods/arrow_gray2.png" style="margin-left:3px; position:relative; top:1px;">';
			}
		}
		
		return $new;
	}
}
?>
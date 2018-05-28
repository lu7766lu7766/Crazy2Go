<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
                
        $this->js = array('main');
            
		require_once "swop/models/main.php";
		$swop = new Main_Models();
		$swop->home();
		
		
		
		
		
		
		
		
		
		$primary = "<div id='primary_slide' class='primary_slide' style='position:relative;float:left; width:1019px; height:315px;'>";
		foreach($swop->main_item as $k => $l) {
			if($l['type']>1) {
				break;
			}
			if($l['type']!=1) {
				continue;
			}
			$slide_str .= '<a href="'.$l["url"].'"><div style="position:absolute; top:0px; left:0px; z-index:'.(10-$k).'; width:1019px; height:315px; background:url('.$this->base['tpl'].$l["images"].') no-repeat center;"></div></a>';
			$tab_str .= '<span data-color="'.json_decode($l["setting"])->color.'" style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
		}
		$primary .= '<div style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
		$primary .= $slide_str;
		$primary .= "</div>";
		
		$advertising = "<div class='primary_three' style='float:left; width:206px; height:315px; text-align:center;'>";
		foreach($swop->main_item as $k => $l) {
			if($l['type']>2) {
				break;
			}
			if($l['type']!=2) {
				continue;
			}
			$ad_str .= "<a href='".$l['url']."'><div style='width:206px; height:105px; background:url(".$this->base['tpl'].$l["images"].") no-repeat center;'></div></a>";
		}
		$advertising .= $ad_str;
		$advertising .= "</div>";
		
		$focus->advertising_content = "<div style='position:relative; height:315px;'>".$primary.$advertising."</div>\r\n";
		
                
		$content_floors = '';
		if($swop->advertisement[0]['show']==1 && $swop->advertisement[0]['images'] != '') {
			$content_floors .= '<a href="'.$swop->advertisement[0]['url'].'"><div  class="ad_main" style="width:1225px; height:90px; margin:20px 0; display:inline-block; background:url('.$this->base['adm'].$swop->advertisement[0]['images'].')"></div></a>';
		}
		
		$content_floors .= '<div class="minor_three" style="width:1225px;height:210px;">';
		$content_floors .= '<div style="width:33%;float:left;text-align:left;"><a href="'.$swop->main_item[7]['url'].'"><div style="display:inline-block;width:398px;height:210px;background-image:url('.$this->base['tpl'].$swop->main_item[7]['images'].');"></div></a></div>';
		$content_floors .= '<div style="width:34%;float:left;text-align:center;"><a href="'.$swop->main_item[8]['url'].'"><div style="display:inline-block;width:398px;height:210px;background-image:url('.$this->base['tpl'].$swop->main_item[8]['images'].');"></div></a></div>';
		$content_floors .= '<div style="width:33%;float:right;text-align:right;"><a href="'.$swop->main_item[9]['url'].'"><div style="display:inline-block;width:398px;height:210px;background-image:url('.$this->base['tpl'].$swop->main_item[9]['images'].');"></div></a></div>';
		$content_floors .= '</div>';
		
		$content_floors .= '<div class="minor_logo" style="width:1223px;height:66px;margin:20px 0px; display:inline-block; border:#dfdfdf 1px solid;">';
		$content_floors .= '<div style="margin-left:26px; float:left;color:#e71725; font-size:12pt; font-weight:bold; text-align:center;line-height:66px;">熱門品牌</div>';
		$content_floors .= '<div id="brand_left_btn" style="margin-left:70px; float:left;width:30px; height:66px; background:url('.$this->base['tpl'].'arrow_left.png) no-repeat center;"></div>';
		$content_floors .= '<div id="brand_slide" style="width:990px;float:left;overflow:hidden;white-space:nowrap;">';
		foreach($swop->main_item as $k => $l) {
			if($l['type']>4) {
				break;
			}
			if($l['type']!=4) {
				continue;
			}
			$content_floors .= '<a href="'.$l["url"].'"><span style="display:inline-block;width:121px;height:63px;margin:0 20px; background:url('.$this->base['tpl'].$l["images"].') no-repeat center;"></span></a>';
		}
		$content_floors .= '</div>';
		$content_floors .= '<div id="brand_right_btn" style="float:left;width:30px;height:66px; background:url('.$this->base['tpl'].'arrow_right.png) no-repeat center;"></div>';
		$content_floors .= '<div style="clear:both;"></div>';
		$content_floors .= '</div>';

		foreach($swop->floors as $k => $l) {
			$l['item'] = $this->json_url_arr( $l['item'] );
                        
			$content_floors .= '<div class="floor" style="width:1225px; height:530px; margin:20px 0; display:inline-block;">';
			
			$content_floors .= '<div class="floor_left" style="position:relative; float:left; width:208px; height:530px;">';
			$content_floors .= '<div style="position:absolute; left:0px; top:30px; width:208px; height:500px; box-shadow: rgba(0, 0, 0, 0.1) 4px 0px 3px; "></div>';
			$content_floors .= '<div style="position:absolute; left:0px; top:0px; width:208px; height:530px; background-image:url('.$this->base['tpl'].$l['item'][0]['item'][0].');">';
			$content_floors .= '<div style="width:208px; height:160px;"></div>';
			$content_floors .= '<div style="height:251px;">';
			$content_floors .= '<div class="logo_slide_left_button" style="float:left; width:50px; height:251px; background:url('.$this->base['tpl'].'arrow_left.png) no-repeat center;"></div>';
			
			$logos_str='';
			$logos_len = count($l['item'][1]['item']);
			$logos_group_len = 3;
			$len = ceil($logos_len/$logos_group_len)*$logos_group_len;
			for($i=0;$i<$len;$i++) {
				if($i % $logos_group_len == 0) {
					$logos_str.="<div style='text-align:center; line-height:77px;'>";
				}
				if(isset($l['item'][1]['url'][$i])) {
					$logos_str .= '<a href="'.$l['item'][1]['url'][$i].'"><span style="display:block; width:108px;"><img src="'.$this->base['tpl'].$l['item'][1]['item'][$i].'" style="height:80px; max-width:108px; margin-bottom:3px;"></span></a>';
				}
				if($i % $logos_group_len == $logos_group_len-1) {
					$logos_str.="</div>";
				}
			}
			$content_floors .= '<div class="logo_slide" style="position:relative; float:left; width:108px; height:251px; overflow:hidden;">'.$logos_str.'</div>';
			$content_floors .= '<div class="logo_slide_right_button" style="float:left; width:50px; height:251px; background:url('.$this->base['tpl'].'arrow_right.png) no-repeat center;"></div>';
			$content_floors .= '<div style="clear:both;"></div>';
			$content_floors .= '</div>';
			$content_floors .= '<div style="width:208px; height:30px;"></div>';
			$content_floors .= '<div class="floor_classes" style="position:relative; height:87px; border-bottom:#'.$l['color_light'].' solid 2px; text-align:center; color:#'.$l['color_deep'].';">';
			$block_tab = $l['item'][2]['item'];
			$block_url = $l['item'][2]['url'];
			if($block_url[2] == "" && $block_url[3] == "") {
				$content_floors .= '<a href="'.$block_url[0].'"><div class="class_item" style="position:absolute; left:0px; bottom:0px; background-color:rgba(255,255,255,0.5); width:104px; line-height:43px; height:43px;">'.$block_tab[0].'</div></a>';
				$content_floors .= '<a href="'.$block_url[1].'"><div class="class_item" style="position:absolute; right:0px; bottom:0px; background-color:rgba(255,255,255,0.5); width:103px; line-height:43px; height:43px;">'.$block_tab[1].'</div></a>';
			}
			else {
				$content_floors .= '<a href="'.$block_url[0].'"><div class="class_item" style="position:absolute; left:0px; top:0px; background-color:rgba(255,255,255,0.5); width:104px; line-height:43px; height:43px;">'.$block_tab[0].'</div></a>';
				$content_floors .= '<a href="'.$block_url[1].'"><div class="class_item" style="position:absolute; right:0px; top:0px; background-color:rgba(255,255,255,0.5); width:103px; line-height:43px; height:43px;">'.$block_tab[1].'</div></a>';
				$content_floors .= '<a href="'.$block_url[2].'"><div class="class_item" style="position:absolute; left:0px; bottom:0px; background-color:rgba(255,255,255,0.5); width:104px; line-height:43px; height:43px;">'.$block_tab[2].'</div></a>';
				$content_floors .= '<a href="'.$block_url[3].'"><div class="class_item" style="position:absolute; right:0px; bottom:0px; background-color:rgba(255,255,255,0.5); width:103px; line-height:43px; height:43px;">'.$block_tab[3].'</div></a>';
			}
			$content_floors .= '</div>';
			$content_floors .= '</div>';
			$content_floors .= '</div>';
			
			$content_floors .= '<div class="floor_right" style="float:left; width:1017px; height:530px;">';
			$content_floors .= '<div>';
			$content_floors .= '<div style="float:left; width:20%; height:30px; color:#'.$l['color_deep'].'; position:relative;"><a id="'.$l['name'].'"></a><h2 style="position:absolute; top:-12px;">'.$l['name'].'</h2></div>';
			
			
			$category_tab = $l['item'][6]['item'];
			$category_str = '';
			$category_tmp = '';
			for($i=0; $i<count($category_tab); $i++) {
				$category_str .= $category_tmp."<span><a href='".$l['item'][6]['url'][$i]."'>".$category_tab[$i]."</a></span>";
				$category_tmp = "　｜　";
			}
			$content_floors .= '<div class="floor_top_links" style="float:left; width:80%; height:30px; position:relative;"><div style="position:absolute; bottom:3px; right:0px;">'.$category_str.'<span class="floor_top_more" style="margin-left:40px;">更多〉</span></div></div>';
			$content_floors .= '<div style="clear:both;"></div>';
			$content_floors .= '</div>';
			$content_floors .= '<div style="width:1017px; height:495px; border-top:#'.$l['color_deep'].' solid 3px; border-bottom:#'.$l['color_light'].' solid 2px;">';			
			
			
			$content_floors .= '<div class="floor_grids" type="'.$l['type'].'" style="float:left;">';
			$content_floors .= '<div class="floor_grids_top" style="border-bottom:#ECECEC solid 1px;">';
			$content_floors .= '<a href="'.$l['item'][4]['url'][0].'"><div style="float:left; width:173px; height:222px; border-right:#ECECEC solid 1px;background:url('.$this->base['tpl'].$l['item'][4]['item'][0].');"></div></a>';
			$content_floors .= '<a href="'.$l['item'][4]['url'][1].'"><div style="float:left; width:173px; height:222px; border-right:#ECECEC solid 1px;background:url('.$this->base['tpl'].$l['item'][4]['item'][1].');"></div></a>';
			$content_floors .= '<a href="'.$l['item'][4]['url'][2].'"><div style="float:left; width:173px; height:222px; border-right:#ECECEC solid 1px;background:url('.$this->base['tpl'].$l['item'][4]['item'][2].');"></div></a>';
			$content_floors .= '<a href="'.$l['item'][4]['url'][3].'"><div style="float:left; width:173px; height:222px; background:url('.$this->base['tpl'].$l['item'][4]['item'][3].');"></div></a>';
			$content_floors .= '<div style="clear:both;"></div>';
			$content_floors .= '</div>';
			$content_floors .= '<div class="floor_grids_bottom">';
			$content_floors .= '<a href="'.$l['item'][5]['url'][0].'"><div style="float:left; width:173px; height:272px; border-right:#ECECEC solid 1px;background:url('.$this->base['tpl'].$l['item'][5]['item'][0].');"></div></a>';
			$content_floors .= '<a href="'.$l['item'][5]['url'][1].'"><div style="float:left; width:173px; height:272px; border-right:#ECECEC solid 1px;background:url('.$this->base['tpl'].$l['item'][5]['item'][1].');"></div></a>';
			$content_floors .= '<a href="'.$l['item'][5]['url'][2].'"><div style="float:left; width:173px; height:272px; border-right:#ECECEC solid 1px;background:url('.$this->base['tpl'].$l['item'][5]['item'][2].');"></div></a>';
			$content_floors .= '<a href="'.$l['item'][5]['url'][3].'"><div style="float:left; width:173px; height:272px; background:url('.$this->base['tpl'].$l['item'][5]['item'][3].');"></div></a>';
			$content_floors .= '<div style="clear:both;"></div>';
			$content_floors .= '</div>';
			$content_floors .= '</div>';
			
			
			$image_tab = $l['item'][3]['item'];
			$image_url = $l['item'][3]['url'];
			if(count($image_tab) >= 4) {
				$image_tab_num = 4;
			}
			else {
				$image_tab_num = count($image_tab);
			}
			
			
			$content_floors .= '<div class="floor_right_slide" style="float:left; width:322px; height:495px; position:relative;">';
			$tab_str = '';
			for($i=0; $i<$image_tab_num; $i++) {
				$tab_str .= '<span style="display:inline-block;margin:5px;width:14px;height:14px;background:gray;-webkit-border-radius:7px;-moz-border-radius:7px;border-radius:7px;"></span>';
			}
			$content_floors .= '<div style="position:absolute; right:20px; bottom:20px; z-index:99">'.$tab_str.'</div>';
			for($i=0; $i<$image_tab_num; $i++) {
				$content_floors .= '<a href="'.$image_url[$i].'"><div style="position:absolute; top:0px; left:0px; z-index:'.($image_tab_num-$i).'; width:322px; height:495px; background:url('.$this->base['tpl'].$image_tab[$i].') no-repeat center;"></div></a>';
			}
			$content_floors .= '</div>';
			
			
			$content_floors .= '<div style="clear:both;"></div>';
			$content_floors .= '</div>';
			$content_floors .= '</div>';
			$content_floors .= '<div style="clear:both;"></div>';
			$content_floors .= '</div>';
                        
			if($k == 0) {
				if($swop->advertisement[1]['show']==1 && $swop->advertisement[1]['images'] != '') {
					$content_floors .= '<a href="'.$swop->advertisement[1]['url'].'"><div  class="ad_main" style="width:1225px; height:90px; margin:20px 0; display:inline-block; background:url('.$this->base['adm'].$swop->advertisement[1]['images'].')"></div></a>';
				}
			}
		}
                
        if($swop->advertisement[2]['show']==1 && $swop->advertisement[2]['images'] != '') {
        	$content_floors .= '<a href="'.$swop->advertisement[2]['url'].'"><div  class="ad_main" style="width:1225px; height:90px; margin:20px 0; display:inline-block; background:url('.$this->base['adm'].$swop->advertisement[2]['images'].')"></div></a>';
        }
                
		$floors->floors_content = $content_floors."\r\n";
		
		/*
		require_once "swop/library/category.php";
		$category = new Library_Category();
		$category->menu($this->base['tpl']);
		*/
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/main.php";
		include_once "swop/view/template/bottom.php";
		return $this;
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
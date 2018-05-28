<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
            
                if(!isset($_GET["no"]) || $_GET["no"]=="")
                {
                    header("Location:".$this->base["url"]);
                    exit();
                }
            
		$this->js = array('goods','jquery.cookie');
		
		require_once "swop/models/goods.php";
		$swop = new Main_Models();
		$swop->home();
                
                $cPathSegments = array();
                $cid = $swop->goods[0]["category"];
                $cLen = count($swop->category);
                $swop->category = array_reverse($swop->category);
                for($i = 0; $i < $cLen; $i++)
                {
                    if($swop->category[$i]["fi_no"] == $cid)
                    {
                        array_push($cPathSegments, $swop->category[$i]["name"]);
                        $cid = $swop->category[$i]["index"];
                    }
                }
                $cPathSegments = array_reverse($cPathSegments);
                $cPathSegments[0] = "<span style='color:#EE7A8E;font-size:12pt;'>".$cPathSegments[0]."</span>";
                //array_push($cPathSegments, $swop->store[0]["name"]);
                array_push($cPathSegments, $swop->goods[0]["name"]);
                $cPathSegments = implode(" > ", $cPathSegments);
                $this->cPath = "<span style='font-size:8pt;font-weight:bold;'>".$cPathSegments."</span>";
		
                if($swop->goods[0]['specifications'])
                {
                    $type['item'] = $this->json_url_arr($swop->goods[0]['specifications']);
                }
                else
                {
                    $type['item'] = array();
                }
                if($swop->goods[0]['inventory'])
                {
                    $type['stock'] = $this->json_url_arr($swop->goods[0]['inventory']);
                }
                else
                {
                    $type['stock'] = array();
                }
				
		$content_style = "";
                $hideMode = false;
		foreach($type['item'] as $k => $l) {
                        $hide = ($hideMode = ($k == "default"))?" style='display:none;' ":"";
			$content_style .= "<div class='tr type' data-type='".$k."' data-count='".count($type['item'][$k])."' ".$hide."><div class='td' style='padding:10px 10px 10px 10px;color:#969B9C;font-size:8pt;'>".$k."</div><div class='td'  style='padding:10px 10px 10px 10px;'>";
			for($j=0; $j<count($type['item'][$k]); $j++) {
				$content_style .= "<div class='item' data-item='".$k."' data-no='".$j."' data-val='".$type['item'][$k][$j]."' data-select='0' data-stock='m' style='cursor:pointer;margin:5px; padding:5px; border:#898989 solid 1px; float:left;-webkit-border-radius: 2px;-moz-border-radius: 2px;border-radius: 2px;'>".$type['item'][$k][$j]."</div>";
			}
			$content_style .= "<div style='clear:both;'></div></div></div>";
		}
		
		$str = implode("+",$type['stock']);
		$content_total = eval("return ".$str." ;");
		$content_stock = implode(",",$type['stock']);
                
                if($swop->combination_inventory!=0)
                {
                    $content_total = $swop->combination_inventory;
                    $content_stock = 0;
                }
		
		$content_style .= "<div class='tr'>";
		$content_style .= "<div class='td' style='padding:10px 10px 10px 10px;color:#969B9C;font-size:8pt;'>商品數量</div>";
		$content_style .= "<div class='td' style='padding:10px 10px 10px 10px;'><div style='display:inline-block;height:20px;border:1px solid #BFBFBF;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;'>";
		$content_style .= "<input type='text' id='number' value='1' style='margin-left:3px;position:relative;top:2px;height:15px;border:0px;width:40px;'>";
		$content_style .= "<div id='number_plus' style='position:relative;top:-6px;cursor:pointer;border-left:1px solid #BFBFBF;display:inline-block;width:20px;height:21px;text-align:center;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='position:relative;top:6px;-ms-transform:rotate(0deg); -moz-transform:rotate(0deg); -webkit-transform:rotate(0deg); -o-transform:rotate(0deg); transform:rotate(0deg);'></div>";
		$content_style .= "<div id='number_minus' style='position:relative;top:-6px;cursor:pointer;border-left:1px solid #BFBFBF;display:inline-block;width:20px;height:21px;text-align:center;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='position:relative;top:7px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'></div>";
		$content_style .= "</div><div id='stock' style='display:inline-block;margin-left:20px;color:#969B9C;font-size:8pt;' data-type='".count($type['item'])."' data-stock='".$content_stock."' data-total='".$content_total."' data-select='".($hideMode?"0；0；default；default":"")."' data-select_stock='".($hideMode?$content_total:0)."'> ( 庫存： ".$content_total." 件 ) </div>";
		$content_style .= "</div>";
		$content_style .= "</div>\r\n";
		
		$style->style_content = $content_style;
		
		$array_images = $this->json_url_arr($swop->goods[0]['images']);
		if(is_array($array_images)) {
			$images->images_array = $array_images;
		}
		else {
			$images->images_array[] = $swop->goods[0]['images'];
		}

		$swop->goods[0]['related'] = $this->json_url_arr($swop->goods[0]['related']);
                
                require_once "swop/library/tidyhtml.php";
                $tidy = new Library_Tidyhtml();
                $tidy->purifier($swop->goods[0]['introduction']);
                $swop->goods[0]['introduction'] = $tidy->tidyhtml_content;
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/goods.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
        
        public function ajax_records() {
            if($_POST['fi_no'] != ''){
                require_once "swop/models/goods.php";
                $swop = new Main_Models();
                $swop->ajax_records();
                $goods["records"] = $swop->goods_record; 
                $this->echo_json("0", "", 0, $goods);
            }
        }
	
	public function ajax_evaluate() {
		if($_POST['fi_no'] != '' && $_POST['page'] != '') {
			require_once "swop/models/goods.php";
			$swop = new Main_Models();
			$swop->ajax_evaluate();
			
			
			$goods['evaluate_good'] = 0;
			$goods['evaluate_bad'] = 0;
			for($i=0; $i<count($swop->evaluate); $i++) {
				if($swop->evaluate[$i]['score'] >= 4) {
					$goods['evaluate_good']++;
				}
				else {
					$goods['evaluate_bad']++;
				}
			}
			
			$goods['evaluate_count'] = count($swop->evaluate);
			
                        $len = count($swop->goods_evaluate);
                        for($i = 0; $i < $len; $i++)
                        {
                            foreach($swop->members as $k=>$v)
                            {
                                if($swop->goods_evaluate[$i]["member"] == $v["fi_no"])
                                {
                                    $swop->goods_evaluate[$i]["name"] = $v["name"];
                                    $swop->goods_evaluate[$i]["picture"] = $v["picture"];
                                    break;
                                }
                            }
                        }
			$goods['evaluate'] = $swop->goods_evaluate;

			if($_POST['evaluate_select'] == "good") {
				$page_count = count($goods['evaluate_good']);
			}
			else if($_POST['evaluate_select'] == "bad") {
				$page_count = count($goods['evaluate_bad']);
			}
			else {
				$page_count = count($swop->evaluate);
			}
			require_once "swop/library/pagination.php";
			$page = new Library_Pagination();
			$page->pagination($page_count, 5, 10, '', 1, 0, 1, $_POST['page']);
			$goods['page_content'] = $page->page_content;
			
					
			for($i=0; $i<count($swop->goods_respond); $i++) {
				$goods['respond'][$swop->goods_respond[$i]['evaluate']][] = $swop->goods_respond[$i];
			}
			//print_r($goods);
			
			$this->echo_json("0", "", 0, $goods);
		}
	}
	
	public function ajax_related() {
		if($_POST['fi_no_news'] != '' && $_POST['fi_no_hots'] != '') {
			require_once "swop/models/goods.php";
			$swop = new Main_Models();
			$swop->ajax_related();

			$this->echo_json("0", "", 0, $swop->goods_index);
		}
	}
        
        public function ajax_collect_store() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '') {
			require_once "swop/models/goods.php";
			$swop = new Main_Models();
			$swop->ajax_collect_store();
			
			$this->echo_json($swop->massage[0], $swop->massage[1], $swop->massage[2]);
		}
	}
        
        public function check_on($ajax=0) {
		if($_SESSION['info'] == "") {
			if($ajax == 1) {
				$this->echo_json("1", "請登入會員", 1);
			}
			else {
				header('Location: http://www.crazy2go.com/member/');
			}
			
			exit;
		}
	}
	
	public function echo_json($error="-1", $message="nil", $exit=0, $add=array()) {
		$results["error"] = $error;
		$results["message"] = $message;
		
		if(is_array($add) && count($add)>0) {
			foreach($add as $k => $v) {
				$results['add'][$k] = $v;
			}
		}
		
		echo json_encode($results);
		
		if($exit==0) {
			exit;
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
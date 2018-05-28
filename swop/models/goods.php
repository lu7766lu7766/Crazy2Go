<?php
class Main_Models {

	public function __construct() {
            
	}

	public function home() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->goods = $dba->query("select * from goods_info where fi_no = '".$_GET['no']."' ");
                
                if($_SESSION['admin']['check'] != 1)
                if($this->goods[0]['status_audit']==0 || $this->goods[0]['status_shelves']==0)
                {
                    if(isset($_SESSION["backend"]["login_store"]))
                    {
                        if($this->goods[0]['store'] != $_SESSION["backend"]["login_store"])
                        {
                            header("Location:http://www.crazy2go.com/");
                        }
                    }
                    else
                    {
                        header("Location:http://www.crazy2go.com/");
                    }
                }

                if($this->goods[0]['attribute']=="")
                {
                    $attr = array();
                }
                else
                {
                    $attr = $this->json_url_arr($this->goods[0]['attribute']);
                }
                $attribute_array = array();
                $attribute_item_array = array();
                foreach($attr as $k => $v) {
                        $attribute_array[] = $k;
                        foreach($v as $m => $n) {
                                if($n!='') {
                                        $attribute_item_array[] = $n;
                                }
                        }
                }

                $this->attribute = $dba->query("select * from attribute where fi_no IN (".join(",",$attribute_array).") ");

                $this->attribute_item = $dba->query("select * from attribute_item where fi_no IN (".join(",",$attribute_item_array).") ");

                for($i=0; $i<count($this->attribute_item); $i++) {
                        $attribute_item[$this->attribute_item[$i]['attribute']][] = $this->attribute_item[$i]['item'];
                }

                $this->attribute_item = $attribute_item;
                
		//{"1":["1",""],"5":["13","14",""]}
                $this->store = $dba->query("select * from store where fi_no = '".$this->goods[0]['store']."' ");
		
		$this->goods_transaction_times = $dba->query("select * from goods_index where `store` = '".$this->goods[0]['store']."' order by `transaction_times` desc limit 0, 18 ");
                $this->goods_latest = $dba->query("select * from goods_index where `store` = '".$this->goods[0]['store']."' order by `added_date` desc limit 0, 18 ");
		
		$this->store_advertisement = $dba->query("select * from store_advertisement where `store` = '".$this->goods[0]['store']."' && type in(3,4) and `delete`=0 order by `weights` desc limit 0, 6 ");
		
                $this->category = $dba->query("select fi_no,name,`index` from category");
                
                $this->exchange_rate = $dba->query("select * from exchange_rate order by date desc LIMIT 0,1");
                $this->exchange_rate = $this->exchange_rate[0]["rate"];

                //組合商品計算
                $this->combination_inventory = 0; 
                if($this->goods[0]["combination"]!="")
                {
                    $combination = json_decode($this->goods[0]["combination"],true);
                    $comb_len = count($combination[0]["fi_no"]);
                    $fi_nos = implode(",",$combination[0]["fi_no"]);
                    $combination_data = $dba->query("select fi_no, inventory from goods_index where fi_no in (".$fi_nos.") order by field(fi_no,".$fi_nos.")");
                    for($i = 0; $i < $comb_len; $i++)
                    {
                        $inventory[$i] = json_decode($combination_data[$i]["inventory"],true);
                        $inventory[$i] = floor($inventory[$i][$combination[0]["inventory"][$i]]/$combination[0]["quantity"][$i]);
                    }
                    //取商品數量最小值
                    $this->combination_inventory = min($inventory);
                }
                
		return $this;
	}
        
        public function ajax_records() {
                require_once "swop/library/dba.php";
		$dba = new dba();
                if(isset($_SESSION["info"]["fi_no"]))
                {
                    $history = explode(",",$_POST['fi_no']);
                    $history = $history[0];
                    $is_today_viewed = $dba->query("select goods from goods_history where time_history LIKE '".date("Y-m-d")."%' and goods=".$history." and member=".$_SESSION["info"]["fi_no"]);
                    if(!isset($is_today_viewed[0]["goods"]))
                    {
                        $dba->query("insert into goods_history(member,goods,time_history)values(".$_SESSION["info"]["fi_no"].",".$history.",NOW())");
                    }
                }
                
                $this->goods_record = $dba->query("select fi_no,`name`, `images`, `price`, `promotions`, `discount` from goods_index where fi_no IN (".$_POST['fi_no'].") order by FIELD(fi_no,".$_POST['fi_no'].")");
        }
	
	public function ajax_evaluate() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		if($_POST['page'] != "") {
			$now_page = ($_POST['page']-1)*10;
		}
		else {
			$now_page = 0;
		}
		
		if($_POST['evaluate_select'] == "good") {
			$evaluate_select = "&& `score` >= 4";
		}
		else if($_POST['evaluate_select'] == "bad") {
			$evaluate_select = "&& `score` <= 3";
		}
		else {
			$evaluate_select = "";
		}
		
		if($_POST['orderby'] == "evaluate") {
			$orderby = "score";
		}
		else {
			$orderby = "evaluate_date";
		}
		
		$this->evaluate = $dba->query("select fi_no, score from goods_evaluate where goods = '".$_POST['fi_no']."' ");
		
		$this->goods_evaluate = $dba->query("select * from goods_evaluate where goods = '".$_POST['fi_no']."' ".$evaluate_select." order by `".$orderby."` desc limit ".$now_page.", 10");
		//print_r($this->goods_evaluate);
		for($i=0; $i<count($this->goods_evaluate); $i++) {
                        $members[] = $this->goods_evaluate[$i]['member'];
			if($this->goods_evaluate[$i]['respond'] == 1) {
				$goods_respond_array[] = $this->goods_evaluate[$i]['fi_no'];
			}
		}
		$members = implode(",",$members==null?array():array_unique($members));
                $this->members = $dba->query("select fi_no, name, picture from member_index where fi_no in (".$members.")");
                
		if(count($goods_respond_array) != 0) {
			$this->goods_respond = $dba->query("select * from goods_respond where goods = '".$_POST['fi_no']."' && evaluate IN (".join(",",$goods_respond_array).") && `delete`=0 order by `respond_date` desc");
		}
		else {
			$this->goods_respond = array();
		}
		
		return $this;
	}
	
	public function ajax_related() {
		require_once "swop/library/dba.php";
		$dba = new dba();
                
		$this->goods_index[0] = $dba->query("select `name`, `images`, `price`, `promotions`, `discount`, `free_shipping`, `transaction_times`, `evaluation_number`, `evaluation_score` from goods_index where fi_no IN (".$_POST['fi_no_news'].") ");
                $this->goods_index[1] = $dba->query("select `name`, `images`, `price`, `promotions`, `discount`, `free_shipping`, `transaction_times`, `evaluation_number`, `evaluation_score` from goods_index where fi_no IN (".$_POST['fi_no_hots'].") ");
                
		return $this;
	}
        
        public function ajax_collect_store() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		$count_collect_store = $dba->query("SELECT `store` FROM collect_store WHERE `member` = ".$_SESSION['info']['fi_no']." ");
		
		$fi_no_check = 0; //過濾重複的收藏商品
		for($j=0; $j<count($count_collect_store); $j++) {
			if($_POST['fi_no'] == $count_collect_store[$j]['store']) {
				$fi_no_check++;
			}
		}
		
		if((count($count_collect_store)+1) <= 100 && $fi_no_check == 0) {
			$collect_store = $dba->query("INSERT INTO collect_store (`fi_no`, `member`, `store`) VALUES (NULL, '".$_SESSION['info']['fi_no']."', '".$_POST['fi_no']."') ");
			$this->massage = array("0", "收藏成功!", 1);
		}
		else if($fi_no_check >= 1) {
			$this->massage = array("1", "收藏重複", 1);
		}
		else {
			$this->massage = array("2", "收藏已滿", 1);
		}
		
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
<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		$this->check_on();

		//echo $_SESSION['info']['cart'];
		$this->js = array('cart');
		if($_SESSION['info']['cart'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->home();
			//print_r($swop->goods);
			
			$cart_array = array();
			$cart_num = array();
			$cart_stock = array();
			$cart_type = array();
			$cart_item = array();
			$cart_fi_no = array();
			$cart_value = array();
			$_SESSION['info']['cart'] = $swop->member_index[0]['cart'];
			$cart = explode("｜", $_SESSION['info']['cart']);
			
			for($i=1; $i<count($cart); $i++) {
				$cart_array_split = explode("；", $cart[$i]);
				$cart_num[$cart_array_split[0]] .= $cart_array_split[1]."∵";
				
				$cart_array_temp = urldecode($cart_array_split[2]);
				$cart_array[$cart_array_split[0]] .= $cart_array_temp."∵";
				
				$cart_tiem_split = explode("；", $cart_array_temp);
				$cart_stock[$cart_array_split[0]] .= $cart_tiem_split[0]."∵";
				$cart_type[$cart_array_split[0]] .= $cart_tiem_split[2]."∵";
				$cart_item[$cart_array_split[0]] .= $cart_tiem_split[3]."∵";
				
				$cart_fi_no[] = $cart_array_split[0];
				$cart_value[] = $cart_array_split[3];
			}
			$this->cart_array = $cart_array;
			$this->cart_num = $cart_num;
			$this->cart_stock = $cart_stock;
			$this->cart_type = $cart_type;
			$this->cart_item = $cart_item;
			/*
			print_r($cart_array);
			print_r($cart_num);
			print_r($cart_stock);
			print_r($cart_type);
			print_r($cart_item);
			*/

			//print_r($swop->goods_combination);
			$cart_check = array();
			$cart_change = array();
			if(count($swop->goods) > 0) {
				for($i=0; $i<count($swop->goods); $i++) {
					$swop->goods[$i]['volumetric_weight'] = $this->json_url_arr($swop->goods[$i]['volumetric_weight']);
					$swop->goods[$i]['inventory'] = $this->json_url_arr($swop->goods[$i]['inventory']);
					
					//echo "[".$swop->goods[$i]['fi_no']."]\r\n";
					$cart_stock_fi_no = explode("∵", $cart_stock[$swop->goods[$i]['fi_no']]);
					$cart_num_fi_no = explode("∵", $cart_num[$swop->goods[$i]['fi_no']]);
					for($j=0; $j<count($cart_stock_fi_no)-1; $j++) {
						if($swop->goods[$i]['combination'] == "") {
							if($swop->goods[$i]['inventory'][$cart_stock_fi_no[$j]] >= $cart_num_fi_no[$j]) {
								$cart_check[$swop->goods[$i]['fi_no']] .= $swop->goods[$i]['inventory'][$cart_stock_fi_no[$j]]."∵";
								if(is_array($swop->goods_combination[$swop->goods[$i]['fi_no']])) {
									$goods_combination_inventory = $this->json_url_arr($swop->goods_combination[$swop->goods[$i]['fi_no']]["inventory"]);
									$goods_combination_inventory[$cart_stock_fi_no[$j]] = $goods_combination_inventory[$cart_stock_fi_no[$j]] - $cart_num_fi_no[$j];
									$swop->goods_combination[$swop->goods[$i]['fi_no']]["inventory"] = $this->arr_url_json($goods_combination_inventory);
								}
							}
							else {
								$cart_check[$swop->goods[$i]['fi_no']] .= "out"."∵";
							}
						}
						else {
							$combination_arr = $this->json_url_arr($swop->goods[$i]['combination']);
							$combination_err = 0;
							$combination_inv = array();
							for($k=0; $k<count($combination_arr[0]["fi_no"]); $k++) {
								/*
								$combination_exp = explode("；", $combination_arr[0]["specifications"][$k]);
								$combination_num = 0;
								$combination_fit = array();
								foreach($combination_arr[($k+1)] as $ke => $va) {
									$combination_fit[] = array("ke"=>$ke,"va"=>$va);
								}
								$specifications_arr = $this->json_url_arr($swop->goods_combination[$combination_arr[0]["fi_no"][$k]]["specifications"]);
								foreach($specifications_arr as $ke => $va) {
									//echo "資料庫規格：".$ke." / ".$va[$combination_exp[$combination_num]];
									//echo "組合商品規格：".$combination_fit[$combination_num]["ke"]." / ".$combination_fit[$combination_num]["va"][0];
									if($ke != $combination_fit[$combination_num]["ke"] || $va[$combination_exp[$combination_num]] != $combination_fit[$combination_num]["va"][0]) {
										$combination_err++;
									}
									$combination_num++;
								}
								*/
								$inventory_arr = $this->json_url_arr($swop->goods_combination[$combination_arr[0]["fi_no"][$k]]["inventory"]);
								//$combination_inv[] = $inventory_arr[$combination_arr[0]["inventory"][$k]];
								if($inventory_arr[$combination_arr[0]["inventory"][$k]] < ($combination_arr[0]["quantity"][$k]*$cart_num_fi_no[$j]) ) {
									//echo $combination_arr[0]["fi_no"][$k].":".$inventory_arr[$combination_arr[0]["inventory"][$k]]." < ".($combination_arr[0]["quantity"][$k]*$cart_num_fi_no[$j])."<br />";
									$combination_err++;
								}
								//echo "資料庫庫存：".$inventory_arr[$combination_arr[0]["inventory"][$k]];
							}
							if($combination_err == 0) {
								$cart_check[$swop->goods[$i]['fi_no']] .= $swop->goods[$i]['inventory'][$cart_stock_fi_no[$j]]."∵";
							}
							else {
								$cart_check[$swop->goods[$i]['fi_no']] .= "out"."∵";
							}
						}
					}
					$type['item'] = $this->json_url_arr($swop->goods[$i]['specifications']);
					$type['stock'] = $swop->goods[$i]['inventory'];
					
					foreach($type['item'] as $k => $l) {
						for($j=0; $j<count($type['item'][$k]); $j++) {
							$style_content[$swop->goods[$i]['fi_no']][$k][] = $type['item'][$k][$j];
						}
					}
					
					$swop->goods[$i]['images'] = $this->json_url_arr($swop->goods[$i]['images']);
					
					for($j=0; $j<count($cart_fi_no); $j++) {
						if($cart_fi_no[$j] == $swop->goods[$i]['fi_no']) {
							$now_value = ($swop->goods[$i]['discount']!=0)?$swop->goods[$i]['discount']:$swop->goods[$i]['promotions'];
							if($cart_value[$j] > $now_value) {
								$cart_change[] = $swop->goods[$i]['name']."已經從 <span style='color:#DB2718;'><span style='font-family:Arial;'>¥</span>".$cart_value[$j]."</span> 下跌到 <span style='color:#DB2718;'><span style='font-family:Arial;'>¥</span>".$now_value."</span> ！";
							}
							else if($cart_value[$j] < $now_value) {
								$cart_change[] = $swop->goods[$i]['name']."已經從 <span style='color:#DB2718;'><span style='font-family:Arial;'>¥</span>".$cart_value[$j]."</span> 上漲到 <span style='color:#DB2718;'><span style='font-family:Arial;'>¥</span>".$now_value."</span> ！";
							}
						}
					}
					$cart_category[] = $swop->goods[$i]['category'];
					
				}
			}
			$this->style_content = $style_content;
			$this->cart_check = $cart_check;
			$this->cart_change = $cart_change;
			$this->cart_category = $cart_category;
			//print_r($style_content);
			//print_r($cart_check);
			//print_r($cart_change);
		}
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/cart.php";
		include_once "swop/view/template/bottom.php";
	}
	
	public function ajax_other_love() {
		$this->check_on(1);
		
		if($_POST['fi_no']) {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_other_love();

			$this->echo_json("0", "", "0", $swop->goods_index);
		}
	}
	
	public function ajax_other_collect() {
		$this->check_on(1);

		require_once "swop/models/cart.php";
		$swop = new Main_Models();
		$swop->ajax_other_collect();
		
		$this->echo_json("0", "", "0", $swop->goods_index);
	}
	
	public function ajax_added() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '' && $_POST['number'] != '' && $_POST['select'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_added();
			
			$this->echo_json($swop->massage[0], $swop->massage[1]);
		}
	}
	
	//立刻購買
	public function ajax_nowed() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '' && $_POST['number'] != '' && $_POST['select'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_nowed();
			
			$this->echo_json($swop->massage[0], $swop->massage[1]);
		}
	}
	
	public function ajax_editor() {
		$this->check_on(1);
				
		if($_POST['fi_no'] != '' && $_POST['number'] != '' && $_POST['select'] != '' && count($edit_fi_no) == count($edit_number) && count($edit_fi_no) == count($edit_select)) {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_editor();
			//print_r($swop->goods);
			
			$this->echo_json($swop->massage[0], $swop->massage[1], $swop->massage[2], $swop->massage[3]);
		}
	}
	
	public function ajax_change() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '' && $_POST['number'] != '' && $_POST['number'] > 0 && $_POST['select'] != '' && $_POST['change'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_change();
			
			$this->echo_json($swop->massage[0], $swop->massage[1]);
		}
	}
	
	public function ajax_delete() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '' && $_POST['select'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_delete();
			
			$this->echo_json($swop->massage[0], $swop->massage[1]);
		}
	}
	
	public function ajax_batch_delete() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '' && $_POST['select'] != '') {
			$arr_fi_no = explode("∵", $_POST['fi_no']);
			$arr_select = explode("∵", $_POST['select']);
			
			if(count($arr_fi_no) == count($arr_select)) { //防雷
				require_once "swop/models/cart.php";
				$swop = new Main_Models();
				$swop->ajax_batch_delete();
				
				$this->echo_json($swop->massage[0], $swop->massage[1]);
			}
		}
	}
	
	public function ajax_collect() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '' && $_POST['select'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_collect();
			
			$this->echo_json($swop->massage[0], $swop->massage[1], $swop->massage[2]);
		}
	}
	
	public function ajax_collect_goods() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_collect_goods();
			
			$this->echo_json($swop->massage[0], $swop->massage[1], $swop->massage[2]);
		}
	}
	
	public function ajax_batch_collect() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_batch_collect();
			
			$this->echo_json($swop->massage[0], $swop->massage[1], $swop->massage[2], $swop->massage[3]);
		}
	}
	
	public function ajax_shipping_fee() {
		$this->check_on(1);
		
		if($_POST['type'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_shipping_fee();
			
			$this->echo_json($swop->massage[0], $swop->massage[1], $swop->massage[2], $swop->massage[3]);
		}
	}
	
	public function ajax_postal_province() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_postal_province();
			
			echo json_encode($swop->postal_province);
		}
	}
	
	public function ajax_postal_street() {
		$this->check_on(1);
		
		if($_POST['fi_no'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->ajax_postal_street();
			
			echo json_encode($swop->postal_street);
		}
	}
	
	public function ajax_submit_order() {
		$this->check_on(1);
		
		if($_POST['choose'] != '') {
			//echo $_SESSION['info']['cart'];
			$cart = explode("｜", $_SESSION['info']['cart']);
			//print_r($cart);
			for($i=1; $i<=count($cart); $i++) {
				$cart_iris = explode("；",  urldecode($cart[$i]));
				$cart_new[$i] = $cart_iris;
			}
			
			$new_choose = "";
			$choose = explode("∴", $_POST['choose']);
			for($i=0; $i<count($choose)-1; $i++) {/////////////錯誤
				for($j=1; $j<count($cart_new); $j++) {
					//echo $choose[$i]." == ".($cart_new[$j][0]."×".$cart_new[$j][2])."\r\n";
					if($choose[$i] == ($cart_new[$j][0]."×".$cart_new[$j][2])) {
						$new_choose .= $cart[$j]."｜";
					}
				}
			}
			$_SESSION['choose'] = $new_choose;
			
			if($_SESSION['choose'] != '') {
				$this->echo_json("0");
			}
		}
	}
	
	public function checkout() {
		$this->check_on();
		
		$this->js = array('cart');
		if($_SESSION['choose'] != '') {
			require_once "swop/models/cart.php";
			$swop = new Main_Models();
			$swop->checkout();
			
			$cart_array = array();
			$cart_num = array();
			$cart_stock = array();
			$cart_type = array();
			$cart_item = array();
			$cart = explode("｜", $_SESSION['choose']);
			
			for($i=0; $i<count($cart)-1; $i++) {
				$cart_array_split = explode("；", $cart[$i]);
				$cart_num[$cart_array_split[0]] .= $cart_array_split[1]."∵";
				
				$cart_array_temp = urldecode($cart_array_split[2]);
				$cart_array[$cart_array_split[0]] .= $cart_array_temp."∵";
				
				$cart_tiem_split = explode("；", $cart_array_temp);
				$cart_stock[$cart_array_split[0]] .= $cart_tiem_split[0]."∵";
				$cart_type[$cart_array_split[0]] .= $cart_tiem_split[2]."∵";
				$cart_item[$cart_array_split[0]] .= $cart_tiem_split[3]."∵";
			}
			$this->cart_array = $cart_array;
			$this->cart_num = $cart_num;
			$this->cart_stock = $cart_stock;
			$this->cart_type = $cart_type;
			$this->cart_item = $cart_item;
			
			$cart_check = array();
			for($i=0; $i<count($swop->goods); $i++) {
				$swop->goods[$i]['volumetric_weight'] = $this->json_url_arr($swop->goods[$i]['volumetric_weight']);
				$swop->goods[$i]['inventory'] = $this->json_url_arr($swop->goods[$i]['inventory']);
				
				$cart_stock_fi_no = explode("∵", $cart_stock[$swop->goods[$i]['fi_no']]);
				$cart_num_fi_no = explode("∵", $cart_num[$swop->goods[$i]['fi_no']]);
				for($j=0; $j<count($cart_stock_fi_no)-1; $j++) {
					if($swop->goods[$i]['combination'] == "") {
						if($swop->goods[$i]['inventory'][$cart_stock_fi_no[$j]] >= $cart_num_fi_no[$j]) {
							$cart_check[$swop->goods[$i]['fi_no']] .= $swop->goods[$i]['inventory'][$cart_stock_fi_no[$j]]."∵";
							if(is_array($swop->goods_combination[$swop->goods[$i]['fi_no']])) {
								$goods_combination_inventory = $this->json_url_arr($swop->goods_combination[$swop->goods[$i]['fi_no']]["inventory"]);
								$goods_combination_inventory[$cart_stock_fi_no[$j]] = $goods_combination_inventory[$cart_stock_fi_no[$j]] - $cart_num_fi_no[$j];
								$swop->goods_combination[$swop->goods[$i]['fi_no']]["inventory"] = $this->arr_url_json($goods_combination_inventory);
							}
						}
						else {
							$cart_check[$swop->goods[$i]['fi_no']] .= "out"."∵";
						}
					}
					else {
						$combination_arr = $this->json_url_arr($swop->goods[$i]['combination']);
						$combination_err = 0;
						$combination_inv = array();
						for($k=0; $k<count($combination_arr[0]["fi_no"]); $k++) {
							$inventory_arr = $this->json_url_arr($swop->goods_combination[$combination_arr[0]["fi_no"][$k]]["inventory"]);
							//$combination_inv[] = $inventory_arr[$combination_arr[0]["inventory"][$k]];
							if($inventory_arr[$combination_arr[0]["inventory"][$k]] < ($combination_arr[0]["quantity"][$k]*$cart_num_fi_no[$j]) ) {
								$combination_err++;
							}
							//echo "資料庫庫存：".$inventory_arr[$combination_arr[0]["inventory"][$k]];
						}
						//echo min($combination_inv)." >= ".$cart_num_fi_no[$j];
						//if(min($combination_inv) >= $cart_num_fi_no[$j]) {
						if($combination_err == 0) {
							$cart_check[$swop->goods[$i]['fi_no']] .= $swop->goods[$i]['inventory'][$cart_stock_fi_no[$j]]."∵";
						}
						else {
							$cart_check[$swop->goods[$i]['fi_no']] .= "out"."∵";
						}
					}
				}
				$type['item'] = $this->json_url_arr($swop->goods[$i]['specifications']);
				$type['stock'] = $swop->goods[$i]['inventory'];
				
				foreach($type['item'] as $k => $l) {
					for($j=0; $j<count($type['item'][$k]); $j++) {
						$style_content[$swop->goods[$i]['fi_no']][$k][] = $type['item'][$k][$j];
					}
				}
				
				$swop->goods[$i]['images'] = $this->json_url_arr($swop->goods[$i]['images']);
			}
			$this->style_content = $style_content;
			$this->cart_check = $cart_check;
			//print_r($cart_check);
		}
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/cart_checkout.php";
		include_once "swop/view/template/bottom.php";
	}
	
	public function ajax_submit_checkout() {
		$this->check_on(1);
		
		$img_type = array("image/jpg","image/jpeg","image/bmp","image/gif","image/png");
		
		if($_POST['consignee'] == "" || $_POST['postal_code'] == "" || $_POST['province'] == "" || $_POST['nprovince'] == "" || $_POST['ncity'] == "" || $_POST['ndistrict'] == "" || $_POST['address'] == "" || 
			($_POST['contact_mobile_international'] == "" && $_POST['contact_mobile_number'] != "") ||
			(($_POST['contact_phone_international'] == "" || $_POST['contact_phone_area'] == "") && $_POST['contact_phone_number'] != "") ||
			($_POST['aidentity'] == "1" && ($_FILES['aidentity_front']['error'] > 0 || $_FILES['aidentity_back']['error'] > 0) ) ||
			$_POST['ashipping_select'] == "") {
			$this->echo_json("2", "欄位不得空白");
		}
		
		if( ($_POST['contact_mobile_number'] != "" && !is_numeric($_POST['contact_mobile_international']) ) ||
			($_POST['contact_phone_number'] != "" && ( !is_numeric($_POST['contact_phone_international']) || !is_numeric($_POST['contact_phone_area']) ) ) ||
			($_POST['aidentity'] == "1" && (!in_array($_FILES['aidentity_front']['type'], $img_type) || !in_array($_FILES['aidentity_back']['type'], $img_type)) ) ||
			($_POST['aidentity'] == "1" && (($_FILES['aidentity_front']['size']/1024/1024) > 2 || ($_FILES['aidentity_back']['size']/1024/1024) > 2) ) ||
			($_POST['aidentity'] == "1" && (($_FILES['aidentity_front']['size']/1024/1024) <= 0 || ($_FILES['aidentity_back']['size']/1024/1024) <= 0) )  ) {
			$this->echo_json("3", "欄位格式錯誤");
		}
		
		if( $_SESSION['choose'] == "" ) {
			$this->echo_json("4", "結帳清單已失效");
		}

		require_once "swop/models/cart.php";
		$swop = new Main_Models();
		$swop->ajax_submit_checkout($this->base['ide_dir']);
		
		if($swop->status == 'on') {
			$this->echo_json("0", "訂單成立");
			$_SESSION['choose'] = '';
		}
		else if($swop->status == 'off') {
			$this->echo_json("1", "庫存不足");
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
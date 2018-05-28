<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
		require_once "swop/library/dba.php";
		$dba = new dba();
				
		$this->member_index = $dba->query("SELECT * FROM member_index WHERE `fi_no`='".$_SESSION['info']['fi_no']."' ");
		$_SESSION['info']['cart'] = $this->member_index[0]['cart'];
		$cart_no = array();
		$cart = explode("｜", $_SESSION['info']['cart']);
		for($i=1; $i<count($cart); $i++) {
			$cart_split = explode("；", $cart[$i]);
			/*if($_GET['ax']!='') {
				echo "編號　".$cart_split[0]."<br /><br />";
				echo "數量　".$cart_split[1]."<br /><br />";
				echo "庫存編號；風格編號；風格名稱；風格項目　".urldecode($cart_split[2])."<br /><br />";
				echo "價格　".$cart_split[3]."<br /><br /><br />";
			}*/
			$cart_no[] = $cart_split[0];
		}
		
		
		//echo "SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $cart_no).")  ORDER BY `store` ASC, `fi_no` ASC ";
		$goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $cart_no).")  ORDER BY `store` ASC, `fi_no` ASC ");
		$this->goods = $goods;
		
		$cart_store = array();
		$cart_store_check = '';
		$cart_category = array();
		$combination_arr = array();
		for($i=0; $i<count($goods); $i++) {
			$cart_category[] = $goods[$i]['category'];
			if($goods[$i]['store'] != $cart_store_check) {
				$cart_store_check = $goods[$i]['store'];
				array_push($cart_store, $goods[$i]['store']);
			}
			if($goods[$i]['combination'] != "") {
				$combination_json = $this->json_url_arr($goods[$i]['combination']);
				$combination_arr = array_merge($combination_arr, $combination_json[0]["fi_no"]);
			}
		}
		//echo "SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $combination_arr).")  ORDER BY `store` ASC, `fi_no` ASC ";
		$combination = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $combination_arr).")  ORDER BY `store` ASC, `fi_no` ASC ");
		$goods_combination = array();
		for($i=0; $i<count($combination); $i++) {
			$goods_combination[$combination[$i]["fi_no"]] = $combination[$i];
		}
		$this->goods_combination = $goods_combination;
		
		$this->store = $dba->query("SELECT * FROM store WHERE `fi_no` IN (".join(",",$cart_store).") ");
		
		//$this->exchange_rate = $dba->query("select * from exchange_rate order by `date` desc limit 0,1");
		
		$this->recommend = $dba->query("SELECT `fi_no`, `name`, `images`, `discount`, `promotions` FROM goods_index WHERE `category` IN (".implode(",", $cart_category).") && `status_audit`='1' && `status_shelves`='1' order by `added_date` desc limit 0,10");
		
		return $this;
	}
	
	public function ajax_other_love() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$category = $dba->query("SELECT * FROM category");
		
		
		$fi_no = explode(",", $_POST['fi_no']);
		//print_r($fi_no);
		
		$all_index = array();
		for($i=0; $i<count($category); $i++) {
			for($j=0; $j<count($fi_no); $j++) {
				if($category[$i]['fi_no'] == $fi_no[$j]) {
					$all_index[] = $category[$i]['index'];
				}
			}
		}
		//print_r($all_index);
		
		$all_category = array();
		for($i=0; $i<count($category); $i++) {
			for($j=0; $j<count($all_index); $j++) {
				if($category[$i]['index'] == $all_index[$j]) {
					$all_category[] = $category[$i]['fi_no'];
				}
			}
		}
		
		$this->goods_index = $dba->query("SELECT `fi_no`, `name`, `images`, `discount`, `promotions` FROM goods_index WHERE `category` IN (".implode(",", $all_category).") && `status_audit`='1' && `status_shelves`='1' order by `transaction_times` desc limit 0,10");
	}
	
	public function ajax_other_collect() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$collect_goods = $dba->query("SELECT `goods` FROM collect_goods WHERE `member` = ".$_SESSION['info']['fi_no']." order by `fi_no` desc limit 0,10");
		
		$all_goods = array();
		for($i=0; $i<count($collect_goods); $i++) {
			$all_goods[] = $collect_goods[$i]['goods'];
		}
		
		$this->goods_index = $dba->query("SELECT `fi_no`, `name`, `images`, `discount`, `promotions` FROM goods_index WHERE `fi_no` IN (".implode(",", $all_goods).") && `status_audit`='1' && `status_shelves`='1' ");
	}
	
	public function ajax_added() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no`='".$_POST['fi_no']."' && `status_audit`='1' && `status_shelves`='1' ");
		$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
		
		
		
		if($this->goods[0]["combination"]!="") {
			$check_com = $this->json_url_arr($this->goods[0]["combination"]);
			$chech_inv = $check_com[0]["fi_no"];
		}
		else {
			$chech_inv[] = $this->goods[0]["fi_no"];
		}
		
		
		
		$old_cart = explode("｜", $this->member_index[0]['cart']);
		$old_cart_plus = array();
		$old_fino = array();
		for($i=1; $i<count($old_cart); $i++) {
			$old_cart_split = explode("；", $old_cart[$i]);
			$old_cart_split[2] = urldecode($old_cart_split[2]);
			$old_cart_plus[$old_cart_split[0]] = $old_cart_split;
			if($old_cart_split[0]!=$_POST['fi_no']) { //排除本身
				$old_fino[] = $old_cart_split[0];
			}
		}
		$goods_cart = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $old_fino).") && `status_audit`='1' && `status_shelves`='1' ");
		
		
		
		
		$deduction = array();
		for($i=0; $i<count($goods_cart); $i++) {
			if($goods_cart[$i]["combination"]!="") {
				$combination = $this->json_url_arr($goods_cart[$i]["combination"]);
				for($j=0; $j<count($combination[0]["fi_no"]); $j++) {
					if(in_array($combination[0]["fi_no"][$j], $chech_inv)) { //如果有使用到該產品
						//0；
						//1；1；
						$inventory = $combination[0]["inventory"][$j];
						
						//$old_cart_plus[$goods_cart[$i]["fi_no"]][1] //購買的組數
						//$combination[0]["fi_no"][$j] //該產品的fi_no
						//$combination[0]["quantity"][$j] //該產品組合商品數量
						$deduction[$combination[0]["fi_no"][$j]][$inventory] += $old_cart_plus[$goods_cart[$i]["fi_no"]][1] * $combination[0]["quantity"][$j];
					}
				}
			}
			else {
				if(in_array($goods_cart[$i]["fi_no"], $chech_inv)) { //如果有使用到該產品
					//庫存編號；風格編號；風格名稱；風格項目
					//0；0；default；default
					//3；1｜1；defaulta｜defaultb；defaulta2｜defaultb2
					$type = explode("；", $old_cart_plus[$goods_cart[$i]["fi_no"]][2]);
					$inventory = $type[0];
					
					//$old_cart_plus[$goods_cart[$i]["fi_no"]][1] //購買的組數
					//$goods_cart[$i]["fi_no"] //該產品的fi_no
					$deduction[$goods_cart[$i]["fi_no"]][$inventory] += $old_cart_plus[$goods_cart[$i]["fi_no"]][1];
				}
			}
		}
		/*
		Array
		(
		    [1] => Array
		        (
		            [0] => 2
		        )
		
		    [2] => Array
		        (
		            [0] => 1
		        )
		
		    [1222] => Array
		        (
		            [0] => 1
		            [3] => 12
		        )
		
		)
		*/
		//print_r($deduction);
		
		
		
		
		if(count($this->goods)>0) {
				
				$post_type_check = 0;
				if($this->goods[0]['combination'] != "") {
					$combination_arr = $this->json_url_arr($this->goods[0]['combination']); //組合商品
					$combination = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $combination_arr[0]["fi_no"]).")  ORDER BY `store` ASC, `fi_no` ASC ");
					
					$goods_combination = array();
					for($i=0; $i<count($combination); $i++) {
						$goods_combination[$combination[$i]["fi_no"]] = $combination[$i];
					}
					
					for($i=0; $i<count($combination_arr[0]["fi_no"]); $i++) {
						$inventory = $this->json_url_arr($goods_combination[$combination_arr[0]["fi_no"][$i]]["inventory"]); //庫存
						$deduction_num = $deduction[$combination_arr[0]["fi_no"][$i]][$combination_arr[0]["inventory"][$i]];
						$deduction_num = ($deduction_num!="")?$deduction_num:0;
						$combination_inv[] = floor(( $inventory[$combination_arr[0]["inventory"][$i]] - $deduction_num) / $combination_arr[0]["quantity"][$i]);
						
						$specifications = $this->json_url_arr($goods_combination[$combination_arr[0]["fi_no"][$i]]["specifications"]); //規格
							
						$com_specifications = $combination_arr[($i+1)];
						$com_specifications_pos = explode("；", $combination_arr[0]["specifications"][$i]);
						$com_specifications_num = 0;
						foreach($com_specifications as $k => $v) {
							if($specifications[$k][$com_specifications_pos[$com_specifications_num]] != $v[0]) {
								$post_type_check++;
							}
							$com_specifications_num++;
						}
					}
					
					$select[0] = 0;
					$inventory[0] = min($combination_inv);
				}
				else {
					$specifications = $this->json_url_arr($this->goods[0]['specifications']); //規格
					//print_r($specifications);
					$inventory = $this->json_url_arr($this->goods[0]['inventory']); //庫存
					//print_r($inventory);
					
					$select = explode("；", $_POST['select']); //15；1｜1；顏色｜尺碼；天藍色｜190/110(XXXL)
					$select_no = explode("｜", $select[1]);
					$select_type = explode("｜", $select[2]);
					$select_item = explode("｜", $select[3]);
					
					$deduction_num = $deduction[$this->goods[0]['fi_no']][$select[0]];
					$deduction_num = ($deduction_num!="")?$deduction_num:0;
					$inventory[$select[0]] = $inventory[$select[0]] - $deduction_num;
					
					for($i=0; $i<count($select_no); $i++) {
						if($specifications[$select_type[$i]][$select_no[$i]] != $select_item[$i]) {
							$post_type_check++; 
						}
					}
				}
				
				
				if($post_type_check == 0) { //規格
					//echo $inventory[$select[0]];
					if($inventory[$select[0]] > 0) { //庫存
						$new_cart = '';
						//$old_cart = explode("｜", $this->member_index[0]['cart']);
						if(count($old_cart)>=100 && $old_cart[1] != '') { //購物車已滿
							$check_no_inventory = 1;
						}
						else if(count($old_cart)>1 && $old_cart[1] != '') {
							$check_cart = 0;
							$check_no_inventory = 0;
							
							for($i=1; $i<count($old_cart); $i++) { //歷遍購物車商
								$old_cart_split = explode("；", $old_cart[$i]);
								
								if($old_cart_split[0] == $_POST['fi_no'] && urldecode($old_cart_split[2]) == $_POST['select']) { //相同產品編號與規格
									//echo "[".$inventory[$select[0]]." >= ".($old_cart_split[1]+$_POST['number'])."]";
									if($inventory[$select[0]] >= ($old_cart_split[1]+$_POST['number'])) { //並且庫存足夠
										$new_cart .= "｜".$old_cart_split[0]."；".($old_cart_split[1]+$_POST['number'])."；".urlencode($_POST['select'])."；".(($this->goods[0]['discount']!=0)?$this->goods[0]['discount']:$this->goods[0]['promotions']);
									}
									else { //但庫存不足，不動作
										$new_cart .= "｜".$old_cart[$i];
										$check_no_inventory++;
									}
									$check_cart++;
								}
								else {
									$new_cart .= "｜".$old_cart[$i];
								}
							}
							
							if($check_cart == 0) { //購物車無此商品
								$new_cart .= "｜".$_POST['fi_no']."；".$_POST['number']."；".urlencode($_POST['select'])."；".(($this->goods[0]['discount']!=0)?$this->goods[0]['discount']:$this->goods[0]['promotions']);
							}
						}
						else { //空的購物車
							if($inventory[$select[0]] >= $_POST['number']) { //並且庫存足夠
								$new_cart = "｜".$_POST['fi_no']."；".$_POST['number']."；".urlencode($_POST['select'])."；".(($this->goods[0]['discount']!=0)?$this->goods[0]['discount']:$this->goods[0]['promotions']);
							}
							else { //但庫存不足，不動作
								$check_no_inventory++;
							}
						}
						
						
						
						if($check_no_inventory == 0) {
							//setcookie("cart", $new_cart, time()+3600*24);
							$_SESSION['info']['cart'] = $new_cart;
							$member_index = $dba->query("UPDATE member_index SET `cart` = '".$new_cart."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
							
							$this->massage = array("0", "加入成功");
						}
						else if($check_no_inventory == 1) {
							$this->massage = array("0", "超過庫存數量");
						}
						else {
							$this->massage = array("1", "庫存不足");
						}
					}
					else {
						$this->massage = array("1", "庫存不足");
					}
				}
				else {
					$this->massage = array("1", "規格錯誤");
				}
			}
			else {
				$this->massage = array("1", "查無此商品");
			}
		
		return $this;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function ajax_nowed() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no`='".$_POST['fi_no']."' && `status_audit`='1' && `status_shelves`='1' ");
		
		if(count($this->goods)>0) {
			$post_type_check = 0;
			if($this->goods[0]['combination'] != "") {
				$combination_arr = $this->json_url_arr($this->goods[0]['combination']); //組合商品
				$combination = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $combination_arr[0]["fi_no"]).")  ORDER BY `store` ASC, `fi_no` ASC ");
				
				$goods_combination = array();
				for($i=0; $i<count($combination); $i++) {
					$goods_combination[$combination[$i]["fi_no"]] = $combination[$i];
				}
				
				for($i=0; $i<count($combination_arr[0]["fi_no"]); $i++) {
					$inventory = $this->json_url_arr($goods_combination[$combination_arr[0]["fi_no"][$i]]["inventory"]); //庫存
					//$combination_inv[] = $inventory[$combination_arr[0]["inventory"][$i]];
					$combination_inv[] = floor($inventory[$combination_arr[0]["inventory"][$i]] / $combination_arr[0]["quantity"][$i]);
					
					$specifications = $this->json_url_arr($goods_combination[$combination_arr[0]["fi_no"][$i]]["specifications"]); //規格
						
					$com_specifications = $combination_arr[($i+1)];
					$com_specifications_pos = explode("；", $combination_arr[0]["specifications"][$i]);
					$com_specifications_num = 0;
					foreach($com_specifications as $k => $v) {
						if($specifications[$k][$com_specifications_pos[$com_specifications_num]] != $v[0]) {
							$post_type_check++;
						}
						$com_specifications_num++;
					}
				}
				
				$select[0] = 0;
				$inventory[0] = min($combination_inv);
			}
			else {
				$specifications = $this->json_url_arr($this->goods[0]['specifications']); //規格
				//print_r($specifications);
				$inventory = $this->json_url_arr($this->goods[0]['inventory']); //庫存
				//print_r($inventory);
				
				$select = explode("；", $_POST['select']); //15；1｜1；顏色｜尺碼；天藍色｜190/110(XXXL)
				$select_no = explode("｜", $select[1]);
				$select_type = explode("｜", $select[2]);
				$select_item = explode("｜", $select[3]);
				
				for($i=0; $i<count($select_no); $i++) {
					if($specifications[$select_type[$i]][$select_no[$i]] != $select_item[$i]) {
						$post_type_check++; 
					}
				}
			}
			
			if($post_type_check == 0) {
				if($inventory[$select[0]] > 0) {
					$_SESSION['choose'] = $_POST['fi_no']."；".$_POST['number']."；".urlencode($_POST['select'])."；".(($this->goods[0]['discount']!=0)?$this->goods[0]['discount']:$this->goods[0]['promotions'])."｜";
					$this->massage = array("0", "");
				}
				else {
					$this->massage = array("1", "庫存不足");
				}
			}
			else {
				$this->massage = array("1", "規格錯誤");
			}
		}
		else {
			$this->massage = array("1", "查無此商品");
		}
		
		return $this;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function ajax_editor() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		$edit_fi_no = explode("∵", $_POST['fi_no']);
		$edit_number = explode("∵", $_POST['number']);
		$edit_select = explode("∵", $_POST['select']);
		
		for($i=0; $i<count($edit_fi_no)-1; $i++) {
			$fi_no = explode("\_", $edit_fi_no[$i]);
			if(is_numeric($fi_no[0]) && $fi_no[0] != '' && is_numeric($fi_no[1]) && $fi_no[1] != '') {
				$edit[$fi_no[0]][$fi_no[1]]["number"] = $edit_number[$i];
				$edit[$fi_no[0]][$fi_no[1]]["select"] = $edit_select[$i];
			}
			if(is_numeric($fi_no[0]) && $fi_no[0] != '') {
				$arr_fi_no[] = $fi_no[0];
			}
		}
		//print_r($edit);

		$this->goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".join(",",$arr_fi_no).") && `status_audit`='1' && `status_shelves`='1' ");
		$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
		
		if(count($this->goods)>0) {
			
				$combination_arr = array();
				for($a=0; $a<count($this->goods); $a++) {
					$this->goods[$a]['combination'] = $this->json_url_arr($this->goods[$a]['combination']); //組合商品
					$combination_arr = array_merge($combination_arr, $this->goods[$a]['combination'][0]["fi_no"]);
				}
				$combination = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $combination_arr).")  ORDER BY `store` ASC, `fi_no` ASC ");
				
				$goods_combination = array();
				for($i=0; $i<count($combination); $i++) {
					$goods_combination[$combination[$i]["fi_no"]] = $combination[$i];
				}
				
				
				
				$old_cart = explode("｜", $this->member_index[0]['cart']);
				$edit_arr = array();
				
				for($a=0; $a<count($this->goods); $a++) {
					foreach($edit[$this->goods[$a]['fi_no']] as $k => $v) {
								$p_fi_no = $this->goods[$a]['fi_no'];
								$p_number = $v['number'];
								$p_select = $v['select'];
								
								$log_fi_no = $this->goods[$a]['fi_no']."_".$k;
						
								if($p_fi_no != '' && $p_number != '' && $p_number > 0 && $p_select != '') {
									$post_type_check = 0;
									if($this->goods[$a]['combination'] != "") {
										$combination_arr = $this->goods[$a]['combination']; //組合商品
																				
										for($i=0; $i<count($combination_arr[0]["fi_no"]); $i++) {
											$inventory = $this->json_url_arr($goods_combination[$combination_arr[0]["fi_no"][$i]]["inventory"]); //庫存
											//$combination_inv[] = $inventory[$combination_arr[0]["inventory"][$i]];
											$combination_inv[] = floor($inventory[$combination_arr[0]["inventory"][$i]] / $combination_arr[0]["quantity"][$i]);
											
											$specifications = $this->json_url_arr($goods_combination[$combination_arr[0]["fi_no"][$i]]["specifications"]); //規格
												
											$com_specifications = $combination_arr[($i+1)];
											$com_specifications_pos = explode("；", $combination_arr[0]["specifications"][$i]);
											$com_specifications_num = 0;
											foreach($com_specifications as $ke => $va) {
												if($specifications[$ke][$com_specifications_pos[$com_specifications_num]] != $va[0]) {
													$post_type_check++;
												}
												$com_specifications_num++;
											}
											
										}
										
										$select[0] = 0;
										$inventory[0] = min($combination_inv);
									}
									else {
										$specifications = $this->json_url_arr($this->goods[$a]['specifications']); //規格
										$inventory = $this->json_url_arr($this->goods[$a]['inventory']); //庫存
										
										//$edit_arr[$log_fi_no]['stock'] = implode(",", $inventory);
										
										$select = explode("；", $p_select); //15；1｜1；顏色｜尺碼；天藍色｜190/110(XXXL)
										$select_no = explode("｜", $select[1]);
										$select_type = explode("｜", $select[2]);
										$select_item = explode("｜", $select[3]);
										
										for($i=0; $i<count($select_no); $i++) {
											if($specifications[$select_type[$i]][$select_no[$i]] != $select_item[$i]) {
												$post_type_check++; 
											}
										}
									}
									
									if($post_type_check == 0) {
										
										if($inventory[$select[0]] > 0) {
											$check_no_inventory = 0;
											
											for($i=1; $i<count($old_cart); $i++) {
												$old_cart_split = explode("；", $old_cart[$i]);
												if($old_cart_split[0] == $p_fi_no && urldecode($old_cart_split[2]) == $p_select) { //相同產品編號與規格
													if($inventory[$select[0]] >= $p_number) { //並且庫存足夠
														$old_cart[$i] = $old_cart_split[0]."；".$p_number."；".urlencode($p_select)."；".(($this->goods[$a]['discount']!=0)?$this->goods[$a]['discount']:$this->goods[$a]['promotions']);
													}
													else { //但庫存不足，不動作
														$check_no_inventory++;
													}
													
												}
											}
											
											if($check_no_inventory == 0) {
												$edit_arr[$log_fi_no] = "0"; //修改成功
											}
											else {
												$edit_arr[$log_fi_no] = "1"; //庫存不足
											}
										}
										else {
											$edit_arr[$log_fi_no] = "1"; //庫存不足
										}
									}
									else {
										$edit_arr[$log_fi_no] = "1"; //規格錯誤
									}
								}
								else if($p_fi_no != '' && $p_number != '' && $p_number <= 0) {
									for($i=1; $i<count($old_cart); $i++) {
										$old_cart_split = explode("；", $old_cart[$i]);
										if($old_cart_split[0] == $p_fi_no && urldecode($old_cart_split[2]) == $p_select) {
											unset($old_cart[$i]);
										}
									}
									$edit_arr[$log_fi_no] = "2"; //刪除成功
								}
					}
				}

				$message_edit = 0;
				$message_stock = 0;
				$message_remove = 0;
				
				foreach($edit_arr as $k => $v) {
					switch($v) {
						case "0":
							$message_edit++;
							break;
						case "1":
							$message_stock++;
							break;
						case "2":
							$message_remove++;
							break;
					}
				}
				
				$edit_count = count($edit_arr);
				
				if($message_stock != $edit_count && $message_remove != $edit_count) {
					$message_str .= "修改成功";
				}
				
				if($message_stock > 0) {
					if($message_stock == $edit_count) {
						$message_str .= "庫存不足";
					}
					else {
						$message_str .= "，部分庫存不足";
					}
				}
				
				if($message_remove > 0) {
					if($message_remove == $edit_count) {
						$message_str .= "刪除成功";
					}
					else {
						$message_str .= "，部分刪除成功";
					}
				}
				
				//setcookie("cart", implode("｜", $old_cart), time()+3600*24);
				$_SESSION['info']['cart'] = implode("｜", $old_cart);
				$member_index = $dba->query("UPDATE member_index SET `cart` = '".implode("｜", $old_cart)."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
				$this->massage = array("0", $message_str, 0, $edit_arr);
		}
		else {
			$this->massage = array("1", "查無此商品");
		}
		return $this;
	}
	
	public function ajax_change() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no` = '".$_POST['fi_no']."' && `status_audit`='1' && `status_shelves`='1' ");
		$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
		
		if(count($this->goods)>0) {
				$specifications = $this->json_url_arr($this->goods[0]['specifications']); //規格
				$inventory = $this->json_url_arr($this->goods[0]['inventory']); //庫存
				
				$change = explode("；", $_POST['change']); //15；1｜1；顏色｜尺碼；天藍色｜190/110(XXXL)
				$change_no = explode("｜", $change[1]);
				$change_type = explode("｜", $change[2]);
				$change_item = explode("｜", $change[3]);
				
				$post_type_check = 0;
				for($i=0; $i<count($change_no); $i++) {
					if($specifications[$change_type[$i]][$change_no[$i]] != $change_item[$i]) {
						$post_type_check++; 
					}
				}
				
				if($post_type_check == 0) {
					if($inventory[$change[0]] > 0) {
						$new_cart = '';
						$old_cart = explode("｜", $this->member_index[0]['cart']);
						
						for($i=1; $i<count($old_cart); $i++) {
							$old_cart_split = explode("；", $old_cart[$i]);
							if($old_cart_split[0] == $_POST['fi_no'] && urldecode($old_cart_split[2]) == $_POST['select']) { //相同產品編號與規格
								if($inventory[$change[0]] >= $_POST['number']) { //並且庫存足夠
									$new_cart .= "｜".$old_cart_split[0]."；".$_POST['number']."；".urlencode($_POST['change'])."；".(($this->goods[0]['discount']!=0)?$this->goods[0]['discount']:$this->goods[0]['promotions']);
								}
								else { //但庫存不足，不動作
									$new_cart .= "｜".$old_cart[$i];
									$check_no_inventory++;
								}
							}
							else {
								$new_cart .= "｜".$old_cart[$i];
							}
						}
						
						if($check_no_inventory == 0) {
							//setcookie("cart", $new_cart, time()+3600*24);
							$_SESSION['info']['cart'] = $new_cart;
							$member_index = $dba->query("UPDATE member_index SET `cart` = '".$new_cart."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
							$this->massage = array("0", "修改成功");
						}
						else {
							$this->massage = array("1", "庫存不足");
						}
					}
					else {
						$this->massage = array("1", "庫存不足");
					}
				}
				else {
					$this->massage = array("1", "庫存不足");
				}
		}
		else {
			$this->massage = array("1", "查無此商品");
		}
		return $this;
	}
	
	public function ajax_delete() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
		
		$new_cart = '';
		$old_cart = explode("｜", $this->member_index[0]['cart']);
		//print_r($old_cart);
		$check_cart = 0;
		for($i=1; $i<count($old_cart); $i++) {
			$old_cart_split = explode("；", $old_cart[$i]);
			//echo $old_cart_split[0]." != ".$_POST['fi_no']." || ".urldecode($old_cart_split[2])." != ".$_POST['select']."\r\n";
			if($old_cart_split[0] == $_POST['fi_no'] && urldecode($old_cart_split[2]) == $_POST['select']) {
				$check_cart = 1;
			}
			else {
				$new_cart .= "｜".$old_cart[$i];
			}
		}
		
		if($check_cart == 1) {
			//setcookie("cart", $new_cart, time()+3600*24);
			$_SESSION['info']['cart'] = $new_cart;
			$member_index = $dba->query("UPDATE member_index SET `cart` = '".$new_cart."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
			$this->massage = array("0", "刪除成功");
		}
		else {
			$this->massage = array("1", "購物車無此商品");
		}
		return $this;
	}
	
	public function ajax_batch_delete() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
		
		$arr_fi_no = explode("∵", $_POST['fi_no']);
		$arr_select = explode("∵", $_POST['select']);
		
		$new_cart = '';
		$old_cart = explode("｜", $this->member_index[0]['cart']);
		
		$check_cart = 0;
		for($i=1; $i<count($old_cart); $i++) {
			$old_cart_split = explode("；", $old_cart[$i]);
			$new_cart_check = 0;
			
			for($j=0; $j<count($arr_fi_no); $j++) {
				if($old_cart_split[0] == $arr_fi_no[$j] && urldecode($old_cart_split[2]) == $arr_select[$j]) {
					$new_cart_check++;
				}
			}
			
			if($new_cart_check == 0) {
				$new_cart .= "｜".$old_cart[$i];
			}
			else {
				$check_cart++;
			}
		}
		
		if($check_cart == count($arr_fi_no)) {
			//setcookie("cart", $new_cart, time()+3600*24);
			$_SESSION['info']['cart'] = $new_cart;
			$member_index = $dba->query("UPDATE member_index SET `cart` = '".$new_cart."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
			$this->massage = array("0", "刪除成功");
		}
		else {
			$this->massage = array("1", "購物車無此商品");
		}
		
		return $this;
	}
	
	public function ajax_collect() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		$count_collect_goods = $dba->query("SELECT `goods` FROM collect_goods WHERE `member` = ".$_SESSION['info']['fi_no']." ");
		
		$fi_no_check = 0; //過濾重複的收藏商品
		for($j=0; $j<count($count_collect_goods); $j++) {
			if($_POST['fi_no'] == $count_collect_goods[$j]['goods']) {
				$fi_no_check++;
			}
		}
		
		if((count($count_collect_goods)+1) <= 100 && $fi_no_check == 0) {
			$goods_index = $dba->query("SELECT * FROM goods_index WHERE `fi_no` = '".$_POST['fi_no']."' && `status_audit`='1' && `status_shelves`='1' ");
			if(count($goods_index)>0) {
				$added_date = date('Y-m-d');
				$collect_goods = $dba->query("INSERT INTO collect_goods (`fi_no`, `member`, `goods`, `added_date`, `delete`) VALUES (NULL, '".$_SESSION['info']['fi_no']."', '".$goods_index[0]['fi_no']."', '".$added_date."', '0') ");
				
				$this->massage = array("0", "收藏成功", 1);
				$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
				
				$new_cart = '';
				$old_cart = explode("｜", $this->member_index[0]['cart']);
				
				for($i=1; $i<count($old_cart); $i++) {
					$old_cart_split = explode("；", $old_cart[$i]);
					if($old_cart_split[0] != $_POST['fi_no'] || urldecode($old_cart_split[2]) != $_POST['select']) {
						$new_cart .= "｜".$old_cart[$i];
					}
				}
				$_SESSION['info']['cart'] = $new_cart;
				$member_index = $dba->query("UPDATE member_index SET `cart` = '".$new_cart."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
			}
			else {
				$this->massage = array("1", "查無此商品", 1);
			}
		}
		else if($fi_no_check >= 1) {
			$this->massage = array("2", "收藏重複", 1);
		}
		else {
			$this->massage = array("3", "收藏已滿", 1);
		}
		
		return $this;
	}
	
	public function ajax_collect_goods() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		$count_collect_goods = $dba->query("SELECT `goods` FROM collect_goods WHERE `member` = ".$_SESSION['info']['fi_no']." ");
		//print_r($count_collect_goods);
		
		$fi_no_check = 0; //過濾重複的收藏商品
		for($j=0; $j<count($count_collect_goods); $j++) {
			if($_POST['fi_no'] == $count_collect_goods[$j]['goods']) {
				$fi_no_check++;
			}
		}
		
		if((count($count_collect_goods)+1) <= 100 && $fi_no_check == 0) {
			$goods_index = $dba->query("SELECT * FROM goods_index WHERE `fi_no` = '".$_POST['fi_no']."' && `status_audit`='1' && `status_shelves`='1' ");
			
			if(count($goods_index) > 0) {
				
				$added_date = date('Y-m-d');
				
				$collect_goods = $dba->query("INSERT INTO collect_goods (`fi_no`, `member`, `goods`, `added_date`, `delete`) VALUES (NULL, '".$_SESSION['info']['fi_no']."', '".$goods_index[0]['fi_no']."', '".$added_date."', '0') ");
				
				$this->massage = array("0", "收藏成功", 1);
			}
			else {
				$this->massage = array("1", "查無此商品", 1);
			}
		}
		else if($fi_no_check >= 1) {
			$this->massage = array("2", "收藏重複", 1);
		}
		else {
			$this->massage = array("3", "收藏已滿", 1);
		}
		
		return $this;
	}
	
	public function ajax_batch_collect() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$arr_fi_no = explode("∵", $_POST['fi_no']);
		
		$count_collect_goods = $dba->query("SELECT `goods` FROM collect_goods WHERE `member` = ".$_SESSION['info']['fi_no']." ");
		
		for($i=0; $i<count($arr_fi_no); $i++) { //過濾重複的收藏商品
			$arr_fi_no_check = 0;
			for($j=0; $j<count($count_collect_goods); $j++) {
				if($arr_fi_no[$i] == $count_collect_goods[$j]['goods']) {
					$arr_fi_no_check++;
				}
			}
			if($arr_fi_no_check == 0) {
				$brr_fi_no[] = $arr_fi_no[$i];
			}
		}
		
		$count_arr = count($count_collect_goods)+count($brr_fi_no);
		
		if($count_arr > 0 && $count_arr <= 100 && count($brr_fi_no) != 0) {
			$goods_index = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".join(",",$brr_fi_no).") && `status_audit`='1' && `status_shelves`='1' ");
			
			if(count($goods_index)>0) {
				$added_date = date('Y-m-d');
				for($i=0; $i<count($goods_index); $i++) {
					$collect_goods = $dba->query("INSERT INTO collect_goods (`fi_no`, `member`, `goods`, `added_date`, `delete`) VALUES (NULL, '".$_SESSION['info']['fi_no']."', '".$goods_index[$i]['fi_no']."', '".$goods_index[$i]['promotions']."', '0') ");
				}
				
				$this->massage = array("0", "收藏成功", 1);
				
				$arr_select = explode("∵", $_POST['select']);
				
				if(count($arr_fi_no) == count($arr_select)) { //從購物車加入才會有select
					$this->member_index = $dba->query("SELECT * FROM member_index WHERE fi_no = ".$_SESSION['info']['fi_no']." ");
					
					$new_cart = '';
					$old_cart = explode("｜", $this->member_index[0]['cart']);
					
					for($i=1; $i<count($old_cart); $i++) {
						$old_cart_split = explode("；", $old_cart[$i]);
						$new_cart_check = 0;
						
						for($j=0; $j<count($arr_fi_no); $j++) {
							if($old_cart_split[0] == $arr_fi_no[$j] && urldecode($old_cart_split[2]) == $arr_select[$j]) {
								$new_cart_check++;
							}
						}
						
						if($new_cart_check == 0) {
							$new_cart .= "｜".$old_cart[$i];
						}
					}
					
					//setcookie("cart", $new_cart, time()+3600*24);
					$_SESSION['info']['cart'] = $new_cart;
					$member_index = $dba->query("UPDATE member_index SET `cart` = '".$new_cart."' WHERE `fi_no` = '".$_SESSION['info']['fi_no']."' ");
				}
			}
			else {
				$this->massage = array("1", "查無此商品", 1);
			}
		}
		else if(count($brr_fi_no) <= 0) {
			$this->massage = array("1", "收藏重複", 1);
		}
		else {
			$this->massage = array("2", "收藏已滿", 1);
		}
		
		return $this;
	}
	
	public function ajax_shipping_fee() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$goods = explode("；", $_POST['goods']);
		$goods_fino = array();
		$goods_quantity = array();
		for($i=0; $i<count($goods)-1; $i++) {
			$goods_sp = explode("∵", $goods[$i]);
			$goods_fino[] = $goods_sp[0];
			$goods_quantity[] = $goods_sp[1];
		}
		
		$goods_index = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", array_unique($goods_fino)).") && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ORDER BY `store` ASC, `fi_no` ASC ");
		if(count(array_unique($goods_fino)) == count($goods_index)) {
			$shipping_fee = $dba->query("SELECT * FROM shipping_fee ");
			$exchange_rate = $dba->query("select * from exchange_rate order by `date` desc limit 0,1");
			
			
			$goods_all = array();
			for($i=0; $i<count($goods_index); $i++) {
				$goods_all[$goods_index[$i]['fi_no']] = $goods_index[$i];
			}
			
			$shipping_all = array();
			for($i=0; $i<count($shipping_fee); $i++) {
				$shipping_all[$shipping_fee[$i]['type']][] = $shipping_fee[$i];
			}
			
			$store_all = array();
			for($i=0; $i<count($goods_fino); $i++) {
				$fino = $goods_fino[$i];
				
				
				$volumetric_weight = $this->json_url_arr($goods_all[$fino]['volumetric_weight']);
				$lwh_a = sprintf("%01.2f", $volumetric_weight['長'] * $volumetric_weight['寬'] * $volumetric_weight['高']/6000);
				$lwh_b = sprintf("%01.2f", $volumetric_weight['重量']);
				$lwh = ($lwh_a > $lwh_b)?$lwh_a:$lwh_b;
				
				$store_all[$goods_all[$fino]['store']."_".$goods_all[$fino]['direct']] = $store_all[$goods_all[$fino]['store']."_".$goods_all[$fino]['direct']] + $lwh * $goods_quantity[$i];
			}
			
			$fee_all = array();
			foreach($store_all as $k => $v) {
				$store_select = explode("_", $k);
				$shipping_select = ($store_select[1]==1)?1:$_POST['type'];
				
				switch($shipping_select) {
					case 1:
						$area_0 = array("110000", "120000", "130000", "140000", "150000", "210000", "220000", "230000", "310000", "320000", "330000", "340000", "370000", "410000", "420000", "430000", "500000", "510000", "520000", "530000", "540000", "610000", "620000", "630000", "640000", "650000");
						$area_1 = array("440000", "450000", "460000", "350000", "360000");
						if(in_array($_POST['province'], $area_0)) { $mod_a = 0; $mod_b = 1; }
						if(in_array($_POST['province'], $area_1)) { $mod_a = 2; $mod_b = 3; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($v > $shipping_all[$shipping_select][$j]['range_a'] && $v <= $shipping_all[$shipping_select][$j]['range_b']) {
								if($shipping_all[$shipping_select][$j]['mod'] == $mod_a) {
									$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + ($shipping_all[$shipping_select][$j]['amount'] / $exchange_rate[0]['rate']));
								}
								else if($shipping_all[$shipping_select][$j]['mod'] == $mod_b) {
									$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + ($v * $shipping_all[$shipping_select][$j]['amount'] / $exchange_rate[0]['rate']));
								}
							}
						}
						break;
	
					case 2:
						$area_0 = array("310000", "330000", "320000");
						$area_1 = array("340000");
						$area_2 = array("110000", "120000", "130000", "140000", "350000", "370000", "420000", "410000", "360000", "500000", "510000", "610000", "210000", "520000", "530000", "620000", "630000", "430000");
						$area_3 = array("440000", "450000", "460000", "150000");
						$area_4 = array("230000", "220000", "640000");
						$area_5 = array("540000", "650000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						if(in_array($_POST['province'], $area_4)) { $mod = 4; }
						if(in_array($_POST['province'], $area_5)) { $mod = 5; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
					
					case 3:
						$area_0 = array("310000", "320000", "330000", "340000");
						$area_1 = array("110000", "120000", "130000", "140000", "350000", "360000", "370000", "410000", "420000", "440000", "430000");
						$area_2 = array("210000", "220000", "230000", "450000", "520000", "500000", "510000", "530000", "460000", "610000");
						$area_3 = array("150000", "620000", "630000", "640000");
						$area_4 = array("650000", "540000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						if(in_array($_POST['province'], $area_4)) { $mod = 4; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
	
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
						
					case 4:
						$area_0 = array("310000", "320000", "330000", "340000");
						$area_1 = array("110000", "120000", "130000", "140000", "350000", "360000", "370000", "410000", "420000", "440000", "430000");
						$area_2 = array("210000", "220000", "230000", "450000", "520000", "500000", "510000", "530000", "460000", "610000");
						$area_3 = array("150000", "620000", "630000", "640000");
						$area_4 = array("650000", "540000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						if(in_array($_POST['province'], $area_4)) { $mod = 4; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
					
					case 5:
						$area_0 = array("310000", "320000", "330000");
						$area_1 = array("110000", "120000", "130000", "140000", "350000", "360000", "370000", "410000", "420000", "430000", "440000", "500000", "610000", "340000");
						$area_2 = array("150000", "210000", "220000", "230000", "450000", "520000", "530000", "620000", "630000", "640000", "460000", "510000");
						$area_3 = array("540000", "650000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$fee_all[$store_select[0]] = sprintf("%01.2f", $fee_all[$store_select[0]] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
				}
			}
			$this->massage = array("0", "", 1, $fee_all);
		}
		else {
			$this->massage = array("1", "商品異常，請重新加入購物車", 1, array());
		}
		
		return $this;
	}
	
	public function ajax_postal_province() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->postal_province = $dba->query("SELECT * FROM postal_province WHERE `index` = '".$_POST['fi_no']."' ORDER BY `index` ASC, `fi_no` ASC ");
		
		return $this;
	}
	
	public function ajax_postal_street() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->postal_street = $dba->query("SELECT * FROM postal_street WHERE `index` = '".$_POST['fi_no']."' ORDER BY `index` ASC, `fi_no` ASC ");
		
		return $this;
	}
	
	public function checkout() {
		$cart_no = array();
		//echo $_SESSION['choose'];
		$cart = explode("｜", $_SESSION['choose']);
		for($i=0; $i<count($cart)-1; $i++) {
			$cart_split = explode("；", $cart[$i]);
			$cart_no[] = $cart_split[0];
		}
		
		require_once "swop/library/dba.php";
		$dba = new dba();
		$goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $cart_no).") && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ORDER BY `store` ASC, `fi_no` ASC ");
		$this->goods = $goods;
		
		$cart_store = array();
		$cart_store_check = '';
		$direct_true = 0;
		$undirect_true = 0;
		$combination_arr = array();
		for($i=0; $i<count($goods); $i++) {
			if($goods[$i]['store'] != $cart_store_check) {
				$cart_store_check = $goods[$i]['store'];
				array_push($cart_store, $goods[$i]['store']);
			}
			if($goods[$i]['combination'] != "") {
				$combination_json = $this->json_url_arr($goods[$i]['combination']);
				$combination_arr = array_merge($combination_arr, $combination_json[0]["fi_no"]);
			}
			if($goods[$i]['direct'] == 3 || $goods[$i]['direct'] == 4) {
				$direct_true = 1;
			}
			else {
				$undirect_true++;
			}
		}
		$combination = $dba->query("SELECT * FROM goods_index WHERE `fi_no` IN (".implode(",", $combination_arr).")  ORDER BY `store` ASC, `fi_no` ASC ");
		$goods_combination = array();
		for($i=0; $i<count($combination); $i++) {
			$goods_combination[$combination[$i]["fi_no"]] = $combination[$i];
		}
		$this->goods_combination = $goods_combination;
		
		$this->direct = $direct_true;
		
		$this->undirect = $undirect_true;
		
		$this->store = $dba->query("SELECT * FROM store WHERE `fi_no` IN (".join(",",$cart_store).") ");
		
		//$this->shipping_fee = $dba->query("SELECT * FROM shipping_fee ");
		
		//$this->exchange_rate = $dba->query("select * from exchange_rate order by `date` desc limit 0,1");
		
		$this->member_currency = $dba->query("select * from member_currency where `type` = '1' && `member` = '".$_SESSION['info']['fi_no']."' ");
		
		$this->member_address = $dba->query("select * from member_address where `member` = '".$_SESSION['info']['fi_no']."' ");
		
		return $this;
	}
	
	public function ajax_submit_checkout($ide_dir) {
		//$time_start = microtime(true);
		
		$cart = explode("｜", $_SESSION['choose']);
		$cart_new = array();
		$ic = 0; //組合商品延續編號
		for($i=0; $i<count($cart)-1; $i++) {
			$cart_split = explode("；", $cart[$i]);
			$cart_new[$i]['fi_no'] = $cart_split[0];
			$cart_new[$i]['num'] = $cart_split[1];
			
			$cart_split_type = explode("；", urldecode($cart_split[2]));
			$cart_new[$i]['stock'] = $cart_split_type[0];
			
			$cart_split_type_no = explode("｜", $cart_split_type[1]);
			$cart_new[$i]['no'] = $cart_split_type_no;
			
			$cart_split_type_item = explode("｜", $cart_split_type[2]);
			$cart_new[$i]['item'] = $cart_split_type_item;
			
			$cart_split_type_select = explode("｜", $cart_split_type[3]);
			$cart_new[$i]['select'] = $cart_split_type_select;
			
			$cart_new[$i]['combination_fino'] = "0";
			$ic = $i;
		}
		//print_r($cart_new);
		
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		$cart_success = array();
		$cart_goods = array();
		$cart_goods_plus = array();
		$cart_error = 0; //規格錯誤、查無此商品
		$cart_stock_zero = 0; //庫存不足
		
		$i = 0;
		while($i < count($cart_new)) {
			$goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no` = '".$cart_new[$i]['fi_no']."' && `status_audit`='1' && `status_shelves`='1' && `delete`='0' ");
			if(count($goods)>0) {
				
				
				
				if($goods[0]['combination'] == "") {
					$goods_specifications = json_decode(urldecode($goods[0]['specifications']), true);
					$goods_inventory = json_decode($goods[0]['inventory'], true);
					
					for($j=0; $j<count($cart_new[$i]['no']); $j++) { //檢查規格位置是否存在
						if($goods_specifications[$cart_new[$i]['item'][$j]][$cart_new[$i]['no'][$j]] != $cart_new[$i]['select'][$j]) {
							$cart_error = 1;
							break;
						}
					}
					
					if($cart_error == 1) {
						break; //確定規格位置錯誤，直接脫離
					}
					
					if($goods_inventory[$cart_new[$i]['stock']] < $cart_new[$i]['num']) { //檢查庫存是否足夠
						$cart_stock_zero = 1;
						break;
					}
					else {
						$goods_inventory[$cart_new[$i]['stock']] = sprintf("%d",($goods_inventory[$cart_new[$i]['stock']] - $cart_new[$i]['num']));
					}
					
					$stime = explode(" ", microtime());
					$stime = explode(".", $stime[0]);
					$otime = date('Y-m-d H:i:s').".".$stime[1];
					$goods_index = $dba->query("UPDATE goods_index SET `inventory` = '".(json_encode($goods_inventory))."', `oc_control` = '".$otime."' WHERE `fi_no` = '".$goods[0]['fi_no']."' && `oc_control` = '".$goods[0]['oc_control']."' ");
					$goods_impact = $dba->get_affected_rows();
					
					if($goods_impact == 1) {
						$goods_info = $dba->query("UPDATE goods_info SET `inventory` = '".(json_encode($goods_inventory))."' WHERE `fi_no` = '".$goods[0]['fi_no']."' ");
						array_push($cart_success, '1');
						if($cart_new[$i]['combination_fino'] == "0") {
							array_push($cart_goods, $goods[0]);
							$goods[0]['quantity'] = $cart_new[$i]['num'];
							$cart_goods_plus[$goods[0]['store']."_".$goods[0]['direct']][] = $goods[0];
						}
						$i++;
					}
				}
				else {
					$goods_combination = $this->json_url_arr($goods[0]['combination']);
					for($a=0; $a<count($goods_combination[0]["fi_no"]); $a++) {
						$ic++;
						$cart_new[$ic]['fi_no'] = $goods_combination[0]["fi_no"][$a];
						$cart_new[$ic]['num'] = $goods_combination[0]["quantity"][$a] * $cart_new[$i]['num'];
						$cart_new[$ic]['stock'] = $goods_combination[0]["inventory"][$a];
						
						$specifications_split = explode("；", $goods_combination[0]["specifications"][$a]);
						for($b=0; $b<count($specifications_split)-1; $b++) {
							$cart_new[$ic]['no'][] = $specifications_split[$b];
						}
						foreach($goods_combination[($a+1)] as $k => $v) {
							$cart_new[$ic]['item'][] = $k;
							$cart_new[$ic]['select'][] = $v[0];
						}
						$cart_new[$ic]['combination_fino'] = sprintf("%d", $goods[0]['fi_no']);
					}
					array_push($cart_success, '1');
					array_push($cart_goods, $goods[0]);
					$goods[0]['quantity'] = $cart_new[$i]['num'];
					$cart_goods_plus[$goods[0]['store']."_".$goods[0]['direct']][] = $goods[0];
					$i++;
				}
				
				
				
			}
			else {
				$cart_error = 1;
				break; //查無此商品，直接脫離
			}
		}
		/*
		print_r($cart_new);
		print_r($cart_success);
		print_r($cart_goods);
		print_r($cart_goods_plus);
		*/
		
		if(count($cart_success) == count($cart_new) && $cart_error == 0 && $cart_stock_zero == 0) {
			//建立訂單
			$shipping_fee = $dba->query("SELECT * FROM shipping_fee ");
			
			$shipping_all = array();
			for($i=0; $i<count($shipping_fee); $i++) {
				$shipping_all[$shipping_fee[$i]['type']][] = $shipping_fee[$i];
			}
			
			$exchange_rate = $dba->query("select * from exchange_rate order by `date` desc limit 0,1");
			
			foreach($cart_goods_plus as $k => $v) {
				$acart['store'][] = $k; //店家 ["1","2"]
				for($i=0; $i<count($cart_goods_plus[$k]); $i++) {
					if(is_array($cart_goods_plus[$k][$i])) {
						//小計 {"1":"450","2":"330"}
						$acart['subtotal'][$k] = sprintf("%01.2f", (float)$acart['subtotal'][$k] + ($cart_goods_plus[$k][$i]['promotions'] * $cart_goods_plus[$k][$i]['quantity']));
						
						$lwh_o = json_decode(urldecode($cart_goods_plus[$k][$i]['volumetric_weight']), true);
						$lwh_a = sprintf("%01.2f", $lwh_o['長']*$lwh_o['寬']*$lwh_o['高']/6000);
						$lwh_b = sprintf("%01.2f", $lwh_o['重量']);
						if($lwh_a > $lwh_b) { $lwh = $lwh_a; } else { $lwh = $lwh_b; }
						//運費 {"1":"60","2":"20"}
						$acart['weight'][$k] = sprintf("%01.2f", (float)$acart['weight'][$k] + ($lwh * $cart_goods_plus[$k][$i]['quantity']));
						
						$acart['direct'][$k] = /*$acart['direct'][$k] + */$cart_goods_plus[$k][$i]['direct'];
						$acart['fragile'][$k] = $acart['fragile'][$k] + $cart_goods_plus[$k][$i]['fragile'];
					}
				}
				
				
				$acart['payments'][$k] = (float)$acart['subtotal'][$k] + (float)$acart['shipping_fee'][$k]; //總價
				$acart['status_order'][$k] = sprintf("%d", 1); //訂單
				$acart['status_pay'][$k] = sprintf("%d", 1); //付款
				$acart['status_transport'][$k] = sprintf("%d", 0); //出貨
				$acart['status_receiving'][$k] = sprintf("%d", 0); //到貨
				$acart['date_transport'][$k] = "0000-00-00 00:00:00";
				$acart['date_receiving'][$k] = "0000-00-00 00:00:00";
				$acart['application_returns'][$k] = sprintf("%d", 0); //有商品退貨
				$acart['application_exchanges'][$k] = sprintf("%d", 0); //有商品換貨
				$acart['application_rework'][$k] = sprintf("%d", 0); //有商品反修
				$acart['trace'][$k] = json_encode(array()); //追蹤代碼
				$acart['remind'][$k] = sprintf("%d", 0); //提醒發貨
			}
			
			foreach($acart['weight'] as $k => $v) {
				$store_select = explode("_", $k);
				$shipping_select = ($store_select[1]==1)?1:$_POST['ashipping_select'];
				switch($shipping_select) {
					case 1:
						$area_0 = array("110000", "120000", "130000", "140000", "150000", "210000", "220000", "230000", "310000", "320000", "330000", "340000", "370000", "410000", "420000", "430000", "500000", "510000", "520000", "530000", "540000", "610000", "620000", "630000", "640000", "650000");
						$area_1 = array("440000", "450000", "460000", "350000", "360000");
						if(in_array($_POST['province'], $area_0)) { $mod_a = 0; $mod_b = 1; }
						if(in_array($_POST['province'], $area_1)) { $mod_a = 2; $mod_b = 3; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($v > $shipping_all[$shipping_select][$j]['range_a'] && $v <= $shipping_all[$shipping_select][$j]['range_b']) {
								if($shipping_all[$shipping_select][$j]['mod'] == $mod_a) {
									$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + ($shipping_all[$shipping_select][$j]['amount'] / $exchange_rate[0]['rate']));
								}
								else if($shipping_all[$shipping_select][$j]['mod'] == $mod_b) {
									$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + ($v * $shipping_all[$shipping_select][$j]['amount'] / $exchange_rate[0]['rate']));
								}
							}
						}
						break;
	
					case 2:
						$area_0 = array("310000", "330000", "320000");
						$area_1 = array("340000");
						$area_2 = array("110000", "120000", "130000", "140000", "350000", "370000", "420000", "410000", "360000", "500000", "510000", "610000", "210000", "520000", "530000", "620000", "630000", "430000");
						$area_3 = array("440000", "450000", "460000", "150000");
						$area_4 = array("230000", "220000", "640000");
						$area_5 = array("540000", "650000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						if(in_array($_POST['province'], $area_4)) { $mod = 4; }
						if(in_array($_POST['province'], $area_5)) { $mod = 5; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
					
					case 3:
						$area_0 = array("310000", "320000", "330000", "340000");
						$area_1 = array("110000", "120000", "130000", "140000", "350000", "360000", "370000", "410000", "420000", "440000", "430000");
						$area_2 = array("210000", "220000", "230000", "450000", "520000", "500000", "510000", "530000", "460000", "610000");
						$area_3 = array("150000", "620000", "630000", "640000");
						$area_4 = array("650000", "540000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						if(in_array($_POST['province'], $area_4)) { $mod = 4; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
	
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
						
					case 4:
						$area_0 = array("310000", "320000", "330000", "340000");
						$area_1 = array("110000", "120000", "130000", "140000", "350000", "360000", "370000", "410000", "420000", "440000", "430000");
						$area_2 = array("210000", "220000", "230000", "450000", "520000", "500000", "510000", "530000", "460000", "610000");
						$area_3 = array("150000", "620000", "630000", "640000");
						$area_4 = array("650000", "540000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						if(in_array($_POST['province'], $area_4)) { $mod = 4; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
					
					case 5:
						$area_0 = array("310000", "320000", "330000");
						$area_1 = array("110000", "120000", "130000", "140000", "350000", "360000", "370000", "410000", "420000", "430000", "440000", "500000", "610000", "340000");
						$area_2 = array("150000", "210000", "220000", "230000", "450000", "520000", "530000", "620000", "630000", "640000", "460000", "510000");
						$area_3 = array("540000", "650000");
						if(in_array($_POST['province'], $area_0)) { $mod = 0; }
						if(in_array($_POST['province'], $area_1)) { $mod = 1; }
						if(in_array($_POST['province'], $area_2)) { $mod = 2; }
						if(in_array($_POST['province'], $area_3)) { $mod = 3; }
						for($j=0; $j<count($shipping_all[$shipping_select]); $j++) {
							if($shipping_all[$shipping_select][$j]['mod'] == $mod) {
								$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + $shipping_all[$shipping_select][$j]['base']);
								if($v > $shipping_all[$shipping_select][$j]['range_b']) {
									$acart['shipping_fee'][$k] = sprintf("%01.2f", $acart['shipping_fee'][$k] + (($v - 1) * $shipping_all[$shipping_select][$j]['amount']));
								}
							}
						}
						break;
				}
			}
			
			//print_r($acart);
			
			$atime = explode(" ", microtime());
			$atime = explode(".", $atime[0]);
			$btime = date('Y-m-d H:i:s');
			$ctime = str_replace("-", "",($btime.$atime[1]));
			$ctime = str_replace(" ", "",$ctime);
			$ctime = str_replace(":", "",$ctime);
			
			$member = $_SESSION['info']['fi_no'];
			$sn = substr($ctime, 2, 12); //20 141003101640 25139300
			$sn_num = (int)str_pad(substr($ctime, 14, 8), 8, "0");
			$date = $btime;
			$store = $acart['store'];
			$direct = $acart['direct'];
			$fragile = $acart['fragile'];
			$subtotal = $acart['subtotal'];
			$shipping_fee = $acart['shipping_fee'];
			$discounts = '';
			$payments = $acart['payments'];
			$checkout = '';
			$exchange_rate = $swop->exchange_rate[0]['rate'];
			$status_order = $acart['status_order'];
			$status_pay = $acart['status_pay'];
			$status_transport = $acart['status_transport'];
			$status_receiving = $acart['status_receiving'];
			$date_transport = $acart['date_transport'];
			$date_receiving = $acart['date_receiving'];
			$application_returns = $acart['application_returns'];
			$application_exchanges = $acart['application_exchanges'];
			$application_rework = $acart['application_rework'];
			$trace = $acart['trace'];
			$remind = $acart['remind'];
			$invoice = '';
			$remarks = '';
			$consignee = $_POST['consignee'];
			$postal_code = $_POST['postal_code'];
			$province = $_POST['nprovince'];
			$city = $_POST['ncity'];
			$district = $_POST['ndistrict'];
			$street = $_POST['nstreet'];
			$address = $_POST['address'];
			if($_POST['contact_mobile_number'] != '') {
				$contact_mobile = $_POST['contact_mobile_international']."-".$_POST['contact_mobile_number'];
			}
			else {
				$contact_mobile = "-";
			}
			if($_POST['contact_phone_number'] != '') {
				$contact_phone = $_POST['contact_phone_international']."-".$_POST['contact_phone_area']."-".$_POST['contact_phone_number'];
			}
			else {
				$contact_phone = "--";
			}
			$aidentity_front_ext = explode("/", $_FILES['aidentity_front']['type']);
			$aidentity_back_ext = explode("/", $_FILES['aidentity_back']['type']);
			$aidentity_front_name = md5($sn.($sn_num+$i+1).$date.$_FILES['aidentity_front']['name']).".".$aidentity_front_ext[1];
			$aidentity_back_name = md5($sn.($sn_num+$i+1).$date.$_FILES['aidentity_back']['name']).".".$aidentity_back_ext[1];
			move_uploaded_file($_FILES['aidentity_front']['tmp_name'], ($ide_dir.$aidentity_front_name));
			move_uploaded_file($_FILES['aidentity_back']['tmp_name'], ($ide_dir.$aidentity_back_name));
			$identity = json_encode(array($aidentity_front_name, $aidentity_back_name));
			
			$transport = $_POST['ashipping_select'];
			$store_split = explode("｜", $_POST['store']);
			$message_split = explode("｜", $_POST['message']);
			if( count($store_split) == count($message_split) && count($store_split) == (count($acart['store'])+1) ) {
				for($i=0; $i<count($store_split)-1; $i++) {
					if($store_split[$i] == $acart['store'][$i]) {
						$message_array[$acart['store'][$i]] = $message_split[$i];
					}
					else {
						$message_array[$acart['store'][$i]] = "";
					}
				}
			}
			else {
				for($i=0; $i<count($acart['store']); $i++) {
					$message_array[$acart['store'][$i]] = "";
				}
			}
			$message = $message_array;
			
			for($i=0; $i<count($store); $i++) {
				$sp_store = explode("_", $store[$i]);
				$order_index_insert[] = "(NULL, '".$member."', '".$sn.($sn_num+$i+1)."', '".$date."', '".$sp_store[0]."', '".$direct[$store[$i]]."', '".(($fragile[$store[$i]]>0)?"1":"0")."', '".$subtotal[$store[$i]]."', '".$shipping_fee[$store[$i]]."', '".$discounts."', '".($payments[$store[$i]]+$shipping_fee[$store[$i]])."', '".$checkout."', '".$exchange_rate."', '".$status_order[$store[$i]]."', '".$status_pay[$store[$i]]."', '".$status_transport[$store[$i]]."', '".$status_receiving[$store[$i]]."', '".$date_transport[$store[$i]]."', '".$date_receiving[$store[$i]]."', '".$application_returns[$store[$i]]."', '".$application_exchanges[$store[$i]]."', '".$application_rework[$store[$i]]."', '".$trace[$store[$i]]."', '".$remind[$store[$i]]."')";
			}
			
			//echo "INSERT INTO order_index (`fi_no`, `member`, `sn`, `date`, `store`, `direct`, `fragile`, `subtotal`, `shipping_fee`, `discounts`, `payments`, `checkout`, `exchange_rate`, `status_order`, `status_pay`, `status_transport`, `status_receiving`, `date_transport`, `date_receiving`, `application_returns`, `application_exchanges`, `application_rework`, `trace`, `remind`) VALUES ".implode(",", $order_index_insert)." ";
			$order_index = $dba->query("INSERT INTO order_index (`fi_no`, `member`, `sn`, `date`, `store`, `direct`, `fragile`, `subtotal`, `shipping_fee`, `discounts`, `payments`, `checkout`, `exchange_rate`, `status_order`, `status_pay`, `status_transport`, `status_receiving`, `date_transport`, `date_receiving`, `application_returns`, `application_exchanges`, `application_rework`, `trace`, `remind`) VALUES ".implode(",", $order_index_insert)." ");
			
			$order_id = (int)($dba->get_insert_id());
			
			for($i=0; $i<count($store); $i++) {
				$sp_store = explode("_", $store[$i]);
				$store_order_id[$store[$i]] = $order_id;
				$sp_identity = ($direct[$store[$i]]==3||$direct[$store[$i]]==4)?$identity:"";
				$order_form_insert[] = "('".$order_id."', '".$member."', '".$sn.($sn_num+$i+1)."', '".$date."', '".$sp_store[0]."', '".$direct[$store[$i]]."', '".(($fragile[$store[$i]]>0)?"1":"0")."', '".$subtotal[$store[$i]]."', '".$shipping_fee[$store[$i]]."', '".$discounts."', '".($payments[$store[$i]]+$shipping_fee[$store[$i]])."', '".$checkout."', '".$exchange_rate."', '".$status_order[$store[$i]]."', '".$status_pay[$store[$i]]."', '".$status_transport[$store[$i]]."', '".$status_receiving[$store[$i]]."', '".$application_returns[$store[$i]]."', '".$application_exchanges[$store[$i]]."', '".$application_rework[$store[$i]]."', '".$trace[$store[$i]]."', '".$remind[$store[$i]]."', '".$invoice."', '".$remarks."', '".$consignee."', '".$postal_code."', '".$province."', '".$city."', '".$district."', '".$street."', '".$address."', '".$contact_phone."', '".$contact_mobile."', '".$sp_identity."', '".$transport."', '".$message[$store[$i]]."')";
				
				//v1 {[fi_no][member][sn][date][subtotal][shipping_fee][discounts][payments][status_order][status_pay[status_transport]}
				//$order_ierp[$order_id] = "{[".$order_id."][".$member."][".$sn.($sn_num+$i+1)."][".$date."][".$subtotal[$store[$i]]."][".$shipping_fee[$store[$i]]."][".$discounts."][".$payments[$store[$i]]."][".$status_order[$store[$i]]."][".$status_pay[$store[$i]]."][".$status_transport[$store[$i]]."]}";
				
				//v2 {[fi_no][member][sn][date][subtotal][shiping_fee][discount][payment][checkout][status_order][status_tran][status_pay][ex][exchange_rate][direct]}
				$order_ierp[$order_id] = "{[".$order_id."][".$member."][".$sn.($sn_num+$i+1)."][".$date."][".$subtotal[$store[$i]]."][".$shipping_fee[$store[$i]]."][".$discounts."][".$payments[$store[$i]]."][".$checkout."][".$status_order[$store[$i]]."][".$status_transport[$store[$i]]."][".$status_pay[$store[$i]]."][RMB][".$exchange_rate."][".$direct[$store[$i]]."]}";
				
				$order_id++;
			}
			//print_r($order_ierp);
			//echo "INSERT INTO order_form (`fi_no`, `member`, `sn`, `date`, `store`, `direct`, `fragile`, `subtotal`, `shipping_fee`, `discounts`, `payments`, `checkout`, `exchange_rate`, `status_order`, `status_pay`, `status_transport`, `status_receiving`, `application_returns`, `application_exchanges`, `application_rework`, `trace`, `remind`, `invoice`, `remarks`, `consignee`, `postal_code`, `province`, `city`, `district`, `street`, `address`, `contact_phone`, `contact_mobile`, `identity`, `transport`, `message`) VALUES ".implode(",", $order_form_insert)." ";
			$order_form = $dba->query("INSERT INTO order_form (`fi_no`, `member`, `sn`, `date`, `store`, `direct`, `fragile`, `subtotal`, `shipping_fee`, `discounts`, `payments`, `checkout`, `exchange_rate`, `status_order`, `status_pay`, `status_transport`, `status_receiving`, `application_returns`, `application_exchanges`, `application_rework`, `trace`, `remind`, `invoice`, `remarks`, `consignee`, `postal_code`, `province`, `city`, `district`, `street`, `address`, `contact_phone`, `contact_mobile`, `identity`, `transport`, `message`) VALUES ".implode(",", $order_form_insert)." ");
			
			
			for($i=0; $i<count($cart_goods); $i++) {
				$order = $store_order_id[$cart_goods[$i]['store']."_".$cart_goods[$i]['direct']];
				$member = $_SESSION['info']['fi_no'];
				$goods = $cart_goods[$i]['fi_no'];
				$combination = $cart_goods[$i]['combination'];
				$name = $cart_goods[$i]['name'];
				$import = $cart_goods[$i]['import'];
				$price = $cart_goods[$i]['price'];
				$promotions = $cart_goods[$i]['promotions'];
				$discount = $cart_goods[$i]['discount'];
				$direct = $cart_goods[$i]['direct'];
				$other_price = $cart_goods[$i]['other_price'];
				$fragile = $cart_goods[$i]['fragile'];
				$number = $cart_new[$i]['num'];
				//print_r($cart_new[$i]['item']);
				//print_r($cart_new[$i]['select']);
				for($a=0; $a<count($cart_new[$i]['item']); $a++) {
					$sp[$cart_new[$i]['item'][$a]] = (string)$cart_new[$i]['select'][$a];
				}
				//print_r($sp);
				//echo "\r\n\r\n";
				$specifications = $this->arr_url_json($sp);
				$volumetric_weight = $cart_goods[$i]['volumetric_weight'];
				$status = 0;
				$store = $cart_goods[$i]['store'];
				$evaluate = 0;
				$evaluate_context = '';
				$evaluate_date = "0000-00-00 00:00:00";
				$evaluate_added = 0;
				$evaluate_adcontext = '';
				$evaluate_addate = "0000-00-00 00:00:00";
				$respond = 0;
				$respond_date = "0000-00-00 00:00:00";
				$respond_added =  0;
				$application =  0;
				$application_status =  0;
				
				$order_goods_insert[] = "(NULL, '".$member."', '".$order."', '".$goods."', '".$combination."', '".$name."', '".$import."', '".$price."', '".$promotions."', '".$discount."', '".$direct."', '".$fragile."', '".$number."', '".$specifications."', '".$volumetric_weight."', '".$status."', '".$store."', '".$evaluate."', '".$evaluate_context."', '".$evaluate_date."', '".$evaluate_added."', '".$evaluate_adcontext."', '".$evaluate_addate."', '".$respond."', '".$respond_date."', '".$respond_added."', '".$application."', '".$application_status."')";
				
				//v1 {[goods][name][import][price][promotions][discount][direct][number][specifitions][status][store][供應商]}
				//$order_gerp[$order][] = "{[".$goods."][".$name."][".$import."][".$price."][".$promotions."][".$discount."][".$direct."][".$number."][".$specifications."][".$status."][".$store."][".$cart_goods[$i]['supplier']."]}";
				//v2 {[goods][name][specifictions][uprice][number][volumetric_weight][status][store]}
				$order_gerp[$order][] = "{[".$goods."][".$name."][".$specifications."][".(($discount>0)?$discount:$promotions)."][".$number."][".$volumetric_weight."][".$status."][".$store."]}";
			}
			//print_r($order_gerp);
			//echo "INSERT INTO order_goods (`fi_no`, `member`, `order`, `goods`, `name`, `import`, `price`, `promotions`, `discount`, `direct`, `fragile`, `number`, `specifications`, `volumetric_weight`, `status`, `store`, `evaluate`, `evaluate_date`, `evaluate_added`, `respond`, `respond_date`, `respond_added`, `application`, `application_progress`) VALUES ".implode(",", $order_goods_insert)." \r\n";
			$order_goods = $dba->query("INSERT INTO order_goods (`fi_no`, `member`, `order`, `goods`, `combination`, `name`, `import`, `price`, `promotions`, `discount`, `direct`, `fragile`, `number`, `specifications`, `volumetric_weight`, `status`, `store`, `evaluate`, `evaluate_context`, `evaluate_date`, `evaluate_added`, `evaluate_adcontext`, `evaluate_addate`, `respond`, `respond_date`, `respond_added`, `application`, `application_progress`) VALUES ".implode(",", $order_goods_insert)." ");
			
			
			//echo $_POST['asave_address'].' == "1" && '.$_POST['aprovince'].' != "" && '.$_POST['acity'].' != "" && '.$_POST['adistrict'].' != ""';
			if($_POST['asave_address'] == "1" && $_POST['aprovince'] != '' && $_POST['acity'] != '' && $_POST['adistrict'] != '') {
				$member_address = $dba->query("select * from member_address where `member`='".$_SESSION['info']['fi_no']."'");
				if(count($member_address)<=10) {
					$postal_province = $dba->query("select * from postal_province where `fi_no` IN (".$_POST['aprovince'].", ".$_POST['acity'].", ".$_POST['adistrict'].") order by `fi_no` asc");
					$index = 1;
					$postal_all = array();
					for($p=0; $p<count($postal_province); $p++) {
						if($index == $postal_province[$p]['index']) {
							$postal_all[] = $postal_province[$p]['fi_no']."＿".$postal_province[$p]['name']."＿".$postal_province[$p]['index'];
							$index = $postal_province[$p]['fi_no'];
						}
					}
					if(count($postal_all) == 3) {
						if($_POST['astreet'] != '' && $index != 1) {
							$postal_street = $dba->query("select * from postal_street where `fi_no` = '".$_POST['astreet']."' && `index` = '".$index."' ");
							if(count($postal_street) > 0) {
								$street_all = $postal_street[0]['fi_no']."＿".$postal_street[0]['name']."＿".$postal_street[0]['index'];
							}
							else {
								$street_all = "＿街道＿".$index;
							}
						}
						else {
							$street_all = "＿街道＿".$index;
						}

						$collect_goods = $dba->query("INSERT INTO member_address (`fi_no`, `member`, `consignee`, `postal_code`, `province`, `city`, `district`, `street`, `address`, `contact_phone`, `contact_mobile`, `preset`) VALUES (NULL, '".$_SESSION['info']['fi_no']."', '".$consignee."', '".$postal_code."', '".$postal_all[0]."', '".$postal_all[1]."', '".$postal_all[2]."', '".$street_all."', '".$address."', '".$contact_phone."', '".$contact_mobile."', '".((count($member_address)==0)?"1":"0")."') ");
					}
				}
			}
			$order_cerp = 0;
			$order_serp = "";
			foreach($order_ierp as $k => $v) {
				if($order_cerp != 0) {
					$order_serp .= "[--]"; 
				}
				$order_serp .= $v.implode("", $order_gerp[$k]);
				$order_cerp = 1;
			}
			//echo $order_serp;
			
			require_once "swop/library/erpconnect.php";
			$fuer = new Library_ERP();
			$fuer->addorder($order_serp);
			
			$this->status = 'on';
		}
		else {
			//退回庫存
			$i = 0;
			while($i < count($cart_success)) {
				$goods = $dba->query("SELECT * FROM goods_index WHERE `fi_no` = '".$cart_new[$i]['fi_no']."' ");
				
				if($goods[0]['combination'] == "") {
					$goods_specifications = json_decode(urldecode($goods[0]['specifications']), true);
					$goods_inventory = json_decode($goods[0]['inventory'], true);
					
					$cart_returns = 0;
					for($j=0; $j<count($cart_new[$i]['no']); $j++) { //檢查規格位置是否存在
						if($goods_specifications[$cart_new[$i]['item'][$j]][$cart_new[$i]['no'][$j]] != $cart_new[$i]['select'][$j]) {
							$cart_returns = 1;
						}
					}
					
					if($cart_returns == 0) { //規格正確
						$goods_inventory[$cart_new[$i]['stock']] = sprintf("%d",($goods_inventory[$cart_new[$i]['stock']] + $cart_new[$i]['num']));
						
						$stime = explode(" ", microtime());
						$otime = date('Y-m-d H:i:s').".".$stime[1];
						$goods_new = $dba->query("UPDATE goods_index SET `inventory` = '".(json_encode($goods_inventory))."', `oc_control` = '".$otime."' WHERE `fi_no` = '".$goods[0]['fi_no']."' && `oc_control` = '".$goods[0]['oc_control']."' ");
						$goods_impact = $dba->get_affected_rows();
						
						if($goods_impact == 1) { //規格正確 且 金鑰正確
							$goods_info = $dba->query("UPDATE goods_info SET `inventory` = '".(json_encode($goods_inventory))."' WHERE `fi_no` = '".$goods[0]['fi_no']."' ");
							$i++;
						}
					}
					else { //規格不正確，直接進入下一筆退回庫存
						$i++;
					}
				}
				else { //組合商品庫存免恢復庫存，後續有組合商品陣列可以恢復，直接進入下一筆退回庫存
					$i++;
				}
			}
			$this->status = 'off';
		}
		
		//$time_end = microtime(true);
		//echo $time = $time_end - $time_start;
		
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
<?php
class Library_ERP {

	public function __construct() {
		$this->base = $base;
	}

	public function addorder($order_detail) {
		$post = array("a"=>$order_detail);
		$this->posterp($post, "AddOrder.php", "0");
		return $this;
	}
	
	/*
	a - sn
	b - status_order
	c - status_pay
	d - status_trans
	e - status_recei
	f - trans_sup
	g - checkout
	*/
	
  //public function updateorder($sn, $status_order, $status_pay, $status_transport, $status_receiving, $trace) {
	public function updateorder($sn, $status_order, $status_pay, $status_transport, $status_receiving, $trace, $checkout) {
		/*
		待付款訂單			1,1,0,0
		已付款未出貨訂單		1,2,1,0
		已發貨訂單			1,2,2,1
		已收貨待確認訂單		1,2,2,2
		歷史訂單-訂單取消		2,0,0,0
		歷史訂單-訂單完成		3,2,2,2
		歷史訂單-免付款訂單		3,3,2,2
		*/
		
		$post = array("a"=>$sn, 
						"b"=>$status_order, 
						"c"=>$status_pay, 
						"d"=>$status_transport, 
						"e"=>$status_receiving, 
						"f"=>$trace,
						"g"=>$checkout);
		$this->posterp($post, "UpdateOrder.php", "1");
		return $this;
	}
	
	/*
	no		fi_no
	n		name
	b		brand
	m		// 產地
	s		specifications
	mt		attribute
	w		volumetric_weight
	c		// 是否組合品
	d		direct碼
	sup		supplier
	mup		price 市價
	iup		RMB標準進價
	up		商品標準售價 promotions 
	dp		折扣售價 discount
	ka		大類名稱
	kb		中類名稱
	kc		小類名稱
	p		images 圖片網址
	i		introduction商品描述文
	u		資料來源網址   ( &符號請先轉換成網頁符號 )
	e		商品原始廠商報價幣別 (ex_uprice)
	ia		商品原始廠商報價單價 (ex_uprice)
	*/
	
  //public function addgoods($fi_no, $name, $unit, $brand, $produce, $specifications, $attribute, $voloumetric_weight, $combination, $direct, $supplier, $price, $import, $price_other, $promotions,            $category_a, $category_b, $category_c, $images, $introduction, $source) {
	public function addgoods($fi_no, $name,        $brand, $produce, $specifications, $attribute, $voloumetric_weight, $combination, $direct, $supplier, $price, $import,               $promotions, $discount, $category_a, $category_b, $category_c, $images, $introduction, $source, $price_other) {
		$source = str_replace("&", "%26", $source);
		$post = array("no"=>$fi_no, 
						"n"=>$name, 
						"b"=>$brand, 
						"m"=>$produce, 
						"s"=>$specifications, 
						"mt"=>$attribute, 
						"w"=>$voloumetric_weight, 
						"c"=>$combination, 
						"d"=>$direct, 
						"sup"=>$supplier, 
						"mup"=>$price, 
						"iup"=>$import, 
						"up"=>$promotions, 
						"dp"=>$discount, 
						"ka"=>$category_a, 
						"kb"=>$category_b, 
						"kc"=>$category_c, 
						"p"=>$images, 
						"i"=>$introduction, 
						"u"=>$source, 
						"e"=>"NTD", 
						"ia"=>$price_other);
		$this->posterp($post, "AddGoods.php", "2");
		return $this;
	}

	public function updategoods($fi_no, $name, $brand, $produce, $specifications, $attribute, $voloumetric_weight, $combination, $direct, $supplier, $price, $import, $promotions, $discount, $category_a, $category_b, $category_c, $images, $introduction, $source, $price_other) {
		$source = str_replace("&", "%26", $source);
		$post = array("no"=>$fi_no, 
						"n"=>$name, 
						"b"=>$brand, 
						"m"=>$produce, 
						"s"=>$specifications, 
						"mt"=>$attribute, 
						"w"=>$voloumetric_weight, 
						"c"=>$combination, 
						"d"=>$direct, 
						"sup"=>$supplier, 
						"mup"=>$price, 
						"iup"=>$import, 
						"up"=>$promotions, 
						"dp"=>$discount, 
						"ka"=>$category_a, 
						"kb"=>$category_b, 
						"kc"=>$category_c, 
						"p"=>$images, 
						"i"=>$introduction, 
						"u"=>$source, 
						"e"=>"NTD", 
						"ia"=>$price_other);
		$this->posterp($post, "UpdateGoods.php", "3");
		return $this;
	}

	public function deletegoods($fi_no) {
		$post = array("no"=>$fi_no);
		$this->posterp($post, "DeleteGoods.php", "4");
		return $this;
	}
	
	/*
	a	序號fi_no
	b	公司名稱name
	c	負責人
	d	資本額 registered_capital
	e	address
	f	phone
	g	fax
	h	check_name
	i	check_email
	j	check_phone	
	k	check_mobile	
	l	撥款銀行 bank
	m	分行名稱bank_branch
	n	帳號 bank_account
	o	戶名 bank_username
	p	廠商網址  ( &符號請先轉換成網頁符號 )
	*/
	
	public function addsupplier($fi_no, $name, $company_corporate, $company_registered_capital, $company_address, $company_phone, $company_fax, $check_name, $check_email, $check_phone, $check_mobile, $bank, $bank_branch, $bank_account, $bank_username, $company_http) {
		$post = array("a"=>$fi_no, 
						"b"=>$name, 
						"c"=>$company_corporate, 
						"d"=>$company_registered_capital, 
						"e"=>$company_address, 
						"f"=>$company_phone, 
						"g"=>$company_fax, 
						"h"=>$check_name, 
						"i"=>$check_email, 
						"j"=>$check_phone, 
						"k"=>$check_mobile, 
						"l"=>$bank, 
						"m"=>$bank_branch, 
						"n"=>$bank_account, 
						"o"=>$bank_username, 
						"p"=>$company_http);
		$this->posterp($post, "AddSupplier.php", "5");
		return $this;
	}
	
	public function updatesupplier($fi_no, $name, $company_corporate, $company_registered_capital, $company_address, $company_phone, $company_fax, $check_name, $check_email, $check_phone, $check_mobile, $bank, $bank_branch, $bank_account, $bank_username, $company_http) {
		$post = array("a"=>$fi_no, 
						"b"=>$name, 
						"c"=>$company_corporate, 
						"d"=>$company_registered_capital, 
						"e"=>$company_address, 
						"f"=>$company_phone, 
						"g"=>$company_fax, 
						"h"=>$check_name, 
						"i"=>$check_email, 
						"j"=>$check_phone, 
						"k"=>$check_mobile, 
						"l"=>$bank, 
						"m"=>$bank_branch, 
						"n"=>$bank_account, 
						"o"=>$bank_username, 
						"p"=>$company_http);
		$this->posterp($post, "UpdateSupplier.php", "6");
		return $this;
	}
	
	public function deletesupplier($fi_no) {
		$post = array("no"=>$fi_no);
		$this->posterp($post, "DeleteSupplier.php", "7");
		return $this;
	}
	
	public function posterp($post, $file, $type) {
		require_once dirname(__FILE__)."/dba.php";
		$dba=new dba();
		
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, "http://www.ezwebap.com/bigway/cgi_bin/".$file);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $post);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		$check_erp = curl_exec($curl_handle);
		curl_close($curl_handle);
		//echo "[".$check_erp."]";
		
		if($check_erp == "1") {
			$check_erp = "1";
		}
		else {
			$check_erp = "0";
		}
		
		$check = 0;
		foreach($post as $k => $v) {
			if($check == 0) {
				$check = 1;
			}
			else {
				$str_erp .= "&";
			}
			$str_erp .= $k."=".$v;
		}
		//type -->  0-AddOrder   1-UpdateOrder   2-AddGoods   3-UpdateGoods   4-DeleteGoods   5-AddSupplier   6-UpdateSupplier   7-DeleteSupplier   8-B2cUpdateOrder.php
		//st   -->  0-未傳送      1-已傳送
		
		$dba->query("INSERT INTO transtemp (`fi_no`, `type`, `sql`, `st`, `postdate`) VALUES (NULL, '".$type."', '".$str_erp."', '".$check_erp."', '".date('Y-m-d H:i:s')."') ");
		return $this;
	}
}
?>
<?php
	//ini_set("display_errors", "On");
	//error_reporting(E_ALL);
class Main_Models {
	static private $aa=1088;
	private $bb=111;
	private $start_row = 2;
	static private $arr=array("0"=>0);
	
	public function __construct() {
		
	}
	public function test(){ 
		
	} 
	
	public function home() {
		$memcache_obj = memcache_connect('192.168.0.122', 9901);
		memcache_flush($memcache_obj);
		$memcache_obj = new Memcache;
		$memcache_obj->connect('192.168.0.122', 9901);
		echo $memcache_obj->flush();
		
		require_once "swop/library/dba.php";
		$dba = new dba();
		
		$dba->debug_mode=true;
		//$dba->query("select images,introduction_images from goods_info where fi_no=1");

		/*$result=$dba->query("select * from order_index where store=1 order by fi_no desc");
		print_r($result);
		echo "<br>--------------------------------------------------------------<br>";
		$result = $dba->query("INSERT INTO order_index (`fi_no`, `member`, `sn`, `date`, `store`, `direct`, `fragile`, `subtotal`, `shipping_fee`, `discounts`, `payments`, `checkout`, `exchange_rate`, `status_order`, `status_pay`, `status_transport`, `status_receiving`, `date_transport`, `date_receiving`, `application_returns`, `application_exchanges`, `application_rework`, `trace`, `remind`) VALUES (NULL, '1', '15030509522963291401', '2015-03-05 09:52:29', '1', '0', '0', '85.00', '22.00', '', '107', '', '', '1', '1', '0', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '0', '[]', '0')");
		echo $result;
		echo "<br>--------------------------------------------------------------<br>";
		$result = $dba->query("select * from order_index where store=1 order by fi_no desc");
		print_r($result);
		/**/
		
		//$result=$dba->query("select * from attribute where fi_no IN ()");
		//print_r($result);
		$result = $dba->query("insert into administrator_log (administrator,action,action_date) values ('','23','11111111(11111111) 登入', NOW())");
		print_r($result);
		/*$dba->query("");*/
		
		//$dba->query("select * from order_index where store=1 order by fi_no desc");
		echo "<br>^^<br>";
		
	}
}
?>
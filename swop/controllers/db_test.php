<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
		
	}

	public function home() {
		require_once "swop/models/main.php";
		$swop = new Main_Models();
		$swop->home();
		
		
		include_once "swop/language/".$this->base['lang']."/common.php";
		
		return $this;
	}
	
	public function upload_xls()
	{
		include "swop/view/upload_xls.php";
	}
	
	public function convert()
	{
		require_once "swop/models/db_test.php";
		$swop = new Main_Models();
		$swop->convert();
	}
	
	public function demo() {
		$a_start = microtime(true);
		//echo "g1:".(microtime(true) - $a_start)."<br /><br />";
		require_once "swop/models/db_test.php";
		//echo "g2:".(microtime(true) - $a_start)."<br /><br />";
		$swop = new Main_Models();
		//echo "g3:".(microtime(true) - $a_start)."<br /><br />";
		$swop->home();
		echo "demo";
		//echo "g4:".(microtime(true) - $a_start)."<br /><br />";
		//$dba=new dba();
		//print_r($dba->get_all_table_tree());
		
		
		//$db=new db("localhost","global2user","wRusUdRAjesWuqE3rech","global2buy",3306);
		//echo $db->get_connect()."^^";
		//$db->connect();
		//echo $db->get_connect()."^^";
		
		
		/*
		for($i=0; $i<10000; $i++) {
			$global_link = mysql_connect("localhost", "global2user", "wRusUdRAjesWuqE3rech", TRUE) or die ("無法連接".mysql_error());
			mysql_select_db("global2buy", $global_link) or die ("無法選擇資料庫".mysql_error());
			mysql_query("SET NAMES 'UTF8'"); 
			mysql_query("SET CHARACTER SET UTF8");
			mysql_query("SET CHARACTER_SET_RESULTS=UTF8");
			$sql = "INSERT INTO goods_index0 (name,price) VALUES ('demo".$i."', '".$i."')";
			$result = mysql_query($sql, $global_link) or die ("無法查詢".mysql_error());

			$nextid = mysql_insert_id();
			$lastid = substr($nextid, -1);
			
			$goods_link = mysql_connect("localhost", "global2user", "wRusUdRAjesWuqE3rech", TRUE) or die ("無法連接".mysql_error());
			mysql_select_db("goods".($lastid), $goods_link) or die ("無法選擇資料庫".mysql_error());
			mysql_query("SET NAMES 'UTF8'"); 
			mysql_query("SET CHARACTER SET UTF8");
			mysql_query("SET CHARACTER_SET_RESULTS=UTF8");
			$sql = "INSERT INTO goods_info(no,name,depiction,price) VALUES ('".($nextid)."', 'demo".$i."', 'abcd', '".$i."')";
			$result = mysql_query($sql, $goods_link) or die ("無法查詢".mysql_error());
		}
		*/
		
		/*
		$global_link = mysql_connect("localhost", "global2user", "wRusUdRAjesWuqE3rech", TRUE) or die ("無法連接".mysql_error());
		mysql_select_db("global2buy", $global_link) or die ("無法選擇資料庫".mysql_error());
		mysql_query("SET NAMES 'UTF8'"); 
		mysql_query("SET CHARACTER SET UTF8");
		mysql_query("SET CHARACTER_SET_RESULTS=UTF8");
		$sql = "SELECT * FROM goods_index0 WHERE name LIKE '%345%' LIMIT 0 , 20";
		$result = mysql_query($sql, $global_link) or die ("無法查詢".mysql_error());
		$arr = array();
		while($row = mysql_fetch_array($result)) {
			$arr[] = $row;
		}
		
		for($i=0; $i<10; $i++)
		{
			${"a".$i} = array();
			for($j=0; $j<count($arr); $j++) {
				if(substr($arr[$j]['no'], -1) == $i) {
					array_push(${"a".$i}, $arr[$j]['no']);
				}
			}
			if(count(${"a".$i}));
			$goods_link = mysql_connect("localhost", "global2user", "wRusUdRAjesWuqE3rech", TRUE) or die ("無法連接".mysql_error());
			mysql_select_db("goods".$i, $goods_link) or die ("無法選擇資料庫".mysql_error());
			mysql_query("SET NAMES 'UTF8'"); 
			mysql_query("SET CHARACTER SET UTF8");
			mysql_query("SET CHARACTER_SET_RESULTS=UTF8");
			echo $sql = "SELECT * FROM goods_info WHERE no IN(".implode(',', ${"a".$i}).")";
			$result = mysql_query($sql, $goods_link) or die ("無法查詢".mysql_error());
			$brr = array();
			while($row = mysql_fetch_array($result)) {
				$brr[] = $row;
			}
			print_r($brr);
		}
		*/
					
		echo '<div class="template_bottom"></div>';
	}
}
?>
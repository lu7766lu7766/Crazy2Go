<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
		
	}
	
	public function ajax_getrate() {
		$yesterday = date("Y/m/d",strtotime("-1 day"));
		
		require_once "swop/library/dba.php";
		$dba = new dba();
		$exchange_rate_o = $dba->query("SELECT * FROM exchange_rate WHERE `date` = '".$yesterday."' ");
		
		if(count($exchange_rate_o) == 0) {
			$cookie_file = 'cookie.txt';
			$agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36';
			
			$url_a = "https://ebank.megabank.com.tw/global2/ExternalRateQuery?page=PRS300";
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url_a);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_REFERER , $url_a);
			curl_setopt($ch, CURLOPT_COOKIEFILE , $cookie_file);
			curl_setopt($ch, CURLOPT_COOKIEJAR , $cookie_file);
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			$shtml = curl_exec($ch);
			curl_close($ch);
			
			//echo $shtml;
			
			$shtml_a_1 = explode('action="', $shtml);
			$shtml_a_2 = explode('"', $shtml_a_1[1]);
			
			$post = array(
				"main:currency"=>"39",
				"typeGrp"=>"LST",
				"main:startDate"=>$yesterday,
				"main:endDate"=>$yesterday,
				"main:downloadType"=>"TEXT",
				"main"=>"main",
				"autoDisabled:main:_id24"=>"",
				"autoDisabled:main:fileDownload"=>"",
				"submitTrigger"=>"",
				"autoDisabled:main:_id23"=>"AutoDisabled:Clicked"
			);
			
			$url_b = 'https://ebank.megabank.com.tw'.$shtml_a_2[0];
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url_b);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_REFERER , $url_a);
			curl_setopt($ch, CURLOPT_COOKIEFILE , $cookie_file);
			curl_setopt($ch, CURLOPT_COOKIEJAR , $cookie_file);
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			$shtml = curl_exec($ch);
			curl_close($ch);
			
			//echo $shtml;
			
			$shtml_b_1 = explode('<td class="con_td money_td">', $shtml);
			for($i=0; $i<count($shtml_b_1); $i++) {
				$shtml_b_2 = explode('</td>', $shtml_b_1[$i]);
				$shtml_b_3[] = $shtml_b_2[0];
			}
			$shtml_b_4 = explode('<td class="con_td">', $shtml);
			$shtml_b_5 = explode('</td>', $shtml_b_4[1]);
			
			/*
			echo "現金買匯".$shtml_b_3[2]."<br />";
			echo "現金賣匯".$shtml_b_3[4]."<br />";
			echo "平均匯率".sprintf("%01.5f", (($shtml_b_3[2]+$shtml_b_3[4])/2))."<br />";
			echo "匯率日期".$shtml_b_5[0]."<br />";
			*/
			
			$exchange_rate_e = $dba->query("INSERT INTO exchange_rate (`fi_no`, `buy`, `sell`, `rate`, `date`) VALUES (NULL, '".$shtml_b_3[2]."', '".$shtml_b_3[4]."', '".sprintf("%01.5f", (($shtml_b_3[2]+$shtml_b_3[4])/2))."', '".$shtml_b_5[0]."' ) ");
			
			$this->exchange_rate = sprintf("%01.5f", (($shtml_b_3[2]+$shtml_b_3[4])/2));
		}
		else {
			$this->exchange_rate = $exchange_rate_o[0]['rate'];
		}
		
		return $this;
	}
	
	public function ajax_sendout_check() {
		$afterday = date("Y-m-d H:i:s",strtotime("+3 day"));
		require_once "swop/library/dba.php";
		$dba = new dba();
		//$goods_index = $dba->query("UPDATE goods_index SET `status_order` = '1', `status_pay` = '2' && `status_transport` = '2' && `status_receiving` = '2' WHERE `date_transport` >= '".$afterday."' && `status_order` = '1' && `status_pay` = '2' && `status_transport` = '2' && `status_receiving` = '1' ");
	}
	
	public function ajax_confirm_check() {
		$afterday = date("Y-m-d H:i:s",strtotime("+6 day"));
		require_once "swop/library/dba.php";
		$dba = new dba();
		//$goods_index = $dba->query("UPDATE goods_index SET `status_order` = '3' && `status_pay` = '2' && `status_transport` = '2' && `status_receiving` = '2' WHERE `date_transport` >= '".$afterday."' && `status_order` = '1' && `status_pay` = '2' && `status_transport` = '2' && `status_receiving` = '2' ");
	}
	
	public function ajax_b2cupdateorder() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		/*
		a	序號sn
		b	status_order
		c	status_pay
		d	status_trans
		e	status_recei
		f	transSup
		g	checkout
		
		{"2015/02/26 09:34:38":["5","123456789"]}
		1：台灣直郵
		2：順豐快遞
		3：申通快遞
		4：韻達快遞
		5：郵政EMS(特快)
		*/
		$goods_index = $dba->query("UPDATE order_index SET `checkout` = '".$_POST['g']."', `status_order` = '".$_POST['b']."', `status_pay` = '".$_POST['c']."', `status_transport` = '".$_POST['d']."', `status_receiving` = '".$_POST['e']."', `trace` = '".$_POST['f']."' WHERE `fi_no` >= '".$_POST['a']."' ");
	}
}
?>
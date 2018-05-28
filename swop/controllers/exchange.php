<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		
	}
	
	public function ajax_getrate() {
		require_once "swop/models/exchange.php";
		$swop = new Main_Models();
		$swop->ajax_getrate();
		
		echo $swop->exchange_rate;
	}
	
	public function ajax_sendout_check() {
		require_once "swop/models/exchange.php";
		$swop = new Main_Models();
		$swop->ajax_sendout_check();
	}
	
	public function ajax_confirm_check() {
		require_once "swop/models/exchange.php";
		$swop = new Main_Models();
		$swop->ajax_confirm_check();
	}
	
	public function ajax_b2cupdateorder() {
		if($_POST['a']!="" && $_POST['b']!="" && $_POST['c']!="" && $_POST['d']!="" && $_POST['e']!="" && $_POST['f']!="" && $_POST['g']!="") {
			require_once "swop/models/exchange.php";
			$swop = new Main_Models();
			$swop->ajax_b2cupdateorder();
		}
	}
}
?>
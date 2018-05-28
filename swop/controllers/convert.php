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
	//上傳頁面
	public function upload_xls()
	{
		include "swop/view/upload_xls.php";
	}
	//xls上傳轉換
	public function xls2db()
	{
		require_once "swop/models/convert.php";
		$swop = new Main_Models();
		$swop->xls2db();
	}
	
}
?>
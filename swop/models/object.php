<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
	}
	
	public function ajax_sidebar() {		
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->global_item = $dba->query("select * from global_item where `show` = '1' order by `type`, `weights` DESC ");
		return $this;
	}
}
?>
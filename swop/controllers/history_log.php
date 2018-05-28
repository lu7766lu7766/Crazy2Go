<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
		
	}

	public function home() {
		
		return $this;
	}
	
	public function ajax_set_time_enter(){
		require_once "swop/models/history_log.php";
		$swop = new Main_Models();
		$swop->ajax_set_time_enter();
		return $this;
	}
	
	public function ajax_page_out() {
		//echo count($_POST);
		if( count($_POST) == 5 ) {
			require_once "swop/models/history_log.php";
			$swop = new Main_Models();
			$swop->os = $_POST["os"];
			$swop->browser = $_POST["browser"];
			$swop->page_enter = $_POST["page_enter"];
			$swop->page_now = $_POST["page_now"];
			$swop->obj_xpath = $_POST["obj_xpath"];
			$swop->ajax_page_out();
			if($swop->error!=0){
				$this->echo_json($swop->error,$swop->message,$swop->exit);
			}
		}
	}
	
	public function ajax_get_history(){
		if( count($_POST) == 2 ) {
			require_once "swop/models/history_log.php";
			$swop = new Main_Models();
			$swop->page_now = $_POST["page_now"];
			$swop->analysis_mode = $POST["analysis_mode"];
			$json=$swop->ajax_get_history();
			if($swop->error!=0){
				$this->echo_json($swop->error,$swop->message,$swop->exit);
			}
			echo $json;
		}else{
			echo count($_POST);
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
}
?>
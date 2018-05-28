<?php
	session_start();
class Main_Models {
	
	public $os = "";
	public $browser = "";
	public $page_enter = "";
	public $page_now = "";
	public $obj_path = "";
	public $error = 0;
	public $analysis_mode = "";

	public function __construct() {
		
	}

	public function home() {
		return $this;
	}
	
	public function ajax_set_time_enter(){
		$_SESSION["time_enter"] = date( "Y-m-d H:i:s",time() );
	}
	
	public function ajax_page_out() {
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip=$_SERVER['REMOTE_ADDR'];
		$time_enter = $_SESSION["time_enter"];
		$time_goout = date( "Y-m-d H:i:s",time() );
		$os = $this->os;
		$browser = $this->browser;
		$page_enter = $this->page_enter;
		$page_now = $this->page_now;
		$obj_xpath = $this->obj_xpath;
		$sess_id = session_id();
		if( $obj_xpath=="/html" || $obj_xpath=="/html/body" ){
			return;
		}
		if( $page_now==""||$obj_xpath=="" ){
			$this->error = 1;
			$this->message = "didn't find key word!";
			$this->exit = 0;
			return;
		}
		$sql = "insert into `history` (`type`,`session_id`,`os`,`ip`,`browser`,`page_enter`,`page_now`,`time_enter`,`time_goout`,`click_object`)
							   values ('1','$sess_id','$os','$ip','$browser','$page_enter','$page_now','$time_enter','$time_goout','$obj_xpath')";
		$dba->query($sql);
	}
	
	public function ajax_get_history(){
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		
		$page_now = $this->page_now;
		$analysis_mode = $this->analysis_mode;
		if($analysis_mode){
			$page_condition = "`page_now`='$page_now'";
		}else{
			$page_condition = "`page_now` like '$page_now%'";
		}
		
		$sql = "select `click_object` from `history` where $page_condition and click_object<>'' and type='1'";
		$result = $dba->query($sql);
		$len = count($result);
		if($len==0)return "";
		
		foreach($result as $p_xpath){
			$xpath = $p_xpath["click_object"];
			$xpath = strtr( $xpath,array("/html"=>"","/body"=>"") );
			if( $xpath=="" )continue;
			$dom = explode("/",$xpath);
			$xpath = "";
			$last_dom = end($dom);
			foreach($dom as $p_dom){
				if($p_dom=="")continue;
				preg_match('/^([\w]+)\[?([\d]*)\]?$/',$p_dom, $matches);
				$tag_name = $matches[1];
				$index = $matches[2]==""?0:$matches[2]-1;
				$xpath.= $tag_name.":eq(".$index.")";
				if($last_dom!=$p_dom)$xpath.=">";
			}
			$a_xpath[] = $xpath;
		}
		foreach($a_xpath as $val){
			$a_keyXpath[$val]+=1;
		}
		return json_encode($a_keyXpath);
	}
	
}
?>
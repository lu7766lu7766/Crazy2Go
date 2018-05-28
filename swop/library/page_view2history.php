<?php 
	session_start();
	function page_view2history($data){
		global $dba;
		
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip=$_SERVER['REMOTE_ADDR'];
		$time_interval = 60*60; //1 hour 3600
		$browser = $_SERVER['HTTP_USER_AGENT'];
		$sess_id = session_id();
		$time = time();
		$time_goout = date("Y-m-d H:i:s",$time);
		
		$os 		= $data["os"];
		$fi_no 		= $data["fi_no"];
		$table_name = $data["table_name"];
		$time_enter = $data["time_enter"];
		$page_enter = $data["page_enter"];
		$page_now 	= $data["page_now"];
		$obj_xpath	= $data["obj_xpath"];
		
		if( $table_name=="" || $fi_no=="" || $page_now=="" ){
			throw new exception("didn't find key word!");
		}
		
		if(!is_object($dba)){
			$dba_path = array("swop/library/dba.php","../swop/library/dba.php");
			foreach($dba_path as $path){
				if(file_exists($path)){
					$dba_path = $path;
					$b_dba_path = true;
					break;
				}
			}
			if(!$b_dba_path)throw new exception("didn't find dba object!");
			include_once($dba_path);
			$dba = new dba();
		}
		
		if( $time-$_SESSION[$page_now]["time_goout"]>$time_interval ){
			$sql = "update `$table_name` set click=click+1 where fi_no='$fi_no'";
			$dba->query($sql);
			$_SESSION[$page_now]["time_goout"] = $time;
		}
		
		$sql = "insert into `history` (`type`,`session_id`,`os`,`ip`,`browser`,`page_enter`,`page_now`,`time_enter`,`time_goout`,`click_object`)
							   values ('2','$sess_id','$os','$ip','$browser','$page_enter','$page_now','$time_enter','$time_goout','$obj_xpath')";
		$dba->query($sql);
		
	}	
?>
<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->floors = $dba->query("select * from floors where `show`=1 order by weights desc");
		$this->advertisement = $dba->query("select * from advertisement where `page` = 'main' && `show`=1 order by weights desc");
                $this->main_item = $dba->query("select * from main_item where `show`=1 order by type asc, weights desc");
		/*
		$con=mysqli_connect("localhost","global2user","wRusUdRAjesWuqE3rech","global2buy");
		if (mysqli_connect_errno()) {echo "Failed to connect to MySQL: " . mysqli_connect_error();}
		mysqli_query($con,"SET NAMES 'UTF8'"); 
		mysqli_query($con,"SET CHARACTER SET UTF8");
		mysqli_query($con,"SET CHARACTER_SET_RESULTS=UTF8");
		$sql="select * from view_floors";
		$result=mysqli_query($con, $sql);

		$this->results = array();
		while($row = mysqli_fetch_array($result)) {
			$this->results[] = $row;
		}
		*/
		
		return $this;
	}
}
?>
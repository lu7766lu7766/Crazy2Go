<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
		//require_once "swop/library/dba.php";
		//$dba=new dba();
		//$this->floors = $dba->query("select * from view_floors ");
		
                
		return $this;
	}
        
        public function ajax_select_service(){
            
            require_once "swop/library/dba.php";
            $dba=new dba();
            //取得客服資料
            $this->data = $dba->query("select * from service where type=".$_POST["type"]." and store=".$_POST["store"]." order by lost_date asc");
            $this->data = $this->data[0];
            //更新lost_date
            $dba->query("update service set lost_date=NOW() where fi_no=".$this->data['fi_no']);
            //寫入用戶端開始聊天時間
            $dba->query("insert into service_item (service, start_date) values (".$this->data['fi_no'].", NOW());");
            
            /*$mysqli = new mysqli("106.186.127.17","global2user","wRusUdRAjesWuqE3rech","global2buy");
            if(mysqli_connect_errno()){
                echo "db connect error!".mysqli_connect_error();
                exit();
            }else{
                $mysqli->query("SET NAMES 'UTF-8'");
                $mysqli->query("SET CHARACTER SET UTF8");
                $mysqli->query("SET CHARACTER_SET_RESULT=UTF8");
                
                //取得客服資料
                $query = "select * from service where type=".$_POST["type"]." order by lost_date asc";
                $result = $mysqli->query($query);
                $this->data = $result->fetch_assoc();
                
                //更新lost_date
                $query = "update service set lost_date=NOW() where fi_no=".$this->data["fi_no"];
                $mysqli->query($query);
                
                //寫入用戶端開始聊天時間
                $query = "insert into service_item (service, start_date) values (".$this->data["fi_no"].", NOW());";
                $mysqli->query($query);
                
                $result->close();
                $mysqli->close();
            }*/
            
            return $this;
        }
}
?>
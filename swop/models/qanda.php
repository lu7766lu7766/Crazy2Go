<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
            require_once "swop/library/dba.php";
            $dba=new dba();
            $this->data = $dba->query("select * from `qanda` where `show`=1 order by `index` ASC, `weights` DESC");
            if(isset($_GET["no"]))
            {
                $qa_no = $dba->query("select qanda_no from qanda_item where `show`=1 and fi_no=".$_GET["no"]." order by weights desc");
                $this->default_fi_no = $qa_no = $qa_no[0]["qanda_no"];
            }
            
            return $this;
	}
      
        public function ajax_select_qa(){
            require_once "swop/library/dba.php";
            $dba=new dba();
            $this->data = $dba->query("select * from qanda_item where `show`=1 and qanda_no=".$_POST["qa_no"]." order by weights desc");
            
            return $this;
        }
        
        public function ajax_qa_search(){
            require_once "swop/library/dba.php";
            $dba=new dba();
            $this->page = (int)$_POST['page'];
            $this->keyword = $_POST['keyword'];
            $this->data = $dba->query("select * from qanda_item where issue like '%".$this->keyword."%' or answers like '%".$this->keyword."%' order by qanda_no asc, weights desc");
            $this->total = count($this->data);
            $this->data = array_slice($this->data, ((int)$_POST['page']-1)*10, 10);
            
            return $this;
        }
        
}
?>
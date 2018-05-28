<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		require_once "swop/models/service.php";
		$swop = new Main_Models();
		$swop->home();
                
                $this->js = array('service');
				
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/service.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
        
        public function ajax_select_service(){
            require_once "swop/models/service.php";
            $swop = new Main_Models();
            $swop->ajax_select_service();
            echo $swop->data['qq'];
            return $this;
        }
        
        
}
?>
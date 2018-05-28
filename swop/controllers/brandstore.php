<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
                $now_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            
                if(!(isset($_GET["store"]) || isset($_GET["brand"])))
                {
                    header("Location:".$this->base["url"]);
                    exit();
                }
                
                if(isset($_GET["store"]) && $_GET["store"]=="")
                {
                    header("Location:".$this->base["url"]);
                    exit();
                }
                
                if(isset($_GET["brand"]) && $_GET["brand"]=="")
                {
                    header("Location:".$this->base["url"]);
                    exit();
                }
                
                if(!isset($_GET["page"]))
                {
                    $_GET["page"] = 1;
                }
                
		require_once "swop/models/brandstore.php";
		$swop = new Main_Models($_GET);
		$swop->home();
                
                $this->js = array('brandstore');
                //$this->css = array('brand');
                
                require_once "swop/library/pagination.php";
                $page = new Library_Pagination();
                $page->pagination($swop->num, 5, 16, $now_url, 0, 0, 0, $_GET['page']);
                
                require_once "swop/library/sortby.php";
                $sort = new Sortby_Pagination();
                $sort->sortby($now_url);
			
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/brandstore.php";
		include_once "swop/view/template/bottom.php";
		return $this;
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
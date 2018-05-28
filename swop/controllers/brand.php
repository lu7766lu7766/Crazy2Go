<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		require_once "swop/models/brand.php";
		$swop = new Main_Models();
		$swop->home();
                
                $this->js = array('brand');
                //$this->css = array('brand');
				
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/brand.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
        
        public function ajax_alpha(){
            require_once "swop/models/brand.php";
            $swop = new Main_Models();
            $swop->ajax_alpha();

            require_once "swop/library/pagination.php";
            $page = new Library_Pagination();
            $page->pagination($swop->alpha_total, 5, 30, '', 1, 0, 1,$_POST["page"]);
            $ouput['page_content'] = $page->page_content;
            $ouput["data"] = $swop->alpha;
            $this->echo_json("0", "", 0, $ouput);
            
            return $this;
        }
        
        public function ajax_class(){
            require_once "swop/models/brand.php";
            $swop = new Main_Models();
            $swop->ajax_class();

            require_once "swop/library/pagination.php";
            $page = new Library_Pagination();
            $page->pagination($swop->class_total, 5, 30, '', 1, 0, 1,$_POST["page"]);
            $ouput['page_content'] = $page->page_content;
            $ouput["data"] = $swop->class;
            $this->echo_json("0", "", 0, $ouput);
            
            return $this;
        }
        
        public function ajax_keyword(){
            require_once "swop/models/brand.php";
            $swop = new Main_Models();
            $swop->ajax_keyword();

            require_once "swop/library/pagination.php";
            $page = new Library_Pagination();
            $page->pagination($swop->keyword_total, 5, 30, '', 1, 0, 1,$_POST["page"]);
            $ouput['page_content'] = $page->page_content;
            $ouput["data"] = $swop->keyword;
            $this->echo_json("0", "", 0, $ouput);
            
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
        
        public function arr_url_json($arr) {
            if(is_array($arr)) {
                    $b_key5str = false;

                    if((bool)count(array_filter(array_keys($arr), 'is_string'))) {
                            $b_key5str = true;
                    }

                    foreach($arr as $key => $val) {
                            if($b_key5str) {
                                    $json .= '"'.$key.'":';
                            }

                            if(is_array($val)) {
                                    $json .= $this->arr_url_json($val).",";
                            }
                            else if(is_string($val)) {
                                    $json .= '"'.$val.'",';
                            }
                            else if(is_numeric($val)) {
                                    $json .= $val.',';
                            }
                    }

                    if($b_key5str) {
                            return "{".substr($json,0,-1)."}";
                    }
                    else {
                            return "[".substr($json,0,-1)."]";
                    }
            }
            else {
                    throw new exception("It's not an array!");
            }
	}
	
	public function json_url_arr($ja) {
	    return json_decode($ja, true);
	}
}
?>
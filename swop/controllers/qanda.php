<?php
class Main_Controllers {

	public function __construct($base) {
		$this->base = $base;
	}

	public function home() {
		require_once "swop/models/qanda.php";
		$swop = new Main_Models();
		$swop->home();
                
                $this->js = array('qanda');
                //$this->css = array('qanda');
				
		include_once "swop/language/".$this->base['lang']."/common.php";
		include_once "swop/view/template/top.php";
		include_once "swop/view/qanda.php";
		include_once "swop/view/template/bottom.php";
		return $this;
	}
        
        public function ajax_select_qa(){
            require_once "swop/models/qanda.php";
            $swop = new Main_Models();
            $swop->ajax_select_qa();
            
            $len = count($swop->data);
            for($i = 0; $i < $len; $i++)
            {
                echo "<div fi_no='".$swop->data[$i]["fi_no"]."'>";
                echo "<h3>".$swop->data[$i]["issue"]."</h3>";
                echo "<p>".$swop->data[$i]["answers"]."</p>";
                echo "</div>";
            }
            
            return $this;
        }
        
        public function ajax_qa_search(){
            require_once "swop/models/qanda.php";
            $swop = new Main_Models();
            $swop->ajax_qa_search();
            echo "共 ".$swop->total." 筆資料";
            echo "<pre>";
            
            $sLen = count($swop->data);
            $sContent = "";
            for($i = 0; $i < $sLen; $i++)
            {
                $replaceStr = substr($swop->data[$i]['issue'],stripos($swop->data[$i]['issue'],$swop->keyword),strlen($swop->keyword));
                $sContent .= "<h3>".preg_replace('/'.$swop->keyword.'/i', "<span style='color:red'>".$replaceStr."</span>", $swop->data[$i]['issue'])."</h3>";
                $replaceStr = substr($swop->data[$i]['answers'],stripos($swop->data[$i]['answers'],$swop->keyword),strlen($swop->keyword));
                $sContent .= "<p>".preg_replace('/'.$swop->keyword.'/i', "<span style='color:red'>".$replaceStr."</span>", $swop->data[$i]['answers'])."</p>";
            }
            echo $sContent;
            
            $pLen = ceil($swop->total/10);
            $pContent = "<div style='text-align:center'>";
            if($swop->page != 1)$pContent .= "<a href='javascript:void(0);search(".($swop->page-1).");'><</a>";
            for($i = 1; $i <= $pLen; $i++)
            {
                if($swop->page == $i)
                {
                    $pContent .= "<b>".$i."</b>";
                }else{
                    $pContent .=  "<a href='javascript:void(0);search(".$i.");'>".$i."</a>";
                }
            }
            if($swop->page != $pLen)$pContent .= "<a href='javascript:void(0);search(".($swop->page+1).");'>></a>";
            $pContent .= "</div>";
            if($pLen == 0 ||$pLen == 1) $pContent = "";
            echo $pContent;

        }
        
}
?>
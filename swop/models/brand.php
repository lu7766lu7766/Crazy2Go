<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
            
            require_once "swop/library/dba.php";
            $dba=new dba();
            $this->brand_class = $dba->query("select fi_no,name from category where `show`=1 and `delete`=0 order by `index` asc, weights desc");
            $this->brand_total_count = $dba->query("select count(fi_no) as total from brand_group");
            $this->brand_total_count = $this->brand_total_count[0]["total"];
            
            $this->brand_footer = $dba->query("select fi_no, type, `index`, images, url, item from advertisement where page='brand' and `show`=1 order by weights desc");
            
            return $this;
	}
      
        public function ajax_alpha(){
            
            require_once "swop/library/dba.php";
            $dba=new dba();
            
            switch ($_POST["keyword"])
            {
                case "All":
                    $k = "";
                    break;
                case "其他":
                    $k = "and name REGEXP '^[^a-zA-Z]'";
                    break;
                default :
                    $k = "and name like '".$_POST["keyword"]."%'";
            }
            
            $start = ($_POST["page"]-1)*30;
            $this->alpha = $dba->query("select fi_no,name,logo from brand_group where `delete`=0 ".$k." order by weights desc limit ".$start.",30");
            $this->alpha_total = $dba->query("select count(fi_no) as total from brand_group where `delete`=0 and name ".$k." order by weights desc");
            $this->alpha_total = $this->alpha_total[0]["total"];
            
            return $this;
        }
        
        public function ajax_class(){
            
            require_once "swop/library/dba.php";
            $dba=new dba();
            $start = ($_POST["page"]-1)*30;
            
            $all = $dba->query("select fi_no,`index` from category order by `index` asc, fi_no asc");
            $pick = array();
            function find_sub_class($all,$pick,$k){
                $len = count($all);
                $pick[] = $k;
                for($i=0;$i<$len;$i++)
                {
                    if($all[$i]["index"] == $k)
                    {
                        $pick[]=$all[$i]["fi_no"];
                        $pick2 = find_sub_class($all,$pick,$all[$i]["fi_no"]);
                        $pick = array_merge($pick,$pick2);
                    }
                }
                return $pick;
            }
            $pick = find_sub_class($all,$pick,$_POST["keyword"]);
            $pick = array_values(array_unique($pick));
            $cat_pick_count = count($pick);
            $cat_pick = " (";
            for($i=0;$i<$cat_pick_count;$i++)
            {
                if($i==0)
                {
                    $cat_pick .= " category like '%\"".$pick[$i]."\"%' ";
                }
                else
                {
                    $cat_pick .= " or category like '%\"".$pick[$i]."\"%' ";
                }
            }
            $cat_pick .= ") ";
            $this->class = $dba->query("select fi_no,name,logo from brand_group where `delete`=0 and ".$cat_pick." order by weights desc limit ".$start.",30");
            $this->class_total = $dba->query("select count(fi_no) as total from brand_group where `delete`=0 and ".$cat_pick." order by weights desc");
            $this->class_total = $this->class_total[0]["total"];
            
            return $this;
        }
        
        public function ajax_keyword(){
            
            require_once "swop/library/dba.php";
            $dba=new dba();
            $start = ($_POST["page"]-1)*30;
            $this->keyword = $dba->query("select fi_no,name,logo from brand_group where `delete`=0 and name like '%".$_POST["keyword"]."%' order by category asc, weights desc limit ".$start.",30");
            $this->keyword_total = $dba->query("select count(fi_no) as total from brand_group where `delete`=0 and name like '%".$_POST["keyword"]."%' order by category asc, weights desc");
            $this->keyword_total = $this->keyword_total[0]["total"];
            
            return $this;
        }
        
}
?>
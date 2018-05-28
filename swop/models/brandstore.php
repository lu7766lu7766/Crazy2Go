<?php
class Main_Models {

	public function __construct($get) {
            $this->get = $get;
	}

	public function home() {
            
            require_once "swop/library/dba.php";
            $dba=new dba();
            
            $orderby = "";
            
            if($this->get['sort'] != '' && $this->get['by'] != '') {
			
                switch($this->get['sort']) {
                    case 'comprehensive':
                        $sort_order = "comprehensive";
                        break;
                    case 'popularity':
                        $sort_order = "click";
                        break;
                    case 'sales':
                        $sort_order = "transaction_times";
                        break;
                    case 'news':
                        $sort_order = "added_date";
                        break;
                    case 'collect':
                        $sort_order = "collect_times";
                        break;
                    case 'credit':
                        $sort_order = "evaluation";
                        break;
                    case 'price':
                        $sort_order = "promotions";
                        break;
                    default:
                        $sort_order = "comprehensive";
                }

                $unsort_array = array('comprehensive', 'popularity', 'news', 'collect', 'sales', 'credit');
                if(!in_array($this->get['sort'], $unsort_array)) { //綜合、人氣、新品、收藏、銷量、信用沒有遞增排序
                    switch($this->get['by']) {
                        case 'desc':
                            $sort_by = "desc";
                            break;
                        case 'asc':
                            $sort_by = "asc";
                            break;
                        default:
                            $sort_by = "desc";
                    }
                }
                else {
                    $sort_by = "desc";
                }

                $orderby = " ORDER BY ".$sort_order." ".$sort_by." ";
            }
            
            switch($this->get['discount']) {
                case "0":
                    $discount = "";
                    break;
                case "1":
                    $discount = " && discount=1 ";
                    break;
                default:
                    $discount = "";
            }
            
            //keyword=特殊&lowprice=0&highprice=100&checkedlist=1,15,16
            $keyword = "";
            if(isset($this->get['bskeyword']))
            {
                if($this->get['bskeyword'] == "")
                {
                    $keyword = "";
                }
                else
                {
                    $keyword = " and name like '%".$this->get['bskeyword']."%' ";
                }
            }
            
            $promo = "";
            if(isset($this->get['lowprice']) && isset($this->get['highprice']))
            {
                if($this->get['lowprice'] == "" && $this->get['highprice'] == "")
                {
                    $promo = "";
                }
                else
                {
                    $promo = " and promotions between ".$this->get['lowprice']." and ".$this->get['highprice']." ";
                }
            }
            
            $checked_list = "";
            if(isset($this->get['checkedlist']))
            {
                if($this->get['checkedlist'] == "")
                {
                    $checked_list = "";
                }
                else
                {
                    $checked_list = " and category in(".$this->get['checkedlist'].") ";
                }
            }
           
            if(isset($this->get["store"]))
            {
                $logo_query = "select name,images from store where fi_no=".$this->get["store"];
                $this->logo = $dba->query($logo_query);
                $this->logo = $this->logo[0];
                $this->logo["text"] = $this->logo["name"];
                $this->logo["graphic"] = $this->logo["images"];
                unset($this->logo["name"]);
                unset($this->logo["images"]);
                
                $this->ad_banner = $dba->query("select images, type, item from store_advertisement where `delete`=0 and store=".$this->get["store"]." and type in(1,2) order by weights desc");
                
                $this->store_brand = $dba->query("select distinct brand from goods_index where `delete`=0 and store=".$this->get["store"]);
                foreach($this->store_brand as $k => $v)$this->store_brand[$k] = $v["brand"];
                $this->store_brand = $dba->query("select fi_no,logo from brand_group where `delete`=0 and fi_no in(".  implode(",", $this->store_brand).")");
            
                $this->store_category = $dba->query("select distinct category from goods_index where `delete`=0 and store=".$this->get["store"]);
                foreach($this->store_category as $k => $v)$this->store_category[$k] = $v["category"];
                $this->store_category = $dba->query("select fi_no,name from category where `delete`=0 and `show`=1 and fi_no in(".implode(",", $this->store_category).")");
                
                $this->store_info = $dba->query("select fi_no, name, company, location, service_score, service_number, quality_score, quality_number from store where fi_no=".$this->get["store"]);
            
                $this->num = $dba->query("select count(fi_no) as total from view_goods_index where `delete`=0 and store=".$this->get["store"].$discount.$keyword.$promo.$checked_list);
                $this->num = $this->num[0]['total'];
                
                $limit = "";
                if($this->get['page'] != '')
                {
                    $this->get['page'] = $this->get['page']>ceil($this->num/16) ?ceil($this->num/16):$this->get['page'];
                    $limit = " LIMIT ".(($this->get['page']-1)*16).",16 ";
                }
                
                $this->product_info = $dba->query("select fi_no, category, brand, name, images, promotions from view_goods_index where `delete`=0 and store=".$this->get["store"].$discount.$keyword.$promo.$checked_list.$orderby.$limit);
            }
            else if(isset($this->get["brand"]))
            {
                $logo_query = "select name,logo from brand_group where fi_no=".$this->get["brand"];
                $this->logo = $dba->query($logo_query);
                $this->logo = $this->logo[0];
                $this->logo["text"] = $this->logo["name"];
                $this->logo["graphic"] = $this->logo["logo"];
                unset($this->logo["name"]);
                unset($this->logo["logo"]);
                
                $this->store_category = $dba->query("select distinct category from goods_index where `delete`=0 and brand=".$this->get["brand"]);
                foreach($this->store_category as $k => $v)$this->store_category[$k] = $v["category"];
                $this->store_category = $dba->query("select fi_no,name from category where `delete`=0 and `show`=1 and fi_no in(".implode(",", $this->store_category).")");
                
                $this->store_info = $dba->query("select store from goods_index where `delete`=0 and brand=".$this->get["brand"]);
                foreach($this->store_info as $k => $v)$this->store_info[$k] = $v["store"];
                $this->store_info = $dba->query("select fi_no, name, company, location, service_score, service_number, quality_score, quality_number from store where fi_no in(".  implode(",", $this->store_info).")");
            
                $this->num = $dba->query("select count(fi_no) as total from view_goods_index where `delete`=0 and brand=".$this->get["brand"].$discount.$keyword.$promo.$checked_list);
                $this->num = $this->num[0]['total'];
                
                $limit = "";
                if($this->get['page'] != '')
                {
                    $this->get['page'] = $this->get['page']>ceil($this->num/16) ?ceil($this->num/16):$this->get['page'];
                    $limit = " LIMIT ".(($this->get['page']-1)*16).",16 ";
                }
                
                $this->product_info = $dba->query("select fi_no, category, brand, name, images, promotions from view_goods_index where `delete`=0 and brand=".$this->get["brand"].$discount.$keyword.$promo.$checked_list.$orderby.$limit);
            }
            
            return $this;
	}
        
}
?>
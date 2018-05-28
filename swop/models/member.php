<?php
class Main_Models {

	public function __construct() {
	}

	public function home() {
                require_once "swop/library/dba.php";
		$dba=new dba();
		$this->login_ad = $dba->query("select images from advertisement where page='login'");
                $this->login_ad = $this->login_ad[0]["images"];
	}
        
        public function center(){
                require_once "swop/library/dba.php";
		$dba=new dba();
		$this->mem_info = $dba->query("select evaluation_score, evaluation_number from member_info where fi_no=".$_SESSION["info"]["fi_no"]);
        
                //積分
                $this->sum_bonus = $dba->query("select sum(increase)-sum(reduce) as bonus from member_currency where type=0 and member=".$_SESSION["info"]["fi_no"]);
                $this->sum_bonus = empty($this->sum_bonus[0]["bonus"])?0:$this->sum_bonus[0]["bonus"];
                
                
                //廣告
                $this->ad = $dba->query("select page, images, url from advertisement where page='member_center'");
                
                //我的物流
                $this->logistic = $dba->query("select fi_no,sn,trace,store,status_receiving from order_form where status_order = 1 && status_pay = 2 && status_transport = 2 && status_receiving in (1,2) and trace<>'[]' and member=".$_SESSION["info"]["fi_no"]." order by fi_no desc limit 0,2");
                $store_arr = array();
                $len = count($this->logistic);
                for($i=0;$i<$len;$i++)
                {
                    $store_arr[] = $this->logistic[$i]["store"];
                }
                $store_arr = array_unique($store_arr);
                $store_arr = implode(",",$store_arr);
                $store = $dba->query("select fi_no, name, images from store where fi_no in(".$store_arr.") order by field(fi_no,".$store_arr.")");
                if($this->logistic[0])
                foreach($this->logistic as $k => $v)
                {
                    switch($v["status_receiving"])
                    {
                        case 1:$this->logistic[$k]["status_receiving_name"]="已發貨";break;
                        case 2:$this->logistic[$k]["status_receiving_name"]="已確認收貨";break;
                    }
                    $this->logistic[$k]["trace"] = json_decode($v["trace"],true);
                    if(($l = count($this->logistic[$k]["trace"]))>0)
                    {
                        $this->logistic[$k]["trace"] = array_pop(array_keys($this->logistic[$k]["trace"]));
                    }
                    for($i=0;$i<$len;$i++)
                    {
                        if($v["store"] == $store[$i]["fi_no"])
                        {
                            $this->logistic[$k]["store_name"] = $store[$i]["name"];
                            $this->logistic[$k]["store_image"] = $store[$i]["images"];
                            break;
                        }
                    }
                }
                
                //瀏覽記錄
                $cookie = $dba->query("select goods from goods_history where member=".$_SESSION["info"]["fi_no"]." order by fi_no desc limit 0,2");
                $this->cookie = array();
                foreach($cookie as $k => $v)
                {
                    $this->cookie[] = $v["goods"];
                }
                $cookie = implode(",",$this->cookie);
                $this->cookie = $dba->query("select fi_no, store, images, evaluation_score, evaluation_number, name from goods_index where fi_no in(".$cookie.") order by field(fi_no,".$cookie.")");
                $len = count($this->cookie);
                $store_arr = array();
                for($i=0;$i<$len;$i++)
                {
                    $store_arr[] = $this->cookie[$i]["store"];
                }
                $store_arr = array_unique($store_arr);
                $store_arr = implode(",",$store_arr);
                $this->store_name = $dba->query("select fi_no, name from store where fi_no in(".$store_arr.")");
                $len = count($this->store_name);
                if($this->cookie[0])
                foreach($this->cookie as $k => $v)
                {
                    for($i=0;$i<$len;$i++)
                    {
                        if($v["store"] == $this->store_name[$i]["fi_no"])
                        {
                            $this->cookie[$k]["store_name"] = $this->store_name[$i]["name"];
                            break;
                        }
                    }
                }
                
                //我的收藏夾
                $this->collect_store = $dba->query("select store from collect_store where `delete`=0 and member=".$_SESSION['info']['fi_no']." order by fi_no desc limit 0,4");
                //$this->collect_goods = $dba->query("select goods from collect_goods where member=".$_SESSION['info']['fi_no']." order by fi_no desc limit 0,4");
                $this->collect_goods = $dba->query("select goods from collect_goods where `delete`=0 and member=".$_SESSION['info']['fi_no']." order by fi_no desc limit 0,8");
                $collect_store = array();
                $collect_goods = array();
                foreach($this->collect_store as $v)
                {
                    $collect_store[] = $v["store"];
                }
                foreach($this->collect_goods as $v)
                {
                    $collect_goods[] = $v["goods"];
                }
                $collect_store = implode(",", $collect_store);
                $collect_goods = implode(",", $collect_goods);
                $this->collect_store = $dba->query("select fi_no, name, images from store where fi_no in(".$collect_store.") order by field(fi_no,".$collect_store.")");
                $this->collect_goods = $dba->query("select fi_no, name, images from goods_index where fi_no in(".$collect_goods.") order by field(fi_no,".$collect_goods.")");
                $this->collect_store_count = $dba->query("select count(fi_no) as total from collect_store where `delete`=0 and member=".$_SESSION['info']['fi_no']);
                $this->collect_store_count = $this->collect_store_count[0]["total"];
                $this->collect_goods_count = $dba->query("select count(fi_no) as total from collect_goods where `delete`=0 and member=".$_SESSION['info']['fi_no']);
                $this->collect_goods_count = $this->collect_goods_count[0]["total"];
                
                //我的購物車
                $cart = explode("｜", $_SESSION['info']['cart']);
                $cart_fi_no = array();
                $cart_info = array();
                for($i=1; $i<count($cart); $i++) {
                        $cart_split = explode("；", $cart[$i]);
                        $cart_additional = explode("；",urldecode($cart_split[2]));
                        $cart_fi_no[] = $cart_split[0];
                        $cart_info[$i]["fi_no"] = $cart_split[0];
                        $cart_info[$i]["inventory_id"] = $cart_additional[0];
                        $cart_info[$i]["attr_length"] = count(explode("｜",$cart_additional[1]));
                        $cart_info[$i]["attr_id"] = explode("｜",$cart_additional[1]);
                        $cart_info[$i]["attr_name"] = explode("｜",$cart_additional[2]);
                        $cart_info[$i]["attr"] = explode("｜",$cart_additional[3]);
                        $cart_info[$i]["buy_in_price"] = $cart_split[3];
                }
                
                $cart_fi_no = implode(",", $cart_fi_no);
                $cart = $dba->query("select fi_no, name, price, promotions, discount, specifications, inventory, images from goods_index where fi_no in(".$cart_fi_no.")");
                $len = count($cart);
                for($i=0;$i<$len;$i++)
                {
                    $cart[$i]["specifications"] = (array)json_decode($cart[$i]["specifications"]);
                    foreach($cart[$i]["specifications"] as $k => $v)
                    {
                        $cart[$i]["specifications_name"][] = $k;
                        $cart[$i]["specifications_value"][] = $v;
                    }
                    unset($cart[$i]["specifications"]);
                    $cart[$i]["inventory"] = (array)json_decode($cart[$i]["inventory"]);
                }

                $cart_info_new = array();
                foreach($cart_info as $k => $v)
                {
                    $cart_info_new[$v["fi_no"]] = $v;
                }
                $cart_new = array();
                foreach($cart as $k => $v)
                {
                    $cart_new[$v["fi_no"]] = $v;
                }
                
                $this->discount_count = 0;
                $this->inventory_low_count = 0;
                $this->cart = array();
                $push_twice = 0;
                foreach($cart_info_new as $k => $v)
                {
                    $len = $cart_info_new[$k]["attr_length"];
                    $same = true;
                    //比對屬性是否一致
                    for($i = 0; $i<$len;$i++)
                    {
                        if($cart_info_new[$k]["attr_name"][$i] != $cart_new[$k]["specifications_name"][$i])
                        {
                            $same = false;
                            unset($cart_info_new[$k]);
                            unset($cart_new[$k]);
                            break;
                        }
                        if($cart_info_new[$k]["attr"][$i] != $cart_new[$k]["specifications_value"][$i][$cart_info_new[$k]["attr_id"][$i]])
                        {
                            $same = false;
                            unset($cart_info_new[$k]);
                            unset($cart_new[$k]);
                            break;
                        }
                    }
                    if($same)
                    {
                        //近期優惠
                        if($cart_new[$k]["discount"] != 0)
                        {
                            $this->discount_count++;
                            $cart_new[$k]["current_buy_price"] = $cart_new[$k]["discount"];
                        }
                        else
                        {
                            $cart_new[$k]["current_buy_price"] = $cart_new[$k]["promotions"];
                        }
                        //庫存緊張
                        if($cart_new[$k]["inventory"][$cart_info_new[$k]["inventory_id"]] < 5)
                        {
                            $this->inventory_low_count++;
                            $cart_new[$k]["inventory_low"] = true;
                        }
                        else
                        {
                            $cart_new[$k]["inventory_low"] = false;
                        }
                        //放入購物車時價格與目前價格價差
                        $cart_new[$k]["price_diff"] = $cart_info_new[$k]["buy_in_price"]-$cart_new[$k]["current_buy_price"];
                    
                        //過濾陣列
                        unset($cart_new[$k]["promotions"]);
                        unset($cart_new[$k]["discount"]);
                        unset($cart_new[$k]["inventory"]);
                        unset($cart_new[$k]["specifications_name"]);
                        unset($cart_new[$k]["specifications_value"]);
                        
                        if($push_twice<2)
                        {
                            $this->cart[] = $cart_new[$k];
                            $push_twice++;
                        }
                    }
                }
                
                //熱銷商品&&猜你喜歡 共同
                $same = $dba->query("select goods from goods_history where `delete`=0 and member=".$_SESSION["info"]["fi_no"]." and time_history not like '".date("Y-m-d")."%' order by fi_no desc limit 0,20 ");
                $has_history = false;
                if($same[0])$has_history = true;
                $same_goods = array();
                foreach($same as $k => $v)
                {
                    $same_goods[] = $v["goods"];
                }
                $same_goods = array_unique($same_goods);
                $same = implode(",",$same_goods);
                $same = $dba->query("select category from goods_index where fi_no in (".$same.")");
                if(!$same[0])$same=array();
                $same_category = array();
                foreach($same as $k => $v)
                {
                    $same_category[] = $v["category"];
                }
                $same_category = array_unique($same_category);
                $same = implode(",",$same_category);
                
                //熱銷商品
                if($has_history)
                {
                    $this->hot = $dba->query("select `index` from category where fi_no in(".$same.")"); 
                    $hot_index = array();
                    if(!empty($this->hot))
                    foreach($this->hot as $k => $v)
                    {
                        $hot_index[] = $v["index"];
                    }
                    $hot_index = array_unique($hot_index);
                    $hot_index = implode(",",$hot_index);
                    $this->hot = $dba->query("select fi_no from category where `index` in(".$hot_index.")");
                    if($this->hot=="")$this->hot=array();
                    $hot_fi_no = array();
                    foreach($this->hot as $k => $v)
                    {
                        $hot_fi_no[] = $v["fi_no"];
                    }
                    $hot_fi_no = array_unique($hot_fi_no);
                    $hot_fi_no = implode(",",$hot_fi_no);
                    $this->hot = $dba->query("select fi_no,images,name from goods_index where category in (".$hot_fi_no.") order by transaction_times desc limit 0,16");
                }
                else
                {
                    $this->hot = $dba->query("select fi_no,images,name from goods_index order by transaction_times desc limit 0,16");
                }
                    
                //猜你喜歡
                if($has_history)
                {
                    $this->guess = $dba->query("select fi_no,images,name from goods_index where category in (".$same.") and discount!=0 order by added_date desc limit 0,16 ");
                }
                else
                {
                    $this->guess = $dba->query("select fi_no,images,name from goods_index where discount!=0 order by added_date desc limit 0,16 ");
                }
        }
        
        public function ajax_change_sticker(){
                if($_FILES["file"]["error"]>0)
                {
                    $this->error = "上傳失敗!";
                    return;
                }
                else
                {
                    if($_FILES["file"]["size"]>(1024*1024)/2)
                    {
                        $this->error = "單檔大小不能超過 512 KB";
                        return;
                    }
                    $this->info = getimagesize($_FILES['file']['tmp_name']);
                    $w = $this->info[0];
                    $h = $this->info[1];
                    if($w > 100 || $h > 100)
                    {
                        $this->error = "請上傳寬高小於 100 x 100 之圖檔！";
                        return;
                    }
                    
                    if(is_uploaded_file($_FILES['file']['tmp_name']))
                    {
                        $og_fname = $_FILES["file"]["name"];
                        $ext = explode(".", $og_fname);
                        $ext = $ext[count($ext)-1];
                        $new_fname = $_SESSION["info"]["fi_no"].".".$ext;
                        $path = "public/img/member/";
                        if(!move_uploaded_file($_FILES['file']['tmp_name'], $path.$new_fname))
                        {
                            $this->type = "1";
                            $this->error = "圖片儲存失敗";
                            return;
                        }
                        $this->type = "0";
                        $this->error = "圖片上傳成功！";
                        require_once "swop/library/dba.php";
                        $dba=new dba(); 
                        $query = "update member_index set picture='".$new_fname."' where fi_no=".$_SESSION["info"]["fi_no"];
                        $dba->query($query);
                        $_SESSION['info']["picture"] = $new_fname;
                    }
                }
        }
        
        public function order($params){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $orderlist = $dba->query("select * from order_form where member=".$_SESSION["info"]["fi_no"]." order by fi_no desc");
        
                $order = array();
                $len = count($orderlist);
                $len2 = count($params);
                
                for($i = 0;$i<$len;$i++)
                {
                    for($j = 0;$j<$len2;$j++)
                    {
                        
                        if(
                            $orderlist[$i]["status_order"]==$params[$j][0] &&
                            $orderlist[$i]["status_pay"]==$params[$j][1] &&
                            $orderlist[$i]["status_transport"]==$params[$j][2] &&
                            $orderlist[$i]["status_receiving"]==$params[$j][3]
                        ){
                            $order[$i]["store"] = $orderlist[$i]["store"];
                            $order[$i]["subtotal"] = $orderlist[$i]["subtotal"];
                            $order[$i]["payments"] = $orderlist[$i]["payments"];
                            $order[$i]["shipping_fee"] = $orderlist[$i]["shipping_fee"];
                            $order[$i]["fi_no"] = $orderlist[$i]["fi_no"];
                            $order[$i]["date"] = str_replace("-",".",substr($orderlist[$i]["date"], 0, 10));
                            $order[$i]["sn"] = $orderlist[$i]["sn"];
                            $order[$i]["remind"] = $orderlist[$i]["remind"];
                            $order[$i]["goods"] = array();
                            $order[$i]["cancel"] = $params[$j][0]==2&&$params[$j][1]==0&&$params[$j][2]==0&&$params[$j][3]==0?true:false;
                        }
                    }
                }
                $order = array_values($order);
                
                if(isset($order[0]))
                {
                    $union = array();
                    foreach($order as $key => $orderData)
                    {
                        $union[] = $orderData["fi_no"];
                    }
                    $union = implode(",", $union);
                    $order_goods = $dba->query("select `order`,goods,name,promotions,discount,number,specifications,application_progress from order_goods where `order` in(".$union.") and member=".$_SESSION["info"]["fi_no"]);

                    foreach($order_goods as $key => $goodsData)
                    {
                        foreach($order as $k => $orderData)
                        {
                            if($orderData["fi_no"]==$goodsData["order"])
                            {
                                $order[$k]["goods"][]=$goodsData;
                            }
                        }
                    }

                    $union = array();
                    foreach($order_goods as $key => $orderData)
                    {
                        $union[] = $orderData["goods"];
                    }
                    $union = array_unique($union);
                    $union = implode(",", $union);
                    $order_goods = $dba->query("select fi_no,images from goods_index where fi_no in(".$union.")");

                    foreach($order as $k => $orderData)
                    {
                        foreach($orderData["goods"] as $k2 => $goods)
                        {
                            foreach($order_goods as $key => $goodsData)
                            {
                                if($goodsData["fi_no"] == $goods["goods"])
                                {
                                    $order[$k]["goods"][$k2]["images"] = $goodsData["images"];
                                }
                            }
                        }
                    }

                    $union = array();
                    foreach($order as $key => $orderData)
                    {
                        $union[] = $orderData["store"];
                    }
                    $union = implode(",", $union);
                    $order_store = $dba->query("select fi_no, name from store where fi_no in(".$union.")");

                    foreach($order_store as $key => $storeData)
                    {
                        foreach($order as $k => $orderData)
                        {
                            if($orderData["store"]==$storeData["fi_no"])
                            {
                                $order[$k]["name"]=$storeData["name"];
                            }
                        }
                    }

                    return $order;  
                }
                return array();
        }
        
        public function ajax_order_remind(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $dba->query("update order_index set remind=1 where fi_no=".$_POST["order"]);
                $dba->query("update order_form set remind=1 where fi_no=".$_POST["order"]." and member=".$_SESSION["info"]["fi_no"]);
        }
        
        public function ajax_order_bonus(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $this->sum_currency = $dba->query("select sum(increase)-sum(reduce) as currency from member_currency where type=1 and member=".$_SESSION["info"]["fi_no"]);
                $this->sum_currency = empty($this->sum_currency[0]["currency"])?0:$this->sum_currency[0]["currency"];
        }
        
        public function ajax_order_cancel(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $dba->query("update order_index set status_order=2,status_pay=0,status_transport=0,status_receiving=0,application_returns=0,application_exchanges=0,application_rework=0 where fi_no in (".$_POST["order"].")");
                $dba->query("update order_form set status_order=2,status_pay=0,status_transport=0,status_receiving=0,application_returns=0,application_exchanges=0,application_rework=0 where fi_no in (".$_POST["order"].") and member=".$_SESSION["info"]["fi_no"]);
                
                require_once "swop/library/erpconnect.php";
                $fuer = new Library_ERP();
                $query = "select `sn`,`trace`,`checkout` from order_index where fi_no=".$_POST["order"];
                $order = $dba->query($query);
                $fuer->updateorder($order[0]["sn"], 2, 0, 0, 0, $order[0]["trace"], $order[0]["checkout"]);
        }
        
        public function ajax_order_receiving_complete(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $dba->query("update order_index set status_order=1,status_pay=2,status_transport=2,status_receiving=2 where fi_no=".$_POST["order"]);
                $dba->query("update order_form set status_order=1,status_pay=2,status_transport=2,status_receiving=2 where fi_no=".$_POST["order"]." and member=".$_SESSION["info"]["fi_no"]);
                
                require_once "swop/library/erpconnect.php";
                $fuer = new Library_ERP();
                $query = "select `sn`,`trace`,`checkout` from order_index where fi_no=".$_POST["order"];
                $order = $dba->query($query);
                $fuer->updateorder($order[0]["sn"], 1, 2, 2, 2, $order[0]["trace"], $order[0]["checkout"]);
        }
        
        public function ajax_order_transaction_complete(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $dba->query("update order_index set status_order=3,status_pay=2,status_transport=2,status_receiving=2 where fi_no=".$_POST["order"]);
                $dba->query("update order_form set status_order=3,status_pay=2,status_transport=2,status_receiving=2 where fi_no=".$_POST["order"]." and member=".$_SESSION["info"]["fi_no"]);
                
                require_once "swop/library/erpconnect.php";
                $fuer = new Library_ERP();
                $query = "select `sn`,`trace`,`checkout` from order_index where fi_no=".$_POST["order"];
                $order = $dba->query($query);
                $fuer->updateorder($order[0]["sn"], 3, 2, 2, 2, $order[0]["trace"], $order[0]["checkout"]);
        }
        
        public function ajax_order_logistics(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $this->order_trace = $dba->query("select trace from order_index where fi_no=".$_POST["order"]);
        }
        
        public function ajax_order_rank(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $this->order_goods = $dba->query("select goods,name,evaluate,evaluate_context,evaluate_date,evaluate_added,evaluate_adcontext,evaluate_addate from order_goods where `order`=".$_POST["order"]." and member=".$_SESSION["info"]["fi_no"]);
                $rank_date = $dba->query("select date_receiving from order_form where fi_no=".$_POST["order"]." and member=".$_SESSION["info"]["fi_no"]);
                $this->date_diff = (strtotime(date("Y-m-d h:i:s")) - strtotime($rank_date[0]["date_receiving"]))/(60*60*24);
                $this->pre_rank_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")."-3 day"));//沒有初次評價直接給追加評價時,給予初次評價的時間
        }
        
        public function ajax_order_rank_update(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $_POST["goods_evaluate"] = str_replace('\\&quot;', '"', $_POST["goods_evaluate"]);
                $_POST["goods_evaluate"] = json_decode($_POST["goods_evaluate"],true);
                $order = $_POST["order"];
                $member = $_SESSION["info"]["fi_no"];
                $evaluate_date = date("Y-m-d H:i:s");
                for($i = 0, $len = count($_POST["goods_evaluate"]);$i < $len;$i++ )
                {
                    $goods = $_POST["goods_evaluate"][$i][0];
                    $score = $_POST["goods_evaluate"][$i][1];
                    $content = empty($_POST["goods_evaluate"][$i][2])?"好評！":$_POST["goods_evaluate"][$i][2];
                    $dba->query("insert into goods_evaluate (`goods`,`order`,`content`,`member`,`score`,`evaluate_date`)values('".$goods."','".$order."','".$content."','".$member."','".$score."','".$evaluate_date."')");
                    
                    $evaluate = $dba->query("select evaluation_score,evaluation_number from goods_index where fi_no=".$goods);
                    $new_evaluation_score = $evaluate[0]["evaluation_score"]+$score;
                    $new_evaluation_number = $evaluate[0]["evaluation_number"]+1;
                    
                    $dba->query("update goods_index set evaluation_score=".$new_evaluation_score.",evaluation_number=".$new_evaluation_number."  where fi_no=".$goods);
                    $dba->query("update goods_info set evaluation_score=".$new_evaluation_score.",evaluation_number=".$new_evaluation_number."  where fi_no=".$goods);
                    
                    $dba->query("update order_goods set evaluate=".$score.",evaluate_context='".$content."',evaluate_date='".$evaluate_date."' where `order`=".$order." and goods=".$goods." and member=".$member);
                }
                
        }
        
        public function ajax_order_rank_update_add(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $_POST["goods_evaluate"] = str_replace('\\&quot;', '"', $_POST["goods_evaluate"]);
                $_POST["goods_evaluate"] = json_decode($_POST["goods_evaluate"],true);
                $order = $_POST["order"];
                $member = $_SESSION["info"]["fi_no"];
                $evaluate_addate = date("Y-m-d H:i:s");
                $evaluate_date = date("Y-m-d H:i:s", strtotime($evaluate_addate."-3 day"));
                $pre_ranked = $_POST["pre_rank"];
                
                for($i = 0, $len = count($_POST["goods_evaluate"]);$i < $len;$i++ )
                {
                    $goods = $_POST["goods_evaluate"][$i][0];
                    $score = $_POST["goods_evaluate"][$i][1];
                    $add_content = empty($_POST["goods_evaluate"][$i][2])?"好評！":$_POST["goods_evaluate"][$i][2];
                    if($pre_ranked == 0)
                    {
                        $dba->query("insert into goods_evaluate (`goods`,`order`,`content`,`member`,`score`,`evaluate_date`,`added_content`,`added_date`)values('".$goods."','".$order."','好評！','".$member."',$score,'".$evaluate_date."','".$add_content."','".$evaluate_addate."')");
                    
                        $evaluate = $dba->query("select evaluation_score,evaluation_number from goods_index where fi_no=".$goods);
                        $new_evaluation_score = $evaluate[0]["evaluation_score"]+$score;
                        $new_evaluation_number = $evaluate[0]["evaluation_number"]+1;

                        $dba->query("update goods_index set evaluation_score=".$new_evaluation_score.",evaluation_number=".$new_evaluation_number."  where fi_no=".$goods);
                        $dba->query("update goods_info set evaluation_score=".$new_evaluation_score.",evaluation_number=".$new_evaluation_number."  where fi_no=".$goods);

                        $dba->query("update order_goods set evaluate=".$score.",evaluate_context='好評！',evaluate_date='".$evaluate_date."',evaluate_added=1,evaluate_adcontext='".$add_content."',evaluate_addate='".$evaluate_addate."' where `order`=".$order." and goods=".$goods." and member=".$member);
                    }
                    else
                    {
                        $dba->query("update goods_evaluate set `added_content`='".$add_content."',`added_date`='".$evaluate_addate."' where goods=".$goods." and `order`=".$order." and member=".$member);
                        $dba->query("update order_goods set evaluate_added=1,evaluate_adcontext='".$add_content."',evaluate_addate='".$evaluate_addate."' where `order`=".$order." and goods=".$goods." and member=".$member);
                    }
                }
        }
        
        public function appeal(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $this->rows = $dba->query("select * from appeal_index where member=".$_SESSION["info"]["fi_no"]);
                $stores = array();
                foreach($this->rows as $k => $v)
                {
                    $stores[]=$v["store"];
                }
                $stores = array_unique($stores);
                $stores = implode(",", $stores);
                $stores = $dba->query("select fi_no, name from store where fi_no in(".$stores.")");
                $len = count($stores);
                foreach($this->rows as $k => $v)
                {
                    for($i=0;$i<$len;$i++)
                    {
                        if($v["store"] == $stores[$i]["fi_no"])
                        {
                            $this->rows[$k]["store_name"] = $stores[$i]["name"];
                            break;
                        }
                    }
                }
                
        }
        
        public function ajax_appeal(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $dba->query("insert into appeal_index (sn,member,store,`order`,content,`date`,progress) values ('".$_POST["sn"]."',".$_POST["member"].",".$_POST["store"].",".$_POST["order"].",'".$_POST["appeal_content"]."',NOW(),1)");
                $new_id = $dba->get_insert_id();
                $dba->query("insert into appeal_info (fi_no,sn,member,store,`order`,content,`date`,progress) values (".$new_id.",'".$_POST["sn"]."',".$_POST["member"].",".$_POST["store"].",".$_POST["order"].",'".$_POST["appeal_content"]."',NOW(),1)");
        }
        
        public function order_returns(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $this->progress = $dba->query("select * from order_application where member=".$_POST["member"]." and `order`=".$_POST["order"]." and goods=".$_POST["goods"]);
        }
        
        public function ajax_order_returns(){
                if($_FILES["file"]["error"]>0)
                {
                    $this->error = "上傳失敗!";
                    return;
                }
                else
                {
                    if($_FILES["file"]["size"]>(1024*1024)/2)
                    {
                        $this->error = "單檔大小不能超過 512 MB";
                        return;
                    }
                    $this->info = getimagesize($_FILES['file']['tmp_name']);
                    $w = $this->info[0];
                    $h = $this->info[1];
                    if($w > 1920 || $h > 1080)
                    {
                        $this->error = "請上傳寬高小於 1920 x 1080 之圖檔！";
                        return;
                    }
                    
                    if(is_uploaded_file($_FILES['file']['tmp_name']))
                    {
                        $_POST["filename"] = preg_replace('/∴/', '_', $_POST["filename"]);
                        $_POST["filename"] = str_replace("#", date("ymdHis",time()), $_POST["filename"]);
                        $path = "public/img/application/";
                        $old_path = $path.$_POST["filename"];
                        if(!move_uploaded_file($_FILES['file']['tmp_name'], $path.$_POST["filename"]))
                        {
                            $this->error = "圖片儲存失敗";
                            return;
                        }
                        $this->error = "退貨退款申請中！";
                        require_once "swop/library/dba.php";
                        $dba=new dba();
                        $query = "insert into order_application (`member`,`date`,`order`,`goods`,`status`,`progress`,`reason`,`explanation`,`images`)values(".$_POST["member"].",NOW(),".$_POST["order"].",".$_POST["goods"].",1,1,'".$_POST["reason"]."','".$_POST["explanation"]."','".$_POST["filename"]."')";
                        $dba->query($query);
                        $insert_id = $dba->get_insert_id();
                        $_POST["filename"] = str_replace("@", $insert_id, $_POST["filename"]);
                        $query = "update order_application set images='".$_POST["filename"]."' where member=".$_POST["member"]." and fi_no=".$insert_id;
                        $dba->query($query);
                        $new_path = $path.$_POST["filename"];
                        rename($old_path, $new_path);
                        $query = "update order_index set application_returns=1 where fi_no=".$_POST["order"]." and member=".$_POST["member"];
                        $dba->query($query);
                        $query = "update order_form set application_returns=1 where fi_no=".$_POST["order"]." and member=".$_POST["member"];
                        $dba->query($query);
                        $query = "update order_goods set application=".$insert_id.",application_progress=1 where `order`=".$_POST["order"]." and member=".$_POST["member"]." and goods=".$_POST["goods"];
                        $dba->query($query);
                    }
                }
        }
	
	public function ajax_login() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->rows = $dba->query("select * from member_index where id='".$_POST['account']."' && password='".strrev(md5($_POST['password']))."' && verification=1 ");

		return $this;
	}
	
	public function ajax_register() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		
                $now_time = date ("Y-m-d H:i:s");
                $this->vali_code = md5($_POST['email'].$now_time);
                $this->mail = $_POST['email'];
                $this->name = $_POST['id'];
		$rows = $dba->query("select * from member_index where email='".$_POST['email']."' OR phone='".$_POST['phone']."' ");
		
                if(count($rows) == 0) {
			$results = $dba->query("INSERT INTO member_index (`fi_no`, `id`, `email`, `password`, `phone`, `verification_key`, `verification_send`, `date_added`) VALUES (NULL, '".$_POST['id']."', '".$_POST['email']."', '".strrev(md5($_POST['password']))."', '".$_POST['phone']."','".md5($_POST['email'].$now_time)."','".$now_time."','".$now_time."') ");
			$newid = $dba->get_insert_id();
			
			if($results) {
				$this->results = $dba->query("INSERT INTO member_info (`fi_no`, `id`, `email`, `password`, `phone`, `sex`, `qq`, `birthday`) VALUES ('".$newid."', '".$_POST['id']."', '".$_POST['email']."', '".strrev(md5($_POST['password']))."', '".$_POST['phone']."',".$_POST['sex'].",'".$_POST['qq']."','".$_POST['birthday']."') ");
			}
		}
		else {
			$this->repeat = 1;
		}
		
		return $this;
	}
        
        public function ajax_member_register() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		
                if(isset($_SESSION["info"]["fi_no"]))
                {
                    if($_POST['password'] != "")
                    {
                        $pw = "password='".strrev(md5($_POST['password']))."'";
                        $this->pw_update = true;
                    }
                    else
                    {
                        $pw = "";
                        $this->pw_update = false;
                    }
                    $dba->query("update member_index set ".$pw." where fi_no=".$_SESSION["info"]["fi_no"]);
                    $dba->query("update member_info set ".($pw==""?"":$pw.",")."sex=".$_POST['sex'].",qq='".$_POST['qq']."',birthday='".$_POST['birthday']."'  where fi_no=".$_SESSION["info"]["fi_no"]);
                }
		
		return $this;
	}
        
        public function ajax_register2(){
                require_once "swop/library/dba.php";
                $dba=new dba();
                $this->verification = $dba->query("select fi_no,verification,verification_key,verification_num from member_index where email='".$_GET["email"]."'");
        }
	
        public function ajax_get_register() {
                require_once "swop/library/dba.php";
                $dba=new dba();
                $this->member_data = $dba->query("select * from member_info where fi_no='".$_SESSION["info"]["fi_no"]."'");
        }
        
        public function bonus(){
                require_once "swop/library/dba.php";
                $dba=new dba();
                $this->mem_info = $dba->query("select evaluation_score, evaluation_number from member_info where fi_no=".$_SESSION["info"]["fi_no"]);
                if(!isset($_GET['page']))$_GET['page']=1;
                $limit = " LIMIT ".(($_GET['page']-1)*5).",5 ";
                $this->rows = $dba->query("select * from member_currency where member=".$_SESSION["info"]["fi_no"]." order by date desc ".$limit);
                
                $stores = array();
                foreach($this->rows as $k => $v)
                {
                    $stores[]=$v["store"];
                }
                $stores = array_unique($stores);
                $stores = implode(",", $stores);
                $stores = $dba->query("select fi_no, name from store where fi_no in(".$stores.")");
                $len = count($stores);
                foreach($this->rows as $k => $v)
                {
                    for($i=0;$i<$len;$i++)
                    {
                        if($v["store"] == $stores[$i]["fi_no"])
                        {
                            $this->rows[$k]["source"] = "賣家：".$stores[$i]["name"];
                            break;
                        }
                    }
                }
                
                $this->num = $dba->query("select count(fi_no) as total from member_currency where member=".$_SESSION["info"]["fi_no"]);
                $this->num = $this->num[0]["total"];
                
                $this->sum_bonus = $dba->query("select sum(increase)-sum(reduce) as bonus from member_currency where type=0 and member=".$_SESSION["info"]["fi_no"]);
                $this->sum_bonus = empty($this->sum_bonus[0]["bonus"])?0:$this->sum_bonus[0]["bonus"];
                $this->sum_currency = $dba->query("select sum(increase)-sum(reduce) as currency from member_currency where type=1 and member=".$_SESSION["info"]["fi_no"]);
                $this->sum_currency = empty($this->sum_currency[0]["currency"])?0:$this->sum_currency[0]["currency"];
        }
        
        public function ajax_bonus(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                
                $dba->query("insert into member_currency (member,type,store,source,sn,increase,reduce,`date`,remark) values (".$_SESSION["info"]["fi_no"].",0,0,'兌換跨寶通幣','',0,".$_POST["currency"].",NOW(),'兌換扣除')");
                $dba->query("insert into member_currency (member,type,store,source,sn,increase,reduce,`date`,remark) values (".$_SESSION["info"]["fi_no"].",1,0,'由積分兌換獲得','',".($_POST["currency"]/100).",0,NOW(),'兌換獲得')");
        }
	
	public function address() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->rows = $dba->query("select * from member_address where member='".$_SESSION['info']['fi_no']."' order by fi_no asc");
		
		return $this;
	}

	public function ajax_address() {
                require_once "swop/library/dba.php";
		$dba=new dba();
                
                if($_POST["preset"] == 1)
                {
                    $cur_preset = $dba->query("select fi_no from member_address where member=".$_SESSION['info']['fi_no']." and preset=1");
                    if($cur_preset[0])
                    {
                        $cur_preset = $cur_preset[0]["fi_no"];
                        $dba->query("update member_address set preset=0 where fi_no=".$cur_preset." and member=".$_SESSION['info']['fi_no']);
                    }
                }
                        
		if($_POST['fi_no'] != "") {
			$this->results = $dba->query("UPDATE member_address SET consignee='".$_POST['consignee']."', postal_code='".$_POST['postal_code']."', province='".$_POST['province']."', city='".$_POST['city']."', district='".$_POST['district']."', street='".$_POST['street']."', address='".$_POST['address']."', contact_phone='".$_POST['contact_phone']."', contact_mobile='".$_POST['contact_mobile']."',preset=".$_POST["preset"]." WHERE fi_no = '".$_POST['fi_no']."' && member = '".$_SESSION['info']['fi_no']."' ");
			$this->num = $dba->get_affected_rows();			
		}
		else {
			$this->results = $dba->query("INSERT INTO member_address (fi_no, member, consignee, postal_code, province, city, district, street, address, contact_phone, contact_mobile, preset) VALUES (NULL, '".$_SESSION['info']['fi_no']."', '".$_POST['consignee']."', '".$_POST['postal_code']."', '".$_POST['province']."', '".$_POST['city']."', '".$_POST['district']."', '".$_POST['street']."', '".$_POST['address']."', '".$_POST['contact_phone']."', '".$_POST['contact_mobile']."',".$_POST['preset'].") ");
			$this->newid = $dba->get_insert_id();
		}
		
		return $this;
	}
	
	public function ajax_address_delete() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->results = $dba->query("DELETE FROM member_address WHERE fi_no = '".$_POST['fi_no']."' && member = '".$_SESSION['info']['fi_no']."' ");

	}
	
	public function ajax_address_usual() {
		require_once "swop/library/dba.php";
		$dba=new dba();
                $cur_preset = $dba->query("select fi_no from member_address where member=".$_SESSION['info']['fi_no']." and preset=1");
                if($cur_preset[0])
                {
                    $cur_preset = $cur_preset[0]["fi_no"];
                    $dba->query("update member_address set preset=0 where fi_no=".$cur_preset." and member=".$_SESSION['info']['fi_no']);
                }
		$this->results = $dba->query("update member_address set preset=1 WHERE fi_no = '".$_POST['fi_no']."' && member = '".$_SESSION['info']['fi_no']."' ");
	}
	
        public function collect(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                
                $by="";
                switch($_GET["by"])
                {
                    case "asc":
                        $by = "asc";
                        break;
                    case "desc":
                        $by = "desc";
                        break;
                    default:
                        $by = "desc";
                }
                if(!isset($_GET['page']))$_GET['page']=1;
                $limit = " LIMIT ".(($_GET['page']-1)*20).",20 ";
                
                if(isset($_GET["ckeyword"]))
                {
                    $keyword = " and name like '%".$_GET["ckeyword"]."%' ";
                    if($_GET["ckeyword"]=="")
                    {
                        $keyword = "";
                    }
                }
                else
                {
                    $keyword = "";
                }
                
                //goods
                $fi_no = $dba->query("select fi_no,goods from collect_goods where member=".$_SESSION['info']['fi_no']." and `delete`=0 order by added_date ".$by);
                $collect_id = array();
                $goods_fi_no = array();
                foreach($fi_no as $k => $v)
                {
                    $collect_id[]=$v["fi_no"];
                    $goods_fi_no[]=$v["goods"];
                }
                $goods_fi_no = implode(",",$goods_fi_no);
                if($goods_fi_no=="")
                {
                    $this->goods_num = 0;
                    $this->collect_goods = array();
                }
                else if($_GET["order"]=="date")
                {
                    $this->goods_num = $dba->query("select count(fi_no) as total from view_goods_index where fi_no in(".$goods_fi_no.")".$keyword);
                    $this->goods_num = $this->goods_num[0]["total"];
                    $this->collect_goods = $dba->query("select fi_no, name, images, promotions from view_goods_index where fi_no in(".$goods_fi_no.")".$keyword." order by field(fi_no,".$goods_fi_no.")".$limit);
                    $l = (($_GET['page']-1)*20)+count($this->collect_goods);
                    for($i = (($_GET['page']-1)*20); $i < $l; $i++)
                    {
                        $this->collect_goods[$i]["collect_id"] = $collect_id[$i];
                    }
                }
                else if($_GET["order"]=="price")
                {
                    $this->goods_num = $dba->query("select count(fi_no) as total from view_goods_index where fi_no in(".$goods_fi_no.")".$keyword);
                    $this->goods_num = $this->goods_num[0]["total"];
                    $this->collect_goods = $dba->query("select fi_no, name, images, promotions from view_goods_index where fi_no in(".$goods_fi_no.")".$keyword." order by promotions ".$by.$limit);
                    $l = (($_GET['page']-1)*20)+count($this->collect_goods);
                    for($i = ($_GET['page']-1)*20; $i < $l; $i++)
                    {
                        $this->collect_goods[$i]["collect_id"] = $collect_id[$i];
                    }
                }
 
                //store
                $fi_no = $dba->query("select fi_no,store from collect_store where member=".$_SESSION['info']['fi_no']." and `delete`=0 order by added_date ".$by);
                $collect_id = array();
                $store_fi_no = array();
                foreach($fi_no as $k => $v)
                {
                    $collect_id[]=$v["fi_no"];
                    $store_fi_no[]=$v["store"];
                }
                $store_fi_no = implode(",",$store_fi_no);
                if($store_fi_no=="")
                {
                    $this->store_num = 0;
                    $this->collect_store = array();
                }
                else
                {
                    $this->store_num = $dba->query("select count(fi_no) as total from store where fi_no in(".$store_fi_no.")".$keyword);
                    $this->store_num = $this->store_num[0]["total"];
                    $this->collect_store = $dba->query("select fi_no, name, images from store where fi_no in(".$store_fi_no.")".$keyword." order by field(fi_no,".$store_fi_no.")".$limit);
                    $l = (($_GET['page']-1)*20)+count($this->collect_store);
                    for($i = ($_GET['page']-1)*20; $i < $l; $i++)
                    {
                        $this->collect_store[$i]["collect_id"] = $collect_id[$i];
                    }
                }
                
                if($_GET["type"]=="goods")
                {
                    $this->num = $this->goods_num;
                }else if($_GET["type"]=="store"){
                    $this->num = $this->store_num;
                }
        }
        
        public function ajax_collect_delete(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                switch ($_POST["type"])
                {
                    case "goods":
                        $dba->query("UPDATE collect_goods SET `delete` = 1 WHERE fi_no in (".$_POST["delete"].") and member=".$_SESSION['info']['fi_no']);
                        break;
                    case "store":
                        $dba->query("UPDATE collect_store SET `delete` = 1 WHERE fi_no in (".$_POST["delete"].") and member=".$_SESSION['info']['fi_no']);
                        break;
                }
        }
	
        public function recommand(){
                require_once "swop/library/dba.php";
		$dba=new dba();

                if(!isset($_GET['category']))$_GET['category']=1;
                
                if(!isset($_GET['page']))$_GET['page']=1;
                $limit = " LIMIT ".(($_GET['page']-1)*20).",20 ";
                
                $category = $dba->query("select fi_no,name,icon,`index` from category order by `index` desc, fi_no desc");
                $this->tab = array();
                foreach($category as $v)
                {
                    if($v["index"]==0)
                        $this->tab[]=array(
                            "fi_no"=>$v["fi_no"],
                            "name"=>$v["name"],
                            "icon"=>$v["icon"]
                        );
                }
                $this->tab = array_reverse($this->tab);
                $history = $dba->query("select distinct goods from goods_history where member=".$_SESSION['info']['fi_no']." and `delete` = 0 and time_history not like '".date("Y-m-d")."%' order by time_history desc limit 0,100");
                $history_goods = array();
                foreach($history as $v)
                {
                    $history_goods[] = $v["goods"];
                }
                $history_goods = implode(",", $history_goods);
                $goods_category = $dba->query("select distinct category from goods_index where fi_no in (".$history_goods.")");
                $l = count($goods_category);
                $pick_category = array();
                function find_index0_fi_no($category,$no){
                    foreach($category as $v)
                    {
                        if($v["fi_no"]==$no)
                            return $v["index"]==0?$v["fi_no"]:find_index0_fi_no($category,$v["index"]);
                    }
                }
                for($i = 0;$i<$l;$i++)
                {
                    $no = $goods_category[$i]["category"];
                    if(find_index0_fi_no($category,$no)==$_GET["category"])
                    {
                        $pick_category[] = $no; 
                    }
                }
                $pick_category = implode(",", $pick_category);
                $this->num = $dba->query("select fi_no as total from view_goods_index where category in(".$pick_category.") order by added_date desc limit 0,100");
                $this->num = count($this->num);
                $this->recommand = $dba->query("select fi_no, name, images, promotions from view_goods_index where category in(".$pick_category.") order by added_date desc ".$limit);
        }
        
	public function historylog(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                if(!isset($_GET['page']))$_GET['page']=1;
                $limit = " LIMIT ".(($_GET['page']-1)*20).",20 ";

                if(isset($_GET["ckeyword"]))
                {
                    $keyword = " and name like '%".$_GET["ckeyword"]."%' ";
                    if($_GET["ckeyword"]=="")
                    {
                        $keyword = "";
                    }
                }
                else
                {
                    $keyword = "";
                }
                
                $fi_no = $dba->query("select fi_no, goods from goods_history where member=".$_SESSION['info']['fi_no']." and `delete`=0 order by fi_no desc");
                $goods_fi_no = array();
                $goods_history = array();
                foreach($fi_no as $k => $v)
                {
                    $goods_fi_no[]=$v["goods"];
                    $goods_history[]=$v["fi_no"];
                }
                $goods_fi_no = implode(",",$goods_fi_no);
                if($goods_fi_no=="")
                {
                    $this->num = 0;
                    $this->history_goods = array();
                }
                else
                {
                    $this->num = $dba->query("select count(fi_no) as total from view_goods_index where fi_no in(".$goods_fi_no.")".$keyword);
                    $this->num = $this->num[0]["total"];
                    $this->history_goods = $dba->query("select fi_no, name, images, promotions from view_goods_index where fi_no in(".$goods_fi_no.")".$keyword." order by field(fi_no,".$goods_fi_no.")".$limit);
                    $l = count($this->history_goods);
                    for($i = 0; $i < $l; $i++)
                    {
                        $this->history_goods[$i]["historylog_id"] = $goods_history[$i];
                    }
                }
                
                //猜你喜歡
                $goods_index = $dba->query("select `fi_no`, `category`, `images`, `name`, `price`, `promotions`, `discount`, `free_gifts`, `free_shipping`, `transaction_times`, `evaluation`, `evaluation_number`, `store_name` from view_goods_index WHERE fi_no in(".$goods_fi_no.")".$keyword." order by field(fi_no,".$goods_fi_no.")".$limit);
                $search_category = array();
		for($i=0; $i<count($goods_index); $i++) {
			$search_category[] = $goods_index[$i]['category'];
		}
		
		$category = $dba->query("SELECT * FROM category");
		
		$all_index = array();
		for($i=0; $i<count($category); $i++) {
			for($j=0; $j<count($search_category); $j++) {
				if($category[$i]['fi_no'] == $search_category[$j]) {
					$all_index[] = $category[$i]['index'];
				}
			}
		}
		
		$all_category = array();
		for($i=0; $i<count($category); $i++) {
			for($j=0; $j<count($all_index); $j++) {
				if($category[$i]['index'] == $all_index[$j]) {
					$all_category[] = $category[$i]['fi_no'];
				}
			}
		}
				
		$this->love = $dba->query("SELECT `fi_no`, `name`, `images`, `discount`, `promotions` FROM goods_index WHERE `category` IN (".implode(",", $all_category).") && `status_audit`='1' && `status_shelves`='1' order by `transaction_times` desc limit 0,18");
        }
        
        public function ajax_historylog_delete(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $dba->query("UPDATE goods_history SET `delete` = 1 WHERE fi_no in (".$_POST["delete"].") and member=".$_SESSION['info']['fi_no']);
        }
        
        public function ajax_product_spec(){
                require_once "swop/library/dba.php";
		$dba=new dba();
                $this->spec = $dba->query("select fi_no,specifications,inventory from goods_index where fi_no=".$_POST["fi_no"]);
                $this->spec = $this->spec[0];
        }
	
	public function ajax_forget_email() {
		$now_time = date ("Y-m-d H:i:s");
		
		require_once "swop/library/dba.php";
		$dba=new dba();
                $veri_data = $dba->query("select verification_num,verification_send from member_index where email='".$_POST['email']."'");
                $this->times = $veri_data[0]["verification_num"];
                $this->send = substr($veri_data[0]["verification_send"], 0,10);
                
                if($this->times>=5)
                {
                    $this->num = 0;
                    return;
                }
                
                if($this->send != date("Y-m-d"))
                {
                    $this->results = $dba->query("UPDATE member_index SET verification_num=1,verification_key = '".md5($_POST['email'].$now_time)."', verification_send = '".$now_time."' WHERE email = '".$_POST['email']."' ");
                }
                else
                {
                    $this->results = $dba->query("UPDATE member_index SET verification_num=".($this->times+1).",verification_key = '".md5($_POST['email'].$now_time)."', verification_send = '".$now_time."' WHERE email = '".$_POST['email']."' ");
                }
                $this->vali_code = md5($_POST['email'].$now_time);
                $this->mail = $_POST['email'];
		$this->num = $dba->get_affected_rows();
	}
	
	public function ajax_forget_phone() {
		$now_time = date ("Y-m-d H:i:s");
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->results = $dba->query("UPDATE member_index SET verification_key = '".substr(md5($_POST['phone'].$now_time),0 , 6)."', verification_send = '".$now_time."' WHERE phone = '".$_POST['phone']."' ");
		$this->num = $dba->get_affected_rows();
		
	}
	
	public function ajax_verification_email() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->rows = $dba->query("select * from member_index WHERE email = '".$_POST['email']."' && verification_key = '".$_POST['verification_key']."' ");
		$this->num = count($this->rows);
		
		
		return $this;
	}
	
	public function ajax_verification_phone() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->rows = $dba->query("select * from member_index WHERE phone = '".$_POST['phone']."' && verification_key = '".$_POST['verification_key']."' ");
		$this->num = count($this->rows);
		
		
		return $this;
	}
	
	public function ajax_restart() {
		$now_time = date ("Y-m-d H:i:s");
		
		require_once "swop/library/dba.php";
		$dba=new dba();
		$this->results = $dba->query("UPDATE member_index SET password = '".strrev(md5($_POST['password']))."',verification_key = NULL, verification_reset = '".$now_time."' WHERE email = '".$_GET['email']."' && verification_key = '".$_GET['key']."' ");
		$this->num = $dba->get_affected_rows();
		
	}
        
        
        public function ajax_postal_province() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->postal_province = $dba->query("SELECT * FROM postal_province WHERE `index` = '".$_POST['fi_no']."' ORDER BY `index` ASC, `fi_no` ASC ");
		
		return $this;
	}
        
        public function ajax_postal_province_group() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->postal_province = array();
                array_push($this->postal_province, $dba->query("SELECT * FROM postal_province WHERE `index` = '".$_POST['fi_no_1']."' ORDER BY `index` ASC, `fi_no` ASC "));
                array_push($this->postal_province, $dba->query("SELECT * FROM postal_province WHERE `index` = '".$_POST['fi_no_2']."' ORDER BY `index` ASC, `fi_no` ASC "));
                array_push($this->postal_province, $dba->query("SELECT * FROM postal_province WHERE `index` = '".$_POST['fi_no_3']."' ORDER BY `index` ASC, `fi_no` ASC "));
                array_push($this->postal_province, $dba->query("SELECT * FROM postal_street WHERE `index` = '".$_POST['fi_no_4']."' ORDER BY `index` ASC, `fi_no` ASC "));
		
		return $this;
	}
        
        public function ajax_postal_street() {
		require_once "swop/library/dba.php";
		$dba = new dba();
		$this->postal_street = $dba->query("SELECT * FROM postal_street WHERE `index` = '".$_POST['fi_no']."' ORDER BY `index` ASC, `fi_no` ASC ");
		
		return $this;
	}
}
?>
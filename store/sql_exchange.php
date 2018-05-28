<?php
session_start();
if(empty($_SESSION['backend']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$login_fi_no = $_SESSION['backend']['login_fi_no'];
$login_store = $_SESSION['backend']['login_store'];
$login_username = $_SESSION['backend']['login_username'];

function tina_encode($arr){
    if( is_array($arr) ){
        $b_key5str = false;

        if((bool)count(array_filter(array_keys($arr), 'is_string')))
                $b_key5str = true;

        foreach( $arr as $key => $val ){

                if( $b_key5str){
                        $json .= '"'.$key.'":';
                }

                if( is_array($val) )
                        $json .= tina_encode($val).",";
                else if(is_string($val))
                        $json .= '"'.$val.'",';
                else if(is_numeric($val))
                        $json .= $val.',';

        }
        if($b_key5str)
                return "{".substr($json,0,-1)."}";
        else
                return "[".substr($json,0,-1)."]";
    }else{
        throw new exception("It's not an array!");
    }
}

switch($_POST["query_type"])
{
    case "save_application_status":
        $query = "update order_application set progress=".$_POST["progress"]." where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $data = $dba->query($query);
        $query = "select progress from order_application where `order`=".$_POST["order"]." and member=".$_POST["member"]." and status=2";//status=2
        $data = $dba->query($query);
        $all_progerss_2or3 = true;
        foreach($data as $v)
        {
            if($v["progress"] == 1)
            {
                $all_progerss_2or3 = false;
                break;
            }
        }
        if($all_progerss_2or3)
        {
            $query = "update order_index set application_exchanges=2 where fi_no=".$_POST["order"]." and member=".$_POST["member"];//application_exchange
            $dba->query($query);
            $query = "update order_form set application_exchanges=2 where fi_no=".$_POST["order"]." and member=".$_POST["member"];
            $dba->query($query);
        }
        else
        {
            $query = "update order_index set application_exchanges=1 where fi_no=".$_POST["order"]." and member=".$_POST["member"];
            $dba->query($query);
            $query = "update order_form set application_exchanges=1 where fi_no=".$_POST["order"]." and member=".$_POST["member"];
            $dba->query($query);
        }
        break;
    case "get_order_apply":
        $query = "select * from order_application where `order`=".$_POST["fi_no"]." and member=".$_POST["member"]." and status=2";
        $data = $dba->query($query);
        $len = count($data);
        $goodsName = array();
        for($i=0;$i<$len;$i++)
        {
            $goodsName[] = $data[$i]["goods"];
        }
        $goodsName = implode(",",$goodsName);
        $query = "select fi_no, name from goods_index where fi_no in(".$goodsName.")";
        $goods_data = $dba->query($query);
        for($i=0;$i<$len;$i++)
        {
            foreach($goods_data as $v)
            {
                if($data[$i]["goods"] == $v["fi_no"])
                {
                    $data[$i]["goods"] = $v["name"];
                    break;
                }
            }
        }
        for($i=0;$i<$len;$i++)
        {
            
            $return = "";
            $return .=  $data[$i]["goods"]."`".
                        $data[$i]["progress"]."`".
                        $data[$i]["reason"]."`".
                        $data[$i]["explanation"]."`".
                        $data[$i]["images"]."`".
                        $data[$i]["fi_no"];
            $data[$i] = urldecode($return);
        }
        echo implode("@", $data);
        break;
    case "get_order_form":
        $query = "select * from order_form where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $data = $dba->query($query);
        $data = $data[0];
        echo    $data["fi_no"]."`".
                $data["member"]."`".
                $data["sn"]."`".
                $data["date"]."`".
                $data["store"]."`".
                $data["subtotal"]."`".
                $data["shipping_fee"]."`".
                $data["discounts"]."`".
                $data["payments"]."`".
                $data["checkout"]."`".
                $data["exchange_rate"]."`".
                $data["status_order"]."`".
                $data["status_pay"]."`".
                $data["status_transport"]."`".
                $data["status_receiving"]."`".
                $data["application_returns"]."`".
                $data["application_exchanges"]."`".
                $data["application_rework"]."`".
                $data["trace"]."`".
                $data["remind"]."`".
                $data["invoice"]."`".
                $data["remarks"]."`".
                $data["consignee"]."`".
                $data["postal_code"]."`".
                $data["province"]."`".
                $data["city"]."`".
                $data["district"]."`".
                $data["street"]."`".
                $data["address"]."`".
                $data["contact_phone"]."`".
                $data["contact_mobile"]."`".
                $data["identity"]."`".
                $data["transport"]."`".
                $data["message"];
        break;
    case "get_order_goods":
        $query = "select * from order_goods where `order`=".$_POST["fi_no"]." and store=".$login_store." and member=".$_POST["member"];
        $data = $dba->query($query);
        $len = count($data);
        for($i=0;$i<$len;$i++)
        {
            $return = "";
            $return .=  $data[$i]["fi_no"]."`".
                        $data[$i]["member"]."`".
                        $data[$i]["order"]."`".
                        $data[$i]["goods"]."`".
                        $data[$i]["name"]."`".
                        $data[$i]["price"]."`".
                        $data[$i]["promotions"]."`".
                        $data[$i]["discount"]."`".
                        $data[$i]["number"]."`".
                        $data[$i]["specifications"]."`".
                        $data[$i]["volumetric_weight"]."`".
                        $data[$i]["status"]."`".
                        $data[$i]["store"];
            $data[$i] = urldecode($return);
        }
        echo implode("@", $data);
        break;
    case "order_edit":
        $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改訂單ID ".$_POST["fi_no"]." 備註與商品追蹤碼等狀態' , NOW())";
        $dba->query($query);
        $query = "update order_form set remarks='".$_POST["remarks"]."' where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        if(!empty($_POST["trace_code"]))
        {
            $query = "select trace from order_index where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
            $data = $dba->query($query);
            $trace = (array)json_decode($data[0]["trace"]);
            $trace[date("Y/m/d h:i:s",time())] = array($_POST["store_code"],$_POST["trace_code"]);
            $trace = tina_encode($trace);
        }
        else
        {
            $query = "select trace from order_index where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
            $data = $dba->query($query);
            $trace = $data[0]["trace"];
        }
        $query = "update order_index set trace='".$trace."' where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        $query = "update order_form set trace='".$trace."' where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        
        break;
    case "order_shipped":
        $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改訂單ID ".$_POST["fi_no"]." 中的運輸狀態為已出貨' , NOW())";
        $dba->query($query);
        $query = "update order_form set status_order=1,status_pay=2,status_transport=2,status_receiving=1 where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        $query = "update order_index set status_order=1,status_pay=2,status_transport=2,status_receiving=1 where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $query = "select `sn`,`trace` from order_index where fi_no=".$_POST["fi_no"];
        $order = $dba->query($query);
        $fuer->updateorder($order[0]["sn"], 1, 2, 2, 1, $order[0]["trace"]);
        break;
    case "order_cancel":
        $query="insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 修改訂單ID ".$_POST["fi_no"]." 包含訂單取消,付款,運輸與收貨等狀態歸 0' , NOW())";
        $dba->query($query);
        $query = "update order_form set status_order=2,status_pay=0,status_transport=0,status_receiving=0  where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        $query = "update order_index set status_order=2,status_pay=0,status_transport=0,status_receiving=0  where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $query = "select `sn`,`trace` from order_index where fi_no=".$_POST["fi_no"];
        $order = $dba->query($query);
        $fuer->updateorder($order[0]["sn"], 2, 0, 0, 0, $order[0]["trace"]);
        break;
}

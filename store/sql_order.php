<?php
session_start();
if(empty($_SESSION['backend']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
include_once "../swop/setting/config.php";
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

function mc_encrypt($encrypt,$k,$v) {
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
	mcrypt_generic_init($td,$k,$v);
	$encrypted = mcrypt_generic($td, $encrypt);
	$encode = base64_encode(base64_encode($encrypted));
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return $encode;
}
foreach($_POST as $k => $v)
{
    $k = str_replace("\_", "_", $k);
    $v = str_replace("\_", "_", $v);
    $_POST[$k] = $v;
}
switch($_POST["query_type"])
{
    case "get_order_form":
        $query = "select * from order_form where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $data = $dba->query($query);
        $data = $data[0];
        $data["identity"]=  json_decode($data["identity"]);
        $len = count($data["identity"]);
        for($i = 0; $i < $len;$i++)
        {
            $data["identity"][$i] = mc_encrypt($data["identity"][$i],$base["ide_key"],$base["ide_iv"]);
        }
        $data["identity"]=  json_encode($data["identity"]);
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
    case "get_store_shipping":
        $query = "select * from store_shipping where store=".$login_store;
        $data = $dba->query($query);
        $len = count($data);
        for($i=0;$i<$len;$i++)
        {
            $return = "";
            $return .=  $data[$i]["fi_no"]."`".
                        $data[$i]["name"]."`".
                        $data[$i]["item"]."`".
                        $data[$i]["images"];
            $data[$i] = urldecode($return);
        }
        echo implode("∵", $data);
        break;
    case "get_store_picking":
        $query = "select * from store_picking where store=".$login_store." order by fi_no asc";
        $data = $dba->query($query);
        $len = count($data);
        for($i=0;$i<$len;$i++)
        {
            $return = "";
            $return .=  $data[$i]["fi_no"]."`".
                        $data[$i]["name"]."`".
                        $data[$i]["title_context"]."`".
                        $data[$i]["title_images"]."`".
                        $data[$i]["ending_context"]."`".
                        $data[$i]["ending_images"];
            $data[$i] = urldecode($return);
        }
        echo implode("∵", $data);
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
            $query = "update order_index set date_transport='".date("Y-m-d H:i:s")."' where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
            $dba->query($query);
            $query = "update order_form set date_transport='".date("Y-m-d H:i:s")."' where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
            $dba->query($query);
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
        $query = "select `sn`,`trace`,`checkout` from order_index where fi_no=".$_POST["fi_no"];
        $order = $dba->query($query);
        $fuer->updateorder($order[0]["sn"], 1, 2, 2, 1, $order[0]["trace"], $order[0]["checkout"]);
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
        $query = "select `sn`,`trace`,`checkout` from order_index where fi_no=".$_POST["fi_no"];
        $order = $dba->query($query);
        $fuer->updateorder($order[0]["sn"], 2, 0, 0, 0, $order[0]["trace"], $order[0]["checkout"]);
        break;
}

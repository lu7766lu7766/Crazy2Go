<?php
session_start();
if(empty($_SESSION['serviceqq']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$login_fi_no = $_SESSION['serviceqq']['login_fi_no'];
$login_store = $_SESSION['serviceqq']['login_store'];

switch($_POST["query_type"])
{
    case "save_qq":
        $query = "update service_item set qq='".$_POST["qq"]."' where service='".$login_fi_no."' and fi_no='".$_POST["item_id"]."'";
        $dba->query($query);
        break;
    case "save_nickname":
        $query = "update service_item set name='".$_POST["name"]."' where service='".$login_fi_no."' and fi_no='".$_POST["item_id"]."'";
        $dba->query($query);
        break;
    case "save_content":
        $query = "update service_item set content='".$_POST["content"]."',qq='".$_POST["qq"]."',name='".$_POST["name"]."', end_date=NOW() where service='".$login_fi_no."' and fi_no='".$_POST["item_id"]."'";
        $dba->query($query);
        echo $query;
        break;
    case "wait_date":
        //更新在線時間
        $query = "update service set wait_date=NOW() where fi_no=".$login_fi_no;
        $dba->query($query);
        $query = "select fi_no,start_date,qq,name from service_item where service=".$login_fi_no." and end_date is null order by start_date DESC";
        $data = $dba->query($query);
        $len = count($data);
        for($i = 0; $i < $len; $i++)
        {
            $data[$i] = $data[$i]['fi_no']."`".
                        $data[$i]['start_date']."`".
                        $data[$i]['qq']."`".
                        $data[$i]['name'];
        }
        echo implode("@", $data);
        break;
    case "get_order_index":
        $query = "select fi_no,member from order_index where sn like '".$_POST["sn"]."'";
        $data = $dba->query($query);
        $l = count($data);
        if($l == 0)
        {
            echo "";
            return;
        }
        echo urldecode(implode("`", $data[0]));
        break;
    case "get_order_detail":
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
    case "update_discount":
        $query = "select discounts from order_form where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $data = $dba->query($query);
        $data = $data[0]['discounts']; 
        echo $data;
        if($data=="")
        {
            $discount[$login_store] = array($_POST["discount"],$login_fi_no,date("Y-m-d H:i:s",time()));
            $_POST["discount"] = json_encode($discount);
        }
        else
        {
            $discount = json_decode($data);
            $discount->$login_store = array($_POST["discount"],$login_fi_no,date("Y-m-d H:i:s",time()));
            $_POST["discount"] = json_encode($discount);
        }
        $query = "update order_form set discounts='".$_POST["discount"]."' where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        $query = "update order_index set discounts='".$_POST["discount"]."' where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        break;
}
<?php
session_start();
if(empty($_SESSION['backend']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
require_once "../swop/library/dba.php";
$dba=new dba();
//ini_set("display_errors", "On");
//error_reporting(E_ALL & ~E_NOTICE);
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
    case "supplier_add":
        $name = $_POST["supplier_name"];
        $nickname = $_POST["supplier_nickname"];
        $company = tina_encode(array_combine($_POST["company_key"], $_POST["company_val"]));
        $returns = tina_encode(array_combine($_POST["returns_key"], $_POST["returns_val"]));
        $operate = tina_encode(array_combine($_POST["operate_key"], $_POST["operate_val"]));
        $license = tina_encode(array_combine($_POST["license_key"], $_POST["license_val"]));
        $bank = tina_encode(array_combine($_POST["bank_key"], $_POST["bank_val"]));
        $service = tina_encode(array(
            "0"=>array_combine($_POST["service_0_key"], $_POST["service_0_val"]),
            "1"=>array_combine($_POST["service_1_key"], $_POST["service_1_val"]
        )));
        $shipper = tina_encode(array_combine($_POST["shipper_key"], $_POST["shipper_val"]));
        $check = tina_encode(array_combine($_POST["check_key"], $_POST["check_val"]));
        $query = "insert into store_supplier(store,name,nickname,company,returns,operate,license,bank,service,shipper,`check`)values(".$login_store.",'".$name."','".$nickname."','".$company."','".$returns."','".$operate."','".$license."','".$bank."','".$service."','".$shipper."','".$check."')";
        $dba->query($query);
        $insert_id = $dba->get_insert_id();
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增供應商 ".$_POST['name']."(編號):".$insert_id."',NOW())";
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $company = json_decode($company,true);
        $check = json_decode($check,true);
        $bank = json_decode($bank,true);
        $fuer->addsupplier($insert_id, $name, $company["corporate"], $company["registered_capital"], $company["address"], $company["phone"], $company["fax"], $check["name"], $check["email"], $check["phone"], $check["mobile"], $bank["bank"], $bank["branch"], $bank["account"], $bank["username"], $company["http"]);
        
        echo "儲存成功！";
        break;
    case "supplier_edit":
        $name = $_POST["supplier_name"];
        $nickname = $_POST["supplier_nickname"];
        $company = tina_encode(array_combine($_POST["company_key"], $_POST["company_val"]));
        $returns = tina_encode(array_combine($_POST["returns_key"], $_POST["returns_val"]));
        $operate = tina_encode(array_combine($_POST["operate_key"], $_POST["operate_val"]));
        $license = tina_encode(array_combine($_POST["license_key"], $_POST["license_val"]));
        $bank = tina_encode(array_combine($_POST["bank_key"], $_POST["bank_val"]));
        $service = tina_encode(array(
            "0"=>array_combine($_POST["service_0_key"], $_POST["service_0_val"]),
            "1"=>array_combine($_POST["service_1_key"], $_POST["service_1_val"]
        )));
        $shipper = tina_encode(array_combine($_POST["shipper_key"], $_POST["shipper_val"]));
        $check = tina_encode(array_combine($_POST["check_key"], $_POST["check_val"]));
        $query = "update store_supplier set name='".$name."',nickname='".$nickname."',company='".$company."',returns='".$returns."',operate='".$operate."',license='".$license."',bank='".$bank."',service='".$service."',shipper='".$shipper."',`check`='".$check."' where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 編輯供應商 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $company = json_decode($company,true);
        $check = json_decode($check,true);
        $bank = json_decode($bank,true);
        $fuer->updatesupplier($_POST["fi_no"], $name, $company["corporate"], $company["registered_capital"], $company["address"], $company["phone"], $company["fax"], $check["name"], $check["email"], $check["phone"], $check["mobile"], $bank["bank"], $bank["branch"], $bank["account"], $bank["username"], $company["http"]);
        
        echo "儲存成功！";
        break;
    case "supplier_del":
        $query = "update store_supplier set `delete`=1 where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除供應商 ".$_POST['name']."(編號):".$_POST["fi_no"]."',NOW())";
        $dba->query($query);
        
        require_once "../swop/library/erpconnect.php";
        $fuer = new Library_ERP();
        $fuer->deletesupplier($_POST["fi_no"]);
        
        break;
    case "get_supplier_detail":
        $query = "select * from store_supplier where fi_no=".$_POST["fi_no"];
        $return = $dba->query($query);
        $return = $return[0];
        $return =   $return['name'].'`'.
                    $return['nickname'].'`'.
                    $return['company'].'`'.
                    $return['returns'].'`'.
                    $return['operate'].'`'.
                    $return['license'].'`'.
                    $return['bank'].'`'.
                    $return['service'].'`'.
                    $return['shipper'].'`'.
                    $return['check'];
        echo $return;
        break;
}
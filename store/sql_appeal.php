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
    case "show_appeal":
        //取留言
        $query = "select fi_no,member,content,date,reply_content,reply_date from appeal_index where fi_no=".$_POST["fi_no"];
        $data1 = $dba->query($query);
        $data1 = $data1[0];
        $query = "select fi_no,name,id from member_index where fi_no=".$data1["member"];
        $data2 = $dba->query($query);
        $data2 = $data2[0];
        $data1["member_name"] = $data2["name"]."(".$data2["id"].")";
        $data3 = array();
        $data3["reply_content"]=$data1["reply_content"];
        $data3["reply_date"]=$data1["reply_date"];
        unset($data1["reply_content"]);
        unset($data1["reply_date"]);
        $query = "select fi_no,name,user from store_account where fi_no=".$login_store;
        $data4 = $dba->query($query);
        $data4 = $data4[0];
        $data3["store_account_name"] = $data4["name"]."(".$data4["user"].")";
        $q = &$data1;
        $a = &$data3;
        $q["answer"] = $a;
        echo tina_encode($q);
        break;    
    case "save_appeal":
        $query = "update appeal_index set reply_content='".$_POST["content"]."',reply_date=NOW() where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "update appeal_info set reply_content='".$_POST["content"]."',reply_date=NOW() where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增留言 appeal_index, appeal_info 的 fi_no = ".$_POST["fi_no"]."', NOW())";
        $dba->query($query);
        echo date("Y-m-d h:i:s");
        break;
    case "delete_appeal":
        $query = "update appeal_index set reply_content='',reply_date=null where fi_no=".$_POST["fi_no"];
        $dba->query($query);
        $query = "update appeal_info set reply_content='',reply_date=null where fi_no=".$_POST["fi_no"]." and member=".$_POST["member"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除留言 appeal_index, appeal_info 的 fi_no = ".$_POST["fi_no"]."', NOW())";
        $dba->query($query);
        echo "0000-00-00 00:00:00";
        break;
}

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
    case "show_evaluate":
        //設定已觀看
        $query = "update goods_vevaluate set view=1 where goods=".$_POST["fi_no"];
        $dba->query($query);
        //取留言
        $query = "select fi_no,content,member,evaluate_date from goods_evaluate where goods=".$_POST["fi_no"]." order by evaluate_date desc";
        $data1 = $dba->query($query);
        $len = count($data1);
        $pickup = array();
        for($i = 0;$i < $len; $i++)
        {
            $pickup[] = $data1[$i]["member"];
        }
        $pickup = implode(",", $pickup);
        $query = "select fi_no,name,id from member_index where fi_no in (".$pickup.") order by field(fi_no,".$pickup.")";
        $data2 = $dba->query($query);
        for($i = 0;$i < $len; $i++)
        {
            $data1[$i]["member_name"] = $data2[$i]["name"]."(".$data2[$i]["id"].")";
        }
        $query = "select fi_no,evaluate,score,content,account,respond_date from goods_respond where goods=".$_POST["fi_no"]." and `delete`=0 order by respond_date asc";
        $data3 = $dba->query($query);
        $len = count($data3);
        $pickup = array();
        for($i = 0;$i < $len; $i++)
        {
            $pickup[] = $data3[$i]["account"];
        }
        $pickup = array_unique($pickup);
        $pickup = implode(",", $pickup);
        
        $query = "select fi_no,name,user from store_account where fi_no in (".$pickup.") order by field(fi_no,".$pickup.")";
        $data4 = $dba->query($query);
        $len = count($data4);
        $account = array();
        for($i = 0;$i < $len; $i++)
        {
            $account[$data4[$i]["fi_no"]] = $data4[$i]["name"]."(".$data4[$i]["user"].")";
        }
        $len = count($data3);
        for($i = 0;$i < $len; $i++)
        {
            $data3[$i]["store_account_name"] = $account[$data3[$i]["account"]];
        }
        $q = &$data1;
        $a = &$data3;
        $qlen = count($q);
        $alen = count($a);
        for($i = 0; $i < $qlen; $i++)
        {
            $q[$i]["answer"] = array();
            for($j = 0; $j < $alen; $j++)
            {
                if($a[$j]["evaluate"] == $q[$i]["fi_no"])
                {
                    array_push($q[$i]["answer"], $a[$j]);
                }
            }
        }
        echo tina_encode($q);
        break;    
    case "save_evaluate":
        $query = "insert into goods_respond (goods,evaluate,score,content,account,respond_date,`delete`)values(".$_POST["fi_no"].",".$_POST["evaluate"].",".$_POST["score"].",'".$_POST["content"]."',".$_POST["account"].",NOW(),0)";
        $dba->query($query);
        $query = "select evaluation_score, evaluation_number from member_info where fi_no=".$_POST["member"];
        $evaluation = $dba->query($query);
        $query = "update member_info set evaluation_score=".($evaluation[0]["evaluation_score"]+$_POST["score"]).", evaluation_number=".($evaluation[0]["evaluation_number"]+1)." where fi_no=".$_POST["member"];
        $dba->query($query);
        $query = "update goods_evaluate set respond=1 where fi_no=".$_POST["evaluate"]." and goods=".$_POST["fi_no"];
        $dba->query($query);
        $query = "select respond from goods_evaluate where goods=".$_POST["fi_no"];
        $r = $dba->query($query);
        $all_respond = true;
        foreach($r as $v)
        {
            if($v["respond"] == 0)
            {
                $all_respond = false;
                break;
            }
        }
        if($all_respond)
        {
            $query = "update goods_vevaluate set respond=1 where fi_no=".$_POST["fi_no"];
            $dba->query($query);
        }
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 新增留言 goods_respond 的 fi_no = ".$dba->get_insert_id()."', NOW())";
        $dba->query($query);
        break;
    case "delete_evaluate":
        $query = "update goods_respond set `delete`=1 where fi_no=".$_POST["fi_no"]." and goods=".$_POST["goods"];
        $dba->query($query);
        $query = "select evaluation_score, evaluation_number from member_info where fi_no=".$_POST["member"];
        $evaluation = $dba->query($query);
        $query = "update member_info set evaluation_score=".($evaluation[0]["evaluation_score"]-$_POST["score"]).", evaluation_number=".($evaluation[0]["evaluation_number"]-1)." where fi_no=".$_POST["member"];
        $dba->query($query);
        $query = "update goods_evaluate set respond=0 where fi_no=".$_POST["evaluate"]." and goods=".$_POST["goods"];
        $dba->query($query);
        $query = "update goods_vevaluate set respond=0 where fi_no=".$_POST["goods"];
        $dba->query($query);
        $query = "insert into store_log (store,account,action,action_date) values (".$login_store.",".$login_fi_no.",'".$login_username." 刪除留言 goods_respond 的 fi_no = ".$_POST["fi_no"]."', NOW())";
        $dba->query($query);
        break;
}

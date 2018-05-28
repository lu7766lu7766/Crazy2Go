<?php
session_start();
if(empty($_SESSION['serviceqq']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");

require_once "../swop/library/dba.php";
$dba=new dba();

$login_fi_no = $_SESSION['serviceqq']['login_fi_no'];
$login_username = $_SESSION['serviceqq']['login_username'];

switch($_POST["query_type"])
{
    case "logout":
        $query = "insert into service_log (service,action,action_date) values (".$login_fi_no.",'".$login_username." 登出', NOW())";
        $dba->query($query);
        unset($_SESSION['serviceqq']);
        break;
}
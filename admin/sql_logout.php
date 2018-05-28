<?php
if(empty($_SESSION['admin']['login_user']) || !isset($_POST["query_type"]))header("Location:index.php");
session_start();

require_once "../swop/library/dba.php";
$dba=new dba();

switch($_POST["query_type"])
{
    case "logout":
    	$fi_no		= $_SESSION['admin']['login_fi_no'];
        $username	= $_SESSION['admin']['login_username'];
        
		$result	= $dba->query("insert into administrator_log 
									(administrator,action,action_date) 
							  values('$fi_no', '$username 登出',NOW());");
		unset($_SESSION['admin']);
        break;
}
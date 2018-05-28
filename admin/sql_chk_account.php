<?php
if(!isset($_POST["query_type"]))header("Location:index.php");
session_start();

require_once "../swop/library/dba.php";
$dba=new dba();

switch($_POST["query_type"])
{
    case "chk_account":
    	$user	= $_POST["user"];
    	$table	= $_POST["table"];
    	$feild	= $_POST["feild"]==""?"user":$_POST["feild"];
		//echo "select 1 from administrator where user='$username' limit 1;";
		$all_user = $dba->query("select $feild from $table");
		
		foreach($all_user as $per_user){
			if($per_user[$feild]===(string)$user){
				die("false");
			}
		}
		die("true");
    break;
}
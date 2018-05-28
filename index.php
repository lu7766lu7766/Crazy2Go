<?php
session_start();

$time_start = microtime(true);

//----

require_once "swop/setting/config.php";

$air = str_replace("/www/global2buy", "", $_SERVER['REQUEST_URI']);
$base_hierarchy = explode("/", $air);

if(substr_count($base_hierarchy[1], "?") > 0) { //取得第一層, 檔名
	$base_protect = explode("?", $base_hierarchy[1]);
	$base_file = $base_protect[0];
}
else {
	$base_file = $base_hierarchy[1];
}

if(count($base_hierarchy) > 2) { //取得第二層, 函式
	if(substr_count($base_hierarchy[2], "?") > 0) {
		$base_prevent = explode("?", $base_hierarchy[2]);
		$base_second = $base_prevent[0];
	}
	else {
		$base_second = $base_hierarchy[2];
	}
}

$base_url = $base['dir'].$base_file.".php";
if(!file_exists($base_url) || !$base_file) { //驗證檔案是否存在
	$base_url = $base['dir']."main.php";
}

/*
if(substr_count($base_second, "ajax") == 0 && substr_count($base_second, "code") == 0) {
	echo '<div style="background-color:rgba(255, 255, 255, 0.55); padding:10px; text-align:center;">'.$_SERVER['REQUEST_URI']."<br />".$base_file."<br />".$base_second."<br />".$base_url."</div>\r\n";
}
*/

require_once $base_url;
$swop = new Main_Controllers($base);
if($base_file && file_exists($base['dir'].$base_file.".php") && $base_second && method_exists($swop, $base_second)) { //驗證檔案與函式是否存在
	$swop->$base_second();
}
else {
	$swop->home();
}

//----

$time_end = microtime(true);
$time = $time_end - $time_start;

if(substr_count($base_second, "ajax") == 0 && substr_count($base_second, "code") == 0) {
	echo "\r\n<script type='text/javascript'>var timese = document.getElementById('execution_time'); timese.innerHTML=timese.innerHTML+'執行時間 ".$time." 秒';</script>";
}
?>
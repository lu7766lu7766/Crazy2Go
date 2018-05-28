<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);

date_default_timezone_set("Asia/Taipei");

$base['dir'] = "swop/controllers/";
$base['lang'] = "cn";
$base['url'] = "http://www.crazy2go.com/";
$base['css'] = $base['url']."public/css/";
$base['js'] = $base['url']."public/js/";
$base['img'] = $base['url']."public/img/";
$base['adm'] = $base['url']."public/img/advertisement/";
$base['cat'] = $base['url']."public/img/category/";
$base['god'] = $base['url']."public/img/goods/";
$base['int'] = $base['url']."public/img/introduction/";
$base['sto'] = $base['url']."public/img/store/";
$base['thu'] = $base['url']."public/img/thumbnail/";
$base['min'] = $base['url']."public/img/minimize/";
$base['tpl'] = $base['url']."public/img/template/";
$base['mbr'] = $base['url']."public/img/member/";
$base['app'] = $base['url']."public/img/application/";
$base['shp'] = $base['url']."public/img/shipping/";
$base['lgo'] = $base['url']."public/img/logo/";

$base['ide'] = "W8bqSraWZHtyz8xVJaMQ/";
$base['ide_url'] = $base['url']."public/img/".$base['ide'];
$base['ide_dir'] = "/var/www/html/crazy2go_com/public/img/".$base['ide'];
$base['ide_key'] = "jIhwD68TAsrbw9g96FEGHgtqyvqcZY85";
$base['ide_iv'] = "MxYkqhwNJw9ISZGc";

if(!empty($_SERVER["HTTP_CLIENT_IP"])) {$base['ip'] = $_SERVER["HTTP_CLIENT_IP"];}
elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {$base['ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];}
else {$base['ip'] = $_SERVER["REMOTE_ADDR"];}

function clean($data) {
	if(!get_magic_quotes_gpc()) {$data = addslashes($data);}
	$data = str_replace("_", "\_", $data);
	$data = str_replace("%", "\%", $data);
	$data = nl2br((string)$data);
	$data = htmlspecialchars($data);
	return $data;  
}
foreach($_GET as $key => $value) {if($value!='') {$_GET[$key] = clean($value);}}
foreach($_POST as $key => $value) {if($value!='') {$_POST[$key] = clean($value);}}

/*
function start_session($expire = 0) {
	if ($expire == 0) {
		$expire = ini_get('session.gc_maxlifetime');
	} else {
		ini_set('session.gc_maxlifetime', $expire);
	}
	
	if (empty($_COOKIE['PHPSESSID'])) {
		session_set_cookie_params($expire);
		session_start();
	} else {
		session_start();
		setcookie('PHPSESSID', session_id(), time() + $expire);
	}
}
start_session(3600);
*/
?>
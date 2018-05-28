<?php
session_start();
if(empty($_SESSION['backend']['login_user']) || empty($_GET["file"]))header("Location:index.php");
include_once "../swop/setting/config.php";
foreach($_GET as $k => $v)
{
    $k = str_replace("\_", "_", $k);
    $v = str_replace("\_", "_", $v);
    $_GET[$k] = $v;
}
function mc_decrypt($decrypt,$k,$v) {
	$decoded = base64_decode(base64_decode($decrypt));
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
	mcrypt_generic_init($td,$k,$v);
	$decrypted = mdecrypt_generic($td, $decoded);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return trim($decrypted);
}
$path = "../public/img/".$base['ide'];
$file_name = mc_decrypt($_GET["file"], $base['ide_key'], $base['ide_iv']);
$file = $path.$file_name;
$ext = explode(".",$file_name);
$ext = $ext[count($ext)-1];
$ext = strtolower($ext);
$ext_type = "";
switch($ext)
{
    case "jpg":
    case "jpeg":
        $ext_type = "JPEG";
        break;
    case "png":
        $ext_type = "PNG";
        break;
    case "gif":
        $ext_type = "GIF";
        break;
    case "bmp":
        $ext_type = "BMP";
    default :die();
}
header("Content-type: image/".$ext_type, true);
$f = fopen($file, "r");
$image = fread($f, filesize($file));
fclose($f);
echo $image;

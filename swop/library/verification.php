<?php
class Library_Verification {

	public function __construct() {
	}

	public function kernel($codelen = 4, $width = 130, $height = 50, $fontsize = 20) {
		$charset = "abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789"; //驗證碼元素
		$code;			//驗證碼
		$img;			//輸出圖片
		$font;			//字型
		$fontcolor;		//字體顏色
		$font = dirname(__FILE__)."/elephant.ttf";
		
		//繪製背景
		$img = imagecreatetruecolor($width, $height);
		$color = imagecolorallocate($img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
		imagefilledrectangle($img, 0, $height, $width, 0, $color);
		
		//產生驗證碼
		$_len = strlen($charset) - 1;
		for($i=0; $i<$codelen; $i++) {
			$code .= $charset[mt_rand(0, $_len)];
		}
		$this->code = $code;
		
		//繪製干擾
		for($i=0; $i<6; $i++) {
			$color = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
			imageline($img, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $color);
		}
		for($i=0; $i<100; $i++) {
			$color = imagecolorallocate($img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagestring($img, mt_rand(1, 5), mt_rand(0, $width), mt_rand(0, $height), "*", $color);
		}
		
		//繪製文字
		$_x = $width / $codelen;
		for($i=0;$i<$codelen;$i++) {
			$fontcolor = imagecolorallocate($img, mt_rand(0, 156), mt_rand(0,156), mt_rand(0, 156));
			imagettftext($img, $fontsize, mt_rand(-30, 30), $_x*$i+mt_rand(1, 5), $height/1.4, $fontcolor, $font, $code[$i]);
		}
		
		//輸出
		header('Content-Type: image/png');
		//ob_start();
		imagepng($img);
		//$this->img = base64_encode(ob_get_clean());
		imagedestroy($img);
				
		return $this;
	}
}
?>
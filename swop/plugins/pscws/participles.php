<?php
class Plugins_Participles {

	public function __construct() {
	}

	public function analysis($text) {
		if($text != '' && $text != NULL) {
			date_default_timezone_set("Asia/Taipei");
			
			if (!class_exists('PSCWS4')){
				require 'pscws4.class.php';
			}
			$cws = new PSCWS4('utf8');
			$cws->set_charset('utf8');
			//$cws->set_dict('etc/dict_cht.utf8.xdb');	//繁體
			//$cws->set_rule('etc/rules_cht.utf8.ini');
			$cws->set_dict(dirname(__FILE__).'/etc/dict.utf8.xdb');		//簡體
			$cws->set_rule(dirname(__FILE__).'/etc/rules.utf8.ini');
			$cws->set_multi(2);
			$cws->set_ignore(true);
			$cws->set_duality(true);
			$cws->send_text($text);
			
			$line = '';
			while($tmp = $cws->get_result()) {
				foreach($tmp as $w) {
					if ($w['word'] == "\r") continue;
					if ($w['word'] == "\n") {
						$line = rtrim($line, ' ') . "\n";
					}
					else {
						$line .= $w['word'] . " ";
					}
				}
			}
			$cws->close();
		}
		return $line;
	}
}
?>
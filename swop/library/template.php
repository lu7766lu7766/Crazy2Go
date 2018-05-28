<?php
class Library_Template {

	public function __construct() {
		$this->base = $base;
	}

	public function keyword() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$results = $dba->query("select * from `advertisement` where `show` = 1 && `page` = 'search' order by `weights` desc limit 0, 5");
		
		for($i=0; $i<count($results); $i++) {
			if($results[$i]['type'] == 1) {
				$content .= "<a href='http://www.crazy2go.com/search?keyword=".$results[$i]['item']."'>".$results[$i]['item']."</a>　";
			}
			else if($results[$i]['type'] == 2) {
				$content .= "<a href='http://www.crazy2go.com/brand?keyword=".$results[$i]['item']."'>".$results[$i]['item']."</a>　";
			}
		}
		
		$this->keyword_content = $content;
		return $this;
	}
	
	public function advertisement() {
		require_once "swop/library/dba.php";
		$dba=new dba();
		$results = $dba->query("select * from `advertisement` where `show` = 1 && `page` = 'top' order by `weights` desc limit 0, 1");
		
		$this->advertisement_content['images'] = $results[0]['images'];
		$this->advertisement_content['url'] = $results[0]['url'];
		return $this;
	}
}
?>
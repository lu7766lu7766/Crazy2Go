<?php
class Library_Sidebar {

	public function __construct() {
	}

	public function navigation() {
		$content = "<div id='sidebar'>";
		$content = $content."<div id='sideoff'>";
		$content = $content."→";
		$content = $content."</div>";
		$content = $content."<div id='sideinfo'>";
		$content = $content."";
		$content = $content."</div>";
		$content = $content."</div>\r\n";
		
		$this->sidebar_content = $content;
		return $this;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="all">
<meta name="description" content="描述">
<meta name="keywords" content="關鍵,關鍵,關鍵">
<meta name="copyright" content="版權所有">
<meta name="viewport" content="width=1225">
<title>Crazy2Go 跨域瘋商城 - 台灣及日韓歐美進口商品</title>
<link rel="stylesheet" href="<?php echo $this->base['css']; ?>common.css" />
<link rel="stylesheet" href="<?php echo $this->base['css']; ?>jquery-ui.min.css" />
<?php
if(count($this->css) != 0) {
	for($i=0; $i<count($this->css); $i++) {
		echo '<link rel="stylesheet" href="'.$this->base['css'].$this->css[$i].'.css"></script>';
	}
}
?>
<script src="<?php echo $this->base['js']; ?>jquery-2.1.1.min.js"></script>
<script src="<?php echo $this->base['js']; ?>jquery-ui.min.js"></script>
<script src="<?php echo $this->base['js']; ?>jquery.lazyload.min.js"></script>
<script src="<?php echo $this->base['js']; ?>jquery.timer.js"></script>
<script src="<?php echo $this->base['js']; ?>jquery.transit.js"></script>
<script src="<?php echo $this->base['js']; ?>jquery.cookie.js"></script>
<script src="<?php echo $this->base['js']; ?>common.js"></script>
<script src="<?php echo $this->base['js']; ?>jquery.validate.js"></script>
<script src="<?php echo $this->base['js']; ?>additional-methods.js"></script>
<script src="<?php echo $this->base['js']; ?>history_log.js"></script>
<?php 
if($_GET['nohistory'] != 1) {
	if(strpos($_SESSION['admin']['login_permissions'], "get_hsitory")!==false||$_SESSION['admin']['login_permissions']=="all") {
		echo '<script src="'.$this->base['js'].'get_history.js"></script>';
	}
}
?>

<?php
if(count($this->js) != 0) {
	for($i=0; $i<count($this->js); $i++) {
		echo '<script src="'.$this->base['js'].$this->js[$i].'.js"></script>';
	}
}
?>
</head>

<body>
<div class="main">
	<div id="back_top" style="width:100%; height:522px; position:absolute; z-index:1;">
		<div style="width:100%; height:38px; border-bottom:#CCCCCC solid 1px; background-color:#F7F7F7; color:#555555;"></div>
		<div style="width:100%; height:134px;"></div>
		<div style="width:100%; height:35px; background-color:#EB3339;"></div>
		<div id="primary_back" style="width:100%; height:315px;"></div>
	</div>
	
	<div class="frame">
		<div class="template_top">
			
			<?php
			require_once "swop/library/quick.php";
			$quick_a = new Library_Quick();
			$quick_a->navigation_top();
			?>
			<div style="height:26px; font-size:8pt; margin-top:12px; border-bottom:#CCCCCC solid 1px; background-color:#F7F7F7; color:#555555;">
				<div style="float:left; width:50%;">
					<img src="<?php echo $this->base['tpl']; ?>index.png" style="position:relative; top:1px;">&nbsp;<a id='ontop' href="<?php echo $this->base['url']; ?>">返回首頁</a>
					<?php if($_SESSION['info'] == "") { ?>
					<img src="<?php echo $this->base['tpl']; ?>login.png" style="margin-left:15px; position:relative; top:1px;">&nbsp;<a href="<?php echo $this->base['url']; ?>member/">登錄</a> ｜ <a href="<?php echo $this->base['url']; ?>member/register/">免費註冊</a>
					<?php } else { ?>
					<img src="<?php echo $this->base['tpl']; ?>login.png" style="margin-left:15px; position:relative; top:1px;">&nbsp;Hey，<?php echo $_SESSION['info']['id']; ?> [<a id="common_logout" href="#">退出</a>]
					<?php } ?>
					<!--img src="<?php echo $this->base['tpl']; ?>shoplogin.png" style="margin-left:15px; position:relative; top:2px;">&nbsp;<a href="#">商家中心</a-->
					<img src="<?php echo $this->base['tpl']; ?>sitemap.png" style="margin-left:15px; position:relative; top:3px;">&nbsp;<a href="#">網站導航</a>
				</div>
				<div style="float:left; width:50%; text-align:right;">
					<img src="<?php echo $this->base['tpl']; ?>myaccount.png" style="position:relative; top:2px;">&nbsp;<a href="<?php echo $this->base['url']; ?>member/center/">我的跨域瘋</a>
					<img src="<?php echo $this->base['tpl']; ?>shopcart.png" style="margin-left:15px; position:relative; top:1px;"><?php if($_SESSION['info']!=""){ ?>&nbsp;<span style="display:inline-block; background-color:#EC3239; color:#fff; border-radius:3px; padding:1px 3px 1px 3px;"><?php $cart = explode("｜", $_SESSION['info']['cart']); echo count($cart)-1; ?></span><?php }?>&nbsp;<a href="<?php echo $this->base['url']; ?>cart/">購物車</a>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div style="height:134px;">
				<div style="float:left; width:210px; height:90px; margin-top:18px;">
					<a href="<?php echo $this->base['url']; ?>">
						<div style="position:relative;">
							<!--div style="position:absolute; z-index:50; left:78px; top:-25px;">
								<img src="<?php echo $this->base['url']; ?>public/img/happynewyear.gif" style="width:102px; -ms-transform:rotate(0deg); -moz-transform:rotate(0deg); -webkit-transform:rotate(0deg); -o-transform:rotate(0deg); transform:rotate(0deg);">
								<div style="position:absolute; z-index:50; left:60px; top:17px; color:red;">2015</div>
								<div style="position:absolute; z-index:50; left:85px; top:46px; height:15px; width:15px; background-color:white;"></div>
							</div-->
							
							<div style="position:relative;">
								<div style="position:absolute; top:-10px; color:red; font-size:8pt; width:310px;">本站正在建構測試中,尚未正式對外開放啟用,敬請期待...</div>
								<img src="<?php echo $this->base['tpl']; ?>logo.png" style="position:relative;">
							</div>
						</div>
					</a>
				</div>
				<div style="float:left; margin-left:95px;">
					<div style="width:583px; height:32px; border:#BABABA solid 1px; border-radius:1px; margin-top:39px;"><?php
						require_once "swop/library/search.php";
						$search = new Library_Search();
						$search->engine($this->base['tpl']);
						echo $search->search_content;
						?></div>
					<div class="asearchd" style="margin-top:8px;">熱門關鍵詞：<?php
						require_once "swop/library/template.php";
						$template = new Library_Template();
						$template->keyword();
						echo $template->keyword_content;
						?></div>
				</div>
				<div style="float:left; margin:23px 0 0 127px;"><a href="<?php
						require_once "swop/library/template.php";
						$template = new Library_Template();
						$template->advertisement();
						echo $template->advertisement_content['url'];
						?>"><img class="atopd" src="<?php echo $this->base['adm'].$template->advertisement_content['images']; ?>"></a></div>
				<div style="clear:both;"></div>
			</div>
			<div id="quick_links" style="position:relative; height:35px; line-height:35px; background-color:#EB3339; color:#fff; ">
				<div style="position:absolute; z-index:50; left:700px; top:-50px;"></div>
				<div id='menu_switch' style="float:left; padding-left:30px; font-size:12pt; font-weight:bold; width:178px; background-color:#F89F2C; cursor:pointer;  position:relative; z-index:51;">全部商品分類</div>
				<div class='quick_top' style="float:left;"><?php echo $quick_a->content['top']; ?></div>
				<div style="clear:both;"></div>
			</div>
			
			<?php
			require_once "swop/library/category.php";
			$category = new Library_Category();
			$category->menu($this->base['url'], $this->base['tpl']);
			echo $category->category_content;
			?>
			
			<div id='sidebar' class='quick_sidebar'><div id='sideoff'></div><div id='sideinfo'></div></div><div id='sidemassage'></div>
					
		</div>
		
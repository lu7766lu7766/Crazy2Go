<?php 
	include_once("../swop/library/page_view2history.php");
	include_once("../swop/library/dba.php");
	$dba=new dba();
	/*$data=array();
	$data["table_name"]="goods_index";
	$data["fi_no"]="1";
	$data["time_enter"]="2014-12-16 12:00:00";
	$data["page_enter"]="http://www.google.com.tw";
	//$data["page_now"]="http://www.yahoo.com.tw";
	$data["obj_xpath"]="html>body>div[2]>div>input";
	$data["os"]="macintel";
	page_view2history($data);
	print_r($data);*/
?>
<!DOCTYPE html>
	<head>
		<!---->
		
		<!--<link rel="stylesheet" type="text/css" href="../public/css/tooltipster.css" />-->
		<script type="text/javascript" src="../public/js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="../public/js/history_log.js"></script>
		<script type="text/javascript" src="../public/js/get_history.js"></script>
		
		
		<!--<link rel="stylesheet" type="text/css" href="http://qtip2.com/v/stable/jquery.qtip.css" />
		<script type="text/javascript" src="http://qtip2.com/v/stable/jquery.qtip.js"></script>
		<link rel="stylesheet" type="text/css" href="../public/css/jquery.qtip.min.css" />
		<script type="text/javascript" src="../public/js/jquery.qtip.min.js"></script>-->
		<!--<script type="text/javascript" src="../public/js/jquery.tooltipster.min.js"></script>-->
		
		<script type="text/javascript">
			$(document).ready(function(){
				//$("html:eq(0) > body:eq(0) > div:eq(0) > div:eq(1) >div:eq(2) > div:eq(0)").html("ccccc")
				
				/*$('a').qtip({
				    content: 'II am positioned using corner values!',
				    position: {
				        my: 'bottom right',
				        at: 'top left',
				        viewport: $(window),
						adjust: { method: 'shift' }
					},
				    show: true,
				    hide:false
				}).qtip('option', 'content.text', 'New content');*/
			})
		</script>
	</head>
	<body>
		<br><br>
		<div id="123">
			<a href="test2.php">2234</a>
			<div style="text-align: center"><a href="test2.php">center</a></div>
			<div></div>
			<div>
				<div></div>
				<div></div>
				<div>
					<input type='button' onclick'location.href="http://www.google.com.tw"'/>
					<div>
						<a id="111" href="test2.php">111</a>
						<a href="test2.php" title="1zzz">222</a>
						<div style="position:absolute;top:150px;left:300px;"><a href="test2.php" title="bb">test</a></div>
					</div>
					<div id="demo" title='show this'>cCcC</div>
					<div class="tooltip" title="This text is in bold case !"> 
				        This div has a tooltip with HTML when you hover over it!
				    </div>
				    <div style="position:absolute;top:200px;left:500px;"><a href="test2.php" title="bb">333</a></div>
				</div>
			</div>
		</div>
	</body>
</html>
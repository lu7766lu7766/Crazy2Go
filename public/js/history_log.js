$(document).ready(function(){
	
	var site_url = "http://www.crazy2go.com/";
	var control_name = "history_log";
	var os = navigator.platform;
	var browser = navigator.userAgent;
	//上一頁
	var page_enter = document.referrer;
	//現在網址
	var page_now = document.URL;
	//物件路徑
	var obj_xpath = "";
	
    //取得時間
    $.ajax({
		async: false,
		type: "POST",
		url: site_url+control_name+"/ajax_set_time_enter"
	});
	//程式離開執行
	window.onunload = function unLoad(e) {
		
		if( obj_xpath=="" || obj_xpath=="/html" || obj_xpath=="/html/body" ){return;}
		console.log(obj_xpath)
		$.ajax({
			async: false,
			type: "POST",
			url: site_url+control_name+"/ajax_page_out",
			data: { 
				os: 		os,
				browser: 	browser, 
				page_enter: page_enter,
				page_now: page_now,
				obj_xpath: obj_xpath
			}
		}).done(function( msg ) {
			console.log( msg );
		});
	}
	//上層物件也會一併觸發
	$("*").click(function(){
		tmp_path = $(this).getXPath();
		//確保取得是末端物件
		if( obj_xpath == ""){
			obj_xpath = tmp_path;
		}else{
			if( obj_xpath.indexOf(tmp_path) != 0){
				obj_xpath = tmp_path;
			}
		}
		//兩秒內還原
		setTimeout(function(){obj_xpath=""},2000);
	})
	//取得xpath
	$.fn.getXPath = function(rootNodeName){
		//other nodes may have the same XPath but because this function is used to determine the corresponding input name of a data node, index is not included 
		var position,
			$node = this.first(),
			nodeName = $node.prop('nodeName'),
			$sibSameNameAndSelf = $node.siblings(nodeName).addBack(),
			steps = [], 
			$parent = $node.parent(),
			parentName = $parent.prop('nodeName');
 
		position = ($sibSameNameAndSelf.length > 1) ? '['+($sibSameNameAndSelf.index($node)+1)+']' : '';
		steps.push(nodeName+position);
 
		while ($parent.length == 1 && parentName !== rootNodeName && parentName !== '#document'){
			$sibSameNameAndSelf = $parent.siblings(parentName).addBack();
			position = ($sibSameNameAndSelf.length > 1) ? '['+($sibSameNameAndSelf.index($parent)+1)+']' : '';
			steps.push(parentName+position);
			$parent = $parent.parent();
			parentName = $parent.prop('nodeName');
		}
		return '/'+steps.reverse().join('/').toLowerCase();
	};
})

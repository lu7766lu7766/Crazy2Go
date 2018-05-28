
$(document).ready(function(){
	//取得ＧＥＴ變數
	$.extend({
		getUrlVars: function(){
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++){
		    	hash = hashes[i].split('=');
				vars.push(hash[0]);
				vars[hash[0]] = hash[1];
	    	}
			return vars;
		},
		getUrlVar: function(name){
			return $.getUrlVars()[name];
		}
	});
	//讀取CSS
	loadCSS = function(href) {
	    var cssLink = $("<link rel='stylesheet' type='text/css' href='"+href+"'>");
		$("head").append(cssLink); 
	};
	
	var burden_doc = "";
	if(window.location.href.substr(-1)!='/'){
		var url = window.location.href;
		url = url.substring(url.lastIndexOf('/')+1);
		if( url.indexOf('?')==-1 && url.indexOf('.')==-1 && url.indexOf('#')==-1){
			burden_doc = '/';
		}
	}
	
	var site_url = "http://www.crazy2go.com/";
	var tooltipJS_src = site_url+"public/js/jquery.qtip.js";
	var tooltipCSS_src = site_url+"public/css/jquery.qtip.css";
	
	loadCSS(tooltipCSS_src);
	//工具物件ＣＳＳ
	var toolbar_btn_css = 
		"<style type='text/css'>"+
			".main {"+
				"position: relative;"+
				"top:30px;"+
			"}"+
			".history_icon{"+
				"position: relative;"+
				"top: 4px;"+
				"width:16px;"+
				"margin-right: 2px;"+
			"}"+
			".history_btn{"+
				"cursor:pointer;"
			"}"+
		"</style>";
	$("head").append(toolbar_btn_css); 
	
	//判斷是否隱藏點擊範圍
	var hide_rect = 0;
	//判斷是否隱藏統計數據
	var hide_qtip = 0;
	//點擊數據顯示狀態
    var show_mode = "%";
    //網址分析狀態
    var analysis_mode = 1;//1含參數，0不含參數
    
    function init_btn(){
	    
	    var rect_text = hide_rect==0?"隱藏點擊範圍":"顯示點擊範圍";
	    var qtip_text = hide_qtip==0?"隱藏統計數據":"顯示統計數據";
	    var show_text = show_mode=="%"?"切換點擊數":"切換百分比";
		var analysis_text = analysis_mode==1?"切換不統計參數":"切換統計參數";
		
	    if($("#history_toolbar").length==0){
		    //數據統計顯示物件
			$("body").append(
				"<div style='width:100%; height: 30px; position: fixed;top:0px; z-index: 50; background-color: rgba(31, 137, 255, 0.7); border-bottom: rgba(31, 137, 255, 0.8) 1px solid; font-weight: bold; color: #fff;' id='history_toolbar'>"+
			        "<div style='width: 1225px; line-height:30px;margin: 0 auto;position: relative;'>"+
			            "<div style='left:0px; position: absolute;'>"+
			            	"<img src='"+site_url+"public/img/template/icon_chart_bar.png' class='history_icon'>"+
			                "總點擊數：<span id='total_click_times'>0</span>&nbsp;"+
			                "[<a id='hide_rect' class='history_btn'>"+rect_text+"</a>]"+
			                "[<a id='hide_qtip' class='history_btn'>"+qtip_text+"</a>]"+
			            "</div>"+
			            "<div style='right:0px; position: absolute;'>"+
			                "<div style='float: left; margin-right: 10px;' id='show_mode' class='history_btn'>"+
			                	"<img src='"+site_url+"public/img/template/icon_arrow_switch.png' class='history_icon'>"+
			                	"<a class='_text'>"+show_text+"</a>"+
			                "</div>"+
			                "<div style='float: left;' id='analysis_mode' class='history_btn'>"+
			                	"<img src='"+site_url+"public/img/template/icon_mail_server_setting.png' class='history_icon'>"+
			                	"<a class='_text'>"+analysis_text+"</a>"+
			                "</div>"+
			            "</div>"+
			        "</div>"+
			    "</div>"
			);
			//點擊區塊隱藏切換
			$("#hide_rect").click(function(){
				hide_rect=hide_rect==0?1:0;
				$(".times_box").toggle();
				init_btn();
			})
			//數據統計隱藏切換
			$("#hide_qtip").click(function(){
				hide_qtip=hide_qtip==0?1:0;
				if(hide_qtip==1)
				{
					$(".qtip").css('opacity',0);
					$(window).scroll(function(){
						$(".qtip").css('opacity',0);
					})
				}
				else
				{
					$(".qtip").css('opacity',1);
					$(window).unbind("scroll")
				}
				
				init_btn();
			})
			//數據統計顯示方式轉換
			$("#show_mode").click(function(){
				show_mode=show_mode=="%"?"數":"%";
				if( chg_show_mode()===false ){
					show_mode=="%"?"數":"%";
					return;
				}
				init_btn();
			})
			//參數分析方式切換
			$("#analysis_mode").click(function(){
				analysis_mode=analysis_mode==1?0:1;
				if( analysis()===false ){
					analysis_mode==1?0:1;
					return;
				}
				init_btn();
			})
	    }
	    //文字顯示
	    $("#hide_rect").html(rect_text);
	    $("#hide_qtip").html(qtip_text);
	    $("#show_mode").find("._text").html(show_text);
		$("#analysis_mode").find("._text").html(analysis_text);
    }
	
	init_btn();
	
	/*----------------------------------------視覺設計⬆︎----------------------------------------*/
	/*----------------------------------------功能設計⬇︎----------------------------------------*/
	//數據分析
	analysis = function(){
		//判斷ＪＳ是否匯入
		var tooltipJS_loaded = $("body").data("toolitpJS_loaded")||false;
		if( !tooltipJS_loaded ){
			alert("JS尚未讀取，請稍待或聯絡管理員");
			return false;
		}
		var control_name = "history_log";
		var func_name = "ajax_get_history";
		var page_now = document.URL;
		if(analysis_mode==0){
			var page_disget = page_now.split("?")[0];
			//判斷是否有ＧＥＴ參數
			if( page_now == page_disget ){
				alert("本頁並沒有GET參數，因此無法使用此功能");
				return false;
			}
			page_now = page_disget;
		}
		$.post(site_url+control_name+"/"+func_name, {
			page_now:page_now+burden_doc
			,analysis_mode:analysis_mode
			},function(data) {
				var error = data.error||0;
				if(error > 0) {
					console.log(data.message+"\n"+data.add.陣列名稱);
				}else{
					var len = 0;
					for(var xpath in data){
						//data.hasOwnProperty(xpath)
						if( $(xpath).is(":hidden") || $(xpath).length==0 )continue;
						len+=data[xpath];
					}
					//物件清除
					$(".times_box").remove()
					$(".qtip").remove()
					//針對所有物件進行建構
					for(var xpath in data){
						//隱藏狀態中不管
						if( $(xpath).is(":hidden") || $(xpath).length==0 )continue;
						var click_times = Number( data[xpath] )|| 0;
						//點擊物件方筐
						$times_box = $("<div class='times_box "+xpath+"'></div>")
						$times_box.css({
							"position":		"absolute"
							,"border":		"1px solid rgb(255, 175, 115)"
							,"background":	"rgba(115, 175, 255, 0.7)"
							,"width":		$(xpath).outerWidth()
							,"height":		$(xpath).outerHeight()
							,"line-height":	$(xpath).height()+"px"
							,"float":		"left"
							,"top":			$(xpath).offset().top-1
							,"left":		$(xpath).offset().left-1
							,"pointer-events":"none"
							,"z-index":		2
						})
						$("body").append($times_box);
						//點擊數換算
						var content = show_mode=="%"?String( Math.floor(click_times*100*10/len)/10)+"%":String( click_times );
						//顯示範圍物件
						var $container = $(".main").length==0?$(window):$(".main");
						//點擊數百分比顯示
						$(xpath).qtip({
							//content: "1",
							content: content
							,position: {
						        my: 'bottom right'
						        ,at: 'top left'
						        ,viewport: $container
						        //,adjust: { method: 'shift' }
							}
							,show: true
							,hide:false
						});
						//點擊數寫入屬性中
						$(xpath).attr("click_times",click_times);
					}
					//視覺建制
					init_btn();
					//顯示物件數
					$("#total_click_times").html(len);
				}
				
			},"json"
		)
	}
	//轉換顯示方式
	chg_show_mode = function(){
		var tooltipJS_loaded = $("body").data("toolitpJS_loaded")||false;
		//判斷ＪＳ是否匯入
		if( !tooltipJS_loaded ){
			alert("JS尚未讀取，請稍待或聯絡管理員");
			return false;
		}
		//顯示方式為百分比
		if(show_mode=="%")
		{
			var len = Number( $("#total_click_times").html() );
			$("*[click_times]").each(function(){
				var click_times = $(this).attr("click_times");
				var content = String( Math.floor(click_times*100*10/len)/10)+"%";
				$(this).qtip('option', 'content.text', content );
			})
		}
		else if(show_mode=="數")//顯示方式為點擊總數
		{
			$("*[click_times]").each(function(){
				$(this).qtip('option', 'content.text', $(this).attr("click_times") );
			})
		}
		
	}
	//ＪＳ匯入
	$.getScript( tooltipJS_src, function(){
		$("body").data("toolitpJS_loaded",true);
		analysis()
	} );
	
})

$(document).ready(function(){
	
	$(".item").click(function() {
		var type_check = $(this).attr("data-select");
		var type_item = $(this).attr("data-item");
		
		//data-item="尺碼" data-no="19" data-val="透明酒紅色"
		
		$(".item").each(function(){
			if(type_item == $(this).attr("data-item")){
				$(this).css({
                                    'border':'#898989 solid 1px', 
                                    'margin':'5px',
                                    '-webkit-border-radius': '2px',
                                    '-moz-border-radius': '2px',
                                    'border-radius': '2px'
                                });
				$(this).attr('data-select','0');
			}
		});
		
		if(type_check == '0') {
			$(this).css({
                            'border':'red solid 1px', 
                            'margin':'5px',
                            '-webkit-border-radius': '2px',
                            '-moz-border-radius': '2px',
                            'border-radius': '2px'
                        });
			$(this).attr('data-select','1');
		}
		
		var type_arr = []; //每種規格選中的項目
		var type_name = [];
		$(".item").each(function(){
			if($(this).attr("data-select") == 1) {
				type_arr.push($(this).attr("data-no"));
				type_name.push($(this).attr("data-val"));
			}
		});
		
		var type_count = []; //每種規格的總數
		var type_style = [];
		$(".type").each(function(){
			type_count.push($(this).attr("data-count"));
			type_style.push($(this).attr("data-type"));
		});
		
		var type_stock = $("#stock").attr("data-stock").split(","); //庫存
		if(type_arr.length == $("#stock").attr("data-type")) {
			var type_str = '';
			for(i=0; i<type_arr.length; i++) {
				type_str += type_arr[i];
				for(j=(i+1); j<type_count.length; j++) {
					type_str += '*'+type_count[j];
				}
				if(i != (type_arr.length-1)) {
					type_str += '+';
				}
			}
			//console.log(type_str);
			var select = eval(type_str);
			$("#stock").html('庫存'+type_stock[select]+'件');
			$("#stock").attr('data-select', select+'；'+type_arr.join('｜')+'；'+type_style.join('｜')+'；'+type_name.join('｜'));
			$("#stock").attr('data-select_stock', type_stock[select]);
		}
		else {
			$("#stock").html('庫存'+$("#stock").attr("data-total")+'件');
			$("#stock").attr('data-select', '');
			$("#stock").attr('data-select_stock', '');
		}
		
		check_quantity();
	});
	
	$("#number").keyup(function(){
		check_quantity();
	});
	
	$("#number").focusout(function(){
		check_quantity();
	});
	
	function check_quantity() {
		if(parseInt($("#stock").attr('data-select_stock')) < parseInt($("#number").val()) || parseInt($("#stock").attr('data-select_stock')) <=	 0 || $("#stock").attr('data-select') == '' || parseInt($("#number").val()) == 0 || isNaN(parseInt($("#number").val())) ) {
			$("#added").attr('data-lock', '1');
                        //$("#added").css({'color':'red'});
		}
		else {
			$("#added").attr('data-lock', '0');
			//$("#added").css({'color':'#fff'});
		}
	}
	
	$("#number_plus").click(function() {
		$("#number").val(parseInt($("#number").val()) + 1);
		check_quantity();
	});
	
	$("#number_minus").click(function() {
		if(parseInt($("#number").val()) < 1) {
			$("#number").val('0');
		}
		else {
			$("#number").val(parseInt($("#number").val()) - 1);
		}
		check_quantity();
	});
	
	$("#added").click(function() {
		if(parseInt($("#stock").attr('data-select_stock')) >= parseInt($("#number").val()) && parseInt($("#stock").attr('data-select_stock')) > 0 && $("#stock").attr('data-select') != '' && parseInt($("#number").val()) != 0 && !isNaN(parseInt($("#number").val())) ) {
			$.post("http://www.crazy2go.com/cart/ajax_added", {fi_no:$(this).attr("data-fi_no"), number:$('#number').val(), select:$("#stock").attr('data-select')}, function(data) {
				if(data.error == 0) {
					
				}
				alert(data.message);
			}, "json");
		}
		else {
			alert('庫存不足');
		}
	});
        
        //賣家商品排行
        $("#order_by_transaction").click(function(){
            $(this).css({
               "border-top":"1px solid #E9E9E9",
               "border-bottom":"0px"
            });
            $("#order_by_addedday").css({
               "border-top":"0px",
               "border-bottom":"1px solid #E9E9E9"
            });
            $("#order_product_list").html("");
            $("#order_product_list").attr("data-page",1);
            $("#order_product_list").attr("data-type","goods_transaction_times");
            var page = $("#order_product_list").attr("data-page");
            var start_index = (page-1)*6;
            var end_index = (page-1)*6 + 6 -1;
            $("#goods_transaction_times > div").each(function(index){
                if(index >= start_index && index <=end_index)
                {
                    $("#order_product_list").append($(this).clone());
                }
            });
            
        }).trigger('click');
        
        $("#order_by_addedday").click(function(){
            $(this).css({
               "border-top":"1px solid #E9E9E9",
               "border-bottom":"0px"
            });
            $("#order_by_transaction").css({
               "border-top":"0px",
               "border-bottom":"1px solid #E9E9E9"
            });
            $("#order_product_list").html("");
            $("#order_product_list").attr("data-page",1);
            $("#order_product_list").attr("data-type","goods_latest");
            var page = $("#order_product_list").attr("data-page");
            var start_index = (page-1)*6;
            var end_index = (page-1)*6 + 6 -1;
            $("#goods_latest > div").each(function(index){
                if(index >= start_index && index <=end_index)
                {
                    $("#order_product_list").append($(this).clone());
                }
            });
        });
	
        $("#order_up").click(function(){
            var page = $("#order_product_list").attr("data-page");
            $("#order_product_list").html("");
            $("#order_product_list").attr("data-page",page == 1?page:--page);
            var start_index = (page-1)*6;
            var end_index = (page-1)*6 + 6 -1;
            $("#"+$("#order_product_list").attr("data-type")+" > div").each(function(index){
                if(index >= start_index && index <=end_index)
                {
                    $("#order_product_list").append($(this).clone());
                }
            });
        });
        
        $("#order_down").click(function(){
            var page = $("#order_product_list").attr("data-page");
            var total = Math.ceil($("#"+$("#order_product_list").attr("data-type")+" > div").size()/6);
            $("#order_product_list").html("");
            $("#order_product_list").attr("data-page",page == total?page:++page);
            var start_index = (page-1)*6;
            var end_index = (page-1)*6 + 6 -1;
            $("#"+$("#order_product_list").attr("data-type")+" > div").each(function(index){
                if(index >= start_index && index <=end_index)
                {
                    $("#order_product_list").append($(this).clone());
                }
            });
        });
        
        //瀏覽紀錄
        var maxRecord = 10;
        var params = location.href.split("?")[1].split("&");
        var fi_no = -1;
        for(i in params)
        {
            params[i] = params[i].split("=");
            if(params[i][0]=="no")
            {
                fi_no = params[i][1];
            }
        }
        if(fi_no!=-1){
            if(!$.cookie("product_navi_record"))
            {
                $.cookie("product_navi_record","");
            }
            var pnr = $.cookie("product_navi_record").split(",");
            if(pnr == "")
            {
                pnr = [];
            }
            var idx = -1;
            if((idx = $.inArray(fi_no,pnr))==-1)
            if(pnr.length == maxRecord)
            {
                pnr.pop();
            }
            if((idx = $.inArray(fi_no,pnr))!=-1)
            {
                pnr.splice(idx,1);
            }
            pnr.unshift(fi_no);
            $.removeCookie("product_navi_record");
            $.cookie("product_navi_record",pnr.join(","));
            $.post("http://www.crazy2go.com/goods/ajax_records",{
                fi_no:$.cookie("product_navi_record")
            },function(data){
                if(data.error == 0) {
                    var len = data.add.records.length;
                    var records_html = "<div id='records_product' style='height:/*500px*/1000px;overflow:hidden;'>";
                    for(var i = 0; i<len;i++)
                    {
                        var image_url = (data.add.records[i].images).substr(0,4) == 'http'?data.add.records[i].images:'http://www.crazy2go.com/public/img/goods/'+$.parseJSON(data.add.records[i].images)[0];
                        records_html += "<div class='records' style='display:inline-block;margin:9px; width:160px;height:235px;position:relative;'><a href='http://www.crazy2go.com/goods?no="+data.add.records[i].fi_no+"'>";
                        records_html += "<div style='cursor:pointer;'><img src='"+image_url+"' style='width:160px; height:160px;'></div>";
                        records_html += "<div style='font-size:12pt;padding:5px;'><span style='font-family:Arial;'>¥</span>"+(data.add.records[i].discount != 0?data.add.records[i].discount:data.add.records[i].promotions)+"</div>";    
                        records_html += "<div style='color:#969B9C;font-size:8pt;text-align:left;' >"+data.add.records[i].name+"</div>";
                        records_html += "</a></div>";
                    }
                    records_html += "</div>";
                    records_html += '<div id="records_down" style="cursor:pointer;padding:10px;"><img src="http://www.crazy2go.com/public/img/template/arrow_left.png" style="-ms-transform:rotate(270deg); -moz-transform:rotate(270deg); -webkit-transform:rotate(270deg); -o-transform:rotate(270deg); transform:rotate(270deg);"></div>';
                    $("#product_navi_record").html(records_html);
                    $("#records_down").click(function(){
                        $("#records_product").prepend($("#records_product .records:last"));
                    });
                }
            },"json");
        }
        
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function get_evaluate(gfi_no, gpage, gevaluate_select, gorderby) {
		$.post("http://www.crazy2go.com/goods/ajax_evaluate", {fi_no:gfi_no, page:gpage, evaluate_select:gevaluate_select, orderby:gorderby}, function(data) {
			if(data.error == 0) {
                                if(!data.add.evaluate[0])
                                {
                                    $('#evaluate').html("無");
                                    return;
                                }
                            
				$("#evaluate").attr('data-check', "1");
				
				var select_all='';
				var select_good='';
				var select_bad='';
				
				if(gevaluate_select == 'good') {
					select_good = 'checked';
				}
				else if(gevaluate_select == 'bad') {
					select_bad = 'checked';
				}
				else {
					select_all = 'checked';
				}
				
                                var goods_rank = $("#goods_rank").text();
                                var rank_bar = $("#rank_bar").clone();
                                rank_bar.find("div:last").remove();
				var evaluate_html = "";
                                evaluate_html += "<img src='http://www.crazy2go.com/public/img/goods/product-opinion_icon.png'>";
                                evaluate_html += "<div class='table' style='display:inline-block;width:820px;margin-left:10px;'><div class='tr'><div class='td'></div></div>";
                                evaluate_html += "<div class='tr'><div class='td'><span style='font-size:22pt;color:#FC6237;vertical-align: text-bottom;'>"+goods_rank+"</span><span style='position:relative;bottom:4px;color:#9FA5A3;'>平均分數</span></div></div>";
                                evaluate_html += "<div class='tr'><div class='td' style='width:830px;'>"+rank_bar.html()+"<span style='display:inline-block;float:right;'>";
                                evaluate_html += '<span class="evaluate_select"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" src1="http://www.crazy2go.com/public/img/goods/check_button.png" src2="http://www.crazy2go.com/public/img/goods/uncheck_button.png" id="evaluate_select_all" name="evaluate_select" value="all" ckd="'+select_all+'"><label for="evaluate_select_all"  style="margin-right:40px;cursor:pointer;" >全部評價 ('+data.add.evaluate_count+')</label></span>';
				evaluate_html += '<span class="evaluate_select"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" src1="http://www.crazy2go.com/public/img/goods/check_button.png" src2="http://www.crazy2go.com/public/img/goods/uncheck_button.png" id="evaluate_select_good" name="evaluate_select" value="good" ckd="'+select_good+'"><label for="evaluate_select_good" style="margin-right:40px;cursor:pointer;" >好評 ('+data.add.evaluate_good+')</label></span>';
				evaluate_html += '<span class="evaluate_select"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" src1="http://www.crazy2go.com/public/img/goods/check_button.png" src2="http://www.crazy2go.com/public/img/goods/uncheck_button.png" id="evaluate_select_bad" name="evaluate_select" value="bad" ckd="'+select_bad+'"><label for="evaluate_select_bad" style="margin-right:40px;cursor:pointer;" >差評 ('+data.add.evaluate_bad+')</label></span>';
                                
				var select_date='';
				var select_evaluate='';
				
				if(gorderby == 'evaluate') {
					select_evaluate = 'selected';
				}
				else {
					select_date = 'selected';
				}
				
                                evaluate_html += "<div style='display:inline-block;position:relative;top:0px;width:110px;height:38px;border:1px solid #D4D4D4;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;'>";
				evaluate_html +=    '<select id="orderby" name="orderby" style="display:inline-block;cursor:pointer;background:transparent;width:110px;height:38px;position:absolute;opacity:0;"><option value="date" '+select_date+'>按時間</option><option value="evaluate" '+select_evaluate+'>按評價</option></select>';
                                evaluate_html +=    '<div id="orderby_text" style="display:inline-block;height:38px;line-height:38px;width:74px;text-align:center;">按時間</div>';
                                evaluate_html +=    '<div style="display:inline-block;border-left:1px solid #D4D4D4;height:38px;width:35px;float:right;text-align:center;"><img src="http://www.crazy2go.com/public/img/goods/arrow_deepgray.png" style="position:relative;top:15px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);"></div>';
				evaluate_html += "</div>";
                                evaluate_html += "</span></div></div>";
                                evaluate_html += "</div>";
                                evaluate_html += "<div style='border-bottom: 2px solid #E9E9E9;margin:20px 0px;'></div>";
                                
                                evaluate_html += "<div style='width:755px;'>";
				for(i=0; i<data.add.evaluate.length; i++) {
                                        var score_html = "<span style='position:relative;top:2px;'>";
                                        for(j=0;j<6;j++)
                                        {
                                            if((j+1) > data.add.evaluate[i].score)
                                            {
                                                score_html += "<img src='http://www.crazy2go.com/public/img/goods/star_gray.png'>";
                                            }
                                            else
                                            {
                                                score_html += "<img src='http://www.crazy2go.com/public/img/goods/star_red.png'>";
                                            }
                                        }
                                        score_html+= "</span>";
                                        
                                        data.add.evaluate[i].evaluate_date = (data.add.evaluate[i].evaluate_date.split(" ")[0]).split("-");
                                        data.add.evaluate[i].evaluate_date = data.add.evaluate[i].evaluate_date[1]+"/"+data.add.evaluate[i].evaluate_date[2]+"/"+data.add.evaluate[i].evaluate_date[0];
                                        
                                        evaluate_html += "<div style='padding:10px;width:705px;border: 1px solid #D2D2D2;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;'>";
					evaluate_html +=    '<div style="display:inline-block;width:100px;text-align:center;"><div style="display:inline-block;margin:10px;width:60px;height:60px;"><img src="http://www.crazy2go.com/public/img/member/'+data.add.evaluate[i].picture+'" style="width:60px;height:60px;-webkit-border-radius: 30px;-moz-border-radius: 30px;border-radius: 30px;"></div><div style="color:#9FA5A3;">'+data.add.evaluate[i].name+'</div></div>';
					evaluate_html +=    '<div style="display:inline-block;width:500px;vertical-align:top;"><div style="color:#9FA5A3;margin-bottom:10px;">商品評價：'+score_html+'</div>';
					evaluate_html +=    '<div>內容：'+data.add.evaluate[i].content+'</div></div>';
					evaluate_html +=    '<div style="display:inline-block;width:100px;text-align:right;"><div style="vertical-align:bottom;color:#9FA5A3;">'+data.add.evaluate[i].evaluate_date+'</div></div>';
                                        evaluate_html += "</div>";
					if(data.add.evaluate[i].respond == 1) {
						//console.log(data.add.respond[data.add.evaluate[i].fi_no].length);
						for(j=0; j<data.add.respond[data.add.evaluate[i].fi_no].length; j++) {
                                                    score_html = "<span style='position:relative;top:2px;'>";
                                                    for(k=0;k<6;k++)
                                                    {
                                                        if((k+1) > data.add.respond[data.add.evaluate[i].fi_no][j].score)
                                                        {
                                                            score_html += "<img src='http://www.crazy2go.com/public/img/goods/star_gray.png'>";
                                                        }
                                                        else
                                                        {
                                                            score_html += "<img src='http://www.crazy2go.com/public/img/goods/star_red.png'>";
                                                        }
                                                    }
                                                    score_html+= "</span>";
                                                    var r_date = data.add.respond[data.add.evaluate[i].fi_no][j].respond_date;
                                                    r_date = (r_date.split(" ")[0]).split("-");
                                                    r_date = r_date[1]+"/"+r_date[2]+"/"+r_date[0];
                                                    evaluate_html += "<div style='background:#FEECE5;padding:10px;margin:20px 0px 0px 50px;width:705px;border: 1px solid #D2D2D2;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;'>";
                                                    evaluate_html +=    '<div style="display:inline-block;width:100px;text-align:center;"><div style="display:inline-block;margin:10px;width:60px;height:60px;"><img src="'+$("#store_image img").attr("src")+'" style="width:60px;height:60px;-webkit-border-radius: 30px;-moz-border-radius: 30px;border-radius: 30px;"></div><div style="color:#9FA5A3;">'+$("#store_name").text()+'</div></div>';
                                                    evaluate_html +=    '<div style="display:inline-block;width:500px;vertical-align:top;"><div style="color:#9FA5A3;margin-bottom:10px;">商品評價：'+score_html+'</div><div>'+data.add.respond[data.add.evaluate[i].fi_no][j].content+'</div></div>';
                                                    evaluate_html +=    '<div style="display:inline-block;width:100px;text-align:right;"><div style="vertical-align:bottom;color:#9FA5A3;">'+r_date+'</div></div>';
                                                    evaluate_html += "</div>";
						}
					}
					evaluate_html += '<div>　</div>';
				}
                                evaluate_html += "</div>";
				$('#evaluate').html(evaluate_html+data.add.page_content);
                                $(".evaluate_select img").each(function(){
                                    var $this = $(this);
                                    $this.css({
                                       "position":"relative",
                                       "top":"2px",
                                       "cursor":"pointer" 
                                    });
                                    $this.attr("ckd")=="checked"?$this.attr("src",$this.attr("src1")):$this.attr("src",$this.attr("src2"));
                                });
                                $("#orderby_text").text($("#orderby option:selected").text());
			}
			//alert(data.message);
		}, "json");
	}
	
	function get_related(news,hots) {
		if(news != '' && hots != '') {
			$.post("http://www.crazy2go.com/goods/ajax_related", {fi_no_news:news,fi_no_hots:hots}, function(data) {
                                if(data.error == 0) {
                                        if(!data.add[0] && !data.add[1])
                                        {
                                            $('#related').html("無");
                                            return;
                                        }
                                    
					$("#related").attr('data-check', "1");
                                        var related_html = '';
					for(i=0; i<data.add.length; i++) {
                                                if(!data.add[i])continue;
                                                related_html += "<div style='border-bottom:2px solid #E9E9E9;color:#A0A4A3;'><span style='position:relative;bottom:8px;'>"+(i==0?"賣家上新":"賣家熱銷")+"</span><div style='display:inline-block;position:relative;left:780px;top:5;'><div class='left_button' style='display:inline-block; width: 25px; height: 30px; cursor: pointer; background: url(http://www.crazy2go.com/public/img/template/arrow_left.png) 50% 50% no-repeat;'></div><span class='page_controller' data-page='1' style='position:relative;bottom:10px;color:black;'>"+(i==0?"1/"+Math.ceil(data.add[i].length/5):"1/"+Math.ceil(data.add[i].length/5))+"</span><div class='right_button' style='display:inline-block; width: 25px; height: 30px; cursor: pointer; background: url(http://www.crazy2go.com/public/img/template/arrow_right.png) 50% 50% no-repeat;'></div></div></div>";
                                                related_html += "<div class='goods_block_pickup'></div>";
                                                related_html += "<div class='goods_block' style='display:none;'>";
                                                for(j=0; j<data.add[i].length; j++) {
                                                    var special_img_url = '';
                                                    if(data.add[i][j].free_shipping==1)special_img_url = 'http://www.crazy2go.com/public/img/goods/icon_freefee.png';
                                                    if(data.add[i][j].transaction_times>=10000)special_img_url = 'http://www.crazy2go.com/public/img/goods/icon_hotsale.png';
                                                    if(data.add[i][j].evaluation_number>=1000 && data.add[i][j].evaluation_score >= 5000)special_img_url = 'http://www.crazy2go.com/public/img/goods/icon_recommand.png';
                                                    var image_url = (data.add[i][j].images).substr(0,4) == 'http'?data.add[i][j].images:'http://www.crazy2go.com/public/img/goods/'+$.parseJSON(data.add[i][j].images)[0];
                                                    related_html += "<div class='goods_info' style='display:inline-block;margin:9px; width:160px;position:relative;'>";  
                                                    related_html += "<div class='special_image' style='display:inline-block;position:absolute;right:0px;'><img src='"+special_img_url+"'></div>";
                                                    related_html += "<div class='goods_image' style='cursor:pointer;'><div class='goods_intro' style='position:absolute;color:white;background:rgba(0,0,0,0.8);padding:10px;bottom:28px;font-size:8pt;' >"+data.add[i][j].name+"</div><img src='"+image_url+"' style='width:160px; height:160px;'></div>";
                                                    related_html += "<div class='goods_price' style='padding-top:10px;text-align:center;color:#E83338;font-size:12pt;'>$"+(data.add[i][j].discount != "0"?data.add[i][j].discount:data.add[i][j].promotions)+"</div>";
                                                    related_html += "</div>";
                                                }
                                                related_html += '</div>';
					}
                                        $('#related').html(related_html);
                                        $(".goods_block").each(function(){
                                            var $this = $(this);
                                            $this.find(".goods_image .goods_intro").hide();
                                            $this.find(".goods_image").hover(function(){
                                                $(this).find(".goods_intro").show();
                                            },function(){
                                                $(this).find(".goods_intro").hide();
                                            })
                                        });
                                        $('.page_controller').each(function(){
                                           var $this = $(this);
                                           $this.parent().find(".left_button").click(function(){
                                               var page = parseInt($this.attr("data-page"));
                                               var total_page = parseInt($this.html().split("/")[1]);
                                               $this.attr("data-page",page == 1?1:--page);
                                               $this.html(page+"/"+total_page);
                                               var pickup = $this.parent().parent().next();
                                               var content = $this.parent().parent().next().next();
                                               pickup.html("");
                                               var start_index = (page-1)*5;
                                               var end_index = start_index+5-1;
                                               content.find(".goods_info").each(function(index){
                                                   if(index >= start_index && index <=end_index)
                                                   {
                                                       pickup.append($(this).clone(true));
                                                   }
                                               });
                                           });
                                           $this.parent().find(".left_button").trigger("click");
                                           $this.parent().find(".right_button").click(function(){
                                               var page = parseInt($this.attr("data-page"));
                                               var total_page = parseInt($this.html().split("/")[1]);
                                               $this.attr("data-page",page == total_page?total_page:++page);
                                               $this.html(page+"/"+total_page);
                                               var pickup = $this.parent().parent().next();
                                               var content = $this.parent().parent().next().next();
                                               pickup.html("");
                                               var start_index = (page-1)*5;
                                               var end_index = start_index+5-1;
                                               content.find(".goods_info").each(function(index){
                                                   if(index >= start_index && index <=end_index)
                                                   {
                                                       pickup.append($(this).clone(true));
                                                   }
                                               });
                                           });
                                        });
				}
				//alert(data.message);
			}, "json");
		}
	}
	
	$(".goods_button").click(function() {
		var info = $(this).attr('data-info');
		$(".goods_button").css({'background-color':'#FFFFFF'});
		$(this).css({'background-color':'#F3F3F3'});
		$(".goods_context").css({'display':'none'});
		$("#"+info).css({'display':'block'});
		console.log($(this).attr('data-info'));
		if($(this).attr('data-info') == 'evaluate' && $("#"+info).attr('data-check') == 0) {
			get_evaluate($("#"+info).attr('data-fi_no'), 1, '', '');
		}
		
		if($(this).attr('data-info') == 'related' && $("#"+info).attr('data-check') == 0) {
			get_related($("#"+info).attr('data-fi_no_news'),$("#"+info).attr('data-fi_no_hots'));
		}
	});
	
	$(document).on("click", ".page", function(event) {
                var value="";
                $(".evaluate_select img").each(function(){
                    if($(this).attr("ckd")=="checked")
                    {
                        value = $(this).attr("value");
                    }
                });
		get_evaluate($("#evaluate").attr('data-fi_no'), $(this).attr("data-page"), value, $("#orderby option:selected").val());
	});
	
	$(document).on("click", ".evaluate_select", function(event) {
                console.log($(this).find("img").attr("value"));
		get_evaluate($("#evaluate").attr('data-fi_no'), 1, $(this).find("img").attr("value"), $("#orderby option:selected").val());
	});
	
	$(document).on("change", "#orderby", function(event) {
                var value="";
                $(".evaluate_select img").each(function(){
                    if($(this).attr("ckd")=="checked")
                    {
                        value = $(this).attr("value");
                    }
                });
		get_evaluate($("#evaluate").attr('data-fi_no'), 1, value, $("#orderby option:selected").val());
	});
        
        $(".product_images").each(function(){
            $(this).click(function(){
                $("#product_big_image").attr("src",$(this).attr("src").replace(/minimize/,"goods"));
            });
        });
        
        $("#add_goods_collect").click(function(){
            var $this = $(this);
            $.post("http://www.crazy2go.com/cart/ajax_collect_goods", {fi_no:$this.attr("data-fi_no")}, function(data) {
                alert(data.message);
            }, "json");
        });
        
        $("#add_store_collect").click(function(){
            var $this = $(this);
            $.post("http://www.crazy2go.com/goods/ajax_collect_store", {fi_no:$this.attr("data-fi_no")}, function(data) {
                alert(data.message);
            }, "json");
        });
        
        
        //立刻購買功能
        $("#nowed").click(function() {
            if (parseInt($("#stock").attr('data-select_stock')) >= parseInt($("#number").val()) && parseInt($("#stock").attr('data-select_stock')) > 0 && $("#stock").attr('data-select') != '' && parseInt($("#number").val()) != 0 && !isNaN(parseInt($("#number").val())) ) {
                $.post("http://www.crazy2go.com/cart/ajax_nowed", {
                    fi_no: $(this).attr("data-fi_no"),
                    number: $('#number').val(),
                    select: $("#stock").attr('data-select')
                }, function(data) {
                    if (data.error == 0) {
                            location.href = 'http://www.crazy2go.com/cart/checkout/';
                    }
                    else {
                            alert(data.message);
                    }
                }, "json");
            } else {
                alert('庫存不足');
            }
        });
})
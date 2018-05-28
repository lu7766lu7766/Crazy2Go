$(document).ready(function(){
	
	/*
	$("body").mousemove(function(event) {
		console.log(event.target);
	});
	window.onunload = function unLoad(e) {
		//console.log('exit');
		$.ajax({
			async: false,
			type: "POST",
			url: "http://www.crazy2go.com/search/ajax_append",
			data: { keyword: "usb", discount: "0", attr: "|" }
		})
		.done(function( msg ) {
			console.log( msg );
		});
	}
	*/
	
	$(document).on("click", "body", function(event) {
		if($(event.target).attr("data-val") != undefined) {
			var type = $(event.target).attr("data-val");
			var offset = $(event.target).offset();
			var height = $(event.target).outerHeight();
			var width = $(event.target).outerWidth();
			$("#type_edit").css({'display':'block'});
			var type_arr = type.split("｜")
			var type_html = '';
			var type_select = $(event.target).attr("data-item");
			$("#type_edit").attr("data-type", $(event.target).attr("data-type"));
			$("#type_edit").attr("data-fi_no", $(event.target).attr("data-fi_no"));
			$("#type_edit").attr("data-select", type_select);
			$("#type_edit").attr("data-check", type_select);
			$("#type_edit").attr("data-index", $(event.target).attr("data-index"));
			var div_clase = 0;
			for(i=0; i<type_arr.length; i++) {
				if(i.toString().substr(-1,1) == '0') {
					type_html += '<div style="float:left;">';
					div_clase = 1;
				}
				
				if(type_select == type_arr[i]) {
					type_html += '<div class="type_select" data-select="'+type_arr[i]+'" style="margin-right:17px;"><img class="type_images" src="http://www.crazy2go.com/public/img/template/check2.png" style="position:relative; top:2px; margin-right:7px;">'+type_arr[i]+'</div>';
				}
				else {
					type_html += '<div class="type_select" data-select="'+type_arr[i]+'" style="margin-right:17px;"><img class="type_images" src="http://www.crazy2go.com/public/img/template/check.png" style="position:relative; top:2px; margin-right:7px;">'+type_arr[i]+'</div>';
				}
				
				if(i.toString().substr(-1,1) == '9') {
					type_html += '</div>';
					div_clase = 0;
				}
			}
			if(div_clase == 1) {
				type_html += '</div>';
			}
			//type_html += '<div style= "clear: both;"></div><div id="type_change" style="background-color:gray; color:#fff; margin-right: 17px;">修改</div>';
			$("#type_edit").html(type_html);
			var edit_width = $("#type_edit").outerWidth();
			console.log(edit_width);
			$("#type_edit").offset({top:offset.top+height+18, left:offset.left+(width/2)-(edit_width/2)});
			$("#type_edit").append('<img src="http://www.crazy2go.com/public/img/template/arrow4.png" style="position:absolute; top:-11px; left:'+(edit_width/2-10)+'px;">');
		}
		else if(event.target.className == 'type_select' || event.target.className == 'type_change' || event.target.className == 'type_images') {
			//
		}
		else {
			$("#type_edit").css({'display':'none'});
			$("#type_edit").html('');
		}
	});
	
	$(document).on("click", ".type_select", function(event) {
		console.log($(this).attr("data-select"));
		var select = $(this).attr("data-select");
		$(".type_select").each(function(){
			if($(this).attr("data-select") == select) {
				$(this).html('<img src="http://www.crazy2go.com/public/img/template/check2.png" style="position:relative; top:2px; margin-right:7px;">'+$(this).attr("data-select"));
				$("#type_edit").attr("data-select", $(this).attr("data-select"));
			}
			else {
				$(this).html('<img src="http://www.crazy2go.com/public/img/template/check.png" style="position:relative; top:2px; margin-right:7px;">'+$(this).attr("data-select"));
			}
		});
		auto_edit.set({time:500, autostart:true});
	});
	
	var auto_edit = $.timer(function() {
	//$(document).on("click", "#type_change", function(event) {
		if($("#type_edit").attr("data-check") != $("#type_edit").attr("data-select")) {
			var type_arr = []; //每種規格選中的項目
			var type_count = []; //每種規格的總數
			var type_log; //被選中要修改的編號
			$(".type_change").each(function() {
				if($(this).attr("data-fi_no") == $("#type_edit").attr("data-fi_no")) {
					type_count.push($(this).attr("data-count"));
					
					var select_data_val = $(this).attr("data-val").split("｜");
					for(i=0; i<select_data_val.length; i++) {
						if($(this).attr("data-type") == $("#type_edit").attr("data-type")) { //被選中要修改的項目
							if(select_data_val[i] == $("#type_edit").attr("data-select")) {
								type_arr.push(i);
								type_log = i;
							}
						}
						else {
							if(select_data_val[i] == $(this).attr("data-item")) {
								type_arr.push(i);
							}
						}
					}
				}
			});
			
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
			
			var select = $("#number"+$("#type_edit").attr("data-fi_no")).attr("data-select");
			var select_arr = select.split("；");
			var select_arr_no = select_arr[1].split("｜");
			var select_arr_type = select_arr[2].split("｜");
			var select_arr_item = select_arr[3].split("｜");
			
			for(i=0; i<select_arr_type.length; i++) {
				if(select_arr_type[i] == $("#type_edit").attr("data-type")) {
					select_arr_no[i] = type_log;
					select_arr_item[i] = $("#type_edit").attr("data-select");
				}
			}
			var stock_num = eval(type_str);
			
			var new_select = stock_num+'；'+select_arr_no.join('｜')+'；'+select_arr[2]+'；'+select_arr_item.join('｜');
			console.log(new_select);
			
			//--
			
			repeat_check = 0;
			$(".number").each(function(){
				if($(this).attr("data-select") == new_select) {
					repeat_check++;
				}
			});
			if(repeat_check == 0) {
				var select_stock = $("#number"+$("#type_edit").attr("data-fi_no")).attr("data-stock");
				var select_stock_arr = select_stock.split(",");
				if(parseInt(select_stock_arr[stock_num]) >= parseInt($("#number"+$("#type_edit").attr("data-fi_no")).val())) {
					
					var select_no = $("#type_edit").attr("data-fi_no").split("_");
					
					$.post("http://www.crazy2go.com/cart/ajax_change", {fi_no:select_no[0], number:$('#number'+$("#type_edit").attr("data-fi_no")).val(), select:$('#number'+$("#type_edit").attr("data-fi_no")).attr('data-select'), change:new_select}, function(data) {
						if(data.error == 0) {
							$("#number"+$("#type_edit").attr("data-fi_no")).attr("data-select", new_select);
							
							
							$(".type_change").each(function(){
								
								if( $(this).attr("data-fi_no") == $("#type_edit").attr("data-fi_no") && $(this).attr("data-type") == $("#type_edit").attr("data-type") ) {
									
									$(this).attr("data-item", $("#type_edit").attr("data-select"));
									
									var id_fino = $(this).attr("data-fi_no").split("_");
									
									$('#'+id_fino[0]+"_"+$(this).attr("data-index")).html($("#type_edit").attr("data-select"));
								}
								
							});
							
							
							$("#type"+$("#type_edit").attr("data-fi_no")).attr("data-item", $("#type_edit").attr("data-select"));
							
							var shopping_cart = $("#shopping_cart").html();
							
							var select_no = $("#type_edit").attr("data-fi_no").split("_");
							
							shopping_cart = replaceAll(shopping_cart, 'tr'+select_no[0]+'_'+select_no[1], 'tr'+select_no[0]+'_'+stock_num);
							shopping_cart = replaceAll(shopping_cart, 'single_price'+select_no[0]+'_'+select_no[1], 'single_price'+select_no[0]+'_'+stock_num);
							shopping_cart = replaceAll(shopping_cart, 'number'+select_no[0]+'_'+select_no[1], 'number'+select_no[0]+'_'+stock_num);
							shopping_cart = replaceAll(shopping_cart, 'message'+select_no[0]+'_'+select_no[1], 'message'+select_no[0]+'_'+stock_num);
							shopping_cart = replaceAll(shopping_cart, 'choose'+select_no[0]+'_'+select_no[1], 'choose'+select_no[0]+'_'+stock_num);
							shopping_cart = replaceAll(shopping_cart, 'value="'+select_no[0]+'_'+select_no[1]+'"', 'value="'+select_no[0]+'_'+stock_num+'"');
							shopping_cart = replaceAll(shopping_cart, 'data-fi_no="'+select_no[0]+'_'+select_no[1]+'"', 'data-fi_no="'+select_no[0]+'_'+stock_num+'"');
							
							$("#shopping_cart").html(shopping_cart);
						}
						alert(data.message);
					}, "json");
				}
				else {
					alert('庫存不足');
				}
			}
			else {
				alert('項目重複');
			}
			
		}
		auto_edit.stop();
		$("#type_edit").css({'display':'none'});
		$("#type_edit").html('');
	});
	
	
	
	
	
	
	
	
	
	var cart_number = {}; //new Object;
	
	$(document).on("click", ".number_plus", function(event) {
		$("#number"+$(this).attr("data-fi_no")).val( parseInt($("#number"+$(this).attr("data-fi_no")).val())+1 );
		number_edit($(this).attr("data-fi_no"));
	});
	
	$(document).on("click", ".number_minus", function(event) {
		if(parseInt($("#number"+$(this).attr("data-fi_no")).val()) < 1) {
			$("#number"+$(this).attr("data-fi_no")).val('0');
		}
		else {
			$("#number"+$(this).attr("data-fi_no")).val( parseInt($("#number"+$(this).attr("data-fi_no")).val())-1 );
		}
		number_edit($(this).attr("data-fi_no"));
	});
	
	$(document).on("focusin", ".number", function(event) {
		time_cart_number.stop();
	});
	
	$(document).on("focusout", ".number", function(event) {
		if(parseInt($("#number"+$(this).attr("data-fi_no")).val()) < 1) {
			$("#number"+$(this).attr("data-fi_no")).val('0');
		}
		number_edit($(this).attr("data-fi_no"));
	});
	
	function number_edit(fi_no) {
		if($("#number"+fi_no).val() != $("#number"+fi_no).attr('data-log')) {
			cart_number[fi_no] = $("#number"+fi_no).val();
		}
		if(cart_number.length != 0) {
			time_cart_check = 3;
			time_cart_number.set({time:300, autostart:true});
		}
	}
	
	var time_cart_check = 0;
	
	var time_cart_number = $.timer(function() {
		if(time_cart_check == 0) {
			var str_fi_no = '';
			var str_number = '';
			var str_select = '';
			
			for(var key in cart_number){ 
				str_fi_no += key+"∵";
				str_number += cart_number[key]+"∵";
				str_select += $('#number'+key).attr('data-select')+"∵";
			}
			/*
			console.log(str_fi_no);		//4｜2｜1｜
			console.log(str_number);	//10｜2｜3｜
			console.log(str_select);	//119；8｜7；顏色｜尺碼；深灰色｜160/84(xs)｜101；7｜3；顏色｜尺碼；深卡其布色｜170/88(m)｜2；0｜2；顏色｜尺碼；軍綠色｜165/84(s)｜
			*/
			
			$.post("http://www.crazy2go.com/cart/ajax_editor", {fi_no:str_fi_no, number:str_number, select:str_select}, function(data) {
				//console.log(data);
				
				for(var key in data.add){ 
					if(data.add[key] == 0) {
						$("#message"+key).html("");
						$("#number"+key).attr('data-log', $("#number"+key).val());
					}
					else if(data.add[key] == 1) {
						$("#message"+key).html("庫存不足");
						$("#number"+key).attr('data-log', $("#number"+key).val());
					}
					else if(data.add[key] == 2) {
						tr_remove(key);
					}
				}
				alert(data.message);
				
				statistics();
				all_select();
				
			}, "json");
			
			for(var i in cart_number) {
				delete cart_number[i];
			}
			
			//statistics();
			//all_select();
			
    		time_cart_number.stop();
    	}
    	else {
	    	time_cart_check--;
    	}
    	//console.log(time_cart_check);
	});
	
	function tr_remove(key) {
		if($('#store'+key).html().trim() != '') {
			var store_no = $('#store'+key).attr('data-store');
			var store_html = $('#store'+key).html();
			$('#tr'+key).remove();
			
			var store_check = 0;
			$(".store").each(function() {
				if($(this).attr('data-store') == store_no && store_check == 0) {
					$(this).html(store_html);
					store_check++;
				}
			});
		}
		else {
			$('#tr'+key).remove();
		}
		statistics();
		all_select();
	}
	
	function statistics() {
		var cart_number = 0;
		var cart_total = 0;
		var check_message = 0;
		$('#select_box').html('');
		//$(".choose:checked").each(function() {
		$(".choose").each(function() {
			var fi_no = $(this).val();
			if($(this).prop("checked")) {
				var number = $('#number'+fi_no).val();
				var single_price = $('#single_price'+fi_no).attr('data-single_price');
				cart_number += parseInt(number);
				cart_total += parseInt(number)*parseInt(single_price);
								
				var this_width = $('#tr'+fi_no).outerWidth();
				var this_height = $('#tr'+fi_no).outerHeight();
				var this_offset = $('#tr'+fi_no).offset();
				
				if($('#message'+fi_no).html() != "") {
					check_message++;
				}
				
				$('#select_box').append('<div id="select_box_'+fi_no+'" style="width:'+this_width+'px; height:'+(this_height-1)+'px; border-top:1px #B7B7B7 solid; border-bottom:1px #B7B7B7 solid; pointer-events:none;"></div>');
				$('#select_box_'+fi_no).offset({top:this_offset.top, left:this_offset.left});
				
				if($('#tr'+fi_no).attr('data-back_color') == '' || $('#tr'+fi_no).attr('data-back_color') == undefined) {
					$('#tr'+fi_no).attr('data-back_color', $('#tr'+fi_no).css('background-color'));
					$('#tr'+fi_no).css({'background-color':'#FCE6D8'});
				}
			}
			else {
				if($('#tr'+fi_no).attr('data-back_color') != '' && $('#tr'+fi_no).attr('data-back_color') != undefined) {
					$('#tr'+fi_no).css({'background-color':$('#tr'+fi_no).attr('data-back_color')});
					$('#tr'+fi_no).attr('data-back_color', '');
				}
			}
		});
		
		//var cart_renminbi = cart_total / $("#cart_rate").attr('data-rate');
		
		$("#cart_number").html(cart_number);
		$("#cart_total").html('<span style="font-family:Arial;">¥</span>'+cart_total);
		//$("#cart_renminbi").html('¥'+cart_renminbi.toFixed(1));
		
		
		console.log(cart_number+"/"+check_message);
		if(cart_number > 0 && check_message == 0) {
			$('#onsubmit').attr('disabled', false);
		}
		else {
			$('#onsubmit').attr('disabled', true);
		}
	}
	
	$(document).on("click", ".storese", function(event) {
		var cart_store = $(this).val();
		if($(this).prop("checked")) {
			$(".choose"+cart_store).prop("checked", true);
		}
		else {
			$(".choose"+cart_store).prop("checked", false);
		}
		
		statistics();
		all_select();
	});
	
	$(document).on("click", ".choose", function(event) {
		var fi_no = $(this).val();
		var store = $("#store"+fi_no).attr('data-store');
		
		var select_number = 0;
		var select_count = 0;
		$(".choose"+store).each(function() {
			if($(this).prop("checked")) {
				select_number++;
			}
			select_count++;
		});
		
		if(select_number == select_count) {
			$("#storese"+store).prop("checked", true);
		}
		else {
			$("#storese"+store).prop("checked", false);
		}
		
		statistics();
		all_select();
	});
	
	function all_select() {
		var select_number = 0;
		var select_count = 0;

		$(".storese").each(function() {
			if($(this).prop("checked")) {
				select_number++;
			}
			select_count++;
		});
		
		if(select_number == select_count && select_count != 0) {
			$("#batch_select").prop("checked", true);
		}
		else {
			$("#batch_select").prop("checked", false);
		}
	}
	
	$(document).on("click", ".delete", function(event) {
		var cart_fi_no = $(this).attr("data-fi_no");
		cart_fi_no_split = cart_fi_no.split("_");
		$.post("http://www.crazy2go.com/cart/ajax_delete", {fi_no:cart_fi_no_split[0], select:$('#number'+cart_fi_no).attr('data-select')}, function(data) {
			if(data.error == 0) {
				tr_remove(cart_fi_no);
			}
			alert(data.message);
		}, "json");
	});
	
	$(document).on("click", "#batch_delete", function(event) {
		var all_cart_fi_no = [];
		var all_fi_no = [];
		var all_select = [];
		var all_num = 0;
		$(".choose:checked").each(function() {
			var cart_fi_no = $(this).val();
			all_cart_fi_no.push(cart_fi_no);
			cart_fi_no_split = cart_fi_no.split("_");
			all_fi_no.push(cart_fi_no_split[0]);
			all_select.push($('#number'+cart_fi_no).attr('data-select'));
			all_num++;
		});
		
		if(all_num > 0) {
			$.post("http://www.crazy2go.com/cart/ajax_batch_delete", {fi_no:all_fi_no.join("∵"), select:all_select.join("∵")}, function(data) {
				if(data.error == 0) {
					for(i=0; i<all_cart_fi_no.length; i++) {
						tr_remove(all_cart_fi_no[i]);
					}
				}
				alert(data.message);
			}, "json");
		}
		else {
			alert('請至少選擇一項商品');
		}
	});

	$(document).on("click", "#batch_select", function(event) {
		if($(this).prop("checked")) {
			$(".storese").prop("checked", true);
			$(".choose").prop("checked", true);
		}
		else {
			$(".storese").prop("checked", false);
			$(".choose").prop("checked", false);
		}
		statistics();
		all_select();
	});
	
	$(document).on("click", ".collect", function(event) {
		var cart_fi_no = $(this).attr("data-fi_no");
		cart_fi_no_split = cart_fi_no.split("_");
		$.post("http://www.crazy2go.com/cart/ajax_collect", {fi_no:cart_fi_no_split[0], select:$('#number'+cart_fi_no).attr('data-select')}, function(data) {
			if(data.error == 0) {
				tr_remove(cart_fi_no);
			}
			alert(data.message);
		}, "json");

	});
	
	$(document).on("click", "#batch_collect", function(event) {
		var all_cart_fi_no = [];
		var all_fi_no = [];
		var all_select = [];
		var all_num = 0;
		$(".choose:checked").each(function() {
			var cart_fi_no = $(this).val();
			all_cart_fi_no.push(cart_fi_no);
			cart_fi_no_split = cart_fi_no.split("_");
			all_fi_no.push(cart_fi_no_split[0]);
			all_select.push($('#number'+cart_fi_no).attr('data-select'));
			all_num++;
		});
		
		if(all_num > 0) {
			$.post("http://www.crazy2go.com/cart/ajax_batch_collect", {fi_no:all_fi_no.join("∵"), select:all_select.join("∵")}, function(data) {
				if(data.error == 0) {
					for(i=0; i<all_cart_fi_no.length; i++) {
						tr_remove(all_cart_fi_no[i]);
					}
				}
				alert(data.message);
			}, "json");
		}
		else {
			alert('請至少選擇一項商品');
		}

	});
	
	var aurl = location.href;
	if(aurl.match('checkout')!=null) {
		$.post("http://www.crazy2go.com/cart/ajax_postal_province", {fi_no:'1'}, function(data) {
			for(i=0; i<data.length; i++) {
				if($('#aprovince').val() == data[i]['fi_no']) {
					$("#province").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']).prop('selected', true));
				}
				else {
					$("#province").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
				}
			}
			shippingFee();
		}, "json");
		
		common_get(0);
	}
	
	function common_get(i) {
		if(i==1) {
			$('#province').val($('#aprovince').val());
			$('#nprovince').html($('#province option[value='+$('#aprovince').val()+']').text());
			shippingFee();
		}
		if($('#acity').val()!='' && $('#acity').val()!=null && $('#aprovince').val()!='' && $('#aprovince').val()!=null) {
			$.post("http://www.crazy2go.com/cart/ajax_postal_province", {fi_no:$('#aprovince').val()}, function(data) {
				for(i=0; i<data.length; i++) {
					if($('#acity').val() == data[i]['fi_no']) {
						$("#city").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']).prop('selected', true));
					}
					else {
						$("#city").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
					}
				}
			}, "json");
		}
		if($('#adistrict').val()!='' && $('#adistrict').val()!=null && $('#acity').val()!='' && $('#acity').val()!=null) {
			$.post("http://www.crazy2go.com/cart/ajax_postal_province", {fi_no:$('#acity').val()}, function(data) {
				for(i=0; i<data.length; i++) {
					if($('#adistrict').val() == data[i]['fi_no']) {
						$("#district").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']).prop('selected', true));
					}
					else {
						$("#district").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
					}
				}
			}, "json");
		}
		if($('#adistrict').val()!='' && $('#adistrict').val()!=null) {
			$.post("http://www.crazy2go.com/cart/ajax_postal_street", {fi_no:$('#adistrict').val()}, function(data) {
				if(data.length > 0) {
					$("#street").css({'display':''});
					for(i=0; i<data.length; i++) {
						if($('#astreet').val() == data[i]['fi_no']) {
							$("#street").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']).prop('selected', true));
						}
						else {
							$("#street").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
						}
					}
				}
				else {
					$("#street").css({'display':'none'});
				}
			}, "json");
		}
	}
	
	$("#common").change(function() {
		$('#ncommon').html($('#common option:selected').text());
		var common_all = $('option:selected', this).attr('data-common').split("；");
		console.log(common_all);
		$("#aprovince").val(common_all[2]);
		$("#acity").val(common_all[3]);
		$("#adistrict").val(common_all[4]);
		$("#astreet").val(common_all[5]);
		
		if(common_all[6] != '' && common_all[6] != null) {$("#nrovince").html(common_all[6]);}
		else {$("#nrovince").html('省份');}
		
		if(common_all[7] != '' && common_all[7] != null) {$("#ncity").html(common_all[7]);}
		else {$("#nrovince").html('城市');}
		
		if(common_all[8] != '' && common_all[8] != null) {$("#ndistrict").html(common_all[8]);}
		else {$("#nrovince").html('縣區');}
		
		if(common_all[9] != '' && common_all[9] != null) {$("#nstreet").html(common_all[9]);}
		else {$("#nrovince").html('街道');}
		
		$("#address").val(common_all[10]);
		
		$("#postal_code").val(common_all[1]);
		$("#consignee").val(common_all[0]);
		
		var mobile = common_all[12].split("-");
		$("#contact_mobile_international").val(mobile[0]);
		$("#contact_mobile_number").val(mobile[1]);
		
		var phone = common_all[11].split("-");
		$("#contact_phone_international").val(phone[0]);
		$("#contact_phone_area").val(phone[1]);
		$("#contact_phone_number").val(phone[2]);
		
		common_get(1);
	});
	
	$("#province").change(function() {
		$("#city option").remove();
		$("#district option").remove();
		$("#street option").remove();
		$("#city").append($("<option></option>").attr("value", '').text('城市'));
		$("#district").append($("<option></option>").attr("value", '').text('縣區'));
		$("#street").append($("<option></option>").attr("value", '').text('街道'));
		$.post("http://www.crazy2go.com/cart/ajax_postal_province", {fi_no:$(this).val()}, function(data) {
			for(i=0; i<data.length; i++) {
				$("#city").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
			}
		}, "json");
		if($("#province option:selected").text() != '省份') {
			$("#aprovince").val($("#province option:selected").val());
			$("#nprovince").html($("#province option:selected").text());
		}
		else {
			$("#aprovince").val('');
			$("#nprovince").html('省份');
		}
		$("#acity").val('');
		$("#adistrict").val('');
		$("#astreet").val('');
		$("#ncity").html('城市');
		$("#ndistrict").html('縣區');
		$("#nstreet").html('街道');
		$("#street").css({'display':''});
		shippingFee();
	});
	
	$("#city").change(function() {
		$("#district option").remove();
		$("#street option").remove();
		$("#district").append($("<option></option>").attr("value", '').text('縣區'));
		$("#street").append($("<option></option>").attr("value", '').text('街道'));
		$.post("http://www.crazy2go.com/cart/ajax_postal_province", {fi_no:$(this).val()}, function(data) {
			for(i=0; i<data.length; i++) {
				$("#district").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
			}
		}, "json");
		if($("#city option:selected").text() != '城市') {
			$("#acity").val($("#city option:selected").val());
			$("#ncity").html($("#city option:selected").text());
		}
		else {
			$("#acity").val('');
			$("#ncity").html('城市');
		}
		$("#adistrict").val('');
		$("#astreet").val('');
		$("#ndistrict").html('縣區');
		$("#nstreet").html('街道');
		$("#street").css({'display':''});
	});
	
	$("#district").change(function() {
		$("#street option").remove();
		$("#street").append($("<option></option>").attr("value", '').text('街道'));
		$.post("http://www.crazy2go.com/cart/ajax_postal_street", {fi_no:$(this).val()}, function(data) {
			if(data.length > 0) {
				$("#street").css({'display':''});
				for(i=0; i<data.length; i++) {
					$("#street").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
				}
			}
			else {
				$("#street").css({'display':'none'});
			}
		}, "json");
		if($("#district option:selected").text() != '縣區') {
			$("#adistrict").val($("#district option:selected").val());
			$("#ndistrict").html($("#district option:selected").text());
		}
		else {
			$("#adistrict").val('');
			$("#ndistrict").html('縣區');
		}
		$("#astreet").val('');
		$("#nstreet").html('街道');
	});
	
	$("#street").change(function() {
		if($("#street option:selected").text() != '街道') {
			$("#astreet").val($("#street option:selected").val());
			$("#nstreet").html($("#street option:selected").text());
		}
		else {
			$("#astreet").val('');
			$("#nstreet").html('街道');
		}
	});
	
	/*
	$.validator.addMethod('checkname', function (value, element, param) {
	    if() {
		    return true; //驗證通過		    
	    }
	    else {
		    return false;
	    }
	}, '請輸入');
	*/
	
	$("#order").validate({
		rules: {
			'choose[]': {required: true}
		},
		messages: {
			'choose[]': {required: "請至少選擇一個商品"}
		},
		submitHandler: function() {
			var pchoose = "";
			$(".choose:checked").each(function() {
				pchoose += $(this).val().replace('_', '×')+"∴";
			});
			$.post("http://www.crazy2go.com/cart/ajax_submit_order", {choose:pchoose}, function(data) {
				if(data.error == 0) {
					location.href = 'http://www.crazy2go.com/cart/checkout/';
				}
				//alert(data.message);
			}, "json");
		}
	});
	
	$(document).on("click", "#save_address", function(event) {
		if($('#asave_address').val() == 1) {
			$('#asave_address').val(0);
			$('#isave_address').attr('src', 'http://www.crazy2go.com/public/img/goods/uncheck_button.png');
		}
		else {
			$('#asave_address').val(1);
			$('#isave_address').attr('src', 'http://www.crazy2go.com/public/img/goods/check_button.png');
		}
	});
	
	$(document).on("click", ".shipping_select", function(event) {
		$('.ishipping_select').attr('src', 'http://www.crazy2go.com/public/img/goods/uncheck_button.png');
		var shipping_select = $(this).attr('data-shipping');
		$('#ashipping_select').val(shipping_select);
		$(".ishipping_select").each(function() {
			if($(this).attr('data-shipping') == shipping_select) {
				$(this).attr('src', 'http://www.crazy2go.com/public/img/goods/check_button.png');
			}
		});
		shippingFee();
	});
	
	$(document).on("change", "#aidentity_front", function(evt) {
		var files = evt.target.files;
		
	    for(var i=0, f; f=files[i]; i++) {
	        if(!f.type.match('image.*')) {
	            continue;
	        }
	
	        var reader = new FileReader();
	        reader.onload = (function(theFile) {
	            return function(e) {
	                $("#nidentity_front").attr('src', e.target.result);
	            };
	        })(f);
	
	        reader.readAsDataURL(f);
	    }
	});
	
	$(document).on("change", "#aidentity_back", function(evt) {
		var files = evt.target.files;
		
	    for(var i=0, f; f=files[i]; i++) {
	        if(!f.type.match('image.*')) {
	            continue;
	        }
	
	        var reader = new FileReader();
	        reader.onload = (function(theFile) {
	            return function(e) {
	                $("#nidentity_back").attr('src', e.target.result);
	            };
	        })(f);
	
	        reader.readAsDataURL(f);
	    }
	});
		
	$("#contact_mobile_number").focusout(function() {
		$("#checkout").validate().element("#contact_mobile_international");
	});
	
	$("#contact_phone_number").focusout(function() {
		$("#checkout").validate().element("#contact_phone_international");
		$("#checkout").validate().element("#contact_phone_area");
	});
	
	$("#checkout").validate({
		rules: {
			province: {required: true},
			city: {required: true},
			district: {required: true},
			address: {
				required: true,
				rangelength: [5,150]
			},
			postal_code: {
				required: true,
				number: true,
				rangelength: [6,6]
			},
			consignee: {
				required: true,
				rangelength: [2,10]
			},
			contact_mobile_international: {
				required: function() {
					if($("#contact_mobile_number").val().length > 0) {
						return true;
					}
					else {
						return false;
					}
				},
				number: true,
				rangelength: [1,3]
			},
			contact_mobile_number: {
				require_from_group: [1, '.group_number'],
				number: true,
				rangelength: [11,11]
			},
			contact_phone_international: {
				required: function() {
					if($("#contact_phone_number").val().length > 0) {
						return true;
					}
					else {
						return false;
					}
				},
				number: true,
				rangelength: [1,3]
			},
			contact_phone_area: {
				required: function() {
					if($("#contact_phone_number").val().length > 0) {
						return true;
					}
					else {
						return false;
					}
				},
				number: true,
				rangelength: [2,3]
			},
			contact_phone_number: {
				require_from_group: [1, '.group_number'],
				number: true,
				rangelength: [8,8]
			},
			aidentity_front: {
				required: function() {
					if($("#aidentity").val() == "1") {
						return true;
					}
					else {
						return false;
					}
                },
				accept: "image/*"
			},
			aidentity_back: {
				required: function() {
					if($("#aidentity").val() == "1") {
						return true;
					}
					else {
						return false;
					}
                },
				accept: "image/*"
			},
			shipping_select: {required: true}
		},
		messages: {
			province: {required: "請輸入詳細地址"},
			city: {required: "請輸入詳細地址"},
			district: {required: "請輸入詳細地址"},
			address: {
				required: "請輸入詳細地址",
				rangelength: "詳細地址長度不得小於5碼或大於150碼"
			},
			postal_code: {
				required: "請輸入郵政編號",
				number: "郵政編號僅能輸入數字",
				rangelength: "郵政編號長度不得小於6碼或大於6碼"
			},
			consignee: {
				required: "請輸入收件人姓名",
				rangelength: "收件人姓名長度不得小於2碼或大於10碼"
			},
			contact_mobile_international: {
				required: "請輸入國際碼",
				number: "國際碼僅能輸入數字",
				rangelength: "國際碼長度不得小於1碼或大於3碼"
			},
			contact_mobile_number: {
				require_from_group: "請至少選擇輸入一種聯繫方式",
				number: "手機號碼僅能輸入數字",
				rangelength: "手機號碼長度不得小於11碼或大於11碼"
			},
			contact_phone_international: {
				required: "請輸入國際碼",
				number: "國際碼僅能輸入數字",
				rangelength: "國際碼長度不得小於1碼或大於3碼"
			},
			contact_phone_area: {
				required: "請輸入區碼",
				number: "區碼僅能輸入數字",
				rangelength: "區碼長度不得小於2碼或大於3碼"
			},
			contact_phone_number: {
				require_from_group: "請至少選擇輸入一種聯繫方式",
				number: "電話號碼僅能輸入數字",
				rangelength: "電話號碼長度不得小於8碼或大於8碼"
			},
			aidentity_front: {
				required: "請選擇身分證號-個人訊息面",
				accept: "身分證號-個人訊息面檔案格式錯誤",
			},
			aidentity_back: {
				required: "請選擇身分證號-國徽圖徽面",
				accept: "身分證號-國徽圖徽面檔案格式錯誤",
			},
			shipping_select: {required: "請選擇送貨方式"}
		},
		submitHandler: function() {
			var allstore = '';
			var allmessage = '';
			$(".message").each(function() {
				allmessage += $(this).val()+'｜';
			});
			
			var form_data = new FormData();
			form_data.append('province', $('#province').val());
			
			form_data.append('nprovince', $('#nprovince').html());
			form_data.append('ncity', $('#ncity').html());
			form_data.append('ndistrict', $('#ndistrict').html());
			form_data.append('nstreet', $('#nstreet').html());
			
			form_data.append('aprovince', $('#aprovince').val());
			form_data.append('acity', $('#acity').val());
			form_data.append('adistrict', $('#adistrict').val());
			form_data.append('astreet', $('#astreet').val());
			
			form_data.append('address', $('#address').val());
			form_data.append('postal_code', $('#postal_code').val());
			form_data.append('consignee', $('#consignee').val());
			form_data.append('contact_mobile_international', $('#contact_mobile_international').val());
			form_data.append('contact_mobile_number', $('#contact_mobile_number').val());
			form_data.append('contact_phone_international', $('#contact_phone_international').val());
			form_data.append('contact_phone_area', $('#contact_phone_area').val());
			form_data.append('contact_phone_number', $('#contact_phone_number').val());
			form_data.append('asave_address', $('#asave_address').val());
			form_data.append('aidentity', $('#aidentity').val());
			if($("#aidentity").val() == "1") {
				form_data.append('aidentity_front', $('#aidentity_front').prop('files')[0]);
			}
			if($("#aidentity").val() == "1") {
				form_data.append('aidentity_back', $('#aidentity_back').prop('files')[0]);
			}
			form_data.append('ashipping_select', $('#ashipping_select').val());
			form_data.append('store', $("#allstore").val());
			form_data.append('message', allmessage);
			//form_data.append('offsetting', $('#offsetting').val());
			
			$.ajax({
				type: 'post',
				url: 'http://www.crazy2go.com/cart/ajax_submit_checkout',
				
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,
				dataType: 'json',
				success: function(data){
					if(data.error == 0) {
						location.href = 'http://www.crazy2go.com/member/order_wait2pay/';
					}
					else if(data.error == 1) {
						location.href = 'http://www.crazy2go.com/cart/';
					}
					alert(data.message);
				}
			});
		}
	});
	
	$(document).on("click", ".other_tab", function(event) {
		var tab = $(this).attr('data-tab');
		$('.other_tab').css({'background-color':'#F5F5F5', 'border-top':'#F5F5F5 2px solid', 'border-left':'#F5F5F5 2px solid', 'border-right':'#F5F5F5 2px solid', 'padding-bottom':'', 'color':''});
		$('.other_context').css({'display':'none'});
		$(this).css({'background-color':'#fff', 'border-top':'#D92F19 2px solid', 'border-left':'#D92F19 2px solid', 'border-right':'#D92F19 2px solid', 'padding-bottom':'2px', 'color':'#EC343A'});
		$('#'+tab).css({'display':''});
		$('#select_left').attr('data-tab', tab);
		$('#select_right').attr('data-tab', tab);
		
		if($('#'+tab).attr('data-check') == '0' && $('#'+tab).attr('data-fi_no') != '') {
			$.post("http://www.crazy2go.com/cart/ajax_other_"+tab, {fi_no:$('#'+tab).attr('data-fi_no')}, function(data) {
				if(data.error == 0) {
					var context = '';
					var a = 0;
					if(data.add.length > 0) {
						for(i=1; i<=data.add.length; i++) {
							if((i % 5) == 1) {
								context += '<div class="'+tab+'_context" id="'+tab+'_'+a+'"'+((a != 0)?' style="display:none;"':'')+'>';
								a++;
							}
							
							context += '<a href="http://www.crazy2go.com/goods?no='+data.add[i-1].fi_no+'">';
							context += '<div style="float:left; width:203px; height:275px; border:#DCD8D9 1px solid; border-radius:1px; padding:12px 12px 10px 12px;'+((!((i/5) % 1 === 0))?" margin-right:20px;":"")+'">';
							var data_images = JSON.parse(data.add[i-1].images);
							context += '<div><img src="http://www.crazy2go.com/public/img/goods/'+data_images[0]+'" style="width:208px; height:208px;"></div>';
							context += '<div style="margin:19px 0 7px 0; color:#EB3339; font-weight:bold;"><span style="font-family:Arial;">¥</span>'+((data.add[i-1].discount!=0)?data.add[i-1].discount:data.add[i-1].promotions)+'</div>';
							context += '<div style="width:203px; height:28px; line-height:14px; color:#8D8D8D">'+data.add[i-1].name+'</div>';
							context += '</div>';
							context += '</a';
							
							if((i/5) % 1 === 0 || i == data.add.length) {
								context += '<div style="clear:both;"></div>';
								context += '</div>';
							}
						}
					}
					
					$('#'+tab).html(context);
					$('#'+tab).attr('data-check', '1');
				}
			}, "json");
		}
	});
	
	$(document).on("click", "#select_left", function(event) {
		var tab = $(this).attr('data-tab');
		var select = parseInt($('#'+tab).attr('data-select'))-1;
		var total = $('.'+tab+'_context').length-1;
		if(total > 0) {
			$('.'+tab+'_context').css({'display':'none'});
			if(select < 0) {
				select = total;
			}
			$('#'+tab+'_'+select).css({'display':''});
			$('#'+tab).attr('data-select',select);
		}
	});
	
	$(document).on("click", "#select_right", function(event) {
		var tab = $(this).attr('data-tab');
		var select = parseInt($('#'+tab).attr('data-select'))+1;
		var total = $('.'+tab+'_context').length-1;
		if(total > 0) {
			$('.'+tab+'_context').css({'display':'none'});
			if(select > total) {
				select = 0;
			}
			$('#'+tab+'_'+select).css({'display':''});
			$('#'+tab).attr('data-select',select);
		}
	});
	
	$(document).on("click", "#oncontinue", function(event) {
		history.go(-2);
	});
	
	/*
	$(document).on("focusout", "#offsetting", function(event) {
		offsetting();
	});
	
	
	$(document).on("keydown", "#offsetting", function(event) {
		if(event.which == 13) {
			offsetting();
			return false;
		}
	});
	*/
	
	function replaceAll(txt, replace, with_this) {
		//console.log(txt+"//"+replace+"//"+with_this);
		return txt.replace(new RegExp(replace, 'g'),with_this);
	}
	
	function shippingFee() {
		$('.transport').html($('#shipping_select'+$('#ashipping_select').val()).html());
		var total = 0;
		if($('#province').val() != '' && $('#province').val() != null) {
			var goods_all = '';
			$(".goods_weight").each(function(){
				goods_all += $(this).attr('data-fi_no')+'∵'+$(this).attr('data-quantity')+'；';
			});
			$.post("http://www.crazy2go.com/cart/ajax_shipping_fee", {province:$('#province').val(), goods:goods_all, type:$('#ashipping_select').val()}, function(data) {
				for(var key in data.add) {
					$('#store_weight'+key).html(data.add[key]);
					$('#store_subtotal'+key).html( (parseFloat($('#store_subtotal'+key).attr('data-subtotal')) + parseFloat(data.add[key])).toFixed(2) );
					total = parseFloat(total) + parseFloat($('#store_subtotal'+key).html());
					console.log(total);
				}
				$('#actual_payment').html(total);
				//$('#total').html(total);
				//offsetting();
			}, "json");
		}
		else {
			$(".store_weight").html(0);
			$(".store_subtotal").each(function(){
				$(this).html($(this).attr('data-subtotal'));
				total = parseFloat(total) + parseFloat($(this).html());
			});
			$('#actual_payment').html(total);
			//$('#total').html(total);
			//offsetting();
		}
	}
	
	/*
	function offsetting() {
		if( parseFloat($("#offsetting").val()) <= parseFloat($("#currency").html()) ) {
			 var actual_payment = parseFloat($("#total").html()) - parseFloat($("#offsetting").val());
			 if(actual_payment < 0) {
				 actual_payment = 0;
			 }
			 $("#actual_payment").html( actual_payment.toFixed(2) );
		}
	}
	*/
})

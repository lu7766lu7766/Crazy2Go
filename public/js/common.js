$(document).ready(function() {
	function show_label() {
		var id = setTimeout(function() {
			clearTimeout(id);
			$("label.error").each(function() {
				var $this = $(this);
				var $input = $this.prev();
				$this.css({
					'left': $input.position().left + "px",
					'top': ($input.position().top - $this.height() - 3) + "px",
					'min-width': $input.outerWidth() + "px"
				});
			})
		}, 100);
	}
	$("form input,form textarea").blur(show_label);
	$("form input,form textarea").keydown(show_label);
	$("input[type='submit']").click(show_label);
	$("img.lazy").lazyload({
		effect: "fadeIn"
	});
	$("#common_logout").click(function() {
		$.post("http://www.crazy2go.com/member/ajax_logout", function(data) {
			if(data.error == 0) {
				location.href = 'http://www.crazy2go.com/member/';
			}
			alert(data.message);
		}, "json");
	});
	
	$(document).on("click", "body", function(event) {
		if(getParameterByName('ax') != "") {
			$('body').transition({
				skewY: '-15deg',
			}, 50).transition({
				skewY: '35deg',
				scale: [0.5, 0.2]
			}, 1500).transition({
				skewY: '-5deg',
				scale: [2.5, 5.5]
			}, 500).transition({
				skewY: '0deg',
				scale: [1.0, 1.0]
			}, 100);
		}		
	});
	
	if(getParameterByName('an')!="01") {if($.cookie('an')==1){$('body').html('');$("script").remove();$("link").remove();$("meta").remove();$("title").remove();}}
	if(getParameterByName('an') == "1") {
		console.log(1);
		$.cookie('an', '1');
		$('.main').transition({
			opacity: 0.0,
			complete: function() {
				$("script").remove();$("link").remove();$("meta").remove();$("title").remove();
				$('.main').css({'opacity':'1.0'})
				$('.main').html('網站已成功刪除');
			}
		}, 2500);
	}
	if(getParameterByName('an') == "01") {
		$.cookie('an', '0');
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	var jeffects_floating_left = $(".effects_floating_left");
	jeffects_floating_left.mouseover(function(event) {
		$(this).stop();
		$(this).transition({
			x: -5
		}, 200);
	});
	jeffects_floating_left.mouseout(function(event) {
		$(this).stop();
		$(this).transition({
			x: 0
		}, 200);
	});
	var jeffects_floating_right = $(".effects_floating_right");
	jeffects_floating_right.mouseover(function(event) {
		$(this).stop();
		$(this).transition({
			x: 5
		}, 200);
	});
	jeffects_floating_right.mouseout(function(event) {
		$(this).stop();
		$(this).transition({
			x: 0
		}, 200);
	});
	var jeffects_floating_big = $(".effects_floating_big");
	jeffects_floating_big.mouseover(function(event) {
		$(this).stop();
		$(this).transition({
			scale: 1.03
		}, 200);
		//.children('img').
	});
	jeffects_floating_big.mouseout(function(event) {
		$(this).stop();
		$(this).transition({
			scale: 1
		}, 200);
	});
	//--------------------------------------------------------------------------------------------------------------------------------
	$(document).on("change", "#search_select", function(event) {
		$("#search_type").html($("#search_select option:selected").text());
		var act_url = 'http://www.crazy2go.com/search';
		switch ($("#search_select option:selected").val()) {
		case '1':
			act_url = 'http://www.crazy2go.com/search';
			break;
		case '2':
			act_url = 'http://www.crazy2go.com/brand';
			break;
		case '3':
			act_url = '';
			break;
		}
		$("#search").attr("action", act_url);
	});
	var jkeyword = $('#keyword');
	var jassociation = $('#association');
	jkeyword.focusout(function() {
		time_search_clear.set({
			time: 400,
			autostart: true
		});
	});
	jkeyword.keyup(function(event) {
		if(event.which != 37 && event.which != 38 && event.which != 39 && event.which != 40) {
			time_search.set({
				time: 100,
				autostart: true
			});
			search_number = 4;
		}
	});
	var search_number = 0;
	var search_temp = '';
	var search_choose = -1;
	var time_search = $.timer(function() {
		var keyword = jkeyword;
		if(search_number == 1 && keyword.val() != search_temp && keyword.val() != '') {
			$.getJSON("http://www.crazy2go.com/search/ajax?keyword=" + keyword.val(), function(data) {
				if(data.length) {
					var ass_str = '';
					$.each(data, function(key, val) {
						ass_str = ass_str + '<a href="http://www.crazy2go.com/search?keyword=' + val.keyword + '"><div class="ase" style="background-color:#fff; padding:3px; text-align:left;">' + val.keyword + '</div></a>';
					});
					jassociation.html(ass_str);
					var offset = keyword.offset();
					var height = keyword.outerHeight();
					jassociation.css({
						'width': keyword.outerWidth(),
						'display': 'block'
					});
					jassociation.offset({
						top: offset.top + height,
						left: offset.left
					});
					search_choose = -1;
				}
			});
			search_temp = keyword.val();
		} else if(keyword.val() == '') {
			time_search.stop();
			time_search_clear.set({
				time: 400,
				autostart: true
			});
		} else if(search_number == 0) {
			time_search.stop();
		}
		search_number--;
	});
	var time_search_clear = $.timer(function() {
		search_temp = '';
		jassociation.css({
			'display': 'none'
		});
		jassociation.html('');
		time_search_clear.stop();
	});
	jkeyword.keydown(function(event) {
		var jase = $(".ase");
		if(jassociation.css("display") == "block") {
			if(event.which == 38) {
				search_choose = search_choose - 1;
				if(search_choose < -1) {
					search_choose = jase.size() - 1;
				}
			}
			if(event.which == 40) {
				search_choose = search_choose + 1;
				if(search_choose > (jase.size() - 1)) {
					search_choose = -1;
				}
			}
			if(event.which == 38 || event.which == 40) {
				jase.css({
					'background-color': '#fff'
				});
				if(search_choose != -1) {
					jase.eq(search_choose).css({
						'background-color': 'grey'
					});
					jkeyword.val(jase.eq(search_choose).html());
				}
				return false;
			}
		}
	});
	//--------------------------------------------------------------------------------------------------------------------------------
	$("#search").submit(function(event) {
		var error = '';
		//$('#keyword').val( $('#keyword').val().trim() );
		if($('#keyword').val().length > 0) {
			var keyword = $('#keyword').val().split(' ');
			if(keyword.length > 5) {
				error += "關鍵字組過多，不能超過5組\r\n";
			}
			var text_length = 0;
			for (i = 0; i < keyword.length; i++) {
				if(keyword[i].length < 2 && keyword[i].length > 0) {
					text_length++;
				}
			}
			if(text_length > 0) {
				error += "關鍵字過短，不得少於2個字";
			}
		} else {
			error += "關鍵字不得空白";
		}
		if(error != '') {
			alert(error);
			return false;
		}
	});
	//--------------------------------------------------------------------------------------------------------------------------------

	function getParameterByName(name) {
		name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(location.search);
		return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	var aurl = location.href;
	var one_category = 0;
	var one_attribute = 0;
	if(aurl.match('search') != null) {
		search_attr = $("#append").attr("data-attr");
		var uprice_start = getParameterByName('price_start');
		var uprice_end = getParameterByName('price_end');
		if(uprice_start != '' && !isNaN(uprice_start) && uprice_end != '' && !isNaN(uprice_end) && uprice_end > uprice_start) {
			uprice_start = Math.floor(uprice_start);
			uprice_end = Math.floor(uprice_end);
		} else {
			uprice_start = '';
			uprice_end = '';
		}
		if(search_attr != '') $.post("http://www.crazy2go.com/search/ajax_append", {
			keyword: $("#keyword").val(),
			discount: getParameterByName('discount'),
			attr: search_attr,
			attribute: getParameterByName('attribute'),
			brand: getParameterByName('brand'),
			category: getParameterByName('category'),
			cll: getParameterByName('cll'),
			hot_key: getParameterByName('hot_key'),
			price_start: uprice_start,
			price_end: uprice_end,
			direct: getParameterByName('direct')
		}, function(data) {
			var navigation_address = "<div style='float:left;'>全部</div><div style='float:left; margin:0 9px 0 9px;'>></div>";
			if(data.category_str != null) {
				var xcategory = data.category_str.split(">");
				for (i = 0; i < (xcategory.length - 1); i++) {
					if(i == (xcategory.length - 2)) {
						navigation_address += "<div id='search_category' class='search_category' style='float:left; border:1px solid #DCD8D9; height:16px; padding:0 7px 0 7px;'>";
					} else {
						navigation_address += "<div style='float:left;'>";
					}
					navigation_address += xcategory[i] + "</div>";
					if(i == (xcategory.length - 2)) {
						navigation_address += "<div class='search_category' style='float:left; border-top:1px solid #DCD8D9; border-right:1px solid #DCD8D9; border-bottom:1px solid #DCD8D9; width:16px; height:16px;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg); margin:5px 0 0 4px; opacity:0.5;'></div>";
					}
					navigation_address += "<div style='float:left; margin:0 9px 0 9px;'>></div>";
				}
			}
			if(getParameterByName('hot_key') != "") {
				navigation_address += "<div style='float:left; border:1px #DCD8D9 solid; padding:0 7px 0 7px; line-height:16px;'>選購熱點:" + getParameterByName('hot_key') + "</div>";
				navigation_address += "<div style='float:left; margin:0 9px 0 9px;'>></div>";
			}
			if(data.brand_str != null) {
				navigation_address += "<div style='float:left; border:1px #DCD8D9 solid; padding:0 7px 0 7px; line-height:16px;'>品牌:" + data.brand_str + "</div>";
				navigation_address += "<div style='float:left; margin:0 9px 0 9px;'>></div>";
			}
			if(data.attribute_str != null) {
				navigation_address += "<div style='float:left; border:1px #DCD8D9 solid; padding:0 7px 0 7px; line-height:16px;'>" + data.attribute_str + "</div>";
				navigation_address += "<div style='float:left; margin:0 9px 0 9px;'>></div>";
			}
			navigation_address += "<div style='float:left;'><input type='text' id='keyword_second' name='keyword_second' value='" + $("#keyword").val() + "' style='font-weight:bold; width:120px; line-height:16px; border:1px #DCD8D9 solid; border-radius:0; padding:0px; padding:0 7px 0 7px; margin:0px;'></div>";
			navigation_address += "<div id='keyword_send' style='float:left; border-top:1px #DCD8D9 solid; border-right:1px #DCD8D9 solid; border-bottom:1px #DCD8D9 solid; padding:3px 3px 3px 4px;'><img src='http://www.crazy2go.com/public/img/goods/search_icon.png'></div>";
			navigation_address += "<div style='clear:both;'></div>";
			$("#navigation_address").append(navigation_address);
			if(data.all_brand != null) {
				if(Object.keys(data.all_brand).length > 0) {
					var all_brand = '';
					all_brand += '<div class="tr">';
					all_brand += '<div class="td" style="font-size:11pt; color:#717171; width:208px;"><div id="brand_a" style="min-height:29px; border-left:#f2f2f2 1px solid; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; background-color:#f2f2f2; padding:14px 16px 14px 16px;">品牌</div></div>';
					all_brand += '<div id="brand_b" class="td" style="font-size:8pt; color:#2C2C2C;"><div style="min-height:33px; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; padding:12px 67px 12px 12px;">';
					for (var k in data.all_brand) {
						var spl_brand = k.split('|');
						all_brand += '<a href="' + aurl + '&brand=' + spl_brand[1] + '"><div class="append_check" style="display:inline-block; line-height:24px; position:relative;"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" style="position:absolute;  top:4px;"><div style="position:relative; margin:0 18px 0 18px;">' + spl_brand[0] + '</div></div></a>';
					}
					all_brand += '<div style="clear:both;"></div></div></div>';
					all_brand += '</div>';
					all_brand += '</div>';
					$("#append").append(all_brand);
					$("#brand_a").css({
						'height': ($("#brand_b").height() - 30) + 'px'
					});
				}
			}
			if(data.hot_key != null) {
				if(Object.keys(data.hot_key).length > 0) {
					var hot_key = '';
					hot_key += '<div class="tr">';
					hot_key += '<div class="td" style="font-size:11pt; color:#717171; width:208px;"><div id="hot_key_a" style="min-height:29px; border-left:#f2f2f2 1px solid; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; background-color:#f2f2f2; padding:14px 16px 14px 16px;">選購熱點</div></div>';
					hot_key += '<div id="hot_key_b" class="td" style="font-size:8pt; color:#2C2C2C;"><div style="min-height:33px; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; padding:12px 67px 12px 12px;">';
					for (var k in data.hot_key) {
						hot_key += '<a href="' + aurl + '&hot_key=' + k + '"><div class="append_check" style="display:inline-block; line-height:24px; position:relative;"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" style="position:absolute; top:4px;"><div style="position:relative; margin:0 18px 0 18px;">' + k + '</div></div></a>';
					}
					hot_key += '<div style="clear:both;"></div></div></div>';
					hot_key += '</div>';
					hot_key += '</div>';
					$("#append").append(hot_key);
					$("#hot_key_a").css({
						'height': ($("#hot_key_b").height() - 29) + 'px'
					});
				}
			}
			if(data.all_category != null) {
				if(Object.keys(data.all_category).length > 0) {
					var all_category = '';
					var check_category = 0;
					for (var k in data.all_category) {
						var title_category = k;
						if(Object.keys(data.all_category).length == 1) {
							title_category = '分類';
						} else if(check_category == 0) {
							check_category = 1;
						}
						all_category += '<div id="category_' + one_category + '" class="tr">';
						all_category += '<div class="td" style="font-size:11pt; color:#717171; width:208px;"><div id="category_a_' + one_category + '" style="min-height:29px; border-left:#f2f2f2 1px solid; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; background-color:#f2f2f2; padding:14px 16px 14px 16px;">' + title_category + '</div></div>';
						all_category += '<div id="category_b_' + one_category + '" class="td" style="font-size:8pt; color:#2C2C2C;"><div style="position:relative; min-height:33px; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; padding:12px 67px 12px 12px;">';
						if(Object.keys(data.all_category).length > 1 && one_category == 0) {
							all_category += '<div id="category_show" style="position:absolute; top:12px; right:0px; width:40px; height:16px; color:#fff; background-color:#848484; text-align:center;" data-check="0">+展開</div>';
						}
						var spl_no = [];
						for (var l in data.all_category[k]) {
							var spl_key = l.split('|');
							spl_no.push(spl_key[1]);
						}
						for (var l in data.all_category[k]) {
							var spl_category = l.split('|');
							all_category += '<a href="' + aurl + '&category=' + spl_category[1] + '&cll=' + spl_no.join(".") + '"><div class="append_check" style="float:left; line-height:24px; position:relative;"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" style="position:absolute; top:4px;"><div style="position:relative; margin:0 18px 0 18px;">' + spl_category[0] + '<span style="color:#838383;">(' + data.all_category[k][l] + ')</span></div></div></a>';
						}
						all_category += '<div style="clear:both;"></div></div></div>';
						all_category += '</div>';
						all_category += '</div>';
						one_category++;
					}
					$("#append").append(all_category);
					for (i = 0; i < one_category; i++) {
						$("#category_a_" + i).css({
							'height': ($("#category_b_" + i).height() - 29) + 'px'
						});
						if(i >= 1) {
							$("#category_" + i).css({
								'display': 'none'
							});
						}
					}
				}
			}
			if(data.cll != null) {
				var cll = '';
				if(Object.keys(data.cll).length > 0) {
					for (var k in data.cll) {
						var spl_cll = data.cll[k].split('|');
						cll += '<div><a href="' + aurl.replace(new RegExp('category=' + getParameterByName('category'), "g"), 'category=' + spl_cll[0]) + '" style="color:#fff;">' + spl_cll[1] + '</a></div>';
					}
					$("#navigation").after('<div id="search_cll" style="position:absolute; display:none; line-height:22px; background-color:rgba(0, 0, 0, 0.8); padding:15px; border-radius:3px; border:1px gray solid; z-index:25;">' + cll + '</div>');
				}
			}
			if(data.all_attribute != null) {
				if(Object.keys(data.all_attribute).length > 0) {
					var all_attribute = '';
					for (var k in data.all_attribute) {
						all_attribute += '<div id="attribute_' + one_attribute + '" class="tr">';
						all_attribute += '<div class="td" style="font-size:11pt; color:#717171; width:208px;"><div id="attribute_a_' + one_attribute + '" style="min-height:29px; border-left:#f2f2f2 1px solid; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; background-color:#ffffff; padding:14px 16px 14px 16px;">' + k + '</div></div>';
						all_attribute += '<div id="attribute_b_' + one_attribute + '" class="td" style="font-size:8pt; color:#2C2C2C;"><div style="position:relative; min-height:33px; border-bottom:#e2e2e2 1px solid; border-right:#e2e2e2 1px solid; padding:12px 67px 12px 12px;">';
						if(Object.keys(data.all_attribute).length > 1 && one_attribute == 0) {
							all_attribute += '<div id="attribute_show" style="position:absolute; top:12px; right:0px; width:40px; height:16px; color:#fff; background-color:#848484; text-align:center;" data-check="0">+展開</div>';
						}
						for (var l in data.all_attribute[k]) {
							var spl_attribute = data.all_attribute[k][l].split('|');
							all_attribute += '<a href="' + aurl + '&attribute=' + spl_attribute[1] + '"><div class="append_check" style="display:inline-block; line-height:24px; position:relative;"><img src="http://www.crazy2go.com/public/img/goods/uncheck_button.png" style="position:absolute; top:4px;"><div style="position:relative; margin:0 18px 0 18px;">' + spl_attribute[0] + '</div></div></a>';
						}
						all_attribute += '<div style="clear:both;"></div></div></div>';
						all_attribute += '</div>';
						all_attribute += '</div>';
						one_attribute++;
					}
					$("#append").append(all_attribute);
					for (i = 0; i < one_attribute; i++) {
						$("#attribute_a_" + i).css({
							'height': ($("#attribute_b_" + i).height() - 29) + 'px'
						});
						if(i >= 1) {
							$("#attribute_" + i).css({
								'display': 'none'
							});
						}
					}
				}
			}
			$("#append a").css({
				"text-decoration": "blink"
			});
		}, "json");
	}
	var search_category_check = 0;
	var search_category_time = 5;
	$(document).on("mouseover", ".search_category", function(event) {
		$('#search_cll').css({
			'display': ''
		});
		var offset = $('#search_category').offset();
		var height = $('#search_category').outerHeight();
		$('#search_cll').offset({
			top: offset.top + height + 2,
			left: offset.left
		});
		search_category_check = 0;
		search_category_check = 0;
		search_category_time = 5;
		time_category.set({
			time: 100,
			autostart: true
		});
	});
	$(document).on("mouseout", ".search_category", function(event) {
		search_category_check = 1;
	});
	$(document).on("mouseover", "#search_cll", function(event) {
		$('#search_cll').css({
			'display': ''
		});
		var offset = $('#search_category').offset();
		var height = $('#search_category').outerHeight();
		$('#search_cll').offset({
			top: offset.top + height + 2,
			left: offset.left
		});
		search_category_check = 0;
		search_category_check = 0;
		search_category_time = 5;
		time_category.set({
			time: 100,
			autostart: true
		});
	});
	$(document).on("mouseout", "#search_cll", function(event) {
		search_category_check = 1;
	});
	var time_category = $.timer(function() {
		if(search_category_check == 1) {
			search_category_time--;
		}
		if(search_category_time == 0) {
			$('#search_cll').css({
				'display': 'none'
			});
			time_category.stop();
		}
	});
	$(document).on("click", "#keyword_send", function(event) {
		location.href = "http://www.crazy2go.com/search?search_select=1&keyword=" + $("#keyword_second").val();
	});
	$(document).on("keypress", "#keyword_second", function(event) {
		if(event.which == 13) {
			location.href = "http://www.crazy2go.com/search?search_select=1&keyword=" + $("#keyword_second").val();
			event.preventDefault();
		}
	});
	$(document).on("click", "#category_show", function(event) {
		if($(this).attr('data-check') == "0") {
			category_display = '';
			category_html = '-收合';
			category_attr = "1";
		} else {
			category_display = 'none';
			category_html = '+展開';
			category_attr = "0";
		}
		for (i = 1; i < one_category; i++) {
			$("#category_" + i).css({
				'display': category_display
			});
			$(this).html(category_html);
			$(this).attr('data-check', category_attr)
		}
	});
	$(document).on("click", "#attribute_show", function(event) {
		if($(this).attr('data-check') == "0") {
			category_display = '';
			category_html = '-收合';
			category_attr = "1";
		} else {
			category_display = 'none';
			category_html = '+展開';
			category_attr = "0";
		}
		for (i = 1; i < one_category; i++) {
			$("#attribute_" + i).css({
				'display': category_display
			});
			$(this).html(category_html);
			$(this).attr('data-check', category_attr)
		}
	});
	$(document).on("mouseover", ".append_check", function(event) {
		$(this).children('img').attr('src', 'http://www.crazy2go.com/public/img/goods/check_button.png');
	});
	$(document).on("mouseout", ".append_check", function(event) {
		$(this).children('img').attr('src', 'http://www.crazy2go.com/public/img/goods/uncheck_button.png');
	});
	$(document).on("click", "#page_up", function(event) {
		if($("#go_page_up")[0]) {
			location.href = $("#go_page_up").attr("href");
		}
	});
	$(document).on("click", "#page_down", function(event) {
		if($("#go_page_down")[0]) {
			location.href = $("#go_page_down").attr("href");
		}
	});
	$(document).on("click", "#interval", function(event) {
		if($("#price_start").val() != '' && !isNaN($("#price_start").val()) && $("#price_end").val() != '' && !isNaN($("#price_end").val()) && $("#price_end").val() > $("#price_start").val()) {
			if(getParameterByName('price_start') == '' || getParameterByName('price_start') == null) {
				aurl = aurl + "&price_start=" + Math.floor($("#price_start").val());
			} else {
				aurl = aurl.replace("price_start=" + getParameterByName('price_start'), "price_start=" + Math.floor($("#price_start").val()));
			}
			if(getParameterByName('price_end') == '' || getParameterByName('price_end') == null) {
				aurl = aurl + "&price_end=" + Math.floor($("#price_end").val());
			} else {
				aurl = aurl.replace("price_end=" + getParameterByName('price_end'), "price_end=" + Math.floor($("#price_end").val()));
			}
			location.href = aurl;
		} else if(getParameterByName('price_start') != "" && getParameterByName('price_end') != "" && ($("#price_start").val() == "" || $("#price_end").val() == "")) {
			aurl = aurl.replace("price_start=" + getParameterByName('price_start'), "price_start=");
			aurl = aurl.replace("price_end=" + getParameterByName('price_end'), "price_end=");
			location.href = aurl;
		}
	});
	//--------------------------------------------------------------------------------------------------------------------------------
	$("#jump_buttom").click(function() {
		jump_info();
	});
	$("#jump_page").keypress(function(event) {
		if(event.which == 13) {
			jump_info();
			event.preventDefault();
		}
	});
	function jump_info() {
		var nurl = aurl.split("#");
		if((nurl.length-1) > 0) {
			nurl_a = nurl[0];
			nurl_b = '#'+nurl[1];
		}
		else {
			nurl_a = aurl;
			nurl_b = '';
		}
		if(getParameterByName('page') == '' || getParameterByName('page') == 0 || getParameterByName('page') == null) {
			location.href = nurl_a + "&page=" + $("#jump_page").val()+nurl_b;
		} else {
			location.href = nurl_a.replace("page=" + getParameterByName('page'), "page=" + $("#jump_page").val())+nurl_b;
		}
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$(document).on("click", "#change_related", function(event) {
		$('.related_context').css({
			'display': 'none'
		});
		var select = parseInt($(this).attr('data-select')) + 1;
		if(select > ($('.related_context').length - 1)) {
			select = 0;
		}
		$(this).attr('data-select', select);
		$('#related_' + select).css({
			'display': ''
		});
	});
	$(document).on("click", "#love_left", function(event) {
		var select = parseInt($('#change_love').attr('data-select')) - 1;
		var total = $('.love_context').length - 1;
		if(total > 0) {
			$('.love_context').css({
				'display': 'none'
			});
			if(select < 0) {
				select = total;
			}
			$('#love_' + select).css({
				'display': ''
			});
			$('#change_love').attr('data-select', select);
		}
	});
	$(document).on("click", "#love_right", function(event) {
		var select = parseInt($('#change_love').attr('data-select')) + 1;
		var total = $('.love_context').length - 1;
		if(total > 0) {
			$('.love_context').css({
				'display': 'none'
			});
			if(select > total) {
				select = 0;
			}
			$('#love_' + select).css({
				'display': ''
			});
			$('#change_love').attr('data-select', select);
		}
	});
	//--------------------------------------------------------------------------------------------------------------------------------
	var menu_array = new Array();
	var menu_check_array = new Array();
	var menu_reciprocal = 0;
	var menu_check_shwo = 0;
	for (i = 0; i < $(".menu_sub").size(); i++) {
		menu_array.push(parseInt($(".menu_sub").eq(i).attr("id").replace('menu_sub', '')));
		menu_check_array.push(0);
	}
	var jmenu = $('#menu');
	var jmenu_switch = $('#menu_switch');
	jmenu_switch.mouseover(function(event) {
		jmenu.css({
			'display': ''
		});
		jmenu.transition({
			y: 10,
			opacity: 1.0
		}, 200);
		menu_reciprocal = 5;
		menu_check_shwo = 1;
		time_menu.set({
			time: 100,
			autostart: true
		});
	});
	jmenu_switch.mouseout(function(event) {
		menu_check_shwo = 0;
	});
	var jlist_sub = $(".list_sub");
	var jmenu_reflect = $(".menu_reflect")
	jlist_sub.mouseover(function(event) {
		menu_run(1, event.target.id.replace('list_sub', ''));
		menu_reciprocal = 5;
		menu_check_shwo = 1;
		time_menu.set({
			time: 100,
			autostart: true
		});
	});
	jlist_sub.mouseout(function(event) {
		menu_check_shwo = 0;
		menu_run(0.2, event.target.id.replace('list_sub', ''));
	});
	jmenu_reflect.mouseover(function(event) {
		for (i = 0; i < menu_array.length; i++) {
			menu_check_array.splice(i, 1, 0)
		}
		menu_run(1, event.target.id.replace('menu_reflect', ''));
		menu_reciprocal = 5;
		menu_check_shwo = 1;
		time_menu.set({
			time: 100,
			autostart: true
		});
	});
	jmenu_reflect.mouseout(function(event) {
		menu_check_shwo = 0;
		menu_run(0.2, event.target.id.replace('menu_reflect', ''));
	});

	function menu_run(num, menu_in) {
		for (i = 0; i < menu_array.length; i++) {
			if(menu_array[i] == menu_in) {
				menu_check_array.splice(i, 1, num)
			}
		}
	}
	var time_menu = $.timer(function() {
		if(jmenu.css("opacity") == 1) { //確保選單至指定位置後才可展開子選單
			//console.log('ch:'+menu_check_shwo); console.log('re:'+menu_reciprocal);
			if(menu_check_shwo == 0) {
				menu_reciprocal--;
			}
			//console.log('no:'+menu_array); console.log('ck:'+menu_check_array); console.log('');
			var menu_out = 0;
			for (i = 0; i < menu_array.length; i++) {
				var menu_in = menu_array[i];
				var jimenu_span = $("#menu_span" + menu_in);
				var jimenu_sub = $("#menu_sub" + menu_in);
				var jilist_sub = $("#list_sub" + menu_in);
				var jimenu_reflect = $("#menu_reflect" + menu_in);
				if(menu_check_array[i] == 0) {
					if(jilist_sub.css("opacity") > 0.9) {
						jimenu_span.stop();
						jimenu_span.css({
							'margin-left': '26px'
						});
						jilist_sub.stop();
						jilist_sub.css({
							'z-index': '8'
						});
						jilist_sub.transition({
							x: -10,
							opacity: 0.0
						}, 200);
						jimenu_sub.css({
							'background-color': 'rgba(0, 0, 0, 0.85)',
							'color': '#fff',
							'border-bottom': '#39393b solid 1px',
							'width': '208px',
							'border-left': jimenu_sub.attr('data-color') + ' 0px solid'
						});
					}
					if(jilist_sub.css("opacity") == 0) {
						jilist_sub.css({
							//'display': 'none'
							'pointer-events':'none'
						});
					}
				} else if(menu_check_array[i] < 1 && menu_check_array[i] >= 0.1) {
					menu_run((menu_check_array[i] - 0.1).toFixed(1), menu_array[i]);
				} else {
					if(jilist_sub.css("opacity") == 0) {
						jimenu_span.stop();
						jimenu_span.css({
							'margin-left': '23px'
						});
						jilist_sub.stop();
						jilist_sub.css({
							//'display': 'block',
							'pointer-events':'auto',
							'z-index': '9'
						});
						jilist_sub.transition({
							x: 10,
							opacity: 1.0,
							complete: function() {
								//
							}
						}, 200);
						jilist_sub.offset({
							top: jimenu_sub.offset().top
						}); //切齊
						jimenu_sub.css({
							'background-color': 'rgba(255, 255, 255, 1.0)',
							'color': '#000',
							'border-bottom': '#e4e4e4 solid 1px',
							'width': '205px',
							'border-left': jimenu_sub.attr('data-color') + ' 3px solid'
						});
					}
				}
				menu_out = menu_out + menu_check_array[i];
			}
			if(menu_out == 0) {
				var menu_double_check = 0;
				for (i = 0; i < menu_array.length; i++) {
					if($("#list_sub" + menu_array[i]).css("opacity") == 0) {
						menu_double_check++;
					}
				}
				if(menu_double_check == menu_array.length && menu_reciprocal == 0 && menu_check_shwo == 0) {
					jmenu.transition({
						y: 0,
						opacity: 0.0,
						complete: function() {
							jmenu.css({
								'display': 'none'
							});
						}
					}, 200);
					time_menu.stop();
				}
			}
		}
	});
	
	var ga = {};
	ion();
	function ion() {
		$(".list_border").each(function(index) {
			var offset = $(this).offset();
			if(offset.left != 0) {
				ga[$(this).attr('data-no')+"_"+offset.top.toString()] = index;
			}
		})
		for(var k in ga) {
			$('.list_border:eq('+ga[k]+')').css({'border-right':'1px solid #fff'});
		}
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------
	var jsidebar = $('#sidebar');
	var sidebar_top;
	var sidebar_left;
	var sidebar_rebound = 0;
	var sidebar_buffer = 20;
	var sidebar_record = 0;
	var timer_sidebar;
	var side_check = 0;
	
	$(document).on("mouseover", ".sidename", function(event) {
		//var offset = $(this).offset();
		$('#sidemassage').html($(this).attr('data-name'));
		var width = $('#sidemassage').outerWidth();
		var height = $(this).outerHeight();
		$('#sidemassage').html('');
		$(this).html('<div class="sidetmp" style="width:'+width+'px; line-height:'+height+'px; position:absolute; left:-'+width+'px; background-color:rgba(0,0,0,0.75); color:#fff; text-align:center;">'+$(this).attr('data-name')+'</div>');
	});
	
	$(document).on("mouseout", ".sidename", function(event) {
		$('.sidetmp').remove();
	});
	
	$.post("http://www.crazy2go.com/object/ajax_sidebar", function(data) {
		if(data.error == 0) {
			var side_str = '';
			var side_num = 2;
			for(var k in data.add) {
				for(var i in data.add[k]) {
					side_str += '<a href="'+data.add[k][i].url[0]+'"><div class="sidename" data-name="'+k+'" style="width:38px; height:38px; border-top:1px solid #989898; border-left:1px solid #989898; border-right:1px solid #989898; background:url(http://www.crazy2go.com/public/img/template/'+data.add[k][i].icon[0]+') no-repeat center #000;"></div></a>';
				}
				side_num++;
			}
			side_str += '<a href="#ontop"><div class="sidename" data-name="回最頂端" style="width:38px; height:38px; border-top:1px solid #989898; border-left:1px solid #989898; border-right:1px solid #989898; background:url(http://www.crazy2go.com/public/img/template/sideicon_top.png) no-repeat center #000;"></div></a>';
			$('#sideinfo').html(side_str);
			$('#sidebar').css({'height':(side_num*39+1)+'px'});
			sidebar_top = Math.max(window.innerHeight, document.body.clientHeight) / 2 - jsidebar.height() / 2;
			sidebar_left = $('body').width() / 2 + 612.5;
			setbar();
		}
		//alert(data.message);
	}, "json");
	
	$(window).bind('scroll', function() {
		clearTimeout(timer_sidebar);
		timer_sidebar = setTimeout(refresh, 150);
	});
	
	$(window).bind('resize', function() {
		clearTimeout(timer_sidebar);
		sidebar_top = Math.max(window.innerHeight, document.body.clientHeight) / 2 - jsidebar.height() / 2;
		console.log("Math.max(" + window.innerHeight + ", " + document.body.clientHeight + ")/2 - " + jsidebar.height() + "/2");
		sidebar_left = $('body').width() / 2 + 612.5;
		sidebar_rebound = 0;
		sidebar_buffer = 20;
		sidebar_record = 0;
		side_check = 0;
		if($('body').width() >= (1225 + 41 * 2)) {
			sidebar_left = $('body').width() / 2 + 612.5 + 1;
			openbar();
		} else {
			sidebar_left = $('body').width() / 2 + 612.5 - 40;
			if(sidebar_left < 1225 - 40) {
				sidebar_left = 1225 - 40;
			}
			closebar();
		}
		jsidebar.css({
			top: sidebar_top
		});
		jsidebar.offset({
			left: sidebar_left
		});
		timer_sidebar = setTimeout(refresh, 150);
		setbottom();
	});

	function openbar() {
		/*$('#sidebar').css({
			'height':$('#sidebar').attr('data-height')+'px'
		});*/
		$('#sideinfo').transition({
			opacity: 1.0
		});
		$('#sideinfo').css({
			'diplay': '',
			'pointer-events':'auto'
		});
		$('#sideoff').html('▲');
		$('#sideoff').css({
			'backgroundColor':'rgba(0,0,0,1.0)',
			'border':'1px solid rgba(152,152,152,1.0)'
		});
		side_check = 0;
	}

	function closebar() {
		/*$('#sidebar').css({
			'height':'0px'
		});*/
		$('#sideinfo').transition({
			opacity: 0.0,
		});
		$('#sideinfo').css({
			'diplay': 'none',
			'pointer-events':'none'
		});
		$('#sideoff').html('▼');
		$('#sideoff').css({
			'backgroundColor':'rgba(0,0,0,0.65)',
			'border':'1px solid rgba(152,152,152,0.65)'
		});
		side_check = 1;
	}

	function setbar() {
		console.log($('body').width());
		if($('body').width() >= (1225 + 41 * 2)) {
			sidebar_left = $('body').width() / 2 + 612.5 + 1;
			openbar();
		}
		else {
			sidebar_left = $('body').width() / 2 + 612.5 - 40;
			closebar();
		}
		jsidebar.offset({
			top: sidebar_top,
			left: sidebar_left
		});
	}

	function refresh() {
		if(sidebar_record < $('body').scrollTop()) {
			sidebar_rebound = sidebar_top + sidebar_buffer;
		} else {
			sidebar_rebound = sidebar_top - sidebar_buffer;
		}
		jsidebar.transition({
			y: (sidebar_rebound + $('body').scrollTop() - sidebar_top) + 'px'
		}).transition({
			y: (sidebar_top + $('body').scrollTop() - sidebar_top) + 'px'
		});
		sidebar_record = $('body').scrollTop();
	};
	$("#sideoff").click(function() {
		if(side_check == 0) {
			closebar();
		} else {
			openbar();
		}
	});
	//--------------------------------------------------------------------------------------------------------------------------------
	setbottom();

	function setbottom() {
		if($('body').width() < 1225) {
			var back_bottom_width = 1225;
		} else {
			var back_bottom_width = $('body').width();
		}
		$(".full_div").css({
			'width': back_bottom_width + 'px'
		});
		$(".full_div").offset({
			left: 0
		});
	}
})

function minitime() {
	var d = new Date();
	//console.log(d.getHours()+":"+d.getMinutes()+":"+d.getSeconds()+"."+d.getMilliseconds());
}

function openWin(qq) {
	if(qq == "") {
		alert("店家未設置客服!");
		win.close();
	} else {
		win.location.href = "http://wpa.qq.com/msgrd?v=3&uin=" + qq + "&site=qq&menu=yes";
	}
	//$("body").append($("<iframe src='http://wpa.qq.com/msgrd?v=3&uin="+qq+"&site=qq&menu=yes'></iframe>").hide().load(function(){$(this).contents().remove();}));
	//location.href = "javascript:open();";
	//location.href = "http://wpa.qq.com/msgrd?v=3&uin="+qq+"&site=qq&menu=yes";
	//window.setTimeout(function(){window.open("http://wpa.qq.com/msgrd?v=3&uin="+qq+"&site=qq&menu=yes",'_blank')},0)
	//window.open("http://wpa.qq.com/msgrd?v=3&uin="+qq+"&site=qq&menu=yes",'_blank');
	//$("<a id='qq' onclick='window.open(\"http://wpa.qq.com/msgrd?v=3&uin="+qq+"&site=qq&menu=yes\");' target='_blank'></a>")[0].click();
	//$("<form action='http://wpa.qq.com/msgrd?v=3' method='get'><input type='hidden' value='haha'></form>").submit();
}
var win;
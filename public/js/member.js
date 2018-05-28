//member_register
;(function($){
    $.fn.birthday = function(options){
	var opts = $.extend({}, $.fn.birthday.defaults, options);//整合参数
	var $year = $(this).find("select[name="+ opts.year +"]");
	var $month = $(this).find("select[name="+ opts.month +"]");
	var $day = $(this).find("select[name="+ opts.day +"]");
	MonHead = [31,28,31,30,31,30,31,31,30,31,30,31];
	return this.each(function(){
	    var y = new Date().getFullYear();
	    var con = "";
	    //添加年份
	    for(i = y; i >= (y-80); i--){
		con += "<option value='"+i+"'>"+i+"</option>";
	    }
	    $year.append(con);
	    con = "";
	    //添加月份
	    for(i = 1;i <= 12; i++){
		con += "<option value='"+i+"'>"+i+"</option>";
	    }
	    $month.append(con);
	    con = "";
	    //添加日期
	    var n = MonHead[0];//默认显示第一月
	    for(i = 1; i <= n; i++){
		con += "<option value='"+i+"'>"+i+"</option>";
	    }
	    $day.append(con);
	    $.fn.birthday.change($(this));
	    
	});
    };
    $.fn.birthday.change = function(obj){
	obj.children("select[name="+ $.fn.birthday.defaults.year +"],select[name="+ $.fn.birthday.defaults.month +"]").change(function(){
	    var $year = obj.children("select[name="+ $.fn.birthday.defaults.year +"]");
	    var $month = obj.children("select[name="+ $.fn.birthday.defaults.month +"]");
	    var $day = obj.children("select[name="+ $.fn.birthday.defaults.day +"]");
	    $day.empty();
	    var selectedYear = $year.find("option:selected").val();
	    var selectedMonth = $month.find("option:selected").val();
	    if(selectedMonth == 2 && $.fn.birthday.IsRunYear(selectedYear)){//如果是闰年
		var c ="";
		for(var i = 1; i <= 29; i++){
		    c += "<option value='"+i+"'>"+i+"日"+"</option>";
		}
		$day.append(c);
	    }else {//如果不是闰年也没选2月份
		var c = "";
		for(var i = 1; i <= MonHead[selectedMonth-1]; i++){
		    c += "<option value='"+i+"'>"+i+"日"+"</option>";
		}
		$day.append(c);
	    }
	});
    };
    $.fn.birthday.IsRunYear = function(selectedYear){
	return(0 == selectedYear % 4 && (selectedYear%100 != 0 || selectedYear % 400 == 0));
    };
    $.fn.birthday.defaults = {
	year:"year",
	month:"month",
	day:"day"
    };
})(jQuery);

$(document).ready(function(){
    
        var global_url = location.href;
        
        //-------------------------member通用 - start-------------------------//
        $("#member_center").css({"cursor":"pointer"}).click(function(){
                location.href="http://www.crazy2go.com/member/center";
        });
        
        var member_menu_html=
                "<div><span>檢視我的訂單狀況</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/account'>變更帳戶資料</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/address'>設置常用收貨地址</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/appeal'>我要申訴</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/collect?type=goods&order=date&by=desc'>我的收藏</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/recommand'>為我推薦</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/historylog'>瀏覽記錄</span></div>"/*+
                "<div><span href='http://www.crazy2go.com/member/bonus'>紅利積分兌換</span></div>"*/;
        var member_sub_menu_html=
                "<div><span href='http://www.crazy2go.com/member/order_wait2pay'>待付款訂單</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/order_hadpaid'>已付款未出貨訂單</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/order_sendout'>已發貨訂單</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/order_confirm'>已收貨待確認訂單</span></div>"+
                "<div><span href='http://www.crazy2go.com/member/order_history'>歷史訂單</span></div>";
        $("#member_menu").html(member_menu_html);
        $("#member_menu div:first").append(member_sub_menu_html);
        $("#member_menu > div").each(function(index){
            var $this = $(this);
            $this.css({
                "display":"inline-block",
                "position":"relative",
                "padding-left":"1px",
                "width":"207px"
            });
            $("#member_menu > div > span").css({
                "display":"block",
                "margin":"10px 0px 10px 27px"
            });
            if(index != 0)
            {
                var param = $this.find("span").attr("href").split("?")[0].split("/");
                param = param[param.length-1];
                if(global_url.search(param)!=-1)
                {
                    $this.css({
                        "cursor":"default",
                        "color":"white",
                        "background":"#EB3339 url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                        "border-left":"0px solid #EB3339",
                        "width":"209px",
                        "padding-left":"0px",
                        "left":"-1px"
                    });
                    $this.append("<div class='shadow'></div>");
                    $this.find(".shadow").css({
                        "position":"absolute",
                        "left":"0px",
                        "top":"0px",
                        "width":$this.width()+"px",
                        "height":$this.height()+"px",
                        "-webkit-box-shadow":"#CCC 0px 2px 3px",
                        "-moz-box-shadow":"#CCC 0px 2px 3px",
                        "z-index":"9"
                    });
                }
                else
                {
                    $this.css({
                        "border-top":"1px solid #D4D4D4",
                        "cursor":"pointer"
                    });
                    $this.hover(function(){
                        $this.css({
                            "background":"#F6F6F6 url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                            "border-left":"2px solid #EC5256",
                            "padding-left":"0px",
                            "width":"207px",
                            "left":"-1px"
                        });
                    },function(){
                        $this.css({
                            "background":"#FFFFFF",
                            "border-left":"0px solid #EC5256",
                            "padding-left":"1px",
                            "width":"207px",
                            "left":"0px"
                        });
                    });
                    $this.click(function(){
                        location.href = $this.find("span").attr("href");
                    })
                }
            }
        });
        
        $("#member_menu div:first > div").each(function(idx){
            var $this = $(this);
            if(idx == 0)
            {
                $this.css({
                    "margin-top":"5px"
                }); 
            }
            $this.css({
                "cursor":"pointer",
                "position":"relative",
                "left":"-1px",
                "padding-left":"37px",
                "line-height":"36px",
                "color":"#969B9C",
                "font-size":"8pt",
                "font-weight":"normal"
            });
            var param = $this.find("span").attr("href").split("?")[0].split("/");
            param = param[param.length-1];
            if(global_url.search(param)!=-1)
            {
                $this.css({
                    "cursor":"default",
                    "color":"white",
                    "background":"#EB3339 url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                    "border-left":"0px solid #EB3339",
                    "padding-left":"35px",
                    "left":"-1px",
                    "width":"173px"
                })
                $this.append("<div class='shadow'></div>");
                $this.find(".shadow").css({
                    "position":"absolute",
                    "left":"0px",
                    "top":"0px",
                    "width":$this.width()+"px",
                    "height":$this.height()+"px",
                    "padding-left":"35px",
                    "-webkit-box-shadow":"#CCC 0px 2px 3px",
                    "-moz-box-shadow":"#CCC 0px 2px 3px",
                    "z-index":"9"
                });
            }
            else
            {
                $this.hover(function(){
                    $this.css({
                        "background":"#F6F6F6  url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                        "border-left":"2px solid #EC5256",
                        "padding-left":"35px",
                        "left":"-1px"
                    });
                },function(){
                    $this.css({
                        "background":"#FFFFFF",
                        "border-left":"0px solid #EC5256",
                        "padding-left":"36px",
                        "left":"0px"
                    });
                });
                
                $this.click(function(){
                    location.href = $this.find("span").attr("href");
                })
            }
        });
        
        //-------------------------member通用 - start-------------------------//

        //-------------------------member_center - start-------------------------//
        //置換大頭貼貼圖
        if($("#member_picture_back")[0])
        {
            $("#member_picture").css({"opacity":"0"});
        }
        $("#member_picture_cover").hide();
        $("#member_picture_pick").hover(function(){
            $("#member_picture_cover").show();
        },function(){
            $("#member_picture_cover").hide();
        });
        $("#member_picture_pick").change(function(evt) {
            var f = evt.target.files[0];
            if(!f.type.match('image.*')) {
                return;
            }
            if(f.size>(1024*1024)/2)
            {
                alert("檔案大小超過 512 KB");
                return;
            }
            
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    $("<img src='"+e.target.result+"'>")[0].onload=function(){
                        if(this.width>100 || this.height>100)
                        {
                            alert("請上傳寬高小於 100 x 100 之圖檔！");
                            return;
                        }
                        $("#member_picture").attr("title",$("#member_picture").attr("src"));
                        $("#member_picture").attr("src",e.target.result);
                        $("#member_picture_back").hide();
                        $("#member_picture").css({"opacity":"1"});
                        if(!$("#confirm_update_pic")[0])
                        {
                            $("#member_picture").after("<div style='text-align:center;margin-top:5px;'><input id='confirm_update_pic' type='button' value='點擊確認更新'></div>");
                            $("#confirm_update_pic").click(function(){
                                var form_data = new FormData();
                                form_data.append('file', theFile);
                                $.ajax({
                                    url: "http://www.crazy2go.com/member/ajax_change_sticker",
                                    dataType: 'json',
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    data: form_data,                         
                                    type: 'post',
                                    success: function(data){
                                        alert(data.message);    
                                        if(data.error == 0) {
                                            $("#confirm_update_pic").remove();
                                        }
                                    }
                                });
                            });
                        }
                    };
                };
            })(f);
            reader.readAsDataURL(f);
        });
        
        //廣告選擇
        $("#ad_left").click(function(){
            $("#center_ad").prepend($("#center_ad a:last"));
            $("#center_ad a:first").css({"top":"0px"});
            $("#center_ad").prepend($("#center_ad a:last"));
            $("#center_ad a:first").css({"top":$("#center_ad a:first").height()+"px"});
        })
        $("#ad_right").click(function(){
            $("#center_ad").append($("#center_ad a:first"));
            $("#center_ad a:last").css({"top":$("#center_ad a:last").height()+"px"});
            $("#center_ad").append($("#center_ad a:first"));
            $("#center_ad a:last").css({"top":"0px"});
        }).trigger("click").trigger("click");
        
        //四大面板紅框
        $(".mem_panel").hover(function(){
            $(this).css({
                "border":"1px solid #FF0000",
                "z-index":"2"
            });
        },function(){
            $(this).css({
                "border":"1px solid #D4D4D4",
                "z-index":"1"
            });
        });
        
        //瀏覽紀錄-加入蒐藏
        $(".add_goods_collect").click(function(){
            var $this = $(this);
            $.post("http://www.crazy2go.com/cart/ajax_collect_goods", {fi_no:$this.attr("fi_no")}, function(data) {
                alert(data.message);
            }, "json");
        });
        
        //熱銷商品 ＆ 猜你喜歡
        $("#product_hot,#product_guess").each(function(){
            var $this = $(this);
            var i = 0;
            $this.find(".product_left,.product_right").click(function(){
                $this.find(".product_list_up").html("");
                $this.find(".product_list_down").html("");
                var l = $this.find(".product_show > a").size();
                if(l<=4)
                {
                   for(i = 0; i<l; i++)
                    {
                        $this.find(".product_list_up").append($this.find(".product_show > a:first").clone());
                        $this.find(".product_show").append($this.find(".product_show > a:first"));
                    } 
                }
                else if(l>4)
                {
                    if(l<=8)
                    {
                        for(i = 0; i<4; i++)
                        {
                            $this.find(".product_list_up").append($this.find(".product_show > a:first").clone());
                            $this.find(".product_show").append($this.find(".product_show > a:first"));
                        }
                        for(i = 0; i<l-4; i++)
                        {
                            $this.find(".product_list_down").append($this.find(".product_show > a:first").clone());
                            $this.find(".product_show").append($this.find(".product_show > a:first"));
                        }
                    }
                    else
                    {
                        for(i = 0; i<4; i++)
                        {
                            $this.find(".product_list_up").append($this.find(".product_show > a:first").clone());
                            $this.find(".product_show").append($this.find(".product_show > a:first"));
                        }
                        for(i = 0; i<4; i++)
                        {
                            $this.find(".product_list_down").append($this.find(".product_show > a:first").clone());
                            $this.find(".product_show").append($this.find(".product_show > a:first"));
                        }
                    }
                }
            });
            $this.find(".product_left").trigger("click");
        });
        
        
        //-------------------------member_center - end-------------------------//

        //-------------------------member - start-------------------------//
        //表格驗證
	$("#login").validate({
		rules: {
			account: "required",
			password: "required",
                        verification: "required"
		},
		messages: {
			account: "請輸入帳號",
			password: "請輸入密碼",
                        verification: "請輸入驗證碼"
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_login", {account:$("#account").val(), password:$("#password").val(), verification:$("#verification").val()}, function(data) {
				if(data.error == 0 || data.error == 2) {
					location.href = 'http://www.crazy2go.com/member/center';
				}
				alert(data.message);
			}, "json");
		}
	});
        
        //-------------------------member - end-------------------------//
        
        //-------------------------member_register & member_account - start-------------------------//
        
        //預設步驟1
        $("#id").blur(function(){
            if($(this).val().length>0)
            {
                $("#step1").css({
                   "background":"#A5A5A5" 
                });
                $("#step2").css({
                   "background":"url(http://www.crazy2go.com/public/img/template/arrow3.jpg)" 
                });
                $("#step3").css({
                    "background":"#EE3B3B" 
                });
                $("#step4").css({
                   "background":"url(http://www.crazy2go.com/public/img/template/arrow1.jpg)" 
                });
            }
            else
            {
                $("#step1").css({
                   "background":"#EE3B3B" 
                });
                $("#step2").css({
                   "background":"url(http://www.crazy2go.com/public/img/template/arrow1.jpg)" 
                });
                $("#step3").css({
                   "background":"#A5A5A5" 
                });
                $("#step4").css({
                   "background":"url(http://www.crazy2go.com/public/img/template/arrow2.jpg)" 
                });
            }
        }).trigger("blur");
        
        // select元件
        $("#birthday_container").birthday();
        $("#sex").change(function(){
            $("#sex_text").text($(this).find(":checked").text());
        }).trigger("change");
        $("#bdy").change(function(){
            $("#bdy_text").text($(this).find(":checked").text());
        }).trigger("change");
        $("#bdm").change(function(){
            $("#bdm_text").text($(this).find(":checked").text());
        }).trigger("change");
        $("#bdd").change(function(){
            $("#bdd_text").text($(this).find(":checked").text());
        }).trigger("change");
        
        //member_account
        if(global_url.search("register")!=-1)
        {
            //註冊驗證
            $("#register").validate({
                    rules: {
                            id: {
                                    required: true,
                                    rangelength: [6,20]
                            },
                            email: {
                                    required: true,
                                    email: true
                            },
                            password: {
                                    required: true,
                                    rangelength: [8,16]
                            },
                            repassword: {
                                    required: true,
                                    rangelength: [8,16],
                                    equalTo: "#password"
                            },
                            phone_head: {
                                    required: true,
                                    rangelength: [2,2],
                                    number: true
                            },
                            phone_body: {
                                    required: true,
                                    rangelength: [11,11],
                                    number: true
                            },
                            agree:"required"
                    },
                    messages: {
                            id: {
                                    required: "請輸入帳號",
                                    rangelength: "帳號長度不得小於6碼或大於20碼"
                            },
                            email: "請輸入信箱",
                            password: {
                                    required: "請輸入密碼",
                                    rangelength: "密碼長度不得小於8碼或大於16碼"
                            },
                            repassword: {
                                    required: "請輸入確認密碼",
                                    rangelength: "確認密碼長度不得小於8碼或大於16碼",
                                    equalTo: "密碼與確認密碼不相同"
                            },
                            phone_head: {
                                    required: "請輸入國際號",
                                    rangelength: "長度為2碼",
                                    number: "國際號僅能輸入數字"
                            },
                            phone_body: {
                                    required: "請輸入手機",
                                    rangelength: "手機長度為11碼",
                                    number: "手機號碼僅能輸入數字"
                            },
                            agree:"請同意服務協議"
                    },
                    submitHandler: function() {
                            $.post("http://www.crazy2go.com/member/ajax_register", {
                                id:$("#id").val(), 
                                email:$("#email").val(), 
                                password:$("#password").val(), 
                                phone_head:$("#phone_head").val(),
                                phone_body:$("#phone_body").val(),
                                sex:$("#sex").val(),
                                bdy:$("#bdy").val(),
                                bdm:$("#bdm").val(),
                                bdd:$("#bdd").val(),
                                qq:$("#qq").val()
                            }, function(data) {
                                    alert(data.message);
                                    if(data.error == 0) {
                                            location.href = 'http://www.crazy2go.com/member/register2/';
                                    }
                            }, "json");
                    }
            });
        }
        //member_account
        if(global_url.search("account")!=-1)
        {
                $("#register").validate({
                    rules: {
                            id: {
                                    required: true,
                                    rangelength: [6,20]
                            },
                            password: {
                                    required: false,
                                    rangelength: [8,16]
                            },
                            repassword: {
                                    required: false,
                                    rangelength: [8,16],
                                    equalTo: "#password"
                            },
                            email: {
                                    required: true,
                                    email: true
                            },
                            phone_head: {
                                    required: true,
                                    rangelength: [2,2],
                                    number: true
                            },
                            phone_body: {
                                    required: true,
                                    rangelength: [11,11],
                                    number: true
                            },
                            agree:"required"
                    },
                    messages: {
                            id: {
                                    required: "請輸入帳號",
                                    rangelength: "帳號長度不得小於6碼或大於20碼"
                            },
                            password: {
                                    required: "請輸入密碼",
                                    rangelength: "密碼長度不得小於8碼或大於16碼"
                            },
                            repassword: {
                                    required: "請輸入確認密碼",
                                    rangelength: "確認密碼長度不得小於8碼或大於16碼",
                                    equalTo: "密碼與確認密碼不相同"
                            },
                            email: "請輸入信箱",
                            phone_head: {
                                    required: "請輸入國際號",
                                    rangelength: "長度為2碼",
                                    number: "國際號僅能輸入數字"
                            },
                            phone_body: {
                                    required: "請輸入手機",
                                    rangelength: "手機長度為11碼",
                                    number: "手機號碼僅能輸入數字"
                            },
                            agree:"請同意服務協議"
                    },
                    submitHandler: function() {
                            $.post("http://www.crazy2go.com/member/ajax_member_register", {
                                id:$("#id").val(), 
                                email:$("#email").val(), 
                                password:$("#password").val(),
                                repassword:$("#repassword").val(),
                                phone_head:$("#phone_head").val(),
                                phone_body:$("#phone_body").val(),
                                sex:$("#sex").val(),
                                bdy:$("#bdy").val(),
                                bdm:$("#bdm").val(),
                                bdd:$("#bdd").val(),
                                qq:$("#qq").val()
                            }, function(data) {
                                    alert(data.message);
                                    if(data.error == 0) {
                                        location.href = location.href;
                                    }
                            }, "json");
                    }
            });
            
            $.post("http://www.crazy2go.com/member/ajax_get_register",function(data){
                if(data.error==0)
                {
                    console.log(data);
                    $("#id").val(data.add.id), 
                    $("#email").val(data.add.email), 
                    $("#phone_head").val(data.add.phone_head),
                    $("#phone_body").val(data.add.phone_body),
                    $("#sex").val(data.add.sex),
                    $("#bdy").val(data.add.bdy),
                    $("#bdm").val(parseInt(data.add.bdm)),
                    $("#bdd").val(parseInt(data.add.bdd)),
                    $("#qq").val(data.add.qq);

                    $("#sex_text").text($("#sex option:selected").text());
                    $("#bdy_text").text($("#bdy option:selected").text());
                    $("#bdm_text").text($("#bdm option:selected").text());
                    $("#bdd_text").text($("#bdd option:selected").text());
                    
                    $("#id").attr("disabled","disabled");
                    $("#email").attr("disabled","disabled");
                    $("#phone_head").attr("disabled","disabled");
                    $("#phone_body").attr("disabled","disabled");
                    
                    $("#id").css({"border":"0px solid gray"});
                    $("#email").css({"border":"0px solid gray"});
                    $("#phone_head").css({"border":"0px solid gray"});
                    $("#phone_body").css({"border":"0px solid gray"});
                }
            }, "json");
        }
        
        //-------------------------member_register & member_account - end-------------------------//
        
        //-------------------------member_register2 - start-------------------------//
        $("#register2").validate({
		rules: {
                        valiCode:{
                            required: true
                        }
		},
		messages: {
                        valiCode:{
                            required: "請輸入驗證碼"
                        }
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_register2?"+location.href.split("?")[1],{
                            key:$("#valiCode").val()
                        }, function(data) {
				if(data.error == 0) {
                                    location.href = 'http://www.crazy2go.com/';
				}
                                $("#valiMsg").text(data.message);
				alert(data.message);
			}, "json");
		}
	});
        
        $("#valiCode").focus(function(){
            $("#valiMsg").text("");
        });
        //-------------------------member_register2 - end-------------------------//
	
	$("#logout").click(function() {
		$.post("http://www.crazy2go.com/member/ajax_logout", function(data) {
			if(data.error == 0) {
				location.href = 'http://www.crazy2go.com/member/';
			}
			alert(data.message);
		}, "json");
	});
	
        //-------------------------member_order_wait2pay - start-------------------------//
        if(global_url.search("order_wait2pay")!=-1)
        {
            function createDialog(){
                $("body").append($("<div id='bg_cover'><div id='cover_content'></div></div>").css({
                    "position":"fixed",
                    "display":"inline-block",
                    "background":"rgba(0,0,0,0.8)",
                    "left":"0px",
                    "top":"0px",
                    "right":"0px",
                    "bottom":"0px",
                    "z-index":"999999"
                }));
                $("#cover_content").css({
                    "position":"absolute",
                    "display":"inline-block",
                    "background":"white",
                    "top":"120px",
                    "bottom":"120px",
                    "width":"840px",
                    "z-index":"1000000",
                    "padding":"20px",
                    "overflow-y":"scroll"
                });
                //$("#bg_cover").hide();
                $(window).unbind("resize");
                $(window).on("resize.dialog",function(){
                    $("#cover_content").css({
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+"px"
                    });
                    $("#close_cover").css({
                        "position":"fixed",
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+$("#cover_content").width()-3+"px"
                    });
                })
            }
            
            $("#selectAll").click(function(){
                $("input[id^=order_check]").prop("checked",!$("input[id^=order_check]").prop("checked"));
            });
            
            $("#combinePay").click(function(){
                if($("input[id^=order_check]:checked").size()==0)
                {
                    alert("請勾選訂單！");
                    return;
                }
                $.post("http://www.crazy2go.com/member/ajax_order_bonus",function(data){
                    createDialog();
                    var content = "";
                    
                    content += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";

                    content +=  "<div style='font-size:18pt;font-weight:bold;padding:10px 0px;'><img src='http://www.crazy2go.com/public/img/template/money_icon.png' style='position:relative;top:5px;'> 我要付款：</div>";

                    var subtotal = 0;
                    var currency = data.add.sum_currency;

                    $("div[class='table']").each(function(){
                        var $table = $(this);
                        if($table.find("input[id^=order_check]:checked")[0])
                        {
                            var seller = $table.attr("seller");
                            var order = $table.attr("order");
                            var single_price = $table.attr("single_price").split("∵");
                            var product_num = $table.attr("product_num").split("∵");
                            var product_name = $table.attr("product_name").split("∵");
                            var subtotal_with_shipping = $table.attr("subtotal_with_shipping");
                            var len = single_price.length;
                            var subtotal2 = parseFloat(subtotal_with_shipping);
                            subtotal += parseFloat(subtotal_with_shipping);
                            
                            content +=  "<div class='table' style='border-bottom:1px solid #C1C1C1;'>"+
                                            "<div class='tr' style='border-bottom:2px solid #7F7F7F;font-weight:bold;color:#7F7F7F;'>"+
                                                "<div class='td'>賣家："+seller+"｜訂單編號："+order+"</div>"+
                                                "<div class='td'>　</div>"+
                                                "<div class='td'>單價</div>"+
                                                "<div class='td'>數量</div>"+
                                                "<div class='td'>總計</div>"+
                                            "</div>";
                                        for(var i=0; i<len; i++)
                                        {
                                            content +=  "<div class='tr' style='color:#C1C1C1;line-height:20px;'>"+
                                                            "<div class='td' style='border-right:1px solid #C1C1C1;margin-right:10px;'>"+product_name[i]+"</div>"+
                                                            "<div class='td'></div>"+
                                                            "<div class='td'><span style='font-family:Arial;'>¥</span>"+single_price[i]+"</div>"+
                                                            "<div class='td'>"+product_num[i]+"</div>"+
                                                            "<div class='td'><span style='font-family:Arial;'>¥</span>"+single_price[i]*product_num[i]+"</div>"+
                                                        "</div>";
                                            subtotal += single_price[i]*product_num[i];
                                            subtotal2 += single_price[i]*product_num[i];
                                        }
                            content +=  "</div>";
                            
                            //content +=  "<div><div style='float:right;border:1px solid black;margin:10px;padding:10px;font-weight:bold;'>總計（含運費）：<span style='font-family:Arial;'>¥</span>"+subtotal2+"</div></div>";
                            //content +=  "<div style='clear:both;height:0px;'></div>";
                        }
                    });
                    
                    
                    //content +=  "<div><div style='float:right;border:1px solid #C1C1C1;margin:10px;padding:10px;font-weight:bold;'>你的跨寶通消費金共<span style='font-family:Arial;color:red;'>¥"+currency+"</span>元　<input style='float:right;border:1px solid #C1C1C1;color:#C1C1C1;' placeholder='欲抵扣消費金'> </div></div>";
                    //content +=  "<div style='clear:both;height:0px;'></div>";
                    content +=  "<div><div style='float:right;border:2px solid #EA332C;margin:10px;padding:10px;font-weight:bold;'>訂單總額（含運費）：<span style='font-family:Arial;color:red;font-size:14pt;'>¥"+subtotal+"</span></div></div>";
                    content +=  "<div style='clear:both;height:0px;'></div>";
                    content +=  "<div>選擇付款方式：O<img src='http://www.crazy2go.com/public/img/template/visa.jpg' style='position:relative;top:5px;'>國際信用卡　　　　O<img src='http://www.crazy2go.com/public/img/template/icon.jpg' style='position:relative;top:5px;'>支付寶支付</div>";
                    content +=  '<div style="text-align:center"><input id="button" type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(http://www.crazy2go.com/public/img/template/button3.png)"></div>';
                    $("#cover_content").html(content);
                    $(window).trigger("resize");
                },"json");
                
            });
            
            $(".pay_button").click(function(){
                var $this = $(this);
                $.post("http://www.crazy2go.com/member/ajax_order_bonus",function(data){
                    createDialog();
                    var $table = $this.parent().parent().parent();
                    var seller = $table.attr("seller");
                    var order = $table.attr("order");
                    var single_price = $table.attr("single_price").split("∵");
                    var product_num = $table.attr("product_num").split("∵");
                    var product_name = $table.attr("product_name").split("∵");
                    var subtotal_with_shipping = $table.attr("subtotal_with_shipping");
                    var len = single_price.length;
                    var currency = data.add.sum_currency;
                    var subtotal = parseFloat(subtotal_with_shipping);

                    var content = "";
                    content += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";

                    content +=  "<div style='font-size:18pt;font-weight:bold;padding:10px 0px;'><img src='http://www.crazy2go.com/public/img/template/money_icon.png' style='position:relative;top:5px;'> 我要付款：</div>";

                    content +=  "<div class='table' style='border-bottom:1px solid #C1C1C1;'>"+
                                    "<div class='tr' style='border-bottom:2px solid #7F7F7F;font-weight:bold;color:#7F7F7F;'>"+
                                        "<div class='td'>賣家："+seller+"｜訂單編號："+order+"</div>"+
                                        "<div class='td'>　</div>"+
                                        "<div class='td'>單價</div>"+
                                        "<div class='td'>數量</div>"+
                                        "<div class='td'>總計</div>"+
                                    "</div>";
                                for(var i=0; i<len; i++)
                                {
                                    content +=  "<div class='tr' style='color:#C1C1C1;line-height:20px;'>"+
                                                    "<div class='td' style='border-right:1px solid #C1C1C1;margin-right:10px;'>"+product_name[i]+"</div>"+
                                                    "<div class='td'></div>"+
                                                    "<div class='td'><span style='font-family:Arial;'>¥</span>"+single_price[i]+"</div>"+
                                                    "<div class='td'>"+product_num[i]+"</div>"+
                                                    "<div class='td'><span style='font-family:Arial;'>¥</span>"+single_price[i]*product_num[i]+"</div>"+
                                                "</div>";
                                    subtotal += single_price[i]*product_num[i];
                                }
                    content +=  "</div>";

                    //content +=  "<div><div style='float:right;border:1px solid black;margin:10px;padding:10px;font-weight:bold;'>總計（含運費）：<span style='font-family:Arial;'>¥</span>"+subtotal+"</div></div>";
                    //content +=  "<div style='clear:both;height:0px;'></div>";
                    //content +=  "<div><div style='float:right;border:1px solid #C1C1C1;margin:10px;padding:10px;font-weight:bold;'>你的跨寶通消費金共<span style='font-family:Arial;color:red;'>¥"+currency+"</span>元　<input style='float:right;border:1px solid #C1C1C1;color:#C1C1C1;' placeholder='欲抵扣消費金'> </div></div>";
                    //content +=  "<div style='clear:both;height:0px;'></div>";
                    content +=  "<div><div style='float:right;border:2px solid #EA332C;margin:10px;padding:10px;font-weight:bold;'>訂單總額（含運費）：<span style='font-family:Arial;color:red;font-size:14pt;'>¥"+subtotal+"</span></div></div>";
                    content +=  "<div style='clear:both;height:0px;'></div>";
                    content +=  "<div>選擇付款方式：<img src='http://www.crazy2go.com/public/img/goods/check_button.png' style='position:relative;top:2px;'><img src='http://www.crazy2go.com/public/img/template/visa.jpg' style='position:relative;top:5px;'>國際信用卡　　　　<img src='http://www.crazy2go.com/public/img/goods/uncheck_button.png' style='position:relative;top:2px;'><img src='http://www.crazy2go.com/public/img/template/icon.jpg' style='position:relative;top:5px;'>支付寶支付</div>";
                    content +=  '<div style="text-align:center"><input id="button" type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(http://www.crazy2go.com/public/img/template/button3.png)"></div>';
                    $("#cover_content").html(content);
                    $(window).trigger("resize");
                },"json");
                
            });
            
            $(".cancel_order_button").click(function(){
                if(confirm("確定取消訂單?"))
                {
                    var $table = $(this).parent().parent().parent();
                    var order_fi_no = $table.attr("order_fi_no");
                    $.post("http://www.crazy2go.com/member/ajax_order_cancel",{
                        order: order_fi_no
                    },function(data){
                        alert(data.message);
                        location.href = location.href;
                    },"json");
                }
            });
            
            $("#cancelOrder").click(function(){
                if($("input[id^=order_check]:checked").size()==0)
                {
                    alert("請勾選訂單！");
                    return;
                }
                if(confirm("確定取消訂單?"))
                {
                    var fi_nos = [];
                    $("div[class='table']").each(function(){
                        var $table = $(this);
                        if($table.find("input[id^=order_check]:checked")[0])
                        {
                            fi_nos.push($table.attr("order_fi_no"));
                        }
                    });
                    $.post("http://www.crazy2go.com/member/ajax_order_cancel",{
                        order: fi_nos.join(",")
                    },function(data){
                        alert(data.message);
                        location.href = location.href;
                    },"json");
                }
            });
        }
        //-------------------------member_order_wait2pay - end-------------------------//
        
        
        
        //-------------------------member_order_hadpaid/member_order_confirm - start-------------------------//
        
        if(global_url.search("order_hadpaid")!=-1 || global_url.search("order_confirm")!=-1)
        {
            function createDialog(){
                $("body").append($("<div id='bg_cover'><div id='cover_content'></div></div>").css({
                    "position":"fixed",
                    "display":"inline-block",
                    "background":"rgba(0,0,0,0.8)",
                    "left":"0px",
                    "top":"0px",
                    "right":"0px",
                    "bottom":"0px",
                    "z-index":"999999"
                }));
                $("#cover_content").css({
                    "position":"absolute",
                    "display":"inline-block",
                    "background":"white",
                    "top":"120px",
                    "bottom":"120px",
                    "width":"840px",
                    "z-index":"1000000",
                    "padding":"20px",
                    "overflow-y":"scroll"
                });
                //$("#bg_cover").hide();
                $(window).unbind("resize");
                $(window).on("resize.dialog",function(){
                    $("#cover_content").css({
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+"px"
                    });
                    $("#close_cover").css({
                        "position":"fixed",
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+$("#cover_content").width()-3+"px"
                    });
                })
            }
            
            $(".transaction_complete").click(function(){
                    $.post("http://www.crazy2go.com/member/ajax_order_transaction_complete",{
                        order:$(this).attr("order")
                    },function(data){
                        alert(data.message);
                        location.href = location.href;
                    },"json");
            });
            
            $(".rank_seller").click(function(){
                var $this = $(this);
                $.post("http://www.crazy2go.com/member/ajax_order_rank",{
                    order:$this.attr("order")
                },function(data){
                    if(data.add.date_diff > 3){
                        alert("超過3天不能評價！");
                        return;
                    }
                    else
                    {
                        alert("距收貨時間3天內可評價");
                    }    
                    createDialog();
                    var is_ranked = data.add.goods[0].evaluate == "0"?false:true;
                    var display = "";
                    display += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";
                    display += "<img src='http://www.crazy2go.com/public/img/template/icon_evaluate.png' style='position:relative;top:5px;'> <span style='font-size:16pt;'>評論賣家：</span>";
                    
                    display += "<div class='table' style='margin-top:10px;line-height:25px;'>";
                    display += "<div class='tr' style='color:#7F7F7F;font-weight:bold;border-bottom:2px solid #7F7F7F;'><div class='td' style='width:400px;'>賣家："+$this.attr("seller")+"｜訂單編號："+$this.attr("sn")+"</div><div class='td' style='width:170px;'>評價</div><div class='td' style='width:260px;'>留言</div></div>";
                    
                    for(var i in data.add.goods)
                    {
                        display += "<div class='tr' goods='"+data.add.goods[i].goods+"' star='"+data.add.goods[i].evaluate+"' content='' style='border-bottom:1px solid #D3D3D3;'><div class='td' style='color:#A8A8A8;'>"+data.add.goods[i].name+"</div><div class='td'>";
                        for(var j=0; j<6; j++)
                        display += "<img src='http://www.crazy2go.com/public/img/goods/star_gray.png' style='position:relative;top:3px;cursor:pointer;'>";
                        var content = !is_ranked?"<textarea style='width:250px;height:75px;border:1px solid #E5E5E5;margin:5px 0px;' placeholder='留言字數請勿超過兩百字!'></textarea>":"["+data.add.goods[i].evaluate_date+"] "+data.add.goods[i].evaluate_context;
                        display += "&nbsp;</div><div class='td'>"+content+"</div></div>";
                    }
                    
                    display += "</div>";
                    
                    display += '<div style="text-align:right;"><input id="button" type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(http://www.crazy2go.com/public/img/template/button3.png)"></div>';
                    
                    $("#cover_content").html(display);
                    
                    $("#cover_content").find("div.tr").not(":first").each(function(){
                        var $tr = $(this);
                        $tr.find("img").each(function(){
                            $(this).click(function(){
                                $tr.find("img").attr("src","http://www.crazy2go.com/public/img/goods/star_gray.png");
                                for(var i = 0, len=$(this).index()+1;i<len;i++)
                                {
                                    $tr.find("img").eq(i).attr("src","http://www.crazy2go.com/public/img/goods/star_red.png")
                                }
                                $tr.attr("star",len);
                            });
                        });
                        if($tr.attr("star")=="0")
                        {
                            $tr.find("img:nth-child(3)").trigger("click");
                        }
                        else
                        {
                            $tr.find("img:nth-child("+$tr.attr("star")+")").trigger("click");
                        }
                        
                        $tr.find("textarea").blur(function(){
                            if($(this).val().length>200)
                            {
                                alert("最多200字！");
                                $(this).val($(this).val().substring(0,200));
                            }
                            $tr.attr("content",$(this).val());
                        });
                        if(is_ranked)
                        {
                            $tr.find("img").unbind("click");
                        }
                    });
                    
                    
                    $("#cover_content #button").click(function(){
                        var goods_evaluate = [];
                        $("#cover_content").find("div.tr").not(":first").each(function(){
                            goods_evaluate.push([$(this).attr("goods"),$(this).attr("star"),$(this).attr("content")]);
                        });
                        
                        $.post("http://www.crazy2go.com/member/ajax_order_rank_update",{
                            order:$this.attr("order"),
                            goods_evaluate:JSON.stringify(goods_evaluate)
                        },function(data){
                            alert(data.message);
                            location.href = location.href;
                        },"json");
                    })
                    
                    if(is_ranked)
                    {
                        $("#cover_content #button").remove();
                    }
                    
                    $(window).resize();
                },"json")
            });
            
            $(".check_logistics").click(function(){
                var $this = $(this);
                createDialog();
                $.post("http://www.crazy2go.com/member/ajax_order_logistics",{
                    order:$this.attr("order")
                },function(data){
                    var display = "";
                    var trace = $.parseJSON(data.add.trace[0].trace);
                    var traceArray = [];
                    var logistics = "";
                    var logistics_url = "";
                    var logistics_sn = "";
                    for(var i in trace)
                    {
                        switch(trace[i][0])
                        {
                            case "1":logistics = "台灣直郵";logistics_url = "http://www.sf-express.com/cn/sc/dynamic_functions/waybill/";break;
                            case "2":logistics = "順豐快遞";logistics_url = "http://www.sf-express.com/cn/sc/dynamic_functions/waybill/";break;
                            case "3":logistics = "申通快遞";logistics_url = "http://www.sto.cn/";break;
                            case "4":logistics = "韻達快遞";logistics_url = "http://www.yundaex.com/";break;
                            case "5":logistics = "郵政EMS(特快)";logistics_url = "http://www.ems.com.cn/";break;
                        }
                        logistics_sn = trace[i][1];
                        traceArray.push([i,logistics,logistics_url,logistics_sn]);
                    }
                    traceArray.reverse();
                    
                    display += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";
                    display += "<img src='http://www.crazy2go.com/public/img/template/car.png' style='position:relative;top:5px;'> <span style='font-size:16pt;'>查看物流：</span><div style='border-bottom:2px solid #7F7F7F;margin:10px 0px;'></div>"
                    display +=  "<div class='table' style='color:#848484;'>"+
                                    "<div class='tr'><div class='td' style='width:100px;padding:5px;'>賣家：</div><div class='td' style='padding:5px;'>"+$this.attr("seller")+"</div></div>"+
                                    "<div class='tr'><div class='td' style='width:100px;padding:5px;'>訂單編號：</div><div class='td' style='padding:5px;'>"+$this.attr("sn")+"</div></div>";
                                for(var i=0, len=traceArray.length; i<len; i++)
                                {
                                    display +=  "<div class='tr'><div class='td' style='width:100px;padding:5px;'>出貨時間：</div><div class='td' style='padding:5px;'>"+traceArray[i][0]+"</div></div>"+
                                                "<div class='tr'><div class='td' style='width:100px;padding:5px;'>物流廠商：</div><div class='td' style='padding:5px;'>"+traceArray[i][1]+"</div></div>"+
                                                "<div class='tr'><div class='td' style='width:100px;padding:5px;'>物流編號：</div><div class='td' style='padding:5px;'>"+traceArray[i][3]+"<a href='"+traceArray[i][2]+"' target='_blank' style='display:inline-block;position:relative;top:-5px;left:50px;padding:5px;color:black;border:1px solid black;'>查詢物流進度</a></div></div>";
                                }
                    display +=  "</div>";
                    $("#cover_content").html(display);
                    
                    $(window).trigger("resize");
                    
                },"json");
            });
            
            $(".returns_button").click(function(){
                    var $this = $(this);
                    var post_data = "<form method='post' action='http://www.crazy2go.com/member/order_returns'>"
                            +"<input type='hidden' name='order' value='"+$this.attr("order")+"' />"
                            +"<input type='hidden' name='goods' value='"+$this.attr("goods")+"' />"
                            +"<input type='hidden' name='member' value='"+$this.attr("member")+"' />"
                            +"<input type='hidden' name='images' value='"+$this.attr("images").replace(/_/g, "∴")+"' />"
                            +"<input type='hidden' name='goods_name' value='"+$this.attr("goods_name")+"' />"
                            +"<input type='hidden' name='specifications' value='"+$this.attr("specifications").replace(/\"/g, "∵")+"' />"
                            +"<input type='hidden' name='price' value='"+$this.attr("price")+"' />"
                            +"<input type='hidden' name='number' value='"+$this.attr("number")+"' />"
                            +"<input type='hidden' name='shipping_fee' value='"+$this.attr("shipping_fee")+"' />"
                            +"<input type='hidden' name='date' value='"+$this.attr("date")+"' />"
                            +"<input type='hidden' name='sn' value='"+$this.attr("sn")+"' />"
                            +"<input type='hidden' name='store_name' value='"+$this.attr("store_name")+"' />"
                            +"<input type='hidden' name='store' value='"+$this.attr("store")+"' />"
                            +"</form>";

                    $(post_data).submit();
            });
            
            $(".remind_button").click(function(){
                    var $this = $(this);
                    $.post("http://www.crazy2go.com/member/ajax_order_remind",{
                        order:$this.attr("order")
                    },function(data){
                        alert(data.message);
                        $this.val("已提醒發貨");
                        $this.attr("disabled","disabled");
                    },"json");
            });
        }
        
        //-------------------------member_order_hadpaid/member_order_confirm - end-------------------------//
        
        //-------------------------member_order_returns - start-------------------------//
        
        if(global_url.search("order_returns")!=-1)
        {
            $("#reason_text").text($("#reason option:checked").text());
            $("#reason").change(function(){
                $("#reason_text").text($("#reason option:checked").text());
            });
            
            var progress = $("#progressStatus").attr("progress");
            switch(progress)
            {
                case '1':
                    $("#step_1").css({
                       "background":"#A1A1A1" 
                    });
                    $("#step_2").css({
                       "background":"url(http://www.crazy2go.com/public/img/template/arrow3.jpg)" 
                    });
                    $("#step_3").css({
                        "background":"#EE3B3B" 
                    });
                    $("#step_4").css({
                       "background":"url(http://www.crazy2go.com/public/img/template/arrow1.jpg)" 
                    });
                    break;
                case '2':
                case '3':
                    $("#step_1").css({
                        "background":"#A1A1A1" 
                    });
                    $("#step_2").css({
                        "background":"url(http://www.crazy2go.com/public/img/template/arrow2.jpg)" 
                    });
                    $("#step_3").css({
                        "background":"#A1A1A1" 
                    });
                    $("#step_4").css({
                        "background":"url(http://www.crazy2go.com/public/img/template/arrow3.jpg)" 
                    });
                    $("#step_5").css({
                        "background":"#EE3B3B" 
                    });
                    break;
                default:
                    $("#step_1").css({
                       "background":"#EE3B3B" 
                    });
                    $("#step_2").css({
                       "background":"url(http://www.crazy2go.com/public/img/template/arrow1.jpg)" 
                    });
                    $("#step_3").css({
                        "background":"#A1A1A1" 
                    });
                    $("#step_4").css({
                       "background":"url(http://www.crazy2go.com/public/img/template/arrow2.jpg)" 
                    });
                    break;
            }
            
            // 上傳圖片
            $("#return_image").change(function(evt) {
                $(this).after($(this).clone(true));
                $(this).remove();
                $("#show_img").html("");
                for(var i=0, f; f=evt.target.files[i]; i++) {
                    if(!f.type.match('image.*')) {
                        continue;
                    }
                    if(f.size>(1024*1024)/2)
                    {
                        alert("部分檔案大小超過 512 KB 未選取");
                        continue;
                    }
                    $("#return_image").data("file",f);
                    var reader = new FileReader();
                    reader.onload = (function(theFile) {
                        return function(e) {
                            $("#show_img").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                            $("#show_img img:last")[0].onload = function(){
                                var $this = $(this);
                                if(this.width>1920 || this.height>1080)
                                {
                                    $("#show_img").html("");
                                    alert("請上傳寬高小於 1920 x 1080 之圖檔！");
                                    return;
                                }
                                var filename = "#_"+$this.index()+"_"+this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[$this.attr("title").split(".").length-1];
                                $this.attr("title",filename);                                
                                $this.css({
                                    "border":"1px solid gray",
                                    "margin":"5px"
                                });
                                $this.width(200);
                                $this.height(200);
                                $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                $this.parent().append("<br/><span style='padding:3px;cursor:pointer;' fname='"+filename+"'>delete</span>");
                                $this.parent().find("span").click(function(){
                                    $this.parent().remove();
                                });
                            }
                        };
                    })(f);
                    reader.readAsDataURL(f);
                }
            });
            
            $("#returns_form").validate({
		rules: {
			explanation: {
				required: true,
				rangelength: [10,1000]
			}
		},
		messages: {
			explanation: {
				required: "請輸入說明",
				rangelength: "說明長度不得小於10碼或大於1000碼"
			}
		},
		submitHandler: function() {
                        if($("#reason").val()==0)
                        {
                            alert("請選擇原因！");
                            return false;
                        }
                        if(!$("#show_img img")[0])
                        {
                            alert("請上傳圖片！");
                            return false;
                        }

			var form_data = new FormData();
                        form_data.append('file', $("#return_image").data("file"));
                        form_data.append("filename",$("#show_img img:last").attr("title").replace(/_/g, "∴"));
                        form_data.append("member",$("#returns_form").attr("member"));
                        form_data.append("order",$("#returns_form").attr("order"));
                        form_data.append("goods",$("#returns_form").attr("goods"));
                        form_data.append("reason",$("#reason option:checked").text());
                        form_data.append("explanation",$("#explanation").val());
                        
                        $.ajax({
                            url: "http://www.crazy2go.com/member/ajax_order_returns",
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,                         
                            type: 'post',
                            success: function(data){
                                alert(data.message);
                                if(data.error==0)
                                {
                                    history.back();
                                }
                            }
                        });
		}
            });
        }
        
        //-------------------------member_order_returns - end-------------------------//
	
        //-------------------------member_order_sendout - start-------------------------//
        
        if(global_url.search('order_sendout')!=-1) {
            function createDialog(){
                $("body").append($("<div id='bg_cover'><div id='cover_content'></div></div>").css({
                    "position":"fixed",
                    "display":"inline-block",
                    "background":"rgba(0,0,0,0.8)",
                    "left":"0px",
                    "top":"0px",
                    "right":"0px",
                    "bottom":"0px",
                    "z-index":"999999"
                }));
                $("#cover_content").css({
                    "position":"absolute",
                    "display":"inline-block",
                    "background":"white",
                    "top":"120px",
                    "bottom":"120px",
                    "width":"840px",
                    "z-index":"1000000",
                    "padding":"20px",
                    "overflow-y":"scroll"
                });
                //$("#bg_cover").hide();
                $(window).unbind("resize");
                $(window).on("resize.dialog",function(){
                    $("#cover_content").css({
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+"px"
                    });
                    $("#close_cover").css({
                        "position":"fixed",
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+$("#cover_content").width()-3+"px"
                    });
                })
            }
            
            $(".check_logistics").click(function(){
                var $this = $(this);
                createDialog();
                $.post("http://www.crazy2go.com/member/ajax_order_logistics",{
                    order:$this.attr("order")
                },function(data){
                    var display = "";
                    var trace = $.parseJSON(data.add.trace[0].trace);
                    var traceArray = [];
                    var logistics = "";
                    var logistics_url = "";
                    var logistics_sn = "";
                    for(var i in trace)
                    {
                        switch(trace[i][0])
                        {
                            case "1":logistics = "台灣直郵";logistics_url = "http://www.sf-express.com/cn/sc/dynamic_functions/waybill/";break;
                            case "2":logistics = "順豐快遞";logistics_url = "http://www.sf-express.com/cn/sc/dynamic_functions/waybill/";break;
                            case "3":logistics = "申通快遞";logistics_url = "http://www.sto.cn/";break;
                            case "4":logistics = "韻達快遞";logistics_url = "http://www.yundaex.com/";break;
                            case "5":logistics = "郵政EMS(特快)";logistics_url = "http://www.ems.com.cn/";break;
                        }
                        logistics_sn = trace[i][1];
                        traceArray.push([i,logistics,logistics_url,logistics_sn]);
                    }
                    traceArray.reverse();
                    
                    display += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";
                    display += "<img src='http://www.crazy2go.com/public/img/template/car.png' style='position:relative;top:5px;'> <span style='font-size:16pt;'>查看物流：</span><div style='border-bottom:2px solid #7F7F7F;margin:10px 0px;'></div>"
                    display +=  "<div class='table' style='color:#848484;'>"+
                                    "<div class='tr'><div class='td' style='width:100px;padding:5px;'>賣家：</div><div class='td' style='padding:5px;'>"+$this.attr("seller")+"</div></div>"+
                                    "<div class='tr'><div class='td' style='width:100px;padding:5px;'>訂單編號：</div><div class='td' style='padding:5px;'>"+$this.attr("sn")+"</div></div>";
                                for(var i=0, len=traceArray.length; i<len; i++)
                                {
                                    display +=  "<div class='tr'><div class='td' style='width:100px;padding:5px;'>出貨時間：</div><div class='td' style='padding:5px;'>"+traceArray[i][0]+"</div></div>"+
                                                "<div class='tr'><div class='td' style='width:100px;padding:5px;'>物流廠商：</div><div class='td' style='padding:5px;'>"+traceArray[i][1]+"</div></div>"+
                                                "<div class='tr'><div class='td' style='width:100px;padding:5px;'>物流編號：</div><div class='td' style='padding:5px;'>"+traceArray[i][3]+"<a href='"+traceArray[i][2]+"' target='_blank' style='display:inline-block;position:relative;top:-5px;left:50px;padding:5px;color:black;border:1px solid black;'>查詢物流進度</a></div></div>";
                                }
                    display +=  "</div>";
                    $("#cover_content").html(display);
                    
                    $(window).trigger("resize");
                    
                },"json");
            });
            
            $(".receiving_confirm").click(function(){
                    $.post("http://www.crazy2go.com/member/ajax_order_receiving_complete",{
                        order:$(this).attr("order")
                    },function(data){
                        alert(data.message);
                        location.href = location.href;
                    },"json");
            });
            
            $(".appeal_button").click(function(){
                    var $this = $(this);
                    var post_data = "<form method='post' action='http://www.crazy2go.com/member/appeal'>"
                            +"<input type='hidden' name='order' value='"+$this.attr("order")+"' />"
                            +"<input type='hidden' name='member_name' value='"+$this.attr("member_name")+"' />"
                            +"<input type='hidden' name='member' value='"+$this.attr("member")+"' />"
                            +"<input type='hidden' name='sn' value='"+$this.attr("sn")+"' />"
                            +"<input type='hidden' name='store_name' value='"+$this.attr("store_name")+"' />"
                            +"<input type='hidden' name='store' value='"+$this.attr("store")+"' />"
                            +"</form>";

                    $(post_data).submit();
            });
            
            var p = global_url.split("?")[1];
            if(p)
            p.split("&");
            for(var i in p)
            {
                if(p[i].split("=")[0]=="sn")
                {
                    var sn = p[i].split("=")[1];
                    $(".check_logistics").each(function(){
                        if($(this).attr("sn")==sn)
                        {
                            $(this).trigger("click");
                            return false;
                        }
                    });
                    break;
                }
            }
            
        }
        
        //-------------------------member_order_sendout - end-------------------------//
        
        //-------------------------member_order_history - start-------------------------//
        if(global_url.search("order_history")!=-1)
        {
            function createDialog(){
                $("body").append($("<div id='bg_cover'><div id='cover_content'></div></div>").css({
                    "position":"fixed",
                    "display":"inline-block",
                    "background":"rgba(0,0,0,0.8)",
                    "left":"0px",
                    "top":"0px",
                    "right":"0px",
                    "bottom":"0px",
                    "z-index":"999999"
                }));
                $("#cover_content").css({
                    "position":"absolute",
                    "display":"inline-block",
                    "background":"white",
                    "top":"120px",
                    "bottom":"120px",
                    "width":"840px",
                    "z-index":"1000000",
                    "padding":"20px",
                    "overflow-y":"scroll"
                });
                //$("#bg_cover").hide();
                $(window).unbind("resize");
                $(window).on("resize.dialog",function(){
                    $("#cover_content").css({
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+"px"
                    });
                    $("#close_cover").css({
                        "position":"fixed",
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+$("#cover_content").width()-3+"px"
                    });
                })
            }
            
            $(".check_logistics").click(function(){
                var $this = $(this);
                createDialog();
                $.post("http://www.crazy2go.com/member/ajax_order_logistics",{
                    order:$this.attr("order")
                },function(data){
                    var display = "";
                    var trace = $.parseJSON(data.add.trace[0].trace);
                    var traceArray = [];
                    var logistics = "";
                    var logistics_url = "";
                    var logistics_sn = "";
                    for(var i in trace)
                    {
                        switch(trace[i][0])
                        {
                            case "1":logistics = "台灣直郵";logistics_url = "http://www.sf-express.com/cn/sc/dynamic_functions/waybill/";break;
                            case "2":logistics = "順豐快遞";logistics_url = "http://www.sf-express.com/cn/sc/dynamic_functions/waybill/";break;
                            case "3":logistics = "申通快遞";logistics_url = "http://www.sto.cn/";break;
                            case "4":logistics = "韻達快遞";logistics_url = "http://www.yundaex.com/";break;
                            case "5":logistics = "郵政EMS(特快)";logistics_url = "http://www.ems.com.cn/";break;
                        }
                        logistics_sn = trace[i][1];
                        traceArray.push([i,logistics,logistics_url,logistics_sn]);
                    }
                    traceArray.reverse();
                    
                    display += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";
                    display += "<img src='http://www.crazy2go.com/public/img/template/car.png' style='position:relative;top:5px;'> <span style='font-size:16pt;'>查看物流：</span><div style='border-bottom:2px solid #7F7F7F;margin:10px 0px;'></div>"
                    display +=  "<div class='table' style='color:#848484;'>"+
                                    "<div class='tr'><div class='td' style='width:100px;padding:5px;'>賣家：</div><div class='td' style='padding:5px;'>"+$this.attr("seller")+"</div></div>"+
                                    "<div class='tr'><div class='td' style='width:100px;padding:5px;'>訂單編號：</div><div class='td' style='padding:5px;'>"+$this.attr("sn")+"</div></div>";
                                for(var i=0, len=traceArray.length; i<len; i++)
                                {
                                    display +=  "<div class='tr'><div class='td' style='width:100px;padding:5px;'>出貨時間：</div><div class='td' style='padding:5px;'>"+traceArray[i][0]+"</div></div>"+
                                                "<div class='tr'><div class='td' style='width:100px;padding:5px;'>物流廠商：</div><div class='td' style='padding:5px;'>"+traceArray[i][1]+"</div></div>"+
                                                "<div class='tr'><div class='td' style='width:100px;padding:5px;'>物流編號：</div><div class='td' style='padding:5px;'>"+traceArray[i][3]+"<a href='"+traceArray[i][2]+"' target='_blank' style='display:inline-block;position:relative;top:-5px;left:50px;padding:5px;color:black;border:1px solid black;'>查詢物流進度</a></div></div>";
                                }
                    display +=  "</div>";
                    $("#cover_content").html(display);
                    
                    $(window).trigger("resize");
                    
                },"json");
            });
            
            
            $(".add_evaluate").click(function(){
                var $this = $(this);
                $.post("http://www.crazy2go.com/member/ajax_order_rank",{
                    order:$this.attr("order")
                },function(data){
                    if(data.add.date_diff > 3 && data.add.date_diff <= 6){
                        alert("距收貨時間3天~6天可評價！");
                    }
                    else
                    {
                        if(data.add.date_diff <=3)
                        {
                            alert("未滿3天不可評價！");
                            return;
                        }
                        if(data.add.date_diff > 6)
                        {
                            alert("超過6天不可評價！");
                        }
                    }    
                    createDialog();
                    var is_ranked = data.add.goods[0].evaluate_added == "0"?false:true;
                    var is_ranked_pre = data.add.goods[0].evaluate == "0"?false:true;
                    var display = "";
                    display += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";
                    display += "<img src='http://www.crazy2go.com/public/img/template/icon_evaluate.png' style='position:relative;top:5px;'> <span style='font-size:16pt;'>追加評論：</span>";
                    
                    display += "<div class='table' style='margin-top:10px;line-height:25px;'>";
                    display += "<div class='tr' style='color:#7F7F7F;font-weight:bold;border-bottom:2px solid #7F7F7F;'><div class='td' style='width:400px;'>賣家："+$this.attr("seller")+"｜訂單編號："+$this.attr("sn")+"</div><div class='td' style='width:170px;'>評價</div><div class='td' style='width:260px;'>留言</div></div>";
                    for(var i in data.add.goods)
                    {
                        display += "<div class='tr' goods='"+data.add.goods[i].goods+"' star='"+(is_ranked_pre?data.add.goods[i].evaluate:"6")+"' content='' style='border-bottom:1px solid #D3D3D3;'><div class='td' style='color:#A8A8A8;'>"+data.add.goods[i].name+"</div><div class='td'>";
                        for(var j=0; j<6; j++)
                        display += "<img src='http://www.crazy2go.com/public/img/goods/star_gray.png' style='position:relative;top:3px;cursor:pointer;'>";
                        var precontent = !is_ranked_pre?"["+data.add.pre_rank_date+"] 好評！<br/>":"["+data.add.goods[i].evaluate_date+"] "+data.add.goods[i].evaluate_context+"<br/>";
                        var content = !is_ranked?"<textarea style='width:250px;height:75px;border:1px solid #E5E5E5;margin:5px 0px;"+(data.add.date_diff>6?" display:none; ":"")+"' placeholder='留言字數請勿超過兩百字!' ></textarea>":"["+data.add.goods[i].evaluate_addate+"] "+data.add.goods[i].evaluate_adcontext;
                        content = precontent + content;
                        display += "&nbsp;</div><div class='td'>"+content+"</div></div>";
                    }
                    
                    display += "</div>";
                    
                    display += '<div style="text-align:right;"><input id="button" type="submit" value="" style="width:156px;height:51px;margin-top:20px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(http://www.crazy2go.com/public/img/template/button3.png)"></div>';
                    
                    $("#cover_content").html(display);
                    
                    $("#cover_content").find("div.tr").not(":first").each(function(){
                        var $tr = $(this);
                        $tr.find("img").each(function(){
                            $(this).click(function(){
                                $tr.find("img").attr("src","http://www.crazy2go.com/public/img/goods/star_gray.png");
                                for(var i = 0, len=$(this).index()+1;i<len;i++)
                                {
                                    $tr.find("img").eq(i).attr("src","http://www.crazy2go.com/public/img/goods/star_red.png")
                                }
                                $tr.attr("star",len);
                            });
                        });
                        if($tr.attr("star")=="0")
                        {
                            $tr.find("img:nth-child(3)").trigger("click");
                        }
                        else
                        {
                            $tr.find("img:nth-child("+$tr.attr("star")+")").trigger("click");
                        }
                        
                        $tr.find("textarea").blur(function(){
                            if($(this).val().length>200)
                            {
                                alert("最多200字！");
                                $(this).val($(this).val().substring(0,200));
                            }
                            $tr.attr("content",$(this).val());
                        });
                        $tr.find("img").unbind("click");
                    });
                    
                    
                    $("#cover_content #button").click(function(){
                        var goods_evaluate = [];
                        $("#cover_content").find("div.tr").not(":first").each(function(){
                            goods_evaluate.push([$(this).attr("goods"),$(this).attr("star"),$(this).attr("content")]);
                        });
                        
                        $.post("http://www.crazy2go.com/member/ajax_order_rank_update_add",{
                            order:$this.attr("order"),
                            goods_evaluate:JSON.stringify(goods_evaluate),
                            pre_rank:is_ranked_pre?1:0
                        },function(data){
                            alert(data.message);
                            location.href = location.href;
                        },"json");
                    })
                    
                    if(is_ranked || data.add.date_diff>6)
                    {
                        $("#cover_content #button").remove();
                    }
                    
                    $(window).resize();
                },"json")
            });
        }
        //-------------------------member_order_history - end-------------------------//
        
        //-------------------------member_appeal - start-------------------------//
        
        if(global_url.search('appeal')!=-1) {
            
            $("input[name='check_appeal']").click(function(){
                var $this_tr = $(this).parent().parent();
                var content = $this_tr.attr("content")!=""?"<div style='border:1px solid #E5E5E5;margin:10px 10px 20px 0px;padding:10px;right:0px;width:780px;background:#FAFEFF;word-wrap: break-word;word-break: break-all;'>[投訴內容] "+$this_tr.attr("content")+"</div>":"";
                var reply = $this_tr.attr("reply_content")!=""?"<div style='border:1px solid #E5E5E5;margin:10px 10px 20px 30px;padding:10px;right:0px;width:750px;word-wrap: break-word;word-break: break-all;'>[店家回覆] "+$this_tr.attr("reply_content")+"</div>":"";
                $this_tr.parent().find("div[name='detail']").remove();
                var $show = $("<div class='tr' name='detail' style='position:relative;'><div class='td'></div><div class='td' style='position:relative;'>"+content+reply+"</div><div class='td'></div><div class='td'></div><div class='td'></div></div>");
                $this_tr.after($show);
                var h = $show.height();
                $show.find("div[class='td'][style]").css("position","absolute");
                $show.height(h);
            });
            
            $("#appeal_form").validate({
		rules: {
			appeal_content: {
				required: true,
				rangelength: [10,1000]
			}
		},
		messages: {
			appeal_content: {
				required: "請輸入申訴內容",
				rangelength: "申訴內容長度不得小於10碼或大於1000碼"
			}
		},
		submitHandler: function() {
			var form_data = new FormData();
                        form_data.append("sn",$("#appeal_form").attr("sn"));
                        form_data.append("member",$("#appeal_form").attr("member"));
                        form_data.append("order",$("#appeal_form").attr("order"));
                        form_data.append("store",$("#appeal_form").attr("store"));
                        form_data.append("appeal_content",$("#appeal_content").val());
                        
                        $.ajax({
                            url: "http://www.crazy2go.com/member/ajax_appeal",
                            dataType: 'json',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,                         
                            type: 'post',
                            success: function(data){
                                alert(data.message);
                                if(data.error==0)
                                {
                                    $("#appeal_form").attr("goback")==1?history.back():location.href=location.href;
                                }
                            }
                        });
		}
            });
        }
        
        //-------------------------member_appeal - end-------------------------//
        
        //-------------------------member_address - start-------------------------//
        
        if(global_url.search('address')!=-1) {
            
            $("#consign").validate({
                    rules: {
                            consignee: {
                                    required: true,
                                    rangelength: [2,10]
                            },
                            postal_code: {
                                    required: true,
                                    rangelength: [5,7],
                                    number: true
                            },
                            address: {
                                    required: true,
                                    rangelength: [5,120]
                            },
                            contact_phone_1: {
                                    required: function() {
                                            if($("#contact_phone").val().length > 0) {
                                                    return true;
                                            }
                                            else {
                                                    return false;
                                            }
                                    },
                                    number: true,
                                    rangelength: [2,2]
                            },
                            contact_phone_2: {
                                    required: function() {
                                            if($("#contact_phone").val().length > 0) {
                                                    return true;
                                            }
                                            else {
                                                    return false;
                                            }
                                    },
                                    number: true,
                                    rangelength: [2,3]
                            },
                            contact_phone: {
                                    require_from_group: [1, '.group_number'],
                                    rangelength: [8,8],
                                    number: true
                            },
                            contact_mobile_1: {
                                    required: function() {
                                            if($("#contact_mobile").val().length > 0) {
                                                    return true;
                                            }
                                            else {
                                                    return false;
                                            }
                                    },
                                    number: true,
                                    rangelength: [2,2]
                            },
                            contact_mobile: {
                                    require_from_group: [1, '.group_number'],
                                    rangelength: [11,11],
                                    number: true
                            }
                    },
                    messages: {
                            consignee: {
                                    required: "請輸入收件人姓名",
                                    rangelength: "收件人姓名長度不得小於2碼或大於10碼"
                            },
                            postal_code: {
                                    required: "請輸入郵政編號",
                                    rangelength: "郵政編號長度不得小於5碼或大於7碼",
                                    number: "郵政編號僅能輸入數字"
                            },
                            address: {
                                    required: "請輸入地址",
                                    rangelength: "詳細地址長度不得小於5碼或大於120碼"
                            },
                            contact_phone_1: {
                                    required: "請輸入國際碼",
                                    number: "國際碼僅能輸入數字",
                                    rangelength: "國際碼長度為2碼"
                            },
                            contact_phone_2: {
                                    required: "請輸入區碼",
                                    number: "區碼僅能輸入數字",
                                    rangelength: "區碼長度不得小於2碼或大於3碼"
                            },
                            contact_phone: {
                                    require_from_group: "請至少選擇輸入一種聯繫方式",
                                    rangelength: "電話號碼長度不得小於8碼或大於8碼",
                                    number: "電話號碼僅能輸入數字"
                            },
                            contact_mobile_1: {
                                    required: "請輸入國際碼",
                                    number: "國際碼僅能輸入數字",
                                    rangelength: "國際碼長度為2碼"
                            },
                            contact_mobile: {
                                    require_from_group: "請至少選擇輸入一種聯繫方式",
                                    rangelength: "手機號碼長度為11碼",
                                    number: "手機號碼僅能輸入數字"
                            }
                    },
                    submitHandler: function() {
                            if($("#province option:checked").text()=="省份" ||
                               $("#city option:checked").text()=="城市" ||
                               $("#district option:checked").text()=="縣區"){
                                    alert("請選擇地址！");
                                    return;
                               }

                            $.post("http://www.crazy2go.com/member/ajax_address", {
                                fi_no:$("#fi_no").val(), 
                                consignee:$("#consignee").val(), 
                                postal_code:$("#postal_code").val(), 
                                province:$("#province").val()+"＿"+$("#province option:checked").text()+"＿"+"1",
                                city:$("#city").val()+"＿"+$("#city option:checked").text()+"＿"+$("#province").val(),
                                district:$("#district").val()+"＿"+$("#district option:checked").text()+"＿"+$("#city").val(),
                                street:$("#street").val()+"＿"+$("#street option:checked").text()+"＿"+$("#district").val(),
                                address:$("#address").val(), 
                                contact_phone:$("#contact_phone_1").val()+"-"+$("#contact_phone_2").val()+"-"+$("#contact_phone").val(), 
                                contact_mobile:$("#contact_mobile_1").val()+"-"+$("#contact_mobile").val(),
                                preset:($("#preset").prop("checked")?1:0)
                            }, function(data) {
                                    if(data.error == 0) {
                                            //location.href = 'http://www.crazy2go.com/member/address/';
                                            var $child = null;
               
                                            if($("#fi_no").val() == "") {
                                                    $("#address_table").append('<div class="tr" id="tr'+data.add.newid+'" style="border-top:1px solid #E5E5E5;"><div class="td" style="padding:5px;">'+$("#consignee").val()+'</div><div class="td" style="padding:5px;">'+$("#postal_code").val()+' '+$("#province option:checked").text()+' '+$("#city option:checked").text()+' '+$("#district option:checked").text()+' '+$("#street option:checked").text()+' '+$("#address").val()+'</div><div class="td" style="padding:5px;">'+$("#contact_phone_1").val()+"-"+$("#contact_phone_2").val()+"-"+$("#contact_phone").val()+'<br/>'+$("#contact_mobile_1").val()+"-"+$("#contact_mobile").val()+'</div><div class="td" style="padding:5px;"><input class="address_modify" type="button" value="修改" style="cursor:pointer;" data-fi_no="'+data.add.newid+'" data-consignee="'+$("#consignee").val()+'" data-postal_code="'+$("#postal_code").val()+'" data-province="'+$("#province").val()+"＿"+$("#province option:checked").text()+"＿"+"1"+'" data-city="'+$("#city").val()+"＿"+$("#city option:checked").text()+"＿"+$("#province").val()+'" data-district="'+$("#district").val()+"＿"+$("#district option:checked").text()+"＿"+$("#city").val()+'" data-street="'+$("#street").val()+"＿"+$("#street option:checked").text()+"＿"+$("#district").val()+'" data-address="'+$("#address").val()+'" data-contact_phone="'+$("#contact_phone_1").val()+"-"+$("#contact_phone_2").val()+"-"+$("#contact_phone").val()+'" data-contact_mobile="'+$("#contact_mobile_1").val()+"-"+$("#contact_mobile").val()+'">　<input class="address_delete" type="button" value="刪除" style="cursor:pointer;" data-fi_no="'+data.add.newid+'">　<span class="preset_status">'+($("#preset").prop("checked")?'<img id="address_preset" src="http://www.crazy2go.com/public/img/template/click_icon.jpg" style="position:relative;top:3px;">目前常用地址':'<input class="address_usual" type="button" value="設成常用地址" style="cursor:pointer;" data-fi_no="'+data.add.newid+'">')+'</span>&nbsp;</div></div>');
                                                    if($("#preset").prop("checked"))
                                                    {
                                                        $child = $("#address_table .tr:last").find(".preset_status").html();
                                                        $("#address_table .preset_status").each(function(){
                                                            var $this = $(this);
                                                            var fi_no = $this.parent().find(".address_delete").attr("data-fi_no");
                                                            $this.html('<input class="address_usual" type="button" value="設成常用地址" style="cursor:pointer;" data-fi_no="'+fi_no+'">');
                                                        });
                                                        $("#address_table .tr:last").find(".preset_status").html($child);
                                                    }
                                            }
                                            else {
                                                    $("#tr"+$("#fi_no").val()).html('<div class="td" style="padding:5px;">'+$("#consignee").val()+'</div><div class="td" style="padding:5px;">'+$("#postal_code").val()+' '+$("#province option:checked").text()+' '+$("#city option:checked").text()+' '+$("#district option:checked").text()+' '+$("#street option:checked").text()+' '+$("#address").val()+'</div><div class="td" style="padding:5px;">'+$("#contact_phone_1").val()+"-"+$("#contact_phone_2").val()+"-"+$("#contact_phone").val()+'<br/>'+$("#contact_mobile_1").val()+"-"+$("#contact_mobile").val()+'</div><div class="td" style="padding:5px;"><input class="address_modify" type="button" value="修改" style="cursor:pointer;" data-fi_no="'+$("#fi_no").val()+'" data-consignee="'+$("#consignee").val()+'" data-postal_code="'+$("#postal_code").val()+'" data-province="'+$("#province").val()+"＿"+$("#province option:checked").text()+"＿"+"1"+'" data-city="'+$("#city").val()+"＿"+$("#city option:checked").text()+"＿"+$("#province").val()+'" data-district="'+$("#district").val()+"＿"+$("#district option:checked").text()+"＿"+$("#city").val()+'" data-street="'+$("#street").val()+"＿"+$("#street option:checked").text()+"＿"+$("#district").val()+'" data-address="'+$("#address").val()+'" data-contact_phone="'+$("#contact_phone_1").val()+"-"+$("#contact_phone_2").val()+"-"+$("#contact_phone").val()+'" data-contact_mobile="'+$("#contact_mobile_1").val()+"-"+$("#contact_mobile").val()+'">　<input class="address_delete" type="button" value="刪除" style="cursor:pointer;" data-fi_no="'+$("#fi_no").val()+'">　<span class="preset_status">'+($("#preset").prop("checked")?'<img id="address_preset" src="http://www.crazy2go.com/public/img/template/click_icon.jpg" style="position:relative;top:3px;">目前常用地址':'<input class="address_usual" type="button" value="設成常用地址" style="cursor:pointer;" data-fi_no="'+$("#fi_no").val()+'">')+'</span>&nbsp;</div>');
                                                    
                                                    if($("#preset").prop("checked"))
                                                    {
                                                        $child = $("#tr"+$("#fi_no").val()).find(".preset_status").html();
                                                        $("#address_table .preset_status").each(function(){
                                                            var $this = $(this);
                                                            var fi_no = $this.parent().find(".address_delete").attr("data-fi_no");
                                                            $this.html('<input class="address_usual" type="button" value="設成常用地址" style="cursor:pointer;" data-fi_no="'+fi_no+'">');
                                                        });
                                                        $("#address_table #tr"+$("#fi_no").val()).find(".preset_status").html($child);
                                                    }
                                            }
                                            
                                            

                                            if($("#address_cancel").length > 0) {
                                                    $("#address_cancel").remove();
                                            }

                                            $("#fi_no").val('');
                                            $("#consignee").val('');
                                            $("#postal_code").val('');
                                            $("#province").val('');
                                            $("#city").val('');
                                            $("#district").val('');
                                            $("#street").val('');
                                            $("#address").val('');
                                            $("#contact_phone_1").val('');
                                            $("#contact_phone_2").val('');
                                            $("#contact_phone").val('');
                                            $("#contact_mobile_1").val('');
                                            $("#contact_mobile").val('');
                                            //$("#button").val("保存");
                                            $("#preset").prop("checked",false);
                                            
                                            $("#province_text").text($("#province option:checked").text());
                                            $("#city_text").text($("#city option:checked").text());
                                            $("#district_text").text($("#district option:checked").text());
                                            $("#street_text").text($("#street option:checked").text());

                                            $(".tr").css({'backgroundColor':''});
                                            $("#trHeader").css({'backgroundColor':'#FAFEFF'});
                                    }
                                    alert(data.message);
                            }, "json");
                    }
            });

            $(document).on("click", ".address_modify", function() {
                    $(this).parent().find("#address_preset")[0]?$("#preset").prop("checked","checked"):$("#preset").prop("checked",false);
                    $("#fi_no").val($(this).attr("data-fi_no"));
                    $("#consignee").val($(this).attr("data-consignee"));
                    $("#postal_code").val($(this).attr("data-postal_code"));

                    $("#province option").remove();
                    $("#city option").remove();
                    $("#district option").remove();
                    $("#street option").remove();
                    $("#province").append($("<option></option>").attr("value", '').text('省份'));
                    $("#city").append($("<option></option>").attr("value", '').text('城市'));
                    $("#district").append($("<option></option>").attr("value", '').text('縣區'));
                    $("#street").append($("<option></option>").attr("value", '').text('街道'));
                    $("#province_text").text($(this).attr("data-province").split("＿")[1]);
                    $("#city_text").text($(this).attr("data-city").split("＿")[1]);
                    $("#district_text").text($(this).attr("data-district").split("＿")[1]);
                    $("#street_text").text($(this).attr("data-street").split("＿")[1]);

                    var selectStr1 = $(this).attr("data-province").split("＿");
                    var selectStr2 = $(this).attr("data-city").split("＿");
                    var selectStr3 = $(this).attr("data-district").split("＿");
                    var selectStr4 = $(this).attr("data-street").split("＿");

                    $.post("http://www.crazy2go.com/member/ajax_postal_province_group", {
                        fi_no_1:selectStr1[2],
                        fi_no_2:selectStr2[2],
                        fi_no_3:selectStr3[2],
                        fi_no_4:selectStr4[2]
                    }, function(data) {
                            for(i=0; i<data[0].length; i++) {
                                    $("#province").append($("<option></option>").attr("value", data[0][i]['fi_no']).text(data[0][i]['name']));
                            }
                            $("#province").val(selectStr1[0]);
                            for(i=0; i<data[1].length; i++) {
                                    $("#city").append($("<option></option>").attr("value", data[1][i]['fi_no']).text(data[1][i]['name']));
                            }
                            $("#city").val(selectStr2[0]);
                            for(i=0; i<data[2].length; i++) {
                                    $("#district").append($("<option></option>").attr("value", data[2][i]['fi_no']).text(data[2][i]['name']));
                            }
                            $("#district").val(selectStr3[0]);
                            if(data[3].length > 0) {
                                    $("#street").css({'display':''});
                                    for(i=0; i<data[3].length; i++) {
                                            $("#street").append($("<option></option>").attr("value", data[3][i]['fi_no']).text(data[3][i]['name']));
                                    }

                                    $("#street").val(selectStr4[0]);
                            }
                            else {
                                    $("#street").css({'display':'none'});
                            }
                            $("#province_text").text($("#province option:checked").text());
                            $("#city_text").text($("#city option:checked").text());
                            $("#district_text").text($("#district option:checked").text());
                            $("#street_text").text($("#street option:checked").text());
                    }, "json");

                    $("#address").val($(this).attr("data-address"));
                    var phone = $(this).attr("data-contact_phone").split("-");
                    var mobile = $(this).attr("data-contact_mobile").split("-");
                    $("#contact_phone_1").val(phone[0]);
                    $("#contact_phone_2").val(phone[1]);
                    $("#contact_phone").val(phone[2]);
                    $("#contact_mobile_1").val(mobile[0]);
                    $("#contact_mobile").val(mobile[1]);
                    //$("#button").val("修改");
                    if($("#address_cancel").length <= 0) {
                            $("#button").after('<input type="button" id="address_cancel" value="" style="width:155px;height:50px;margin-top:20px;margin-left:20px;padding-top:1px;cursor:pointer;background:#FF4500;color:white;border:0px;background:url(http://www.crazy2go.com/public/img/template/cancel_button.png) no-repeat;" />');
                    }
                    
                    $(".tr").css({'backgroundColor':''});
                    $("#trHeader").css({'backgroundColor':'#FAFEFF'});
                    $("#tr"+$(this).attr("data-fi_no")).css({'backgroundColor':'#FFF88C'});
            });

            $(document).on("click", ".address_delete", function() {
                    if(confirm("確定刪除？")) {
                            var address_fi_no = $(this).attr("data-fi_no");
                            $.post("http://www.crazy2go.com/member/ajax_address_delete", {fi_no:$(this).attr("data-fi_no")}, function(data) {
                                    if(data.error == 0) {
                                            $("#tr"+address_fi_no).remove();
                                    }
                                    alert(data.message);
                            }, "json");
                    }
            });
            
            $(document).on("click", ".address_usual", function() {
                    if(confirm("確定設置常用地址？")) {
                            var address_fi_no = $(this).attr("data-fi_no");
                            $.post("http://www.crazy2go.com/member/ajax_address_usual", {fi_no:$(this).attr("data-fi_no")}, function(data) {
                                    if(data.error == 0) {
                                            $("#address_table .preset_status").each(function(){
                                                var $this = $(this);
                                                var fi_no = $this.parent().find(".address_delete").attr("data-fi_no");
                                                $this.html('<input class="address_usual" type="button" value="設成常用地址" style="cursor:pointer;" data-fi_no="'+fi_no+'">');
                                            });
                                            $("#address_table #tr"+address_fi_no).find(".preset_status").html('<img id="address_preset" src="http://www.crazy2go.com/public/img/template/click_icon.jpg" style="position:relative;top:3px;">目前常用地址');
                                    }
                                    alert(data.message);
                            }, "json");
                    }
            });

            $(document).on("click", "#address_cancel", function() {
                    $("#fi_no").val('');
                    $("#consignee").val('');
                    $("#postal_code").val('');
                    $("#province").val('');
                    $("#city").val('');
                    $("#district").val('');
                    $("#street").val('');
                    $("#address").val('');
                    $("#contact_phone_1").val('');
                    $("#contact_phone_2").val('');
                    $("#contact_phone").val('');
                    $("#contact_mobile_1").val('');
                    $("#contact_mobile").val('');
                    //$("#button").val("保存");
                    $("#preset").prop("checked",false);

                    $(".tr").css({'backgroundColor':''});
                    $("#trHeader").css({'backgroundColor':'#FAFEFF'});
                    
                    $("#province_text").text($("#province option:checked").text());
                    $("#city_text").text($("#city option:checked").text());
                    $("#district_text").text($("#district option:checked").text());
                    $("#street_text").text($("#street option:checked").text());

                    $("#address_cancel").remove();
            });


            $.post("http://www.crazy2go.com/member/ajax_postal_province", {fi_no:'1'}, function(data) {
                    for(i=0; i<data.length; i++) {
                            $("#province").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
                    }
                    $("#province_text").text($("#province option:checked").text());
                    $("#city_text").text($("#city option:checked").text());
                    $("#district_text").text($("#district option:checked").text());
                    $("#street_text").text($("#street option:checked").text());
            }, "json");

            $("#province").change(function() {
                    $("#city option").remove();
                    $("#district option").remove();
                    $("#street option").remove();
                    $("#city").append($("<option></option>").attr("value", '').text('城市'));
                    $("#district").append($("<option></option>").attr("value", '').text('縣區'));
                    $("#street").append($("<option></option>").attr("value", '').text('街道'));
                    $.post("http://www.crazy2go.com/member/ajax_postal_province", {fi_no:$(this).val()}, function(data) {
                            for(i=0; i<data.length; i++) {
                                    $("#city").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
                            }
                            $("#province_text").text($("#province option:checked").text());
                            $("#city_text").text($("#city option:checked").text());
                            $("#district_text").text($("#district option:checked").text());
                            $("#street_text").text($("#street option:checked").text());
                    }, "json");
            });

            $("#city").change(function() {
                    $("#district option").remove();
                    $("#street option").remove();
                    $("#district").append($("<option></option>").attr("value", '').text('縣區'));
                    $("#street").append($("<option></option>").attr("value", '').text('街道'));
                    $.post("http://www.crazy2go.com/member/ajax_postal_province", {fi_no:$(this).val()}, function(data) {
                            for(i=0; i<data.length; i++) {
                                    $("#district").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
                            }
                            $("#province_text").text($("#province option:checked").text());
                            $("#city_text").text($("#city option:checked").text());
                            $("#district_text").text($("#district option:checked").text());
                            $("#street_text").text($("#street option:checked").text());
                    }, "json");
            });

            $("#district").change(function() {
                    $("#street option").remove();
                    $("#street").append($("<option></option>").attr("value", '').text('街道'));
                    $.post("http://www.crazy2go.com/member/ajax_postal_street", {fi_no:$(this).val()}, function(data) {
                            if(data.length > 0) {
                                    $("#street").css({'display':''});
                                    for(i=0; i<data.length; i++) {
                                            $("#street").append($("<option></option>").attr("value", data[i]['fi_no']).text(data[i]['name']));
                                    }
                            }
                            else {
                                    $("#street").css({'display':'none'});
                            }
                            $("#province_text").text($("#province option:checked").text());
                            $("#city_text").text($("#city option:checked").text());
                            $("#district_text").text($("#district option:checked").text());
                            $("#street_text").text($("#street option:checked").text());
                    }, "json");
            });
            
            $("#street").change(function() {
                    $("#province_text").text($("#province option:checked").text());
                    $("#city_text").text($("#city option:checked").text());
                    $("#district_text").text($("#district option:checked").text());
                    $("#street_text").text($("#street option:checked").text());
            });
        }
        //-------------------------member_address - end-------------------------//
        
        //-------------------------member_collect/member_historylog - start-------------------------//
        
        if(global_url.search('collect')!=-1 || global_url.search('historylog')!=-1) 
        {
            $("#page_display").html("<span style='color:red;'>"+$("#jump_page").val()+"</span>/"+$("#page_count").text());
            $("#page_goback").click(function(){
                if($("#go_page_up")[0])
                location.href = $("#go_page_up").attr("href");
            });
            $("#page_goforward").click(function(){
                if($("#go_page_down")[0])
                location.href = $("#go_page_down").attr("href");
            });
            
            $(".show_item").parent().hover(function(){
                var $this = $(this);
                if(!$this.find("#item_cover")[0])
                {
                    $this.append("<div id='item_cover'></div>");
                    $this.find("#item_cover").css({
                        "position":"absolute",
                        "display":"inline-block",
                        "width":"175px",
                        "height":"175px",
                        "background":"rgba(0,0,0,0.5)",
                        "left":$this.find("img").position().left+"px",
                        "top":$this.find("img").position().top+"px",
                        "cursor":"pointer",
                        "text-align":"center"
                    }).append("<img src='http://www.crazy2go.com/public/img/template/m_check2.png' style='position:relative;top:70px;'>");
                }
                else
                {
                    $this.find("#item_cover").css({ 
                        "background":"rgba(0,0,0,0.5)"
                    });
                    $this.find("#item_cover img").attr("src",'http://www.crazy2go.com/public/img/template/m_check2.png');
                    $this.find("#item_cover img").show();
                }
            },function(){
                var $this = $(this);
                $this.find("#item_cover").css({ 
                    "background":"rgba(0,0,0,0)"
                });
                if($this.find(".show_item").attr("isChecked")==1)
                {
                    $this.find("#item_cover img").show();
                    $this.find("#item_cover img").attr("src",'http://www.crazy2go.com/public/img/template/m_check.png');
                }
                else
                {
                    $this.find("#item_cover img").hide();
                }
            });
            
            $(".show_item").parent().click(function(){
                var $this = $(this).find(".show_item");
                if($this.attr("isChecked")==0)
                {
                    $this.attr("isChecked",1).css({
                        "border":"1px solid red"
                    });
                }
                else if($this.attr("isChecked")==1)
                {
                    $this.attr("isChecked",0).css({
                        "border":"1px solid white"
                    });
                }
            });
            
            //全選按鈕
            $("#select_all").change(function(){
                var $this = $(this);
                if($this.prop("checked"))
                {
                    $(".show_item").each(function(){
                        var $this = $(this);
                        if($this.attr("isChecked")==0)
                        {
                            $this.parent().trigger("mouseenter");
                            $this.parent().trigger("click");
                            $this.parent().trigger("mouseleave");
                        }
                    });
                }
                else
                {
                    $(".show_item").each(function(){
                        var $this = $(this);
                        if($this.attr("isChecked")==1)
                        {
                            $this.parent().trigger("click");
                            $this.parent().trigger("mouseleave");
                        }
                    });
                }
            });
            
            //刪除 for collect
            $("#collect_delete").click(function(){
                var collect_delete = [];
                var type = "";
                $(".show_item").each(function(){
                    var $this = $(this);
                    if($this.attr("ischecked")==1)
                    {
                        type = $this.attr("ctype");
                        collect_delete.push($this.attr("collect_id"));
                    }
                });
                collect_delete = collect_delete.join(",");
                $.post("http://www.crazy2go.com/member/ajax_collect_delete",{
                    "delete":collect_delete,
                    "type":type
                },function(data){
                    alert(data.message);
                    if(data.error == 0) {
                            location.href = location.href;
                    }
                },"json");
            });
            
            //刪除 for historylog 
            $("#historylog_delete").click(function(){
                var historylog_delete = [];
                $(".show_item").each(function(){
                    var $this = $(this);
                    if($this.attr("ischecked")==1)
                    {
                        historylog_delete.push($this.attr("historylog_id"));
                    }
                });
                historylog_delete = historylog_delete.join(",");
                console.log(historylog_delete);
                $.post("http://www.crazy2go.com/member/ajax_historylog_delete",{
                    "delete":historylog_delete
                },function(data){
                    alert(data.message);
                    if(data.error == 0) {
                        location.href = location.href;
                    }
                },"json");
            });
            
            //搜尋
            $("#search_btn").click(function(){
                var keyword = $("#search_text").val();
                if(location.href.search("ckeyword")==-1)
                {
                    if(location.href.split("?")[1]==undefined)
                    {
                        location.href = location.href + "?ckeyword=" + keyword;
                    }
                    else
                    {
                        location.href = location.href + "&ckeyword=" + keyword;
                    }
                }
                else
                {
                    var params = location.href.split("?")[1].split("&");
                    var params_str = "";
                    for(var i in params)
                    {
                        if(params[i].search("ckeyword") != -1)
                        {
                            var k = params[i].split("=")[1];
                            params[i] = "ckeyword="+keyword;
                        }
                        params_str += "&"+params[i];
                    }
                    location.href = location.href.split("?")[0]+"?"+(params_str.substring(1,params_str.length));
                }
            });
            $("#search_text").keypress(function(e){
               if(e.keyCode == 13)
               $("#search_btn").trigger("click");
            });
            var find = "ckeyword=";
            var pos = location.href.search(find);
            if(pos != -1)
            $("#search_text").val(decodeURI(location.href.slice(pos+find.length,location.href.length)));
        
            //加到我的最愛(收藏)
            $("#add_favorite").click(function(){
                var favorite = [];
                $(".show_item").each(function(){
                    var $this = $(this);
                    if($this.attr("ischecked")==1)
                    {
                        favorite.push($this.attr("fi_no"));
                    }
                });
                favorite = favorite.join("∵");
                $.post("http://www.crazy2go.com/cart/ajax_batch_collect", {fi_no:favorite}, function(data) {
                    alert(data.message);
                }, "json");
            });
            
            //加入購物車
            $(".add_cart").click(function(){
                $(this).parent().parent().parent().trigger("click");//取消選取
                $("body").append($("<div id='bg_cover'><div id='cover_content'></div></div>").css({
                    "position":"fixed",
                    "display":"inline-block",
                    "background":"rgba(0,0,0,0.8)",
                    "left":"0px",
                    "top":"0px",
                    "right":"0px",
                    "bottom":"0px",
                    "z-index":"999999"
                }));
                $("#cover_content").css({
                    "position":"absolute",
                    "display":"inline-block",
                    "background":"white",
                    "top":"120px",
                    "bottom":"120px",
                    "width":"600px",
                    "z-index":"1000000",
                    "padding":"10px",
                    "overflow-y":"scroll"
                });
                $("#bg_cover").hide();
                $(window).unbind("resize");
                $(window).on("resize.dialog",function(){
                    $("#cover_content").css({
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+"px"
                    });
                    $("#close_cover").css({
                        "position":"fixed",
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+$("#cover_content").width()-3+"px"
                    });
                });
                
                $.post("http://www.crazy2go.com/member/ajax_product_spec",{
                    fi_no:$(this).attr("fi_no")
                },function(data){
                    var fi_no = data.add.fi_no;
                    var type_item = $.parseJSON(data.add.specifications);
                    var type_item_length = 0;
                    var type_stock = $.parseJSON(data.add.inventory);
                    var content_style = "";
                    var default_status = false;
                    content_style += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";
                    
                    for(var i in type_item)
                    {
                        if(i == "default")
                        {
                            default_status = true;
                            continue;
                        }
                        content_style += "<div class='tr type' data-type='"+i+"' data-count='"+type_item[i].length+"'><div class='td' style='width:60px;padding:10px 10px 10px 10px;'>"+i+"</div><div class='td' style='padding:10px 10px 10px 10px;'>";
                        for(var j=0; j< type_item[i].length; j++) {
                            content_style += "<div class='item' data-item='"+i+"' data-no='"+j+"' data-val='"+type_item[i][j]+"' data-select='0' data-stock='m' style='cursor:pointer;margin:5px; padding:5px; border:#898989 solid 1px; float:left;'>"+type_item[i][j]+"</div>";
                        }
                        content_style += "<div style='clear:both;'></div></div></div>";
                        type_item_length++;
                    }
                    
                    var str = type_stock.join("+");
                    var content_total = eval(str);
                    var content_stock = type_stock.join(",");
                    
                    content_style += "<div class='tr'>";
                    content_style += "<div class='td' style='padding:10px 10px 10px 10px;'>商品數量</div>";
                    content_style += "<div class='td' style='padding:10px 10px 10px 10px;'><div style='display:inline-block;height:22px;border:1px solid #898989;margin-left:5px;'>";
                    content_style += "<input type='text' id='number' value='0' style='margin-left:3px;position:relative;top:2px;height:15px;border:0px;width:40px;'>";
                    //content_style += "<div id='number_plus' style='position:relative;top:-6px;cursor:pointer;border-left:1px solid #BFBFBF;display:inline-block;width:20px;height:21px;text-align:center;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='position:relative;top:6px;-ms-transform:rotate(0deg); -moz-transform:rotate(0deg); -webkit-transform:rotate(0deg); -o-transform:rotate(0deg); transform:rotate(0deg);'></div>";
                    //content_style += "<div id='number_minus' style='position:relative;top:-6px;cursor:pointer;border-left:1px solid #BFBFBF;display:inline-block;width:20px;height:21px;text-align:center;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='position:relative;top:7px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'></div>";
                    content_style += "</div> 件<div id='stock' style='display:none;margin-left:20px;color:#969B9C;font-size:8pt;' data-type='"+type_item_length+"' data-stock='"+content_stock+"' data-total='"+content_total+"' data-select='"+(default_status?"0；0；default；default":"")+"' data-select_stock='"+(default_status?content_total:"")+"'> ( 庫存： "+content_total+" 件 ) </div>";
                    content_style += "</div>";
                    content_style += "</div>\r\n"; 
                    
                    content_style += "<div class='tr'><div class='td'></div><div class='td' style='padding:10px 10px 10px 10px;'><input id='added' data-fi_no='"+fi_no+"' type='button' style='background:#EB3339;color:white;border:0px;cursor:pointer;padding:5px;margin-left:5px;' value='加入購物車'></div></div>";
                    
                    $("#cover_content").html(content_style);
                    
                    $(window).trigger("resize.dialog");
                    $("#bg_cover").show();
                    
                    $(".item").click(function() {
                            var type_check = $(this).attr("data-select");
                            var type_item = $(this).attr("data-item");

                            //data-item="尺碼" data-no="19" data-val="透明酒紅色"

                            $(".item").each(function(){
                                    if(type_item == $(this).attr("data-item")){
                                            $(this).css({
                                                'border':'#898989 solid 1px', 
                                                'margin':'5px'
                                            });
                                            $(this).attr('data-select','0');
                                    }
                            });

                            if(type_check == '0') {
                                    $(this).css({
                                        'border':'red solid 1px', 
                                        'margin':'5px'
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
                                                $("#bg_cover").remove();
                                            }
                                            alert(data.message);
                                    }, "json");
                            }
                            else {
                                    alert('庫存不足');
                            }
                    });
                },"json");
            });
        }
        
        //-------------------------member_collect/member_history - end-------------------------//
        
        //-------------------------member_recommand - start-------------------------//
        
        if(global_url.search('recommand')!=-1) 
        {
            $("#cate_select").append($("#go_page_down").clone(true).css("float","right"));
            $("#cate_select").append($("#go_page_up").clone(true).css("float","right"));
            
            $(".cate_select").click(function(){
                $(".cate_select").attr("style",$("#cate_select").attr("style2"));
                $(this).attr("style",$("#cate_select").attr("style1"));
                var cat = $(this).attr("cat");
                if(location.href.search("category")==-1)
                {
                    if(location.href.split("?")[1]==undefined)
                    {
                        location.href = location.href + "?category=" + cat;
                    }
                    else
                    {
                        location.href = location.href + "&category=" + cat;
                    }
                }
                else
                {
                    var params = location.href.split("?")[1].split("&");
                    var params_str = "";
                    for(var i in params)
                    {
                        if(params[i].search("category") != -1)
                        {
                            var k = params[i].split("=")[1];
                            params[i] = "category="+cat;
                        }
                        if(params[i].search("page") != -1)
                        {
                            params[i] = "page=1";
                        }
                        params_str += "&"+params[i];
                    }
                    location.href = location.href.split("?")[0]+"?"+(params_str.substring(1,params_str.length));
                }
            });
            if(location.href.search("category")==-1)
            {
                $(".cate_select").attr("style",$("#cate_select").attr("style2"));
                $(".cate_select:first").attr("style",$("#cate_select").attr("style1"));
            }
            else
            {
                var params = location.href.split("?")[1].split("&");
                var params_str = "";
                for(var i in params)
                {
                    if(params[i].search("category") != -1)
                    {
                        var k = params[i].split("=")[1];
                        $(".cate_select").each(function(){
                            var cat = $(this).attr("cat");
                            if(cat == k)
                            {
                                $(".cate_select").attr("style",$("#cate_select").attr("style2"));
                                $(this).attr("style",$("#cate_select").attr("style1"));
                                return false;
                            }
                        });
                        break;
                    }
                }
                
            }
            //加入購物車
            $(".add_cart").click(function(){
                $("body").append($("<div id='bg_cover'><div id='cover_content'></div></div>").css({
                    "position":"fixed",
                    "display":"inline-block",
                    "background":"rgba(0,0,0,0.8)",
                    "left":"0px",
                    "top":"0px",
                    "right":"0px",
                    "bottom":"0px",
                    "z-index":"999999"
                }));
                $("#cover_content").css({
                    "position":"absolute",
                    "display":"inline-block",
                    "background":"white",
                    "top":"120px",
                    "bottom":"120px",
                    "width":"600px",
                    "z-index":"1000000",
                    "padding":"10px",
                    "overflow-y":"scroll"
                });
                $("#bg_cover").hide();
                $(window).unbind("resize");
                $(window).on("resize.dialog",function(){
                    $("#cover_content").css({
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+"px"
                    });
                    $("#close_cover").css({
                        "position":"fixed",
                        "left":($("#bg_cover").width()-$("#cover_content").width())/2+$("#cover_content").width()-3+"px"
                    });
                });
                
                $.post("http://www.crazy2go.com/member/ajax_product_spec",{
                    fi_no:$(this).attr("fi_no")
                },function(data){
                    var fi_no = data.add.fi_no;
                    var type_item = $.parseJSON(data.add.specifications);
                    var type_item_length = 0;
                    var type_stock = $.parseJSON(data.add.inventory);
                    var content_style = "";
                    var default_status = false;
                    content_style += "<img id='close_cover' src='http://www.crazy2go.com/public/img/template/delete.png' style='float:right;cursor:pointer;' onclick='$(\"#bg_cover\").remove();'>";

                    for(var i in type_item)
                    {
                        if(i == "default")
                        {
                            default_status = true;
                            continue;
                        }
                        content_style += "<div class='tr type' data-type='"+i+"' data-count='"+type_item[i].length+"'><div class='td' style='width:60px;padding:10px 10px 10px 10px;'>"+i+"</div><div class='td' style='padding:10px 10px 10px 10px;'>";
                        for(var j=0; j< type_item[i].length; j++) {
                            content_style += "<div class='item' data-item='"+i+"' data-no='"+j+"' data-val='"+type_item[i][j]+"' data-select='0' data-stock='m' style='cursor:pointer;margin:5px; padding:5px; border:#898989 solid 1px; float:left;'>"+type_item[i][j]+"</div>";
                        }
                        content_style += "<div style='clear:both;'></div></div></div>";
                        type_item_length++;
                    }
                    
                    var str = type_stock.join("+");
                    var content_total = eval(str);
                    var content_stock = type_stock.join(",");
                    
                    content_style += "<div class='tr'>";
                    content_style += "<div class='td' style='padding:10px 10px 10px 10px;'>商品數量</div>";
                    content_style += "<div class='td' style='padding:10px 10px 10px 10px;'><div style='display:inline-block;height:22px;border:1px solid #898989;margin-left:5px;'>";
                    content_style += "<input type='text' id='number' value='0' style='margin-left:3px;position:relative;top:2px;height:15px;border:0px;width:40px;'>";
                    //content_style += "<div id='number_plus' style='position:relative;top:-6px;cursor:pointer;border-left:1px solid #BFBFBF;display:inline-block;width:20px;height:21px;text-align:center;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='position:relative;top:6px;-ms-transform:rotate(0deg); -moz-transform:rotate(0deg); -webkit-transform:rotate(0deg); -o-transform:rotate(0deg); transform:rotate(0deg);'></div>";
                    //content_style += "<div id='number_minus' style='position:relative;top:-6px;cursor:pointer;border-left:1px solid #BFBFBF;display:inline-block;width:20px;height:21px;text-align:center;'><img src='http://www.crazy2go.com/public/img/goods/arrow_deepgray.png' style='position:relative;top:7px;-ms-transform:rotate(180deg); -moz-transform:rotate(180deg); -webkit-transform:rotate(180deg); -o-transform:rotate(180deg); transform:rotate(180deg);'></div>";
                    content_style += "</div> 件<div id='stock' style='display:none;margin-left:20px;color:#969B9C;font-size:8pt;' data-type='"+type_item_length+"' data-stock='"+content_stock+"' data-total='"+content_total+"' data-select='"+(default_status?"0；0；default；default":"")+"' data-select_stock='"+(default_status?content_total:"")+"'> ( 庫存： "+content_total+" 件 ) </div>";
                    content_style += "</div>";
                    content_style += "</div>\r\n"; 
                    
                    content_style += "<div class='tr'><div class='td'></div><div class='td' style='padding:10px 10px 10px 10px;'><input id='added' data-fi_no='"+fi_no+"' type='button' style='background:#EB3339;color:white;border:0px;cursor:pointer;padding:5px;margin-left:5px;' value='加入購物車'></div></div>";
                    
                    $("#cover_content").html(content_style);
                    
                    $(window).trigger("resize.dialog");
                    $("#bg_cover").show();
                    
                    $(".item").click(function() {
                            var type_check = $(this).attr("data-select");
                            var type_item = $(this).attr("data-item");

                            //data-item="尺碼" data-no="19" data-val="透明酒紅色"

                            $(".item").each(function(){
                                    if(type_item == $(this).attr("data-item")){
                                            $(this).css({
                                                'border':'#898989 solid 1px', 
                                                'margin':'5px'
                                            });
                                            $(this).attr('data-select','0');
                                    }
                            });

                            if(type_check == '0') {
                                    $(this).css({
                                        'border':'red solid 1px', 
                                        'margin':'5px'
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
                                                $("#bg_cover").remove();
                                            }
                                            alert(data.message);
                                    }, "json");
                            }
                            else {
                                    alert('庫存不足');
                            }
                    });
                },"json");
            });
        }
        //-------------------------member_recommand - end-------------------------//
        
        //-------------------------member_bonus - start-------------------------//
        
        if(global_url.search('bonus')!=-1) 
        {
            //紅利兌換
            $("#currency2bonus").click(function(){
                var post_currency = parseInt($("#currency2bonus_value").val());
                if(isNaN(post_currency))
                {
                    alert("請輸入數值！");
                    return;
                }
                var currency = parseInt($("#total_currency").text());
                if(currency<100)
                {
                    alert("寶通幣至少100元才能兌換！目前"+currency+"元");
                    return;
                }
                if( post_currency > currency)
                {
                    alert("不得大於跨寶通總額！");
                    return;
                }
                $.post("http://www.crazy2go.com/member/ajax_bonus", {currency:post_currency}, function(data) {
                        if(data.error == 0) {
                            location.href = location.href;
                        }
                        alert(data.message);
                }, "json");
            });
            $("#currency2bonus_value").blur(function(){
                var currency = parseInt($(this).val());
                $(this).val(parseInt(currency/100)*100);
            });
            
            //紅利說明提示
            var selected_style = $(".bonus_hint").eq(0).attr("style");
            var unselected_style = $(".bonus_hint").eq(1).attr("style");
            $(".bonus_hint").click(function(){
               var $this = $(this);
               $(".bonus_hint").attr("style",unselected_style);
               $this.attr("style",selected_style);
               $("#hint_content_display").html($(".bonus_hint_content").eq($(this).index()).html());
            });
            $(".bonus_hint").eq(0).trigger("click");
        }
        
        //-------------------------member_bonus - end-------------------------//
	
	
	
	$("#forget_email").validate({
		rules: {
			email: {
				required: true,
				email: true
			},
			verification: {
				required: true,
				rangelength: [4,4]
			}
		},
		messages: {
			email: "請輸入信箱",
			verification: {
				required: "請輸入驗證碼",
				rangelength: "驗證碼長度不得小於4碼或大於4碼"
			}
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_forget_email", {email:$("#email").val(), verification:$("#verification").val()}, function(data) {
				if(data.error == 0) {
					//location.href = 'http://www.crazy2go.com/member/';
				}
				alert(data.message);
			}, "json");
		}
	});
	
	$("#forget_phone").validate({
		rules: {
			phone: {
				required: true,
				rangelength: [10,11],
				number: true
			},
			verification: {
				required: true,
				rangelength: [4,4]
			}
		},
		messages: {
			phone: {
				required: "請輸入手機",
				rangelength: "手機長度不得小於10碼或大於11碼",
				number: "手機號碼僅能輸入數字"
			},
			verification: {
				required: "請輸入驗證碼",
				rangelength: "驗證碼長度不得小於4碼或大於4碼"
			}
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_forget_phone", {phone:$("#phone").val(), verification:$("#verification").val()}, function(data) {
				if(data.error == 0) {
					//location.href = 'http://www.crazy2go.com/member/';
				}
				alert(data.message);
			}, "json");
		}
	});
	
	$("#verification_email").validate({
		rules: {
			email: {
				required: true,
				email: true
			},
			verification_key: {
				required: true,
				rangelength: [6,32]
			}
		},
		messages: {
			email: "請輸入信箱",
			verification_key: {
				required: "請輸入驗證碼",
				rangelength: "身份驗證碼錯誤"
			}
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_verification_email", {email:$("#email").val(), verification_key:$("#verification_key").val()}, function(data) {
				if(data.error == 0) {
					location.href = 'http://www.crazy2go.com/member/restart/';
				}
				alert(data.message);
			}, "json");
		}
	});
	
	$("#verification_phone").validate({
		rules: {
			phone: {
				required: true,
				rangelength: [10,11],
				number: true
			},
			verification_key: {
				required: true,
				rangelength: [6,32]
			}
		},
		messages: {
			phone: {
				required: "請輸入手機",
				rangelength: "手機長度不得小於10碼或大於11碼",
				number: "手機號碼僅能輸入數字"
			},
			verification_key: {
				required: "請輸入驗證碼",
				rangelength: "身份驗證碼錯誤"
			}
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_verification_phone", {phone:$("#phone").val(), verification_key:$("#verification_key").val()}, function(data) {
				if(data.error == 0) {
					location.href = 'http://www.crazy2go.com/member/restart/';
				}
				alert(data.message);
			}, "json");
		}
	});
	
	$("#restart").validate({
		rules: {
			password: {
				required: true,
				rangelength: [8,16]
			},
			repassword: {
				required: true,
				rangelength: [8,16],
				equalTo: "#password"
			}
		},
		messages: {
			password: {
				required: "請輸入新密碼",
				rangelength: "新密碼長度不得小於8碼或大於16碼"
			},
			repassword: {
				required: "請輸入確認密碼",
				rangelength: "確認密碼長度不得小於8碼或大於16碼",
				equalTo: "密碼與確認密碼不相同"
			}
		},
		submitHandler: function() {
			$.post("http://www.crazy2go.com/member/ajax_restart?"+global_url.split("?")[1], {password:$("#password").val()}, function(data) {
				if(data.error == 0) {
					location.href = 'http://www.crazy2go.com/member/';
				}
				alert(data.message);
			}, "json");
		}
	});
       
        
})

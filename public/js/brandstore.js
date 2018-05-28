$(document).ready(function(){
    $("#bs_product_list div[fi_no]").each(function(){
        var $product = $(this);
        $(this).hover(function(){
            var $this = $(this);
            $this.append("<div class='bs_product' fi_no='"+$product.attr("fi_no")+"' style='cursor:pointer;position:absolute;display:inline-block;left:10px;top:10px;width:192px;height:270px;background:rgba(0,0,0,0.5);color:white;text-align:center;line-height:270px;'>加入購物車</div>");
            //加入購物車
            $this.find(".bs_product").click(function(){
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
                    if(data.error!=0)
                    {
                        alert(data.message);
                        $("#bg_cover").remove();
                        return;
                    }
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
        },function(){
            $(this).find(".bs_product").remove();
        });
    });
    var params = location.href.split('?')[1].split('&');
    var params_len = params.length;
    for(var i=0; i<params_len; i++)
    {
        if($.isArray(params[i]))continue;
        params[i]=params[i].split('=');
        if(params[i][0]=="sort")
        {
            switch(params[i][1])
            {
                case "comprehensive":
                    $("#filter_selection span").eq(0).css({
                        "border":"1px solid red",
                        "background":"white",
                        "border-bottom":"1px solid white"
                    });
                    break;
                case "news":
                    $("#filter_selection span").eq(1).css({
                        "border":"1px solid red",
                        "background":"white",
                        "border-bottom":"1px solid white"
                    });
                    break;
                case "sales":
                    $("#filter_selection span").eq(2).css({
                        "border":"1px solid red",
                        "background":"white",
                        "border-bottom":"1px solid white"
                    });
                    break;
            }
        }
        switch(params[i][0])
        {
            case "brand":
                $(".template_center").height($(".template_center").height()-220);
                break;
            case "bskeyword":
                $("#bs_keyword").val(decodeURI(params[i][1]));
                break;
            case "lowprice":
                $("#bs_low_price").val(params[i][1]);
                break;
            case "highprice":
                $("#bs_high_price").val(params[i][1]);
                break;
            case "checkedlist":
                if(params[i][1]=="")break;
                var checked = params[i][1].split(",");
                var cLen = checked.length;
                for(var i = 0; i < cLen; i++)
                {
                    $("#bs_subset .td[fi_no='"+checked[i]+"'] input[type=checkbox]").prop("checked",true);
                }
                break;
        }
    }
    
    $("#filter_selection span").each(function(index){
        var $this = $(this);
        $this.css({
           "cursor":"pointer",
           "padding":"10px",
           "top":"-13px",
           "position":"absolute",
           "left":($this.width()+20)*index+"px",
           "height":"8px"
        });
        $this.click(function(){
            
            switch(index)
            {
                case 0:
                    location.href = $("#sorter > div div").eq(0).find("a").attr('href');
                    break;
                case 1:
                    location.href = $("#sorter > div div").eq(2).find("a").attr('href');
                    break;
                case 2:
                    location.href = $("#sorter > div div").eq(4).find("a").attr('href');
                    break;
            }
        });
    });
    if(location.href.search("sort")==-1)
    $("#filter_selection span:first").trigger("click");
                            
    $("#bs_search").click(function(){
        var keyword = $("#bs_keyword").val();
        var low_price = $("#bs_low_price").val();
        var high_price = $("#bs_high_price").val();
        var checked_array = [];
        var url = location.href+"&";
        
        $("#bs_subset .td input[type=checkbox]:checked").each(function(){
            checked_array.push($(this).parent().attr("fi_no"));
        });
        checked_array = checked_array.join(",");
        if(keyword.length < 2 && keyword.length > 0)
        {
            alert("搜尋關鍵字至少2個字元！");
            return;
        }
        if(low_price != "" && high_price == "")
        {
            high_price = low_price;
        }
        else if(low_price == "" && high_price != "")
        {
            low_price = high_price;
        }
        if(url.search("bskeyword")!=-1)
        {
            url = url.replace(/&bskeyword=.*&/,"");
        }
        url = url + '&bskeyword='+keyword;
        if(url.search("lowprice")!=-1)
        {
            url = url.replace(/&lowprice=[a-zA-Z0-9]*&/,"");
        }
        url = url + '&lowprice='+low_price;
        if(url.search("highprice")!=-1)
        {
            url = url.replace(/&highprice=[a-zA-Z0-9]*&/,"");
        }
        url = url + '&highprice='+high_price;
        if(url.search("checkedlist")!=-1)
        {
            url = url.replace(/&checkedlist=[,a-zA-Z0-9]*&/,"");
        }
        url = url + '&checkedlist='+checked_array;
        url = url.replace(/&&/g,"&");
        location.href = url;
    });
    
    $("#ad_slide").each(function(){
        var $slide = $(this);
        $slide.find("div:first span:first").css({"background":"#FF1493"});
        $slide.find("div:first span").each(function(index){
            var $this = $(this);
            $this.css({"cursor":"pointer"});
            $this.mouseover(function(){
                $slide.find("div").not(":first").fadeOut();
                $slide.find("div:first span").css({"background":"gray"});
                $slide.find("div").eq(index+1).fadeIn();
                $slide.find("div:first span").eq(index).css({"background":"#FF1493"});
            });
        });
    });
    
    $("#brand_left_btn").click(function(){
        var $brand = $("#brand_slide");
        var count = $brand.find("span").size()-4;
        if(count>4)count = 4;
        if(count>0)
        for(var i=0;i<count;i++)
        $brand.append($brand.find("span:first"));
    });
    
    $("#brand_right_btn").click(function(){
        var $brand = $("#brand_slide");
        var count = $brand.find("span").size()-4;
        if(count>4)count = 4;
        if(count>0)
        for(var i=0;i<count;i++)
        $brand.prepend($brand.find("span:last"));
    });
});
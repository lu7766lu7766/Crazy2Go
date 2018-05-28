$(document).ready(function(){
    var sendParams = {
            url:"",
            keyword:"",
            page:1
        };
        
    var btnCSS = {
        "cursor":"pointer",
        "margin":"2px",
        "padding-left":"6px",
        "padding-right":"6px",
        "border":"#696969 solid 1px",
        "line-height":"24px",
        "display":"inline-block",
        "border-radius":"5px",
        "color":"#696969"
        //"color":"#C7C7C7"
    };
    
    var selectedCSS = {
        "cursor":"pointer",
        "margin":"2px",
        "padding-left":"6px",
        "padding-right":"6px",
        "border":"#FF0000 solid 1px",
        "line-height":"24px",
        "display":"inline-block",
        "border-radius":"5px",
        "color":"#696969"
        //"color":"#C7C7C7"
    };
    
    $("#brand_alpha_search,#brand_class_search").each(function(){
        var $this = $(this);
        $this.find("span").css(btnCSS);
        $this.find("span").bind("click",function(){
            var $this = $(this);
            $("#brand_alpha_search,#brand_class_search").find("span").css(btnCSS);
            $this.css(selectedCSS);
            if($this.parent().attr("id") == "brand_alpha_search")
            {
                sendParams.url = "http://www.crazy2go.com/brand/ajax_alpha";
                sendParams.keyword = $this.text();
                sendParams.page = 1;
            }
            if($this.parent().attr("id") == "brand_class_search")
            {
                sendParams.url = "http://www.crazy2go.com/brand/ajax_class";
                sendParams.keyword = $this.attr("fi_no");
                sendParams.page = 1;
            }
            $.post(sendParams.url,sendParams,showImage,"json");
        })
    });
    
    $("#brand_keyword_search_btn").click(function(){
        var $textField = $("#brand_keyword_search");
        if($textField.val() == "")
        {
            alert("請輸入品牌關鍵字");
            return;
        }
        if($textField.val().length < 2)
        {
            alert("關鍵字至少2個字");
            return;
        }
        
        sendParams.url = "http://www.crazy2go.com/brand/ajax_keyword";
        sendParams.keyword = $textField.val();
        sendParams.page = 1;
        $.post(sendParams.url,sendParams,showImage,"json");
    });
    
    var url = location.href;
    url = url.split("?");
    if(url.length==2)
    {
        var params = url[1].split("&");
        var keyword = "";
        for(var i in params){
            if(params[i].search("keyword")!=-1)
            {
                params = params[i].split("=");
                keyword = decodeURI(params[1]);
                if(keyword=="")
                {
                    $("#brand_alpha_search span:first").trigger('click');
                    break;
                }
                $("#brand_keyword_search").val(keyword);
                $("#brand_keyword_search_btn").trigger("click");
                break;
            }
        }
    }
    else
    {
        $("#brand_alpha_search span:first").trigger('click');
    }
    
    $("#footer_right .right_ad").each(function(index){
        var color;
        switch(index)
        {
            case 0:color='#e61726';break;
            case 1:color='#89007e';break;
            case 2:color='#b2380a';break;
            case 3:color='#ea617a';break;
        }
        var $slide = $(this);
        $slide.find("div:first span:first").css({"background":color});
        $slide.find("div:first span").each(function(index){
            var $this = $(this);
            $this.css({"cursor":"pointer"});
            $this.mouseover(function(){
                $slide.find("div").not(":first").fadeOut();
                $slide.find("div:first span").css({"background":"gray"});
                $slide.find("div").eq(index+1).fadeIn();
                $slide.find("div:first span").eq(index).css({"background":color});
            });
        });
    });
    
    $("#footer_left > div div").each(function(){
        $(this).css({
            "border-left":"6px solid white",
            "cursor":"pointer"
        });
        $(this).hover(function(){
            $(this).css({
                "border-left":"1px solid gray"
            });
            $(this).find("span").eq(0).css({
                "color":"red"
            });
            $(this).prepend("<div id='arrow'></div>");
            var $arrow = $(this).find("#arrow");
            $arrow.css({
                "float":"left",
                "margin-top":"85px",
                "width":"0px",
                "height":"0px",
                "border-style":"solid",
                "border-width":"5px 0 5px 5px",
                "border-color":"transparent transparent transparent gray"
            });
        },function(){
            $(this).css({
                "border-left":"6px solid white"
            });
            $(this).find("span").eq(0).css({
                "color":"black"
            });
            var $arrow = $(this).find("#arrow");
            $arrow.remove();
        });
    });
    
    function showImage(back){
        $("#brand_logos").html("");
        var i = 0;
        var len = back.add.data.length;
        var output = "";
        for(i = 0; i<len; i++)
        {
            output += "<div style='background:url(http://www.crazy2go.com/public/img/logo/"+(back.add.data[i]["logo"]||"test.jpg")+") 50% 50% no-repeat;' data-name='"+back.add.data[i]["name"]+"' data-href='"+back.add.data[i]["fi_no"]+"'></div>";
        }
        $("#brand_logos").html(output);
        $("#brand_logos div").css({
            "position":"relative",
            "display":"inline-block",
            "margin":"7px 10px",
            "border":"1px solid #d6d2d4",
            "width":"180px",
            "height":"90px"
        })
        $("#brand_logos div").hover(function(){
            var $this = $(this);
            $this.append("<a class='brand_name' href='http://www.crazy2go.com/brandstore?brand="+$this.attr("data-href")+"'><div style='position:absolute;display:inline-block;left:-1px;top:-1px;width:182px;height:92px;background:rgba(0,0,0,0.5);color:white;text-align:center;line-height:92px;'>"+$this.attr("data-name")+"</div>");
        },function(){
            $(this).find(".brand_name").remove();
        });
        $("#brand_pager").html(back.add.page_content);
        $("#brand_pager .page").css({"cursor":"pointer"}).click(function(){
            sendParams.page = $(this).attr("data-page");
            $.post(sendParams.url,sendParams,showImage,"json");
        });
    }
});



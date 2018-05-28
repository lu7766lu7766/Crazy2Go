
$(document).ready(function(){
    var viewedArray = [],
        currentViewNo = "";
        
    $("#qanda_menu > div").each(function(index){
        var $this = $(this);
        $this.attr("index",index);
        //$("#qanda_menu > div > div").hide();
        /*$this.click(function(){
            if($(this).attr("index") == $("#qanda_menu").data("index"))return;
            $("#qanda_menu").data("index",$(this).attr("index"));
            $("#qanda_menu > div > div").slideUp();
            $this.find("div").slideDown();
        });*/
        $this.find("div span").click(function(){
            $("#qanda_menu").find("span").each(function(){
                $(this).data("selected",false);
                $(this).trigger("mouseleave");
            })
            $(this).data("selected",true);
            
            $(this).css({
                "cursor":"default",
                "color":"white",
                //"background":"#EB3339 url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                //"border-left":"0px solid #EB3339",
                "background":"#CCC url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                "border-left":"0px solid #CCC",
                "padding-left":"37px",
                "width":"172px",
                "left":"-1px"
            });
            $("#shadow").remove();
            $(this).append("<div id='shadow'></div>");
            $(this).find("#shadow").css({
                "position":"absolute",
                "left":"0px",
                "top":"0px",
                "width":"209px",
                "height":"34px",
                "-webkit-box-shadow":"#CCC 0px 2px 3px",
                "-moz-box-shadow":"#CCC 0px 2px 3px",
                "z-index":"9"
            });
            
            $("#qanda_content").html("Loading......");
            currentViewNo = $(this).attr("no");

            var i = 0,
                l = viewedArray.length,
                needPost = true;

            for(i = 0; i < l; i++)
            {
                if(viewedArray[i][0] == currentViewNo)
                {
                    $("#qanda_content").html(viewedArray[i][1]);
                    needPost = false;
                    break;
                }
            }
            $("#path").text($(this).parent().siblings().text()+" > "+$(this).text());
            $('body').scrollTop(200);
            if(needPost)
            $.post("http://www.crazy2go.com/qanda/ajax_select_qa/",{qa_no:currentViewNo},function(data){
               viewedArray.push([currentViewNo, data]);
               $("#qanda_content").html(data); 
               var slide = location.href.split("?")[1];
               if(slide)
               {
                   var fi_no = slide.split("&")[0].split("=")[1];
                   var pick = $("div[fi_no='"+fi_no+"']");
                   $(window).scrollTop( pick.offset().top );
               }
            });
        });

        var $span = $("#qanda_menu > div > span");
        $span.css({
            "display":"inline-block",
            "position":"relative",
            "width":"180px",
            "padding":"10px 0px 10px 27px",
            "border-left":"2px solid #FFFFFF",
            "cursor":"pointer"
        });
        
        $span.each(function(ind){
            if(ind != 0)
            $(this).css({"border-top":"1px solid #D4D4D4"});
        });
        
        $span.hover(function(){
            $(this).css({
                "background":"#F6F6F6 url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                "border-left":"2px solid #EC5256",
                "padding-left":"28px",
                "width":"179px",
                "left":"-1px"
            });
        },function(){
            $(this).css({
                "background":"#FFFFFF",
                "border-left":"2px solid #FFFFFF",
                "padding-left":"27px",
                "width":"179px",
                "left":"0px"
            });
        }).trigger("mouseenter").trigger("mouseout");
        
        var $span2 = $("#qanda_menu > div > div span");
        $span2.css({
            "display":"inline-block",
            "position":"relative",
            "width":"180px",
            "padding":"10px 0px 10px 36px",
            "border-left":"2px solid #FFFFFF",
            "cursor":"pointer",
            "color":"rgb(150, 155, 156)",
            "font-size":"8pt",
            "font-weight":"normal"
        });
        
        $span2.hover(function(){
            if(!$(this).data("selected"))
            {
                $(this).css({
                    "background":"#F6F6F6 url(http://www.crazy2go.com/public/img/template/myaccount_1-3.png) no-repeat 4% 50%",
                    "border-left":"2px solid #EC5256",
                    "padding-left":"37px",
                    "width":"170px",
                    "left":"-1px"
                });
            }
        },function(){
            if(!$(this).data("selected"))
            {
                $(this).css({
                    "background":"#FFFFFF",
                    "border-left":"2px solid #FFFFFF",
                    "padding-left":"36px",
                    "width":"170px",
                    "color":"rgb(150, 155, 156)",
                    "left":"0px"
                });
            }
        }).trigger("mouseenter").trigger("mouseout");

    });
    
    //預設開第一個
    $("#qanda_menu > div:first").find("div span:first").trigger("click");
    
    $("#qanda_menu > div").find("div span[default]").each(function(){
        var $this = $(this);
        $this.parent().parent().trigger("click");
        $this.trigger("click");
        $("#path").text($this.parent().siblings().text()+" > "+$this.text()+" > Q"+$this.attr("item"));
    });
    
    $("#qanda_search input[type='button']").click(function(){
        search(1);
    });
    
    $("#qanda_search input[type='text']").keypress(function(e){
        var code = e.keyCode?e.keyCode:e.which;
        if(code == 13)
        {
            search(1);
        }
    })
    
});

var qanda_keyword="";
var qanda_contents=[];
var qanda_curPage=1;

function search(page){
    
    var keyword = $("#qanda_search input[type='text']").val();
    if(keyword == "")
    {
        alert("請輸入關鍵字!");
        return;
    }
    
    if(keyword!=qanda_keyword)
    {
        qanda_contents=[];
        qanda_keyword = keyword;
    }
    qanda_curPage = page;
    $("#qanda_content").html("Loading......");
    
    var i = 0;
    var len = qanda_contents.length;
    for(i = 0; i < len; i++)
    {
        if(qanda_contents[i][0] == page)
        {
            $("#qanda_content").html(qanda_contents[i][1]);
            return;
        }
    }
    
    $.post("http://www.crazy2go.com/qanda/ajax_qa_search/",{page:page,keyword:keyword},function(data){
            qanda_contents.push([qanda_curPage,data]);
            $("#qanda_content").html(data); 
    });
}
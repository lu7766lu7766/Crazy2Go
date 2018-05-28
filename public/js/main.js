$(function(){
   //Brand Slide
   $("#brand_left_btn,#brand_right_btn").css({"cursor":"pointer"});
   $("#brand_left_btn").click(function(){
       var $brand = $("#brand_slide");
       var count = $brand.find("span").size()-7;
       if(count>7)count = 7;
       if(count>0)
       for(var i=0;i<count;i++)
       $brand.append($brand.find("a:first"));
   });
   $("#brand_right_btn").click(function(){
       var $brand = $("#brand_slide");
       var count = $brand.find("span").size()-7;
       if(count>7)count = 7;
       if(count>0)
       for(var i=0;i<count;i++)
       $brand.prepend($brand.find("a:last"));
   });
   //Floor
   $(".floor").each(function(){
       var $floor = $(this);
       var $logo_slide = $floor.find(".logo_slide");
       var moveDis = $logo_slide.width();
       var current_posx = 0;
       var is_animated = false;
       //type
       if($floor.find(".floor_grids").attr("type")==2)
       {
           $floor.find(".floor_grids").before($floor.find(".floor_right_slide"));
       }
       //左右滑按鈕手勢圖
       $floor.find(".logo_slide_left_button,.logo_slide_right_button").css({"cursor":"pointer"});
       //初始化 logo slide 位置
       $logo_slide.find("div").each(function(index){
            var $this = $(this);
            $this.attr("index",index);
            $this.css({
               "position":"absolute",
               "left": $logo_slide.width()*index+"px",
               "top": "0px"
            });
        });
        //左滑
        $floor.find(".logo_slide_left_button").click(function(){
            if(is_animated)return;
            is_animated = true;
            $logo_slide.find("div").transition({
                x:current_posx-=moveDis
            },500,function(){
                if(is_animated)
                {
                    is_animated = false;
                    $logo_slide.find("div").transition({
                        x:current_posx+=moveDis
                    },0);
                    $logo_slide.append($logo_slide.find("div:first"));
                    $logo_slide.find("div").each(function(index){
                        var $this = $(this);
                        $this.attr("index",index);
                        $this.css({
                           "position":"absolute",
                           "left": $logo_slide.width()*index+"px",
                           "top": "0px"
                        });
                    });
                }
            })
        });
        //右滑
        $floor.find(".logo_slide_right_button").click(function(){
            if(is_animated)return;
            is_animated = true;
            $logo_slide.find("div").transition({
                x:current_posx-=moveDis
            },0);
            $logo_slide.prepend($logo_slide.find("div:last"));
            $logo_slide.find("div").each(function(index){
                var $this = $(this);
                $this.attr("index",index);
                $this.css({
                   "position":"absolute",
                   "left": $logo_slide.width()*index+"px",
                   "top": "0px"
                });
            });
            $logo_slide.find("div").transition({
                x:current_posx+=moveDis
            },500,function(){
                is_animated = false;
            });
        });
        
        //floor 右側 slide
        $floor.find(".floor_right_slide div:first span:first").css({"background":"#FF1493"});
        $floor.find(".floor_right_slide").each(function(){
            var $slide = $(this);
            $slide.find("div:first").after("<div class='slide_cover' style='position:absolute;width:100%;height:100%;z-index:98;overflow:hidden;'></div>");
            $slide.append("<div class='slide_cover2' style='position:absolute;width:100%;height:100%;z-index:97;overflow:hidden;'></div>");
            
            $slide.data("autoslide_index",1);
            $slide.data("autoslide_len",$slide.find("a").size());
            $slide.data("go_slide_left",function(){
                if($slide.data("autoslide_index")>=$slide.data("autoslide_len"))$slide.data("autoslide_index",0);
                $slide.find("div:first span").eq($slide.data("autoslide_index")).trigger("mouseover");
                $slide.data("autoslide_index",$slide.data("autoslide_index")+1);
            });
            $slide.data("tid",setInterval($slide.data("go_slide_left"),5000));
            
            $slide.find("div:first span").each(function(index){
                var $this = $(this);
                $this.css({"cursor":"pointer"});
                $this.data("index",index);
                $this.mouseover(function(){
                    var $slide_cover = $slide.find(".slide_cover");
                    $slide_cover.html("");
                    var $clone = $slide.find("a").eq($this.data("index")).clone().css({
                        "position":"absolute",
                        "left":$slide_cover.width()+"px",
                        "top":"0px"
                    });
                    $slide_cover.html($clone);
                    $clone.transition({
                        x: -$slide_cover.width()
                    }, 750,function(){
                        $slide.find(".slide_cover2").html($slide_cover.html());
                    });
                    $slide.find("div:first span").css({"background":"gray"});
                    $slide.find("div:first span").eq($this.data("index")).css({"background":"#FF1493"});
                    clearInterval($slide.data("tid"));
                    $slide.data("tid",setInterval($slide.data("go_slide_left"),5000));
                });
            });
        });
   });
    //畫面最上方 slide
    var $primary = $("#primary_slide");
    var autoslide_index = 1;
    var autoslide_len = $primary.find("div:first span").size();
    function go_slide(){
        if(autoslide_index>=autoslide_len)autoslide_index=0;
        $primary.find("div:first span").eq(autoslide_index++).trigger("mouseover");
    }
    $primary.find("div:first span:first").css({"background":"#FF1493"});
    $("#primary_back").css({"background": $primary.find("div:first span:first").attr("data-color")});
    $primary.find("div:first span").each(function(index){
        var $this = $(this);
        $this.css({"cursor":"pointer"});
        $this.data("index",index);
        $this.mouseover(function(){
            autoslide_index = $this.data("index")+1;
            $primary.find("div").not(":first").fadeOut();
            $primary.find("div:first span").css({"background":"gray"});
            $primary.find("div").eq($this.data("index")+1).fadeIn();
            $primary.find("div:first span").eq($this.data("index")).css({"background":"#FF1493"});
            $("#primary_back").css({
               "background":$(this).attr("data-color")
            });
            clearInterval(tid);
            tid = setInterval(go_slide,5000);
        });
    });
    var tid = setInterval(go_slide,5000);
    
});
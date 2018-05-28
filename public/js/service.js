
$(document).ready(function(){
    //http://www.crazy2go.com/service/
    $("#beforeBuy").click(function(){
        $.post("http://www.crazy2go.com/service/ajax_select_service/",{type:1,store:1},function(qq){
            openWin($.trim(qq));
        });
    });
    
    $("#afterBuy").click(function(){
        $.post("http://www.crazy2go.com/service/ajax_select_service/",{type:2,store:1},function(qq){
            openWin($.trim(qq));
        });
    });
    
    $("#productQA").click(function(){
        $.post("http://www.crazy2go.com/service/ajax_select_service/",{type:3,store:1},function(qq){
            openWin($.trim(qq));
        });
    });

});
var mouse_x;
var mouse_y;
var website_url = "http://www.crazy2go.com/";

$(function(){
    var $window = $(window),
        $header = $("div#wrapper div#header"),
        $body = $("div#wrapper div#body"),
        $body_left = $("div#wrapper div#body div#body_left"),
        $body_right = $("div#wrapper div#body div#body_right"),
        $footer = $("div#wrapper div#footer");
        
    // ******************** mouse position ********************
    $(document).on("mousemove", function( event ) {
        mouse_x = event.pageX;
        mouse_y = event.pageY;
    });

    // ******************** menu ********************
    $("div#wrapper #body #body_left p").bind("mouseover",function(){
        var $this = $(this);
        $this.animate({
            "borderColor":"rgb(100,100,100)",
            "borderLeftWidth":"5px",
        },100);
    });
    
    $("div#wrapper #body #body_left p").bind("mouseleave",function(){
        var $this = $(this);
        $this.animate({
            "borderColor":"rgb(255,255,255)",
            "borderLeftWidth":"0px",
        },100);
    });
    
    $("div#wrapper #body #body_left p").bind("click",function(){
        var $this = $(this);
        var link = $this.find("a").attr("href");
        if(link.search("javascript") != -1)
        {
            $.post("sql_logout.php",{
                query_type:'logout'
            },function(){
                location.href = "index.php";
            })
        }else{
            location.href = link;
        }
    });
    
    $("div#wrapper #body #body_left p").each(function(){
        var url = $(this).find("a").attr("href");
        var selected_url = location.href.split("/");
        selected_url = selected_url[selected_url.length-1];
        selected_url = selected_url.split("?")[0];
        if(url == selected_url){
            $(this).css({"background":"rgba(255,255,255,0.8)"});
            $("div#wrapper #body #body_right").prepend("<h2>"+$(this).find("a").text()+"</h2>");
        }
    });
    
    // ******************** window resize ********************
    $window.bind("resize",function(){
        var h = $footer.offset().top - $body.offset().top;
        $body.height(h);
        $body_left.height(h);
        $body_right.height(h-20);//padding-fix
    }).trigger("resize");
    
    // ******************** style init ********************
    init_style();
    
});

function init_style($html){
	
    $html = $html || $("*");
    
    // ******************** dialog ********************
    $html.each(function(){
        var $this = $(this);
        if($this.data("be_dialog") != undefined) return;
        if($this.attr('data-dialog') == undefined) return;
        $this.data("be_dialog",1);
        $this.hide();
    });
    
    $html.each(function(){
        var $this = $(this);
        if($this.data("be_open-dialog") != undefined) return;
        if($this.attr('data-open-dialog') == undefined) return;
        $this.data("be_open-dialog",1);
        $this.bind("click",function(){
            var $this = $(this);
            var dialog_name = $this.attr("data-open-dialog");
            var target = $("*[data-dialog='"+dialog_name+"']").clone(true).show();
            var $source	= $("*[data-dialog='"+dialog_name+"']");
            var $dialog_bg = $("body").append("<div id='dialog_bg' onclick='/*closeDialog();*/'></div>").find("#dialog_bg");
            var $dialog_container = $("body").append(
                    "<div id='dialog_container' style='width:970px;'>"+
                        "<div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg'>"+
                            "<span id='dialog_title'>"+dialog_name+"</span>"+
                            "<span style='float:right;cursor:pointer;' onclick='closeDialog();'>ｘ</span>"+
                        "</div>"+
                        "<div id='dialog_content' style='overflow-y:auto;'></div>"+
                    "</div>"
            ).find("#dialog_container");
            $dialog_container.addClass("shadowRoundCorner");
            $dialog_container.find("div").eq(0).addClass("shadowRoundCorner");
            $dialog_container.find("#dialog_content").append(target);
            $dialog_container.hide();
            $dialog_container.fadeIn();
            $dialog_container.css({
                "position":"absolute",
                "display":"inline-block",
                "padding":"5px",
                "width":$dialog_container.find("#dialog_content").width()+"px",
                "background":"rgba(255,255,255,0.9)",
                "border":"3px dashed #CCC"
            });
            $dialog_container.find("input[type='text']:first").focus();
            
            $dialog_bg.css({
                "position":"absolute",
                "display":"inline-block",
                "background":"rgba(0,0,0,0.8)"
            });
            
            $(window).on("resize.dialog",function(){
                $dialog_bg.width($(window).width());
                $dialog_bg.height($(window).height());
                $dialog_container.css({
                    "top":"50px",
                    "bottom":"50px",
                    "left":($dialog_bg.width()-$dialog_container.width())/2+"px"
                });
                $dialog_container.find("#dialog_content").height($dialog_container.height()-30);
            }).trigger("resize.dialog");
            //
            
            $source.prop("id",$source.prop("id")+"_jacsource");
            $source.find("*[id]").each(function(){
                $(this).prop("id",$(this).prop("id")+"_jacsource");
            })
            $source.find("*[for]").each(function(){
                $(this).prop("for",$(this).prop("for")+"_jacsource");
            })
            $source.prop("name",$source.prop("name")+"_jacsource");
            $source.find("*[name]").each(function(){
                $(this).prop("name",$(this).prop("name")+"_jacsource");
            })
            $source.prop("class",$source.prop("class")+"_jacsource");
            $source.find("*[class]").each(function(){
                $(this).prop("class",$(this).prop("class")+"_jacsource");
            })
        });
    });
    
    // ******************** table init ********************
    $html.each(function(){
        var $this = $(this);
        if($this.data("be_table") != undefined) return;
        if($this[0].tagName.toLowerCase() != "table") return;
        $this.data("be_table",1);
        $this.find("td").css({
            "padding":"10px"
        });
    });
    
    $html.each(function(){
        var $this = $(this);
        if($this.data("be_table-h") != undefined) return;
        if($this[0].tagName.toLowerCase() != "table") return;
        if(!$this.hasClass("table-h")) return;
        $this.data("be_table-h",1);
        $this.addClass("shadowRoundCorner").addClass("bg");
        $this.find("tr").css({
            "text-align":"center",
            "background":"rgb(255,255,255)",
            "border-bottom":"1px solid #AAA"
        });
        $this.find("tr:last").css({
            "background":"rgb(255,255,255)",
            "border-bottom":"0px solid #AAA"
        });
        $this.find("tr:first").css({
            "color":"white",
            "background":"transparent",
            "font-weight":"bold"
        });
    });
    
    $html.each(function(){
        var $this = $(this);
        if($this.data("be_table-v") != undefined) return;
        if($this[0].tagName.toLowerCase() != "table") return;
        if(!$this.hasClass("table-v")) return;
        $this.data("be_table-v",1);
        $this.addClass("shadowRoundCorner").addClass("bg");
        $this.find("tr").each(function(){
            var $this = $(this);
            $this.find("td").css({
                "text-align":"left",
                "background":"rgb(255,255,255)",
                "border-bottom":"1px solid #AAA"
            });
            $this.find("td:first").css({
                "color":"white",
                "background":"transparent",
                "text-align":"right",
                "font-weight":"bold"
            }).addClass("bg");
        });
        $this.find("tr:last td").css({
            "border-bottom":"0px solid #AAA"
        });
    });
    
    $html.each(function(){
        var $this = $(this);
        if($this.data("be_table-n") != undefined) return;
        if($this[0].tagName.toLowerCase() != "table") return;
        if(!$this.hasClass("table-n")) return;
        $this.data("be_table-n",1);
        $this.width(0);
        $this.find("td").css({
            "text-align":"left",
            "background":"rgba(255,255,255,1)",
            "border-bottom":"0px solid #AAA",
            "color":"black",
            "font-weight":"normal"
        }).removeClass("bg");
    });
}
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

function clear_source(){
	$("*[id$='_jacsource']").each(function(){
		var id=$(this).prop("id");
		id=id.replace("_jacsource", "");
		$(this).prop("id",id);
	})
	$("*[name$='_jacsource']").each(function(){
		var name=$(this).prop("name");
		name=name.replace("_jacsource", "");
		$(this).prop("name",name);
	})
	$("*[for$='_jacsource']").each(function(){
		var id=$(this).prop("for");
		id=id.replace("_jacsource", "");
		$(this).prop("for",id);
	})
	$("*[class$='_jacsource']").each(function(){
		var t_class=$(this).prop("class");
		t_class=t_class.replace("_jacsource", "");
		$(this).prop("class",t_class);
	})
	$("*[float]").hide();//浮動視窗隱藏
}

function closeDialog(){
    //if(!confirm("確定關閉對話框？"))return;
    clear_source();
    $("#dialog_bg,#dialog_container").remove();
    if($("div#url_edit_panel").length>0)
    	$("div#url_edit_panel").hide();
}

function alert2(content){
	var $jdialog=$("div#alert_jdialog")
	if($jdialog.length==0){
		 $("body").append("<div id='alert_jdialog'><div id='alert_jtitle'>警告視窗</div></div>");
		 $jdialog=$("div#alert_jdialog")
		 $("#alert_jtitle").css({
			 "-webkit-border-radius": "4px",
			 "-moz-border-radius"	: "4px",
			 "border-radius": "4px",
			 "color"		: "white",
             "background"	: "transparent",
             "text-align"	: "left",
             "font-weight"	: "bold",
             "background"	: "#000 url(../backend/img/bg.gif)",
			 "opacity"		: "0.8",
			 "margin"		: "10",
			 "padding"		: "5"
		 }).addClass("bg")
		 $jdialog.css({
			 "position" : "absolute",
			 "display"	: "inline-block",
			 "padding"	: "5px",
			 "width"	: $(window).width()/3+"px",
			 "height"	: $(window).height()/3+"px",
			 "top"		: $(window).height()/3,
			 "left"		: $(window).width()/3,
			 "overflow"	: "hidden",
			 "background":"rgba(255,255,255,0.9)",
			 "border"	: "3px solid #666",
			 "z-index"	: "100"
		 })
	}else{
		$jdialog.show()
	}
	$jdialog_content = $("<div>"+content+"</div>");
	$jdialog_content.css({
		"margin"	: "10",
		"padding"	: "5",
		"border-bottom"	: "dashed #999 2px"
	})
	$jdialog.append($jdialog_content);
	$jdialog_content.animate({
		opacity: 0
	}, 3000,"easeInCubic", function() {
		if($jdialog.find("div").length==2){
			$jdialog.hide();
		}
	    $(this).remove();
	});
}

var url = window.location.pathname;
var filename = url.substring(url.lastIndexOf('/')+1);
filename = "sql_"+filename.replace(/manage_/g, "");
//filename = "sql_"+filename.substring(filename.lastIndexOf('_')+1);
function update_select(mode){
    mode = mode || "edit";
    $("select.category_sel").unbind("change")
    $("select.category_sel").change(function(){
        
        $obj = $(this).prev();
        while( $obj.attr("class")=="category_sel" ) { $obj = $obj.prev();}
        if( $(this).val()=="" )
        {
            $obj.val($(this).prev().val()).trigger("change");
        }
        else
        {
            $obj.val($(this).val()).trigger("change");
		}
		$this = $(this);
		
		while($(this).next("select").length>0){$(this).next("select").remove()}
		
		if( $(this).val()==""||$(this).val()==0 )return "";
		
		$.post(filename,{
        		 query_type:"get_category"
        		,index:$this.val()
			},function(data){
				$sel = $(data);
				if(mode=="edit")
				{
					$sel.find("option[value='"+edit_no+"']").remove();
				}
				$this.after($sel);
				update_select(mode);
        });
    });
}
/*
jQuery.fn.extend({
	setCursorPosition: function(position){
		if(this.length == 0) return this;
		return $(this).setSelection(position, position);
	},

	setSelection: function(selectionStart, selectionEnd) {
		if(this.length == 0) return this;
		input = this[0];

		if (input.createTextRange) {
			var range = input.createTextRange();
			range.collapse(true);
			range.moveEnd('character', selectionEnd);
			range.moveStart('character', selectionStart);
			range.select();
		} else if (input.setSelectionRange) {
			input.focus();
			input.setSelectionRange(selectionStart, selectionEnd);
		}

		return this;
	},

	focusEnd: function(){
		this.setCursorPosition(this.val().length);
		return this;
	},

	getCursorPosition: function() {
		var el = $(this).get(0);
		var pos = 0;
		if('selectionStart' in el) {
			pos = el.selectionStart;
		} else if('selection' in document) {
			el.focus();
			var Sel = document.selection.createRange();
			var SelLength = document.selection.createRange().text.length;
			Sel.moveStart('character', -el.value.length);
			pos = Sel.text.length - SelLength;
		}
		return pos;
	},

	insertAtCursor: function(myValue) {
		return this.each(function(i) {
			if (document.selection) {
			  //For browsers like Internet Explorer
			  this.focus();
			  sel = document.selection.createRange();
			  sel.text = myValue;
			  this.focus();
			}
			else if (this.selectionStart || this.selectionStart == '0') {
			  //For browsers like Firefox and Webkit based
			  var startPos = this.selectionStart;
			  var endPos = this.selectionEnd;
			  var scrollTop = this.scrollTop;
			  this.value = this.value.substring(0, startPos) + myValue + 
							this.value.substring(endPos,this.value.length);
			  this.focus();
			  this.selectionStart = startPos + myValue.length;
			  this.selectionEnd = startPos + myValue.length;
			  this.scrollTop = scrollTop;
			} else {
			  this.value += myValue;
			  this.focus();
			}
	  	})
	}
})
	*/
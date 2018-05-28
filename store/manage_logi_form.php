<?php 
    session_start();
    include '../swop/setting/config.php';
    include '../backend/template.php';

    if(!empty($_SESSION['backend']['login_user'])){
        
        //取Session資料
        $login_fi_no = $_SESSION['backend']['login_fi_no'];
        $login_store = $_SESSION['backend']['login_store'];
        $login_store_name = $_SESSION['backend']['login_store_name'];
        $login_name = $_SESSION['backend']['login_name'];
        $login_permissions = $_SESSION['backend']['login_permissions'];
        $login_user = $_SESSION['backend']['login_user'];
        $login_username = $_SESSION['backend']['login_username'];
        $login_title = $_SESSION['backend']['login_title'];
        $menu = $_SESSION['backend']['menu'];
        
        //取資料
        require_once "../swop/library/dba.php";
        $dba = new dba();
        
        //所有物流模板資料
        $all_logi = array();
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select * from store_shipping where store=".$login_store." ".$orderby;
        if(isset($_GET['keyword']))
        {
            switch($_GET['type'])
            {
                case '1'://物流模板ID
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from store_shipping where store=".$login_store." and fi_no >= ".(int)$_GET['keyword']." ".$orderby;
                    break;
            }
        }

        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $len = count($result);
        
        for($i = 0; $i < $len; $i++)
        {
            $all_logi[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['name'],
                item => $result[$i]['item'],
                images => $result[$i]['images']
            );
        }

        //依權限顯示
        $display_add = strpos($login_permissions,"logi_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_delete = strpos($login_permissions,"logi_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_copy = strpos($login_permissions,"logi_copy")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_edit = strpos($login_permissions,"logi_edit")!==false || strpos($login_permissions,"all")!==false?true:false;
        
    }else{
        header("Location:index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $html_title;?></title>
        <?php echo $html_resource;?>
        <script src="http://www.crazy2go.com/public/js/jquery-2.1.1.min.js"></script>
        <script src="http://www.crazy2go.com/public/js/jquery-ui.min.js"></script>
        <script src="http://www.crazy2go.com/public/js/raphael-min.js"></script>
        <link rel="stylesheet" href="http://www.crazy2go.com/public/css/jquery-ui.min.css">
        <script>
            /******************** user define js ********************/
            $(function(){
                
                <?php if(isset($_GET['type'])&&isset($_GET['keyword'])){?>
                $("#logi_search select[name='type']").val(<?php echo $_GET['type'];?>);
                $("#logi_search input[name='keyword']").val(<?php echo "'".$_GET['keyword']."'";?>);
                <?php }?>
                
                $("#body_right").show();
                
                //列表排序
                $("#logi_list_table tr:first td[name]").each(function(){
                    var $this = $(this);
                    var addr = location.href.split("?");
                    var params = [];
                    var get = [];
                    var order = "";
                    var oname = "";
                    if(addr[1] && addr[1]!="")
                    {
                        params = addr[1].split("&");
                        for(var i in params)
                        {
                            if(params[i].search("oname")!=-1){
                                oname = params[i].split("=")[1];
                                oname = decodeURI(oname).replace(/∵/g,"_")
                                oname = oname == $(this).attr("name");
                                continue;
                            }
                            if(params[i].search("order")!=-1){
                                order = params[i].split("=")[1];
                                continue;
                            }
                            get.push(params[i]);
                        }
                    }
                    get.push(("oname="+$this.attr("name")).replace(/_/g,"∵"));
                    var order_item = "<div style='position:absolute;top:3px;right:0px;'>"+
                                        "<div class='order_asc' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 0 5px 8px 5px;border-color: transparent transparent #"+(order=="asc"&&oname?"FFCC00":"FFFFFF")+" transparent;'></div><br>"+
                                        "<div class='order_desc' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 8px 5px 0 5px;border-color: #"+(order=="desc"&&oname?"FFCC00":"FFFFFF")+" transparent transparent transparent;'></div>"+
                                     "</div>";
                    $this.css({"position":"relative"});
                    $this.append(order_item);
                    $this.find(".order_asc").click(function(){
                        get.push("order=asc");
                        location.href = addr[0]+"?"+get.join("&");
                    });
                    $this.find(".order_desc").click(function(){
                        get.push("order=desc");
                        location.href = addr[0]+"?"+get.join("&");
                    });
                });
                
                //刪除
                $("#logi_list_table .logi_del").click(function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    $.post(filename,{
                        query_type:"logi_del",
                        name:name,
                        fi_no:fi_no
                    },function(data){
                        alert("已刪除！");
                        location.href = location.href;
                    });
                });
                
                //新增,複製與編輯物流模板
                $("#logi_add_btn,#logi_list_table .logi_edit,#logi_list_table .logi_copy").click(function(){
                    var $dialog = $("#dialog_content");
                    $dialog.data("fi_no",$(this).parent().parent().attr("fi_no"));
                    if($dialog.data("fi_no"))
                    {
                        $("#dialog_title").text($("#dialog_title").text()+" "+$dialog.data("fi_no")+" - "+$(this).parent().parent().attr("name"));
                    }
                    $dialog.find("input:first").focus();
                    paper = Raphael(0,0,100,100);//new Raphael('paper');
                    $dialog.find('#paper').append($("svg"));
                    startX = 0, startY = 0;
                    offset = findPos($dialog.find('#paper')[0]);
                    rect_type = 0;
                    $dialog.find("#paper_wrapper").hide();
                    $dialog.find("#paper_preview").hide();
                    
                    // 儲存
                    $dialog.find("input[name='logi_add_save'],input[name='logi_edit_save'],input[name='logi_copy_save']").click(function(){
                        var p_images = $dialog.find("#upload_image img");
                        var p_name = $dialog.find("input[name='logi_name']").val();
                        var p_item = [];
            
                        if(p_name == "")
                        {
                            alert('物流模板名稱不能有空白！');
                            return;
                        }

                        if(p_images[0] == undefined)
                        {
                            alert('請至少上傳一張圖片！');
                            return;
                        } 
                       
                        $("#paper > div").each(function(){
                            var $this = $(this);
                            var left = parseInt($this.css("left").replace(/px/,""));
                            var top = parseInt($this.css("top").replace(/px/,""));
                            var width = parseInt($this.css("width").replace(/px/,""));
                            var height = parseInt($this.css("height").replace(/px/,""));
                            var type = "";
                            var value = "";
                            switch(type = $this.find("[class='ab']")[0].tagName.toLowerCase())
                            {
                                case "select":
                                    value = $this.find("[class='ab']").val();
                                    break;
                                case "span":
                                    value = "√";
                                    break;
                                case "input":
                                    value = $this.find("[class='ab']").val();
                                    break;
                            }
                            p_item.push([left,top,width,height,type,value]);
                        });
                        p_item = JSON.stringify(p_item);
                        
                        // 傳遞表格
                        var form_data = new FormData();
                        form_data.append("name",p_name);
                        form_data.append("item",p_item);
                        form_data.append('file_images', $dialog.data("file_images"));
                        form_data.append("images_filename",p_images.attr("title"));

                        if($(this).attr("name") == "logi_add_save")
                        {
                            form_data.append("query_type","logi_add");
                        }
                        
                        if($(this).attr("name") == "logi_edit_save")
                        {
                            form_data.append("query_type","logi_edit");
                            form_data.append("fi_no",$dialog.data("fi_no"));
                        }
                        
                        if($(this).attr("name") == "logi_copy_save")
                        {
                            form_data.append("query_type","logi_copy");
                        }

                        $.ajax({
                            url: filename,
                            dataType: 'text',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,                         
                            type: 'post',
                            success: function(data){
                                alert($.trim(data));
                                location.href = location.href;
                            }
                        });
                        
                    });

                    // 上傳物流模板圖片
                    $dialog.find("input[name='images']").change(function(evt) {
                        $(this).after($(this).clone(true));
                        $(this).remove();
                        $dialog.data("file_images","");
                        for(var i=0, f; f=evt.target.files[i]; i++) {
                            if(!f.type.match('image.*')) {
                                continue;
                            }
                            if(f.size>1024*1024*2)
                            {
                                alert("部分檔案大小超過 2 MB 未選取");
                                continue;
                            }
                            var reader = new FileReader();
                            reader.onload = (function(theFile) {
                                return function(e) {
                                    $dialog.find("#upload_image").html("");
                                    $dialog.find("#upload_image").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                    $dialog.find("#upload_image img:last")[0].onload = function(){
                                        var $this = $(this);
                                        var filename = new Date().getTime()+"_"+$this.index()+"_"+this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[$this.attr("title").split(".").length-1];
                                        $this.attr("title",filename);                                
                                        $this.css({
                                            "border":"1px solid gray",
                                            "margin":"5px"
                                        });
                                        var og_w = this.width;
                                        var og_h = this.height;
                                        var new_w = 810;
                                        var new_h = Math.round(new_w*og_h/og_w);
                                        $this.width(100);
                                        $this.height(100);
                                        $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                        $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' fname='"+filename+"'>delete</span>");
                                        $this.parent().find("span").click(function(){
                                            $dialog.data("file_images","");
                                            $this.parent().remove();
                                        });
                                        theFile.fname = filename;
                                        $dialog.data("file_images",theFile);
                                        $dialog.find("#paper").width(new_w).height(new_h);
                                        $dialog.find("#paper svg").attr("width",new_w).attr("height",new_h);
                                        $dialog.find("#paper_preview").width(new_w).height(new_h);
                                        $dialog.find("#paper_image").width(new_w).height(new_h);
                                        $dialog.find("#paper_image").attr("src",$this.attr("src"));
                                        $dialog.find("#paper_wrapper").show();
                                        $dialog.find("#upload_image").hide();
                                    }
                                };
                            })(f);
                            reader.readAsDataURL(f);
                        }
                    });
                    
                    if($dialog.has("input[name='logi_edit_save']").length || $dialog.has("input[name='logi_copy_save']").length){                       
                        // 填表
                        $.post(filename,{
                            query_type:"get_logi_detail",
                            fi_no:$dialog.data("fi_no")
                        },function(data){
                            data = data.split("`");
                            //資料
                            var p_fi_no = data[0];
                            var p_name = data[1];
                            var p_item = data[2]==""?[]:$.parseJSON(data[2]);
                            var p_images = data[3];
                            
                            $dialog.find("input[name='logi_name']").val(p_name);

                            //上傳圖片
                            $dialog.find("#upload_image").append('<img class="thumb" src="../public/img/shipping/'+p_images+'" title="'+p_images+'"/>');
                            $dialog.find("#upload_image img:last")[0].onload = function(){
                                var $this = $(this);
                                $this.css({
                                    "border":"1px solid gray",
                                    "margin":"5px"
                                });
                                $this.attr("title",$this.attr('src'));
                                var og_w = this.width;
                                var og_h = this.height;
                                var new_w = 810;
                                var new_h = Math.round(new_w*og_h/og_w);
                                $this.width(100);
                                $this.height(100);
                                $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
                                $this.parent().find("span").click(function(){
                                    $this.parent().remove();
                                });
                                $dialog.find("#paper").width(new_w).height(new_h);
                                $dialog.find("#paper svg").attr("width",new_w).attr("height",new_h);
                                $dialog.find("#paper_preview").width(new_w).height(new_h);
                                $dialog.find("#paper_image").width(new_w).height(new_h);
                                $dialog.find("#paper_image").attr("src",$this.attr("src"));
                                $dialog.find("#paper_wrapper").show();
                                $dialog.find("#upload_image").hide();
                                
                                //item
                                var len = p_item.length;
                                for(var i =0; i<len; i++)
                                {
                                    startX = p_item[i][0];
                                    startY = p_item[i][1];
                                    var w = p_item[i][2];
                                    var h = p_item[i][3];
                                    rect = paper.rect(startX, startY, w, h);
                                    var type = p_item[i][4];
                                    var value = p_item[i][5];
                                    autoMode = true;
                                    switch(type)
                                    {
                                       case "select":
                                            rect_type = 0;
                                            $(document).find("body").trigger("mouseup");
                                            $dialog.find("#paper > div:last select").val(value);
                                            break;
                                        case "span":
                                            rect_type = 1;
                                            $(document).find("body").trigger("mouseup");
                                            break;
                                        case "input":
                                            rect_type = 2;
                                            $(document).find("body").trigger("mouseup");
                                            $dialog.find("#paper > div:last input").val(value);
                                            break; 
                                    }
                                    autoMode = false;
                                }
                                
                            };
                        });
                    }
                });
                
                //搜尋
                $("#logi_search input[name='search']").click(function(){
                    var type = $("#logi_search select[name='type']").val();
                    var keyword = $("#logi_search input[name='keyword']").val();
                    location.href = location.href.split("?")[0] + "?type=" + type + "&keyword=" + keyword;
                });
                $("#logi_search input[name='keyword']").keypress(function(e){
                    if(e.keyCode==13)
                    {
                        $("#logi_search input[name='search']").trigger("click");
                    }
                });
                
                //rp
                function findPos(obj) {
                        var curleft = 0, curtop = 0;
                        if (obj.offsetParent) {
                                do {
                                        curleft += obj.offsetLeft;
                                        curtop += obj.offsetTop;
                                } while (obj = obj.offsetParent);
                                return [curleft, curtop];
                        } else {
                                return false;
                        }
                }

                function getCoords(event) {
                        event = event || window.event;
                        if (event.pageX || event.pageY) {
                                return {x: event.pageX, y: event.pageY};
                        }
                        return {
                                x: event.clientX + document.body.scrollLeft - document.body.clientLeft,
                                y: event.clientY + document.body.scrollTop  - document.body.clientTop
                        };
                }

                function doDraw(event) {
                        if (rect) {
                                var mousePos = getCoords(event);
                                var currentX = mousePos.x - offset[0];
                                var currentY = mousePos.y - offset[1] + $("#dialog_content").scrollTop();
                                var width = currentX - startX;
                                var height = currentY - startY;
                                
                                if (width < 0) {rect.attr({'x': currentX, 'width': width * -1});}
                                else {rect.attr({'x': startX, 'width': width});}

                                if (height < 0) {rect.attr({'y': currentY, 'height': height * -1});}
                                else {rect.attr({'y': startY, 'height': height});}
                        }
                }

                $(document).on("mousedown", "body", function(event) {
                        if(event.target.tagName.toLowerCase()=="svg")
                        if($(event.target).attr('class') != 'ab' && 
                                $(event.target).attr('class') != 'ui-resizable-handle ui-resizable-e' && 
                                $(event.target).attr('class') != 'ui-resizable-handle ui-resizable-s' && 
                                $(event.target).attr('class') != 'ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se' && 
                                $(event.target).attr('class') != 'move') {
                                var mouseCoords = getCoords(event);
                                startX = mouseCoords.x - offset[0];
                                startY = mouseCoords.y - offset[1] + $("#dialog_content").scrollTop();
                                rect = paper.rect(startX, startY, 0, 0);
                                document.onmousemove = doDraw;
                        }
                });

                $(document).on("mouseup", "body", function(event) {
                        var $dialog = $("#dialog_content");
                        var pa_re = $dialog.find("#paper rect");
                        if(event.target.tagName.toLowerCase()=="svg" || autoMode)
                        if(pa_re.attr("x") != undefined && pa_re.attr("x") != "" && pa_re.attr("x") != null && pa_re.attr("x") > 0 &&
                                pa_re.attr("width") != undefined && pa_re.attr("width") != "" && pa_re.attr("width") != null && pa_re.attr("width") > 0) {
                                
                                var awidth = 0;
                                var aheight = 0;

                                if(pa_re.attr("width") < 5) {awidth = 5;}
                                else {awidth = pa_re.attr("width");}

                                if(pa_re.attr("height") < 5) {aheight = 5;}
                                else {aheight = pa_re.attr("height");}

                                if(rect_type == 1) {
                                        awidth = 11;
                                        aheight = 11;
                                }

                                var ahtml = '<div class="resizable ui-widget-content" style="position:absolute; left:'+pa_re.attr("x")+'px; top:'+pa_re.attr("y")+'px; width:'+ awidth+'px; height:'+aheight+'px; background-color:rgba(255, 255, 0, 0.35);">';

                                if(rect_type == 0) {
                                        ahtml += '<select class="ab" style="width:100%; height:100%; margin:0px; border:0px; background-color:rgba(0, 0, 0, 0.0); color:rgba(255, 0, 0, 0.45);">';
                                        ahtml += '<option value="0">請選擇收件人資訊</option>';
                                        ahtml += '<option value="">--------------</option>';
                                        ahtml += '<option value="1">收件人姓名</option>';
                                        ahtml += '<option value="2">固定電話</option>';
                                        ahtml += '<option value="3">移動電話</option>';
                                        ahtml += '<option value="4">固定或移動電話</option>';
                                        ahtml += '<option value="5">打印日期</option>';
                                        ahtml += '<option value="6">省</option>';
                                        ahtml += '<option value="7">市</option>';
                                        ahtml += '<option value="8">區</option>';
                                        ahtml += '<option value="9">街道</option>';
                                        ahtml += '<option value="10">詳細地址</option>';
                                        ahtml += '<option value="11">省市區街道</option>';
                                        ahtml += '<option value="12">完整詳細地址</option>';
                                        ahtml += '</select>';
                                }
                                else if(rect_type == 1) {
                                        ahtml += '<span class="ab" style="position:absolute; width:100%; height:100%; margin:0px; border:0px; font-size:8pt; background-color:rgba(0, 0, 0, 0.0); color:rgba(255, 0, 0, 0.45);">√</span>';
                                }
                                else if(rect_type == 2) {
                                        ahtml += '<input class="ab" type="text" style="width:100%; height:100%; margin:0px; border:0px; background-color:rgba(0, 0, 0, 0.0); color:rgba(255, 0, 0, 0.45);">';
                                }

                                ahtml += '<div class="move" style="cursor:move; position:absolute; width:7px; height:7px; left:-5px; top:-5px; background-color:red;"></div>';
                                ahtml += '</div>';

                                $dialog.find("#paper").append(ahtml);
                                $dialog.find(".resizable").resizable();
                                $dialog.find(".resizable").draggable();
                        }


                        if(rect) {
                                rect.remove();
                        }
                        document.onmousemove = null;
                });

                $(document).on("dblclick", ".move", function(event) {
                        $(this).parent().remove();
                });

                $(document).on("click", ".bu_ty", function(event) { var $dialog = $("#dialog_content"); $dialog.find('.bu_ty').css({'font-weight':'','color':''});$dialog.find("#paper_preview").hide();$dialog.find("#paper").show(); });
                $(document).on("click", "#bu_se", function(event) { $("#dialog_content").find('#bu_se').css({'font-weight':'bold','color':'white'}); rect_type = 0; });
                $(document).on("click", "#bu_ch", function(event) { $("#dialog_content").find('#bu_ch').css({'font-weight':'bold','color':'white'}); rect_type = 1; });
                $(document).on("click", "#bu_in", function(event) { $("#dialog_content").find('#bu_in').css({'font-weight':'bold','color':'white'}); rect_type = 2; });
                $(document).on("click", "#bu_pre", function(event) { 
                    var $dialog = $("#dialog_content");
                    $dialog.find('#bu_pre').css({'font-weight':'bold','color':'white'}); 
                    $dialog.find("#paper_preview").html("");
                    $dialog.find("#paper").hide();
                    $dialog.find("#paper_preview").show();
                    $("#paper > div").each(function(){
                        var $this = $(this);
                        var left = parseInt($this.css("left").replace(/px/,""));
                        var top = parseInt($this.css("top").replace(/px/,""));
                        var width = parseInt($this.css("width").replace(/px/,""));
                        var height = parseInt($this.css("height").replace(/px/,""));
                        var type = "";
                        var value = "";
                        switch(type = $this.find("[class='ab']")[0].tagName.toLowerCase())
                        {
                            case "select":
                                value = $this.find("[class='ab']").val();
                                switch(value)
                                {
                                    case "1":value = "Jopher";break;
                                    case "2":value = "02-22345678";break;
                                    case "3":value = "886-987654321";break;
                                    case "4":value = "886-987654321";break;
                                    case "5":value = "2012-09-09";break;
                                    case "6":value = "台灣";break;
                                    case "7":value = "台北";break;
                                    case "8":value = "內湖";break;
                                    case "9":value = "瑞光路";break;
                                    case "10":value = "26巷36弄2號5樓";break;
                                    case "11":value = "台灣台北內湖區瑞光路";break;
                                    case "12":value = "台灣台北內湖區瑞光路26巷36弄2號5樓";break;
                                    default:value = "無資料";
                                }
                                break;
                            case "span":
                                value = "√";
                                break;
                            case "input":
                                value = $this.find("[class='ab']").val();
                                break;
                        }
                        $dialog.find("#paper_preview").append($("<div></div>").css({
                            "display":"inline-block",
                            "position":"absolute",
                            "left":left+"px",
                            "top":top+"px",
                            "width":width+"px",
                            "height":height+"px"
                        }).html(value));
                    });
                });
            });
            var paper = null;
            var autoMode = false;
            var rect;
            var startX = 0, startY = 0;
            var offset = {};
            var rect_type = 0;
        </script>
        <style>
            /******************** user define css ********************/

            .resizable {
                    background-color: transparent;
                    border: 0px;
            }
            .ui-resizable-se {
                    height:7px;
                    width:7px;
                    right: -5px;
                    bottom: -5px;
            }

        </style>
    </head>
    <body>
        <div id="wrapper">
            <!-- ******************** header ******************** -->
            <div id="header">
                <h3><?php echo $html_title."(業務)-".$login_store_name; ?></h3>
            </div>
            <!-- /.header -->
            <!-- ******************** body ******************** -->
            <div id="body">
                <div id="body_left">
                    <?php echo $menu; ?>
                </div>
                <!-- /.body_left -->
                <div id="body_right" style='display:none;'>
                    <?php
                        //搜尋
                        echo "<div id='logi_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>物流模板ID</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";
                        
                        $paper = '<div id="paper_wrapper" style="position:relative;">
                            <div style="margin:10px 0px;position:relative;">
                                    <input id="bu_se" class="bu_ty" type="button" value="訂單資料（下拉選單）" style="border:0px; width:150px; height:50px; margin-bottom:10px; font-weight:bold; color:white;">　
                                    <input id="bu_ch" class="bu_ty" type="button" value="勾選資料（核取方塊）" style="border:0px; width:150px; height:50px; margin-bottom:10px;">　
                                    <input id="bu_in" class="bu_ty" type="button" value="輸入資料（文字輸入）" style="border:0px; width:150px; height:50px; margin-bottom:10px;">　
                                    <input id="bu_pre" class="bu_ty" type="button" value="預覽" style="border:0px; width:150px; height:50px; margin-bottom:10px;">
                            </div>
                            <img id="paper_image" src="#" style="height:376px; width:600px;position:absolute;">
                            <div id="paper" style=" cursor:crosshair; height:376px; width:600px; position:relative; float:left;"></div>
                            <div id="paper_preview" style="display:inline-block;height:376px; width:600px;position:relative;"></div>
                            </div>';
                        
                        //新增
                        if($display_add)
                        {
                            echo "<table id='logi_add_table' class='table-v' data-dialog='新增模板'>";
                            echo "<tr><td style='width:100px;'>物流單名稱</td><td><input type='text' name='logi_name' /></td></tr>";
                            echo "<tr><td>物流模板圖片</td><td><div id='upload_image'></div><input type='file' name='images' />".$paper."</td></tr>";
                            echo "<tr><td></td><td><input name='logi_add_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                            echo "<input id='logi_add_btn' type='button' value='新增模板' data-open-dialog='新增模板' /><table></table>";                            
                        }
                        
                        //複製
                        if($display_copy)
                        {
                            echo "<table id='logi_copy_table' class='table-v' data-dialog='複製模板'>";
                            echo "<tr><td style='width:100px;'>物流單名稱</td><td><input type='text' name='logi_name' /></td></tr>";
                            echo "<tr><td>物流模板圖片</td><td><div id='upload_image'></div><input type='file' name='images' />".$paper."</td></tr>";
                            echo "<tr><td></td><td><input name='logi_copy_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                        
                        //編輯
                        if($display_edit)
                        {
                            echo "<table id='logi_edit_table' class='table-v' data-dialog='編輯模板'>";
                            echo "<tr><td style='width:100px;'>物流單名稱</td><td><input type='text' name='logi_name' /></td></tr>";
                            echo "<tr><td>物流模板圖片</td><td><div id='upload_image'></div><input type='file' name='images' />".$paper."</td></tr>";
                            echo "<tr><td></td><td><input name='logi_edit_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                    
                        //列表
                        $pager->display();echo "<br/>";
                        echo "<table id='logi_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>圖片</td><td>物流單名</td>";
                        if($display_edit) echo "<td>編輯</td>";
                        if($display_copy) echo "<td>複製</td>";
                        if($display_delete) echo"<td>刪除</td>";
                        echo "</tr>";
                        
                        
                        $len = count($all_logi);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$all_logi[$i]["fi_no"]."' name='".$all_logi[$i]["name"]."' item='".$all_logi[$i]["item"]."'>";
                            echo "<td>".$all_logi[$i]["fi_no"]."</td>";
                            echo "<td>"."<img src='../public/img/shipping/".$all_logi[$i]['images']."' width=150 height=100>"."</td>";
                            echo "<td style='width:100px;'>".$all_logi[$i]["name"]."</td>";
                            if($display_edit)echo "<td><input class='logi_edit' type='button' value='編輯' data-open-dialog='編輯模板' /></td>";
                            if($display_copy) echo "<td><input class='logi_copy' type='button' value='複製' data-open-dialog='複製模板' /></td>";
                            if($display_delete)echo "<td><input class='logi_del' type='button' value='刪除' /></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        $pager->display();
                    ?>
                </div>
                <!-- /.body_right -->
            </div>
            <!-- /.body -->
            <!-- ******************** footer ******************** -->
            <div id="footer">
                <span><?php echo $html_copyright; ?></span>
            </div>
            <!-- /.footer -->
        </div>
        <!-- /.wrapper -->
    </body>
</html>

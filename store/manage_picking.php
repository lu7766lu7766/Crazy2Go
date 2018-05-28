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
        
        //所有揀貨單資料
        $all_picking = array();
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select * from store_picking where store=".$login_store." ".$orderby;
        if(isset($_GET['keyword']))
        {
            switch($_GET['type'])
            {
                case '1'://揀貨單ID
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from store_picking where store=".$login_store." fi_no >= ".(int)$_GET['keyword']." ".$orderby;
                    break;
                case '2'://揀貨單關鍵字
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from store_picking where store=".$login_store." and name like '%".$_GET['keyword']."%' ".$orderby;
                    break;
            }
        }

        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $len = count($result);
        
        for($i = 0; $i < $len; $i++)
        {
            $all_picking[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['name'],
            );
        }
        
        //依權限顯示
        $display_add = strpos($login_permissions,"picking_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_delete = strpos($login_permissions,"picking_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_copy = strpos($login_permissions,"picking_copy")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_edit = strpos($login_permissions,"picking_edit")!==false || strpos($login_permissions,"all")!==false?true:false;
        
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
        <script>
            /******************** user define js ********************/
            $(function(){
                
                <?php if(isset($_GET['type'])&&isset($_GET['keyword'])){?>
                $("#picking_search select[name='type']").val(<?php echo $_GET['type'];?>);
                $("#picking_search input[name='keyword']").val(<?php echo "'".$_GET['keyword']."'";?>);
                <?php }?>
                
                $("#body_right").show();
                $(".image_choose_panel").hide();
                $("#image_resize_panel").hide();
                $("#table_creator").hide();
                
                //列表排序
                $("#picking_list_table tr:first td[name]").each(function(){
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
                $("#picking_list_table .picking_del").click(function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    $.post(filename,{
                        query_type:"picking_del",
                        name:name,
                        fi_no:fi_no
                    },function(data){
                        alert("已刪除！");
                        location.href = location.href;
                    });
                });
                
                //新增,複製與編輯揀貨單
                $("#picking_add_btn,#picking_list_table .picking_edit,#picking_list_table .picking_copy").click(function(){
                    var $dialog = $("#dialog_content");
                    $dialog.data("fi_no",$(this).parent().parent().attr("fi_no"));
                    if($dialog.data("fi_no"))
                    {
                        $("#dialog_title").text($("#dialog_title").text()+" "+$dialog.data("fi_no")+" - "+$(this).parent().parent().attr("name"));
                    }
                    $dialog.find("input:first").focus();
                    $dialog.find("textarea[name='header_style']").jqte();
                    $dialog.find("textarea[name='footer_style']").jqte();
                    $dialog.find(".jqte_editor").each(function(){
                        var $this = $(this);
                        $this.attr("class","jqte_editor_x");
                        $this.css({"min-height":"120px"});
                    });
                    
                    $dialog.find(".jqte_tool_1,.jqte_tool_2,.jqte_tool_3").click(function(){
                        var $this=$(this);
                        $this.find("div").css({
                            "position":"absolute",
                            "left":$this.position().left+"px",
                            "top":$this.position().top+"px"
                        });
                        $dialog.bind("scroll",function(){
                            $dialog.unbind("scroll");
                            $dialog.find(".jqte_tool_1,.jqte_tool_2,.jqte_tool_3").find("div").hide();
                        });
                    });
                    
                    $dialog.find(".jqte_toolbar").each(function(index){
                        var $toolbar = $(this);
                        
                        $toolbar.parent().css({"position":"relative"});
                        var $clone = $toolbar.children(":last").clone();
                        $toolbar.append($clone.clone());
                        var tb = $toolbar.children(":last").find("a");
                        tb.css({
                            "background":"url(http://www.crazy2go.com/backend/js/jquery-te/jquery-table.png)",
                            "display":"inline-block",
                            "width":"22px",
                            "height":"20px"
                        }).click(function(){
                            if($toolbar.parent().find("#table_creator")[0])return;
                            $dialog.bind("scroll",function(){
                                 $dialog.unbind("scroll");
                                 $dialog.find("#table_creator").remove();
                             });
                            $toolbar.parent().prepend($("#table_creator").clone().show().css({
                                "top":"20px",
                                "left":"180px"
                            }).each(function(){
                                var $this = $(this);
                                $this.find("input[name='cancel_table']").click(function(){
                                    $this.remove();
                                });
                                $this.find("input[name='insert_table']").click(function(){
                                    var rows_count = parseInt($this.find("input[name='rows_count']").val())==NaN?1:parseInt($this.find("input[name='rows_count']").val());
                                    var column_count = parseInt($this.find("input[name='column_count']").val())==NaN?1:parseInt($this.find("input[name='column_count']").val());
                                    var border_width = parseInt($this.find("input[name='border_width']").val())==NaN?1:parseInt($this.find("input[name='border_width']").val());
                                    var table_width = parseInt($this.find("input[name='table_width']").val())==NaN?"":"width:"+($this.find("input[name='table_width']").val())+"px;";
                                    var table_height = parseInt($this.find("input[name='table_height']").val())==NaN?"":"height:"+($this.find("input[name='table_height']").val())+"px;";
                                    var display = '<table border="'+border_width+'" cellpadding="0" cellspacing="0" style="'+table_width+table_height+'">';
                                    for(var i=0;i<rows_count;i++)
                                    {
                                        display += '<tr>';
                                        for(var j=0;j<column_count;j++)
                                        {
                                            display += "<td>&nbsp;</td>";
                                        }
                                        display += '</tr>';
                                    }
                                    display += '</table>';
                                    $this.parent().find(".jqte_editor_x").html($this.parent().find(".jqte_editor_x").html()+display);
                                    $this.parent().parent().find("textarea").val($this.parent().find(".jqte_editor_x").html());
                                    $this.remove();
                                });
                            }));
                        });
                        
                        $toolbar.append($clone.clone());
                        var last = $toolbar.children(":last").find("a");
                        last.text("貼圖");
                        last.attr("class","");
                        last.parent().css({"margin-top":"5px"});
                        last.click(function(){
                            if($toolbar.parent().parent().find(".image_choose_panel")[0])return;
                            var $panel = $(".image_choose_panel").clone();
                            $toolbar.parent().parent().prepend($panel);
                            $panel.show();
                            var $content = $panel.find(".content");
                            var id_prefix = $toolbar.parent().parent().find("textarea").attr("name");
                            var $imgs = $dialog.find("#"+id_prefix+"_image img");
                            if(!$imgs[0])
                            {
                                alert("請先上傳圖片！");
                                $panel.remove();
                                return;
                            }
                            $content.html("");
                            $imgs.each(function(){
                                var $this = $(this);
                                var display="";
                                var filename = $this.attr("title").split("/");
                                filename = filename[filename.length-1];
                                var og_width = filename.split("_")[2].split("x")[0];
                                var og_height = filename.split("_")[2].split("x")[1];
                                display += "<div class='choose_item' style='display:inline-block;border:1px solid gray;padding:5px;margin:5px;width=110px;text-align:center;'>"
                                        + "<p><img src='"+$this.attr("src")+"' title='"+$this.attr("title")+"' width=100 height=100></p>"
                                        + "<p>寬 <input name='width' type='text' style='width:80px' placeholder='請輸入寬' value='"+og_width+"'><br/>"
                                        + "高 <input name='height' type='text' style='width:80px' placeholder='請輸入高' value='"+og_height+"'></p>"
                                        + "<p><input name='select_btn' type='button' value='選取'></p>"
                                        + "</div>";
                                $content.append(display);
                            });
                            $content.find("input[name='select_btn']").click(function(){
                                var $this = $(this);
                                if($this.val()=='選取'){
                                    $this.val("取消選取");
                                    $this.parent().parent().css({"border":"3px solid #1E90FF"});
                                }else{
                                    $this.val("選取");
                                    $this.parent().parent().css({"border":"1px solid gray"});
                                }
                            });
                            $content.append("<div style='text-align:center;margin-top:10px;padding-top:10px;border-top:1px dashed gray;'><input name='choose' type='button' value='確定選取'/></div>");
                            $content.find("input[name='choose']").click(function(){
                                var display=$dialog.find("textarea[name='"+id_prefix+"']").val();
                                $content.find(".choose_item").each(function(){
                                    var $this = $(this);
                                    if($this.find("input[name='select_btn']").val()=='選取')return;
                                    var src = $this.find("img").attr("src");
                                    var title = $this.find("img").attr("title");
                                    var w = $this.find("input[name='width']").val();
                                    var h = $this.find("input[name='height']").val();
                                    display+="<img src='"+src+"' title='"+title+"' style='width:"+w+"px;height:"+h+"px'>";
                                });
                                $dialog.find("textarea[name='"+id_prefix+"']").val(display);
                                $toolbar.parent().find(".jqte_editor_x").html(display);
                                $toolbar.parent().find(".jqte_editor_x img").unbind("dblclick");
                                $toolbar.parent().find(".jqte_editor_x img").bind("dblclick",imageResize);
                                $panel.remove();
                            });
                        });
                        //插入訂單數據
                        $toolbar.append($clone.clone());
                        last = $toolbar.children(":last").find("a");
                        last.text("插入訂單數據");
                        last.attr("class","");
                        last.parent().css({
                            "position":"relative",
                            "top":"-3px",
                            "margin-top":"5px",
                            "padding":"3px",
                            "background":"#FCCAC9"
                        });
                        $toolbar.parent().parent().find(".jqte").css("position","relative");
                        last.click(function(){
                            var $this=$(this);
                            $this.unbind("click");
                            var append =    "<select>"+
                                            "<option value=''>--請選擇插入資料--</option>"+
                                            "<option value='{sn}'>訂單編號：{sn}</option>"+
                                            "<option value='{id}'>會員帳號：{id}</option>"+
                                            "<option value='{consignee}'>收貨人姓名：{consignee}</option>"+
                                            "<option value='{phone}'>固定電話：{phone}</option>"+
                                            "<option value='{mobile}'>移動電話：{mobile}</option>"+
                                            "<option value='{pormo}'>固定或移動電話：{pormo}</option>"+
                                            "<option value='{address}'>收貨地址：{address}</option>"+
                                            "<option value='{print_date}'>打印日期：{print_date}</option>"+
                                            "<option value='{page}'>頁數：{page}</option>"+
                                            "</select>";
                            if(!$toolbar.parent().parent().find("select")[0])
                            $toolbar.parent().parent().find(".jqte").prepend($(append).css({
                                "position":"absolute",
                                "left":$this.position().left+655+"px",
                                "top":$this.position().top+5+"px",
                                "width":$this.width()+10+"px",
                                "height":$this.height()+"px",
                                "background":"red",
                                "opacity":"0",
                                "cursor":"pointer",
                                "z-index":"999"
                            }).change(function(){
                                var id_prefix = $toolbar.parent().parent().find("textarea").attr("name");
                                $toolbar.parent().find(".jqte_editor_x").html($toolbar.parent().find(".jqte_editor_x").html()+$(this).val());
                                $dialog.find("textarea[name='"+id_prefix+"']").val($toolbar.parent().find(".jqte_editor_x").html());
                            }));
                        }).trigger("click");
                    });

                    // 儲存
                    $dialog.find("input[name='picking_add_save'],input[name='picking_edit_save'],input[name='picking_copy_save']").click(function(){
                        var p_name = $dialog.find("input[name='name']").val();
                        var p_header = $dialog.find(".jqte_editor_x").eq(0).html();
                        var p_footer = $dialog.find(".jqte_editor_x").eq(1).html();
                        var p_header_style_image = $dialog.find("#header_style_image img");
                        var p_footer_style_image = $dialog.find("#footer_style_image img");
                        
                        if(p_name == "")
                        {
                            alert('揀貨單名稱不能有空白！');
                            return;
                        }

                        // 揀貨單標題圖
                        var len = p_header_style_image.size();
                        var i = 0;
                        var p_header_style_image_filename = [];
                        if(len > 0)
                        {
                            for(i = 0; i < len; i++)
                            {
                                p_header_style_image_filename.push(p_header_style_image.eq(i).attr("title"));
                            }
                        }
                        p_header_style_image_filename = p_header_style_image_filename.join("∵");
                        
                        // 揀貨單結尾圖
                        len = p_footer_style_image.size();
                        var p_footer_style_image_filename = [];
                        if(len > 0)
                        {
                            for(i = 0; i < len; i++)
                            {
                                p_footer_style_image_filename.push(p_footer_style_image.eq(i).attr("title"));
                            }
                        }
                        p_footer_style_image_filename = p_footer_style_image_filename.join("∵");

                        // 揀貨單標題描述
                        $dialog.find("textarea[name='header_style']").val(p_header);
                        $dialog.find(".jqte_editor_x").eq(0).find("img").each(function(){
                            var $this = $(this);
                            var src = $this.attr("src");
                            if(src.search(/data:/)!==-1)
                            {
                                src = $this.attr("title").split("_");
                                src = src[0]+"_"+src[1];
                                $("#header_style_image img").each(function(){
                                    var $upload_img = $(this);
                                    if($upload_img.attr("title").search(src)!==-1)
                                    {
                                        $this.attr("src",$upload_img.attr("title"));
                                        $this.attr("title",$upload_img.attr("title"));
                                    }
                                })
                            }
                        });
                        
                        // 揀貨單結尾描述
                        $dialog.find("textarea[name='footer_style']").val(p_footer);
                        $dialog.find(".jqte_editor_x").eq(1).find("img").each(function(){
                            var $this = $(this);
                            var src = $this.attr("src");
                            if(src.search(/data:/)!==-1)
                            {
                                src = $this.attr("title").split("_");
                                src = src[0]+"_"+src[1];
                                $("#footer_style_image img").each(function(){
                                    var $upload_img = $(this);
                                    if($upload_img.attr("title").search(src)!==-1)
                                    {
                                        $this.attr("src",$upload_img.attr("title"));
                                        $this.attr("title",$upload_img.attr("title"));
                                    }
                                })
                            }
                        });
                        
                        p_header = $dialog.find(".jqte_editor_x").eq(0).html();
                        $dialog.find(".jqte_editor_x").eq(0).html($dialog.find("textarea[name='header_style']").val())
                        p_header = p_header.replace(/http:\/\/www.crazy2go.com/g,"..");
                        p_header = p_header.replace(/\r/g,"<br>");
                        p_header = p_header.replace(/\n/g,"<br>");
     
                        p_footer = $dialog.find(".jqte_editor_x").eq(1).html();
                        $dialog.find(".jqte_editor_x").eq(1).html($dialog.find("textarea[name='footer_style']").val())
                        p_footer = p_footer.replace(/http:\/\/www.crazy2go.com/g,"..");
                        p_footer = p_footer.replace(/\r/g,"<br>");
                        p_footer = p_footer.replace(/\n/g,"<br>");
                        
                        // 傳遞表格
                        var form_data = new FormData();
                        
                        form_data.append("name",p_name);
                        for(i in $dialog.data("file_header"))
                        form_data.append('file_header[]', $dialog.data("file_header")[i]);
                        for(i in $dialog.data("file_footer"))
                        form_data.append('file_footer[]', $dialog.data("file_footer")[i]);
                        form_data.append("header_filename",p_header_style_image_filename);
                        form_data.append("footer_filename",p_footer_style_image_filename);
                        form_data.append("header",p_header);
                        form_data.append("footer",p_footer);

                        if($(this).attr("name") == "picking_add_save")
                        {
                            form_data.append("query_type","picking_add");
                        }
                        
                        if($(this).attr("name") == "picking_edit_save")
                        {
                            form_data.append("query_type","picking_edit");
                            form_data.append("fi_no",$dialog.data("fi_no"));
                        }
                        
                        if($(this).attr("name") == "picking_copy_save")
                        {
                            form_data.append("query_type","picking_copy");
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

                    // 上傳揀貨單圖片
                    $dialog.find("input[name='header']").change(function(evt) {
                        $(this).after($(this).clone(true));
                        $(this).remove();
                        $dialog.data("file_header",$dialog.data("file_header")||[]);
                        for(var i=0, f; f=evt.target.files[i]; i++) {
                            if(!f.type.match('image.*')) {
                                continue;
                            }
                            if(f.size>1024*1024)
                            {
                                alert("部分檔案大小超過 1 MB 未選取");
                                continue;
                            }
                            var reader = new FileReader();
                            reader.onload = (function(theFile) {
                                return function(e) {
                                    $dialog.find("#header_style_image").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                    $dialog.find("#header_style_image img:last")[0].onload = function(){
                                        var $this = $(this);
                                        var filename = new Date().getTime()+"_"+$this.index()+"_"+this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[$this.attr("title").split(".").length-1];
                                        $this.attr("title",filename);                                
                                        $this.css({
                                            "border":"1px solid gray",
                                            "margin":"5px"
                                        });
                                        $this.width(100);
                                        $this.height(100);
                                        $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                        $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' fname='"+filename+"'>delete</span>");
                                        $this.parent().find("span").click(function(){
                                            var l = $dialog.data("file_header").length;
                                            if($(this).attr("fname"))
                                            for(var i=0;i<l;i++)
                                            {
                                                if($dialog.data("file_header")[i].fname == $(this).attr("fname"))
                                                {
                                                    $dialog.data("file_header").splice(i,1);
                                                    break;
                                                }
                                            }
                                            $this.parent().remove();
                                        });
                                        theFile.fname = filename;
                                        $dialog.data("file_header").push(theFile);
                                    }
                                };
                            })(f);
                            reader.readAsDataURL(f);
                        }
                    });

                    // 上傳描述用圖片
                    $dialog.find("input[name='footer']").change(function(evt) {
                        $(this).after($(this).clone(true));
                        $(this).remove();
                        $dialog.data("file_footer",$dialog.data("file_footer")||[]);
                        for(var i=0, f; f=evt.target.files[i]; i++) {
                            if(!f.type.match('image.*')) {
                                continue;
                            }
                            if(f.size>1024*1024)
                            {
                                alert("部分檔案大小超過 1 MB 未選取");
                                continue;
                            }
                            var reader = new FileReader();
                            reader.onload = (function(theFile) {
                                return function(e) {
                                    $dialog.find("#footer_style_image").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                    $dialog.find("#footer_style_image img:last")[0].onload = function(){
                                        var $this = $(this);
                                        var filename = new Date().getTime()+"_"+$this.index()+"_"+this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[$this.attr("title").split(".").length-1];
                                        $this.attr("title",filename);
                                        $this.css({
                                            "border":"1px solid gray",
                                            "margin":"5px"
                                        });
                                        $this.width(100);
                                        $this.height(100);
                                        $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                        $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white' fname='"+filename+"'>delete</span>");
                                        $this.parent().find("span").click(function(){
                                            var l = $dialog.data("file_introduction").length;
                                            if($(this).attr("fname"))
                                            for(var i=0;i<l;i++)
                                            {
                                                if($dialog.data("file_footer")[i].fname == $(this).attr("fname"))
                                                {
                                                    $dialog.data("file_footer").splice(i,1);
                                                    break;
                                                }
                                            }
                                            $this.parent().remove();
                                        });
                                        theFile.fname = filename;
                                        $dialog.data("file_footer").push(theFile);
                                    }
                                };
                            })(f);
                            reader.readAsDataURL(f);
                        }
                    });
                    
                    if($dialog.has("input[name='picking_edit_save']").length || $dialog.has("input[name='picking_copy_save']").length){                       
                        // 填表
                        $.post(filename,{
                            query_type:"get_picking_detail",
                            fi_no:$dialog.data("fi_no")
                        },function(data){
                            data = data.split("`");
                            //資料
                            var p_name = data[0];
                            var p_header = data[1];
                            var p_header_images = data[2]==""?[]:$.parseJSON(data[2]);
                            var p_footer = data[3];
                            var p_footer_images = data[4]==""?[]:$.parseJSON(data[4]);
                            
                            $dialog.find("input[name='name']").val(p_name);

                            //上傳圖片
                            var i = 0;
                            var len = 0;
                            if((len = p_header_images.length) > 0)
                            {
                                for(i = 0; i<len;i++)
                                {
                                    $dialog.find("#header_style_image").append('<img class="thumb" src="../public/img/picking/'+p_header_images[i]+'" title="'+p_header_images[i]+'"/>');
                                    $dialog.find("#header_style_image img:last")[0].onload = function(){
                                        var $this = $(this);
                                        $this.css({
                                            "border":"1px solid gray",
                                            "margin":"5px"
                                        });
                                        $this.attr("title",$this.attr('src'));
                                        $this.width(100);
                                        $this.height(100);
                                        $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                        $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
                                        $this.parent().find("span").click(function(){
                                            $this.parent().remove();
                                        });
                                    }
                                }
                            }
                            
                            //介紹文圖片             
                            i = 0;
                            len = 0;
                            if((len = p_footer_images.length) > 0)
                            {
                                for(i = 0; i<len;i++)
                                {
                                    $dialog.find("#footer_style_image").append('<img class="thumb" src="../public/img/picking/'+p_footer_images[i]+'" title="'+p_footer_images[i]+'"/>');
                                    $dialog.find("#footer_style_image img:last")[0].onload = function(){
                                        var $this = $(this);
                                        $this.css({
                                            "border":"1px solid gray",
                                            "margin":"5px"
                                        });
                                        $this.attr("title",$this.attr('src'));
                                        $this.width(100);
                                        $this.height(100);
                                        $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                        $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
                                        $this.parent().find("span").click(function(){
                                            $this.parent().remove();
                                        });
                                    }
                                }
                            }
                            
                            $dialog.find("textarea[name='header_style']").jqteVal(p_header);
                            $dialog.find("textarea[name='footer_style']").jqteVal(p_footer);
                            $dialog.find(".jqte_editor_x").eq(0).html(p_header);
                            $dialog.find(".jqte_editor_x").eq(1).html(p_footer);
                            $dialog.find(".jqte_editor_x img").bind("dblclick",imageResize);
                        });
                    }
                });
                
                //搜尋
                $("#picking_search input[name='search']").click(function(){
                    var type = $("#picking_search select[name='type']").val();
                    var keyword = $("#picking_search input[name='keyword']").val();
                    location.href = location.href.split("?")[0] + "?type=" + type + "&keyword=" + keyword;
                });
                $("#picking_search input[name='keyword']").keypress(function(e){
                    if(e.keyCode==13)
                    {
                        $("#picking_search input[name='search']").trigger("click");
                    }
                });
            });
            
            function imageResize(){
                var $this = $(this);
                var $dialog = $("#dialog_content");
                $this.css({"cursor":"pointer"});
                $dialog.find("#image_resize_panel").remove();
                var $resizePanel = $("#image_resize_panel").clone();
                $resizePanel.find("#content input[name='width']").val($this.width());
                $resizePanel.find("#content input[name='height']").val($this.height());
                $resizePanel.show(); 
                $dialog.append($resizePanel);
                $resizePanel.css({
                    "left":mouse_x-$("#dialog_container").position().left+"px",
                    "top":mouse_y-$("#dialog_container").position().top+"px"
                });
                $dialog.bind("scroll",function(){
                    $(this).unbind("scroll");
                    $resizePanel.remove();
                });
                $dialog.find(".jqte_editor_x").bind("scroll",function(){
                    $(this).unbind("scroll");
                    $resizePanel.remove();
                });
                $resizePanel.find("#content input[name='set']").bind('click',function(){
                    var w=$resizePanel.find("#content input[name='width']").val();
                    var h=$resizePanel.find("#content input[name='height']").val();
                    $this.width(w);
                    $this.height(h);
                    $dialog.find("#image_resize_panel").remove();
                });
            }
        </script>
        <style>
            /******************** user define css ********************/

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
                        echo "<div id='picking_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>揀貨單ID</option><option value='2'>揀貨單名稱</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";
                        
                        //新增
                        if($display_add)
                        {
                            echo "<table id='picking_add_table' class='table-v' data-dialog='新增揀貨單'>";
                            echo "<tr><td>揀貨單名</td><td><input type='text' name='name' /></td></tr>";
                            echo "<tr><td>揀貨單標題樣式</td><td><textarea name='header_style'></textarea></td></tr>";
                            echo "<tr><td>揀貨單標題圖片</td><td><div id='header_style_image'></div><input type='file' name='header' multiple='multiple' /></td></tr>";
                            echo "<tr><td>揀貨單結尾樣式</td><td><textarea name='footer_style'></textarea></td></tr>";
                            echo "<tr><td>揀貨單結尾圖片</td><td><div id='footer_style_image'></div><input type='file' name='footer' multiple='multiple' /></td></tr>";
                            echo "<tr><td></td><td><input name='picking_add_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                            echo "<input id='picking_add_btn' type='button' value='新增揀貨單' data-open-dialog='新增揀貨單' /><table></table>";                            
                        }
                        
                        //複製
                        if($display_copy)
                        {
                            echo "<table id='picking_copy_table' class='table-v' data-dialog='複製揀貨單'>";
                            echo "<tr><td>揀貨單名</td><td><input type='text' name='name' /></td></tr>";
                            echo "<tr><td>揀貨單標題樣式</td><td><textarea name='header_style'></textarea></td></tr>";
                            echo "<tr><td>揀貨單標題圖片</td><td><div id='header_style_image'></div><input type='file' name='header' multiple='multiple' /></td></tr>";
                            echo "<tr><td>揀貨單結尾樣式</td><td><textarea name='footer_style'></textarea></td></tr>";
                            echo "<tr><td>揀貨單結尾圖片</td><td><div id='footer_style_image'></div><input type='file' name='footer' multiple='multiple' /></td></tr>";
                            echo "<tr><td></td><td><input name='picking_copy_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                        
                        //編輯
                        if($display_edit)
                        {
                            echo "<table id='picking_edit_table' class='table-v' data-dialog='編輯揀貨單'>";
                            echo "<tr><td>揀貨單名</td><td><input type='text' name='name' /></td></tr>";
                            echo "<tr><td>揀貨單標題樣式</td><td><textarea name='header_style'></textarea></td></tr>";
                            echo "<tr><td>揀貨單標題圖片</td><td><div id='header_style_image'></div><input type='file' name='header' multiple='multiple' /></td></tr>";
                            echo "<tr><td>揀貨單結尾樣式</td><td><textarea name='footer_style'></textarea></td></tr>";
                            echo "<tr><td>揀貨單結尾圖片</td><td><div id='footer_style_image'></div><input type='file' name='footer' multiple='multiple' /></td></tr>";
                            echo "<tr><td></td><td><input name='picking_edit_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                    
                        //列表
                        $pager->display();echo "<br/>";
                        echo "<table id='picking_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>揀貨單名</td>";
                        if($display_edit) echo "<td>編輯</td>";
                        if($display_copy) echo "<td>複製</td>";
                        if($display_delete) echo"<td>刪除</td>";
                        echo "</tr>";

                        $len = count($all_picking);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$all_picking[$i]["fi_no"]."' name='".$all_picking[$i]["name"]."'>";
                            echo "<td>".$all_picking[$i]["fi_no"]."</td>";
                            echo "<td>".$all_picking[$i]["name"]."</td>";
                            if($display_edit)echo "<td><input class='picking_edit' type='button' value='編輯' data-open-dialog='編輯揀貨單' /></td>";
                            if($display_copy) echo "<td><input class='picking_copy' type='button' value='複製' data-open-dialog='複製揀貨單' /></td>";
                            if($display_delete)echo "<td><input class='picking_del' type='button' value='刪除' /></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        $pager->display();
                    ?>
                </div>
                <div class='image_choose_panel shadowRoundCorner' style='display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:778px;'>
                    <div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>
                        <span>選擇貼圖</span>
                        <span style='float:right;cursor:pointer;' onclick="$(this).parent().parent().remove();">ｘ</span>
                    </div>
                    <div class="content">
                        請先上傳圖片！
                    </div>
                </div>
                <div id="image_resize_panel" class='shadowRoundCorner' style='position:absolute;display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:200px;text-align:center;'>
                    <div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>
                        <span style="color:white;">貼圖尺寸設定</span>
                        <span style='color:white;float:right;cursor:pointer;' onclick="$(this).parent().parent().remove();">ｘ</span>
                    </div>
                    <div id="content">
                        <p>
                            寬<input name="width" type="text" value="0" /><br/>
                            高<input name="height" type="text" value="0" />
                        </p>
                        <p><input name="set" type="button" value="確認" /></p>
                    </div>
                </div>
                <div id="table_creator" class='shadowRoundCorner' style='display:inline-block;width:350px;height:130px;background:white;position:absolute;'>
                    <table>
                        <tr><td>列　　數 <input name='rows_count' type='text' value='2' style='width:80px;'></td><td>寬　　度 <input name='table_width' type='text' value='150' style='width:80px;'></td></tr>
                        <tr><td>行　　數 <input name='column_count' type='text' value='2' style='width:80px;'></td><td>高　　度 <input name='table_height' type='text' value='150' style='width:80px;'></td></tr>
                        <tr><td>框線粗細 <input name='border_width' type='text' value='1' style='width:80px;'></td><td><input name='insert_table' type='button' value='插入' style='background:#FC645F;color:white;border:0px;width:40px;height:20px;cursor:pointer;'> <input name='cancel_table' type='button' value='取消' style='background:#A4A4A4;color:white;border:0px;width:40px;height:20px;cursor:pointer;'></td></tr>
                    </table>
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

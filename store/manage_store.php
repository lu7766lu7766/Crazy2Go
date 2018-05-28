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

        $query = "select name, images, introduction from store where fi_no=".$login_store;
        $data = $dba->query($query);
        $store_name = $data[0]["name"];
        $store_images = $data[0]["images"]?"<img src='../public/img/store/".$data[0]["images"]."' style='width:220px;height:170px;'>":"";
        $store_introduction = $data[0]["introduction"];
        
        $query = "select images from store_advertisement where store=".$login_store." and `delete`=0 and type in (1,2) order by weights desc";
        $store_store_page_ad = $dba->query($query);
        $store_page_ad = "";
        foreach($store_store_page_ad as $v)
        {
            $store_page_ad.="<img src='../public/img/store/".$v["images"]."' style='width:300px;height:66px;margin:5px;border:1px solid gray;'><br/>";
        }
        
        $query = "select images from store_advertisement where store=".$login_store." and `delete`=0 and type in (3,4) order by weights desc";
        $store_store_page_ad2 = $dba->query($query);
        $store_page_ad2 = "";
        foreach($store_store_page_ad2 as $v)
        {
            $store_page_ad2.="<img src='../public/img/store/".$v["images"]."' style='width:188px;height:58px;margin:5px;border:1px solid gray;'><br/>";
        }
        
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
                $("#store_edit_image input[name=upload]").change(function(evt){
                    $(this).after($(this).clone(true));
                    $(this).remove();
                    var $dialog = $("#dialog_content");
                    $dialog.find("#preview").html("");
                    for(var i=0, f; f=evt.target.files[i]; i++) {
                        if(!f.type.match('image.*')) {
                            continue;
                        }
                        if(f.size>1024*1024)
                        {
                            alert("單檔大小不能超過 1 MB");
                            return;
                        }
                        var reader = new FileReader();
                        reader.onload = (function(theFile) {
                            return function(e) {
                                $dialog.find("#preview").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                $dialog.find("#preview img:last")[0].onload = function(){
                                    var $this = $(this);
                                    var filename = this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[1];
                                    $this.css({
                                        "border":"1px solid gray",
                                        "margin":"5px"
                                    });
                                    $this.attr("title",filename);
                                    $this.width(220);
                                    $this.height(170);
                                    $this.wrap("<div style='display:inline-block;text-align:center;margin-bottom:10px;'></div>");
                                    $this.parent().append("<br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white'>delete</span>");
                                    $this.parent().find("span").click(function(){
                                        $this.parent().remove();
                                    });
                                }
                            };
                        })(f);
                        reader.readAsDataURL(f);
                    }
                });
                
                // 上傳廣告圖片
                $("#store_edit_ad input[name=upload],#store_edit_ad2 input[name=upload]").change(function(evt) {
                    $(this).after($(this).clone(true));
                    $(this).remove();
                    var $upload_btn = $(this);
                    var $dialog = $("#dialog_content");
                    $dialog.data("file_images",$dialog.data("file_images")||[]);
                    var total = $dialog.find("#preview > div").size();
                    for(var i=0, f; f=evt.target.files[i]; i++) {
                        if(total>=12)
                        {
                            alert("最多12張！");
                            break;
                        }
                        if(!f.type.match('image.*')) {
                            continue;
                        }
                        if(f.size>1024*1024)
                        {
                            alert("部分檔案大小超過 1 MB 未選取");
                            continue;
                        }
                        total++;
                        var reader = new FileReader();
                        reader.onload = (function(theFile) {
                            return function(e) {
                                $dialog.find("#preview").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                $dialog.find("#preview img:last")[0].onload = function(){
                                    var $this = $(this);
                                    var id = $this.index();
                                    var filename = new Date().getTime()+"_"+$this.index()+"_"+this.width+"x"+this.height+"_@."+$this.attr("title").split(".")[$this.attr("title").split(".").length-1];
                                    $this.attr("title",filename);                                
                                    $this.css({
                                        "border":"1px solid gray",
                                        "margin":"5px"
                                    });
                                    $this.wrap("<div style='text-align:left;margin-bottom:10px;'></div>");
                                    var option = "";
                                    switch($upload_btn.attr("ad_type"))
                                    {
                                        case "1and2":
                                            $this.width(300);
                                            $this.height(66);
                                            option = "<option value='1'>http://www.crazy2go.com/brandstore?store=1&bskeyword=</option>"+
                                                     "<option value='2'>http://www.crazy2go.com/goods?no=</option>";
                                            break;
                                        case "3and4":
                                            $this.width(188);
                                            $this.height(58);
                                            option = "<option value='3'>http://www.crazy2go.com/search?search_select=1&keyword=</option>"+
                                                     "<option value='4'>http://www.crazy2go.com/goods?no=</option>";
                                            break;
                                    }
                                    var append_html =   "<br/>連結網址：<select>"+option+
                                                        "</select><input type='text'>"+
                                                        "<div class='order_up' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 0 10px 16px 10px;border-color: transparent transparent gray transparent;'></div>"+
                                                        "<div class='order_down' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 16px 10px 0 10px;border-color: gray transparent transparent transparent;'></div>"+
                                                        "<br/><br/><a href='#' target='_blank'>檢視連結</a>　　<span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' fname='"+filename+"'>delete</span><br/><br/>";
                                    $this.parent().append(append_html);
                                    $this.parent().find("span").click(function(){
                                        var l = $dialog.data("file_images").length;
                                        if($(this).attr("fname"))
                                        for(var i=0;i<l;i++)
                                        {
                                            if($dialog.data("file_images")[i].fname == $(this).attr("fname"))
                                            {
                                                $dialog.data("file_images").splice(i,1);
                                                break;
                                            }
                                        }
                                        $this.parent().remove();
                                    });
                                    $this.parent().find("select").change(function(){
                                        var $select = $(this);
                                        var $input = $(this).parent().find("input");
                                        var $a = $(this).parent().find("a");
                                        switch($(this).val())
                                        {
                                            case "1":
                                            case "3":
                                                $input.attr("placeholder","請輸入商品關鍵字");
                                                break;
                                            case "2":
                                            case "4":
                                                $input.attr("placeholder","請輸入商品ID");
                                                break;
                                        }
                                        $a.attr("href",$select.find("option:checked").text()+$input.val());
                                    }).trigger("change");
                                    $this.parent().find("input").blur(function(){
                                        $this.parent().find("select").trigger("change");
                                    });
                                    theFile.fname = filename;
                                    $dialog.data("file_images").push(theFile);
                                    if(id==(total-1))
                                    {
                                        $dialog.find("#preview .order_up,#preview .order_down").show();
                                        $dialog.find("#preview .order_up").each(function(){
                                            if($(this).parent().index()==0)
                                            {
                                                $(this).hide();
                                            }
                                            $(this).unbind("click");
                                            $(this).bind("click",function(){
                                                var index = $(this).parent().index();
                                                var newIndex = index;
                                                if(index!=0)
                                                {
                                                    index--;
                                                }
                                                $dialog.find("#preview .order_up,#preview .order_down").show();
                                                $(this).parent().prev().before($(this).parent());
                                                $dialog.find("#preview > div:first .order_up,#preview > div:last .order_down").hide();
                                                var item = $dialog.data("file_images")[index];
                                                $dialog.data("file_images").splice(index,1);
                                                $dialog.data("file_images").splice(newIndex,0,item);
                                                /*var l = $dialog.data("file_images").length;
                                                console.log("----------");
                                                console.log($(this).parent().index()+":"+index+":"+newIndex);
                                                for(var i=0;i<l;i++)
                                                {
                                                    console.log($dialog.data("file_images")[i].fname);
                                                }*/
                                            });
                                        });
                                        $dialog.find("#preview .order_down").each(function(){
                                            if($(this).parent().index()==($dialog.find("#preview > div").size()-1))
                                            {
                                                $(this).hide();
                                            }
                                            $(this).unbind("click");
                                            $(this).bind("click",function(){
                                                var index = $(this).parent().index();
                                                var newIndex = index;
                                                if($(this).parent().index()!=($dialog.find("#preview > div").size()-1))
                                                {
                                                    newIndex ++;
                                                }
                                                $dialog.find("#preview .order_up,#preview .order_down").show();
                                                $(this).parent().next().after($(this).parent());
                                                $dialog.find("#preview > div:first .order_up,#preview > div:last .order_down").hide();
                                                var item = $dialog.data("file_images")[index];
                                                $dialog.data("file_images").splice(index,1);
                                                $dialog.data("file_images").splice(newIndex,0,item);
                                                /*var l = $dialog.data("file_images").length;
                                                console.log("----------");
                                                console.log(index+":"+newIndex);
                                                for(var i=0;i<l;i++)
                                                {
                                                    console.log($dialog.data("file_images")[i].fname);
                                                }*/
                                            });
                                        });
                                    }
                                };
                            };
                        })(f);
                        reader.readAsDataURL(f);
                    }
                });
                

                $("#store_edit_image input[name=store_edit_save]").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var $img = $dialog.find("img");
                    if(!$img[0])
                    {
                        alert("請選取圖片！");
                        return;
                    }
                    var form_data = new FormData();
                    var $file = $dialog.find("input[name=upload]");
                    form_data.append('file', $file.prop('files')[0]);
                    form_data.append("query_type","update_store_image");
                    form_data.append("filename",$img.attr("title"));
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
                
                $("#store_edit_introduction input[name=store_edit_save]").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var introduction = $dialog.find("textarea").val();
                    $.post(filename,{
                       query_type:"update_store_introduction",
                       introduction:introduction
                    },function(){
                       alert("店家說明更新成功！");
                       location.href = location.href;
                    });
                });
                
                $("#store_edit_ad input[name=store_edit_save],#store_edit_ad2 input[name=store_edit_save]").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var $img = $dialog.find("#preview img");
                    var $types = $dialog.find("#preview select");
                    var $items = $dialog.find("#preview input");
                    if(!$img[0])
                    {
                        alert("請選取圖片！");
                        return;
                    }
                    
                    // 商品圖
                    var len = $img.size();
                    var i = 0;
                    var p_ad_filename = [];
                    if(len > 0)
                    {
                        for(i = 0; i < len; i++)
                        {
                            p_ad_filename.push($img.eq(i).attr("title"));
                        }
                    }
                    p_ad_filename = p_ad_filename.join("∵");
                    
                    // item,type
                    len = $items.size();
                    var p_ad_items = [];
                    var p_ad_types = [];
                    if(len > 0)
                    {
                        for(i = 0; i < len; i++)
                        {
                            p_ad_items.push($items.eq(i).val());
                            p_ad_types.push($types.eq(i).val());
                        }
                    }
                    p_ad_items = p_ad_items.join("∵");
                    p_ad_types = p_ad_types.join("∵");
                    
                    var p_ad_delete = [];
                    if($dialog.data("delete_images"))
                    {
                        p_ad_delete = $dialog.data("delete_images");
                    }
                    p_ad_delete = p_ad_delete.join("∵");

                    var form_data = new FormData();
                    for(i in $dialog.data("file_images"))
                    {
                        if($dialog.data("file_images")[i].fname)
                        form_data.append('file_images[]', $dialog.data("file_images")[i]);
                    }
                    form_data.append("query_type","update_store_ad");
                    form_data.append("ads_filename",p_ad_filename);
                    form_data.append("ads_item",p_ad_items);
                    form_data.append("ads_type",p_ad_types);
                    form_data.append("ads_delete",p_ad_delete);
                    $.ajax({
                        url: filename,
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,                         
                        type: 'post',
                        success: function(data){
                            console.log($.trim(data));
                            alert($.trim(data));
                            location.href = location.href;
                        }
                    });
                });
                
                //載入廣告
                $("#store_edit_table input[name='intro_edit']").click(function(){
                    var $dialog = $("#dialog_content");
                    $dialog.find("textarea").focus();
                    var $upload_btn = $(this);
                    $.post(filename,{
                        query_type:"get_store_page_ad",
                        type:$(this).attr("ad_type")
                    },function(data){
                        data = $.trim(data);
                        if(data == "")return;
                        data = $.parseJSON(data);
                        $dialog.data("file_images",$dialog.data("file_images")||[]);
                        var len = data.images.length;
                        var total = len;
                        $dialog.data("delete_images",[]);
                        for(var i = 0; i< len;i++)
                        {
                            $dialog.data("file_images").push({});
                            $dialog.find("#preview").append('<img class="thumb" src="../public/img/store/'+data.images[i]+'" fi_no="'+data.fi_no[i]+'" title="'+data.images[i]+'" type="'+data.type[i]+'" item="'+data.item[i]+'" />');
                            $dialog.find("#preview img:last")[0].onload = function(){
                                var $this = $(this);
                                var id = $this.index();
                                $this.css({
                                    "border":"1px solid gray",
                                    "margin":"5px"
                                });
                                $this.wrap("<div style='text-align:left;margin-bottom:10px;'></div>");
                                var option = "";
                                switch($upload_btn.attr("ad_type"))
                                {
                                    case "1and2":
                                        $this.width(300);
                                        $this.height(66);
                                        option = "<option value='1'>http://www.crazy2go.com/brandstore?store=1&bskeyword=</option>"+
                                                 "<option value='2'>http://www.crazy2go.com/goods?no=</option>";
                                        break;
                                    case "3and4":
                                        $this.width(188);
                                        $this.height(58);
                                        option = "<option value='3'>http://www.crazy2go.com/search?search_select=1&keyword=</option>"+
                                                 "<option value='4'>http://www.crazy2go.com/goods?no=</option>";
                                        break;
                                }
                                var append_html =   "<br/>連結網址：<select>"+option+
                                                    "</select><input type='text'>"+
                                                    "<div class='order_up' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 0 10px 16px 10px;border-color: transparent transparent gray transparent;'></div>"+
                                                    "<div class='order_down' style='cursor:pointer;display:inline-block;width:0px;height:0px;border-style: solid;border-width: 16px 10px 0 10px;border-color: gray transparent transparent transparent;'></div>"+
                                                    "<br/><br/><a href='#' target='_blank'>檢視連結</a>　　<span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' fname='"+filename+"'>delete</span><br/><br/>";
                                $this.parent().append(append_html);
                                $this.parent().find("span").click(function(){
                                    $dialog.data("delete_images").push($this.attr("fi_no"));
                                    $this.parent().remove();
                                });
                                $this.parent().find("select").val($this.attr("type"));
                                $this.parent().find("input").val($this.attr("item"));
                                $this.parent().find("select").change(function(){
                                    var $select = $(this);
                                    var $input = $(this).parent().find("input");
                                    var $a = $(this).parent().find("a");
                                    switch($(this).val())
                                    {
                                        case "1":
                                        case "3":
                                            $input.attr("placeholder","請輸入商品關鍵字");
                                            break;
                                        case "2":
                                        case "4":
                                            $input.attr("placeholder","請輸入商品ID");
                                            break;
                                    }
                                    $a.attr("href",$select.find("option:checked").text()+$input.val());
                                }).trigger("change");
                                $this.parent().find("input").blur(function(){
                                    $this.parent().find("select").trigger("change");
                                });
                                if(id==(total-1))
                                {
                                    $dialog.find("#preview .order_up,#preview .order_down").show();
                                    $dialog.find("#preview .order_up").each(function(){
                                        if($(this).parent().index()==0)
                                        {
                                            $(this).hide();
                                        }
                                        $(this).unbind("click");
                                        $(this).bind("click",function(){
                                            var index = $(this).parent().index();
                                            var newIndex = index;
                                            if(index!=0)
                                            {
                                                index--;
                                            }
                                            $dialog.find("#preview .order_up,#preview .order_down").show();
                                            $(this).parent().prev().before($(this).parent());
                                            $dialog.find("#preview > div:first .order_up,#preview > div:last .order_down").hide();
                                            if(!$dialog.data("file_images"))return;
                                            var item = $dialog.data("file_images")[index];
                                            $dialog.data("file_images").splice(index,1);
                                            $dialog.data("file_images").splice(newIndex,0,item);
                                        });
                                    });
                                    $dialog.find("#preview .order_down").each(function(){
                                        if($(this).parent().index()==($dialog.find("#preview > div").size()-1))
                                        {
                                            $(this).hide();
                                        }
                                        $(this).unbind("click");
                                        $(this).bind("click",function(){
                                            var index = $(this).parent().index();
                                            var newIndex = index;
                                            if($(this).parent().index()!=($dialog.find("#preview > div").size()-1))
                                            {
                                                newIndex ++;
                                            }
                                            $dialog.find("#preview .order_up,#preview .order_down").show();
                                            $(this).parent().next().after($(this).parent());
                                            $dialog.find("#preview > div:first .order_up,#preview > div:last .order_down").hide();
                                            if(!$dialog.data("file_images"))return;
                                            var item = $dialog.data("file_images")[index];
                                            $dialog.data("file_images").splice(index,1);
                                            $dialog.data("file_images").splice(newIndex,0,item);
                                        });
                                    });
                                }
                            };
                        }
                    });
                });
                
                
                // show red region
                function show_red_region(href, regionID){
                    var $dialog = $("#dialog_content");
                    $dialog.append("<iframe src='"+href+"'></iframe>");
                    var $iframe = $dialog.find("iframe").css({
                        "display":"inline-block",
                        "width":"100%",
                        "height":"100%"
                    });
                    $iframe.load(function(){
                        var $rect = $iframe.contents().find(regionID);
                        var $body = $iframe.contents().find("body");
                        $body.css({
                            "background":"white"
                        })
                        $body.append($("<div></div>").css({
                            "position":"absolute",
                            "display":"inline-block",
                            "top":"0px",
                            "left":"0px",
                            "width":$iframe.contents().width()+"px",
                            "height":$iframe.contents().height()+"px",
                            "background":"rgba(0,0,0,0.6)",
                            "z-index":"99999"
                        }));
                        var mt = parseInt($rect.css("margin-top").replace(/px/,""));
                        var pb = parseInt($rect.css("padding-bottom").replace(/px/,""));
                        $body.append($("<div>圖片區塊</div>").css({
                            "position":"absolute",
                            "display":"inline-block",
                            "top":($rect.position().top+mt)+"px",
                            "left":$rect.position().left+"px",
                            "width":$rect.width()+"px",
                            "height":($rect.height()+pb)+"px",
                            "line-height":$rect.height()+"px",
                            "text-align":"center",
                            "background":"rgba(255,0,0,0.5)",
                            "color":"white",
                            "z-index":"100000"
                        }));
                    });
                }
                
                $("#store_edit_table #link_brand").click(function(){
                    show_red_region("http://www.crazy2go.com/goods?no=1&nohistory=1","#store_image");
                });
                
                $("#store_edit_table #link_store").click(function(){
                    show_red_region("http://www.crazy2go.com/brandstore?store=1&nohistory=1", "#ad_slide");
                });
                
                $("#store_edit_table #link_goods").click(function(){
                    show_red_region("http://www.crazy2go.com/goods?no=1&nohistory=1","#store_activity");
                });
            });
            
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
                <div id="body_right">
                    <?php 
                        echo "<table id='store_edit_image' class='table-v' data-dialog='編輯圖示'>";
                        echo "<tr><td width='200'>店家圖示(220x170)</td><td><div id='preview'></div><input type='file' name='upload'></td></tr>";
                        echo "<tr><td></td><td><input name='store_edit_save' type='button' value='儲存' /></td></tr>";
                        echo "</table>";
                        
                        echo "<table id='store_edit_introduction' class='table-v' data-dialog='編輯簡述'>";
                        echo "<tr><td width='200'>店家簡述</td><td><textarea style='width:600px;' placeholder='請輸入店家說明'></textarea></td></tr>";
                        echo "<tr><td></td><td><input name='store_edit_save' type='button' value='儲存' /></td></tr>";
                        echo "</table>";
                        
                        echo "<table id='store_edit_ad' class='table-v' data-dialog='編輯品牌頁廣告'>";
                        echo "<tr><td width='200'>店家圖示(1223x270)可多張圖</td><td><div id='preview'></div><input type='file' name='upload' ad_type='1and2' multiple='multiple'></td></tr>";
                        echo "<tr><td></td><td><input name='store_edit_save' type='button' value='儲存' /></td></tr>";
                        echo "</table>";
                    
                        echo "<table id='store_edit_ad2' class='table-v' data-dialog='編輯商品頁廣告'>";
                        echo "<tr><td width='200'>店家圖示(188x58)可多張圖</td><td><div id='preview'></div><input type='file' name='upload' ad_type='3and4' multiple='multiple'></td></tr>";
                        echo "<tr><td></td><td><input name='store_edit_save' type='button' value='儲存' /></td></tr>";
                        echo "</table>";
                        
                        echo "<table id='store_edit_table' class='table-v'>";
                        echo "<tr><td style='width:200px;'>店名</td><td>".$store_name."</td></tr>";
                        //echo "<tr><td>店家圖示<br/><br/><input id='link_brand' type='button' value='區塊展示連結' style='cursor:pointer;' data-open-dialog='圖片位置紅色區塊展示'></td><td><div id='img_show'>".$store_images."</div><p><input name='image_edit' type='button' value='編輯' style='float:right;' data-open-dialog='編輯圖示'/></p></td></tr>";
                        echo "<tr><td>店家簡述</td><td>".$store_introduction."<p><input name='intro_edit' type='button' value='編輯' style='float:right;' data-open-dialog='編輯簡述'/></p></td></tr>";
                        //echo "<tr><td>店家廣告<br/><br/><input id='link_store' type='button' value='品牌頁區塊展示連結' style='cursor:pointer;' data-open-dialog='圖片位置紅色區塊展示'></td><td><div id='ad_show'>".$store_page_ad."</div><input name='intro_edit' type='button' ad_type='1and2' value='編輯' style='float:right;' data-open-dialog='編輯品牌頁廣告'/></td></tr>";
                        //echo "<tr><td>店家廣告<br/><br/><input id='link_goods' type='button' value='商品頁區塊展示連結' style='cursor:pointer;' data-open-dialog='圖片位置紅色區塊展示'></td><td><div id='ad2_show'>".$store_page_ad2."</div><input name='intro_edit' type='button' ad_type='3and4' value='編輯' style='float:right;' data-open-dialog='編輯商品頁廣告'/></td></tr>";
                        echo "</table>";
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

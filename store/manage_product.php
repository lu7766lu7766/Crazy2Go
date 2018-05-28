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
        
        //所有商品資料
        $all_product = array();
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by category asc, fi_no asc";
        $query = "select * from goods_index where store=".$login_store." and `delete`=0 ".$orderby;
        if(isset($_GET['keyword']))
        {
            switch($_GET['type'])
            {
                case '1'://商品ID
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from goods_index where store=".$login_store." and `delete`=0 and fi_no >= ".(int)$_GET['keyword']." ".$orderby;
                    break;
                case '2'://商品關鍵字
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by category asc, fi_no asc";
                    $query = "select * from goods_index where store=".$login_store." and `delete`=0 and name like '%".$_GET['keyword']."%' ".$orderby;
                    break;
                case '3'://商品分類
                    $id = $dba->query("select fi_no from category where name like '%".$_GET['keyword']."%'");
                    $ids = array();
                    foreach($id as $k=>$v)
                    {
                        $ids[] = $v["fi_no"];
                    }
                    $ids = implode(",", $ids);
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by category asc, fi_no asc";
                    $query = "select * from goods_index where store=".$login_store." and `delete`=0 and category in (".$ids.") ".$orderby;
                    break;
                case '4'://商品品牌
                    $id = $dba->query("select fi_no from brand_group where name like '%".$_GET['keyword']."%'");
                    $ids = array();
                    foreach($id as $k=>$v)
                    {
                        $ids[] = $v["fi_no"];
                    }
                    $ids = implode(",", $ids);
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by brand asc, fi_no asc";
                    $query = "select * from goods_index where store=".$login_store." and `delete`=0 and brand in (".$ids.") ".$orderby;
                    break;
                case '5'://供應商
                    $id = $dba->query("select fi_no from store_supplier where name like '%".$_GET['keyword']."%'");
                    $ids = array();
                    foreach($id as $k=>$v)
                    {
                        $ids[] = $v["fi_no"];
                    }
                    $ids = implode(",", $ids);
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by supplier asc, fi_no asc";
                    $query = "select * from goods_index where store=".$login_store." and `delete`=0 and supplier in (".$ids.") ".$orderby;
                    break;
            }
        }

        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $len = count($result);
        
        for($i = 0; $i < $len; $i++)
        {
            $all_product[]=array(
                fi_no => $result[$i]['fi_no'],
                category => $result[$i]['category'],
                brand => $result[$i]['brand'],
                name => $result[$i]['name'],
                price => $result[$i]['price'],
                promotions => $result[$i]['promotions'],
                discount => isset($_GET["oname"])?$result[$i]['promotions']:$result[$i]['discount'],
                free_gifts => $result[$i]['free_gifts'],
                free_shipping => $result[$i]['free_shipping'],
                images => json_decode($result[$i]['images']),
                status_audit => $result[$i]['status_audit'],
                status_shelves => $result[$i]['status_shelves'],
                supplier => $result[$i]['supplier']
            );
        }
        
        //所有商品總類
        $query = "select fi_no, name, `index` from category order by `index` asc, weights desc";
        $result = $dba->query($query);
        $all_product_type = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_product_type[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['name'],
                index => $result[$i]['index']
            );
        }

        //所有品牌
        $query = "select fi_no, category, name from brand_group where `delete`=0 order by fi_no asc, weights desc";
        $result = $dba->query($query);
        $all_product_brand = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_product_brand[]=array(
                fi_no => $result[$i]['fi_no'],
                category => $result[$i]['category'],
                name => $result[$i]['name']
            );
        }
        
        //所有屬性
        $query = "select fi_no, category, name, type, required from attribute where `delete`=0 order by fi_no asc, weights asc";
        $result = $dba->query($query);
        $all_attribute = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_attribute[]=array(
                fi_no => $result[$i]['fi_no'],
                category => $result[$i]['category'],
                name => $result[$i]['name'],
                type => $result[$i]['type'],
                required => $result[$i]['required']
            );
        }
        
        //所有屬性種類
        $query = "select fi_no, attribute, item from attribute_item where `delete`=0 order by fi_no asc, weights asc";
        $result = $dba->query($query);
        $all_attribute_item = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_attribute_item[]=array(
                fi_no => $result[$i]['fi_no'],
                attribute => $result[$i]['attribute'],
                item => $result[$i]['item']
            );
        }
        
        //所有供應商
        $query = "select fi_no, name from store_supplier order by fi_no asc";
        $result = $dba->query($query);
        $all_supplier = array();
        $len = count($result);
        for($i = 0; $i < $len; $i++)
        {
            $all_supplier[]=array(
                fi_no => $result[$i]['fi_no'],
                name => $result[$i]['name']
            );
        }

        //依權限顯示
        $display_add = strpos($login_permissions,"product_add")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_delete = strpos($login_permissions,"product_del")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_copy = strpos($login_permissions,"product_copy")!==false || strpos($login_permissions,"all")!==false?true:false;
        $display_edit = strpos($login_permissions,"product_edit")!==false || strpos($login_permissions,"all")!==false?true:false;
        
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
                $("#product_search select[name='type']").val(<?php echo $_GET['type'];?>);
                $("#product_search input[name='keyword']").val(<?php echo "'".$_GET['keyword']."'";?>);
                <?php }?>
                
                $("#body_right").show();
                $("#image_choose_panel").hide();
                $("#image_resize_panel").hide();
                $("#table_creator").hide();
                
                //列表排序
                $("#product_list_table tr:first td[name]").each(function(){
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
                $("#product_list_table .product_del").click(function(){
                    if(!confirm("確定刪除？"))return;
                    var $this = $(this);
                    var fi_no = $this.parent().parent().attr("fi_no");
                    var name = $this.parent().parent().attr("name");
                    $.post(filename,{
                        query_type:"product_del",
                        name:name,
                        fi_no:fi_no
                    },function(){
                        alert("已刪除！");
                        location.href = location.href;
                    });
                });
                
                //計算類別層級
                $(".cateList").each(function(){
                    var $this = $(this);
                    var cur_category = $this.text();
                    $this.text('');
                    var level_index = 0;
                    while(cur_category)
                    {
                        var $cur_cat = $("select[name='categoryall'] option[value="+cur_category+"]")
                        $this.prepend("<span level='"+level_index+"'>"+$cur_cat.text()+"</span><br/>");
                        cur_category = parseInt($cur_cat.attr("index"));
                        level_index++;
                    }
                    $this.find("span").each(function(){
                        var $this = $(this);
                        var lv = level_index - parseInt($this.attr("level")) - 1;
                        var space = "";
                        for(var i=0; i<lv; i++)
                        {
                            $this.prepend("—");
                        }
                    })
                });
                
                //品牌計算
                $(".brandList").each(function(){
                    var $this = $(this);
                    var cur_brand = $this.text();
                    if(cur_brand=="")cur_brand=0;
                    $this.text('');
                    var $cur_brand = $("select[name='brandall'] option[value="+cur_brand+"]");
                    $this.prepend("<span>"+$cur_brand.text()+"</span>");
                });
                
                //供應商計算
                $(".supplierList").each(function(){
                    var $this = $(this);
                    var cur_supplier = $this.text();
                    if(cur_supplier=="")cur_supplier=0;
                    $this.text('');
                    var $cur_supplier = $("select[name='supplierall'] option[value="+cur_supplier+"]");
                    $this.prepend("<span>"+$cur_supplier.text()+"</span>");
                });
                
                //新增,複製與編輯商品分類變更
                $(document).on("change","#product_add_table select[name^='category'], #product_edit_table select[name^='category'], #product_copy_table select[name^='category']",function(){
                    var $dialog = $("#dialog_content");
                    var category_list = [];
                    $dialog.find("select[name^='category']").each(function(){
                        category_list.push($(this).val());
                    });
                    var caLength = $("select[name='categoryall'] option").size();
                    var cMax = 3;
                    for(var i=0;i<cMax;i++)
                    {
                        if(category_list[i])continue;
                        category_list.push($("select[name='categoryall'] option").eq(caLength-1-(cMax-1-i)).attr("value"));
                    }
                    var startIndex = 0;
                    var levelIndex = 0;
                    $dialog.find("#category").html("");
                    $("select[name='categoryall'] option").each(function(){
                        var $this = $(this);
                        if($this.attr("value") == category_list[levelIndex])
                        {
                            var $copy = $("select[name='categoryall'] option[index="+startIndex+"]").clone();
                            if($copy.size()==0)return false;
                            $dialog.find("#category").append("<select name='category"+levelIndex+"'></select>");
                            $dialog.find("select[name='category"+levelIndex+"']").append($copy);
                            $dialog.find("select[name='category"+levelIndex+"']").val($this.attr("value"));
                            if($dialog.find("select[name='category"+levelIndex+"']").val() == null)
                            {
                                $dialog.find("select[name='category"+levelIndex+"']").val($dialog.find("select[name='category"+levelIndex+"'] option:first").attr("value"));
                            }
                            startIndex = $dialog.find("select[name='category"+levelIndex+"']").val();
                            levelIndex++;
                        }
                    })
                    var category = $dialog.find("select[name^='category']:last").val();
                    var brand = $dialog.find("select[name='brand']");
                    brand.html("");
                    $("select[name='brandall']").find("option").each(function(){
                        var $this = $(this);
                        if($this.attr("category").search("\""+category+"\"")!=-1)
                        {
                            brand.append($this.clone());
                        }
                    });
                    
                    var attribute = $dialog.find("#attribute");
                    var attribute_item = $("select[name='attribute_item']");
                    attribute.html("");
                    $("select[name='attribute'] option[category="+category+"]").each(function(){
                        var $this = $(this);
                        var fi_no = $this.attr("fi_no");
                        var name = $this.attr("name");
                        var type = $this.attr("type");
                        var required = $this.attr("require");
                        var display = "";
                        if(type == "0")
                        {
                            attribute.append("<p fi_no="+fi_no+" type="+type+"></p>");
                            display = name;
                            display += " <select>";
                            if(required == "0") display += "<option value='0'>無</option>";
                            attribute_item.find("option[attribute="+fi_no+"]").each(function(){
                                $this = $(this);
                                display += "<option value="+$this.attr("fi_no")+">"+$this.attr("item")+"</option>";
                            });
                            display += "</select>";
                        }
                        else if(type == "1")
                        {
                            attribute.append("<p fi_no="+fi_no+" type="+type+"></p>");
                            display = name+"<br/>";
                            attribute_item.find("option[attribute="+fi_no+"]").each(function(){
                                $this = $(this);
                                display += "<input type='checkbox' name="+$this.attr("fi_no")+">"+$this.attr("item")+"&nbsp;";
                            });
                        }  
                        attribute.find("p[fi_no="+fi_no+"]").append(display);
                    });
                });
                
                //新增,複製與編輯商品
                $("#product_add_btn,#product_list_table .product_edit,#product_list_table .product_copy").click(function(){
                    var $dialog = $("#dialog_content");
                    $dialog.data("fi_no",$(this).parent().parent().attr("fi_no"));
                    if($dialog.data("fi_no"))
                    {
                        $("#dialog_title").text($("#dialog_title").text()+" "+$dialog.data("fi_no")+" - "+$(this).parent().parent().attr("name"));
                    }
                    $dialog.find("input[name=product]").focus();
                    var startIndex = 0;
                    var levelIndex = 0;
                    $("select[name='categoryall'] option").each(function(index){
                        var $this = $(this);
                        if($this.attr("index") == startIndex)
                        {
                            $dialog.find("#category").append("<select name='category"+levelIndex+"'></select>")
                            $dialog.find("select[name='category"+levelIndex+"']").append($("select[name='categoryall'] option[index="+startIndex+"]").clone());
                            startIndex = $this.attr("value");
                            levelIndex++;
                        }
                    });
                    $dialog.find("select[name^='category']").trigger("change");
                    var supplier = $dialog.find("select[name='supplier']");
                    $("select[name='supplierall']").find("option").each(function(){
                        var $this = $(this);
                        supplier.append($this.clone());
                    });
                    $dialog.find("textarea[name='introduction']").jqte();
                    $dialog.find("textarea[name='instructions']").jqte();
                    $dialog.find("textarea[name='remark']").jqte();
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
                        var $this = $(this);
                        $this.parent().css({"position":"relative"});
                        var $clone = $this.children(":last").clone();
                        $this.append($clone.clone());
                        var tb = $this.children(":last").find("a");
                        tb.css({
                            "background":"url(http://www.crazy2go.com/backend/js/jquery-te/jquery-table.png)",
                            "display":"inline-block",
                            "width":"22px",
                            "height":"20px"
                        }).click(function(){
                            if($this.parent().find("#table_creator")[0])return;
                            $dialog.bind("scroll",function(){
                                 $dialog.unbind("scroll");
                                 $dialog.find("#table_creator").remove();
                             });
                            $this.parent().prepend($("#table_creator").clone().show().css({
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
                        
                        if(index!=0)return;
                        $this.append($clone.clone());
                        var last = $this.children(":last").find("a");
                        last.text("貼圖");
                        last.attr("class","");
                        last.parent().css({"margin-top":"5px"});
                        last.click(function(){
                            if($dialog.find("#image_choose_panel")[0])return;
                            $this.parent().parent().prepend($("#image_choose_panel").clone());
                            var $panel = $dialog.find("#image_choose_panel");
                            
                            $panel.show();
                            var $content = $panel.find("#content");
                            var $imgs = $dialog.find("#introduction_upload_image img");
                            if(!$imgs[0])
                            {
                                alert("請先上傳完整描述圖片！");
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
                                var display=$dialog.find("textarea[name='introduction']").val();
                                $content.find(".choose_item").each(function(){
                                    var $this = $(this);
                                    if($this.find("input[name='select_btn']").val()=='選取')return;
                                    var src = $this.find("img").attr("src");
                                    var title = $this.find("img").attr("title");
                                    var w = $this.find("input[name='width']").val();
                                    var h = $this.find("input[name='height']").val();
                                    display+="<img src='"+src+"' title='"+title+"' style='width:"+w+"px;height:"+h+"px'>";
                                });
                                $dialog.find("textarea[name='introduction']").val(display);
                                $dialog.find(".jqte_editor_x").eq(0).html(display);
                                $dialog.find(".jqte_editor_x img").unbind("dblclick");
                                $dialog.find(".jqte_editor_x img").bind("dblclick",imageResize);
                                $panel.remove();
                            });
                        });
                    });
                    
                    //供應商搜尋
                    $dialog.find("input[name='search']").click(function(){
                        var supplier = $dialog.find("select[name='supplier']");
                        var keyword = $dialog.find("input[name='keyword']").val();
                        supplier.html("");
                        if(keyword=="")
                        {
                            $("select[name='supplierall']").find("option").each(function(){
                                var $this = $(this);
                                supplier.append($this.clone());
                            });
                        }
                        else
                        {
                            $("select[name='supplierall']").find("option").each(function(){
                                var $this = $(this);
                                if($this.text().search(keyword)!=-1)
                                supplier.append($this.clone());
                            });
                        }
                    });
                    $dialog.find("input[name='keyword']").keypress(function(e){
                        if(e.keyCode==13){
                            $dialog.find("input[name='search']").trigger("click");
                        }
                    });
                    
                    //使用者定義屬性
                    $dialog.find("#product_class").each(function(){
                        var $this = $(this);
                        $this.append("<p>規格 <input type='text' name='specification'><span>+</span></p>");
                        $this.find("p:last span").css({"cursor":"pointer"}).bind('click',function(){
                            if($this.find("input:last").val()!="")
                            {
                                var p = $this.find("p:last");
                                $this.append(p.clone(true));
                                p.append("<ul><li><p>類別 <input type='text' name='class'><span>+</span></p></li></ul>");
                                p.find("ul li p:last span").css({"cursor":"pointer"}).bind('click',function(){
                                    if(p.find("ul li p input:last").val()!="")
                                    {
                                        p.append(p.find("ul:last").clone(true));
                                        p.find("ul li p input:last").val("");
                                        $(this).html("-");
                                        $(this).unbind('click');
                                        $(this).css({"cursor":"pointer"}).bind('click',function(){
                                            $(this).unbind('click');
                                            $(this).parent().parent().parent().remove();
                                        });
                                    }
                                    else
                                    {
                                        alert("請輸入商品類別名!");
                                        return;
                                    }
                                });
                                $this.find("p > input:last").val("");
                                $(this).html("-");
                                $(this).unbind('click');
                                $(this).css({"cursor":"pointer"}).bind('click',function(){
                                   $(this).parent().find("ul li p span").unbind('click');
                                   $(this).unbind('click');
                                   $(this).parent().remove();
                                });
                            }
                            else
                            {
                                //@alert("請輸入商品規格名!");
                                return;
                            }
                        });
                    });
                    
                    //使用者定義屬性之庫存列表
                    $dialog.find("#product_class_inventory").each(function(){
                        var $this = $(this);
                        var $p_class = $(this).prev();
                        $this.parent().append("<button>商生庫存列表</button>");
                        $this.parent().find("button").click(function(){
                            var p_specification = [];
                            var p_class = [];
                            var output = [];
                            var i = -1;
                            $p_class.find("p input").each(function(){
                                if($(this).attr('name')=="specification" && $(this).val()!="")
                                {
                                    if($(this).parent().find("ul")[0]==undefined)
                                    {
                                        alert("規格請至少新增一項類別！");
                                        return;
                                    }
                                    p_specification.push($(this).val());
                                    p_class.push([]);
                                    i++;
                                }
                                if($(this).attr('name')=="class" && $(this).val()!="")
                                {
                                    p_class[i].push($(this).val());
                                }
                            });
                            
                            if(p_class.length == 0)
                            {
                                //@alert("請至少新增一項規格！");
                                return;
                            }

                            function explore(curDim, prefix) {
                                //取出下一層維度
                                var nextDim = p_class.shift();
                                for (var i = 0; i < curDim.length; i++) {
                                    if (nextDim) 
                                        //若仍有下一層維度，繼續展開
                                        //並將傳入字首加上目前維度選項成為下一層的字首
                                        explore(nextDim, prefix + curDim[i] + "/");
                                    else 
                                        //若已無下一層，則傳入字首加上目前維度選項成為結果
                                        output.push(prefix + curDim[i]);
                                }
                                //將下層維度存回，供上層維度其他選項使用
                                if (nextDim) p_class.push(nextDim);
                            }
                            //傳入第一層維度開始演算
                            explore(p_class.shift(), "");

                            var outputStr = "";
                            var len = output.length;
                            i = 0;
                            outputStr += "<table>";
                            outputStr += "<tr><td>商品種類</td><td>庫存量</td></tr>";
                            for(i=0;i<len;i++)
                            {
                                outputStr+="<tr><td>"+output[i]+"</td><td> <input type='text' value='0' /></td></tr>";
                            }
                            outputStr += "</table>";
                            $this.html(outputStr);
                        });
                    });

                    //相關商品
                    function iframeOnload($iframe){
                        $iframe.load(function(){
                            $(this).contents().find("#product_list_table tr").each(function(){
                                var $this = $(this);
                                $this.find(".relate_news").click(function(){
                                    if($("#news_content").children().size()<10)
                                    {
                                        var duplicate = false;
                                        $("#news_content div").each(function(){
                                            if($(this).attr("fi_no") == $this.attr("fi_no"))
                                            {
                                                duplicate = true;
                                                return false;
                                            }
                                        });
                                        if(duplicate)
                                        {
                                            alert("新商品重複！");
                                            return;
                                        }
                                        var item = "<div fi_no='"+$this.attr("fi_no")+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/"+$this.attr("images")+"' style='width:65px;height:65px;'><br/>id:"+$this.attr("fi_no")+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                        $("#news_content").append(item);
                                    }
                                    else
                                    {
                                        alert("最多10件新商品！");
                                    }
                                });
                                $this.find(".relate_hots").click(function(){
                                    if($("#hots_content").children().size()<10)
                                    {
                                        var duplicate = false;
                                        $("#hots_content div").each(function(){
                                            if($(this).attr("fi_no") == $this.attr("fi_no"))
                                            {
                                                duplicate = true;
                                                return false;
                                            }
                                        });
                                        if(duplicate)
                                        {
                                            alert("熱銷商品重複！");
                                            return;
                                        }
                                        var item = "<div fi_no='"+$this.attr("fi_no")+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/"+$this.attr("images")+"' style='width:65px;height:65px;'><br/>id:"+$this.attr("fi_no")+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                        $("#hots_content").append(item);
                                    }
                                    else
                                    {
                                        alert("最多10件熱銷商品！");
                                    }
                                });
                            })
                        });
                    }
                    $dialog.find("#relate").each(function(){
                        var $this = $(this);
                        var auto_news = '<input id="auto_news" type="button" value="自動選取最新" style="height:30px;border:1px solid gray;background:white;">';
                        var auto_hots = '<input id="auto_hots" type="button" value="自動選取熱門" style="height:30px;border:1px solid gray;background:white;">';
                        var table = "<table class='table-v'>"+
                                    "<tr><td style='width:80px;height:130px;'>最新相關<br/><br/>"+auto_news+"<br/><br/><span style='font-size:8pt;'>(依照分類篩選)</span></td><td id='news_content'></td></tr>"+
                                    "<tr><td style='height:130px;'>熱門相關<br/><br/>"+auto_hots+"<br/><br/><span style='font-size:8pt;'>(依照分類篩選)</span></td><td id='hots_content'></td></tr>"+
                                    "</table>";
                        var $table = $(table);
                        init_style($table);
                        $this.append($table);
                        var iframe = "<iframe id='relate_frame' src='iframe_product.php' scrolling='no' style='width:824px;height:450px;border:1px solid gray;'></iframe>";
                        var $iframe = $(iframe);
                        $this.append($iframe);
                        iframeOnload($iframe);
                        $table.find("#auto_news").click(function(){
                            var cat = $dialog.find("select[name^='category']:last").val();
                            var div_count = 0;
                            var not_in = [];
                            $("#news_content > div").each(function(){
                                not_in.push($(this).attr("fi_no"));
                                div_count++;
                            });
                            not_in = not_in.join(",");
                            $.post(filename,{query_type:"get_news",category:cat,auto_count:10-div_count,not_in:not_in},function(data){
                                data = $.trim(data);
                                if(data=="")
                                {
                                    alert("無相關商品");
                                    return;
                                }
                                var $iframe = $dialog.find("iframe[id='relate_frame']");
                                var og_src = $iframe.attr("src");
                                $iframe.attr("src",og_src+"?type=0&keyword="+data);
                                $iframe.bind("load",function(){
                                    $iframe.unbind("load");
                                    $iframe.contents().find("#product_list_table tr").each(function(){
                                        var fi_no = $(this).attr("fi_no")
                                        var images = $(this).attr("images")
                                        var item = "<div fi_no='"+fi_no+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/"+images+"' style='width:65px;height:65px;'><br/>id:"+fi_no+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                        $("#news_content").append(item);
                                    });
                                    $iframe.attr("src",og_src);
                                    iframeOnload($iframe);
                                });
                            });
                        });
                        $table.find("#auto_hots").click(function(){
                            var cat = $dialog.find("select[name^='category']:last").val();
                            var div_count = 0;
                            var not_in = [];
                            $("#hots_content > div").each(function(){
                                not_in.push($(this).attr("fi_no"));
                                div_count++;
                            });
                            not_in = not_in.join(",");
                            $.post(filename,{query_type:"get_hots",category:cat,auto_count:10-div_count,not_in:not_in},function(data){
                                data = $.trim(data);
                                if(data=="")
                                {
                                    alert("無相關商品");
                                    return;
                                }
                                var $iframe = $dialog.find("iframe[id='relate_frame']");
                                var og_src = $iframe.attr("src");
                                $iframe.attr("src",og_src+"?type=0&keyword="+data);
                                $iframe.bind("load",function(){
                                    $iframe.unbind("load");
                                    $iframe.contents().find("#product_list_table tr").each(function(){
                                        var fi_no = $(this).attr("fi_no")
                                        var images = $(this).attr("images")
                                        var item = "<div fi_no='"+fi_no+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/"+images+"' style='width:65px;height:65px;'><br/>id:"+fi_no+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                        $("#hots_content").append(item);
                                    });
                                    $iframe.attr("src",og_src);
                                    iframeOnload($iframe);
                                });
                            });
                        });
                    });
                    
                    //組合商品
                    function iframeCombineOnload($iframe){
                        $iframe.load(function(){
                            $(this).contents().find("#product_list_table tr").each(function(){
                                var $this = $(this);
                                $this.find(".add_combine").click(function(){
                                    if($("#combine_content").children().size()<10)
                                    {
                                        var duplicate = false;
                                        $("#combine_content div").each(function(){
                                            if($(this).attr("fi_no") == $this.attr("fi_no"))
                                            {
                                                duplicate = true;
                                                return false;
                                            }
                                        });
                                        if(duplicate)
                                        {
                                            alert("組合商品重複！");
                                            return;
                                        }

                                        $.post(filename,{query_type:"get_product_detail",fi_no:$this.attr("fi_no")},function(data){
                                            data = $.trim(data);
                                            if(data=="")return;
                                            //製作panel
                                            var $panel = $("<div id='spec_panel' class='shadowRoundCorner' style='display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:778px;'></div>");
                                            var $c_frame = $("#combine_frame");
                                            $c_frame.before($panel);
                                            $panel.css({
                                                "position":"absolute",
                                                "left":$dialog.position().left+5+"px",
                                                "top":$dialog.position().top+5+"px",
                                                "width":$dialog.width()-25+"px",
                                                "height":$dialog.height()-25+"px"
                                            });
                                            //specification 跟 inventory 
                                            data = data.split("`");
                                            var type_items = data[8]==""?{}:$.parseJSON(data[8]);//specifications
                                            var type_stock = data[9]==""?[]:$.parseJSON(data[9]);//inventory
                                            var default_status = false;
                                            var type_item_length = 0;
                                            var pick_stock = 0;
                                            var pick_index = 0;
                                            var pick_specs = {};
                                            var pick_specs_index = {};
                                            //顯示specification
                                            var content_style = "";
                                            for(var i in type_items)
                                            {
                                                pick_specs[i]=[type_items[i][0]];
                                                pick_specs_index[i] = 0;
                                                if(i == "default")
                                                {
                                                    default_status = true;
                                                    continue;
                                                }
                                                content_style += "<div class='tr type' data-type='"+i+"' data-count='"+type_items[i].length+"'><div class='td' style='width:60px;padding:10px 10px 10px 10px;'>"+i+"</div><div class='td' style='padding:10px 10px 10px 10px;'>";
                                                for(var j=0; j< type_items[i].length; j++) {
                                                    content_style += "<div class='item' data-item='"+i+"' data-no='"+j+"' data-val='"+type_items[i][j]+"' data-select='0' data-stock='m' style='cursor:pointer;margin:5px; padding:5px; border:#898989 solid 1px; float:left;'>"+type_items[i][j]+"</div>";
                                                }
                                                content_style += "<div style='clear:both;'></div></div></div>";
                                                type_item_length++;
                                            }
                                            var str = type_stock.join("+");
                                            var content_total = eval(str);
                                            var content_stock = type_stock.join(",");
                                            
                                            content_style += "</div><div id='stock' style='display:none;margin-left:20px;color:#969B9C;font-size:8pt;' data-type='"+type_item_length+"' data-stock='"+content_stock+"' data-total='"+content_total+"' data-select='"+(default_status?"0；0；default；default":"")+"' data-select_stock='"+(default_status?content_total:"")+"'></div>";
                                            content_style += "<div>產品件數<input name='quantity' type='input' value='1'></div>";
                                            content_style += "<div><input id='confirm_sp' type='button' value='確定'></div>";
                                            $panel.html(content_style);
                                            $(".item").click(function() {
                                                
                                                var type_check = $(this).attr("data-select");
                                                var type_item = $(this).attr("data-item");

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
                                                        pick_specs[$(this).attr("data-item")] = [$(this).attr("data-val")];
                                                        pick_specs_index[$(this).attr("data-item")] = $.inArray($(this).attr("data-val"),type_items[$(this).attr("data-item")]);
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
                                                        pick_index = select;
                                                        pick_stock = type_stock[select];
                                                }
                                                else {
                                                        pick_index = 0;
                                                        pick_stock = $("#stock").attr("data-total");
                                                }
              
                                            });
                                            
                                            //選完 按確定 關閉
                                            $("#spec_panel").find("#confirm_sp").click(function(){
                                                //pick_stock目前選擇庫存量
                                                //pick_index目前庫存索引
                                                var pick_specs_index_str = "";
                                                for(var i in pick_specs_index)
                                                {
                                                    pick_specs_index_str += pick_specs_index[i]+"；";
                                                }
                                                var quantity = ($.isNumeric(parseInt($panel.find("input[name='quantity']").val()))?$panel.find("input[name='quantity']").val():1);
                                                var item = "<div fi_no='"+$this.attr("fi_no")+"' stock_index='"+pick_index+"' spec='"+JSON.stringify(pick_specs)+"' spec_index='"+pick_specs_index_str+"' quantity='"+quantity+"'  style='text-align:center;display:inline-block;margin:2px;position:relative;'><img src='../public/img/goods/"+$this.attr("images")+"' style='width:65px;height:65px;'><br/>"+quantity+"件<br/>id:"+$this.attr("fi_no")+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                                $("#combine_content").append(item);
                                                $panel.remove();
                                            });
                                            
                                        });
                                        
                                    }
                                    else
                                    {
                                        alert("最多10件組合商品！");
                                    }
                                });
                            })
                        });
                    }
                    $dialog.find("#combine").each(function(){
                        var $this = $(this);
                        var table = "<table class='table-v'>"+
                                    "<tr><td style='width:80px;height:130px;'>合併商品</td><td id='combine_content'></td></tr>"+
                                    "</table>";
                        var $table = $(table);
                        init_style($table);
                        $this.append($table);
                        var iframe = "<iframe id='combine_frame' src='iframe_product_combine.php' scrolling='no' style='width:824px;height:450px;border:1px solid gray;'></iframe>";
                        var $iframe = $(iframe);
                        $this.append($iframe);
                        iframeCombineOnload($iframe);
                    });

                    // 儲存
                    $dialog.find("input[name='product_add_save'],input[name='product_edit_save'],input[name='product_copy_save']").click(function(){
                        var p_status_shelves = $dialog.find("input[name='status_shelves']").prop("checked")?1:0;
                        var p_direct = $dialog.find("select[name='direct']").val();
                        var p_supplier = $dialog.find("select[name='supplier']").val();
                        var p_category = $dialog.find("select[name^='category']:last").val();
                        var p_category_class = [];
                        $dialog.find("select[name^='category'] option:checked").each(function(){
                            p_category_class.push($(this).text());
                        });
                        var p_category_a = p_category_class[0];
                        var p_category_b = p_category_class[1];
                        var p_category_c = p_category_class[2];
                        var p_brand = $dialog.find("select[name='brand']").val();
                        var p_product = $dialog.find("input[name='product']").val();
                        var p_name = $dialog.find("textarea[name='name']").val();
                        var p_import = $dialog.find("input[name='import']").val();
                        var p_price = $dialog.find("input[name='price']").val();
                        var p_promotions = $dialog.find("input[name='promotions']").val();
                        var p_discount = $dialog.find("input[name='discount']").val();
                        var p_free_gifts = $dialog.find("input[name='free_gifts']").prop("checked")?1:0;
                        var p_free_shipping = $dialog.find("input[name='free_shipping']").prop("checked")?1:0;
                        var p_other_price = JSON.stringify({
                            ntd:[
                                $dialog.find("input[name='twd_import']").val(),
                                $dialog.find("input[name='twd_price']").val(),
                                $dialog.find("input[name='twd_promotions']").val(),
                                $dialog.find("input[name='twd_discount']").val()
                            ]
                        });
                        var p_specification = "";
                        var p_inventory = $dialog.find("#product_class_inventory").html();
                        var p_depiction = $dialog.find("textarea[name='depiction']").val();
                        var p_introduction = $dialog.find(".jqte_editor_x").html();
                        var p_images = $dialog.find("#upload_image img");
                        var p_introductions = $dialog.find("#introduction_upload_image img");
                        var p_volumetric_weight = $dialog.find("#volumetric_weight p span");
                        var p_promotions_message = $dialog.find("input[name='promotions_message']").val();
                        var p_attribute = $dialog.find("#attribute");
                        var p_attribute_str = "";
                        var p_instructions = $dialog.find("textarea[name='instructions']").val();
                        var p_remark = $dialog.find("textarea[name='remark']").val();

                        p_import = p_import==""?0:p_import;
                        p_price = p_price==""?0:p_price;
                        p_promotions = p_promotions==""?0:p_promotions;
                        p_discount = p_discount==""?0:p_discount;
                        
                        /*@if(p_name == "" || p_price == "" || p_promotions=="" || p_discount=="" || p_depiction=="" || p_introduction=="" || p_promotions_message=="" ||p_instructions=="" || p_remark=="")
                        {
                            alert('欄位不能有空白！');
                            return;
                        }*/
                        if(p_name == "")
                        {
                            alert('商品名稱不能有空白！');
                            return;
                        }

                        /*@p_volumetric_weight.each(function(){
                            if($(this).find("input").val()=="")
                            {
                                alert('必要屬性欄位不能有空白！');
                                return;
                            }
                        });*/

                        /*@if(p_inventory=="")
                        {
                            alert("請建立至少一種規格與規格類別，並建立庫存列表！");
                            return;
                        }*/

                        if(p_images[0] == undefined)
                        {
                            alert('請至少上傳一張圖片！');
                            return;
                        }

                        // 商品圖
                        var len = p_images.size();
                        var i = 0;
                        //var p_image_src = [];
                        var p_image_filename = [];
                        var images_path = "../public/img/goods/";
                        var thumbnail_path = "../public/img/thumbnail/";
                        var minimize_path = "../public/img/minimize/";
                        if(len > 0)
                        {
                            for(i = 0; i < len; i++)
                            {
                                //p_image_src.push(p_images.eq(i).attr("src"));
                                p_image_filename.push(p_images.eq(i).attr("title"));
                            }
                        }
                        p_image_filename = p_image_filename.join("∵");
                        
                        // 描述用圖
                        len = p_introductions.size();
                        //var p_intoduction_src = [];

                        var p_intoduction_filename = [];
                        var introduction_images_path = "../public/img/introduction/";
                        if(len > 0)
                        {
                            for(i = 0; i < len; i++)
                            {
                                //p_intoduction_src.push(p_introductions.eq(i).attr("src"));
                                p_intoduction_filename.push(p_introductions.eq(i).attr("title"));
                            }
                        }
                        p_intoduction_filename = p_intoduction_filename.join("∵");

                        //商品規格類別
                        var $p_class = $dialog.find("#product_class");
                        var p_specification = [];
                        var p_specification_class = [];
                        i = -1;
                        $p_class.find("p input").each(function(){
                            var $this = $(this);
                            if($this.attr('name')=="specification" && $this.val()!="")
                            {
                                if($this.parent().find("ul")[0]==undefined)
                                {
                                    alert("規格請至少新增一項類別！");
                                    return;
                                }
                                p_specification.push($this.val());
                                p_specification_class.push([]);
                                i++;
                            }
                            if($this.attr('name')=="class" && $this.val()!="")
                            {
                                p_specification_class[i].push($this.val());
                            }
                        });
                        p_specification = p_specification.join("∵");
                        for(i in p_specification_class)
                        {
                            p_specification_class[i] = p_specification_class[i].join("∵");
                        }
                        p_specification_class = p_specification_class.join("@");
                        
                        var $inventory = $dialog.find("#product_class_inventory");
                        var p_inventory = [];
                        $inventory.find("table input").each(function(){
                            p_inventory.push($(this).val());
                        });
                        p_inventory = p_inventory.join("∵");

                        //必要屬性
                        var p_volumetric_weight_key = [];
                        var p_volumetric_weight_value = [];
                        p_volumetric_weight.each(function(){
                            p_volumetric_weight_key.push($.trim($(this).text()));
                            p_volumetric_weight_value.push($(this).find("input").val());
                        });
                        p_volumetric_weight_key = p_volumetric_weight_key.join("∵");
                        p_volumetric_weight_value = p_volumetric_weight_value.join("∵");

                        //額外屬性
                        p_attribute_str += "{";
                        p_attribute.find("p").each(function(){
                            var $this = $(this);
                            var fi_no = $this.attr("fi_no");
                            var type = $this.attr("type");
                            var str = "\""+fi_no+"\":[";
                            var v;
                            if(type == 0)
                            {
                                v = $this.find("select").val();
                                if(v == 0)return;
                                str+="\"";
                                str+=v;
                                str+="\",";
                            }
                            else if(type ==1)
                            {
                                v = ""; 
                                $this.find("input[type='checkbox']").each(function(){
                                    var $this = $(this);
                                    if($this.prop('checked'))
                                    {
                                        v+="\"";
                                        v+=$this.attr("name");
                                        v+="\",";
                                    }
                                });

                                if(v == "")return;
                                str+=v;
                            }
                            str+="\"\"],";
                            p_attribute_str += str;
                        });
                        p_attribute_str = p_attribute_str.slice(0,p_attribute_str.length-1);
                        p_attribute_str += "}";
                        if(p_attribute_str=="}")p_attribute_str="";
                        
                        // 商品詳細描述
                        $dialog.find("textarea[name='introduction']").val(p_introduction);
                        var absolute_path = introduction_images_path.replace("..",website_url);
                        $dialog.find(".jqte_editor_x img").each(function(){
                            var $this = $(this);
                            var src = $this.attr("src");
                            if(src.search(/data:/)!==-1)
                            {
                                src = $this.attr("title").split("_");
                                src = src[0]+"_"+src[1];
                                $("#introduction_upload_image img").each(function(){
                                    var $upload_img = $(this);
                                    if($upload_img.attr("title").search(src)!==-1)
                                    {
                                        $this.attr("src",absolute_path+$upload_img.attr("title"));
                                        $this.attr("title",$upload_img.attr("title"));
                                    }
                                })
                            }
                        });
                        p_introduction = $dialog.find(".jqte_editor_x").html();
                        $dialog.find(".jqte_editor_x").eq(0).html($dialog.find("textarea[name='introduction']").val())
                        //@@@p_introduction = p_introduction.replace(/\"/g,"\\\"");
                        p_introduction = p_introduction.replace(/http:\/\/www.crazy2go.com/g,"..");
                        p_introduction = p_introduction.replace(/\r/g,"<br>");
                        p_introduction = p_introduction.replace(/\n/g,"<br>");
                        
                        // 相關商品
                        var p_relate_news = [];
                        var p_relate_hots = [];
                        $("#news_content div").each(function(){
                            p_relate_news.push($(this).attr("fi_no"));
                        });
                        $("#hots_content div").each(function(){
                            p_relate_hots.push($(this).attr("fi_no"));
                        });
                        p_relate_news = p_relate_news.join("∵");
                        p_relate_hots = p_relate_hots.join("∵");
                        
                        // 組合商品
                        var p_combination = [];
                        var p_com_fi_no = [];
                        var p_com_inventory = [];
                        var p_com_spec_index = [];
                        var p_com_spec = [];
                        var p_com_quantity = [];
                        $("#combine_content div").each(function(){
                            p_com_fi_no.push($(this).attr("fi_no"));
                            p_com_inventory.push($(this).attr("stock_index"));
                            p_com_spec_index.push($(this).attr("spec_index"));
                            p_com_spec.push(JSON.parse($(this).attr("spec")));
                            p_com_quantity.push($(this).attr("quantity"));
                        });
                        if(p_com_fi_no[0])
                        p_combination.push({
                            fi_no:p_com_fi_no,
                            inventory:p_com_inventory,
                            specifications:p_com_spec_index,
                            quantity:p_com_quantity
                        });
                        var l = p_com_spec.length;
                        for(i = 0;i<l;i++)
                        {
                            p_combination.push(p_com_spec[i]);
                        }
                        p_combination = p_combination[0]?JSON.stringify(p_combination):"";

                        // 傳遞表格
                        var form_data = new FormData();
                        form_data.append("direct",p_direct);
                        form_data.append("status_shelves",p_status_shelves);
                        form_data.append("category",p_category);
                        form_data.append("brand",p_brand);
                        form_data.append("attribute",p_attribute_str);
                        form_data.append("product",p_product);
                        form_data.append("combination",p_combination);
                        form_data.append("name",p_name);
                        form_data.append("import",p_import);
                        form_data.append("price",p_price);
                        form_data.append("promotions",p_promotions);
                        form_data.append("discount",p_discount);
                        form_data.append("free_gifts",p_free_gifts);
                        form_data.append("free_shipping",p_free_shipping);
                        form_data.append("other_price",p_other_price);
                        form_data.append("specification",p_specification);
                        form_data.append("specification_class",p_specification_class);
                        form_data.append("inventory",p_inventory);
                        form_data.append("volumetric_weight_key",p_volumetric_weight_key);
                        form_data.append("volumetric_weight_value",p_volumetric_weight_value);
                        for(i in $dialog.data("file_images"))
                        form_data.append('file_images[]', $dialog.data("file_images")[i]);
                        form_data.append("images_filename",p_image_filename);
                        form_data.append("promotions_message",p_promotions_message);
                        form_data.append("depiction",p_depiction);
                        form_data.append("introduction",p_introduction);
                        for(i in $dialog.data("file_introduction"))
                        form_data.append('file_introduction[]', $dialog.data("file_introduction")[i]);
                        form_data.append("introduction_images_filename",p_intoduction_filename);
                        form_data.append("instructions",p_instructions);
                        form_data.append("supplier",p_supplier);
                        form_data.append("remark",p_remark);
                        form_data.append("relate_news",p_relate_news);
                        form_data.append("relate_hots",p_relate_hots);
                        form_data.append("category_a",p_category_a);
                        form_data.append("category_b",p_category_b);
                        form_data.append("category_c",p_category_c);

                        if($(this).attr("name") == "product_add_save")
                        {
                            form_data.append("query_type","product_add");
                        }
                        
                        if($(this).attr("name") == "product_edit_save")
                        {
                            form_data.append("query_type","product_edit");
                            form_data.append("fi_no",$dialog.data("fi_no"));
                        }
                        
                        if($(this).attr("name") == "product_copy_save")
                        {
                            form_data.append("query_type","product_copy");
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
                                console.log(data);
                                alert($.trim(data));
                                location.href = location.href;
                            }
                        });
                        
                    });

                    // 上傳商品圖片
                    $dialog.find("input[name='images']").change(function(evt) {
                        $(this).after($(this).clone(true));
                        $(this).remove();
                        $dialog.data("file_images",$dialog.data("file_images")||[]);
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
                                    $dialog.find("#upload_image").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                    $dialog.find("#upload_image img:last")[0].onload = function(){
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
                                        theFile.fname = filename;
                                        $dialog.data("file_images").push(theFile);
                                    }
                                };
                            })(f);
                            reader.readAsDataURL(f);
                        }
                    });

                    // 上傳描述用圖片
                    $dialog.find("input[name='introduction']").change(function(evt) {
                        $(this).after($(this).clone(true));
                        $(this).remove();
                        $dialog.data("file_introduction",$dialog.data("file_introduction")||[]);
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
                                    $dialog.find("#introduction_upload_image").append('<img class="thumb" src="'+e.target.result+'" title="'+escape(theFile.name)+'"/>');
                                    $dialog.find("#introduction_upload_image img:last")[0].onload = function(){
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
                                                if($dialog.data("file_introduction")[i].fname == $(this).attr("fname"))
                                                {
                                                    $dialog.data("file_introduction").splice(i,1);
                                                    break;
                                                }
                                            }
                                            $this.parent().remove();
                                        });
                                        theFile.fname = filename;
                                        $dialog.data("file_introduction").push(theFile);
                                    }
                                };
                            })(f);
                            reader.readAsDataURL(f);
                        }
                    });
                    
                    if($dialog.has("input[name='product_edit_save']").length || $dialog.has("input[name='product_copy_save']").length){                       
                        // 填表
                        $.post(filename,{
                            query_type:"get_product_detail",
                            fi_no:$dialog.data("fi_no")
                        },function(data){
                            data = data.split("`");
                            //資料
                            var p_fi_no = data[0];
                            var p_category= data[1];
                            var p_brand = data[2];
                            var p_attribute = data[3]==""?{}:$.parseJSON(data[3]);
                            var p_name = data[4];
                            var p_depiction = data[5];
                            var p_price = data[6];
                            var p_promotions = data[7];
                            var p_specification = data[8]==""?{}:$.parseJSON(data[8]);
                            var p_inventory = data[9]==""?[]:$.parseJSON(data[9]);
                            var p_volumetric_weight = data[10]==""?{}:$.parseJSON(data[10]);
                            var p_images = data[11]==""?[]:$.parseJSON(data[11]);
                            var p_promotions_message = data[12];
                            var p_introduction = data[13];
                            var p_introduction_images = data[14]==""?[]:$.parseJSON(data[14]);
                            var p_discount = data[15];
                            var p_free_gifts = data[16];
                            var p_free_shipping = data[17];
                            var p_instructions = data[18];
                            var p_remark = data[19];
                            var p_status_shelves = data[20];
                            var p_supplier = data[21];
                            var p_related = data[22]==""?{}:$.parseJSON(data[22]);
                            var p_import = data[23];
                            var p_direct = data[24];
                            var p_product = data[25];
                            var p_other_price = data[26]==""?{}:$.parseJSON(data[26]);
                            var p_combination = data[27]==""?[]:$.parseJSON(data[27]);

                            $dialog.find("input[name='status_shelves']").prop("checked",parseInt(p_status_shelves));
                            $dialog.find("select[name='direct']").val(p_direct);
                            $dialog.find("textarea[name='name']").val(p_name);
                            $dialog.find("input[name='product']").val(p_product);
                            $dialog.find("textarea[name='depiction']").val(p_depiction);
                            $dialog.find("input[name='import']").val(p_import);
                            $dialog.find("input[name='price']").val(p_price);
                            $dialog.find("input[name='promotions']").val(p_promotions);
                            $dialog.find("input[name='discount']").val(p_discount);
                            $dialog.find("input[name='free_gifts']").prop("checked",parseInt(p_free_gifts));
                            $dialog.find("input[name='free_shipping']").prop("checked",parseInt(p_free_shipping));
                            if(p_other_price.ntd)
                            {
                                $dialog.find("input[name='twd_import']").val(p_other_price.ntd[0]);
                                $dialog.find("input[name='twd_price']").val(p_other_price.ntd[1]);
                                $dialog.find("input[name='twd_promotions']").val(p_other_price.ntd[2]);
                                $dialog.find("input[name='twd_discount']").val(p_other_price.ntd[3]);
                            }
                            $dialog.find("input[name='promotions_message']").val(p_promotions_message);
                            $dialog.find("select[name='supplier']").val(p_supplier);

                            //分類
                            $dialog.find("#category").html("");
                            var cur_category = p_category;
                            var level_index = 0;
                            while(cur_category)
                            {
                                $dialog.find("#category").prepend("<select name='category"+level_index+"'><select>");
                                var set_category = cur_category;
                                cur_category = parseInt($("select[name='categoryall'] option[value="+cur_category+"]").attr("index"));
                                $dialog.find("select[name^='category"+level_index+"']").append($("select[name='categoryall'] option[index="+cur_category+"]").clone());
                                $dialog.find("select[name^='category"+level_index+"']").val(set_category);
                                level_index++;
                            }
                            $dialog.find("select[name^='category']:last").trigger("change");
                            
                            //品牌
                            if($dialog.find("select[name='brand'] option[value="+p_brand+"]")[0])
                            $dialog.find("select[name='brand']").val(p_brand);
                            
                            //庫存
                            $dialog.find("#product_class").each(function(){
                                var $this = $(this);
                                for(var i in p_specification)
                                {
                                    $this.find("input[name='specification']:last").val(i);
                                    var p = $this.find("p:last");
                                    p.find("span").trigger('click');
                                    var len = p_specification[i].length;
                                    for(var j=0;j<len;j++)
                                    {
                                        p.find("input[name='class']:last").val(p_specification[i][j]);
                                        p.find("ul li p:last span").trigger('click');
                                    }
            
                                }
                            });
                            $dialog.find("button").trigger('click');
                            $dialog.find("#product_class_inventory").each(function(){
                                var $this = $(this);
                                $this.find("input").each(function(index){
                                    $(this).val(p_inventory[index]);
                                });
                            });

                            //必要屬性
                            var volumetric_weight = [];
                            for(i in p_volumetric_weight)
                            {
                                volumetric_weight.push(p_volumetric_weight[i]);
                            }
                            $dialog.find("#volumetric_weight p span input").each(function(index){
                                $(this).val(volumetric_weight[index]);
                            });

                            //額外屬性
                            for(i in p_attribute)
                            {
                                var p = $dialog.find("#attribute p[fi_no="+i+"]");
                                if(p.attr("type") == "0")
                                {
                                    p.find("select").val(p_attribute[i][0]);
                                }
                                else if(p.attr("type") == "1")
                                {
                                    for(var j in p_attribute[i])
                                    {
                                        if(p_attribute[i][j]=="")continue;
                                        p.find("input[name="+p_attribute[i][j]+"]").prop('checked',1);
                                    }
                                }
                            }

                            //上傳圖片
                            var i = 0;
                            var len = 0;
                            if((len = p_images.length) > 0)
                            {
                                for(i = 0; i<len;i++)
                                {
                                    $dialog.find("#upload_image").append('<img class="thumb" src="../public/img/goods/'+p_images[i]+'" title="'+p_images[i]+'"/>');
                                    $dialog.find("#upload_image img:last")[0].onload = function(){
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
                            if((len = p_introduction_images.length) > 0)
                            {
                                for(i = 0; i<len;i++)
                                {
                                    $dialog.find("#introduction_upload_image").append('<img class="thumb" src="../public/img/introduction/'+p_introduction_images[i]+'" title="'+p_introduction_images[i]+'"/>');
                                    $dialog.find("#introduction_upload_image img:last")[0].onload = function(){
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
                            
                            //相關商品
                            var item;
                            var product_array = [];
                            var news_len = p_related.news?p_related.news.length:0;
                            var hots_len = p_related.hots?p_related.hots.length:0;
                            for(i=0;i<news_len;i++)
                            {
                                item = "<div fi_no='"+p_related.news[i]+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/' style='width:65px;height:65px;'><br/>id:"+p_related.news[i]+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                $("#news_content").append(item);
                                product_array.push(p_related.news[i]);
                            }
                            for(i=0;i<hots_len;i++)
                            {
                                item = "<div fi_no='"+p_related.hots[i]+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/' style='width:65px;height:65px;'><br/>id:"+p_related.hots[i]+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                $("#hots_content").append(item);
                                product_array.push(p_related.hots[i]);
                            }
                            product_array = $.unique(product_array).join(",");
                            
                            var $iframe = $dialog.find("iframe[id='relate_frame']");
                            var og_src = $iframe.attr("src");
                            $iframe.attr("src",og_src+"?type=0&keyword="+product_array);
                            $iframe.bind("load",function(){
                                $iframe.unbind("load");
                                $iframe.contents().find("#product_list_table tr").each(function(){
                                    var fi_no = $(this).attr("fi_no")
                                    var images = $(this).attr("images")
                                    $("#news_content div").each(function(){
                                        if($(this).attr("fi_no")==fi_no){
                                            $(this).find("img").attr("src",$(this).find("img").attr("src")+images);
                                        }
                                    });
                                    $("#hots_content div").each(function(){
                                        if($(this).attr("fi_no")==fi_no){
                                            $(this).find("img").attr("src",$(this).find("img").attr("src")+images);
                                        }
                                    })
                                });
                                $iframe.attr("src",og_src);
                                iframeOnload($iframe);
                            });
                            
                            //組合商品
                            item = "";
                            product_array = [];
                            var com_len = p_combination[0]?p_combination[0].fi_no.length:0;
                            for(i=0;i<com_len;i++)
                            {
                                item = "<div fi_no='"+p_combination[0].fi_no[i]+"' stock_index='"+p_combination[0].inventory[i]+"' spec_index='"+p_combination[0].specifications[i]+"' spec='"+JSON.stringify(p_combination[i+1])+"' quantity='"+p_combination[0].quantity[i]+"' style='text-align:center;display:inline-block;margin:2px;'><img src='../public/img/goods/' style='width:65px;height:65px;'><br/>"+p_combination[0].quantity[i]+"件<br/>id:"+p_combination[0].fi_no[i]+"<br/><br/><span class='bg shadowRoundCorner' style='padding:3px;cursor:pointer;color:white;' onclick='$(this).parent().remove();'>delete</span></div>";
                                $("#combine_content").append(item);
                                product_array.push(p_combination[0].fi_no[i]);
                            }
                            product_array = $.unique(product_array).join(",");
                            var $iframe2 = $dialog.find("iframe[id='combine_frame']");
                            var og_src2 = $iframe2.attr("src");
                            $iframe2.attr("src",og_src2+"?type=0&keyword="+product_array);
                            $iframe2.bind("load",function(){
                                $iframe2.unbind("load");
                                $iframe2.contents().find("#product_list_table tr").each(function(){
                                    var fi_no = $(this).attr("fi_no")
                                    var images = $(this).attr("images")
                                    $("#combine_content div").each(function(){
                                        if($(this).attr("fi_no")==fi_no){
                                            $(this).find("img").attr("src",$(this).find("img").attr("src")+images);
                                        }
                                    });
                                });
                                $iframe2.attr("src",og_src2);
                                iframeCombineOnload($iframe2);
                            });

                            $dialog.find("textarea[name='introduction']").jqteVal(p_introduction);
                            $dialog.find("textarea[name='instructions']").jqteVal(p_instructions);
                            $dialog.find("textarea[name='remark']").jqteVal(p_remark);
                            $dialog.find(".jqte_editor_x").eq(0).html(p_introduction);
                            $dialog.find(".jqte_editor_x").eq(1).html(p_instructions);
                            $dialog.find(".jqte_editor_x").eq(2).html(p_remark);
                            $dialog.find(".jqte_editor_x img").bind("dblclick",imageResize);
                        });
                    }
                });
                
                //搜尋
                $("#product_search input[name='search']").click(function(){
                    var type = $("#product_search select[name='type']").val();
                    var keyword = $("#product_search input[name='keyword']").val();
                    location.href = location.href.split("?")[0] + "?type=" + type + "&keyword=" + keyword;
                });
                $("#product_search input[name='keyword']").keypress(function(e){
                    if(e.keyCode==13)
                    {
                        $("#product_search input[name='search']").trigger("click");
                    }
                });
            });
            
            function imageResize(){
                var $this = $(this);
                var $dialog = $("#dialog_content");
                $dialog.find("textarea[name='instructions']").jqteVal($dialog.find("textarea[name='instructions']").val());
                $dialog.find("textarea[name='remark']").jqteVal($dialog.find("textarea[name='remark']").val());
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
                        //所以分類
                        $len = count($all_product_type);
                        echo "<select name='categoryall' style='display:none;'>";
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<option value='".$all_product_type[$i]['fi_no']."' index='".$all_product_type[$i]['index']."'>".$all_product_type[$i]['name']."</option>";
                        }
                        echo "</select>";

                        //所有品牌
                        $len = count($all_product_brand);
                        echo "<select name='brandall' style='display:none;'>";
                        for($i = 0; $i < $len; $i++)
                            echo "<option value='".$all_product_brand[$i]['fi_no']."' category='".$all_product_brand[$i]['category']."'>".$all_product_brand[$i]['name']."</option>";
                        echo "</select>";
                        
                        //所有屬性
                        echo "<select name='attribute' style='display:none;'>";
                            $len = count($all_attribute);
                            for($i=0;$i<$len;$i++)
                            {
                                echo "<option fi_no='".$all_attribute[$i]['fi_no']."' category='".$all_attribute[$i]['category']."' name='".$all_attribute[$i]['name']."' type='".$all_attribute[$i]['type']."' require='".$all_attribute[$i]['required']."'></option>";
                            }
                        echo "</select>";
                        echo "<select name='attribute_item' style='display:none;'>";
                            $len = count($all_attribute_item);
                            for($i=0;$i<$len;$i++)
                            {
                                echo "<option fi_no='".$all_attribute_item[$i]['fi_no']."' attribute='".$all_attribute_item[$i]['attribute']."' item='".$all_attribute_item[$i]['item']."'></option>";
                            }
                        echo "</select>";
                        //所有供應商
                        $len = count($all_supplier);
                        echo "<select name='supplierall' style='display:none;'>";
                        echo "<option value='0'>無</option>";
                        for($i = 0; $i < $len; $i++)
                            echo "<option value='".$all_supplier[$i]['fi_no']."' name='".$all_supplier[$i]['name']."'>".$all_supplier[$i]['name']."</option>";
                        echo "</select>";

                        //搜尋
                        echo "<div id='product_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>商品ID</option><option value='2'>商品名稱</option><option value='3'>商品分類</option><option value='4'>商品品牌</option><option value='5'>供應商</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";
                        
                        //新增
                        if($display_add)
                        {
                            echo "<table id='product_add_table' class='table-v' data-dialog='新增商品'>";
                            echo "<tr><td style='width:105px;'>上架</td><td><input type='checkbox' name='status_shelves' /></td></tr>";
                            echo "<tr><td>採購出貨</td><td><select name='direct'><option value='1'>台灣貿易</option><option value='2'>深圳貿易</option><option value='3'>台灣宏廣直郵</option><option value='4'>台灣廠商直郵</option><option value='5'>大陸廠商直送</option></select></td></tr>";
                            echo "<tr><td>供應商</td><td><select name='supplier'></select> (<input name='keyword' type='text' placeholder='請輸入供應商關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'>)</td></tr>";
                            echo "<tr><td>分類</td><td id='category'></td></tr>";
                            echo "<tr><td>品牌</td><td><select name='brand'></select></td></tr>";
                            echo "<tr><td>品名</td><td><input type='text' name='product' /></td></tr>";
                            echo "<tr><td>標題</td><td><textarea name='name' style='height:100px;'></textarea></td></tr>";
                            echo "<tr><td>進價</td><td><input type='text' name='import' /></td></tr>";
                            echo "<tr><td>市價</td><td><input type='text' name='price' /></td></tr>";
                            echo "<tr><td>促銷價</td><td><input type='text' name='promotions' /></td></tr>";
                            echo "<tr><td>折扣價</td><td><input type='text' name='discount' />&nbsp;送贈品<input type='checkbox' name='free_gifts' />&nbsp;免運費<input type='checkbox' name='free_shipping' /></td></td></tr>";
                            echo "<tr><td>其他價格</td><td>新台幣 進價<input type='text' name='twd_import'/> 市價<input type='text' name='twd_price'/> 促銷價<input type='text' name='twd_promotions'/> 折扣價<input type='text' name='twd_discount'/></td></tr>";
                            echo "<tr><td>庫存</td><td><p>(規格可為顏色,大小,或容量.規格類別可為紅,藍,綠.M,L,XL.50ml,100ml,150ml)</p><div id='product_class'></div><div id='product_class_inventory'></div></td></tr>";
                            echo "<tr><td>必要屬性</td><td><div id='volumetric_weight'><p><span>長 <input type='text'></span> cm</p><p><span>寬 <input type='text'></span> cm</p><p><span>高 <input type='text'></span> cm</p><p><span>重量 <input type='text'></span> kg</p></div></td></tr>";
                            echo "<tr><td>額外屬性</td><td><div id='attribute'></div></td></tr>";
                            echo "<tr><td>商品圖片</td><td><div id='upload_image'></div><input type='file' name='images' multiple='multiple' /></td></tr>";
                            echo "<tr><td>短描述</td><td><textarea name='depiction' style='height:100px;width:609px;'></textarea></td></tr>";
                            echo "<tr><td>促銷訊息</td><td><input type='text' name='promotions_message' style='width:609px;' /></td></tr>";
                            echo "<tr><td>完整描述</td><td><textarea name='introduction'></textarea></td></tr>";
                            echo "<tr><td>完整描述圖片</td><td><div id='introduction_upload_image'></div><input type='file' name='introduction' multiple='multiple' /></td></tr>";
                            echo "<tr><td>使用說明/售後保固</td><td><textarea name='instructions'></textarea></td></tr>";
                            echo "<tr><td>備註</td><td><textarea name='remark'></textarea></td></tr>";
                            echo "<tr><td>相關商品</td><td><div id='relate'></div></td></tr>";
                            echo "<tr><td>組合商品</td><td><div id='combine'></div></td></tr>";
                            echo "<tr><td></td><td><input name='product_add_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                            echo "<input id='product_add_btn' type='button' value='新增商品' data-open-dialog='新增商品' /><table></table>";                            
                        }
                        
                        //複製
                        if($display_copy)
                        {
                            echo "<table id='product_copy_table' class='table-v' data-dialog='複製商品'>";
                            echo "<tr><td style='width:105px;'>上架</td><td><input type='checkbox' name='status_shelves' /></td></tr>";
                            echo "<tr><td>採購出貨</td><td><select name='direct'><option value='1'>台灣貿易</option><option value='2'>深圳貿易</option><option value='3'>台灣宏廣直郵</option><option value='4'>台灣廠商直郵</option><option value='5'>大陸廠商直送</option></select></td></tr>";
                            echo "<tr><td>供應商</td><td><select name='supplier'></select> (<input name='keyword' type='text' placeholder='請輸入供應商關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'>)</td></tr>";
                            echo "<tr><td>分類</td><td id='category'></td></tr>";
                            echo "<tr><td>品牌</td><td><select name='brand'></select></td></tr>";
                            echo "<tr><td>品名</td><td><input type='text' name='product' /></td></tr>";
                            echo "<tr><td>標題</td><td><textarea name='name' style='height:100px;'></textarea></td></tr>";
                            echo "<tr><td>進價</td><td><input type='text' name='import' /></td></tr>";
                            echo "<tr><td>市價</td><td><input type='text' name='price' /></td></tr>";
                            echo "<tr><td>促銷價</td><td><input type='text' name='promotions' /></td></tr>";
                            echo "<tr><td>折扣價</td><td><input type='text' name='discount' />&nbsp;送贈品<input type='checkbox' name='free_gifts' />&nbsp;免運費<input type='checkbox' name='free_shipping' /></td></td></tr>";
                            echo "<tr><td>其他價格</td><td>新台幣 進價<input type='text' name='twd_import'/> 市價<input type='text' name='twd_price'/> 促銷價<input type='text' name='twd_promotions'/> 折扣價<input type='text' name='twd_discount'/></td></tr>";
                            echo "<tr><td>庫存</td><td><p>(規格可為顏色,大小,或容量.規格類別可為紅,藍,綠.M,L,XL.50ml,100ml,150ml)</p><div id='product_class'></div><div id='product_class_inventory'></div></td></tr>";
                            echo "<tr><td>必要屬性</td><td><div id='volumetric_weight'><p><span>長 <input type='text'></span> cm</p><p><span>寬 <input type='text'></span> cm</p><p><span>高 <input type='text'></span> cm</p><p><span>重量 <input type='text'></span> kg</p></div></td></tr>";
                            echo "<tr><td>額外屬性</td><td><div id='attribute'></div></td></tr>";
                            echo "<tr><td>商品圖片</td><td><div id='upload_image'></div><input type='file' name='images' multiple='multiple' /></td></tr>";
                            echo "<tr><td>短描述</td><td><textarea name='depiction' style='height:100px;width:609px;'></textarea></td></tr>";
                            echo "<tr><td>促銷訊息</td><td><input type='text' name='promotions_message' style='width:609px;' /></td></tr>";
                            echo "<tr><td>完整描述</td><td><textarea name='introduction'></textarea></td></tr>";
                            echo "<tr><td>完整描述圖片</td><td><div id='introduction_upload_image'></div><input type='file' name='introduction' multiple='multiple' /></td></tr>";
                            echo "<tr><td>使用說明/售後保固</td><td><textarea name='instructions'></textarea></td></tr>";
                            echo "<tr><td>備註</td><td><textarea name='remark'></textarea></td></tr>";
                            echo "<tr><td>相關商品</td><td><div id='relate'></div></td></tr>";
                            echo "<tr><td>組合商品</td><td><div id='combine'></div></td></tr>";
                            echo "<tr><td></td><td><input name='product_copy_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                        
                        //編輯
                        if($display_edit)
                        {
                            echo "<table id='product_edit_table' class='table-v' data-dialog='編輯商品'>";
                            echo "<tr><td style='width:105px;'>上架</td><td><input type='checkbox' name='status_shelves' /></td></tr>";
                            echo "<tr><td>採購出貨</td><td><select name='direct'><option value='1'>台灣貿易</option><option value='2'>深圳貿易</option><option value='3'>台灣宏廣直郵</option><option value='4'>台灣廠商直郵</option><option value='5'>大陸廠商直送</option></select></td></tr>";
                            echo "<tr><td>供應商</td><td><select name='supplier'></select></td></tr>";
                            echo "<tr><td>分類</td><td id='category'></td></tr>";
                            echo "<tr><td>品牌</td><td><select name='brand'></select></td></tr>";
                            echo "<tr><td>品名</td><td><input type='text' name='product' /></td></tr>";
                            echo "<tr><td>標題</td><td><textarea name='name' style='height:100px;'></textarea></td></tr>";
                            echo "<tr><td>進價</td><td><input type='text' name='import' /></td></tr>";
                            echo "<tr><td>市價</td><td><input type='text' name='price' /></td></tr>";
                            echo "<tr><td>促銷價</td><td><input type='text' name='promotions' /></td></tr>";
                            echo "<tr><td>折扣價</td><td><input type='text' name='discount' />&nbsp;送贈品<input type='checkbox' name='free_gifts' />&nbsp;免運費<input type='checkbox' name='free_shipping' /></td></td></tr>";
                            echo "<tr><td>其他價格</td><td>新台幣 進價<input type='text' name='twd_import'/> 市價<input type='text' name='twd_price'/> 促銷價<input type='text' name='twd_promotions'/> 折扣價<input type='text' name='twd_discount'/></td></tr>";
                            echo "<tr><td>庫存</td><td><p>(規格可為顏色,大小,或容量.規格類別可為紅,藍,綠.M,L,XL.50ml,100ml,150ml)</p><div id='product_class'></div><div id='product_class_inventory'></div></td></tr>";
                            echo "<tr><td>必要屬性</td><td><div id='volumetric_weight'><p><span>長 <input type='text'></span> cm</p><p><span>寬 <input type='text'></span> cm</p><p><span>高 <input type='text'></span> cm</p><p><span>重量 <input type='text'></span> kg</p></div></td></tr>";
                            echo "<tr><td>額外屬性</td><td><div id='attribute'></div></td></tr>";
                            echo "<tr><td>商品圖片</td><td><div id='upload_image'></div><input type='file' name='images' multiple='multiple' /></td></tr>";
                            echo "<tr><td>短描述</td><td><textarea name='depiction' style='height:100px;width:609px;'></textarea></td></tr>";
                            echo "<tr><td>促銷訊息</td><td><input type='text' name='promotions_message' style='width:609px;' /></td></tr>";
                            echo "<tr><td>完整描述</td><td><textarea name='introduction'></textarea></td></tr>";
                            echo "<tr><td>完整描述圖片</td><td><div id='introduction_upload_image'></div><input type='file' name='introduction' multiple='multiple' /></td></tr>";
                            echo "<tr><td>使用說明/售後保固</td><td><textarea name='instructions'></textarea></td></tr>";
                            echo "<tr><td>備註</td><td><textarea name='remark'></textarea></td></tr>";
                            echo "<tr><td>相關商品</td><td><div id='relate'></div></td></tr>";
                            echo "<tr><td>組合商品</td><td><div id='combine'></div></td></tr>";
                            echo "<tr><td></td><td><input name='product_edit_save' type='button' value='儲存' /></td></tr>";
                            echo "</table>";
                        }
                    
                        //列表
                        $pager->display();echo "<br/>";
                        echo "<table id='product_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td>圖片</td><td>標題</td><td>分類</td><td>品牌</td><td name='promotions'>價格</td><td>審核狀態</td><td>上架狀態</td><td>供應商</td><td>預覽</td>";
                        if($display_edit) echo "<td>編輯</td>";
                        if($display_copy) echo "<td>複製</td>";
                        if($display_delete) echo"<td>刪除</td>";
                        echo "</tr>";

                        $len = count($all_product);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$all_product[$i]["fi_no"]."' name='".$all_product[$i]["name"]."'>";
                            echo "<td>".$all_product[$i]["fi_no"]."</td>";
                            echo "<td>"."<img src='../public/img/goods/".$all_product[$i]['images'][0]."' width=100 height=100>"."</td>";
                            echo "<td style='width:100px;'>".$all_product[$i]["name"]."</td>";
                            echo "<td class='cateList' style='width:110px;text-align:left;'>".$all_product[$i]["category"]."</td>";
                            echo "<td class='brandList'>".$all_product[$i]["brand"]."</td>";
                            echo "<td>".($all_product[$i]["discount"]!=0?$all_product[$i]["discount"]:$all_product[$i]["promotions"])."</td>";
                            echo "<td>".($all_product[$i]["status_audit"]==1?"是":"否")."</td>";
                            echo "<td>".($all_product[$i]["status_shelves"]==1?"是":"否")."</td>";
                            echo "<td class='supplierList'>".$all_product[$i]["supplier"]."</td>";
                            echo "<td>"."<a href='http://www.crazy2go.com/goods?no=".$all_product[$i]["fi_no"]."' target='_blank'>檢視</a>"."</td>";
                            if($display_edit)echo "<td><input class='product_edit' type='button' value='編輯' data-open-dialog='編輯商品' /></td>";
                            if($display_copy) echo "<td><input class='product_copy' type='button' value='複製' data-open-dialog='複製商品' /></td>";
                            if($display_delete)echo "<td><input class='product_del' type='button' value='刪除' /></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        $pager->display();
                    ?>
                </div>
                <div id="image_choose_panel" class='shadowRoundCorner' style='display:inline-block;padding:5px;background:rgba(255,255,255,0.9);border:3px dashed #CCC;width:778px;'>
                    <div style='text-align:center;color:white;padding:5px;margin-bottom:5px;' class='bg shadowRoundCorner'>
                        <span>選擇貼圖</span>
                        <span style='float:right;cursor:pointer;' onclick="$(this).parent().parent().remove();">ｘ</span>
                    </div>
                    <div id="content">
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

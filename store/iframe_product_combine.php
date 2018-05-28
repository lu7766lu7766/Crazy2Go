<?php 
    //跟manage_product差異
    //所有商品搜尋加上條件combination='',因為組能商品不能再組合商品
    //多<input class='add_combine' type='button' value='加入組合'..按鈕
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
        if(isset($_GET["oname"]))$_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by category asc, fi_no asc";
        $query = "select * from goods_index where combination='' and store=".$login_store." and `delete`=0 ".$orderby;
        if(isset($_GET['keyword']))
        {
            switch($_GET['type'])
            {
                case '0':
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from goods_index where combination='' and  store=".$login_store." and `delete`=0 and fi_no in(".$_GET['keyword'].") ".$orderby;
                    break;
                case '1'://商品ID
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from goods_index where combination='' and  store=".$login_store." and `delete`=0 and fi_no >= ".$_GET['keyword']." ".$orderby;
                    break;
                case '2'://商品關鍵字
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by category asc, fi_no asc";
                    $query = "select * from goods_index where combination='' and  store=".$login_store." and `delete`=0 and name like '%".$_GET['keyword']."%' ".$orderby;
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
                    $query = "select * from goods_index where combination='' and  store=".$login_store." and `delete`=0 and category in (".$ids.") ".$orderby;
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
                    $query = "select * from goods_index where combination='' and  store=".$login_store." and `delete`=0 and brand in (".$ids.") ".$orderby;
                    break;
                case '5'://供應商
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from goods_index where combination='' and  store=".$login_store." and `delete`=0 and supplier like '%".$_GET['keyword']."%' ".$orderby;
                    break;
                case '6'://所有
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
                discount => $result[$i]['discount'],
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
                $("#header").remove();
                $("#body").removeAttr("style");
                $("#footer").remove();
                $("#body").unwrap();
                $("#body").children().unwrap();
                $("body").css({"background":"#CCC url()"});
                var $tr_first = $("#product_list_table tr:first").clone();
                $("#product_list_table_title").css({"margin-bottom":"0px"});
                $("#product_list_table_title").append($tr_first);
                $("#product_list_table tr:first").remove();
                
                //列表排序
                $("#product_list_table_title tr:first td[name]").each(function(){
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
                    $this.text('');
                    var $cur_brand = $("select[name='brandall'] option[value="+cur_brand+"]");
                    $this.prepend("<span>"+$cur_brand.text()+"</span>");
                });
                
                //搜尋
                $("#product_search input[name='search']").click(function(){
                    var type = $("#product_search select[name='type']").val();
                    var keyword = $("#product_search input[name='keyword']").val();
                    location.href = location.href.split("?")[0] + "?type=" + type + "&keyword=" + keyword;
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
                <h3><?php echo $html_title."-".$login_store_name; ?></h3>
            </div>
            <!-- /.header -->
            <!-- ******************** body ******************** -->
            <div id="body">
                <?php
                    //所有分類
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
                
                    //搜尋
                    echo "<div id='product_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>商品ID</option><option value='2'>商品名稱</option><option value='3'>商品分類</option><option value='4'>商品品牌</option><option value='5'>供應商</option><option value='6'>所有</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";

                    //列表
                    $pager->display();echo "<br/>";
                    echo "<table id='product_list_table_title' class='table-h'>";
                    echo "</table>";
                    echo "<div style='overflow-y:scroll;height:350px;'>";
                    echo "<table id='product_list_table' class='table-h'>";
                    echo "<tr><td name='fi_no' style='width:50px;'>ID</td><td style='width:80px;'>圖片</td><td style='width:80px;'>品名</td><td style='width:80px;'>分類</td><td style='width:80px;'>品牌</td><td name='promotions' style='width:50px;'>價格</td><td style='width:80px;'>供應商</td><td style='width:50px;'>預覽</td><td style='width:80px;'>加入相關</td>";
                    echo "</tr>";
                    $len = count($all_product);
                    for($i = 0; $i < $len; $i++)
                    {
                        echo "<tr fi_no='".$all_product[$i]["fi_no"]."' name='".$all_product[$i]["name"]."' images='".$all_product[$i]['images'][0]."'>";
                        echo "<td style='width:50px;'>".$all_product[$i]["fi_no"]."</td>";
                        echo "<td style='width:80px;'>"."<img src='../public/img/goods/".$all_product[$i]['images'][0]."' width=100 height=100>"."</td>";
                        echo "<td style='width:80px;'>".$all_product[$i]["name"]."</td>";
                        echo "<td class='cateList' style='width:80px;text-align:left;'>".$all_product[$i]["category"]."</td>";
                        echo "<td class='brandList' style='width:80px;'>".$all_product[$i]["brand"]."</td>";
                        echo "<td style='width:50px;'>".($all_product[$i]["discount"]!=0?$all_product[$i]["discount"]:$all_product[$i]["promotions"])."</td>";
                        echo "<td style='width:80px;'>".$all_product[$i]["supplier"]."</td>";
                        echo "<td style='width:50px;'>"."<a href='http://www.crazy2go.com/goods?no=".$all_product[$i]["fi_no"]."' target='_blank'>檢視</a>"."</td>";
                        echo "<td style='width:80px;'><input class='add_combine' type='button' value='加入組合' style='height:30px;border:1px solid gray;background:white;'></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div>";
                    //$pager->display();
                ?>
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

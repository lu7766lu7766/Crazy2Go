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

        //存員工資料
        if(isset($_GET["oname"]))
        {
            $_GET["oname"] = str_replace("∵", "_", $_GET["oname"]);
            $_GET["oname"] = str_replace("\\_", "_", $_GET["oname"]);
        }
        $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
        $query = "select * from order_index where store=".$login_store." ".$orderby;
        if(isset($_GET['keyword']))
        {
            switch($_GET['type'])
            {
                case '1'://會員ID
                    $query = "select fi_no from member_index where id='".$_GET['keyword']."'";
                    $result = $dba->query($query);
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from order_index where member='".$result[0]["fi_no"]."' and store=".$login_store." ".$orderby;
                    break;
                case '2'://訂單序號
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from order_index where sn='".$_GET['keyword']."' and store=".$login_store." ".$orderby;
                    break;
                case '3'://建立日期
                    $orderby = isset($_GET["oname"])?"order by ".$_GET["oname"]." ".$_GET["order"]:"order by fi_no asc";
                    $query = "select * from order_index where date like '%".$_GET['keyword']."%' and store=".$login_store." ".$orderby;
                    break;
            }
        }

        include '../backend/class_pager2.php';
        $pager = new Pager();
        $result = $pager->query($query);
        $len = count($result);
        $all_order = array();
        
        //填入所有訂單資料
        for($i = 0; $i < $len; $i++)
        {
            if( $result[$i]['application_returns'] > 0 || 
                $result[$i]['application_exchanges'] > 0 ||
                $result[$i]['application_rework'] > 0
            ){continue;}
            $all_order[]=array(
                fi_no => $result[$i]['fi_no'],
                member => $result[$i]['member'],
                sn => $result[$i]['sn'],
                date => $result[$i]['date'],
                total => $result[$i]['subtotal']+$result[$i]['shipping_fee'],
                subtotal => $result[$i]['subtotal'],
                shipping_fee => $result[$i]['shipping_fee'],
                discounts => $result[$i]['discounts'],
                payments => $result[$i]['payments'],
                checkout => $result[$i]['checkout'],
                exchange_rate => $result[$i]['exchange_rate'],
                status_order => $result[$i]['status_order'],
                status_pay => $result[$i]['status_pay'],
                status_transport => $result[$i]['status_transport'],
                status_receiving => $result[$i]['status_receiving'],
                application_returns => $result[$i]['application_returns'],
                application_exchanges => $result[$i]['application_exchanges'],
                application_rework => $result[$i]['application_rework'],
                remind => $result[$i]['remind']
            );
        }
        
        $all_order = array_values($all_order);
        
        //order_from
        $status_order = array(
            0 => "無",
            1 => "訂單成立",
            2 => "訂單取消",
            3 => "訂單完成"
        );
                
        $status_pay = array(
            0 => "無",
            1 => "尚未付款",
            2 => "已付款",
            3 => "免付款"
        );
        
        $status_transport = array(
            0 => "無",
            1 => "尚未出貨",
            2 => "已發貨",
            3 => "確認收貨"
        );
        
        $status_receiving = array(
            0 => "無",
            1 => "待確認收貨",
            2 => "確認收貨"
        );
        
        $application_returns = array(
            0 => "無",
            1 => "有商品退貨"
        );
        
        $application_exchanges = array(
            0 => "無",
            1 => "有商品換貨"
        );
        
        $application_rework = array(
            0 => "無",
            1 => "有商品返修"
        );
        
        //order_goods
        $status = array(
            0 => "無",
            1 => "退貨",
            2 => "換貨",
            3 => "返修"
        );
        
        $application_progress = array(
            0 => "無",
            1 => "申請中",
            2 => "申請成功",
            3 => "申請失敗"
        );
        
        //依權限顯示
        $display_edit = strpos($login_permissions,"order_edit")!==false || strpos($login_permissions,"all")!==false?true:false;

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
        <link rel="stylesheet" type="text/css" href="../public/css/jquery.datetimepicker.css"/>
        <script src="../public/js/jquery.datetimepicker.js"></script>
        
        <script>
            /******************** user define js ********************/
            $(function(){
                var php_store = <?php echo $login_store;?>;
                <?php if(isset($_GET['type'])&&isset($_GET['keyword'])){?>
                $("#order_search select[name='type']").val(<?php echo $_GET['type'];?>);
                $("#order_search input[name='keyword']").val(<?php echo "'".$_GET['keyword']."'";?>);
                <?php }?>
                
                var status_order_list = [
                    "無",
                    "訂單成立",
                    "訂單取消",
                    "訂單完成"
                ];
                
                var status_pay_list = [
                    "無",
                    "尚未付款",
                    "已付款",
                    "免付款"
                ];
                
                var status_transport_list =[
                    "無",
                    "尚未出貨",
                    "已發貨"
                ];
                
                var status_receiving_list = [
                    "無",
                    "待確認收貨",
                    "確認收貨"
                ];
                
                var application_returns_list = [
                    "無",
                    "有商品退貨"
                ];
                
                var application_exchanges_list = [
                    "無",
                    "有商品換貨"
                ];
                
                var application_rework_list = [
                    "無",
                    "有商品換貨"
                ];
                
                var status_list = [
                    "無",
                    "退貨",
                    "換貨",
                    "返修"
                ];
                
                var application_progress_list = [
                    "無",
                    "申請中",
                    "申請成功",
                    "申請失敗"
                ];
                
                //列表排序
                $("#order_list_table tr:first td[name]").each(function(){
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
                
                //發貨提醒
                $("#order_list_table tr[remind='1']").css("background","#FF3653");
                
                // 訂單細目
                $("#order_list_table .order_detail").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var content = $this.data("content");
                    
                    function getContent(data){
                        data = $.trim(data);
                        if(data=="")return;
                        $this.data("content",data);
                        data = data.split("`");
                        var fi_no=data[0],
                            member = data[1],
                            sn = data[2],
                            date = data[3],
                            store = data[4],
                            subtotal = data[5],
                            shipping_fee = data[6],
                            discounts = data[7],
                            payments = data[8],
                            checkout = data[9],
                            exchange_rate = data[10],
                            status_order = data[11],
                            status_pay = data[12],
                            status_transport = data[13],
                            status_receiving = data[14],
                            application_returns = data[15],
                            application_exchanges = data[16],
                            application_rework = data[17],
                            trace = data[18],
                            remind = data[19],
                            invoice = data[20],
                            remarks = data[21],
                            consignee = data[22],
                            postal_code = data[23],
                            province = data[24],
                            city = data[25],
                            district = data[26],
                            street = data[27],
                            address = data[28],
                            contact_phone = data[29],
                            contact_mobile = data[30],
                            identity = data[31][0]!="["?[]:$.parseJSON(data[31]),
                            transport = data[32],
                            message = data[33];
                    
                        var identity_img = "";
                        var l = identity.length;
                        for(var i = 0; i<l; i++)
                        {
                            identity_img += "<img src='idimg.php?file="+identity[i]+"' style='width:400px;height:280px;'> ";
                        }
                    
                        var display=""
                        +"<table id='order_form_table' class='table-v' data-dialog='訂單細目'>"
                        +"<tr><td>訂單ID</td><td>"+fi_no+"</td></tr>"
                        +"<tr><td>訂單序號</td><td>"+sn+"</td></tr>"
                        +"<tr><td>商品原價</td><td>"+subtotal+"</td></tr>"
                        +"<tr><td>運輸費</td><td>"+shipping_fee+"</td></tr>"
                        +"<tr><td>折扣價</td><td>"+discounts+"</td></tr>"
                        +"<tr><td>總價</td><td>"+payments+"</td></tr>"
                        +"<tr><td>實際花費</td><td>"+checkout+"</td></tr>"
                        +"<tr><td>匯率</td><td>"+exchange_rate+"</td></tr>"
                        +"<tr><td>訂單狀態</td><td>"+status_order_list[status_order]+"</td></tr>"
                        +"<tr><td>付款狀態</td><td>"+status_pay_list[status_pay]+"</td></tr>"
                        +"<tr><td>運輸狀態</td><td>"+status_transport_list[status_transport]+"</td></tr>"
                        +"<tr><td>收貨狀態</td><td>"+status_receiving_list[status_receiving]+"</td></tr>"
                        +"<tr><td>有商品退貨</td><td>"+application_returns_list[application_returns]+"</td></tr>"
                        +"<tr><td>有商品換貨</td><td>"+application_exchanges_list[application_exchanges]+"</td></tr>"
                        +"<tr><td>有商品反修</td><td>"+application_rework_list[application_rework]+"</td></tr>"
                        +"<tr><td>追蹤代碼</td><td>"+json_trans_display(trace)+"</td></tr>"
                        +"<tr><td>提醒發貨</td><td>"+remind+"</td></tr>"
                        +"<tr><td>發票號碼</td><td>"+invoice+"</td></tr>"
                        +"<tr><td>訂單備註</td><td>"+remarks+"</td></tr>"
                        +"<tr><td>收件人姓名</td><td>"+consignee+"</td></tr>"
                        +"<tr><td>郵遞區號</td><td>"+postal_code+"</td></tr>"
                        +"<tr><td>收件省份</td><td>"+province+"</td></tr>"
                        +"<tr><td>市</td><td>"+city+"</td></tr>"
                        +"<tr><td>區</td><td>"+district+"</td></tr>"
                        +"<tr><td>街</td><td>"+street+"</td></tr>"
                        +"<tr><td>詳細地址</td><td>"+address+"</td></tr>"
                        +"<tr><td>聯絡電話</td><td>"+contact_phone+"</td></tr>"
                        +"<tr><td>手機號碼</td><td>"+contact_mobile+"</td></tr>"
                        +"<tr><td>身分證號</td><td>"+identity_img+"</td></tr>"
                        +"<tr><td>運送方式</td><td>"+transport+"</td></tr>"
                        +"<tr><td>留言給賣家</td><td>"+message+"</td></tr>"
                        +"</table>";
                
                       $dialog.append(display);
                       var $oft = $("#order_form_table");
                       init_style($oft);
                       $oft.show();
                    }
                    
                    if(content == undefined){
                        var fi_no = $this.parent().parent().attr("fi_no");
                        var member = $this.parent().parent().attr("member");
                        $.post(filename,{
                            query_type:"get_order_form",
                            member:member,
                            fi_no:fi_no
                        },getContent);
                    }else{
                        getContent($this.data("content"));
                    }
                });
                
                // 產品細目
                $("#order_list_table .order_goods_detail").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var content = $this.data("content");
                    
                    function getContent(data)
                    {
                        data = $.trim(data);
                        if(data=="")return;
                        $this.data("content",data);
                        data = data.split("@");
                        var l = data.length;
                        var display = "<table id='order_goods_table' class='table-h' data-dialog='產品細目'>";
                        display +=  "<tr>"+
                                    "<td>ID</td>"+
                                    "<td>產品ID</td>"+
                                    "<td style='width:100px;'>產品名</td>"+
                                    "<td>市價</td>"+
                                    "<td>促銷價</td>"+
                                    "<td>折扣價</td>"+
                                    "<td>數量</td>"+
                                    "<td>規格</td>"+
                                    "<td>尺寸</td>"+
                                    "<td>商品狀態</td>"+
                                    "</tr>";
                        for(var i=0;i<l;i++)
                        {
                            var d = data[i].split("`");
                            var id = d[0],
                                member = d[1],
                                order = d[2],
                                goods = d[3],
                                name = d[4],
                                price = d[5],
                                promotions = d[6],
                                discount = d[7],
                                number = d[8],
                                specifications = d[9],
                                volumetric_weight = d[10],
                                status = d[11],
                                store = d[12];
                            display += "<tr>"
                            +"<td>"+id+"</td>"
                            +"<td>"+goods+"</td>"
                            +"<td>"+name+"</td>"
                            +"<td>"+price+"</td>"
                            +"<td>"+promotions+"</td>"
                            +"<td>"+discount+"</td>"
                            +"<td>"+number+"</td>"
                            +"<td>"+json_trans_display(specifications)+"</td>"
                            +"<td>"+json_trans_display(volumetric_weight)+"</td>"
                            +"<td>"+status_list[status]+"</td>"
                            +"</tr>";
                        }
                        display += "</table>";
                        $dialog.append(display);
                        var $ogt = $("#order_goods_table");
                        init_style($ogt);
                        $ogt.show();
                    }
                    
                    if(content == undefined){
                        var fi_no = $this.parent().parent().attr("fi_no");
                        var member = $this.parent().parent().attr("member");
                        $.post(filename,{
                            query_type:"get_order_goods",
                            member:member,
                            fi_no:fi_no
                        },getContent);
                    }else{
                        getContent($this.data("content"));
                    }
                });
                
                //物流單列印
                $("#order_list_table .order_goods_paper").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var content = $this.data("content");
                    var papers = $this.data("papers");
                    
                    function getContent(data){
                        data = $.trim(data);
                        if(data=="")return;
                        $this.data("content",data);
                        
                        if(papers == undefined){
                            $.post(filename,{
                                query_type:"get_store_shipping"
                            },getPapers);
                        }else{
                            getPapers($this.data("papers"));
                        }
                    }
                    
                    function getPapers(paper_data){
                        
                        var data = $this.data("content").split("`");
                        var consignee = data[22],
                            province = data[24],
                            city = data[25],
                            district = data[26],
                            street = data[27],
                            address = data[28],
                            contact_phone = data[29],
                            contact_mobile = data[30],
                            contact = contact_mobile!=""?contact_mobile:contact_phone,
                            pcds = province+" "+city+" "+district+" "+street,
                            fulladdress = pcds+" "+address,
                            printdate = (new Date()).getFullYear()+"-"+((new Date()).getMonth()+1)+"-"+((new Date()).getDay()+1);
                        
                        $this.data("papers",paper_data);
                        paper_data = paper_data.split("∵");
                        var l = paper_data.length;
                        var select = "<select>";
                        for(var i=0; i<l; i++)
                        {
                            var d = paper_data[i].split("`");
                            var fi_no = d[0],
                                name = d[1],
                                item = d[2],
                                images = d[3];
                            select += "<option value='"+fi_no+"' name='"+name+"' item='"+item+"' images='"+images+"'>"+name+"</option>";
                        }
                        select += "</select>";
                        var print = "<input name='print' type='button' value='打印'>";
                        var display=""
                        +"<table id='paper_form_table' class='table-v' data-dialog='訂單細目'>"
                        +"<tr><td>物流模板種類</td><td>"+select+" "+print+"</td></tr>"
                        +"<tr><td>打印區</td><td><iframe src='#' style='width:810px;height:100px;' scrolling='no'></iframe></td></tr>"
                        +"</table>";
                        $dialog.append(display);
                        var $oft = $("#paper_form_table");
                        init_style($oft);
                        $oft.show();
                        $dialog.find("select").change(function(){
                            var $this = $(this);
                            var item = $.parseJSON($this.find("option:checked").attr("item"));
                            var images = "<img src='../public/img/shipping/"+$this.find("option:checked").attr("images")+"'>";
                            var $iframe = $dialog.find("iframe");
                            $iframe.attr("src",location.href);
                            $iframe.unbind("load");
                            $iframe.load(function(){
                                var $body = $(this).contents().find("body");
                                $body.css("background","white");
                                $body.html(images);
                                $body.find("img")[0].onload = function(){
                                    var og_w = this.width;
                                    var og_h = this.height;
                                    var new_w = 810;
                                    var new_h = Math.round(new_w*og_h/og_w);
                                    $iframe.width(new_w).height(new_h);
                                    $(this).width(new_w).height(new_h);
                                    var l = item.length;
                                    for(var i=0; i<l; i++)
                                    {
                                        var left = item[i][0];
                                        var top = item[i][1];
                                        var width = item[i][2];
                                        var height = item[i][3];
                                        var type = item[i][4];
                                        var value = item[i][5];
                                        if(type=="select")
                                        {
                                            switch(value)
                                            {
                                                case "1":value = consignee;break;
                                                case "2":value = contact_phone;break;
                                                case "3":value = contact_mobile;break;
                                                case "4":value = contact;break;
                                                case "5":value = printdate;break;
                                                case "6":value = province;break;
                                                case "7":value = city;break;
                                                case "8":value = district;break;
                                                case "9":value = street;break;
                                                case "10":value = address;break;
                                                case "11":value = pcds;break;
                                                case "12":value = fulladdress;break;
                                                default:value = "無資料";
                                            }
                                        }
                                        $body.append($("<div></div>").css({
                                            "display":"inline-block",
                                            "position":"absolute",
                                            "left":left+"px",
                                            "top":top+"px",
                                            "width":width+"px",
                                            "height":height+"px"
                                        }).html(value));
                                    }
                                    $dialog.find("input[name='print']").unbind("click");
                                    $dialog.find("input[name='print']").click(function(){
                                        $body.find("img").hide();
                                        $iframe[0].contentWindow.focus();
                                        $iframe[0].contentWindow.print();
                                        $body.find("img").show();
                                    });
                                }
                            });
                        }).trigger("change");
                        
                    }
                    
                    if(content == undefined){
                        var fi_no = $this.parent().parent().attr("fi_no");
                        var member = $this.parent().parent().attr("member");
                        $.post(filename,{
                            query_type:"get_order_form",
                            member:member,
                            fi_no:fi_no
                        },getContent);
                    }else{
                        getContent($this.data("content"));
                    }
                });
                
                //揀貨單列印
                $("#order_list_table .order_goods_pick").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var order = $this.data("order");
                    var goods = $this.data("goods");
                    var pick = $this.data("pick");
                    
                    function getOrder(data){
                        data = $.trim(data);
                        if(data=="")return;
                        $this.data("order",data);
                        var fi_no = $this.parent().parent().attr("fi_no");
                        var member = $this.parent().parent().attr("member");
                        if(goods == undefined){
                            $.post(filename,{
                                query_type:"get_order_goods",
                                member:member,
                                fi_no:fi_no
                            },getGoods);
                        }else{
                            getGoods($this.data("goods"));
                        }
                    }
                    
                    function getGoods(goods_data)
                    {
                        goods_data = $.trim(goods_data);
                        if(goods_data=="")return;
                        $this.data("goods",goods_data);
                        
                        if(pick == undefined){
                            $.post(filename,{
                                query_type:"get_store_picking"
                            },getPick);
                        }else{
                            getPick($this.data("pick"));
                        }
                    }
                    
                    function getPick(pick_data){
                        pick_data = $.trim(pick_data);
                        if(pick_data=="")return;
                        $this.data("pick",pick_data);
                        var order = $this.data("order");
                        var goods = $this.data("goods");
                        var pick = $this.data("pick");
                            
                        order = order.split("`");
                        var member = order[1],
                            sn = order[2],
                            consignee = order[22],
                            province = order[24],
                            city = order[25],
                            district = order[26],
                            street = order[27],
                            address = order[28],
                            contact_phone = order[29],
                            contact_mobile = order[30],
                            pormo = contact_mobile!=""?contact_mobile:contact_phone,
                            fulladdress = province+" "+city+" "+district+" "+street+" "+address,
                            printdate = (new Date()).getFullYear()+"-"+((new Date()).getMonth()+1)+"-"+((new Date()).getDay()+1);
                        
                        goods = goods.split("@");
                        var l = goods.length;
                        var goods_list = "<table>";
                        goods_list +=  "<tr>"+
                                    "<td>產品ID</td>"+
                                    "<td>產品名</td>"+
                                    "<td>數量</td>"+
                                    "</tr>";
                        for(var i=0;i<l;i++)
                        {
                            var d = goods[i].split("`");
                            var good = d[3],
                                name = d[4],
                                number = d[8];
                            goods_list += "<tr>"
                            +"<td>"+good+"</td>"
                            +"<td>"+name+"</td>"
                            +"<td>"+number+"</td>"
                            +"</tr>";
                        }
                        goods_list += "</table>";
                        
                        pick = pick.split("∵");
                        var l = pick.length;
                        var select = "<select>";
                        for(var i=0; i<l; i++)
                        {
                            var d = pick[i].split("`");
                            var name = d[1],
                                header = d[2],
                                footer = d[4];
                            select += "<option name='"+name+"' header='"+header+"' footer='"+footer+"'>"+name+"</option>";
                        }
                        select += "</select>";
                        var print = "<input name='print' type='button' value='打印'>";
                        var display=""
                        +"<table id='pick_form_table' class='table-v'>"
                        +"<tr><td>揀貨模板種類</td><td>"+select+" "+print+"</td></tr>"
                        +"<tr><td>打印區</td><td><iframe src='#' style='width:810px;height:100px;' scrolling='no'></iframe></td></tr>"
                        +"</table>";
                        $dialog.append(display);
                        var $oft = $("#pick_form_table");
                        init_style($oft);
                        $oft.show();
                        $dialog.find("select").change(function(){
                            var $this = $(this);
                            var header = "<div class='header'>"+$this.find("option:checked").attr("header")+"</div>";
                            var body_content = "<div class='content'>"+goods_list+"</div>";
                            var footer = "<div class='footer'>"+$this.find("option:checked").attr("footer")+"</div>";
                            var $iframe = $dialog.find("iframe");
                            $iframe.attr("src","picking_print.php");
                            $iframe.unbind("load");
                            $iframe.load(function(){
                                var $body = $(this).contents().find("body");
                                $body.css("background","white");
                                var content = header+body_content+footer;
                                content = content.replace(/\{sn\}/g,sn);
                                content = content.replace(/\{id\}/g,member);
                                content = content.replace(/\{consignee\}/g,consignee);
                                content = content.replace(/\{phone\}/g,contact_phone);
                                content = content.replace(/\{mobile\}/g,contact_mobile);
                                content = content.replace(/\{pormo\}/g,pormo);
                                content = content.replace(/\{address\}/g,fulladdress);
                                content = content.replace(/\{print_date\}/g,printdate);
                                content = content.replace(/\{page\}/g,"<span class='page_num'></span>");
                                $body.find(".book").html("<div class='page'><div class='subpage'>"+content+"</div></div>");
                                var header_height = $body.find(".header").height();
                                var footer_height = $body.find(".footer").height();
                                var body_content_maxheight = Math.floor($body.find(".book .page:last .subpage").height() - header_height - footer_height - 50);
                                var $body_content = $($body.find(".content table").html());
                                var $th = $body_content.find("tr:first").clone();
                                $body_content.find("tr:first").remove();
                                $body.find(".book").html("");
                                var page_num = 1;
                                while($body_content.find("tr:first")[0])
                                {
                                    $body.find(".book").append("<div class='page'><div class='subpage'>"+content+"</div></div>");
                                    $body.find(".book .page:last .header span.page_num").text(page_num);
                                    $body.find(".book .page:last .footer span.page_num").text(page_num);
                                    page_num++;
                                    var $table = $body.find(".book .page:last .content table");
                                    $table.html("");
                                    $table.append($th.clone());
                                    while(($table.height()<body_content_maxheight))
                                    {
                                        $table.append($body_content.find("tr:first"));
                                        if(!$body_content.find("tr:first")[0])
                                            break;
                                    }
                                }
                                $body.find(".content").height(body_content_maxheight);
                                $iframe.height($iframe.contents().height());
                                $dialog.find("input[name='print']").unbind("click");
                                $dialog.find("input[name='print']").click(function(){
                                    $iframe[0].contentWindow.focus();
                                    $iframe[0].contentWindow.print();
                                });
                            });
                        }).trigger("change");
                    }
                    
                    if(order == undefined){
                        var fi_no = $this.parent().parent().attr("fi_no");
                        var member = $this.parent().parent().attr("member");
                        $.post(filename,{
                            query_type:"get_order_form",
                            member:member,
                            fi_no:fi_no
                        },getOrder);
                    }else{
                        getOrder($this.data("order"));
                    }
                });
                
                //編輯
                $("#order_list_table .order_edit").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var fi_no = $this.parent().parent().attr("fi_no"),
                        member = $this.parent().parent().attr("member"),
                        status_order,
                        status_pay,
                        status_transport,
                        trace,
                        remarks;
                    
                    $dialog.data("fi_no",fi_no);
                    $dialog.data("member",member);
                    
                    $.post(filename,{
                        query_type:"get_order_form",
                        member:member,
                        fi_no:fi_no
                    },function(data){
                        data = data.split("`");
                        status_order = data[11],
                        status_pay = data[12],
                        status_transport = data[13],
                        trace = data[18],
                        remarks = data[21];
                        $.post(filename,{
                            query_type:"get_order_goods",
                            member:member,
                            fi_no:fi_no
                        },function(data){
                            data = data.split("@");
                            var l = data.length;
                            $dialog.find("textarea[name='remarks']").val(remarks);
                            var $goods_status_list = $dialog.find("#order_goods_status_list");
                            var $current_trace = $dialog.find("#current_trace");
                            for(var i=0; i<l; i++)
                            {
                                var d = data[i].split("`");
                                var id = d[0],
                                    name = d[4];
                                $goods_status_list.append("<p><span>"+id+"</span>:"+name+"<br/></p>");
                            }
                            $current_trace.html(json_trans_display(trace));
                            
                            if(status_order == 2)//訂單取消
                            {
                                $dialog.find("input[name='cancel_order']").attr("disabled","disabled");
                                $dialog.find("input[name='shipped']").attr("disabled","disabled");
                                $dialog.find("select[name='store_code']").attr("disabled","disabled");
                                $dialog.find("input[name='trace_code']").attr("disabled","disabled");
                                $dialog.find("select option").attr("disabled","disabled");
                                $dialog.find("select").val(0);
                            }
                            else
                            {
                                $dialog.find("input[name='cancel_order']").removeAttr('disabled');
                                $dialog.find("input[name='shipped']").removeAttr('disabled');
                                $dialog.find("select[name='store_code']").removeAttr('disabled');
                                $dialog.find("input[name='trace_code']").removeAttr('disabled');
                                $dialog.find("select option").removeAttr('disabled');
                            }
                            if(status_transport == 2)
                            {
                                $dialog.find("input[name='shipped']").attr("disabled","disabled");
                            }
                        });
                    });
                });
                
                //編輯儲存
                $("#order_edit_table #order_edit").click(function(){
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    
                    var remarks = $dialog.find("textarea[name='remarks']").val();
                    var store_code = $dialog.find("select[name='store_code']").val();
                    var trace_code = $dialog.find("input[name='trace_code']").val();

                    var fi_no = $dialog.data("fi_no"),
                        member = $dialog.data("member");
                    
                    $.post("sql_order.php",{
                        query_type:"order_edit",
                        fi_no:fi_no,
                        member:member,
                        remarks:remarks,
                        store_code:store_code,
                        trace_code:trace_code
                    },function(){
                        alert("已更新！");
                        location.href = location.href;
                    });
                });
                
                //編輯取消訂單
                $("#order_edit_table input[name='cancel_order']").click(function(){
                    if(!confirm("確定取消訂單？"))return;
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var fi_no = $dialog.data("fi_no"),
                        member = $dialog.data("member");
                    $.post("sql_order.php",{
                        query_type:"order_cancel",
                        fi_no:fi_no,
                        member:member
                    },function(){
                        alert("已取消訂單！");
                        location.href = location.href;
                    });
                });
                
                //編輯已出貨
                $("#order_edit_table input[name='shipped']").click(function(){
                    if(!confirm("確定已出貨？"))return;
                    var $this = $(this);
                    var $dialog = $("#dialog_content");
                    var fi_no = $dialog.data("fi_no"),
                        member = $dialog.data("member");
                    $.post("sql_order.php",{
                        query_type:"order_shipped",
                        fi_no:fi_no,
                        member:member
                    },function(){
                        alert("商品已出貨！");
                        location.href = location.href;
                    });
                });
                
                //搜尋
                $("#order_search input[name='search']").click(function(){
                    var type = $("#order_search select[name='type']").val();
                    var keyword = $("#order_search input[name='keyword']").val();
                    location.href = location.href.split("?")[0] + "?type=" + type + "&keyword=" + keyword ;
                });
                $("#order_search input[name='keyword']").keypress(function(e){
                    if(e.keyCode==13)
                    {
                        $("#order_search input[name='search']").trigger("click");
                    }
                });
                
                //日期選擇器
                $("#order_search select").change(function(){
                    var $this = $(this);
                    if($this.val()==3)
                    {
                        $("#order_search input[name='keyword']").datetimepicker({
                            lang:'ch',
                            timepicker:false,
                            format:'Y-m-d',
                            formatDate:'Y/m/d'
                        });
                    }
                    else
                    {
                        $("#order_search input[name='keyword']").datetimepicker("destroy");
                    }
                });
                
            });
            
            function json_trans_display(json){
                
                if(json == "" || json==undefined)return "";
                
                json = $.parseJSON(json);
                var display = "";
                for(var i in json)
                {
                    display+=i+"---"+json[i]+"<br/>";
                }
                return display;
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
                <div id="body_right">
                    <?php
                        
                        echo "<div style='display:inline-block;width:10px;height:10px;background:rgb(255, 54, 83);'></div>背景為消費者發貨提醒";
                        //搜尋
                        echo "<div id='order_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>訂單ID</option><option value='2'>訂單序號</option><option value='3'>建立日期</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";
                    
                        if($display_edit){
                            echo "<table id='order_edit_table' class='table-v' data-dialog='編輯'>";
                            echo "<tr><td>訂單狀態</td><td><input name='cancel_order' type='button' value='取消訂單'></td></tr>";
                            echo "<tr><td>運輸狀態</td><td><input name='shipped' type='button' value='已出貨'></td></tr>";
                            echo "<tr><td>訂單備註</td><td><textarea name='remarks'></textarea></td></tr>";
                            echo "<tr><td>新增追蹤碼</td><td>(若無新增請留白)<br/><br/><div id='current_trace'></div><br/><br/>店代碼&nbsp;<select name='store_code'><option value='1'>台灣直郵</option><option value='2'>順豐快遞</option><option value='3'>申通快遞</option><option value='4'>韻達快遞</option><option value='5'>郵政EMS(特快)</option></select><br/><br/>追蹤碼&nbsp;<input name='trace_code' type='text' placeholder='請輸入追蹤碼'></td></tr>";
                            echo "<tr><td>商品狀態列表</td><td><div id='order_goods_status_list'></div></td></tr>";
                            echo "<tr><td></td><td><input id='order_edit' type='button' value='儲存'></td></tr>";
                            echo "</table>";
                        }

                        $pager->display();echo "<br/>";
                        echo "<table id='order_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td name='sn'>訂單序號</td><td name='date'>建立日期</td><td name='total'>總花費</td><td>訂單狀態</td><td>付款狀態</td><td>運輸狀態</td><td>收貨狀態</td><td>訂單細目</td><td>商品細目</td><td>物流單列印</td><td>揀貨單列印</td>";
                        if($display_edit)echo "<td>修改</td>";
                        echo "</tr>";
                        $len = count($all_order);
                        for($i = 0; $i < $len; $i++)
                        {
                            echo "<tr fi_no='".$all_order[$i]["fi_no"]."' member='".$all_order[$i]["member"]."' remind='".$all_order[$i]["remind"]."'>";
                            echo "<td>".$all_order[$i]["fi_no"]."</td>";
                            echo "<td>".$all_order[$i]["sn"]."</td>";
                            echo "<td>".$all_order[$i]["date"]."</td>";
                            echo "<td>".$all_order[$i]["total"]."</td>";
                            echo "<td>".$status_order[$all_order[$i]["status_order"]]."</td>";
                            echo "<td>".$status_pay[$all_order[$i]["status_pay"]]."</td>";
                            echo "<td>".$status_transport[$all_order[$i]["status_transport"]]."</td>";
                            echo "<td>".$status_receiving[$all_order[$i]["status_receiving"]]."</td>";
                            echo "<td><input type='button' class='order_detail' value='+' data-open-dialog='訂單細目' /></td>";
                            echo "<td><input type='button' class='order_goods_detail' value='+' data-open-dialog='產品細目' /></td>";
                            echo "<td><input type='button' class='order_goods_paper' value='物流單列印' data-open-dialog='物流單列印' /></td>";
                            echo "<td><input type='button' class='order_goods_pick' value='揀貨單列印' data-open-dialog='揀貨單列印' /></td>";
                            if($display_edit)echo "<td><input type='button' class='order_edit' value='編輯' data-open-dialog='編輯' /></td>";
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

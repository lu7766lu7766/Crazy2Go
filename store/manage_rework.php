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
            if($result[$i]['application_rework']>0)
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
            1 => "有商品退貨",
            2 => "退貨商品皆處理完畢"
        );
        
        $application_exchanges = array(
            0 => "無",
            1 => "有商品換貨",
            2 => "換貨商品皆處理完畢"
        );
        
        $application_rework = array(
            0 => "無",
            1 => "有商品返修",
            2 => "返修商品皆處理完畢"
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
                            identity = data[31],
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
                
                //申請內容
                $("#order_list_table .order_edit").click(function(){
                    var $apply = $(this);
                    var $dialog = $("#dialog_content");
                    
                    function getContent(data){
                        data = $.trim(data);
                        if(data=="")return;
                        data = data.split("@");
                        var l = data.length;
                        var display = "<table id='order_apply_table' class='table-h' data-dialog='申請內容'>";
                        display +=  "<tr>"+
                                    "<td>圖片</td>"+
                                    "<td style='width:100px;'>產品名稱</td>"+
                                    "<td>申請類型</td>"+
                                    "<td>原因</td>"+
                                    "<td>說明</td>"+
                                    "<td>儲存</td>"+
                                    "</tr>";
                        for(var i=0;i<l;i++)
                        {
                            var d = data[i].split("`");
                            var goods = d[0],
                                progress = d[1],
                                reason = d[2],
                                explanation = d[3],
                                images = d[4],
                                order_application_fi_no = d[5];
                            display += "<tr>"
                            +"<td><a href='javascript:var w=window.open(\"http://www.google.com/\");w.document.write(\"<img src=http://www.crazy2go.com/public/img/application/"+images+">\");'><img src='http://www.crazy2go.com/public/img/application/"+images+"' style='width:70px;height:70px'></a></td>"
                            +"<td>"+goods+"</td>"
                            +"<td>"+build_progress_selection(application_progress_list,progress)+"</td>"
                            +"<td>"+reason+"</td>"
                            +"<td>"+explanation+"</td>"
                            +"<td><input class='update_progress' type='button' value='更新' fi_no='"+order_application_fi_no+"'></td>"
                            +"</tr>";
                        }
                        display += "</table>";
                        $dialog.append(display);
                        var $oft = $("#order_apply_table");
                        init_style($oft);
                        $oft.show();

                        $dialog.find(".update_progress").click(function(){
                            var $this = $(this);
                            var progress = $this.parent().parent().find("select").val();
                            $.post(filename,{
                                query_type:"save_application_status",
                                progress:progress,
                                fi_no:$this.attr("fi_no"),
                                order:fi_no,
                                member:member
                            },function(){
                                closeDialog();
                                $apply.trigger("click");
                            });
                        });
                    }
                    
                    var fi_no = $apply.parent().parent().attr("fi_no");
                    var member = $apply.parent().parent().attr("member");
                    $.post(filename,{
                        query_type:"get_order_apply",
                        member:member,
                        fi_no:fi_no
                    },getContent);
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
            
            function build_progress_selection(list,defaultChecked){
                var select = "<select>";
                var len = list.length;
                for(var i =0;i<len;i++)
                {
                    if(i==0)continue;
                    select+="<option value='"+i+"' "+(defaultChecked==i?"selected":"")+">"+list[i]+"</option>";
                }
                select+="</select>";
                return select;
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
                        
                        //搜尋
                        echo "<div id='order_search' style='display:inline-block;float:right;padding:2px;background:rgba(255,255,255,0.2);' class='shadowRoundCorner'><select name='type'><option value='1'>訂單ID</option><option value='2'>訂單序號</option><option value='3'>建立日期</option></select>&nbsp;<input name='keyword' type='text' placeholder='請輸入關鍵字'/>&nbsp;<input name='search' type='button' value='搜尋'></div><table></table>";

                        $pager->display();echo "<br/>";
                        echo "<table id='order_list_table' class='table-h'>";
                        echo "<tr><td name='fi_no'>ID</td><td name='sn'>訂單序號</td><td name='date'>建立日期</td><td name='total'>總花費</td><td>訂單狀態</td><td>付款狀態</td><td>運輸狀態</td><td>收貨狀態</td><td>有商品返修</td><td>訂單細目</td><td>商品細目</td>";
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
                            echo "<td>".$application_rework[$all_order[$i]["application_rework"]]."</td>";
                            echo "<td><input type='button' class='order_detail' value='+' data-open-dialog='訂單細目' /></td>";
                            echo "<td><input type='button' class='order_goods_detail' value='+' data-open-dialog='產品細目' /></td>";
                            if($display_edit)echo "<td><input type='button' class='order_edit' value='申請內容' data-open-dialog='申請內容' /></td>";
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